<?php

header('Content-Type: application/json');

include_once '../models/TournamentGame.php';
include_once '../repos/BracketRepository.php';
include_once '../repos/TeamsRepository.php';
include_once '../repos/TournamentUsersRepository.php';

try {

    // Instantiate repositories
    $bracketRepo = new BracketRepository();
    $teamsRepo = new TeamsRepository();

    // Get parameters from AJAX
    $tournament_id = isset($_POST['tournament_id']) ? (int)$_POST['tournament_id'] : null;
    $tournament_game_id = isset($_POST['tournament_game_id']) ? (int)$_POST['tournament_game_id'] : null;

    $tournamentGame = new TournamentGame($tournament_game_id);
    $teamsPerMatch = $tournamentGame->getTeamsPerMatch();  // Teams per match from tournament config
    $winnersPerMatch = $tournamentGame->getWinnersPerMatch();  // Winners per match from tournament config

    if (!$tournament_id || !$tournament_game_id) {
        echo json_encode(["error" => "Missing tournament or game ID"]);
        exit;
    }

    // Calculate total teams based on player count
    $tournamentUsers = new TournamentUsersRepository();
    $teams = $teamsRepo->getTeams($tournament_game_id); // Retrieve team data
    $numberOfTeams = count($teams);
    $perfectBracketNumber = $teamsPerMatch;

    $prelimMatches = 0;
    while($perfectBracketNumber < $numberOfTeams) {
        if(abs($perfectBracketNumber-$numberOfTeams) < $perfectBracketNumber) {
            $prelimMatches = abs($perfectBracketNumber-$numberOfTeams);
            break;
        }
        $perfectBracketNumber = ($perfectBracketNumber * $teamsPerMatch ) / $winnersPerMatch;
    }

    shuffle($teams);

    $numberOfMatches = $perfectBracketNumber / $teamsPerMatch;
    $matchCount = 1;
    $prelimMatchNumber = 1;
    $roundOneMatches = [];
    while($prelimMatches !== 0) {
        for($i=1; $i <= 2; $i++){
            $matchTeam = array_pop($teams);
            $bracketRepo->createBracketEntry($tournament_game_id,
                0,
                $prelimMatchNumber,
                $matchTeam['id'],
                $matchCount,
                json_encode([]),
                $i
            );
        }

        $blankFirstRoundPosition = !isset($roundOneMatches[$matchCount])
            ? $blankFirstRoundPosition = 1
            : $blankFirstRoundPosition = $roundOneMatches[$matchCount];

        $fillMatch = ceil(($matchCount * $winnersPerMatch) / $teamsPerMatch);
        $bracketRepo->createBracketEntry(
            $tournament_game_id,
            1,
            $matchCount,
            null,
            $fillMatch,
            json_encode([]),
            $blankFirstRoundPosition
        );
        if (isset($roundOneMatches[$matchCount])) {
            $roundOneMatches[$matchCount]++;
        } else {
            $roundOneMatches[$matchCount] = 2;
        }

        $prelimMatches--;
        $prelimMatchNumber++;
        if($matchCount === $numberOfMatches){
            $matchCount = 1;
        } else {
            $matchCount++;
        }
    }

    //Round 1 Population with gaps



    //Round 1 population Remaining PLayers
    $matchCount = 1;
    while (isset($roundOneMatches[$matchCount])) {
        while ($roundOneMatches[$matchCount] <= $teamsPerMatch) {
            $fillMatch = ceil(($matchCount * $winnersPerMatch) / $teamsPerMatch);
            $matchTeam = array_pop($teams);
            $bracketRepo->createBracketEntry(
                $tournament_game_id,
                1,
                $matchCount,
                $matchTeam['id'],
                $fillMatch,
                json_encode([]),
                $roundOneMatches[$matchCount]
            );
            $roundOneMatches[$matchCount]++;
        }
        $matchCount++;
    }


    //Empty TBD rounds
    $numberOfMatchesInThisRound = floor(($winnersPerMatch * count($roundOneMatches)) /$teamsPerMatch);

    $round = 2;

    while ($numberOfMatchesInThisRound > 0) {
        for($match=1; $match<=$numberOfMatchesInThisRound; $match++) {
            for ($position=1; $position<=$teamsPerMatch; $position++) {
                $fillMatch = $numberOfMatchesInThisRound == 1
                    ? 0
                    : ceil(($match * $winnersPerMatch) / $teamsPerMatch);
                $bracketRepo->createBracketEntry(
                    $tournament_game_id,
                    $round,
                    $match,
                    null,
                    $fillMatch,
                    json_encode([]),
                    $position
                );
            }
        }
        $numberOfMatchesInThisRound = floor(($winnersPerMatch * $numberOfMatchesInThisRound) / $teamsPerMatch);
        $round++;
    }
    echo json_encode([
        "success" => true,
        "variables" => get_defined_vars()
    ]);
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ]);
}