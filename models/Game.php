<?php
class Game extends Model {
    protected $table = 'games';
    protected $id;
    protected $name;
    protected $description;
    protected $image_url;
    protected $release_year;
    protected $platform;
    protected $type;
    protected $team_size;
    protected $teams_per_match;
    protected $winners_per_match;

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
            $this->name = $game['name'];
            $this->description = $game['description'];
            $this->image_url = $game['image_url'];
            $this->release_year = $game['release_year'];
            $this->platform = $game['platform'];
            $this->type = $game['type'];
            $this->team_size = $game['team_size'];
            $this->teams_per_match = $game['teams_per_match'];
            $this->winners_per_match = $game['winners_per_match'];
        }
        $stmt->close();
    }

}
