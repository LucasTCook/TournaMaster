$(document).ready(function() {
    loadTournaments();
    $('#tournament-info').show();
    $('#create-tournament-button').on('click', function (e) {
        e.preventDefault();
    
        const tournamentName = $('#tournament-name').val();
        const tournamentDate = $('#tournament-date').val();
        const tournamentLogo = $('#tournament-logo')[0].files[0];
    
        // Basic validation
        if (!tournamentName || !tournamentDate) {
            alert('Please fill out name and date.');
            return;
        }
    
        // Create FormData object to handle file uploads
        const formData = new FormData();
        formData.append('tournamentName', tournamentName);
        formData.append('tournamentDate', tournamentDate);
        formData.append('tournamentLogo', tournamentLogo);
    
        $.ajax({
            url: '/scripts/create_tournament.php',
            method: 'POST',
            dataType: 'json',
            data: formData, // Use FormData object
            processData: false, // Prevent jQuery from converting the data
            contentType: false, // Prevent jQuery from setting contentType
            success: function (response) {
                if (response.status === 'success') {
                    $('#add-tournament').hide();
                    $('#current-tournaments').fadeIn();
                    showBanner('#save-banner');
                    $('#tournament-name').val('');
                    $('#tournament-date').val('');
                    $('#tournament-logo').val('');
                    loadTournaments();
                } else {
                    showBanner('#save-error-banner');
                }
            },
            error: function () {
                showBanner('#save-error-banner');
            }
        });
    });

    function showBanner(selector) {
        $(selector).addClass('show');
        setTimeout(function() {
            $(selector).removeClass('show');
        }, 3000); // Hide after 3 seconds
    }

    $('#add-tournament-button').on('click', function() {
        $('#add-tournament').show();
        $('#current-tournaments').hide();
    });

    $('#number-of-players').html($('.player-card').length);

    let scanner = new Instascan.Scanner({ video: document.getElementById('QR-preview') });
    scanner.addListener('scan', function (content) {
        alert('QR Code Scanned: ' + content);
    });

    $('#add-player').on('click', function() {
        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert('No cameras found.');
            }
        }).catch(function (e) {
            console.error(e);
        });
    });

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

    $('#game-type').on('change', function() {
        if ($(this).val() === 'bracket') {
            $('#bracket-fields').removeClass('hidden');
        } else {
            $('#bracket-fields').addClass('hidden');
        }
    });
    
    $('.edit-game-button').on('click', function() {
        $('#tournament-games').hide();
        $('#configure-game').show();
    });

    $('.save-configuration').on('click', function() {
        showBanner('#game-configured-banner');
        $('#configure-game').hide();
        $('#tournament-games').show();
    });
});

function loadTournaments() {
    $.ajax({
        url: '/scripts/get_tournaments.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderTournaments(response.data);
            } else {
                showError(response.message || 'Failed to load tournaments.');
            }
        },
        error: function() {
            showError('An error occurred while fetching tournaments.');
        }
    });
}

function renderTournaments(tournaments) {
    const container = $('#current-tournaments');
    container.empty();
    
    if (tournaments.length > 0) {
        tournaments.forEach(function(tournament) {
            const tournamentItem = `<div class="tournament-item">
                <h3>${tournament.name}</h3>
                <p>${tournament.date}</p>
            </div>`;

            const tournamentCard = `
                <div class="tournament-card" onclick="window.location.href='./manage-tournament/${tournament.id}'">
                    <div class="tournament-logo-container">
                        <img src="/images/uploads/tournament_logos/${tournament.logo}" alt="Tournament Logo" class="tournament-logo">
                    </div>
                    <div>
                        <span class="tournament-name">${tournament.name}</span>
                        <br>
                        <span class="tournament-info">${tournament.creator_name}</span>
                        <br>
                        <span class="tournament-info date">${tournament.date}</span>
                        <br>
                        <span class="tournament-info">${tournament.games_count} Games</span>
                    </div>
                </div>
            `;
            container.append(tournamentCard);
        });
    } else {
        container.append('<p>No tournaments found.</p>');
    }
}

function cancelTournamentCreation() {
    $('#tournament-name').val('');
    $('#tournament-date').val('');
    $('#tournament-logo').val('');

    $('#add-tournament').hide();
    $('#current-tournaments').show();
}

function editTournamentInfo() {
    $('#tournament-info').hide();
    $('#tournament-info-form').show();
}

function saveTournamentInfo() {
    $('#tournament-info').show();
    $('#tournament-info-form').hide();
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
    $('#tournament-games').hide();
    $('#tournament-game-form').show();
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