<?php
require_once 'Model.php';

class Game extends Model {
    protected $table = 'games';
    protected $id;
    protected $slug;
    protected $name;
    protected $image_url;
    protected $release_year;
    protected $platform;

    public function __construct($game_id = null) {
        parent::__construct();  // Initialize the DB connection
        if ($game_id) {
            $this->getById($game_id);
        }
    }

    public function getById($game_id) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $game = $result->fetch_assoc();
            $this->id = $game['id'];
            $this->slug = $game['slug'];
            $this->name = $game['name'];
            $this->image_url = $game['image_url'];
            $this->release_year = $game['release_year'];
            $this->platform = $game['platform'];
        }
        $stmt->close();
    }

    // ID
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // Slug
    public function getSlug() {
        return $this->slug;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    // Name
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    // Image URL
    public function getImageUrl() {
        return $this->image_url;
    }

    public function setImageUrl($image_url) {
        $this->image_url = $image_url;
    }

    // Release Year
    public function getReleaseYear() {
        return $this->release_year;
    }

    public function setReleaseYear($release_year) {
        $this->release_year = $release_year;
    }

    // Platform
    public function getPlatform() {
        return $this->platform;
    }

    public function setPlatform($platform) {
        $this->platform = $platform;
    }

    public function save() {
        if ($this->slug) {
            $stmt = $this->db->prepare(
                "INSERT INTO $this->table (name, slug, image_url, release_year, platform, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
            );
            
            $stmt->bind_param("sssss", $this->name, $this->slug, $this->image_url, $this->release_year, $this->platform);
            
            if ($stmt->execute()) {
                return $this->db->insert_id;
            }
        }
        return false;
    }
}
