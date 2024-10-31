$(document).ready(function() {
    loadTournamentGames();
    loadLeaderboard();
    $('#leaderboard-button').click(function(event) {
        loadLeaderboard();
        $('#games-button').removeClass('active');
        $('#user-tournament-games-list').addClass('hidden');
        $('#leaderboard-button').addClass('active');
        $('#leaderboard').removeClass('hidden');
    });

    $('#games-button').click(function(event) {
        loadTournamentGames();
        $('#games-button').addClass('active');
        $('#user-tournament-games-list').removeClass('hidden');
        $('#leaderboard-button').removeClass('active');
        $('#leaderboard').addClass('hidden');
    });
});

function loadTournamentGames() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    $('#user-tournament-games-list').empty();
    $.ajax({
        url: '/scripts/get_tournament_games.php',
        method: 'GET',
        dataType: 'json',
        data: { tournamentId: tournamentId },
        success: function(response) {
            if (response.success) {
                response.games.forEach(game => renderGameCard(game));  // Render each game
            } else {
                console.error('Failed to load games:', response.error);
            }
        },
        error: function() {
            console.error('Error fetching tournament games');
        }
    });
}

function renderGameCard(game) {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    let gameCardHtml = '';
    const gameImage = game.game_image_url || '\\images\\game-placeholder.jpg';

    if (game.status === 2) {  // Completed game
        gameCardHtml = `
            <div class="game-card" onclick="window.location.href='/game/${tournamentId}/${game.id}'">
                <img src="${gameImage}" alt="Game Image" class="game-image">
                <div class="game-info">
                    <span class="game-name">${game.game_name}</span>
                    ${game.type === 'bracket' ? `
                        <div class="tournament-info-display-card">
                            <span><b>Game Type:</b> Bracket</span>
                            <span><b>Number of teams:</b> ${game.team_count}</span>
                            <span><b>Players per team:</b> ${game.team_size}</span>
                            <span><b>Teams per match:</b> ${game.teams_per_match}</span>
                            <span><b>Winners per match:</b> ${game.winners_per_match}</span>
                        </div>` : '<div class="tournament-info-display-card"><span><b>Game Type:</b> Points</span><div>' } 
                    <div class="winner-info">
                        <i class="fas fa-trophy winner-trophy"></i>
                        <div class="winner-names-container">
                            <span>${game.winner_team_number === 0 ? 'TIE' : game.winner_name}</span>
                        </div>
                    </div>
                </div>
            </div>`;
    } else if (game.status === 1) {  // In-progress game
        if (game.type === 'points') {
            gameCardHtml = `
                <div class="game-card" onclick="window.location.href='/game/${tournamentId}/${game.id}'">
                    <img src="${gameImage}" alt="Game Image" class="game-image">
                    <div class="game-info">
                        <span class="game-name">${game.game_name}</span>
                        <div class="winner-info">
                            <span>In Progress</span>
                            <i class="fas fa-spinner in-progress"></i>
                        </div>
                        <div class="tournament-info-display-card">
                            <span><b>Game Type:</b> Points</span>
                            <span><b>Number of teams:</b> ${game.team_count}</span>
                            <span><b>Players per team:</b> ${game.team_size}</span>
                        <div>
                    </div>
                </div>`;
        } else if (game.type === 'bracket') {
            gameCardHtml = `
                <div class="game-card" onclick="window.location.href='/game/${tournamentId}/${game.id}'">
                    <img src="${gameImage}" alt="Game Image" class="game-image">
                    <div class="game-info">
                        <span class="game-name">${game.game_name}</span>
                        <div class="winner-info">
                            <span>In Progress</span>
                            <i class="fas fa-spinner in-progress"></i>
                        </div>
                        <div class="tournament-info-display-card">
                            <span><b>Game Type:</b> Bracket</span>
                            <span><b>Number of teams:</b> ${game.team_count}</span>
                            <span><b>Players per team:</b> ${game.team_size}</span>
                            <span><b>Teams per match:</b> ${game.teams_per_match}</span>
                            <span><b>Winners per match:</b> ${game.winners_per_match}</span>
                        </div>
                    </div>
                </div>`;
        }
    } else if (game.status === 0) {  // Not started
        if (game.type && (game.type !== 'bracket' || (game.team_size && game.teams_per_match && game.winners_per_match))) {
            gameCardHtml = `
                <div class="game-card" onclick="window.location.href='/game/${tournamentId}/${game.id}'">
                    <img src="${gameImage}" alt="Game Image" class="game-image">
                    <div class="game-info">
                        <span class="game-name">${game.game_name}</span>
                        <div class="winner-info">
                            <span>Not Yet Started</span>
                        </div>
                        ${game.type === 'bracket' ? `
                            <div class="tournament-info-display-card">
                                <span><b>Game Type:</b> Bracket</span>
                                <span><b>Number of teams:</b> ${game.team_count}</span>
                                <span><b>Players per team:</b> ${game.team_size}</span>
                                <span><b>Teams per match:</b> ${game.teams_per_match}</span>
                                <span><b>Winners per match:</b> ${game.winners_per_match}</span>
                            </div>`
                            : `<div class="tournament-info-display-card">
                                <span><b>Game Type:</b> Points</span>
                                <span><b>Number of teams:</b> ${game.team_count}</span>
                                <span><b>Players per team:</b> ${game.team_size}</span>
                            <div>` } 
                        <div class="manage-game-buttons">
                            <button class="edit-btn small-font auto-width edit-game-button" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="configureGame(this)">Edit Game</button>
                            <button class="success-btn small-font auto-width" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="confirmStartGame(this)">START GAME</button>
                        </div>
                    </div>
                </div>`;
        }
    }
    // Append to the tournament-games-list container
    $('#user-tournament-games-list').append(gameCardHtml);
}

function loadLeaderboard() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    $.ajax({
        url: '/scripts/get_all_users_points.php',
        method: 'GET',
        dataType: 'json',
        data: { tournament_id: tournamentId },
        success: function(response) {
            if (response.success) {
                renderLeaderboard(response.leaderboard);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error('Failed to fetch leaderboard data');
        }
    });
}

function renderLeaderboard(leaderboard) {
    const leaderboardContainer = $('#leaderboard');
    leaderboardContainer.empty();

    if (leaderboard.length === 0) {
        return;
    }

    leaderboard.forEach((player, index) => {
        let trophyClass = '';
        let trophyIcon = '';

        if (index === 0) {
            trophyClass = 'first-place';
            trophyIcon = '<i class="fas fa-trophy gold-trophy"></i>';
        } else if (index === 1) {
            trophyClass = 'second-place';
            trophyIcon = '<i class="fas fa-trophy silver-trophy"></i>';
        } else if (index === 2) {
            trophyClass = 'third-place';
            trophyIcon = '<i class="fas fa-trophy bronze-trophy"></i>';
        }

        const playerPoints = player.total_points > 0 ? player.total_points : '--';
        const leaderboardCardHtml = `
            <div class="leaderboard-card ${trophyClass}">
                <div>
                    ${trophyIcon}
                    <span class="player-name">${player.username}</span>
                </div>
                <span class="player-points">${playerPoints}</span>
            </div>
        `;

        leaderboardContainer.append(leaderboardCardHtml);
    });
}