<?php
require '../config.php';
require '../models/Game.php';
require '../models/TournamentGame.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentGameId = $_POST['id'];
    $gameName = $_POST['gameName'];
    $gameType = $_POST['gameType'];
    $sizeOfTeams = isset($_POST['sizeOfTeams']) ? $_POST['sizeOfTeams'] : 0;
    $teamsPerMatch = isset($_POST['teamsPerMatch']) ? $_POST['teamsPerMatch'] : 0;
    $winnersPerMatch = isset($_POST['winnersPerMatch']) ? $_POST['winnersPerMatch'] : 0;

    // Load Game by ID and update name, slug
    $tournamentGame = new TournamentGame($tournamentGameId);
    $game = new Game($tournamentGame->getGameId());
    $game->setName($gameName);
    $game->setSlug(generateSlug($gameName));

    if (!$game->update()) {
        echo json_encode(['error' => 'Failed to update game']);
        exit;
    }

    // Update TournamentGame fields
    $tournamentGame->setType($gameType);
    $tournamentGame->setTeamSize($sizeOfTeams);
    $tournamentGame->setTeamsPerMatch($teamsPerMatch);
    $tournamentGame->setWinnersPerMatch($winnersPerMatch);
    

    if ($tournamentGame->update()) {
        echo json_encode(['success' => true, 'message' => 'Tournament game updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update tournament game configuration']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

// Helper function to create a dashed slug
function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string))) . '-custom';
}
