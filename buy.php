<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: index.php");
    exit();
}

include 'config.php'; // must define $conn (SQLite3), RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET and createRazorpayOrder()

/**
 * Ensure purchases table and required columns exist (idempotent).
 * Call this right after $conn is available.
 */
function ensurePurchasesSchema(SQLite3 $conn) {
    $required = [
        'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
        'user_id' => 'INTEGER',
        'product_id' => 'INTEGER',
        'price' => 'REAL',
        'purchase_date' => 'TEXT',
        'payment_method' => 'TEXT',
        'delivery_address' => 'TEXT',
        'razorpay_payment_id' => 'TEXT',
        'razorpay_order_id' => 'TEXT'
    ];

    // Does table exist?
    $res = $conn->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='purchases'");
    if (!$res) {
        // Create table with all required columns
        $cols = [];
        foreach ($required as $col => $type) {
            $cols[] = $col . ' ' . $type;
        }
        $sql = "CREATE TABLE purchases (" . implode(', ', $cols) . ")";
        if (!$conn->exec($sql)) {
            throw new Exception("Failed to create purchases table: " . $conn->lastErrorMsg());
        }
        return true;
    }

    // Table exists -> check columns
    $existingCols = [];
    $pragma = $conn->query("PRAGMA table_info('purchases')");
    while ($row = $pragma->fetchArray(SQLITE3_ASSOC)) {
        $existingCols[$row['name']] = true;
    }

    // Add missing columns
    foreach ($required as $col => $type) {
        if (!isset($existingCols[$col])) {
            $alter = "ALTER TABLE purchases ADD COLUMN {$col} {$type}";
            if (!$conn->exec($alter)) {
                throw new Exception("Failed to add column {$col}: " . $conn->lastErrorMsg());
            }
        }
    }
    return true;
}

// ensure schema is present
try {
    ensurePurchasesSchema($conn);
} catch (Exception $e) {
    die("Database schema error: " . htmlspecialchars($e->getMessage()));
}

$customer_id = $_SESSION['customer'];

// Fetch cart items
$query = "SELECT c.product_id, p.name, p.price, c.quantity 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("DB error (prepare cart select): " . htmlspecialchars($conn->lastErrorMsg()));
}
$stmt->bindValue(1, $customer_id, SQLITE3_INTEGER);
$result = $stmt->execute();

$cart_items = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    die("Your cart is empty!");
}

// Compute totals
$total = 0.0;
foreach ($cart_items as $item) {
    $item_total = $item['price'] * $item['quantity'];
    $total += $item_total;
}
$cgst = $total * 0.09;
$sgst = $total * 0.09;
$grand_total = $total + $cgst + $sgst;

