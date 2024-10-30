<?php
require '../repos/TrophiesRepository.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;

$response = ['success' => false, 'trophies' => []];

if (!$userId) {
    $response['error'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

try {
    $trophiesRepo = new TrophiesRepository();
    $response = $trophiesRepo->getPlayerTrophies($userId);
} catch (Exception $e) {
    $response['error'] = 'Failed to fetch trophies: ' . $e->getMessage();
}

echo json_encode($response);
