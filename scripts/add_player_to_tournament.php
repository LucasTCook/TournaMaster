<?php
require '../config.php';
require '../repos/TournamentUsersRepository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournamentId = $_POST['tournamentId'];
    $userId = $_POST['id'];

    // Initialize TournamentUsersRepository
    $tournamentUsersRepo = new TournamentUsersRepository();

    // Attempt to add user to the tournament
    $added = $tournamentUsersRepo->addUserToTournament($tournamentId, $userId);

    if ($added) {
        echo json_encode(['success' => 'User added to tournament']);
    } else {
        echo json_encode(['error' => 'User already in tournament or failed to add']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
