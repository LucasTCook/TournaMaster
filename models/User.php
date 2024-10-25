<?php
require_once 'Model.php';

class User extends Model {
    protected $table = 'users';
    public $id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $profile_image_url;
    public $bio;
    public $role;
    public $created_at;
    public $updated_at;

    public function __construct($user_id = null) {
        parent::__construct();  // Inherit the database connection from the parent Model

        if ($user_id) {
            $this->getById($user_id);
        }
    }

    // Fetch user details by ID and populate the object properties
    public function getById($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->role = $user['role'];
            $this->password = $user['password'];
            $this->first_name = $user['first_name'];
            $this->last_name = $user['last_name'];
            $this->profile_image_url = $user['profile_image_url'];
            $this->bio = $user['bio'];
        }

        $stmt->close();
    }

    // Fetch user by username
    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE username = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        return $user;
    }

    // Verify user password
    public function verifyPassword($inputPassword, $storedPasswordHash) {
        return password_verify($inputPassword, $storedPasswordHash);
    }

    // Check if a user exists by username or email
    public function userExists($username, $email) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE username = ? OR email = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;  // Return true if user exists
    }

    // Create a new user
    public function createUser($username, $password, $email) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO $this->table (username, password, email) VALUES (?, ?, ?)");

        if (!$stmt) {
            die('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("sss", $username, $hashedPassword, $email);
        return $stmt->execute();  // Return true if successful
    }
    
    public function update() {
        // Prepare the SQL statement
        $query = "
            UPDATE {$this->table} 
            SET 
                username = ?, 
                email = ?, 
                password = ?, 
                first_name = ?, 
                last_name = ?, 
                profile_image_url = ?, 
                bio = ?, 
                role = ?, 
                updated_at = NOW()
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($query);

        if ($stmt) {
            // Bind the parameters to the prepared statement
            $stmt->bind_param(
                "ssssssssi", 
                $this->username, 
                $this->email, 
                $this->password, 
                $this->first_name, 
                $this->last_name, 
                $this->profile_image_url, 
                $this->bio, 
                $this->role, 
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
