<?php
require_once '../config.php';

header('Content-Type: application/json');

$apiKey = getAPIKey() ?? null;

// Get the search query from the AJAX request
$searchQuery = isset($_GET['query']) ? urlencode($_GET['query']) : '';

if (!$searchQuery) {
    echo json_encode(['error' => 'No search query provided']);
    exit();
}

// Make the API request to RAWG
$apiUrl = "https://api.rawg.io/api/games?key={$apiKey}&search={$searchQuery}";

$response = file_get_contents($apiUrl);
if ($response === FALSE) {
    echo json_encode(['error' => 'Unable to fetch data from RAWG API']);
    exit();
}

echo $response;