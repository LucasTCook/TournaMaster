<?php
require '../config.php';
require '../repos/BracketRepository.php';

$tournamentGameId = $_POST['tournamentGameId'];
$round = $_POST['round'];
$matchNumber = $_POST['match_number'];
$winners = json_decode($_POST['winners'], true); // List of winner user IDs

$bracketRepo = new BracketRepository();
$teams = $bracketRepo->getPlayersInMatch($tournamentGameId, $round, $matchNumber);

//remove any previous recordings from this match
foreach ($teams as $team) {
    $bracketRepo->removePlayerResult(
        $tournamentGameId,
        $round,
        $matchNumber,
        $team['team_id'],
    );
}

// Update player results based on winners
foreach ($teams as $team) {
    $result = in_array($team['team_id'], $winners) ? 'WIN' : 'LOSE';
    $bracketRepo->updatePlayerResult(
        $tournamentGameId,
        $round,
        $matchNumber,
        $team['team_id'],
        $result
    );
}

echo json_encode(['success' => true]);
