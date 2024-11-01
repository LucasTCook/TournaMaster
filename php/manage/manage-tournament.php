<?php 
session_start();
$activePage = 'manage'; // Set active page to 'manage'
include '../../includes/header.php'; 
?>
<link rel="stylesheet" href="/css/manage.css">
<link rel="stylesheet" href="/css/tournaments.css">
<link rel="stylesheet" href="/css/games.css">
<link rel="stylesheet" href="/css/game.css">
<link rel="stylesheet" href="/css/bracket.css">

<script src="/js/bracket.js"></script>
<script src="../../js/manage-tournament.js"></script>

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
        <div id="tournament-leaderbaord-button" class="manage-selection leaderboard" onclick="goToLeaderboard()">
            <span class="manage-selection-title">Leaderboard</span>
        </div>
    </div>
</div>

<div id="tournament-info" class="tournament-panel">
    <div class="tournament-info-display">
        <img id="info-tournament-logo" src="../../images/game-placeholder.jpg" alt="Profile Picture" class="info-tournament-logo">
        <div class="tournament-info-container">
            <span class="info-tournament-name"><b>Tournament Name:</b></span>
            <span id="tournament-name"></span>
            <span class="info-tournament-date"><b>Tournament Date: </b></span>
            <span id="tournament-date"></span>
            <button class="edit-btn" onclick="editTournamentInfo()">Edit Tournament Info</button>
        </div>
    </div>
</div>

<div id="tournament-info-form" class="tournament-panel">
    <div class="tournament-info-form">
        <div class="form-group">
            <label for="tournament-name-edit">Tournament Name</label>
            <input type="text" id="tournament-name-edit" class="form-input" placeholder="Enter Tournament Name">
        </div>
        <div class="form-group">
            <label for="tournament-date-edit">Tournament Date</label>
            <input type="date" id="tournament-date-edit" class="form-input">
        </div>
        <div class="form-group">
            <label for="tournament-logo">Tournament Logo</label>
            <input type="file" id="tournament-logo-edit" name="form-input" accept="image/*">
        </div>
        <button class="success-btn" onclick="updateTournamentInfo()">Save</button>
    </div>
</div>

<div id="tournament-games" class="tournament-panel">
    <button class="success-btn add-game-button" onclick="addGame()">
        <i class="fas fa-add"></i>
        Add Game
    </button>
    <div id="tournament-games-list"></div>
</div>

<div id="configure-game" class="tournament-panel">
    <div class="game-card">
        <div class="configure-game-container">
            <div class="form-group">
                <label for="configure-game-name">Game Name</label>
                <input type="text" id="configure-game-name" name="configure-game-name" placeholder="Enter game name" required>
            </div>
            <div class="form-group">
                <label for="configure-game-type">Game Type</label>
                <select id="configure-game-type" name="configure-game-type">
                    <option value=""></option>
                    <option value="bracket">Bracket</option>
                    <option value="points">Points</option>
                </select>
            </div>

            <!-- Bracket specific fields -->
            <div id="bracket-fields" class="hidden">
                <div class="form-group">
                    <label for="configure-team-size">Size of Teams</label>
                    <input type="number" id="configure-team-size" name="configure-team-size" placeholder="Enter team size">
                </div>

                <div class="form-group">
                    <label for="configure-teams-per-match">Teams per Match</label>
                    <input type="number" id="configure-teams-per-match" name="configure-teams-per-match" placeholder="Enter number of teams per match">
                </div>

                <div class="form-group">
                    <label for="configure-winners-per-match">Number of Winners per Match</label>
                    <input type="number" id="configure-winners-per-match" name="configure-winners-per-match" placeholder="Enter number of winners per match">
                </div>
            </div>

             <!-- Points specific fields -->
             <div id="points-fields" class="hidden">
                <div class="form-group">
                    <label for="configure-team-size-points">Size of Teams</label>
                    <input type="number" id="configure-team-size-points" name="configure-team-size-points" placeholder="Enter team size">
                </div>
            </div>

            <button id="submit-configure-game" class="success-btn save-configuration" onclick="saveGameConfiguration(this)">Save Configuration</button>
            <div class="delete-cancel-container">
                <button id="cancel-configure-game" class="edit-btn">Cancel</button>
                <button id="delete-game" class="cancel-btn" onclick="deleteGame(this)">DELETE</button>
            </div>

        </div>
    </div>
