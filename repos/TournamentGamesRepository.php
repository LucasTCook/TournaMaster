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
}
