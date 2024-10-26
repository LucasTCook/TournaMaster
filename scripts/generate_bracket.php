<?php
// Include necessary repositories and configuration
require_once '../config.php';
require_once '../models/TournamentGame.php';
require_once '../repos/TournamentUsersRepository.php';
require_once '../repos/BracketRepository.php';

header('Content-Type: application/json');

// Get POST data
$tournamentGameId = $_POST['id'] ?? null;

if (!$tournamentGameId) {
    echo json_encode(['error' => 'Invalid tournament or game ID']);
    exit;
}

try {
    // Fetch the tournament game settings
    $tournamentGame = new TournamentGame($tournamentGameId);

    if (!$tournamentGame) {
        echo json_encode(['error' => 'Tournament game not found']);
        exit;
    }

    // Retrieve settings
    $teamSize = $tournamentGame->getTeamSize();
    $teamsPerMatch = $tournamentGame->getTeamsPerMatch();
    $winnersPerMatch = $tournamentGame->getWinnersPerMatch();

    // Fetch active players in this tournament
    $tournamentUsersRepo = new TournamentUsersRepository();
    $activePlayers = $tournamentUsersRepo->getActivePlayersByTournamentId($tournamentGame->getTournamentId());

    // Shuffle players for random team assignment
    shuffle($activePlayers);

    // Initialize Bracket Repository
    $numPlayers = count($activePlayers);
    $numTeams = ceil($numPlayers / $teamSize); // Initial number of teams based on team size
    $round = 1;
    $matchIndex = 1;

    $bracketRepo = new BracketRepository();

    // First Round: Populate with actual player teams
    for ($i = 0; $i < $numTeams; $i += $teamsPerMatch) {
        $teamsInMatch = array_slice($activePlayers, $i * $teamSize, $teamSize * $teamsPerMatch);

        // Fill any empty spots with NPC players if there are fewer than required
        while (count($teamsInMatch) < $teamSize * $teamsPerMatch) {
            $teamsInMatch[] = ['id' => null];  // Use 'null' for NPC entries to avoid foreign key issues
        }

        // Insert each team in this match
        $teamNum = 1; // Reset team number for each match
        for ($j = 0; $j < count($teamsInMatch); $j += $teamSize) {
            $playersInTeam = array_slice($teamsInMatch, $j, $teamSize);

            // Insert each player in the current team
            foreach ($playersInTeam as $player) {
                $userId = $player['id'] ?? null;  // Set user ID to null if it's an NPC
                $bracketRepo->insertBracketEntry($tournamentGameId, $round, $matchIndex, $teamNum, $userId);
            }
            $teamNum++; // Move to the next team within this match
        }
        $matchIndex++;
    }

    // Populate subsequent rounds with placeholders until the number of teams reaches the final configuration
    while ($numTeams > $teamsPerMatch) {  
        $round++;
        $matchIndex = 1;  // Reset match index for each new round

        // Calculate advancing teams based on winners per match
        $numTeams = ceil(($numTeams * $winnersPerMatch) / $teamsPerMatch);

        // Insert placeholders for each match in the round with "TBD" teams
        for ($i = 0; $i < $numTeams; $i += $teamsPerMatch) {
            $teamNum = 1;
            for ($j = 0; $j < $teamsPerMatch; $j++) {
                for ($k = 0; $k < $teamSize; $k++) {
                    $bracketRepo->insertBracketEntry($tournamentGameId, $round, $matchIndex, $teamNum, null);  // null user_id for TBD
                }
                $teamNum++;
            }
            $matchIndex++;
        }
    }



    echo json_encode(['success' => 'Bracket generated successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error generating bracket: ' . $e->getMessage()]);
}
