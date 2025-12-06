<?php
session_start();
include 'config.php';

// Generate CSRF token
$csrf_token = generateCSRFToken();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }
    
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');

    // Prepare SQL query to insert the contact form data into SQLite
    $stmt = $conn->prepare("INSERT INTO contact (name, email, message) VALUES (:name, :email, :message)");
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':message', $message, SQLITE3_TEXT);

    $stmt->execute();

    echo "<script>alert('Message Sent Successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="images/seal.png?v=1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>

    <!-- ✅ Google Font Added -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ✅ Apply to whole website */
        * {
            font-family: 'Poppins', sans-serif;
        }

        body { 
            background: linear-gradient(to right, red, blue); 
            color: white; 
            text-align: center; 
        }

        .container { 
            margin: 50px auto; 
            width: 50%; 
            padding: 20px; 
            background: rgba(0, 0, 0, 0.8); 
            border-radius: 10px; 
        }

        input, textarea { 
            width: 90%; 
            padding: 10px; 
            margin: 10px 0; 
            border-radius: 4px;
            border: none;
            outline: none;
        }

        .btn { 
            background: red; 
            color: white; 
            padding: 10px; 
            border: none; 
            cursor: pointer; 
            transition: 0.3s; 
            border-radius: 5px;
            font-weight: 600;
        }

        .btn:hover { 
            background: blue; 
        }
    </style>
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Contact Us</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit" class="btn">Send Message</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
