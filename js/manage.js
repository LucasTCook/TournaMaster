$(document).ready(function() {
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

});

function cancelTournamentCreation() {
    $('#tournament-name').val('');
    $('#tournament-date').val('');
    $('#tournament-logo').val('');

    $('#add-tournament').hide();
    $('#current-tournaments').show();
}