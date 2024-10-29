<?php
require '../config.php';
require '../repos/TeamsRepository.php';
require '../models/User.php';

$teamId = $_GET['teamId'] ?? null;
$response = ['success' => false, 'players' => []];

if ($teamId) {
    try {
        $teamsRepository = new TeamsRepository();

        // Get player IDs from the team repository
        $playerIds = $teamsRepository->getPlayersOnTeam($teamId);

        // Retrieve player details based on decoded IDs
        if ($playerIds && is_array($playerIds)) {
            $players = [];
            foreach ($playerIds as $userId) {
                $user = new User($userId);  // Fetch user details
                if ($user) {
                    $players[] = [
                        'id' => $user->getId(),
                        'username' => $user->getUsername()
                    ]; 
                }
            }

            $response['players'] = $players;
            $response['success'] = true;
        } else {
            $response['error'] = 'No players found on this team';
        }
    } catch (Exception $e) {
        $response['error'] = 'Failed to fetch players: ' . $e->getMessage();
    }
} else {
    $response['error'] = 'Invalid team ID';
}

echo json_encode($response);
