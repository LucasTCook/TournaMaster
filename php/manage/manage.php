<?php 
session_start();
$activePage = 'manage'; // Set active page to 'manage'
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="/css/manage.css">
<link rel="stylesheet" href="/css/tournaments.css">
<script src="../../js/manage.js"></script>

<div class="manage-header-container">
    <h3 class="manage-header">Manage Tournaments</h3>
    <div class="manage-header-buttons">
        <div id="add-tournament-button" class="manage-selection-success">
            <span class="manage-selection-title">Create New Tournament</span>
        </div>
    </div>
</div>

<div id="current-tournaments" class="tournament-list-container">
    <div class="tournament-card" onclick="window.location.href='./manage-tournament/1'">
        <span class="tournament-name">Somewhat Bi-annual Gaming Tournament</span>
        <br>
        <span class="tournament-info">Los Brolos Gaming</span>
        -
        <span class="tournament-info date">10/02/2024</span>
        <br>
        <span class="tournament-info">0 Games</span>
    </div>
    <div class="tournament-card" onclick="window.location.href='./manage-tournament/1'">
        <span class="tournament-name">Somewhat Bi-annual Gaming Tournament 2</span>
        <br>
        <span class="tournament-info">Los Brolos Gaming</span>
        -
        <span class="tournament-info date">10/02/2024</span>
        <br>
        <span class="tournament-info">13 Games</span>
    </div>
</div>

<div id="add-tournament" class="add-tournament-form">
    <div class="form-group">
        <label for="tournament-name">Tournament Name</label>
        <input type="text" id="tournament-name" class="form-input" placeholder="Enter Tournament Name">
    </div>
    <div class="form-group">
        <label for="tournament-date">Tournament Date</label>
        <input type="date" id="tournament-date" class="form-input">
    </div>
    <div class="form-group">
        <label for="tournament-logo">Tournament Logo</label>
        <input type="file" id="tournament-logo" name="form-input" accept="image/*">
    </div>
    <div class="add-tournament-form-buttons">
        <button id="create-tournament-button" class="save-btn">Create</button>
        <button class="cancel-btn" onclick="cancelTournamentCreation()">Cancel</button>
    </div>
</div>

<div id="save-banner" class="save-banner">Tournament Created</div>
<div id="save-error-banner" class="save-banner error">Tournament Creation Cancelled</div>

<?php include '../../includes/footer.php'; ?>
