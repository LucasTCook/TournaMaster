<?php
require_once 'Model.php';

class Team extends Model
{
    protected $table = 'teams';

    private $id;
    private $tournamentGameId;
    private $teamNumber;
    private $players;
    private $createdAt;
    private $updatedAt;

    public function __construct( $id = null)
    {
        parent::__construct();

        if ($id) {
            $teamData = $this->findById($this->table, $id);
            if ($teamData) {
                $this->id = $teamData['id'];
                $this->tournamentGameId = $teamData['tournament_game_id'];
                $this->teamNumber = $teamData['team_number'];
                $this->players = json_decode($teamData['players'], true);
                $this->createdAt = $teamData['created_at'];
                $this->updatedAt = $teamData['updated_at'];
            }
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTournamentGameId() { return $this->tournamentGameId; }
    public function getTeamNumber() { return $this->teamNumber; }
    public function getPlayers() { return $this->players; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setTournamentGameId($tournamentGameId) { $this->tournamentGameId = $tournamentGameId; }
    public function setTeamNumber($teamNumber) { $this->teamNumber = $teamNumber; }
    public function setPlayers(array $players) { $this->players = json_encode($players); }

    // Save new team
    public function save()
    {
        $stmt = $this->db->prepare("
            INSERT INTO teams (tournament_game_id, team_number, players)
            VALUES (?, ?, ?)
        ");
        if (!$stmt) {
            echo "Prepare failed: " . $this->db->error;
            return false;
        }

        $stmt->bind_param("iis", $this->tournamentGameId, $this->teamNumber, $this->players);
        
        if ($stmt->execute()) {
            $this->id = $this->db->insert_id;
            return true;
        } else {
            echo "Execute failed: " . $stmt->error;
            return false;
        }
    }


    // Update existing team
    public function update()
    {
        $stmt = $this->db->prepare("
            UPDATE $this->table SET team_number = ?, players = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->bind_param("isi", $this->teamNumber, $this->players, $this->id);
        return $stmt->execute();
    }

    // Load team by ID
    public function loadById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->id = $result['id'];
            $this->tournamentGameId = $result['tournament_game_id'];
            $this->teamNumber = $result['team_number'];
            $this->players = $result['players'];
            $this->createdAt = $result['created_at'];
            $this->updatedAt = $result['updated_at'];
            return true;
        }
        return false;
    }
}
