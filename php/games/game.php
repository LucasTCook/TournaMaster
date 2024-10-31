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

<div id="leaderboard" class="leaderboard-container hidden"></div>

<?php include '../../includes/footer.php'; ?>