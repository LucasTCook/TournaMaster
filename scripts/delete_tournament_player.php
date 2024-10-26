<?php
require '../config.php';
require '../repos/TournamentUsersRepository.php';
require '../repos/PointsRepository.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'], $_POST['tournamentId'])) {
    $userId = $_POST['userId'];
    $tournamentId = $_POST['tournamentId'];

    $tournamentUsersRepo = new TournamentUsersRepository();
    $pointsRepo = new PointsRepository();

    // Check if the user has points in any game in this tournament
    $pointsExist = $pointsRepo->userHasPointsInTournament($userId, $tournamentId);

    if ($pointsExist) {
        // If points exist, set active to 0
        $result = $tournamentUsersRepo->setUserInactive($tournamentId, $userId);
    } else {
        // If no points exist, delete from tournament_users
        $result = $tournamentUsersRepo->deleteUserFromTournament($tournamentId, $userId);
    }

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update or delete user']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
