$(document).ready(function() {
    const urlPath = window.location.pathname.split('/');
    const tournamentId = urlPath[urlPath.length - 1];
    autoUpdateLeaderboard(tournamentId);
});

function loadTournamentResults(tournamentId) {
    $.ajax({
        url: '/scripts/get_leaderboard.php',
        method: 'GET',
        data: { tournament_id: tournamentId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderTournamentResultsTable(response.data);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error("Failed to fetch tournament results.");
        }
    });
}

function renderTournamentResultsTable(data) {
    const games = data.games;
    const players = data.players;
    const table = $(".tournament-results-table");

    // Clear table headers and body
    table.find("thead tr").empty().append('<th></th>');
    table.find("tbody").empty();

    // Add headers for each game
    games.forEach(game => {
        const gameHeader = `
            <th>
                <div class="game-header text-wrap small-font">
                    <img src="${game.game_image_url}" alt="${game.name}" class="game-logo">
                </div>
            </th>
        `;
        table.find("thead tr").append(gameHeader);
    });
    table.find("thead tr").append('<th>Total Points</th>');

    // Render each player row with their points
    players.forEach(player => {
        const playerRow = $('<tr>');
        playerRow.append(`<td class="bold">${player.username}</td>`);

        // Insert points for each game
        games.forEach(game => {
            playerRow.append(`<td>${player.games[game.id] || '-'}</td>`);
        });

        playerRow.append(`<td class="bold">${player.total_points}</td>`);
        table.find("tbody").append(playerRow);
    });
}

function autoUpdateLeaderboard(tournamentId) {
    loadTournamentResults(tournamentId);
    setInterval(() => loadTournamentResults(tournamentId), 30000);  // Update every 30 seconds
}

