<?php

class TournamentUsers extends Model {
    protected $table = 'tournament_users';

    protected $id;
    protected $tournament_id;
    protected $user_id;
    protected $active;
    protected $created_at;
    protected $updated_at;

    // Getters
    public function getId() { return $this->id; }
    public function getTournamentId() { return $this->tournament_id; }
    public function getUserId() { return $this->user_id; }
    public function isActive() { return $this->active; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters
    public function setTournamentId($tournamentId) { $this->tournament_id = $tournamentId; }
    public function setUserId($userId) { $this->user_id = $userId; }
    public function setActive($active) { $this->active = $active; }

    // Save method
    public function save() {
        $stmt = $this->db->prepare("INSERT INTO $this->table (tournament_id, user_id, active) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $this->tournament_id, $this->user_id, $this->active);
        if ($stmt->execute()) {
            $this->id = $this->db->insert_id;
            return true;
        }
        return false;
    }

    // Update method
    public function update() {
        $stmt = $this->db->prepare("UPDATE $this->table SET active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $this->active, $this->id);
        return $stmt->execute();
    }
}
?>
