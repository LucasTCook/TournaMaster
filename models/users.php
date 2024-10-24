<?php

class User {
    private $db;
    
    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $first_name;
    public $last_name;
    public $profile_image_url;
    public $bio;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->db = $db;  // Database connection
    }

    // Create a new user
    public function create() {
        $query = "INSERT INTO users (username, email, password_hash, first_name, last_name, profile_image_url, bio, role) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssssssss', $this->username, $this->email, $this->password_hash, $this->first_name, $this->last_name, $this->profile_image_url, $this->bio, $this->role);
        return $stmt->execute();
    }

    // Get a user by ID
    public function getById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update user information
    public function update() {
        $query = "UPDATE users 
                  SET username = ?, email = ?, first_name = ?, last_name = ?, profile_image_url = ?, bio = ?, role = ?, updated_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('sssssssi', $this->username, $this->email, $this->first_name, $this->last_name, $this->profile_image_url, $this->bio, $this->role, $this->id);
        return $stmt->execute();
    }

    // Delete a user (soft delete option could be added)
    public function delete() {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $this->id);
        return $stmt->execute();
    }

    // Get all users
    public function getAll() {
        $query = "SELECT * FROM users";
        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Authenticate a user (used for login)
    public function authenticate($username, $password) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }
}
