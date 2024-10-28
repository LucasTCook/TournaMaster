<?php
require_once '../config.php';
class Model
{
    protected $db;

    public function __construct() {
        $this->db = getDbConnection();  // Automatically sets up the database connection
    }

    public function findById($table, $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
