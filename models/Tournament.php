<?php

require_once 'Model.php';

class Tournament extends Model
{
    protected $table = 'tournaments';
    public $id;
    public $name;
    public $date;
    public $logo;
    public $completed_date;
    public $creator_id;
    public $is_active;
    public $created_at;
    public $updated_at;

    public function __construct($id = null)
    {
        parent::__construct(); // Calls the parent constructor to set up the DB connection

        // If an ID is provided, load the tournament data
        if ($id !== null) {
            $this->getById($id);
        }
    }

    // Load tournament by ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $tournament = $result->fetch_assoc();
                $this->id = $tournament['id'];
                $this->name = $tournament['name'];
                $this->date = $tournament['date'];
                $this->logo = $tournament['logo'];
                $this->completed_date = $tournament['completed_date'];
                $this->creator_id = $tournament['creator_id'];
                $this->is_active = $tournament['is_active'];
            }
            $stmt->close();
        } else {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }
    }

    public function save()
    {
        $stmt = $this->db->prepare("INSERT INTO tournaments (name, date, logo, creator_id, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");

        if ($stmt) {
            $stmt->bind_param("sssi", $this->name, $this->date, $this->logo, $this->creator_id);
            return $stmt->execute();
        } else {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }
    }
}

