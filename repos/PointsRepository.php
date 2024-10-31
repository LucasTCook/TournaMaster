<?php
require_once '../models/Model.php';

class PointsRepository extends Model {
    protected $table = 'points';

    public function userHasPointsInTournament($userId, $tournamentId) {
        // Prepare statement with debug message if it fails
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM $this->table WHERE user_id = ? AND tournament_game_id IN 
                                    (SELECT id FROM tournament_games WHERE tournament_id = ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
        }
        
        $stmt->bind_param("ii", $userId, $tournamentId);
        
        // Check if execution was successful
        if (!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Check if $row['count'] is fetched correctly
        if ($row) {
            $stmt->close();
            return $row['count'] > 0;
        } else {
            // Output an error if fetch_assoc did not retrieve data
            die("Fetch failed: Could not retrieve row count.");
        }
    }

    public function getPointsByTournamentAndUser($tournamentGameId, $userId) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE tournament_game_id = ? AND user_id = ? AND points IS NULL AND tournament_points IS NULL");
        $stmt->bind_param("ii", $tournamentGameId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Returns the record if found, or null if not
    }
    
    public function addPointsToPlayer($tournamentGameId, $playerId, $additionalPoints = 0){
        $stmt = $this->db->prepare("
            UPDATE points
            SET tournament_points = tournament_points + 1 + $additionalPoints
            WHERE user_id = ? AND tournament_game_id = ?
        ");
        $stmt->bind_param("ii", $playerId, $tournamentGameId);
        
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating points: " . $stmt->error;
            return false;
        }
    }

    public function removePoints($tournamentGameId, $playerId){
        $stmt = $this->db->prepare("
            UPDATE points
            SET tournament_points = tournament_points - 1
            WHERE user_id = ? AND tournament_game_id = ?
        ");
        $stmt->bind_param("ii", $playerId, $tournamentGameId);
        
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating points: " . $stmt->error;
            return false;
        }
    }

    public function getLeaderboardPointsByTournamentGameId($tournamentGameId) {
        // Step 1: Fetch user points for the tournament game
        $stmt = $this->db->prepare("
            SELECT 
                p.user_id,
                COALESCE(p.points, 0) AS points
            FROM 
                points p
            WHERE 
                p.tournament_game_id = ?
        ");
        
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $userPointsResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        // Map points by user ID
        $userPoints = [];
        foreach ($userPointsResult as $row) {
            $userPoints[$row['user_id']] = $row['points'];
        }
    
        // Step 2: Fetch team data with players for the tournament game
        $stmt = $this->db->prepare("
            SELECT 
                t.id,
                t.players  -- players JSON field
            FROM 
                teams t
            WHERE 
                t.tournament_game_id = ?
        ");
    
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $teamDataResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        // Step 3: Map player names and points by team
        $leaderboard = [];
        foreach ($teamDataResult as $teamRow) {
            $teamId = $teamRow['id'];
            $playerIds = json_decode($teamRow['players'], true);
    
            // Initialize the array for storing player names and team points
            $playerNames = [];
            $totalPoints = 0;
            $pointsSet = false;  // Flag to ensure points are set only once per team
    
            foreach ($playerIds as $userId) {
                // Fetch player name
                $user = $this->getUserById($userId);
                $playerNames[] = $user['username'];
                
                // Set totalPoints only once per team
                if (!$pointsSet && isset($userPoints[$userId])) {
                    $totalPoints = $userPoints[$userId];
                    $pointsSet = true;  // Set flag to true after assigning points
                }
            }
    
            // Add team data to leaderboard array
            $leaderboard[] = [
                'team_id' => $teamId,
                'player_names' => $playerNames,  // Array of names instead of single concatenated string
                'points' => $totalPoints
            ];
        }
    
        // Sort leaderboard by points in descending order
        usort($leaderboard, function ($a, $b) {
            return $b['points'] - $a['points'];
        });
    
        return $leaderboard;
    }  
    
    public function getLeaderboardTournamentPointsByTournamentGameId($tournamentGameId) {
        // Step 1: Fetch user points for the tournament game
        $stmt = $this->db->prepare("
            SELECT 
                p.user_id,
                COALESCE(p.tournament_points, 0) AS points
            FROM 
                points p
            WHERE 
                p.tournament_game_id = ?
        ");
        
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $userPointsResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        // Map points by user ID
        $userPoints = [];
        foreach ($userPointsResult as $row) {
            $userPoints[$row['user_id']] = $row['points'];
        }
    
        // Step 2: Fetch team data with players for the tournament game
        $stmt = $this->db->prepare("
            SELECT 
                t.id,
                t.players  -- players JSON field
            FROM 
                teams t
            WHERE 
                t.tournament_game_id = ?
        ");
    
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $teamDataResult = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
        // Step 3: Map player names and points by team
        $leaderboard = [];
        foreach ($teamDataResult as $teamRow) {
            $teamId = $teamRow['id'];
            $playerIds = json_decode($teamRow['players'], true);
    
            // Initialize the array for storing player names and team points
            $playerNames = [];
            $totalPoints = 0;
            $pointsSet = false;  // Flag to ensure points are set only once per team
    
            foreach ($playerIds as $userId) {
                // Fetch player name
                $user = $this->getUserById($userId);
                $playerNames[] = $user['username'];
                
                // Set totalPoints only once per team
                if (!$pointsSet && isset($userPoints[$userId])) {
                    $totalPoints = $userPoints[$userId];
                    $pointsSet = true;  // Set flag to true after assigning points
                }
            }
    
            // Add team data to leaderboard array
            $leaderboard[] = [
                'team_id' => $teamId,
                'player_names' => $playerNames,  // Array of names instead of single concatenated string
                'points' => $totalPoints
            ];
        }
    
        // Sort leaderboard by points in descending order
        usort($leaderboard, function ($a, $b) {
            return $b['points'] - $a['points'];
        });
    
        return $leaderboard;
    }    
    
    // Helper function to get user by ID
    public function getUserById($userId) {
        $stmt = $this->db->prepare("SELECT id, username FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function savePointsForPlayer($userId, $points, $tournamentGameId){
        $stmt = $this->db->prepare("
            UPDATE points
            SET points = ?
            WHERE user_id = ? AND tournament_game_id = ?
        ");
        $stmt->bind_param("iii", $points, $userId, $tournamentGameId);
        
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error updating points: " . $stmt->error;
            return false;
        }
    }

    public function getTeamScoresByTournamentGameId($tournamentGameId) {
        $teamsRepo = new TeamsRepository();
    
        // Step 1: Fetch teams in the tournament game
        $teamQuery = "
            SELECT 
                t.id AS team_id
            FROM 
                teams t
            WHERE 
                t.tournament_game_id = ?
        ";
    
        $stmt = $this->db->prepare($teamQuery);
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $teamScores = [];
    
        // Step 2: Calculate team points by summing player points for each team
        while ($teamRow = $result->fetch_assoc()) {
            $teamId = $teamRow['team_id'];
            $players = $teamsRepo->getPlayersOnTeam($teamId);
    
            $totalPoints = 0;
            foreach ($players as $userId) {
                // Fetch points for each player on the team in the tournament
                $pointsQuery = "
                    SELECT 
                        points
                    FROM 
                        points
                    WHERE 
                        user_id = ? AND tournament_game_id = ?
                ";
    
                $playerStmt = $this->db->prepare($pointsQuery);
                $playerStmt->bind_param("ii", $userId, $tournamentGameId);
                $playerStmt->execute();
                $playerResult = $playerStmt->get_result()->fetch_assoc();
    
                // Accumulate the points for the team
                $totalPoints += $playerResult['points'] ?? 0;
                $playerStmt->close();
            }
    
            $teamScores[] = [
                'team_id' => $teamId,
                'points' => $totalPoints
            ];
        }
    
        $stmt->close();
    
        // Sort team scores in descending order by points
        usort($teamScores, function($a, $b) {
            return $b['points'] - $a['points'];
        });
    
        return $teamScores;
    }
    

    public function updateTournamentPointsForTeams($teamScores, $tournamentGameId) {
        $teamsRepo = new TeamsRepository();
    
        foreach ($teamScores as $team) {
            $teamId = $team['team_id'];
            $tournamentPoints = $team['tournament_points'];
    
            // Retrieve players on the team
            $players = $teamsRepo->getPlayersOnTeam($teamId);
    
            foreach ($players as $userId) {
                // Update tournament points for each player
                $stmt = $this->db->prepare("
                    UPDATE points 
                    SET tournament_points = ? 
                    WHERE user_id = ? AND tournament_game_id = ?
                ");
                $stmt->bind_param("iii", $tournamentPoints, $userId, $tournamentGameId);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    public function getUserPointsByTournamentGameId($userId, $tournamentGameId) {
        $stmt = $this->db->prepare("
            SELECT points
            FROM points
            WHERE user_id = ? AND tournament_game_id = ?
            ORDER BY points DESC
            LIMIT 1
        ");
        
        $stmt->bind_param("ii", $userId, $tournamentGameId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if there’s a record for this user in the tournament game
        if ($row = $result->fetch_assoc()) {
            return $row['points'];
        }
        
        // If no points record exists, return 0 or null based on preference
        return 0; // or return null;
    }
    
    public function getMaxPointsByTournamentGameId($tournamentGameId) {
        $stmt = $this->db->prepare("
            SELECT MAX(points) AS max_points
            FROM points
            WHERE tournament_game_id = ?
        ");
        
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if there’s a max points record for this tournament game
        if ($row = $result->fetch_assoc()) {
            return $row['max_points'];
        }
        
        // If no points record exists, return 0 or null based on preference
        return 0; // or return null;
    }    

    public function getAllUserPointsByTournamentId($tournamentId) {
        $query = "
            SELECT 
                u.id AS user_id,
                u.username,
                COALESCE(SUM(p.tournament_points), 0) AS total_points
            FROM 
                users u
            JOIN 
                tournament_users tu ON u.id = tu.user_id AND tu.tournament_id = ?
            LEFT JOIN 
                points p ON p.user_id = u.id
            WHERE 
                p.tournament_game_id IN (
                    SELECT id 
                    FROM tournament_games 
                    WHERE tournament_id = ?
                )
            GROUP BY 
                u.id
            ORDER BY 
                total_points DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $tournamentId, $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $players = [];
        while ($row = $result->fetch_assoc()) {
            $players[] = [
                'user_id' => $row['user_id'],
                'username' => $row['username'],
                'total_points' => (int)$row['total_points']
            ];
        }

        return $players;
    }

    public function getPlayersTournamentPointsByTournamentId($tournamentId) {
        // Query to retrieve points for each user in each game within the tournament
        $query = "
            SELECT 
                p.user_id,
                u.username,
                p.tournament_game_id AS game_id,
                COALESCE(p.tournament_points, 0) AS points
            FROM 
                points p
            JOIN 
                users u ON p.user_id = u.id
            JOIN 
                tournament_games tg ON p.tournament_game_id = tg.id
            WHERE 
                tg.tournament_id = ?
            ORDER BY 
                game_id, points DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $playersPoints = [];
        while ($row = $result->fetch_assoc()) {
            $playersPoints[] = [
                'user_id' => $row['user_id'],
                'username' => $row['username'],
                'game_id' => $row['game_id'],
                'points' => (int) $row['points']
            ];
        }

        $stmt->close();

        return $playersPoints;
    }
}