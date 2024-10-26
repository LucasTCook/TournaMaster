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
}
?>
