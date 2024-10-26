<?php
require_once 'Model.php';

class Points extends Model {
    protected $table = 'points';
    protected $id;
    protected $tournament_game_id;
    protected $user_id;
    protected $points;
    protected $tournament_points;
    protected $created_at;
    protected $updated_at;

    // Constructor
    public function __construct($id = null) {
        parent::__construct();  // Initialize the DB connection
        if ($id) {
            $this->getById($id);
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getTournamentGameId() {
        return $this->tournament_game_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getPoints() {
        return $this->points;
    }

    public function getTournamentPoints() {
        return $this->tournament_points;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    // Setters
    public function setTournamentGameId($tournament_game_id) {
        $this->tournament_game_id = $tournament_game_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setPoints($points) {
        $this->points = $points;
    }

    public function setTournamentPoints($tournament_points) {
        $this->tournament_points = $tournament_points;
    }

    // Save function (Insert a new entry)
    public function save() {
        if (!$this->id) {  // Only insert if the object doesn't already have an ID
            $stmt = $this->db->prepare("INSERT INTO $this->table (tournament_game_id, user_id, points, tournament_points, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("iiii", $this->tournament_game_id, $this->user_id, $this->points, $this->tournament_points);

            if ($stmt->execute()) {
                $this->id = $this->db->insert_id;  // Set the ID to the newly created ID
                return $this->id;
            } else {
                throw new Exception("Error inserting points record: " . $stmt->error);
            }
        } else {
            return $this->update();  // If ID exists, update instead of inserting
        }
    }

    // Update function (Update an existing entry)
    public function update() {
        if ($this->id) {
            $stmt = $this->db->prepare("UPDATE $this->table SET tournament_game_id = ?, user_id = ?, points = ?, tournament_points = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("iiiii", $this->tournament_game_id, $this->user_id, $this->points, $this->tournament_points, $this->id);

            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Error updating points record: " . $stmt->error);
            }
        }
        return false;
    }

    // Retrieve by ID and populate object
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $record = $result->fetch_assoc();
            $this->id = $record['id'];
            $this->tournament_game_id = $record['tournament_game_id'];
            $this->user_id = $record['user_id'];
            $this->points = $record['points'];
            $this->tournament_points = $record['tournament_points'];
            $this->created_at = $record['created_at'];
            $this->updated_at = $record['updated_at'];
        }
        $stmt->close();
    }
}
