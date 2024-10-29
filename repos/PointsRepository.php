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
    
    public function addPointsToPlayer($tournamentGameId, $playerId){
        $stmt = $this->db->prepare("
            UPDATE points
            SET tournament_points = tournament_points + 1
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

    public function getLeaderboardDataByTournamentGameId($tournamentGameId) {
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
                t.team_number,
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
            $teamNumber = $teamRow['team_number'];
            $playerIds = json_decode($teamRow['players'], true);
    
            // Initialize the array for storing player names and team points
            $playerNames = [];
            $totalPoints = 0;
    
            foreach ($playerIds as $userId) {
                // Fetch player name
                $user = $this->getUserById($userId);
                $playerNames[] = $user['username'];
                $totalPoints += $userPoints[$userId] ?? 0;
            }
    
            // Add team data to leaderboard array
            $leaderboard[] = [
                'team_number' => $teamNumber,
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
    
    
    
    
}