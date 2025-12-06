<?php
// ===============================
// ERROR REPORTING (DEV MODE)
// ===============================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===============================
// SQLITE DATABASE CONNECTION
// ===============================
class MyDB extends SQLite3 {
    function __construct() {
        // Database name
        $this->open('barter_bay.db');
    }
}

$conn = new MyDB();
if (!$conn) {
    die("Connection failed: " . $conn->lastErrorMsg());
}

// ===============================
// CSRF TOKEN HELPER FUNCTIONS
// ===============================

/**
 * Generate and store CSRF token in session
 */
function generateCSRFToken() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ===============================
// RAZORPAY PAYMENT CONFIGURATION
// ===============================

// Load from environment variables or use defaults (for development only)
// In production, set these in your server environment or .env file
define("RAZORPAY_KEY_ID",     getenv('RAZORPAY_KEY_ID') ?: "rzp_test_RNh0qZpmrPFLcI");
define("RAZORPAY_KEY_SECRET", getenv('RAZORPAY_KEY_SECRET') ?: "gyWUUP337T6kY15UMB2EDjKR");

// ===============================
// SITE GLOBAL SETTINGS (OPTIONAL)
// ===============================
define("SITE_NAME", "Barter Bay");

// ===============================
// RAZORPAY API ORDER FUNCTION
// ===============================

function createRazorpayOrder($amount_in_paise, $receipt_id)
{
    $url = "https://api.razorpay.com/v1/orders";

    $data = [
        "amount"          => $amount_in_paise,  // Razorpay expects paise
        "currency"        => "INR",
        "receipt"         => $receipt_id,
        "payment_capture" => 1                 // Auto-capture payment
    ];

    $payload = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error || $httpcode >= 400) {
        return [
            "error"    => $error ?: "HTTP error $httpcode",
            "response" => $response
        ];
    }

    return json_decode($response, true);
}
?>
