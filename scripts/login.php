<?php
require '../config.php';  // Include database connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");

    if (!$stmt) {
        echo 'Execute failed: no stmt'; // Return error if the prepare statement fails
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Success: Log the user in
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];  // Save user ID for future queries
            $_SESSION['username'] = $user['username'];  // Save username for display
            $_SESSION['role'] = $user['role'];
            echo 'success';  // Respond with success for JavaScript
        } else {
            // Invalid password
            echo 'Execute failed: ' . $stmt->error;
        }
    } else {
        // User not found
        echo 'Execute failed: ' . $stmt->error;
    }

    $stmt->close();
    $db->close();
}
