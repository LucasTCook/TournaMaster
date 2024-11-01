<?php

require '../models/User.php';  // Include the User model
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle logo upload
    $targetDir = "../images/uploads/profile_photos/";
    $targetFile = $targetDir . basename($_FILES["profile-photo"]["name"]);
    if (move_uploaded_file($_FILES["profile-photo"]["tmp_name"], $targetFile)) {
        $profilePhotoUrl = basename($_FILES["profile-photo"]["name"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading profile photo.']);
        exit();
    }

    // Use the Tournament model to insert the tournament
    $user = new User($_SESSION['user_id']);
    $user->profile_image_url = $profilePhotoUrl;

    // Save the tournament
    if ($user->update()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create tournament.']);
    }
}
