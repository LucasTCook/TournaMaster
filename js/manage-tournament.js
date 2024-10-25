
$(document).ready(function() {
    loadTounamentInfo();
    $('#tournament-info').show();

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
            $('#tournament-date').html(response.data.date);
            $('#tournament-name-edit').val(response.data.name);
            $('#tournament-date-edit').val(response.data.date);
            if (response.data.logo !== ''){
                $('#info-tournament-logo').attr('src', '/images/uploads/tournament_logos/'+response.data.logo)
            }
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