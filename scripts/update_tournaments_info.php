<?php

require '../models/Tournament.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle logo upload
    if(isset($_FILES['tournamentLogo'])) {
        $targetDir = "../images/uploads/tournament_logos";
        $targetFile = $targetDir . basename($_FILES["tournamentLogo"]["name"]);
        if (move_uploaded_file($_FILES["tournamentLogo"]["tmp_name"], $targetFile)) {
            $logo = basename($_FILES["tournamentLogo"]["name"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading logo.']);
            exit();
        }
    } else {
        $logo = '';
    }

    // Use the Tournament model to insert the tournament
    $tournament = new Tournament($_POST['id']);
    $tournament->logo = $logo;
    $tournament->name = $_POST['tournamentName'];
    $tournament->date = $_POST['tournamentDate'];

    // Save the tournament
    if ($tournament->update()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create tournament.']);
    }
}
