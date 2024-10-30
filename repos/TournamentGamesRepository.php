<?php

require_once '../models/Model.php';
require_once '../models/User.php';
require_once '../models/TournamentGame.php';

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
        // Step 1: Retrieve game data with winner team numbers
        $gameQuery = "
            SELECT 
                tg.*, 
                g.name AS game_name, 
                g.image_url AS game_image_url, 
                g.release_year AS game_release_year, 
                g.platform AS game_platform,
                tg.winner_team_number,
                (
                    SELECT COUNT(*) 
                    FROM teams t
                    WHERE t.tournament_game_id = tg.id
                ) AS team_count
            FROM 
                $this->table AS tg
            JOIN 
                games g ON tg.game_id = g.id
            WHERE 
                tg.tournament_id = ?
        ";
    
        $stmt = $this->db->prepare($gameQuery);
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }
    
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Collect games with their winner team number for further processing
        $games = [];
        $winnerTeamNumbers = [];
        while ($game = $result->fetch_assoc()) {
            $game['winner_name'] = '';  // Initialize empty winner name
            $games[$game['id']] = $game; // Store game by its ID
            if (!empty($game['winner_team_number'])) {
                $winnerTeamNumbers[$game['id']] = $game['winner_team_number'];
            }
        }
        $stmt->close();
    
        // Step 2: Loop through each winner team number, fetch players JSON, and get usernames
        foreach ($winnerTeamNumbers as $gameId => $teamNumber) {
            $teamQuery = "
                SELECT t.players 
                FROM teams t
                WHERE t.tournament_game_id = ? AND t.id = ?
            ";
    
            $stmt = $this->db->prepare($teamQuery);
            if (!$stmt) {
                die('Prepare failed: ' . $this->db->error);
            }
    
            $stmt->bind_param("ii", $gameId, $teamNumber);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($teamRow = $result->fetch_assoc()) {
                $playerIds = json_decode($teamRow['players'], true);
                $usernames = [];
    
                // Fetch usernames for each player in the winning team using the User model
                foreach ($playerIds as $userId) {
                    $user = new User($userId);
                    $usernames[] = $user->getUsername();
                }
    
                // Concatenate usernames and update the winner_name field in games array
                $games[$gameId]['winner_name'] = implode(', ', $usernames);
            }
            $stmt->close();
        }
    
        return array_values($games); // Return games as an indexed array
    }
    
    public function updateWinnerTeamNumber($tournamentGameId, $teamNumber) {
        $stmt = $this->db->prepare("
            UPDATE tournament_games 
            SET winner_team_number = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("ii", $teamNumber, $tournamentGameId);
        $stmt->execute();
        $stmt->close();
    }

    public function getGamesByGameId($gameId) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE game_id = ?");
        $stmt->bind_param("i", $gameId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($gameId, $status) {
        $tournamentGame = new TournamentGame($gameId);
        $tournamentGame->setStatus($status);
        $tournamentGame->update();
    }
    
    function setTournamentGameWinningTeam($tournamentGameId, $teamId) {
        $tournamentGame = new TournamentGame($tournamentGameId);
        $tournamentGame->setWinnerTeamNumber($teamId);
        $tournamentGame->update();
    }

    public function getGameDetailsById($tournamentGameId) {
        $stmt = $this->db->prepare("
            SELECT 
                tg.id AS tournament_game_id,
                tg.tournament_id,
                tg.type,
                tg.status,
                tg.team_size,
                tg.teams_per_match,
                tg.winners_per_match,
                g.name AS game_name,
                g.image_url AS game_image_url,
                g.release_year AS game_release_year,
                g.platform AS game_platform
            FROM 
                $this->table tg
            JOIN 
                games g ON tg.game_id = g.id
            WHERE 
                tg.id = ?
        ");
        
        $stmt->bind_param("i", $tournamentGameId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    
        return null;
    }
    
}
