<?php
require_once '../models/Model.php';
require_once '../models/User.php';
require_once 'TournamentGamesRepository.php';

class TournamentRepository extends Model {
    public function getTournamentsByCreatorId($creatorId) {
        $stmt = $this->db->prepare("SELECT id, name, date, creator_id,logo FROM tournaments WHERE creator_id = ? ORDER BY date DESC");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }
        
        $stmt->bind_param('i', $creatorId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

         // Add extra data (creator name, games count)
         foreach ($result as &$tournament) {
            $user = new User($tournament['creator_id']);
            $gameRepo = new TournamentGamesRepository();
            $tournament['creator_name'] = $user->username; 
            $tournament['games_count'] = $gameRepo->countByTournamentId($tournament['id']);
        }
        
        return $result;
    }
}
