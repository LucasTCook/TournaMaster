<?php
require '../config.php';  // DB connection and setup
require '../models/User.php';
require '../repos/TournamentUsersRepository.php';

header('Content-Type: application/json');

if (isset($_POST['tournamentId'])) {
    $tournamentId = $_POST['tournamentId'];
    $tournamentUsersRepo = new TournamentUsersRepository();

    // Fetch all users associated with the tournament
    $players = $tournamentUsersRepo->getPlayersByTournamentId($tournamentId);

    if ($players) {
        echo json_encode(['success' => true, 'players' => $players]);
    } else {
        echo json_encode(['error' => 'No players found for this tournament']);
    }
} else {
    echo json_encode(['error' => 'Tournament ID not provided']);
}
