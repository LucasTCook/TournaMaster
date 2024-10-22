<?php 
session_start();
$activePage = 'manage'; // Set active page to 'manage'
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="/css/manage.css">
<link rel="stylesheet" href="/css/tournaments.css">
<script src="../../js/manage.js"></script>

<div class="manage-header-container">
    <h3 class="manage-header">Tournament Name</h3>
    <div class="manage-header-buttons">
    <div id="add-tournament-button" class="manage-selection active">
            <span class="manage-selection-title">Tournament Info</span>
        </div>
        <!-- <div id="add-tournament-button" class="manage-selection">
            <span class="manage-selection-title">Games</span>
        </div>
        <div id="add-tournament-button" class="manage-selection">
            <span class="manage-selection-title">Players</span>
        </div> -->
    </div>
</div>

<div id="tournament-info" class="tournament-info-form">
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
    <button class="save-btn">Save</button>
</div>

<div id="save-banner" class="save-banner">Tournament Info Saved</div>
<div id="save-error-banner" class="save-banner error">Tournament Info Save Error</div>



<?php include '../../includes/footer.php'; ?>
