<?php
require '../repos/TeamsRepository.php';
require '../repos/PointsRepository.php';

try {
    $points = $_POST['points'];
    $teamId = $_POST['teamId'];
    $tournamentGameId = $_POST['tournamentGameId'];
    
    $teamsRepo = new TeamsRepository();
    $pointsRepo = new PointsRepository();
    $players = $teamsRepo->getPlayersOnTeam($teamId);
    
    foreach($players as $player) {
        $pointsRepo->savePointsForPlayer($player, $points, $tournamentGameId);
    }
    echo json_encode(
        [
            "success" => true,
            "message" => "points added successfully"
        ]
    );

} catch (Exception $e) {
    // Return error as JSON
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}