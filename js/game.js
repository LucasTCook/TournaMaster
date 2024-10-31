$(document).ready(function() {
    loadGame();
});

function loadGame() {
    const urlPath = window.location.pathname.split('/');
    const tournamentGameId = urlPath[urlPath.length - 1];
    
    // Fetch game details
    $.ajax({
        url: `/scripts/get_game_details.php`,
        method: 'GET',
        data: { tournamentGameId: tournamentGameId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#game-header-container').empty();
                const game = response.data;
                
                // Create HTML structure
                let gameHtml = `
                    <div class="back-to-games" onclick="window.location.href='/tournament/${game.tournament_id}'"><</div>
                    <img src="${game.game_image_url || '/images/game-placeholder.jpg'}" alt="Game Picture" class="game-pic">
                    <h2 class="game-name">${game.game_name}</h2>
                    <div class="games-header-buttons">`;

                // Add bracket button only if type is 'bracket'
                if (game.type === 'bracket') {
                    gameHtml += `
                        <div id="game-bracket-button" class="games-selection active">
                            <span class="games-selection-title">View Bracket</span>
                        </div>`;
                }

                // Always show leaderboard button
                gameHtml += `
                    <div id="game-leaderboard-button" class="games-selection">
                        <span class="games-selection-title">Leaderboard</span>
                    </div>
                </div>`;

                // Inject the HTML into the page
                $('#game-header-container').html(gameHtml);

                if (game.type === 'bracket') {
                    $('#game-bracket-button').click(function(event) {
                        $('#game-bracket-button').addClass('active');
                        $('#bracket').removeClass('hidden');
                        $('#game-leaderboard-button').removeClass('active');
                        $('#leaderboard').addClass('hidden');
                    });
                    openBracket(game.tournament_game_id);
                } else {
                    $('#bracket').addClass('hidden');
                    $('#leaderboard').removeClass('hidden');
                }

                $('#game-leaderboard-button').click(function(event) {
                    $('#game-bracket-button').removeClass('active');
                    $('#bracket').addClass('hidden');
                    $('#game-leaderboard-button').addClass('active');
                    $('#leaderboard').removeClass('hidden');
                });
                loadGameLeaderboard(game.tournament_game_id, game.type);

            } else {
                console.error("Failed to load game details.");
            }
        },
        error: function(error) {
            console.error("Error fetching game details:", error);
        }
    });
}

