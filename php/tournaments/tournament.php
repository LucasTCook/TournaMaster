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

<div id="leaderboard" class="leaderboard-container hidden">
    <div class="leaderboard-card first-place">
        <div>
            <i class="fas fa-trophy gold-trophy"></i>
            <span class="player-name">John Doe</span>
        </div>
        <span class="player-points">1200</span>
    </div>

    <div class="leaderboard-card second-place">
        <div>
            <i class="fas fa-trophy silver-trophy"></i>
            <span class="player-name">Jane Smith</span>
        </div>
        <span class="player-points">1100</span>
    </div>

    <div class="leaderboard-card third-place">
        <div>
            <i class="fas fa-trophy bronze-trophy"></i>
            <span class="player-name">Alex Roe</span>
        </div>
        <span class="player-points">1000</span>
    </div>

    <div class="leaderboard-card">
        <span class="player-name">Alex Roe</span>
        <span class="player-points">900</span>
    </div>

    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
