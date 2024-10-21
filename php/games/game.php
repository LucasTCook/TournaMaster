<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/game.css">
<script src="../../js/game.js"></script>

<div class="game-header-container">
    <img src="../../images/game-placeholder.jpg" alt="game Picture" class="game-pic">
    <h2 class="game-name">Mario Kart 64: Electric Boogaloo</h2>
    <div class="games-header-buttons">
        <div id="game-bracket-button" class="games-selection active">
            <span class="games-selection-title">View Bracket</span>
        </div>
        <div id="game-leaderboard-button" class="games-selection">
            <span class="games-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<div id="bracket" class="bracket-container">

</div>

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
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>