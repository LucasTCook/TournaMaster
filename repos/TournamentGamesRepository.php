<?php

require_once '../models/Model.php';
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
        // First query to get tournament games data
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
    
        // Collect all games and winner team numbers
        $games = [];
        $winnerTeamNumbers = [];
        while ($game = $result->fetch_assoc()) {
            $game['winner_usernames'] = '';  // Initialize empty string
            $games[] = $game;  // Store each game directly as an indexed array element
            if (!empty($game['winner_team_number'])) {
                $winnerTeamNumbers[$game['id']] = $game['winner_team_number'];
            }
        }
        $stmt->close();
    
        // If no winner teams exist, return the games as is
        if (empty($winnerTeamNumbers)) {
            return $games;
        }
    
        // Second query to get usernames for the winning teams only
        $placeholders = implode(',', array_fill(0, count($winnerTeamNumbers), '?'));
        $teamQuery = "
            SELECT 
                t.tournament_game_id, 
                u.username 
            FROM 
                teams t
            JOIN 
                users u ON JSON_CONTAINS(t.players, CAST(u.id AS CHAR)) -- Assuming 'players' is JSON
            WHERE 
                t.tournament_game_id IN ($placeholders) 
                AND t.team_number = ?
        ";
    
        $stmt = $this->db->prepare($teamQuery);
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }
    
        // Bind parameters dynamically for each game and team
        $bindParams = [];
        foreach ($winnerTeamNumbers as $gameId => $teamNumber) {
            $bindParams[] = $gameId;
            $bindParams[] = $teamNumber;
        }
        $stmt->bind_param(str_repeat('ii', count($winnerTeamNumbers)), ...$bindParams);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Map usernames to the winning team for each tournament game
        $usernamesByGame = [];
        while ($row = $result->fetch_assoc()) {
            $tournamentGameId = $row['tournament_game_id'];
            $usernamesByGame[$tournamentGameId][] = $row['username'];
        }
        $stmt->close();
    
        // Integrate usernames back into the games array
        foreach ($games as &$game) {
            if (isset($usernamesByGame[$game['id']])) {
                $game['winner_name'] = implode(', ', $usernamesByGame[$game['id']]);
            }
        }
    
        return $games;
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
}
