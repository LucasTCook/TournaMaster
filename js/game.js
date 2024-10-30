$(document).ready(function() {
    loadGame();
});

function loadGame() {
    const urlPath = window.location.pathname.split('/');
    const tournamentGameId = urlPath[urlPath.length - 1];
    const tournamentId = urlPath[urlPath.length - 2];
    
    // Fetch game details
    $.ajax({
        url: `/scripts/get_game_details.php`,
        method: 'GET',
        data: { tournamentGameId: tournamentGameId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#game-header-container').empty();
                const game = response.data;
                
                // Create HTML structure
                let gameHtml = `
                    <div class="back-to-games" onclick="window.location.href='/tournament/${game.tournament_id}'"><</div>
                    <img src="${game.game_image_url || '/images/game-placeholder.jpg'}" alt="Game Picture" class="game-pic">
                    <h2 class="game-name">${game.game_name}</h2>
                    <div class="games-header-buttons">`;

                // Add bracket button only if type is 'bracket'
                if (game.type === 'bracket') {
                    gameHtml += `
                        <div id="game-bracket-button" class="games-selection active">
                            <span class="games-selection-title">View Bracket</span>
                        </div>`;
                }

                // Always show leaderboard button
                gameHtml += `
                    <div id="game-leaderboard-button" class="games-selection">
                        <span class="games-selection-title">Leaderboard</span>
                    </div>
                </div>`;

                // Inject the HTML into the page
                $('#game-header-container').html(gameHtml);

                if (game.type === 'bracket') {
                    $('#game-bracket-button').click(function(event) {
                        $('#game-bracket-button').addClass('active');
                        $('#bracket').removeClass('hidden');
                        $('#game-leaderboard-button').removeClass('active');
                        $('#leaderboard').addClass('hidden');
                    });
                } else {
                    $('#bracket').addClass('hidden');
                    $('#leaderboard').removeClass('hidden');
                }

                $('#game-leaderboard-button').click(function(event) {
                    $('#game-bracket-button').removeClass('active');
                    $('#bracket').addClass('hidden');
                    $('#game-leaderboard-button').addClass('active');
                    $('#leaderboard').removeClass('hidden');
                });

            } else {
                console.error("Failed to load game details.");
            }
        },
        error: function(error) {
            console.error("Error fetching game details:", error);
        }
    });
}
