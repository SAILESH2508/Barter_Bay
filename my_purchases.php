<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';
$customer_id = $_SESSION['customer'];

// Fetch all purchases for this customer
$query = "SELECT p.id, p.purchase_date, p.price, p.payment_method, 
                 pr.name AS product_name, pr.category, pr.image
          FROM purchases p
          JOIN products pr ON p.product_id = pr.id
          WHERE p.user_id = ?
          ORDER BY p.purchase_date DESC";

$stmt = $conn->prepare($query);
$stmt->bindValue(1, $customer_id, SQLITE3_INTEGER);
$result = $stmt->execute();

$purchases = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $purchases[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/seal.png?v=1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases - Barter Bay</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, red, blue);
            color: white;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.85);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: yellow;
            margin-bottom: 30px;
        }
        .purchase-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .purchase-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            transition: transform 0.3s;
        }
        .purchase-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }
        .purchase-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .purchase-info {
            margin: 10px 0;
        }
        .purchase-info strong {
            color: yellow;
        }
        .payment-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }
        .badge-razorpay {
            background: #3395ff;
        }
        .badge-cod {
            background: #ff9800;
        }
        .no-purchases {
            text-align: center;
            padding: 40px;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            background: red;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn:hover {
            background: blue;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>My Purchase History</h2>

    <?php if (empty($purchases)): ?>
        <div class="no-purchases">
            <p>You haven't made any purchases yet.</p>
            <a href="products.php" class="btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="purchase-grid">
            <?php foreach ($purchases as $purchase): ?>
                <div class="purchase-card">
                    <img src="images/<?= htmlspecialchars($purchase['image']) ?>" alt="<?= htmlspecialchars($purchase['product_name']) ?>">
                    <h3><?= htmlspecialchars($purchase['product_name']) ?></h3>
                    <div class="purchase-info">
                        <strong>Category:</strong> <?= htmlspecialchars($purchase['category']) ?>
                    </div>
                    <div class="purchase-info">
                        <strong>Price:</strong> ₹<?= number_format($purchase['price'], 2) ?>
                    </div>
                    <div class="purchase-info">
                        <strong>Date:</strong> <?= date('M d, Y', strtotime($purchase['purchase_date'])) ?>
                    </div>
                    <div class="purchase-info">
                        <?php 
                        $payment_method = $purchase['payment_method'] ?? 'cod';
                        $badge_class = $payment_method === 'razorpay' ? 'badge-razorpay' : 'badge-cod';
                        ?>
                        <span class="payment-badge <?= $badge_class ?>">
                            <?= strtoupper($payment_method) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 30px;">
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
