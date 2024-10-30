<?php
require '../config.php';
require '../repos/TournamentGamesRepository.php';

header('Content-Type: application/json');

$tournamentGameId = $_GET['tournamentGameId'] ?? null;

if (!$tournamentGameId) {
    echo json_encode(['success' => false, 'error' => 'Tournament Game ID is required']);
    exit;
}

try {
    $gamesRepo = new TournamentGamesRepository();
    $gameDetails = $gamesRepo->getGameDetailsById($tournamentGameId);

    if ($gameDetails) {
        echo json_encode(['success' => true, 'data' => $gameDetails]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Tournament Game not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
