<?php
session_start();
require '../repos/TournamentRepository.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'current'; // Default to 'current' if no type provided

// Instantiate the repository and fetch tournaments
$tournamentsRepo = new TournamentRepository();
$tournaments = $tournamentsRepo->getTournamentsByUserAndType($userId, $type);

// Return the data as JSON
echo json_encode(['success' => true, 'tournaments' => $tournaments]);
