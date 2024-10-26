<?php 

class BracketRepository extends Model {

    private $table = 'bracket';

    public function insertBracketEntry($tournamentGameId, $round, $match, $team, $userId = null) {
        // Insert a new entry in the tournament_brackets table
        $stmt = $this->db->prepare("
            INSERT INTO $this->table (tournament_game_id, round, match_number, team_number, user_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        // Bind the parameters
        $stmt->bind_param("iiiii", $tournamentGameId, $round, $match, $team, $userId);
    
        // Execute and check for success
        if ($stmt->execute()) {
            return $this->db->insert_id;  // Return the ID of the newly inserted record
        } else {
            throw new Exception("Error inserting bracket entry: " . $stmt->error);
        }
    }
    
}