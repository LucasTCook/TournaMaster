<?php 
session_start();
$activePage = 'manage'; // Set active page to 'manage'
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="/css/manage.css">
<link rel="stylesheet" href="/css/tournaments.css">
<link rel="stylesheet" href="/css/games.css">
<script src="../../js/manage.js"></script>

<div class="manage-header-container">
    <h3 class="manage-header">Tournament Name</h3>
    <div class="manage-header-buttons">
        <div id="tournament-info-button" class="manage-selection active" onclick="changePage(this.id, 'tournament-info')">
            <span class="manage-selection-title">Tournament Info</span>
        </div>
        <div id="tournament-games-button" class="manage-selection" onclick="changePage(this.id, 'tournament-games')">
            <span class="manage-selection-title">Games</span>
        </div>
        <div id="tournament-players-button" class="manage-selection" onclick="changePage(this.id, 'tournament-players')">
            <span class="manage-selection-title">Players</span>
        </div>
        <div id="tournament-leaderbaord-button" class="manage-selection leaderboard" onclick="changePage(this.id, 'tournament-leaderboard')">
            <span class="manage-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<div id="tournament-info" class="tournament-panel">
    <div class="tournament-info-display">
        <img src="../../images/profile-placeholder.jpg" alt="Profile Picture" class="info-tournament-logo">
        <div class="tournament-info-container">
            <span class="info-tournament-name"><b>Tournament Name:</b></span>
            <span>Los Brolos Gaming Tournament</span>
            <span class="info-tournament-date"><b>Tournament Date: </b></span>
            <span>11/9/2024</span>
            <button class="edit-btn" onclick="editTournamentInfo()">Edit Tournament Info</button>
        </div>
    </div>
</div>

<div id="tournament-info-form" class="tournament-panel">
    <div class="tournament-info-form">
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
        <button class="success-btn" onclick="saveTournamentInfo()">Save</button>
    </div>
</div>

<div id="tournament-games" class="tournament-panel">
    <button class="success-btn add-game-button" onclick="addGame()">
        <i class="fas fa-add"></i>
        Add Game
    </button>
    <div class="game-card">
        <img src="\images\game-placeholder.jpg" alt="Game Image" class="game-image">
        <div class="game-info">
            <span class="game-name">Super Mario Bros</span>
            <div class="winner-info">
                <span>John Doe</span>
                <i class="fas fa-trophy winner-trophy"></i>
            </div>
        </div>
    </div>
    <div class="game-card">
        <img src="\images\game-placeholder.jpg" alt="Game Image" class="game-image">
        <div class="game-info">
            <span class="game-name">Super Mario Bros 2</span>
            <div class="winner-info">
                <span>In Progress</span>
                <i class="fas fa-spinner in-progress"></i>
            </div>
        </div>
    </div>
    <div class="game-card">
        <img src="\images\game-placeholder.jpg" alt="Game Image" class="game-image">
        <div class="game-info">
            <span class="game-name">Super Mario Bros 3</span>
            <div class="winner-info">
                <span>Not Yet Started</span>
            </div>
        </div>
    </div>
</div>

<div id="tournament-game-form" class="tournament-panel">
    <div>
        <div class="search-container">
            <label for="search-game">Search Game</label>
            <div class="input-wrapper">
                <input type="text" id="search-game" placeholder="Search game">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>

        <!-- <div id="add-game-info" class="add-game-info hidden"> -->
        <div id="add-game-info" class="add-game-info">
            <div class="add-game-header">
                <img id="add-game-image" class="add-game-image" src="/images/game-placeholder.jpg" alt="Game Image">
                <button id="add-game-confirm" class="add-game-form-btn" onclick="addGame()">
                    <i class="fas fa-add"></i> Add Game
                </button>
            </div>
            <p><strong>Name:</strong> <span id="game-name">Super Mario Brothers</span></p>
            <p><strong>Description:</strong> <span id="game-description">Description of the game. This can be long and will likely wrap. word vomit asdfasdf asdf as df asd f as df a sdf a sdf a sd f asd f as df a sdfasdfasdf asd fa sdfasdf asdfasdfasdf asdfasdf asdfasdf asdfasdf.</span></p>
            <p><strong>Release Year:</strong> <span id="game-release-year">1995</span></p>
            <p><strong>Platforms:</strong> <span id="game-platforms">Sega Genesis</span></p>
        </div>
    </div>
</div>

<div id="tournament-players" class="tournament-panel">
    <button id="add-player" class="success-btn add-game-button" onclick="scanQRCode()">
        <i class="fas fa-qrcode"></i>
        Add Player
    </button>
    <span class="bold">Number of players: </span><span id="number-of-players"></span>
    <div class="players-container">
        <div class="player-card">
            <span class="player-name">Alex Roe</span>
        </div>
    </div>
</div>

<div id="tournament-players-form" class="tournament-panel center">
    <video id="QR-preview"></video>
</div>

<div id="tournament-leaderboard" class="tournament-panel">
    leaderboard
</div>

<div id="save-banner" class="save-banner">Tournament Info Saved</div>
<div id="save-error-banner" class="save-banner error">Tournament Info Save Error</div>
<div id="game-added-banner" class="save-banner">Game Added</div>



<?php include '../../includes/footer.php'; ?>
