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
        <div class="tournaments-selection" onclick="window.location.href='./past-tournaments'">
            <span class="tournaments-selection-title">Past Tournaments</span>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
