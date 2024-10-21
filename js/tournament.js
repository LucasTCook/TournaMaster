$(document).ready(function() {
    $('#leaderboard-button').click(function(event) {
        $('#games-button').removeClass('active');
        $('#games').addClass('hidden');
        $('#leaderboard-button').addClass('active');
        $('#leaderboard').removeClass('hidden');
    });

    $('#games-button').click(function(event) {
        $('#games-button').addClass('active');
        $('#games').removeClass('hidden');
        $('#leaderboard-button').removeClass('active');
        $('#leaderboard').addClass('hidden');
    });
});