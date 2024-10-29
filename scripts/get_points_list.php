<?php
require_once '../config.php';
require_once '../repos/PointsRepository.php';
require_once '../repos/TeamsRepository.php';

header('Content-Type: application/json');

$tournamentGameId = $_GET['tournament_game_id'] ?? null;

if (!$tournamentGameId) {
    echo json_encode(['error' => 'Invalid tournament game ID']);
    exit;
}

try {
    $pointsRepo = new PointsRepository();
    $teamsRepo = new TeamsRepository();

    // Fetch players with their points and team info for the leaderboard
    $leaderboardData = $pointsRepo->getLeaderboardDataByTournamentGameId($tournamentGameId);

    // Sort by points in descending order to prepare for top 3 logic
    usort($leaderboardData, fn($a, $b) => $b['points'] <=> $a['points']);

    echo json_encode(['success' => true, 'data' => $leaderboardData]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error retrieving leaderboard data: ' . $e->getMessage()]);
}
