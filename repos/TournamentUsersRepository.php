<?php
require_once '../models/Model.php';
require_once '../models/TournamentUsers.php';

class TournamentUsersRepository extends Model {
    protected $table = 'tournament_users';

    public function __construct() {
        parent::__construct();
    }

    // Check if a user is already in the tournament
    public function getByTournamentAndUserId($tournamentId, $userId) {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE tournament_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tournamentId, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Add a user to the tournament if not already present
    public function addUserToTournament($tournamentId, $userId) {
        $existingUser = $this->getByTournamentAndUserId($tournamentId, $userId);
        
        if ($existingUser) {
            return false;  // User already exists in the tournament
        }

        // Create a new TournamentUsers model instance
        $tournamentUser = new TournamentUsers();
        $tournamentUser->setTournamentId($tournamentId);
        $tournamentUser->setUserId($userId);
        $tournamentUser->setActive(1); // Set active status to true by default
        return $tournamentUser->save();
    }

    public function getPlayersByTournamentId($tournamentId) {
        $stmt = $this->db->prepare(
            "SELECT u.username, u.id, tu.active
             FROM tournament_users tu
             JOIN users u ON tu.user_id = u.id
             WHERE tu.tournament_id = ?"
        );
        $stmt->bind_param("i", $tournamentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function setUserInactive($tournamentId, $userId) {
        $stmt = $this->db->prepare("UPDATE $this->table SET active = 0 WHERE tournament_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tournamentId, $userId);
        return $stmt->execute();
    }
    
    public function deleteUserFromTournament($tournamentId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM $this->table WHERE tournament_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $tournamentId, $userId);
        return $stmt->execute();
    }

    public function reinstatePlayer($userId, $tournamentId) {
        $stmt = $this->db->prepare("UPDATE $this->table SET active = 1 WHERE user_id = ? AND tournament_id = ?");
        $stmt->bind_param("ii", $userId, $tournamentId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>
