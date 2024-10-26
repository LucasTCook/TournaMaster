<?php
require '../config.php';
require '../models/TournamentGame.php';
require '../models/Game.php';
require '../repos/TournamentGamesRepository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentGameId = $_POST['id'] ?? null;

    if (empty($tournamentGameId)) {
        echo json_encode(['error' => 'Invalid tournament game ID']);
        exit;
    }

    // Initialize the TournamentGame model
    $tournamentGame = new TournamentGame($tournamentGameId);

    // Check if the tournament game exists
    if (!$tournamentGame->getId()) {
        echo json_encode(['error' => 'Tournament game not found']);
        exit;
    }

    // Store the Game ID before deleting the tournament game
    $gameId = $tournamentGame->getGameId();

    // Delete the TournamentGame record
    if ($tournamentGame->delete()) {
        
        // Use the repository to check for other tournament links
        $tournamentGameRepo = new TournamentGamesRepository();
        $otherTournamentGames = $tournamentGameRepo->getGamesByGameId($gameId);

        // Delete the Game only if there are no other tournament links
        if (empty($otherTournamentGames)) {
            $game = new Game($gameId);
            $game->delete();
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete tournament game']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
