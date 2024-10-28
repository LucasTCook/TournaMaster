<?php
require '../config.php';
require '../repos/TournamentGamesRepository.php';

header('Content-Type: application/json');

// Get the tournament ID from the POST request
$tournamentGameId = $_POST['tournament_game_id'] ?? null;
$tournamentGameStatus = $_POST['status'] ?? null;

if (!$tournamentGameId) {
    echo json_encode(['error' => 'Invalid request: tournament ID missing']);
    exit;
}

try {
    $tournamentGameRepo = new TournamentGamesRepository();
    $tournamentGameRepo->updateStatus($tournamentGameId, $tournamentGameStatus);
    echo json_encode(['success' => 'Updated Tournament Game Status Successfully!']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error updating status: ' . $e->getMessage()]);
}
