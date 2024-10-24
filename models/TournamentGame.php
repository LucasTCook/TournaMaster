<?php

require_once 'Model.php';  // Assuming Model.php is the base model

class TournamentGame extends Model
{
    protected $table = 'tournament_games';
    public $id;
    public $tournament_id;
    public $game_id;
    public $created_at;
    public $updated_at;

    // Constructor, optional parameter to load by ID
    public function __construct($id = null) {
        parent::__construct();
        if ($id) {
            $this->loadById($id);
        }
    }

    // Load a record by ID
    public function loadById($id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}
