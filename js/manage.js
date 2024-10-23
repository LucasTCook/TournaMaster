$(document).ready(function() {
    $('#tournament-info').show();
    $('#create-tournament-button').on('click', function() {
        // Simulate a save process
        let saveSuccess = true; // Change this based on actual save outcome

        if (saveSuccess) {
            $('#add-tournament').hide();
            $('#current-tournaments').show();
            showBanner('#save-banner');
        } else {
            showBanner('#save-error-banner');
        }
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

});

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