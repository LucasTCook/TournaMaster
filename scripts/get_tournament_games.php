<?php
require '../config.php';  // Adjust path as necessary
require '../models/TournamentGame.php';
require '../repos/TournamentGamesRepository.php';

// Ensure request method is GET and tournamentId is provided
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tournamentId'])) {
    $tournamentId = $_GET['tournamentId'];
    $tournamentGameRepo = new TournamentGamesRepository();

    // Fetch all games associated with the tournament ID
    $games = $tournamentGameRepo->getGamesByTournamentId($tournamentId);

    if ($games) {
        echo json_encode(['success' => true, 'games' => $games]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No games found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
