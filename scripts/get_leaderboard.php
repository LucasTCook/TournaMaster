<?php
require_once '../config.php';
require_once '../repos/TournamentGamesRepository.php';
require_once '../repos/PointsRepository.php';

header('Content-Type: application/json');

$tournamentId = $_GET['tournament_id'] ?? null;

if (!$tournamentId) {
    echo json_encode(['success' => false, 'error' => 'Invalid tournament ID']);
    exit;
}

try {
    $gamesRepo = new TournamentGamesRepository();
    $pointsRepo = new PointsRepository();

    // Get all games in the tournament with game images
    $games = $gamesRepo->getGamesByTournamentId($tournamentId);

    // Get players and points per game for this tournament
    $playersPoints = $pointsRepo->getPlayersTournamentPointsByTournamentId($tournamentId);

    // Aggregate total points per player across all games
    $totals = [];
    foreach ($playersPoints as $entry) {
        $userId = $entry['user_id'];
        $gameId = $entry['game_id'];
        $points = $entry['points'];

        // Initialize player data if not already in totals
        if (!isset($totals[$userId])) {
            $totals[$userId] = [
                'username' => $entry['username'],
                'games' => array_fill_keys(array_column($games, 'id'), '-'),
                'total_points' => 0
            ];
        }

        // Assign points for each game and calculate total
        $totals[$userId]['games'][$gameId] = $points;
        $totals[$userId]['total_points'] += $points;
    }

    // Sort by total points in descending order
    usort($totals, function ($a, $b) {
        return $b['total_points'] - $a['total_points'];
    });

    echo json_encode([
        'success' => true,
        'data' => [
            'games' => $games,
            'players' => array_values($totals)  // Remove user ID keys for a clean JSON array
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to retrieve tournament results: ' . $e->getMessage()]);
}
