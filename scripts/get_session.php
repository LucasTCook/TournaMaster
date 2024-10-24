<?php
session_start();

// Fetch user ID from the session
if (isset($_SESSION)) {
    echo json_encode($_SESSION);
} else {
    echo json_encode(['error' => 'User is not logged in']);
}
