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
    

    
}