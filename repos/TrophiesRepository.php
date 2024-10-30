<?php
require '../models/Model.php';
require '../repos/TeamsRepository.php';
require '../repos/PointsRepository.php';

class TrophiesRepository extends Model {

    public function getPlayerTrophies($userId) {
        $tournamentGamesQuery = "
        SELECT 
            tg.id AS tournament_game_id,
            tg.game_id,
            tg.winner_team_number,
            g.image_url AS game_image_url,
            g.name as game_name,
            t.name AS tournament_name,
            DATE_FORMAT(t.date, '%b %d, %Y') AS tournament_date
        FROM 
            tournament_games tg
        JOIN 
            games g ON tg.game_id = g.id
        JOIN 
            tournaments t ON tg.tournament_id = t.id
        WHERE 
            tg.winner_team_number != 0
            OR (
                tg.winner_team_number = 0
                AND EXISTS (
                    SELECT 1 FROM points p 
                    WHERE p.tournament_game_id = tg.id 
                    AND p.user_id = ? 
                    AND p.points = (
                        SELECT MAX(points) FROM points 
                        WHERE tournament_game_id = tg.id
                    )
                )
            )
        ";
        
        $stmt = $this->db->prepare($tournamentGamesQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $teamRepo = new TeamsRepository();
        $pointsRepo = new PointsRepository();

        while ($row = $result->fetch_assoc()) {
            $isWinner = false;

            if ($row['winner_team_number'] != 0) {
                // Check if the user is on the winning team
                $playersOnTeam = $teamRepo->getPlayersOnTeam($row['winner_team_number']);
                $isWinner = in_array($userId, $playersOnTeam);
            } else {
                // Check if the user is tied for the most points
                $userPoints = $pointsRepo->getUserPointsByTournamentGameId($userId, $row['tournament_game_id']);
                $maxPoints = $pointsRepo->getMaxPointsByTournamentGameId($row['tournament_game_id']);
                $isWinner = ($userPoints == $maxPoints);
            }

            if ($isWinner) {
                $response['trophies'][] = [
                    'game_name' => $row['game_name'],
                    'game_image_url' => $row['game_image_url'],
                    'tournament_name' => $row['tournament_name'],
                    'tournament_date' => $row['tournament_date']
                ];
            }
        }

        $stmt->close();
        $response['success'] = true;
        return $response;
    }
    
}