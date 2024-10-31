<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="/css/game.css">
<link rel="stylesheet" href="/css/bracket.css">
<link rel="stylesheet" href="/css/tournament.css">
<script src="/js/game.js"></script>
<script src="/js/bracket.js"></script>

<div id="game-header-container" class="game-header-container"></div>

<div id="bracket" class="bracket-container">
    <div class="bracket-button-container">
        <div class="bracket-button">
            <span>< Previous</span>
        </div>
        <div class="bracket-button">
            <span>Next ></Nex></span>
        </div>
    </div>
    <div id="player-view-bracket"></div>
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