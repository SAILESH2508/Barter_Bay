<?php
session_start();
include 'config.php';

if (!isset($_SESSION['customer'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer'];

// Get payment method (only 'razorpay' and 'cod' are valid now)
$method_raw = isset($_GET['method']) ? trim($_GET['method']) : 'cod';
$method_raw = in_array($method_raw, ['razorpay', 'cod']) ? $method_raw : 'cod';

$method_map = [
    'razorpay' => 'Razorpay (Online)',
    'cod'      => 'Cash On Delivery',
];
$payment_method_label = $method_map[$method_raw] ?? 'Unknown';

// optional: capture razorpay identifiers
$razorpay_payment_id = $_GET['payment_id'] ?? '';
$razorpay_order_id   = $_GET['order_id'] ?? '';
$razorpay_signature  = $_GET['signature'] ?? '';

// Fetch recent purchases for this customer
// Get purchases from the last 10 minutes to show the most recent order
$query = "SELECT p.id, pr.name AS product_name, pr.category, p.price, 1 as quantity 
          FROM purchases p 
          JOIN products pr ON p.product_id = pr.id 
          WHERE p.user_id = ? 
          AND datetime(p.purchase_date) >= datetime('now', '-10 minutes')
          ORDER BY p.purchase_date DESC";
$stmt = $conn->prepare($query);
$stmt->bindValue(1, $customer_id, SQLITE3_INTEGER);
$result = $stmt->execute();

$purchases = [];
$total_amount = 0;

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $row['total_price'] = $row['price'] * $row['quantity'];
    $total_amount += $row['total_price'];
    $purchases[] = $row;
}

if (empty($purchases)) {
    // No recent purchases found - show helpful message
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Receipt - Barter Bay</title>
        <style>
            body { font-family: Arial; background: linear-gradient(to right, red, blue); color: white; text-align: center; padding: 50px; }
            .message-box { background: rgba(0,0,0,0.8); padding: 40px; border-radius: 10px; max-width: 600px; margin: 0 auto; }
            .btn { background: red; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
            .btn:hover { background: blue; }
        </style>
    </head>
    <body>
        <div class="message-box">
            <h2>✅ Purchase Completed!</h2>
            <p>Your order has been processed successfully and your cart has been cleared.</p>
            <p>You can view your purchase history from your dashboard.</p>
            <a href="my_purchases.php" class="btn">View Purchase History</a>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
        </div>
    </body>
    </html>';
    exit();
}

// Taxes
$gst_rate = 0.18;
$gst_amount = $total_amount * $gst_rate;
$cgst = $gst_amount / 2;
$sgst = $gst_amount / 2;
$grand_total = $total_amount + $gst_amount;

$receipt_id = rand(100000, 999999);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart Receipt - Barter Bay</title>
    <style>
        body {
            background: linear-gradient(to right, red, blue);
            color: white;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
        }

        .wrapper-border {
            margin: 20px auto;
            border: 3px dashed #000;
            border-radius: 16px;
            background: white;
            color: black;
            max-width: 750px;
        }

        .bill-container {
            background: #fff;
            color: #000;
            padding: 30px 40px;
            border-radius: 12px;
            max-width: 750px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px dotted #333;
        }

        .header img {
            width: 80px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #c0392b;
        }

        .tagline {
            font-style: italic;
            font-size: 14px;
            color: #555;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.items th {
            background: #c0392b;
            color: white;
            padding: 10px;
        }

        table.items td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background: #f7f7f7;
        }

        .offer-section {
            margin-top: 30px;
            background: #f9f9f9;
            border: 1px dashed #c0392b;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .offer-section h3 {
            color: #c0392b;
            margin-top: 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 13px;
            color: #333;
        }

        .btn-container {
            text-align: center;
            margin: 20px 0;
        }

        .btn {
            background: #c0392b;
            color: white;
            padding: 10px 18px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #2980b9;
        }

        @media print {
            .btn-container, #navbar { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body>

<div id="navbar"><?php include 'navbar.php'; ?></div>

<div class="wrapper-border">
    <div class="bill-container">

        <div class="header">
            <img src="images/seal.png" alt="Barter Bay">
            <h1>Barter Bay</h1>
            <div class="tagline">"Where every trade is a treasure!"</div>
        </div>

        <table style="width:100%; margin-top:20px;">
            <tr>
                <td><strong>Receipt #:</strong> <?= $receipt_id ?></td>
                <td style="text-align:right;"><strong>Date:</strong> <?= date("F j, Y, g:i A") ?></td>
            </tr>
            <tr>
                <td><strong>Customer ID:</strong> <?= $customer_id ?></td>
                <td style="text-align:right;"><strong>Cashier ID:</strong> BB-CASH-001</td>
            </tr>
            <tr>
                <td><strong>Payment Mode:</strong> <?= $payment_method_label ?></td>
                <td style="text-align:right;"><strong>Store:</strong> Barter Bay, Coimbatore, Tamil Nadu</td>
            </tr>

            <?php if ($method_raw === 'razorpay' && $razorpay_payment_id): ?>
            <tr>
                <td><strong>Razorpay Payment ID:</strong> <?= htmlspecialchars($razorpay_payment_id) ?></td>
                <td style="text-align:right;"><strong>Order ID:</strong> <?= htmlspecialchars($razorpay_order_id) ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <table class="items">
            <tr>
                <th>Cart ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Price (₹)</th>
                <th>Total (₹)</th>
            </tr>

            <?php foreach ($purchases as $purchase): ?>
            <tr>
                <td><?= $purchase['id'] ?></td>
                <td><?= htmlspecialchars($purchase['product_name']) ?></td>
                <td><?= htmlspecialchars($purchase['category']) ?></td>
                <td><?= $purchase['quantity'] ?></td>
                <td><?= number_format($purchase['price'], 2) ?></td>
                <td><?= number_format($purchase['total_price'], 2) ?></td>
            </tr>
            <?php endforeach; ?>

            <tr class="total-row">
                <td colspan="5" style="text-align:right;">Subtotal</td>
                <td>₹<?= number_format($total_amount, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">CGST (9%)</td>
                <td>₹<?= number_format($cgst, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">SGST (9%)</td>
                <td>₹<?= number_format($sgst, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">Grand Total</td>
                <td><strong>₹<?= number_format($grand_total, 2) ?></strong></td>
            </tr>
        </table>

        <div class="offer-section">
            <h3>🎉 Special Offer!</h3>
            <p>Use code <strong>BARTER20</strong> on your next purchase and get <strong>20% OFF</strong> instantly!</p>
            <small>Offer valid for 7 days.</small>
        </div>

        <div class="footer">
            <hr style="width:80%; border-top:2px dotted #bbb;">
            <p>Thank you for shopping with <strong>Barter Bay</strong>!</p>
            <ul style="list-style:none; padding:0; line-height:1.8;">
                <li><strong>Store Address:</strong> Barter Bay, Coimbatore, Tamil Nadu</li>
                <li><strong>Support:</strong> 📞 9688748656, 9342816669</li>
                <li><strong>Email:</strong> <a href="mailto:support@barterbay.com">support@barterbay.com</a></li>
                <li>Come again for the best shopping & trading experience! 🛍️</li>
            </ul>
        </div>

    </div>
</div>

<div class="btn-container">
    <button class="btn" onclick="window.print()">🖨️ Print Bill</button>
    <button class="btn" onclick="window.location.href='dashboard.php'">🏠 Back to Dashboard</button>
</div>

</body>
</html>
