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
        <div class="games-selection">
            <span class="games-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<div class="games-container">
    <div class="game-card">
        <img src="images\game-placeholder.jpg" alt="Game Image" class="game-image">
        <div class="game-info">
            <span class="game-name">Super Mario Bros</span>
            <div class="winner-info">
                <span class="winner-name">John Doe</span>
                <i class="fas fa-trophy winner-trophy"></i>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>