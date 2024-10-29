<?php

require '../models/TournamentGame.php';
require '../models/Team.php';
require '../repos/TeamsRepository.php';
require '../repos/PointsRepository.php';
require '../repos/TournamentGamesRepository.php';

const ADDITIONAL_POINTS = 0;

$tournamentGameId = $_POST['tournament_game_id'];
$tournamentGame = new TournamentGame($tournamentGameId);
$tournamentGamesRepo = new TournamentGamesRepository();
$pointsRepo = new PointsRepository();

if($tournamentGame->getType() == 'bracket') {
    $teamRepo = new TeamsRepository();
    $winningPlayers = $teamRepo->getPlayersOnTeam($tournamentGame->getWinnerTeamNumber());
    foreach($winningPlayers as $player) {
        $pointsRepo->addPointsToPlayer($tournamentGameId, $player, ADDITIONAL_POINTS);
    }
    echo json_encode(value: ['success' => 'Points added to winner of bracket']);
}

if ($tournamentGame->getType() == 'points') {
    // Step 1: Fetch team scores for the tournament game
    $teamScores = $pointsRepo->getTeamScoresByTournamentGameId($tournamentGameId);

    // Step 2: Define placement points
    $placementPoints = [5, 3, 3, 2, 2, 2, 1, 1, 1, 1];
    
    $currentPlacement = 1;
    $currentPoints = $placementPoints[0];
    $prevScore = null;
    $tieCount = 0;

    // Step 3: Loop through sorted teams to assign tournament points
    foreach ($teamScores as $index => &$team) {
        // Check if the team’s score ties with the previous team's score
        if ($prevScore !== null && $team['points'] == $prevScore) {
            $team['tournament_points'] = $currentPoints;
            $tieCount++;
        } else {
            // Adjust current points if the team is not in the first position and there were ties
            if ($index > 0) {
                $currentPlacement += $tieCount + 1;
                if (isset($placementPoints[$currentPlacement - 1])) {
                    $currentPoints = $placementPoints[$currentPlacement - 1];
                } else {
                    // If there are no more defined placement points, give 0 points
                    $currentPoints = 0;
                }
            }

            // Assign points for this team
            $team['tournament_points'] = $currentPoints;
            $tieCount = 0;
        }

        // Update previous score
        $prevScore = $team['points'];
    }

    // Step 4: Update each player's tournament points based on their team's ranking
    $pointsRepo->updateTournamentPointsForTeams($teamScores, $tournamentGameId);  
    
    $highestScore = $teamScores[0]['points'];  // Assumes teamScores is sorted in descending order
    $winningTeams = array_filter($teamScores, function($team) use ($highestScore) {
        return $team['points'] == $highestScore;
    });

    if (count($winningTeams) > 1) {
        // Multiple teams tied for the highest score, set to 0
        $winningTeamNumber = 0;
    } else {
        // Only one team with the highest score
        $winningTeamNumber = reset($winningTeams)['team_id'];
    }

    // Step 6: Update the winner_team_number on tournament_games table
    $tournamentGamesRepo->updateWinnerTeamNumber($tournamentGameId, $winningTeamNumber);
    echo json_encode(['success' => 'Points added to players of points game']);
}


