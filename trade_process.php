<?php
session_start();
include 'config.php';

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['customer'])) {
    header("Location: index.php");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    die("Invalid CSRF token. Please try again.");
}

// Getting user details from the session
$sender_id = $_SESSION['customer'];  // User's ID from session
$receiver_id = $_POST['receiver_id'] ?? null;  // Receiver ID from form
$sender_product_id = $_POST['sender_product_id'] ?? null;  // Sender product ID
$receiver_product_id = $_POST['receiver_product_id'] ?? null;  // Receiver product ID

// Check if any form data is missing
if ($receiver_id === null || $sender_product_id === null || $receiver_product_id === null) {
    die("Error: Missing required form data. Please try again.");
}

// Prepare SQL query to insert into trades table
$stmt = $conn->prepare("INSERT INTO trades (sender_id, receiver_id, sender_product_id, receiver_product_id) 
                        VALUES (:sender_id, :receiver_id, :sender_product_id, :receiver_product_id)");

if ($stmt) {
    // Bind values to SQL parameters
    $stmt->bindValue(':sender_id', $sender_id, SQLITE3_INTEGER);
    $stmt->bindValue(':receiver_id', $receiver_id, SQLITE3_INTEGER);
    $stmt->bindValue(':sender_product_id', $sender_product_id, SQLITE3_INTEGER);
    $stmt->bindValue(':receiver_product_id', $receiver_product_id, SQLITE3_INTEGER);

    // Execute the query
    if ($stmt->execute()) {
        echo "<script>alert('Trade request sent successfully!'); window.location.href='my_trades.php';</script>";
    } else {
        // Error handling using SQLite's lastErrorMsg() method
        $errorMsg = $conn->lastErrorMsg();
        echo "Trade failed: " . $errorMsg; // Show error message if the query failed
    }
} else {
    // Handle error if the statement preparation fails
    $errorMsg = $conn->lastErrorMsg();
    echo "Database error: " . $errorMsg;
}
?>
