<?php 
require_once '../models/Model.php';
require_once '../models/Team.php';

class TeamsRepository extends Model {
    protected $table = 'teams';
    function getTeamCount( $tournament_game_id) {
        $count = 0;
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM teams WHERE tournament_game_id = ?");
        $stmt->bind_param("i", $tournament_game_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    public function getTeams($tournament_game_id)
    {
        $stmt = $this->db->prepare("SELECT id, team_number, players FROM teams WHERE tournament_game_id = ?");
        $stmt->bind_param("i", $tournament_game_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = [
                'id' => $row['id'],
                'team_number' => $row['team_number'],
                'players' => json_decode($row['players'], true) // Assuming players are stored as JSON
            ];
        }

        $stmt->close();
        return $teams;
    }

    function getPlayersOnTeam($team_id) {
        $team = new Team($team_id);
        return $team->getPlayers();
    }
}