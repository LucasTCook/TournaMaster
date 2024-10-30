<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/tournament.css">
<link rel="stylesheet" href="../../css/games.css">
<link rel="stylesheet" href="../../css/leaderboard.css">
<script src="../../js/tournament.js"></script>

<div class="tournament-header-container">
    <h3 class="tournament-header">Tournament Name</h3>
    <div class="tournament-header-buttons">
        <div id="games-button" class="tournament-selection active">
            <span class="tournament-selection-title">View Games</span>
        </div>
        <div id="leaderboard-button" class="tournament-selection">
            <span class="tournament-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<div id="user-tournament-games-list" class="games-container"></div>

<div id="leaderboard" class="leaderboard-container hidden"></div>

<?php include '../../includes/footer.php'; ?>
