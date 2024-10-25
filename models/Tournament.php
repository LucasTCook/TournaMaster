<?php

require_once 'Model.php';

class Tournament extends Model
{
    protected $table = 'tournaments';
    public $id;
    public $name;
    public $date;
    public $logo;
    public $completed_at;
    public $creator_id;
    public $active;
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
                $this->completed_at = $tournament['completed_at'];
                $this->creator_id = $tournament['creator_id'];
                $this->active = $tournament['active'];
                $this->created_at = $tournament['created_at'];
                $this->updated_at = $tournament['updated_at'];
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

    public function update() {
        // Prepare the SQL statement
        $query = "
            UPDATE {$this->table} 
            SET 
                name = ?, 
                date = ?, 
                logo = ?, 
                completed_at = ?, 
                creator_id = ?, 
                active = ?, 
                created_at = ?, 
                updated_at = NOW()
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($query);

        if ($stmt) {
            // Bind the parameters to the prepared statement
            $stmt->bind_param(
                "sssssssi", 
                $this->name, 
                $this->date, 
                $this->logo, 
                $this->completed_at, 
                $this->creator_id, 
                $this->active, 
                $this->created_at, 
                $this->id
            );

            // Execute the statement and check for success
            if ($stmt->execute()) {
                return $stmt->affected_rows > 0; // Return true if a row was updated
            } else {
                throw new Exception('Failed to execute statement: ' . $stmt->error);
            }
        } else {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }
    }
}

