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

    public function getTournamentsByUserAndType($userId, $type) {
        // Determine whether to fetch current or past tournaments based on today's date
        $dateCondition = ($type === 'current') ? ">= CURDATE()" : "< CURDATE()";
        
        // SQL query to fetch tournaments with the specified type
        $query = "
            SELECT 
                t.id,
                t.name AS tournament_name,
                u.username as tournament_creator,
                DATE_FORMAT(t.date, '%m/%d/%Y') AS date
            FROM 
                tournaments t
            JOIN 
                tournament_users tu ON t.id = tu.tournament_id
            JOIN 
                users u ON t.creator_id = u.id
            WHERE 
                tu.user_id = ? 
                AND t.date $dateCondition
            ORDER BY 
                t.date DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tournaments = [];
        while ($row = $result->fetch_assoc()) {
            $tournaments[] = $row;
        }

        return $tournaments;
    }
}
