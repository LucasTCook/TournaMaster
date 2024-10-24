<?php

require '../models/tournament.php';  // Include the Tournament model
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    if (!isset($_POST['tournamentName']) || !isset($_POST['tournamentDate'])) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $tournamentName = $_POST['tournamentName'];
    $tournamentDate = $_POST['tournamentDate'];

    // Handle logo upload
    $targetDir = "../images/uploads/tournament_logos/";
    $targetFile = $targetDir . basename($_FILES["tournamentLogo"]["name"]);
    if (move_uploaded_file($_FILES["tournamentLogo"]["tmp_name"], $targetFile)) {
        $logoUrl = basename($_FILES["tournamentLogo"]["name"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading logo.']);
        exit();
    }

    // Use the Tournament model to insert the tournament
    $tournament = new Tournament();
    $tournament->name = $tournamentName;
    $tournament->date = $tournamentDate;
    $tournament->logo = $logoUrl;
    $tournament->creator_id = $_SESSION['user_id'];  // Assuming the user is logged in

    // Save the tournament
    if ($tournament->save()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create tournament.']);
    }
}
