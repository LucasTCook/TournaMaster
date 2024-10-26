<?php 

class GameRepository extends Model {

    private $table = 'games';

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE slug = ?");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}