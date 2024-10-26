<?php
require '../repos/BracketRepository.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['gameId'])) {
    $gameId = $_GET['gameId'];
    $bracketRepo = new BracketRepository();

    try {
        $bracketData = $bracketRepo->getBracketByGameId($gameId);
        echo json_encode(['success' => true, 'data' => $bracketData]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
