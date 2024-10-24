<?php
require '../config.php';
require '../repos/TournamentRepository.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

$tournamentRepo = new TournamentRepository();
$tournaments = $tournamentRepo->getTournamentsByCreatorId($user_id);

if ($tournaments) {
    echo json_encode(['success' => true, 'data' => $tournaments]);
} else {
    echo json_encode(['success' => false, 'message' => 'No tournaments found.']);
}
