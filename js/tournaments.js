$(document).ready(function() {
    $('#current-button').click(function(event) {
        $('#past-button').removeClass('active');
        $('#past-tournaments').addClass('hidden');
        $('#current-button').addClass('active');
        $('#current-tournaments').removeClass('hidden');
    });

    $('#past-button').click(function(event) {
        $('#past-button').addClass('active');
        $('#past-tournaments').removeClass('hidden');
        $('#current-button').removeClass('active');
        $('#current-tournaments').addClass('hidden');
    });
});