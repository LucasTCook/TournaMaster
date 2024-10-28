<?php
require '../config.php';
require '../repos/BracketRepository.php';

$tournamentGameId = $_POST['tournamentGameId'];
$round = $_POST['round'];
$matchNumber = $_POST['match_number'];

$bracketRepo = new BracketRepository();
$players = $bracketRepo->getPlayersInMatch($tournamentGameId, $round, $matchNumber);

echo json_encode($players);
