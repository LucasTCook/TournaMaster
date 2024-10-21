<?php
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/tournament.css">

<div class="tournament-header-container">
    <h3 class="tournament-header">Tournament Name</h3>
    <div class="tournament-header-buttons">
        <div class="tournament-selection" onclick="window.location.href='../games'">
            <span class="tournament-selection-title">View Games</span>
        </div>
        <div class="tournament-selection">
            <span class="tournament-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
