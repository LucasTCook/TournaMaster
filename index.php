<!DOCTYPE html>
<html lang="en">

<?php
@session_start();

// Check if user is logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    // User is logged in, redirect to profile
    header("Location: profile");
    exit();
} else {
    // User is not logged in, redirect to login
    header("Location: login");
    exit();
}
?>
