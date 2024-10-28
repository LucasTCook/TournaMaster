<?php 
require_once '../models/Model.php';
require_once '../models/TournamentGame.php';
class BracketRepository extends Model {

    private $table = 'brackets';

    public function createBracketEntry($tournament_game_id, $round, $match_number, $team_id, $fill_match, $fill_position, $position)
    {
        $result = 'TBD';
        $stmt = $this->db->prepare("INSERT INTO $this->table (tournament_game_id, round, match_number, team_id, fill_match, fill_position, position, result) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiiisis", $tournament_game_id, $round, $match_number, $team_id, $fill_match, $fill_position, $position, $result);
        $stmt->execute();
        $stmt->close();
    }

    function getBracketByGameId($tournament_game_id)
    {
        // Step 1: Get all bracket entries and join teams, including rows with NULL team_id
        $bracketQuery = "
            SELECT 
                b.id AS bracket_id, 
                b.round, 
                b.match_number, 
                b.team_id, 
                b.fill_match, 
                b.fill_position, 
                b.position, 
                b.result,
                t.team_number,
                t.players -- This is the JSON field containing user IDs
            FROM 
                brackets b
            LEFT JOIN 
                teams t ON b.team_id = t.id
            WHERE 
                b.tournament_game_id = ?
        ";
        
        $stmt = $this->db->prepare($bracketQuery);
        $stmt->bind_param("i", $tournament_game_id);
        $stmt->execute();
        $bracketResult = $stmt->get_result();
        
        // Store bracket and team info, and collect all unique user IDs from the players JSON
        $bracketData = [];
        $userIds = [];
        
        while ($row = $bracketResult->fetch_assoc()) {
            // Check if players JSON exists and decode, otherwise set to "TBD"
            if (isset($row['players']) && $row['players'] !== null) {
                $players = json_decode($row['players'], true);
                $row['players'] = is_array($players) ? $players : [];
                $userIds = array_merge($userIds, $row['players']);
            } else {
                // If team_id is NULL, set players and players_info to "TBD"
                $row['players'] = ["TBD"];
            }

            // Add empty players_info array for player details if players are set
            $row['players_info'] = $row['players'] === ["TBD"] ? ["TBD"] : [];

            // Add the row to the bracket data array
            $bracketData[] = $row;
        }
        $stmt->close();
        
        // Step 2: If there are user IDs, get user information
        $userMap = [];
        if (!empty($userIds)) {
            $userIds = array_unique($userIds); // Remove duplicates
            $userPlaceholders = implode(",", array_fill(0, count($userIds), '?'));
            $userQuery = "SELECT id, username FROM users WHERE id IN ($userPlaceholders)";
            
            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
            $stmt->execute();
            $userResult = $stmt->get_result();
            
            // Map users by their ID for quick access
            while ($user = $userResult->fetch_assoc()) {
                $userMap[$user['id']] = $user;
            }
            $stmt->close();
        }
        
        // Step 3: Map user info back to bracket data or set as "TBD" if missing
        foreach ($bracketData as &$entry) {
            if ($entry['players'] !== ["TBD"]) {
                foreach ($entry['players'] as $userId) {
                    if (isset($userMap[$userId])) {
                        $entry['players_info'][] = $userMap[$userId];
                    }
                }
                // Set "TBD" in players_info if no players were found in the userMap
                if (empty($entry['players_info'])) {
                    $entry['players_info'][] = ["id" => "TBD", "username" => "TBD"];
                }
            }
        }
        
        return [
            "success" => true,
            "data" => $bracketData
        ];
    }

