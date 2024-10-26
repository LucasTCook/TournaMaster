<?php

class TournamentGamesRepository extends Model {

    protected $table = 'tournament_games';

    public function countByTournamentId($tournamentId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS games_count FROM $this->table WHERE tournament_id = ?");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }
        
        $stmt->bind_param('i', $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['games_count'];
        }
        
        return 0;
    }

    public function getGamesByTournamentId($tournamentId) {
        // Your SQL query
        $query = "
            SELECT 
                tg.*, 
                g.name AS game_name, 
                g.image_url AS game_image_url, 
                g.release_year AS game_release_year, 
                g.platform AS game_platform 
            FROM 
                $this->table as tg
            JOIN 
                games g ON tg.game_id = g.id
            WHERE 
                tg.tournament_id = ?
        ";
    
        $stmt = $this->db->prepare($query);
    
        // Check if prepare failed
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error); // Outputs the SQL error for debugging
        }
    
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $games = [];
        while ($game = $result->fetch_assoc()) {
            $games[] = $game;
        }
        
        $stmt->close();
        return $games;
    }

    public function getGamesByGameId($gameId) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE game_id = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
}
