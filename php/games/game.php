<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="/css/game.css">
<link rel="stylesheet" href="/css/bracket.css">
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
    <div id="bracket-page-0" class="bracket-page">
        <div class="bracket-group">
            <div class="bracket-card with-line">
                <div class="double-player-container">
                    <span class="player-name-double eliminated">Francisco Juarez-Martinez</span>
                    <span class="player-name-double">Alexander Fahrenholz</span>
                </div>
                <i class="fas fa-check winner-checkmark"></i>
            </div>
            <div class="bracket-card">
                <span class="player-name eliminated">John Doe</span>
                <div class="points">0</div>
            </div>
        </div>
    </div>
    <div id="bracket-page-1" class="bracket-page">
        <div class="bracket-group">
            <div class="bracket-card with-line">
                <div>
                    <span class="player-name">TBD</span>
                    <i class="fas fa-trophy gold-trophy bracket-winner-trophey"></i>
                </div>
            </div>
            <div class="bracket-card">
                <div>
                    <span class="player-name">TBD</span>
                </div>
            </div>
        </div>
    </div>
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