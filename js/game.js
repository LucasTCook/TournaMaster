$(document).ready(function() {
    $('#game-leaderboard-button').click(function(event) {
        $('#bracket').hide();
        $('#leaderboard').show();
    });

    $('#game-bracket-button').click(function(event) {
        $('#bracket').show();
        $('#leaderboard').hide();
    });
});