</div>

<div id="start-game-confirm" class="tournament-panel">
    <div class="confirm-start">
        <div class="game-info">
            <span id="confirm-start-game-name" class="bold"></span>
            <span class="">Are you sure you want to start this game?
                <br>
                <i>
                    This cannot be undone.
                </i>
            </span>
            <div class="manage-game-buttons margin-top">
                <button class="cancel-btn small-font auto-width auto-margin" onclick="cancelStartGame()">Cancel</button>
                <button id="start-game-button" class="success-btn small-font auto-width auto-margin" onclick="startGame(this)">START GAME</button>
            </div>
        </div>
    </div>
</div>

<div id="tournament-game-form" class="tournament-panel">
    <div class="search-container">
        <label for="search-game">Search Game</label>
        <div class="input-wrapper">
            <input type="text" id="search-game" placeholder="Search game">
            <i class="fas fa-search search-icon"></i>
        </div>
    </div>
    
    <div id="add-games-container"></div>
</div>

<div id="add-points" class="tournament-panel">
    <div id="leaderboard-container"></div>
</div>

<div id="add-points-player" class="tournament-panel">
    <div class="game-card">
        <div id="points-input-players" class="points-input-container"></div>
    </div>
</div>

<div id="add-winners" class="tournament-panel">
    <div class="bracket-button-container">
        <div class="bracket-button">
            <span>< Previous</span>
        </div>
        <div class="bracket-button">
            <span>Next ></span>
        </div>
    </div>
    <div id="add-winners-container"></div>
    <input type="hidden" id="tournamentGameInfo">
    <input type="hidden" id="round-number">
</div>

<div id="add-winners-bracket-group" class="tournament-panel">
    <div class="bracket-group-buttons">
        <div id="bracket-group-back-button" class="error-btn">< Back</div>
    </div>
    <div id="add-winners-bracket-group">
        <div class="bracket-group-container" id="bracket-group-container">
            <!-- Bracket cards will be dynamically inserted here -->
        </div>
        <button id="confirm-winner" class="success-btn" onclick="confirmWinner()">Confirm Winner</button>
    </div>
</div>

<div id="tournament-players" class="tournament-panel">
    <button id="add-player" class="success-btn add-game-button">
        <i class="fas fa-qrcode"></i>
        Add Player
    </button>
    <button id="roll-player" class="edit-btn add-game-button">
        <i class="fas fa-dice"></i>
        Roll Random Active Player
    </button>
    <div id="random-player-container" class="random-player-container">
        <span id="random-player"></span>
    </div>
    <span class="bold">Total players: </span><span id="number-of-players"></span>
    <br>
    <span class="bold">Active players: </span><span id="number-of-active-players"></span>

    <div id="players-container" class="players-container"></div>
</div>

<div id="tournament-players-form" class="tournament-panel center">
    <video id="qr-video" width="300" height="300" autoplay></video>
</div>

<div id="save-banner" class="save-banner">Tournament Info Saved</div>
<div id="save-error-banner" class="save-banner error">Tournament Info Save Error</div>
<div id="game-added-banner" class="save-banner">Game Added</div>
<div id="game-started-banner" class="save-banner">Game Started!</div>
<div id="game-finished-banner" class="save-banner">Game Finished!</div>
<div id="game-configured-banner" class="save-banner">Game Successfully Configured</div>
<div id="invalid-configuration-banner" class="save-banner error">Configuration Invalid</div>
<div id="game-deleted-banner" class="save-banner error">Game Deleted</div>
<div id="duplicate-player-banner" class="save-banner error">Player Already Added</div>
<div id="player-added-banner" class="save-banner">Player Successfully Added</div>
<div id="player-removed-banner" class="save-banner error">Player Removed From Tournament</div>
<div id="player-reinstated-banner" class="save-banner">Player Reinstated</div>



<?php include '../../includes/footer.php'; ?>
