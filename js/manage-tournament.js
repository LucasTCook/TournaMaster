
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

    $('#start-game-button').on('click', function() {
        // Simulate a save process
        let saveSuccess = true; // Change this based on actual save outcome

        if (saveSuccess) {
            $('#start-game-confirm').hide();
            $('#tournament-games').show();
            showBanner('#game-started-banner');
        } else {
            // showBanner('#save-error-banner');
        }
    });

    $('#configure-game-type').on('change', function() {
        if ($(this).val() === 'bracket') {
            $('#bracket-fields').removeClass('hidden');
        } else {
            $('#bracket-fields').addClass('hidden');
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

});

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
                            ${!player.active ? '' : '<i class="fas fa-times-circle delete-icon"></i>'}
                        </div>
                    `);

                    // Add click event for the delete icon
                    playerCard.find('.delete-icon').on('click', function() {
                        deletePlayerFromTournament(player.id);
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
                    <div class="winner-info">
                        <span>${game.winner_name}</span>
                        <i class="fas fa-trophy winner-trophy"></i>
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
                        <div class="manage-game-buttons">
                            <button class="success-btn small-font auto-width" onclick="openAddPoints()">Add Points</button>
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
                        <div class="manage-game-buttons">
                            <button class="success-btn small-font auto-width" onclick="openAddWinners()">Add Winners</button>
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
                        <div class="manage-game-buttons">
                            <button class="edit-btn small-font auto-width edit-game-button" data-game='${JSON.stringify(game).replace(/'/g, "&apos;")}' onclick="configureGame(this)">Edit Game</button>
                            <button class="success-btn small-font auto-width" onclick="confirmStartGame()">START GAME</button>
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
    console.log([gameName,gameType]);
    if (gameName === '' || gameType === '') {
        showBanner('#invalid-configuration-banner');
        return;
    }

    if (gameType === 'bracket') {
        const sizeOfTeams = $('#configure-team-size').val();
        const teamsPerMatch = $('#configure-teams-per-match').val();
        const winnersPerMatch = $('#configure-winners-per-match').val();

        if (sizeOfTeams <= 0 || teamsPerMatch <= 0 || winnersPerMatch <= 0) {
            showBanner('#invalid-configuration-banner');
            return;
        }

        formData.append('sizeOfTeams', sizeOfTeams);
        formData.append('teamsPerMatch', teamsPerMatch);
        formData.append('winnersPerMatch', winnersPerMatch);
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

function resetConfigFields() {
    $('#configure-game-name').val('');
    $('#configure-game-type').val('');
    $('#configure-team-size').val('');
    $('#configure-teams-per-match').val('');
    $('#configure-winners-per-match').val('');
    $('#bracket-fields').addClass('hidden');
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

function confirmStartGame() {
    $('#start-game-confirm').show();
    $('#tournament-games').hide();
}

function cancelStartGame() {
    $('#start-game-confirm').hide();
    $('#tournament-games').show();
}

function openAddWinners() {
    $('#tournament-games').hide();
    $('#add-winners').show();
}

function openBracketGroup(groupNumber) {
    $('.winning-group').removeClass('winning-group');

    $('#add-winners').hide();
    $('#add-winners-bracket-group').show();
    $('#confirm-winner').hide();
}

function selectWinner(winningGroup) {
    $('.winning-group').removeClass('winning-group');
    $(winningGroup).addClass('winning-group');

    $('#confirm-winner').show();
}

function confirmWinner() {
    $('#add-winners').show();
    $('#add-winners-bracket-group').hide();
}

function openAddPoints() {
    $('#tournament-games').hide();
    $('#add-points').show();
}

function addPoints() {
    $('#add-points').hide();
    $('#add-points-player').show();
}

function confirmPoints() {
    $('#points-input').val('');
    $('#add-points').show();
    $('#add-points-player').hide();
}