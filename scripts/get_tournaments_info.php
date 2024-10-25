<?php
require '../models/Tournament.php';

session_start();

$tournament = new Tournament($_GET['id']);
if ($tournament) {
    echo json_encode(['success' => true, 'data' => $tournament]);
} else {
    echo json_encode(['success' => false, 'message' => 'No tournaments found.']);
}
