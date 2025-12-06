<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header("Location: index.php");
    exit();
}

include 'config.php';
$user_id = $_SESSION['customer'];

// Check if database connection is working
if (!$conn) {
    die("Database connection failed: " . $conn->lastErrorMsg());
}

// Fetch user data
$query = "SELECT * FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->lastErrorMsg());
}

$stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    die("Error: User not found in the database.");
}

// Default values for missing fields
$profile_picture = $user['profile_picture'] ?? 'default.png';
$user_email = $user['email'] ?? 'Not available';
$joined_date = !empty($user['created_at']) ? date("F d, Y", strtotime($user['created_at'])) : 'Not available';

// Fetch total purchases from both tables (purchases and transactions)
$query_purchases = "SELECT COUNT(*) AS total_purchases FROM purchases WHERE user_id = :user_id";
$stmt_purchases = $conn->prepare($query_purchases);
$stmt_purchases->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result_purchases = $stmt_purchases->execute();
$purchases = $result_purchases->fetchArray(SQLITE3_ASSOC);
$total_purchases = $purchases['total_purchases'] ?? 0;

// Also check transactions table (legacy)
$query_transactions = "SELECT COUNT(*) AS total_transactions FROM transactions WHERE user_id = :user_id";
$stmt_transactions = $conn->prepare($query_transactions);
if ($stmt_transactions) {
    $stmt_transactions->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $result_transactions = $stmt_transactions->execute();
    $transactions = $result_transactions->fetchArray(SQLITE3_ASSOC);
    $total_purchases += $transactions['total_transactions'] ?? 0;
}

// Fetch total trades
$query_trades = "SELECT COUNT(*) AS total_trades FROM trades WHERE sender_id = :user_id OR receiver_id = :user_id";
$stmt_trades = $conn->prepare($query_trades);
$stmt_trades->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
$result_trades = $stmt_trades->execute();
$trades = $result_trades->fetchArray(SQLITE3_ASSOC);
$total_trades = $trades['total_trades'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/seal.png?v=1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body { background: linear-gradient(to right, red, blue); color: white; text-align: center; font-family: Arial, sans-serif; }
        .container { margin: 50px auto; width: 80%; padding: 20px; background: rgba(0, 0, 0, 0.8); border-radius: 10px; }
        h2 { color: yellow; }
        .profile-card { padding: 20px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; margin-bottom: 20px; }
        .profile-card img { width: 100px; height: 100px; border-radius: 50%; margin-bottom: 10px; }
        .dashboard-stats { display: flex; justify-content: space-around; margin-top: 20px; }
        .stat-box { background: rgba(255, 255, 255, 0.2); padding: 20px; border-radius: 10px; width: 40%; }
        .button-group { margin-top: 20px; }
        .btn { background: red; color: white; padding: 10px 15px; border: none; cursor: pointer; transition: 0.3s; margin: 5px; display: inline-block; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: blue; }
        .btn-logout { background: darkred; }
        .btn-logout:hover { background: maroon; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Welcome To Barter Bay!</h2>

    <!-- User Profile Section -->
    <div class="profile-card">
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
        <p><strong>Joined On:</strong> <?php echo htmlspecialchars($joined_date); ?></p>
        <a href="profile.php" class="btn">Edit Profile</a>
    </div>

    <!-- Dashboard Stats -->
    <div class="dashboard-stats">
        <div class="stat-box">
            <h3>Total Purchases</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0;"><?php echo intval($total_purchases); ?></p>
            <a href="my_purchases.php" class="btn" style="font-size: 14px; padding: 8px 12px;">View History</a>
        </div>
        <div class="stat-box">
            <h3>Total Trades</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0;"><?php echo intval($total_trades); ?></p>
            <a href="my_trades.php" class="btn" style="font-size: 14px; padding: 8px 12px;">View Trades</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="button-group">
        <a href="products.php" class="btn">Browse Products</a>
        <a href="trade.php" class="btn">Trade Items</a>
        <a href="my_trades.php" class="btn">My Trades</a>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
