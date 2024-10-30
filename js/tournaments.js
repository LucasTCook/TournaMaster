$(document).ready(function() {
    getTournaments('current');
    $('#current-button').click(function(event) {
        getTournaments('current');
        $('#past-button').removeClass('active');
        $('#past-tournaments').addClass('hidden');
        $('#current-button').addClass('active');
        $('#current-tournaments').removeClass('hidden');
    });

    $('#past-button').click(function(event) {
        getTournaments('past');
        $('#past-button').addClass('active');
        $('#past-tournaments').removeClass('hidden');
        $('#current-button').removeClass('active');
        $('#current-tournaments').addClass('hidden');
    });
});

function getTournaments(type) {
    $.ajax({
        url: `/scripts/get_user_tournaments.php`,
        method: 'GET',
        dataType: 'json',
        data: { type: type },
        success: function(response) {
            if (response.success) {
                renderTournaments(response.tournaments, type);
            } else {
                console.error(response.error);
            }
        },
        error: function() {
            console.error('Failed to fetch tournaments');
        }
    });
}

// Function to render tournaments in the appropriate container
function renderTournaments(tournaments, type) {
    // Select container based on tournament type
    const containerId = type === 'current' ? '#current-tournaments' : '#past-tournaments';
    const container = $(containerId);
    container.empty(); // Clear previous content

    if (tournaments.length === 0) {
        return;
    }

    // Loop through each tournament and create its card
    tournaments.forEach(tournament => {
        const tournamentCardHtml = `
            <div class="tournament-card" onclick="window.location.href='./tournament/${tournament.id}'">
                <span class="tournament-name">${tournament.tournament_name}</span>
                <br>
                <span class="tournament-info">${tournament.tournament_creator}</span> -
                <span class="tournament-info date">${tournament.date}</span>
            </div>
        `;
        container.append(tournamentCardHtml);
    });
}
