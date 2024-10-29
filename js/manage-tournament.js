
$(document).ready(function() {
    loadTounamentInfo();
    loadTournamentGames();
    loadTournamentPlayers();
    $('#tournament-info').show();
    $('#number-of-players').html($('.player-card').length);

    $('#add-game-confirm').on('click', function() {
        // Simulate a save process
        let saveSuccess = true; // Change this based on actual save outcome

        if (saveSuccess) {
            $('#tournament-game-form').hide();
            $('#tournament-games').show();
            showBanner('#game-added-banner');
        } else {
            // showBanner('#save-error-banner');
        }
    });

    $('#configure-game-type').on('change', function() {
        if ($(this).val() === 'bracket') {
            $('#bracket-fields').removeClass('hidden');
            $('#points-fields').addClass('hidden');
        } else if ($(this).val() === 'points') {
            $('#bracket-fields').addClass('hidden');
            $('#points-fields').removeClass('hidden');
        } else {
            $('#bracket-fields').addClass('hidden');
            $('#points-fields').addClass('hidden');
        }
    });

    $('#search-game').on('keyup', function() {
        const query = $(this).val().trim();
        
        if (query.length > 2) {  // Only start searching if there are 3+ characters
            searchGame(query);
        }
    });

    $('#cancel-configure-game').on('click', function(e){
        $('#tournament-games').show();
        $('#configure-game').hide();
    });

    const video = document.getElementById("qr-video");

    $('#add-player').on('click', async function() {
        $('#tournament-players').hide();
        $('#tournament-players-form').show();
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
        video.srcObject = stream;

        let qrProcessed = false;  // Flag to prevent multiple AJAX calls

        video.addEventListener("play", () => {
            const captureFrame = () => {
                if (video.readyState === video.HAVE_ENOUGH_DATA && !qrProcessed) {
                    const canvas = document.createElement("canvas");
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext("2d");
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Process the frame with jsQR
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code && !qrProcessed) {
                        qrProcessed = true;  // Set flag to prevent further processing
                        
                        // AJAX request
                        const formData = new FormData();
                        formData.append('id', code.data);

                        const urlPath = window.location.pathname.split('/');
                        const tournamentId = urlPath[urlPath.length - 1];
                        formData.append('tournamentId', tournamentId);

                        $.ajax({
                            url: '/scripts/add_player_to_tournament.php',
                            method: 'POST',
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                stream.getTracks().forEach(track => track.stop()); // Stop the video stream
                                $('#tournament-players').show();
                                $('#tournament-players-form').hide();
                                loadTournamentPlayers();
                                // Check for success or error in the response to show the appropriate banner
                                if (response.success) {
                                    showBanner('#player-added-banner');
                                } else if (response.error) {
                                    showBanner('#duplicate-player-banner');
                                }
                            }
                        });
                    }
                }
                if (!qrProcessed) {
                    requestAnimationFrame(captureFrame);  // Continue only if not yet processed
                }
            };
            captureFrame();
        });
    });

    $("#bracket-group-back-button").on('click', function(e){
        $('#add-winners').show();
        $('#add-winners-bracket-group').hide();
    });
});

function reinstatePlayerInTournament(userId) {
    const tournamentId = window.location.pathname.split('/').pop();
    const formData = new FormData();
    formData.append('userId', userId);
    formData.append('tournamentId', tournamentId);

    $.ajax({
        url: '/scripts/reinstate_tournament_player.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Reload players to reflect changes
                loadTournamentPlayers();
                showBanner('#player-reinstated-banner');
            }
        }
    });
}

function deletePlayerFromTournament(userId) {
    const tournamentId = window.location.pathname.split('/').pop();

    const formData = new FormData();
    formData.append('userId', userId);
    formData.append('tournamentId', tournamentId);

    $.ajax({
        url: '/scripts/delete_tournament_player.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Reload players to reflect changes
                loadTournamentPlayers();
                showBanner('#player-removed-banner');
            }
        }
    });
}


