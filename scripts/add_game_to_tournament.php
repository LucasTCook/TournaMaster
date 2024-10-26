<?php

require '../config.php';
require '../models/Game.php';
require '../repos/GameRepository.php';
require '../models/TournamentGame.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON body
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data) {
        $gameSlug = $data['gameSlug'];
        $gameName = $data['gameName'];
        $gameImage = $data['gameImage'];
        $gameYear = $data['gameYear'];
        $gamePlatforms = $data['gamePlatforms'];
        $tournamentId = $data['tournamentId'];

        // Initialize Game model
        $gameRepo = new GameRepository();
        $existingGame = $gameRepo->getBySlug($gameSlug);

        if (!$existingGame) {
            // Create new game if it doesn’t exist
            $game = new Game();
            $game->setName($gameName);
            $game->setImageUrl($gameImage);
            $game->setSlug($gameSlug);
            $game->setReleaseYear($gameYear);
            $game->setPlatform($gamePlatforms);
            $gameId = $game->save();
        } else {
            // Use existing game ID
            $gameId = $existingGame['id'];
        }

        // Initialize TournamentGames model to link game and tournament
        $tournamentGame = new TournamentGame();
        $tournamentGame->setTournamentId($tournamentId);
        $tournamentGame->setGameId($gameId);

        if ($tournamentGame->save()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to link game to tournament']);
        }
    } else {
        echo json_encode(['error' => 'Invalid JSON data']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