// --------------------------------------------------
// Server-side POST handling: Razorpay verification & COD
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // RAZORPAY success POST (JS posts these fields)
    if (isset($_POST['razorpay_payment_id'])) {
        $razorpay_payment_id = $_POST['razorpay_payment_id'];
        $razorpay_order_id   = $_POST['razorpay_order_id'];
        $razorpay_signature  = $_POST['razorpay_signature'];
        $delivery_address    = trim($_POST['delivery_address'] ?? '');

        if ($delivery_address === '') {
            http_response_code(400);
            echo "Delivery address required.";
            exit();
        }

        // verify signature
        if (!defined('RAZORPAY_KEY_SECRET')) {
            http_response_code(500);
            echo "Razorpay secret not configured.";
            exit();
        }
        $generated_signature = hash_hmac('sha256', $razorpay_order_id . '|' . $razorpay_payment_id, RAZORPAY_KEY_SECRET);
        if (!hash_equals($generated_signature, $razorpay_signature)) {
            http_response_code(400);
            echo "Invalid payment signature.";
            exit();
        }

        // store purchases & clear cart (transaction)
        $conn->exec('BEGIN');
        try {
            foreach ($cart_items as $item) {
                $insertSql = "INSERT INTO purchases (user_id, product_id, price, purchase_date, payment_method, delivery_address, razorpay_payment_id, razorpay_order_id)
                              VALUES (?, ?, ?, datetime('now'), ?, ?, ?, ?)";
                $ins = $conn->prepare($insertSql);
                if (!$ins) throw new Exception("Prepare failed: " . $conn->lastErrorMsg());
                $ins->bindValue(1, $customer_id, SQLITE3_INTEGER);
                $ins->bindValue(2, $item['product_id'], SQLITE3_INTEGER);
                $ins->bindValue(3, $item['price'], SQLITE3_FLOAT);
                $ins->bindValue(4, 'razorpay', SQLITE3_TEXT);
                $ins->bindValue(5, $delivery_address, SQLITE3_TEXT);
                $ins->bindValue(6, $razorpay_payment_id, SQLITE3_TEXT);
                $ins->bindValue(7, $razorpay_order_id, SQLITE3_TEXT);
                $resIns = $ins->execute();
                if ($resIns === false) throw new Exception("Insert failed: " . $conn->lastErrorMsg());
            }

            // Clear the cart after successful purchase
            $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            if (!$del) throw new Exception("Prepare delete failed: " . $conn->lastErrorMsg());
            $del->bindValue(1, $customer_id, SQLITE3_INTEGER);
            $delResult = $del->execute();
            if ($delResult === false) throw new Exception("Cart deletion failed: " . $conn->lastErrorMsg());

            $conn->exec('COMMIT');

            // redirect to receipt
            header("Location: receipt_purchase.php?customer_id=" . urlencode($customer_id) . "&method=razorpay&payment_id=" . urlencode($razorpay_payment_id) . "&order_id=" . urlencode($razorpay_order_id));
            exit();
        } catch (Exception $e) {
            $conn->exec('ROLLBACK');
            http_response_code(500);
            echo "Server error while saving purchase: " . htmlspecialchars($e->getMessage());
            exit();
        }
    }

    // COD flow
    if (isset($_POST['confirm_payment']) && $_POST['confirm_payment'] === 'cod') {
        $delivery_address = trim($_POST['delivery_address'] ?? '');
        if ($delivery_address === '') {
            die("Delivery address is required for COD.");
        }

        $conn->exec('BEGIN');
        try {
            foreach ($cart_items as $item) {
                $insertSql = "INSERT INTO purchases (user_id, product_id, price, purchase_date, payment_method, delivery_address)
                              VALUES (?, ?, ?, datetime('now'), ?, ?)";
                $ins = $conn->prepare($insertSql);
                if (!$ins) throw new Exception("Prepare failed: " . $conn->lastErrorMsg());
                $ins->bindValue(1, $customer_id, SQLITE3_INTEGER);
                $ins->bindValue(2, $item['product_id'], SQLITE3_INTEGER);
                $ins->bindValue(3, $item['price'], SQLITE3_FLOAT);
                $ins->bindValue(4, 'cod', SQLITE3_TEXT);
                $ins->bindValue(5, $delivery_address, SQLITE3_TEXT);
                $resIns = $ins->execute();
                if ($resIns === false) throw new Exception("Insert failed: " . $conn->lastErrorMsg());
            }

            // Clear the cart after successful purchase
            $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            if (!$del) throw new Exception("Prepare delete failed: " . $conn->lastErrorMsg());
            $del->bindValue(1, $customer_id, SQLITE3_INTEGER);
            $delResult = $del->execute();
            if ($delResult === false) throw new Exception("Cart deletion failed: " . $conn->lastErrorMsg());

            $conn->exec('COMMIT');

            header("Location: receipt_purchase.php?customer_id=" . urlencode($customer_id) . "&method=cod");
            exit();
        } catch (Exception $e) {
            $conn->exec('ROLLBACK');
            die("Error processing COD order: " . htmlspecialchars($e->getMessage()));
        }
    }
}

