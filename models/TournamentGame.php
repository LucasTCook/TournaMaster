<?php

require_once 'Model.php';  // Assuming Model.php is the base model

class TournamentGame extends Model
{
    protected $table = 'tournament_games';
    public $id;
    public $tournament_id;
    public $game_id;
    protected $type;
    protected $team_size;
    protected $teams_per_match;
    protected $winners_per_match;
    protected $status;
    protected $winner_team_number;
    public $created_at;
    public $updated_at;

    // Constructor, optional parameter to load by ID
    public function __construct($id = null) {
        parent::__construct();
        if ($id) {
            $this->getById($id);
        }
    }

    // Load a record by ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
    
            // Populate the object properties
            $this->id = $data['id'];
            $this->tournament_id = $data['tournament_id'];
            $this->game_id = $data['game_id'];
            $this->type = $data['type'];
            $this->team_size = $data['team_size'];
            $this->teams_per_match = $data['teams_per_match'];
            $this->winners_per_match = $data['winners_per_match'];
            $this->status = $data['status'];
            $this->created_at = $data['created_at'];
            $this->updated_at = $data['updated_at'];
            $this->winner_team_number = $data['winner_team_number'];
    
            return $this; // Return the populated object
        } else {
            return null;
        }
    }
    

    // ID
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // Tournament ID
    public function getTournamentId() {
        return $this->tournament_id;
    }

    public function setTournamentId($tournament_id) {
        $this->tournament_id = $tournament_id;
    }

    // Game ID
    public function getGameId() {
        return $this->game_id;
    }

    public function setGameId($game_id) {
        $this->game_id = $game_id;
    }

    // Type
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    // Team Size
    public function getTeamSize() {
        return $this->team_size;
    }

    public function setTeamSize($team_size) {
        $this->team_size = $team_size;
    }

    // Teams per Match
    public function getTeamsPerMatch() {
        return $this->teams_per_match;
    }

    public function setTeamsPerMatch($teams_per_match) {
        $this->teams_per_match = $teams_per_match;
    }

    // Winners per Match
    public function getWinnersPerMatch() {
        return $this->winners_per_match;
    }

    public function setWinnersPerMatch($winners_per_match) {
        $this->winners_per_match = $winners_per_match;
    }

    // Status
    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getWinnerTeamNumber() {
        return $this->winner_team_number;
    }

    public function setWinnerTeamNumber($winner_team_number) {
        $this->winner_team_number = $winner_team_number;
    }

    // Created At
    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // Updated At
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
    }

    public function save() {
        // Ensure required fields are set
        if ($this->tournament_id && $this->game_id) {
            // Prepare the insert statement for only tournament_id and game_id
            $stmt = $this->db->prepare("INSERT INTO $this->table (tournament_id, game_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            
            // Bind parameters for tournament_id and game_id
            $stmt->bind_param("ii", $this->tournament_id, $this->game_id);
    
            // Execute the statement and return the inserted ID on success
            if ($stmt->execute()) {
                return $this->db->insert_id;
            } else {
                // Handle error or log it
                error_log("Error in TournamentGame::save - " . $stmt->error);
            }
        }
        
        // Return false if save operation failed
        return false;
    }

    public function update() {
        if ($this->id) {
            $stmt = $this->db->prepare("UPDATE $this->table SET tournament_id = ?, game_id = ?, type = ?, team_size = ?, teams_per_match = ?, winners_per_match = ?, winner_team_number = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("iisiiiiis", $this->tournament_id, $this->game_id, $this->type, $this->team_size, $this->teams_per_match, $this->winners_per_match, $this->winner_team_number, $this->status, $this->id);

            // Execute and return success status
            return $stmt->execute();
        }
        return false;
    }

    public function delete() {
        $stmt = $this->db->prepare("DELETE FROM tournament_games WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        return $stmt->execute();
    }
    
}
