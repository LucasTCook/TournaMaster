<?php
require '../config.php';
require '../models/Points.php';
require '../models/TournamentGame.php';
require '../repos/PointsRepository.php';
require '../repos/TournamentUsersRepository.php';

header('Content-Type: application/json');

// Get the tournament ID from the POST request
$tournamentGameId = $_POST['tournament_game_id'] ?? null;

if (!$tournamentGameId) {
    echo json_encode(['error' => 'Invalid request: tournament ID missing']);
    exit;
}

try {

    $tournamentGame = new TournamentGame($tournamentGameId);
    // Initialize repositories
    $tournamentUsersRepo = new TournamentUsersRepository();
    $pointsRepo = new PointsRepository();

    // Fetch all active players for the specified tournament
    $activePlayers = $tournamentUsersRepo->getActivePlayersByTournamentId($tournamentGame->getTournamentId());

    // Loop through each player and generate Points record if it doesn't exist
    foreach ($activePlayers as $player) {
        $userId = $player['id'];

        // Check if there's an existing Points record for this user with null points and null tournament_points
        $existingPoints = $pointsRepo->getPointsByTournamentAndUser($tournamentGameId, $userId);

        if (!$existingPoints) {
            // Create a new Points record if not found
            $points = new Points();
            $points->setTournamentGameId($tournamentGameId);
            $points->setUserId($userId);
            $points->setPoints(null);           // Initialize points to null
            $points->setTournamentPoints(null); // Initialize tournament_points to null
            $points->save();
        }
    }

    echo json_encode(['success' => 'Points records generated for active players']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error generating points: ' . $e->getMessage()]);
}
