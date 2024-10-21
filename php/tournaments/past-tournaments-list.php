<?php 
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="../../css/tournaments.css">

<div class="tournaments-header-container">
    <h3 class="tournaments-header">Tournaments</h3>
    <div class="tournaments-header-buttons">
        <div class="tournaments-selection" onclick="window.location.href='./current-tournaments'">
            <span class="tournaments-selection-title">Current Tournaments</span>
        </div>
        <div class="tournaments-selection active" onclick="window.location.href='./past-tournaments'">
            <span class="tournaments-selection-title">Past Tournaments</span>
        </div>
    </div>
</div>
<div class="tournament-list-container">
    <div class="tournament-card" onclick="window.location.href='./tournament/1'">
        <span class="tournament-name">Somewhat Bi-annual Gaming Tournament</span>
        <br>
        <span class="tournament-info">Los Brolos Gaming</span>
        -
        <span class="tournament-info date">10/02/2023</span>
    </div>
    <div class="tournament-card" onclick="window.location.href='./tournament/1'">
        <span class="tournament-name">Somewhat Bi-annual Gaming Tournament</span>
        <br>
        <span class="tournament-info">Los Brolos Gaming</span>
        -
        <span class="tournament-info date">10/02/2022</span>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
