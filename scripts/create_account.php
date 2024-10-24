<?php
require '../config.php';  // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Check if user exists
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");

    if (!$stmt) {
        // Output the error if prepare fails
        die('Prepare failed: ' . $db->error);
    }

    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'exists';
    } else {
        // Hash the password and insert the new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");

        if (!$stmt) {
            // Output the error if prepare fails
            die('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param("sss", $username, $hashedPassword, $email);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Execute failed: ' . $stmt->error;
        }
    }

    $stmt->close();
    $db->close();
}
