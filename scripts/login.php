<?php
require '../models/user.php';  // Include User model

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Create a new User model instance
    $userModel = new User();

    // Check if the user exists
    $user = $userModel->getByUsername($username);

    if ($user) {
        // Verify password
        if ($userModel->verifyPassword($password, $user['password'])) {
            // Success: Log the user in
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];  // Save user ID for future queries
            $_SESSION['username'] = $user['username'];  // Save username for display
            $_SESSION['profile_photo'] = $user['profile_image_url'];
            $_SESSION['role'] = $user['role'];  // Save role for access control

            echo 'success';  // Respond with success for JavaScript
        } else {
            // Invalid password
            echo 'Invalid password';
        }
    } else {
        // User not found
        echo 'User not found';
    }
}
