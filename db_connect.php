<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'localhost';
$dbname = 'thoughts_db';
$username = 'root';
$password = '';

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection and XSS attacks
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $thoughts = filter_input(INPUT_POST, 'thoughts', FILTER_SANITIZE_STRING);

    // Check if required fields are filled
    if ($name && $email && $address && $thoughts) {
        // Prepare and bind the statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO thoughts (name, email, address, thoughts) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("ssss", $name, $email, $address, $thoughts);

        // Execute the statement and check if successful
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Thoughts submitted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    }

    // Close the connection
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>