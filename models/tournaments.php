<?php

class Tournament {
    private $db;
    
    public $id;
    public $name;
    public $date;
    public $completed_at;
    public $creator_user_id;
    public $active;

    public function __construct($db) {
        $this->db = $db; // pass in the DB connection
    }

    // Create a new tournament
    public function create() {
        $query = "INSERT INTO tournaments (name, date, creator_user_id, active)
                  VALUES (?, ?, ?, 1)";  // 1 is the default active status

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssi', $this->name, $this->date, $this->creator_user_id);

        return $stmt->execute();
    }

    // Get a specific tournament by its ID
    public function getById($id) {
        $query = "SELECT * FROM tournaments WHERE tournament_id = ? AND active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update tournament information
    public function update() {
        $query = "UPDATE tournaments 
                  SET name = ?, date = ?, completed_at = ?, updated_at = NOW() 
                  WHERE tournament_id = ? AND active = 1";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssi', $this->name, $this->date, $this->completed_at, $this->id);

        return $stmt->execute();
    }

    // Soft delete a tournament by setting active to 0
    public function softDelete() {
        $query = "UPDATE tournaments 
                  SET active = 0 
                  WHERE tournament_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $this->id);

        return $stmt->execute();
    }

    // List all active tournaments
    public function getAllActive() {
        $query = "SELECT * FROM tournaments WHERE active = 1";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if the tournament can be deleted (no games started)
    public function canDelete() {
        $query = "SELECT COUNT(*) AS game_count FROM games WHERE tournament_id = ? AND started = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['game_count'] == 0;
    }
}
