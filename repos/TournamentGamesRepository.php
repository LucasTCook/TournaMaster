<?php

class TournamentGamesRepository extends Model {
    public function countByTournamentId($tournamentId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS games_count FROM tournament_games WHERE tournament_id = ?");
        
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
                tournament_games tg
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
    
    
    
}
