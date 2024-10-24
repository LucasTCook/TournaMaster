<?php
require_once '../config.php';
class Model
{
    protected $db;

    public function __construct() {
        $this->db = getDbConnection();  // Automatically sets up the database connection
    }
}