function loadTounamentInfo() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    $.ajax({
        url: '/scripts/get_tournaments_info.php',
        method: 'GET',
        dataType: 'json',
        data: {id: tournamentId},
        success: function(response) {
            $('#tournament-name').html(response.data.name);
            $('#tournament-date').html(moment(response.data.date).format('MM/DD/YYYY'));
            $('#tournament-name-edit').val(response.data.name);
            $('#tournament-date-edit').val(response.data.date);
            if (response.data.logo !== ''){
                $('#info-tournament-logo').attr('src', '/images/uploads/tournament_logos/'+response.data.logo)
            }
        }
    });
}

function loadTournamentPlayers() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];

    $.ajax({
        url: '/scripts/get_tournament_players.php',
        method: 'POST',
        dataType: 'json',
        data: { tournamentId: tournamentId },
        success: function(response) {
            $('#players-container').empty();  // Clear existing players
            if (response.success) {

                // Iterate over each player and create a player card
                response.players.forEach(player => {
                    const playerCard = $(`
                        <div class="player-card ${!player.active ? 'inactive' : ''}" data-user-id="${player.id}">
                            <span class="player-name">${player.username}</span>
                            ${!player.active ? '<i class="fas fa-plus add-icon"></i>' : '<i class="fas fa-times-circle delete-icon"></i>'}
                        </div>
                    `);

                    // Add click event for the delete icon
                    playerCard.find('.delete-icon').on('click', function() {
                        deletePlayerFromTournament(player.id);
                    });

                    playerCard.find('.add-icon').on('click', function() {
                        reinstatePlayerInTournament(player.id);
                    });

                    $('#players-container').append(playerCard);
                });
                $('#number-of-players').html($('.player-card').length);
            } else {
                $('#number-of-players').html(0);
            }
        },
        error: function() {
            console.log('Error fetching players');
        }
    });
}

function updateTournamentInfo() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    const tournamentName = $('#tournament-name-edit').val();
    const tournamentDate = $('#tournament-date-edit').val();
    let tournamentLogo = '';

    if ($('#tournament-logo-edit').length && $('#tournament-logo-edit')[0].files && $('#tournament-logo-edit')[0].files[0]) {
        tournamentLogo = $('#tournament-logo-edit')[0].files[0];
    }

    const formData = new FormData();
        formData.append('id', tournamentId);
        formData.append('tournamentName', tournamentName);
        formData.append('tournamentDate', tournamentDate);
        formData.append('tournamentLogo', tournamentLogo);

    $.ajax({
        url: '/scripts/update_tournaments_info.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            loadTounamentInfo();
            $('#tournament-info').show();
            $('#tournament-info-form').hide();
        }
    });
}

function searchGame(query) {
    $.ajax({
        url: '/scripts/search_game.php',
        method: 'GET',
        data: { query: query },
        dataType: 'json',
        success: function(response) {
            if (response.results) {
                const filteredResults = response.results.map(game => ({
                    name: game.name,
                    description: game.description_raw || 'No description available',
                    year: game.released ? new Date(game.released).getFullYear() : 'Unknown',
                    platforms: game.platforms.map(p => p.platform.name),
                    image: game.background_image,
                    slug: game.slug
                }));

                // Clear the container first
                $('#add-games-container').empty();
    
                // Loop through each game result and create the card
                filteredResults.forEach(function(game) {
                    let gameCard = `
                        <div class="add-game-info">
                            <div class="add-game-header">
                                <img id="add-game-image-${game.slug}" class="add-game-image" src="${game.image || '/images/game-placeholder.jpg'}" alt="Game Image">
                                <button class="add-game-form-btn" onclick="addGameToTournament('${game.slug}')">
                                    <i class="fas fa-add"></i> Add Game
                                </button>
                            </div>
                            <p><strong>Name:</strong> <span id="add-game-name-${game.slug}">${game.name}</span></p>
                            <p><strong>Release Year:</strong> <span id="add-game-year-${game.slug}">${game.year}</span></p>
                            <p><strong>Platforms:</strong> <span id="add-game-platforms-${game.slug}">${game.platforms.join(', ')}</span></p>
                        </div>
                    `;
    
                    // Append the card to the container
                    $('#add-games-container').append(gameCard);
                });
            } else {
                // Handle no results
                $('#add-games-container').html('<p>No games found.</p>');
            }
        },
        error: function() {
            console.error('Error with the request');
        }
    });
}

