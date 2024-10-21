$(document).ready(function() {
    $('#game-leaderboard-button').click(function(event) {
        $('#game-bracket-button').removeClass('active');
        $('#bracket').addClass('hidden');
        $('#game-leaderboard-button').addClass('active');
        $('#leaderboard').removeClass('hidden');
    });

    $('#game-bracket-button').click(function(event) {
        $('#game-bracket-button').addClass('active');
        $('#bracket').removeClass('hidden');
        $('#game-leaderboard-button').removeClass('active');
        $('#leaderboard').addClass('hidden');
    });
});