// --------------------------------------------------
// Create Razorpay order to be used by JS checkout
// --------------------------------------------------
$razorpay_order = null;
$razorpay_error = null;
if (!function_exists('createRazorpayOrder')) {
    $razorpay_error = "createRazorpayOrder() is not defined in config.php.";
} else {
    try {
        $amount_paise = (int) round($grand_total * 100);
        $receipt_id = "rcpt_" . $customer_id . "_" . time();
        $order_resp = createRazorpayOrder($amount_paise, $receipt_id);
        if (isset($order_resp['id'])) {
            $razorpay_order = $order_resp;
        } else {
            $razorpay_error = $order_resp;
        }
    } catch (Exception $e) {
        $razorpay_error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/seal.png?v=1">
<meta charset="utf-8">
<title>Complete Your Purchase — Barter Bay</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
    body { font-family: Arial, sans-serif; background: linear-gradient(to right,#d32,#39f); color:#fff; margin:0; padding:0; }
    .container { max-width:900px; margin:40px auto; background: rgba(0,0,0,0.9); padding:24px; border-radius:12px; }
    table { width:100%; border-collapse:collapse; background:#fff; color:#000; margin-bottom:18px; }
    th, td { padding:10px; border:1px solid #ccc; text-align:left; }
    th { background:#f2f2f2; }
    .summary { display:flex; gap:12px; justify-content:space-between; margin-bottom:12px; color:#fff; }
    .address-box { width:100%; min-height:80px; border-radius:6px; padding:10px; border:1px solid #ccc; box-sizing:border-box; color:#000; }
    .payment-options { background: rgba(255,255,255,0.06); padding:14px; border-radius:8px; color:#fff; }
    .option { display:flex; gap:12px; align-items:flex-start; padding:12px 8px; border-radius:6px; cursor:pointer; }
    .option input[type="radio"] { transform:scale(1.15); margin-top:4px; }
    .option-label { font-weight:600; margin-bottom:6px; }
    .option-desc { font-size:14px; color:#ddd; }
    .action { margin-top:16px; display:flex; gap:12px; align-items:center; }
    .btn { background:#ff3b30; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer; font-weight:700; }
    .btn.secondary { background:transparent; border:1px solid rgba(255,255,255,0.2); }
    .info-box { margin-top:10px; padding:10px; border-radius:6px; background:rgba(255,255,255,0.02); color:#eee; display:none; }
    .error { color: #ffda9e; margin-top:8px; }
    @media (max-width:720px){ .summary{flex-direction:column} }
</style>

<!-- Razorpay -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h2>Complete Your Purchase</h2>

    <table>
        <thead>
            <tr><th>Product</th><th>Price (₹)</th><th>Qty</th><th>Total (₹)</th></tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): 
                $item_total = $item['price'] * $item['quantity'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'],2) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= number_format($item_total,2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" style="text-align:right"><strong>Subtotal</strong></td><td>₹<?= number_format($total,2) ?></td></tr>
            <tr><td colspan="3" style="text-align:right">CGST (9%)</td><td>₹<?= number_format($cgst,2) ?></td></tr>
            <tr><td colspan="3" style="text-align:right">SGST (9%)</td><td>₹<?= number_format($sgst,2) ?></td></tr>
            <tr><td colspan="3" style="text-align:right"><strong>Grand Total</strong></td><td><strong>₹<?= number_format($grand_total,2) ?></strong></td></tr>
        </tfoot>
    </table>

    <div class="summary">
        <div><strong>Customer ID:</strong> <?= htmlspecialchars($customer_id) ?></div>
        <div><strong>Amount:</strong> ₹<?= number_format($grand_total,2) ?></div>
    </div>

    <div>
        <label for="delivery_address"><strong>Delivery Address (required)</strong></label><br>
        <textarea id="delivery_address" class="address-box" placeholder="Enter full delivery address (required)"></textarea>
        <div id="addr_error" class="error" style="display:none">Delivery address is required.</div>
    </div>

    <div style="height:14px"></div>

    <div class="payment-options" role="radiogroup" aria-label="Payment Methods">
        <div class="option" data-value="razorpay" onclick="selectOption('razorpay')">
            <input type="radio" name="payment_method" id="opt_razorpay" value="razorpay" />
            <div>
                <div class="option-label">Razorpay — Online payment</div>
                <div class="option-desc">Secure instant payment via cards, UPI & wallets. Checkout will open after you click Proceed.</div>
            </div>
        </div>

        <div class="option" data-value="cod" onclick="selectOption('cod')">
            <input type="radio" name="payment_method" id="opt_cod" value="cod" />
            <div>
                <div class="option-label">Cash On Delivery (COD)</div>
                <div class="option-desc">Pay in cash when your order is delivered. Address is required to place COD orders.</div>
            </div>
        </div>

        <div id="desc_razorpay" class="info-box">
            <strong>Razorpay (Online)</strong>
            <p>You'll be redirected to Razorpay's secure checkout. After successful payment, the order will be saved and cart cleared.</p>
        </div>

        <div id="desc_cod" class="info-box">
            <strong>Cash On Delivery</strong>
            <p>Your order will be placed and saved. Pay in cash at delivery. Make sure address is correct.</p>
        </div>

        <div id="payment_error" class="error" style="display:none"></div>

        <div class="action">
            <button id="proceedBtn" class="btn">Proceed</button>
            <button id="cancelBtn" class="btn secondary" onclick="window.location.href='dashboard.php'; return false;">Cancel</button>
        </div>
    </div>

    <?php if ($razorpay_error): ?>
        <div style="margin-top:12px; color:#ffd2a6">Razorpay setup issue: <?= htmlspecialchars(is_array($razorpay_error) ? json_encode($razorpay_error) : $razorpay_error) ?></div>
    <?php endif; ?>
</div>

<script>
    let selected = null;
    const addr = document.getElementById('delivery_address');
    const addrErr = document.getElementById('addr_error');
    const payErr = document.getElementById('payment_error');
    const descR = document.getElementById('desc_razorpay');
    const descC = document.getElementById('desc_cod');

    function init() {
        <?php if ($razorpay_order && isset($razorpay_order['id'])): ?>
            selectOption('razorpay');
        <?php else: ?>
            selectOption('cod');
        <?php endif; ?>
    }

    function selectOption(key) {
        selected = key;
        document.getElementById('opt_razorpay').checked = (key === 'razorpay');
        document.getElementById('opt_cod').checked = (key === 'cod');
        descR.style.display = (key === 'razorpay') ? 'block' : 'none';
        descC.style.display = (key === 'cod') ? 'block' : 'none';
        addrErr.style.display = 'none';
        payErr.style.display = 'none';
    }

    document.getElementById('proceedBtn').addEventListener('click', function () {
        payErr.style.display = 'none';
        addrErr.style.display = 'none';
        const addressVal = addr.value.trim();
        if (addressVal === '') {
            addrErr.style.display = 'block';
            addr.focus();
            return;
        }
        if (!selected) {
            payErr.textContent = "Please select a payment method.";
            payErr.style.display = 'block';
            return;
        }

        if (selected === 'cod') {
            const f = document.createElement('form');
            f.method = 'POST';
            f.style.display = 'none';
            const inputMethod = document.createElement('input');
            inputMethod.name = 'confirm_payment';
            inputMethod.value = 'cod';
            f.appendChild(inputMethod);
            const inputAddr = document.createElement('input');
            inputAddr.name = 'delivery_address';
            inputAddr.value = addressVal;
            f.appendChild(inputAddr);
            document.body.appendChild(f);
            f.submit();
            return;
        }

        if (selected === 'razorpay') {
            <?php if (! $razorpay_order): ?>
                payErr.textContent = "Razorpay is currently unavailable. Please choose COD.";
                payErr.style.display = 'block';
                return;
            <?php else: ?>
                const options = {
                    "key": <?= json_encode(RAZORPAY_KEY_ID) ?>,
                    "amount": <?= json_encode($razorpay_order['amount'] ?? '') ?>,
                    "currency": <?= json_encode($razorpay_order['currency'] ?? 'INR') ?>,
                    "name": "Barter Bay",
                    "description": "Order Payment",
                    "order_id": <?= json_encode($razorpay_order['id']) ?>,
                    "handler": function (response) {
                        const formData = new FormData();
                        formData.append('razorpay_payment_id', response.razorpay_payment_id);
                        formData.append('razorpay_order_id', response.razorpay_order_id);
                        formData.append('razorpay_signature', response.razorpay_signature);
                        formData.append('delivery_address', addressVal);

                        fetch(window.location.href, {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin'
                        }).then(r => {
                            if (r.redirected) {
                                window.location.href = r.url;
                            } else {
                                return r.text().then(txt => { alert(txt); });
                            }
                        }).catch(err => {
                            alert("Error finalizing payment: " + err);
                        });
                    },
                    "prefill": {},
                    "theme": { "color": "#F37254" }
                };
                const rzp = new Razorpay(options);
                rzp.on('payment.failed', function(response){
                    alert("Payment failed: " + (response.error && response.error.description ? response.error.description : "Unknown error"));
                });
                rzp.open();
            <?php endif; ?>
        }
    });

    init();
</script>

<?php include 'footer.php'; ?>
</body>
</html>
