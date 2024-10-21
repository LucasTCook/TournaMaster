$(document).ready(function() {
    $('#leaderboard-button').click(function(event) {
        // $('#games').hide();
        $('#games-button').removeClass('active');
        $('#games').addClass('hidden');
        // $('#leaderboard').show();
        $('#leaderboard-button').addClass('active');
        $('#leaderboard').removeClass('hidden');
    });

    $('#games-button').click(function(event) {
        // $('#games').show();
        $('#games-button').addClass('active');
        $('#games').removeClass('hidden');
        // $('#leaderboard').hide();
        $('#leaderboard-button').removeClass('active');
        $('#leaderboard').addClass('hidden');
    });
});