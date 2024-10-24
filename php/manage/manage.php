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

<div id="current-tournaments" class="tournament-list-container"></div>

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
        <button type="submit" id="create-tournament-button" class="success-btn">Create</button>
        <button class="cancel-btn" onclick="cancelTournamentCreation()">Cancel</button>
    </div>
</div>

<div id="save-banner" class="save-banner">Tournament Created</div>
<div id="save-error-banner" class="save-banner error">Tournament Creation Failed</div>

<?php include '../../includes/footer.php'; ?>
