<?php
require_once '../models/User.php';  // Ensure the User model is included

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $userModel = new User();

    // Check if the user exists by username or email
    if ($userModel->userExists($username, $email)) {
        echo 'exists';
    } else {
        // Attempt to create the user
        if ($userModel->createUser($username, $password, $email)) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}
