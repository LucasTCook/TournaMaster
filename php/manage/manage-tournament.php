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
        <div id="tournament-leaderbaord-button" class="manage-selection leaderboard" onclick="window.location.href='/leaderboard'">
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

            <button id="submit-configure-game" class="success-btn save-configuration" onclick="saveGameConfiguration(this)">Save Configuration</button>
            <div class="delete-cancel-container">
                <button id="cancel-configure-game" class="edit-btn">Cancel</button>
                <button id="delete-game" class="cancel-btn" onclick="deleteGame(this)">DELETE</button>
            </div>

        </div>
    </div>
</div>

<div id="start-game-confirm" class="tournament-panel">
    <div class="game-card">
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
    <div class="leaderboard-card first-place margin-bottom-sm" onclick="addPoints()">
        <div>
            <i class="fas fa-trophy gold-trophy"></i>
            <span class="player-name">John Doe</span>
        </div>
        <span class="player-points">1200</span>
    </div>

    <div class="leaderboard-card second-place margin-bottom-sm" onclick="addPoints()">
        <div>
            <i class="fas fa-trophy silver-trophy"></i>
            <span class="player-name">Jane Smith</span>
        </div>
        <span class="player-points">1100</span>
    </div>

    <div class="leaderboard-card third-place margin-bottom-sm" onclick="addPoints()">
        <div>
            <i class="fas fa-trophy bronze-trophy"></i>
            <span class="player-name">Alex Roe</span>
        </div>
        <span class="player-points">1000</span>
    </div>

    <div class="leaderboard-card margin-bottom-sm" onclick="addPoints()">
        <span class="player-name">Alex Roe</span>
        <span class="player-points">900</span>
    </div>

    <div class="leaderboard-card margin-bottom-sm" onclick="addPoints()">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
    <div class="leaderboard-card margin-bottom-sm" onclick="addPoints()">
        <span class="player-name">New Player</span>
        <span class="player-points">--</span>
    </div>
</div>

<div id="add-points-player" class="tournament-panel">
    <div class="game-card">
        <div class="points-input-container">
            <span class="player-name">John Doe</span>
            <div class="form-group margin-top">
                <label for="points-input">Number of Points:</label>
                <input type="number" id="points-input" name="points-input" placeholder="0" required>
            </div>
            <button id="confirm-points" class="success-btn" onclick="confirmPoints()">Confirm Points</buttonid>
        </div>
    </div>
</div>

<div id="add-winners" class="tournament-panel">
    <div class="bracket-button-container">
        <div class="bracket-button">
            <span>< Previous</span>
        </div>
        <div class="bracket-button">
            <span>Next ></Nex></span>
        </div>
    </div>
    <div id="bracket-page-0" class="bracket-page">
        <div class="bracket-group" onclick="openBracketGroup(0)">
            <div class="bracket-card with-line">
                <div class="double-player-container">
                    <span class="player-name-double eliminated">Francisco Juarez-Martinez</span>
                    <span class="player-name-double">Alexander Fahrenholz</span>
                </div>
                <i class="fas fa-check winner-checkmark"></i>
            </div>
            <div class="bracket-card">
                <span class="player-name eliminated">John Doe</span>
                <div class="points">0</div>
            </div>
        </div>
        <div class="bracket-group" onclick="openBracketGroup(0)">
            <div class="bracket-card with-line">
                <div class="double-player-container">
                    <span class="player-name-double eliminated">Francisco Juarez-Martinez</span>
                    <span class="player-name-double">Alexander Fahrenholz</span>
                </div>
                <i class="fas fa-check winner-checkmark"></i>
            </div>
            <div class="bracket-card">
                <span class="player-name eliminated">John Doe</span>
                <div class="points">0</div>
            </div>
        </div>
        <div class="bracket-group" onclick="openBracketGroup(0)">
            <div class="bracket-card with-line">
                <div class="double-player-container">
                    <span class="player-name-double eliminated">Francisco Juarez-Martinez</span>
                    <span class="player-name-double">Alexander Fahrenholz</span>
                </div>
                <i class="fas fa-check winner-checkmark"></i>
            </div>
            <div class="bracket-card">
                <span class="player-name eliminated">John Doe</span>
                <div class="points">0</div>
            </div>
        </div>
        <div class="bracket-group" onclick="openBracketGroup(0)">
            <div class="bracket-card with-line">
                <div class="double-player-container">
                    <span class="player-name-double eliminated">Francisco Juarez-Martinez</span>
                    <span class="player-name-double">Alexander Fahrenholz</span>
                </div>
                <i class="fas fa-check winner-checkmark"></i>
            </div>
            <div class="bracket-card">
                <span class="player-name eliminated">John Doe</span>
                <div class="points">0</div>
            </div>
        </div>
    </div>
    <div id="bracket-page-1" class="bracket-page">
        <div class="bracket-group" onclick="openBracketGroup(1)">
            <div class="bracket-card with-line">
                <div>
                    <span class="player-name">TBD</span>
                    <i class="fas fa-trophy gold-trophy bracket-winner-trophey"></i>
                </div>
            </div>
            <div class="bracket-card">
                <div>
                    <span class="player-name">TBD</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="add-winners-bracket-group" class="tournament-panel">
    <div class="bracket-group-container">
        <div class="bracket-card" onclick="selectWinner(this)">
            <div class="double-player-container">
                <span class="player-name-double">Francisco Juarez-Martinez</span>
                <span class="player-name-double">Alexander Fahrenholz</span>
            </div>
        </div>
        <div class="bracket-card" onclick="selectWinner(this)">
            <div class="double-player-container">
                <span class="player-name-double">Francisco Juarez-Martinez</span>
                <span class="player-name-double">Alexander Fahrenholz</span>
            </div>
        </div>
        <button id="confirm-winner" class="success-btn" onclick="confirmWinner()">Confirm Winner</buttonid>
    </div>
</div>

<div id="tournament-players" class="tournament-panel">
    <button id="add-player" class="success-btn add-game-button">
        <i class="fas fa-qrcode"></i>
        Add Player
    </button>
    <span class="bold">Number of players: </span><span id="number-of-players"></span>
    <div id="players-container" class="players-container"></div>
</div>

<div id="tournament-players-form" class="tournament-panel center">
    <video id="qr-video" width="300" height="300" autoplay></video>
</div>

<div id="save-banner" class="save-banner">Tournament Info Saved</div>
<div id="save-error-banner" class="save-banner error">Tournament Info Save Error</div>
<div id="game-added-banner" class="save-banner">Game Added</div>
<div id="game-started-banner" class="save-banner">Game Started!</div>
<div id="game-configured-banner" class="save-banner">Game Successfully Configured</div>
<div id="invalid-configuration-banner" class="save-banner error">Configuration Invalid</div>
<div id="game-deleted-banner" class="save-banner error">Game Deleted</div>
<div id="duplicate-player-banner" class="save-banner error">Player Already Added</div>
<div id="player-added-banner" class="save-banner">Player Successfully Added</div>
<div id="player-removed-banner" class="save-banner error">Player Removed From Tournament</div>
<div id="player-reinstated-banner" class="save-banner">Player Reinstated</div>



<?php include '../../includes/footer.php'; ?>
