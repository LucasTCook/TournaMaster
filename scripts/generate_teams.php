<?php

require_once '../config.php';
require_once '../models/Team.php';
require_once '../repos/TournamentUsersRepository.php';

$tournamentId = $_POST['tournament_id'] ?? null;
$tournamentGameId = $_POST['tournament_game_id'] ?? null;
$playersPerTeam = $_POST['players_per_team'] ?? 2;

if (!$tournamentId || !$playersPerTeam || !$tournamentGameId) {
    echo json_encode(['error' => 'Invalid tournament ID or players per team configuration']);
    exit;
}

try {
    // Initialize repository and get active players
    $tournamentUsersRepo = new TournamentUsersRepository();
    $activePlayers = $tournamentUsersRepo->getActivePlayersByTournamentId($tournamentId);
    shuffle($activePlayers);

    $teams = [];
    $teamNumber = 0;

    // Group players into teams
    foreach (array_chunk($activePlayers, $playersPerTeam) as $playerGroup) {
        $team = new Team();
        $team->setTournamentGameId($tournamentGameId);
        $team->setTeamNumber($teamNumber++);
        $team->setPlayers(array_column($playerGroup, 'id'));

        if (!$team->save()) {
            throw new Exception("Failed to save team {$teamNumber}");
        }
        $teams[] = $team;
    }

    echo json_encode(['success' => 'Teams generated successfully', 'teams' => $teams]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
