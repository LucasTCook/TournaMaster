<?php
require '../repos/PointsRepository.php';

header('Content-Type: application/json');

// Check if tournament_id is provided
$tournamentId = $_GET['tournament_id'] ?? null;
if (!$tournamentId) {
    echo json_encode(['success' => false, 'error' => 'Tournament ID is required']);
    exit;
}

try {
    $pointsRepo = new PointsRepository();
    $leaderboard = $pointsRepo->getAllUserPointsByTournamentId($tournamentId);

    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
