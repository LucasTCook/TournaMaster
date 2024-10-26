<?php
require '../config.php';
require '../repos/TournamentUsersRepository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $tournamentId = $_POST['tournamentId'];

    $tournamentUsersRepo = new TournamentUsersRepository();

    if ($tournamentUsersRepo->reinstatePlayer($userId, $tournamentId)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to reinstate player']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
