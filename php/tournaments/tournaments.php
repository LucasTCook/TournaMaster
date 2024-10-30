<?php 
session_start();
$activePage = 'tournaments';
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="../../css/tournaments.css">
<script src="../../js/tournaments.js"></script>

<div class="tournaments-header-container">
    <h3 class="tournaments-header">Tournaments</h3>
    <div class="tournaments-header-buttons">
        <div id="current-button" class="tournaments-selection active">
            <span class="tournaments-selection-title">Current Tournaments</span>
        </div>
        <div id="past-button" class="tournaments-selection">
            <span class="tournaments-selection-title">Past Tournaments</span>
        </div>
    </div>
</div>
<div id="current-tournaments" class="tournament-list-container"></div>
<div id="past-tournaments" class="tournament-list-container hidden"></div>

<?php include '../../includes/footer.php'; ?>
