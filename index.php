<!DOCTYPE html>
<html lang="en">

<?php
@session_start();

// Check if user is logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    if (isset($_SESSION['last_activity'])) {
        // Calculate the time since the last activity
        $time_since_last_activity = time() - $_SESSION['last_activity'];
        
        if ($time_since_last_activity > $session_timeout) {
            // If the time exceeds the session timeout, destroy the session
            session_unset();     // Unset session variables
            session_destroy();   // Destroy the session
            header("Location: /login");  // Redirect to login with session expired message
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
    // User is logged in, redirect to profile
    header("Location: profile");
    exit();
} else {
    // User is not logged in, redirect to login
    header("Location: login");
    exit();
}
?>
