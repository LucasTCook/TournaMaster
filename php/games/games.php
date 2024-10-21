<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/games.css">

<div class="games-header-container">
    <span class="games-header-tournament-name">Tournament Name</span>
    <br>
    <span class="games-header">Games</span>
    <div class="games-header-buttons">
        <div class="games-selection active">
            <span class="games-selection-title">View Games</span>
        </div>
        <div class="games-selection" onclick="window.location.href='../leaderboard'">
            <span class="games-selection-title">Leaderboard</span>
        </div>
    </div>
</div>



<?php include '../../includes/footer.php'; ?>