function editTournamentInfo() {
    $('#tournament-info').hide();
    $('#tournament-info-form').show();
}

function changePage(button_id, id) {
    $('.manage-selection').removeClass('active');
    $('#'+button_id).addClass('active');
    $('.tournament-panel').hide();

    $('#'+id).show();
}

function scanQRCode() {
    $('#tournament-players').hide();
    $('#tournament-players-form').show();
    const qrCodeScanner = new Html5Qrcode("qr-reader");
    qrCodeScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, 
    qrCodeMessage => {
        alert("QR Code Scanned: " + qrCodeMessage);
        qrCodeScanner.stop();
    },
    errorMessage => {
        console.log("Scanning error:", errorMessage);
    });
}

function addGame() {
    $('#add-games-container').empty();
    $('#search-game').val('');
    $('#tournament-games').hide();
    $('#tournament-game-form').show();
}

function addGameToTournament(game_slug) {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];

    $.ajax({
        url: '/scripts/add_game_to_tournament.php',
        method: 'POST',
        dataType: 'json',
        contentType: 'application/json',  // Set content type to JSON
        data: JSON.stringify({
            gameSlug: game_slug,
            gameName: $(`#add-game-name-${game_slug}`).html(),
            gameImage: $(`#add-game-image-${game_slug}`).attr('src'),
            gameYear: $(`#add-game-year-${game_slug}`).html(),
            gamePlatforms: $(`#add-game-platforms-${game_slug}`).html(),
            tournamentId: tournamentId
        }),
        success: function(response) {
            if (response.success) {
                loadTournamentGames();
                $('#tournament-games').show();
                $('#tournament-game-form').hide();
            } else {
                console.error(response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
}

function loadTournamentGames() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    $('#tournament-games-list').empty();
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
    let gameCardHtml = '';
    const gameImage = game.game_image_url || '\\images\\game-placeholder.jpg';

    if (game.status === 2) {  // Completed game
        gameCardHtml = `
            <div class="game-card">
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
                            <span>${game.winner_name}</span>
                        </div>
                    </div>
                </div>
            </div>`;
    } else if (game.status === 1) {  // In-progress game
        if (game.type === 'points') {
            gameCardHtml = `
                <div class="game-card">
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
                        <div class="manage-game-buttons">
                            <button class="success-btn small-font auto-width" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="openPointsPage(this)">Add Points</button>
                        </div>
                    </div>
                </div>`;
        } else if (game.type === 'bracket') {
            gameCardHtml = `
                <div class="game-card">
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
                        <div class="manage-game-buttons">
                            <button class="edit-btn small-font auto-width" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="openBracket(this)">Add Winners</button>
                            ${game.winner_name ? `<button class="success-btn small-font auto-width" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="finishGame(this)">FINISH GAME</button>` : ''}
                        </div>
                    </div>
                </div>`;
        }
    } else if (game.status === 0) {  // Not started
        if (game.type && (game.type !== 'bracket' || (game.team_size && game.teams_per_match && game.winners_per_match))) {
            gameCardHtml = `
                <div class="game-card">
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
        } else {
            gameCardHtml = `
                <div class="game-card">
                    <img src="${gameImage}" alt="Game Image" class="game-image">
                    <div class="game-info">
                        <span class="game-name">${game.game_name}</span>
                        <div class="winner-info">
                            <span>Not Yet Configured</span>
                        </div>
                        <div class="manage-game-buttons">
                            <button class="edit-btn small-font auto-width edit-game-button" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="configureGame(this)">Edit Game</button>
                        </div>
                    </div>
                </div>`;
        }
    }

    // Append to the tournament-games-list container
    $('#tournament-games-list').append(gameCardHtml);
}

function configureGame(button){
    const tournamentGame = JSON.parse(button.getAttribute('data-game'));
    const teamsSize = tournamentGame.team_size === 0 ? null : tournamentGame.team_size;
    const teamsPerMatch = tournamentGame.teams_per_match === 0 ? null : tournamentGame.teams_per_match;
    const winnersPerMatch = tournamentGame.winners_per_match === 0 ? null : tournamentGame.winners_per_match;

    if (tournamentGame.type === 'bracket') {
        $('#bracket-fields').removeClass('hidden');
        $('#points-fields').addClass('hidden');
    } else if (tournamentGame.type === 'points') {
        $('#bracket-fields').addClass('hidden');
        $('#points-fields').removeClass('hidden');
    } else {
        $('#bracket-fields').addClass('hidden');
        $('#points-fields').addClass('hidden');
    }

    $('#configure-game-name').val(tournamentGame.game_name);
    $('#configure-game-type').val(tournamentGame.type);
    $('#configure-winners-per-match').val(winnersPerMatch);
    $('#configure-teams-per-match').val(teamsPerMatch)
    $('#configure-team-size').val(teamsSize)

    $('#submit-configure-game').attr({'data-game-id': tournamentGame.id});
    $('#delete-game').attr({'data-game-id': tournamentGame.id});

    $('#tournament-games').hide();
    $('#configure-game').show();
};

function saveGameConfiguration(button) {
    const formData = new FormData();
    const tournamentGameId = JSON.parse(button.getAttribute('data-game-id'));
    const gameName = $('#configure-game-name').val();
    const gameType = $('#configure-game-type').val();

    if (gameName === '' || gameType === '') {
        showBanner('#invalid-configuration-banner');
        return;
    }

    if (gameType === 'bracket') {
        const sizeOfTeams = $('#configure-team-size').val();
        const teamsPerMatch = $('#configure-teams-per-match').val();
        const winnersPerMatch = $('#configure-winners-per-match').val();

        if (
            !isValidConfiguration(sizeOfTeams, teamsPerMatch, winnersPerMatch)
        ) {
            showBanner('#invalid-configuration-banner');
            return false;
        }

        formData.append('sizeOfTeams', sizeOfTeams);
        formData.append('teamsPerMatch', teamsPerMatch);
        formData.append('winnersPerMatch', winnersPerMatch);
    }

    if (gameType === 'points') {
        const sizeOfTeams = $('#configure-team-size-points').val();
        formData.append('sizeOfTeams', sizeOfTeams);
    }

    formData.append('id', tournamentGameId);
    formData.append('gameName', gameName);
    formData.append('gameType', gameType);

    $.ajax({
        url: '/scripts/update_tournament_game.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            showBanner('#game-configured-banner');
            loadTournamentGames();
            resetConfigFields();
            $('#configure-game').hide();
            $('#tournament-games').show();
        }
    });


};

function isValidConfiguration(teamSize, teamsPerMatch, winnersPerMatch) {
    let isValid = true;

    if (teamSize <= 0) {
        $('#invalid-configuration-banner').html("'teamSize' must be greater than 0.");
        console.log("Invalid configuration: 'teamSize' must be greater than 0.");
        isValid = false;
    }

    if (teamsPerMatch <= 1) {
        $('#invalid-configuration-banner').html("'teamsPerMatch' must be greater than 1.");
        console.log("Invalid configuration: 'teamsPerMatch' must be greater than 1.");
        isValid = false;
    }

    if (winnersPerMatch <= 0) {
        $('#invalid-configuration-banner').html("'winnersPerMatch' must be greater than 0.");
        console.log("Invalid configuration: 'winnersPerMatch' must be greater than 0.");
        isValid = false;
    }

    if (winnersPerMatch >= teamsPerMatch) {
        $('#invalid-configuration-banner').html("'winnersPerMatch' must be less than 'teamsPerMatch'");
        console.log("Invalid configuration: 'winnersPerMatch' must be less than 'teamsPerMatch'.");
        isValid = false;
    }

    if (teamsPerMatch % winnersPerMatch !== 0) {
        $('#invalid-configuration-banner').html("'teamsPerMatch' must be divisible by 'winnersPerMatch'");
        console.log("Invalid configuration: 'teamsPerMatch' must be divisible by 'winnersPerMatch' to ensure consistent bracket advancement.");
        isValid = false;
    }

    return isValid;
}

function resetConfigFields() {
    $('#configure-game-name').val('');
    $('#configure-game-type').val('');
    $('#configure-team-size').val('');
    $('#configure-team-size-points').val('');
    $('#configure-teams-per-match').val('');
    $('#configure-winners-per-match').val('');
    $('#bracket-fields').addClass('hidden');
    $('#points-fields').addClass('hidden');
}

function showBanner(bannerId) {
    // Hide any currently visible banners
    $('.notification-banner').hide();

    // Show the specified banner and animate it
    $(bannerId)
        .css({ opacity: 0, visibility: 'visible' })
        .show()
        .animate({ opacity: 1 }, 300);

    // Automatically hide the banner after a delay
    setTimeout(function () {
        $(bannerId).animate({ opacity: 0 }, 300, function () {
            $(this).css('visibility', 'hidden').hide();
        });
    }, 3000); // Hide after 3 seconds
}

function deleteGame(button) {
    const tournamentGameId = JSON.parse(button.getAttribute('data-game-id'));
    const formData = new FormData();
    formData.append('id', tournamentGameId);
    $.ajax({
        url: '/scripts/delete_tournament_game.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            showBanner('#game-deleted-banner');
            loadTournamentGames();
            resetConfigFields();
            $('#configure-game').hide();
            $('#tournament-games').show();
        }
    });
}

function confirmStartGame(button) {
    const tournamentGame = JSON.parse(button.getAttribute('data-game'));
    $('#confirm-start-game-name').html(tournamentGame.game_name);

    $('#start-game-button').attr('data-game', JSON.stringify(tournamentGame));
    $('#start-game-confirm').show();
    $('#tournament-games').hide();
}

function startGame(button) {
    const tournamentGame = JSON.parse(button.getAttribute('data-game'));
    console.log(tournamentGame);

    $.ajax({
        url: '/scripts/get_tournament_active_players.php',
        method: 'GET',
        dataType: 'json',
        data: { id: tournamentGame.tournament_id },
        success: function(response) {
            if (tournamentGame.type === 'bracket') {
                $numberOfTeams = response.activePlayers.length / tournamentGame.team_size;
                $playersNeeded = (tournamentGame.teams_per_match/tournamentGame.winners_per_match) * tournamentGame.teams_per_match;
                if($playersNeeded > $numberOfTeams){
                    console.log("Not enough Players");
                    $('#invalid-configuration-banner').html("Not enough players for this configuration");
                    showBanner('#invalid-configuration-banner');
                    return;
                }
            }

            const formData = new FormData();
            formData.append('tournament_id', tournamentGame.tournament_id);
            formData.append('tournament_game_id', tournamentGame.id);
            formData.append('players_per_team', tournamentGame.team_size);
            formData.append('status', 1);

            if (tournamentGame.type === 'bracket') {
                $.ajax({
                    url: '/scripts/generate_teams.php',
                    method: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $.ajax({
                            url: '/scripts/generate_bracket.php',
                            method: 'POST',
                            dataType: 'json',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                // console.log(response);
                            }
                        });
                    }
                });
            }

            $.ajax({
                url: '/scripts/generate_teams.php',
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $.ajax({
                        url: '/scripts/generate_points.php',
                        method: 'POST',
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $.ajax({
                                url: '/scripts/update_tournament_game_status.php',
                                method: 'POST',
                                dataType: 'json',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success) {
                                        loadTournamentGames();
                                        $('#start-game-confirm').hide();
                                        $('#tournament-games').show();
                                        showBanner('#game-started-banner');
                                    }
                                }
                            });
                        }
                    });
                }
            });
        },
        error: function() {
            console.error("Failed to fetch bracket data.");
        }
    });

}

function openBracket(button) {
    const tournamentGame = JSON.parse(button.getAttribute('data-game'));
    $('#tournamentGameInfo').val(button.getAttribute('data-game'));
    console.log(tournamentGame);

    $.ajax({
        url: '/scripts/get_bracket.php',
        method: 'GET',
        dataType: 'json',
        data: { gameId: tournamentGame.id },
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

    const bracketContainer = $('#add-winners-container');
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
            const bracketGroup = $('<div>', { class: 'bracket-group', onclick: `openBracketGroup(${roundIndex}, ${matchIndex}, this)` });

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

function cancelStartGame() {
    $('#start-game-confirm').hide();
    $('#tournament-games').show();
}

let selectedWinners = [];
let roundNumber = 0;
let groupNumber = 0;

function openBracketGroup(round, group, button) {
    $('.winning-group').removeClass('winning-group');
    const tournamentGame = JSON.parse($('#tournamentGameInfo').val());
    roundNumber = round;
    groupNumber = group;
    selectedWinners = [];
    $('#confirm-winner').hide();
    
    // Fetch players in the specified round and match group
    $.ajax({
        url: '/scripts/get_bracket_group.php',
        method: 'POST',
        data: { round: round, match_number: groupNumber, tournamentGameId: tournamentGame.id },
        dataType: 'json',
        success: function(players) {
            $('#add-winners').hide();
            $('#add-winners-bracket-group').show();
            // Clear the container first
            $('#bracket-group-container').empty();

            // Generate bracket cards for each team
            Object.keys(players).forEach(teamId => {
                const team = players[teamId]; // Get team data
                const teamNumber = team.team_number; // Extract team number
                const playersByPosition = team.players; // Players grouped by position

                // Construct the HTML for players within the team
                let playerNamesHTML = '';
                Object.keys(playersByPosition).forEach(position => {
                    const positionPlayers = playersByPosition[position];
                    positionPlayers.forEach(player => {
                        playerNamesHTML += `<span class="player-name-double">${player.player_name || '--'}</span>`;
                    });
                });

                // Create the bracket card for the team
                const bracketCard = $(`
                    <div class="bracket-card" onclick="selectWinner(this)" data-match='${JSON.stringify({id:parseInt(teamId),round:round,match: groupNumber})}'>
                        <div class="double-player-container">
                            ${playerNamesHTML}
                        </div>
                    </div>
                `);

                // Append the bracket card to the container
                $('#bracket-group-container').append(bracketCard);
            });

        }
    });
}


// Manage selected winners based on tournament settings


function selectWinner(element) {
    const teamMatchData = JSON.parse(element.getAttribute('data-match'));
    const tournamentGameInfo = JSON.parse($('#tournamentGameInfo').val());
    const teamNumber = teamMatchData.id;
    const round = teamMatchData.round;
    const maxWinners = round === 0 ? 1 : tournamentGameInfo.winners_per_match;

    console.log(teamMatchData);

    if ($(element).hasClass('winning-group')) {
        // Deselect the player
        $(element).removeClass('winning-group');
        selectedWinners = selectedWinners.filter(id => id !== teamNumber);
    } else if (selectedWinners.length < maxWinners) {
        // Select the player if under the max winner limit
        $(element).addClass('winning-group');
        selectedWinners.push(teamNumber);
    }

    // Show or hide the confirm button based on selection count
    $('#confirm-winner').toggle(selectedWinners.length === maxWinners);
}

function confirmWinner() {
    const tournamentGameInfo = JSON.parse($('#tournamentGameInfo').val());
    const formData = new FormData();
    formData.append('tournamentGameId', tournamentGameInfo.id);
    formData.append('round', roundNumber);
    formData.append('match_number', groupNumber);
    formData.append('winners', JSON.stringify(selectedWinners));

    $.ajax({
        url: '/scripts/update_bracket_results.php',  // Script to save the results
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log(response); // Log for debugging
            $('#add-winners-bracket-group').hide();
            $('#add-winners').show();

            loadBracket();
        }
    });
}

function loadBracket() {
    const tournamentGameInfo = JSON.parse($('#tournamentGameInfo').val());

    $.ajax({
        url: '/scripts/get_bracket.php',
        method: 'GET',
        dataType: 'json',
        data: { gameId: tournamentGameInfo.id },
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


function finishGame(button){
    const gameData = JSON.parse(button.getAttribute('data-game'));
    const formData = new FormData();
    formData.append('tournament_game_id', gameData.id);
    formData.append('status', 2);
    $.ajax({
        url: '/scripts/generate_points.php',
        method: 'POST',
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $.ajax({
                url: '/scripts/update_tournament_game_status.php',
                method: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        loadTournamentGames();
                        showBanner('#game-finished-banner');
                    }
                }
            });
        }
    });
}

function openPointsPage(button) {
    $('#tournamentGameInfo').val(button.getAttribute('data-game'));
    $('#tournament-games').hide();
    $('#add-points').show();
    loadPointsList();
}

function loadPointsList() {
    const tournamentGame = JSON.parse($('#tournamentGameInfo').val());
    $.ajax({
        url: '/scripts/get_points_list.php',
        method: 'GET',
        dataType: 'json',
        data: { tournament_game_id: tournamentGame.id },
        success: function(response) {
            if (response.success) {
                renderPointsList(response.data, tournamentGame.id);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error("Failed to fetch leaderboard data.");
        }
    });
}

function renderPointsList(leaderboardData, tournamentGameId) {
    const leaderboardContainer = $('#leaderboard-container');
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
                } margin-bottom-sm" onclick="addPoints(${team.team_id}, ${tournamentGameId}, ${team.points})">
                <div>
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


function addPoints(teamId, tournamentGameId, points) {
    // Fetch team players by teamId
    $.ajax({
        url: '/scripts/get_team_players.php',  // Assume this script fetches players by team ID
        method: 'GET',
        dataType: 'json',
        data: { teamId: teamId },
        success: function(response) {
            if (response.success) {
                const playerNamesContainer = $('#points-input-players'); // Clear previous players
                playerNamesContainer.empty();

                // Populate players in add-points-player
                response.players.forEach(player => {
                    playerNamesContainer.append(`
                        <span class="player-name">${player.username}</span>
                    `);
                });
                playerNamesContainer.append(`
                    <div class="form-group margin-top">
                        <label for="points-input">Number of Points:</label>
                        <input type="number" id="points-input-${teamId}" class="points-input" name="points-input" placeholder="0" value="${points}" required>
                    </div>
                    <button id="confirm-points" class="success-btn" onclick="confirmPoints(${teamId}, ${tournamentGameId})">Confirm Points</buttonid>`
                );
                $('#add-points').hide();
                $('#add-points-player').show();
            }
        },
        error: function() {
            console.error("Failed to fetch team players.");
        }
    });
}


function confirmPoints(teamId, tournamentGameId) {
    const points = $(`#points-input-${teamId}`).val();
    
    $.ajax({
        url: '/scripts/save_points.php',
        method: 'POST',
        dataType: 'json',
        data: { points: points, teamId: teamId, tournamentGameId: tournamentGameId },
        success: function(response) {
            if (response.success) {
                loadPointsList();
                $('#points-input').val(''); // Clear input fields
                $('#add-points').show();    // Show the main points screen
                $('#add-points-player').hide();  // Hide the input screen
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error("Failed to save points.");
        }
    });
}
