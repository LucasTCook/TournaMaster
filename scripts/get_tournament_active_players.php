<?php
// Include the necessary files
require '../config.php';
require '../repos/TournamentUsersRepository.php';

// Get the tournament ID from a request or defined variable
$tournamentId = $_GET['id'];

// Initialize the repository
$tournamentUsersRepo = new TournamentUsersRepository();

try {
    // Get the active player count
    $activePlayers = $tournamentUsersRepo->getActivePlayersByTournamentId($tournamentId);
    echo json_encode(['success' => true, 'activePlayers' => $activePlayers]);
} catch (Exception $e) {
    // Handle errors
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