    public function getPlayersInMatch($tournamentGameId, $round, $matchNumber) {
        // Step 1: Get all teams in the match along with their player IDs (as JSON) and team_number
        $stmt = $this->db->prepare("
            SELECT 
                b.team_id,
                b.position,
                t.team_number,
                t.players AS player_ids_json
            FROM 
                $this->table b
            LEFT JOIN 
                teams t ON b.team_id = t.id
            WHERE 
                b.tournament_game_id = ? 
                AND b.round = ? 
                AND b.match_number = ?
            ORDER BY 
                b.team_id, b.position
        ");
        
        $stmt->bind_param("iii", $tournamentGameId, $round, $matchNumber);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        // Step 2: Initialize teams array and collect user IDs
        $teams = [];
        $userIds = [];
    
        foreach ($result as $row) {
            $teamId = $row['team_id'];
            $position = $row['position'];
            $teamNumber = $row['team_number'];
            $playerIds = json_decode($row['player_ids_json'], true); // Decode JSON array of user IDs
    
            if (!isset($teams[$teamId])) {
                $teams[$teamId] = [
                    'team_id' => $teamId,
                    'players' => []
                ];
            }
    
            // Collect all unique player IDs to fetch user information in one query
            if (is_array($playerIds)) {
                $userIds = array_merge($userIds, $playerIds);
            }
    
            // Temporarily store player IDs to replace with actual user info after the user query
            $teams[$teamId]['players'][$position] = $playerIds;
        }
    
        // Step 3: Fetch user details from `users` table
        if (!empty($userIds)) {
            $userIds = array_unique($userIds);
            $placeholders = implode(",", array_fill(0, count($userIds), '?'));
            
            $userQuery = "SELECT id, username FROM users WHERE id IN ($placeholders)";
            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
            $stmt->execute();
            
            $userResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $userMap = [];
            foreach ($userResult as $user) {
                $userMap[$user['id']] = $user['username'];
            }
        }
    
        // Step 4: Replace player IDs in teams array with actual user data
        foreach ($teams as &$team) {
            foreach ($team['players'] as $position => $playerIds) {
                $team['players'][$position] = array_map(function($userId) use ($userMap) {
                    return [
                        'user_id' => $userId,
                        'player_name' => $userMap[$userId] ?? 'Unknown'
                    ];
                }, $playerIds);
            }
        }
    
        return $teams;  // Return teams with grouped players by position
    }
    
    public function removePlayerResult($tournamentGameId, $round, $matchNumber, $teamId) {
        $fillMatch = $this->getFillMatch($round, $matchNumber, $teamId);
        $nextRound = $round + 1;
        // Prepare the DELETE statement
        $stmt = $this->db->prepare("
            UPDATE $this->table
            SET team_id = NULL
            WHERE tournament_game_id = ? 
            AND round = ? 
            AND match_number = ? 
            AND team_id = ?
        ");

        // Bind the parameters
        $stmt->bind_param("iiii", $tournamentGameId, $nextRound, $fillMatch, $teamId);

        // Execute the delete
        $stmt->execute();

        // Check if any rows were deleted
        if ($stmt->affected_rows > 0) {
            echo "Player result successfully deleted for team ID {$teamId} in round {$nextRound}, match number {$fillMatch}.";
        } else {
            echo "No matching player result found to delete.";
        }

        // Close the statement
        $stmt->close();
    }

    public function updatePlayerResult($tournamentGameId, $round, $matchNumber, $teamId, $result) {
        // echo json_encode([$tournamentGameId, $round, $matchNumber, $teamId, $result]);
        $stmt = $this->db->prepare("UPDATE $this->table SET result = ? WHERE tournament_game_id = ? AND round = ? AND match_number = ? AND team_id = ?");
        $stmt->bind_param("siiii", $result, $tournamentGameId, $round, $matchNumber, $teamId);
        $stmt->execute();

        if($result === 'WIN') {
            $fillMatch = $this->getFillMatch($round, $matchNumber, $teamId);
            $this->updateBracketWithTeam($fillMatch, $round, $teamId);
        }

    }

    private function getFillMatch($round, $matchNumber, $teamId) {
        $stmt = $this->db->prepare("
            SELECT fill_match 
            FROM brackets
            WHERE round = ? AND match_number = ? AND team_id = ?
        ");
        
        $stmt->bind_param("iii", $round, $matchNumber, $teamId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result ? $result['fill_match'] : null;
    }

    public function updateBracketWithTeam($fillMatch, $round, $teamId) {
        // Increment the round for the next match
        $nextRound = $round + 1;
    
        // Step 1: Check if the team_id is already in the round
        $checkStmt = $this->db->prepare("
            SELECT COUNT(*) as team_exists 
            FROM brackets 
            WHERE round = ? AND team_id = ?
        ");
        $checkStmt->bind_param("ii", $nextRound, $teamId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();
    
        // If the team is already in the round, skip the update
        if ($checkResult['team_exists'] > 0) {
            echo "Team ID {$teamId} is already populated in round {$nextRound}.";
            return;
        }
    
        // Step 2: Proceed to update only if the team is not present in the round
        $stmt = $this->db->prepare("
            UPDATE brackets
            SET team_id = ?
            WHERE match_number = ? 
              AND round = ? 
              AND team_id IS NULL
            ORDER BY position ASC
            LIMIT 1
        ");
        
        // Bind the parameters
        $stmt->bind_param("iii", $teamId, $fillMatch, $nextRound);
    
        // Execute the update
        $stmt->execute();
    
        // Check if any rows were updated
        if ($stmt->affected_rows > 0) {
            echo "Bracket updated successfully with team ID {$teamId} in round {$nextRound}.";
        } else {
            echo "No available bracket row found for update.";
        }
    
        $stmt->close();
    }
}