function openBracket(tournamentGameId) {
    $.ajax({
        url: '/scripts/get_bracket.php',
        method: 'GET',
        dataType: 'json',
        data: { gameId: tournamentGameId },
        success: function(response) {
            if (response.success) {
                renderBracket(response.data);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error("Failed to fetch bracket data.");
        }
    });
}


function renderBracket(bracketData) {
    console.log(bracketData);

    const bracketContainer = $('#player-view-bracket');
    bracketContainer.empty();

    // Step 1: Group bracket data by round and match number
    const rounds = {};
    bracketData.data.forEach(entry => {
        // Create round if it doesn't exist
        if (!rounds[entry.round]) {
            rounds[entry.round] = {};
        }

        // Create match if it doesn't exist
        if (!rounds[entry.round][entry.match_number]) {
            rounds[entry.round][entry.match_number] = [];
        }

        // Add team entry to the respective match in the round
        rounds[entry.round][entry.match_number].push(entry);
    });

    // Step 2: Render each round and match
    Object.keys(rounds).forEach(roundIndex => {
        const round = rounds[roundIndex];

        // Create a new page for each round
        const bracketPage = $('<div>', { class: 'bracket-page', id: `bracket-page-${roundIndex}` });

        if(parseInt(roundIndex) === 0){
            bracketPage.append("<div class='round-header'><span>Round: Play-ins</span></div>");
        } else {
            bracketPage.append(`<div class='round-header'><span>Round: ${parseInt(roundIndex)}</span></div>`);
        }

        // Iterate over each match within the round
        Object.keys(round).forEach(matchIndex => {
            const matchTeams = round[matchIndex];
            // console.log(matchTeams);

            // Create a new group for each match
            const bracketGroup = $('<div>', { class: 'bracket-group'});

            // Render each team within the match
            matchTeams.forEach(team => {
                const bracketCard = $('<div>', { class: 'bracket-card with-line' });

                if (Array.isArray(team.players) && team.players.length > 1) {
                    // Team has multiple players
                    const doublePlayerContainer = $('<div>', { class: 'double-player-container' });
                    team.players.forEach((playerId) => {
                        const playerInfo = team.players_info.find(info => info.id === playerId) || {};
                        const playerName = $('<span>', {
                            class: `player-name-double ${team.result === 'LOSE' ? 'eliminated' : ''}`,
                            text: playerInfo.username || 'TBD'  // Show "--" if no name
                        });
                        doublePlayerContainer.append(playerName);
                    });
                    bracketCard.append(doublePlayerContainer);

                    // Add checkmark if there's a winner
                    if (team.result === 'WIN') {
                        bracketCard.append($('<i>', { class: 'fas fa-check winner-checkmark' }));
                    }
                } else if (Array.isArray(team.players) && team.players.length === 1) {
                    // Single player
                    const playerInfo = team.players_info[0] || {};
                    const playerName = $('<span>', {
                        class: `player-name ${team.result === 'LOSE' ? 'eliminated' : ''}`,
                        text: playerInfo.username || 'TBD'
                    });
                    bracketCard.append(playerName);

                    // Add checkmark if the player is a winner
                    if (team.result === 'WIN') {
                        bracketCard.append($('<i>', { class: 'fas fa-check winner-checkmark' }));
                    }
                }

                bracketGroup.append(bracketCard);
            });

            bracketPage.append(bracketGroup);
        });

        bracketContainer.append(bracketPage);
    });

    // Initialize pagination controls and show the first page
    let currentPage = 0;
    const totalPages = $('.bracket-page').length;
    showPage(currentPage);

    // Next button click
    $('.bracket-button:last-child').on('click', function() {
        if (currentPage < totalPages - 1) {
            currentPage++;
            showPage(currentPage);
        }
    });

    // Previous button click
    $('.bracket-button:first-child').on('click', function() {
        if (currentPage > 0) {
            currentPage--;
            showPage(currentPage);
        }
    });

    // Show the first bracket page initially
    $('.bracket-page').hide();
    $('#bracket-page-0').show();
    $('#tournament-games').hide();
    $('#add-winners').show();

    function showPage(pageIndex) {
        // Hide all pages and show the specific page
        $('.bracket-page').hide();
        $(`#bracket-page-${pageIndex}`).show();

        // Update button visibility based on page index
        $('.bracket-button:first-child').toggle(pageIndex > 0); // Hide "Previous" on first page
        $('.bracket-button:last-child').toggle(pageIndex < totalPages - 1); // Hide "Next" on last page
    }
}


function loadGameLeaderboard(tournamentGameId) {
    $.ajax({
        url: '/scripts/get_game_leaderboard.php',
        method: 'GET',
        dataType: 'json',
        data: { tournament_game_id: tournamentGameId },
        success: function(response) {
            if (response.success) {
                renderGameLeaderboard(response.data);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error("Failed to fetch leaderboard data.");
        }
    });
}

function renderGameLeaderboard(leaderboardData) {
    const leaderboardContainer = $('#leaderboard');
    leaderboardContainer.empty();

    leaderboardData.forEach((team, index) => {
        let trophyClass = '';
        if (index === 0 && team.points !== 0) trophyClass = 'gold-trophy';
        else if (index === 1 && team.points !== 0) trophyClass = 'silver-trophy';
        else if (index === 2 && team.points !== 0) trophyClass = 'bronze-trophy';

        // Generate individual spans for each player's name
        const playerNameSpans = team.player_names.map(name => `<span class="player-name">${name}</span>`).join('');

        const leaderboardCard = `
            <div class="leaderboard-card ${
                    index === 0 && team.points !== 0
                        ? 'first-place'
                        : index === 1 && team.points !== 0
                            ? 'second-place'
                            : index === 2 && team.points !== 0
                                ? 'third-place'
                                : ''
                }">
                <div class="points-names-container">
                    ${trophyClass ? `<i class="fas fa-trophy ${trophyClass}"></i>` : ''}
                    <div class="player-name-points-container">
                        ${playerNameSpans}
                    </div>
                </div>
                <span class="player-points">${team.points ?? '--'}</span>
            </div>
        `;
        
        leaderboardContainer.append(leaderboardCard);
    });
}