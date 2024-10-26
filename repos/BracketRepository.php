<?php 
require_once '../models/Model.php';
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

    public function getBracketByGameId($gameId) {
        $stmt = $this->db->prepare("
            SELECT 
                b.round, 
                b.match_number, 
                b.team_number, 
                b.user_id, 
                u.username AS player_name, 
                COALESCE(b.result, 'TBD') AS result
            FROM 
                bracket b
            LEFT JOIN 
                users u ON b.user_id = u.id
            WHERE 
                b.tournament_game_id = ?
            ORDER BY 
                b.round, b.match_number, b.team_number
        ");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $bracketData = [];
        while ($row = $result->fetch_assoc()) {
            $round = $row['round'];
            $match = $row['match_number'];
            $team = $row['team_number'];
            
            // Organize data by rounds and matches
            $bracketData['rounds'][$round]['matches'][$match]['teams'][$team]['players'][] = [
                'user_id' => $row['user_id'],
                'name' => $row['player_name'] ?? '--',
                'result' => $row['result']
            ];
        }
    
        return $bracketData;
    }
    
    
}