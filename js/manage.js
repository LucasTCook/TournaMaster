$(document).ready(function() {
    loadTournaments();
    $('#create-tournament-button').on('click', function (e) {
        e.preventDefault();
    
        const tournamentName = $('#tournament-name').val();
        const tournamentDate = $('#tournament-date').val();
        const tournamentLogo = $('#tournament-logo')[0].files[0];
    
        // Basic validation
        if (!tournamentName || !tournamentDate) {
            alert('Please fill out name and date.');
            return;
        }
    
        // Create FormData object to handle file uploads
        const formData = new FormData();
        formData.append('tournamentName', tournamentName);
        formData.append('tournamentDate', tournamentDate);
        formData.append('tournamentLogo', tournamentLogo);
    
        $.ajax({
            url: '/scripts/create_tournament.php',
            method: 'POST',
            dataType: 'json',
            data: formData, // Use FormData object
            processData: false, // Prevent jQuery from converting the data
            contentType: false, // Prevent jQuery from setting contentType
            success: function (response) {
                if (response.status === 'success') {
                    $('#add-tournament').hide();
                    $('#current-tournaments').fadeIn();
                    showBanner('#save-banner');
                    $('#tournament-name').val('');
                    $('#tournament-date').val('');
                    $('#tournament-logo').val('');
                    loadTournaments();
                } else {
                    showBanner('#save-error-banner');
                }
            },
            error: function () {
                showBanner('#save-error-banner');
            }
        });
    });

    function showBanner(selector) {
        $(selector).addClass('show');
        setTimeout(function() {
            $(selector).removeClass('show');
        }, 3000); // Hide after 3 seconds
    }
});

function loadTournaments() {
    $.ajax({
        url: '/scripts/get_tournaments.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderTournaments(response.data);
            }
        }
    });
}

function renderTournaments(tournaments) {
    const container = $('#current-tournaments');
    container.empty();
    
    if (tournaments.length > 0) {
        tournaments.forEach(function(tournament) {
            const tournamentItem = `<div class="tournament-item">
                <h3>${tournament.name}</h3>
                <p>${tournament.date}</p>
            </div>`;

            const tournamentCard = `
                <div class="tournament-card" onclick="window.location.href='./manage-tournament/${tournament.id}'">
                    <div class="tournament-logo-container">
                        <img src="/images/uploads/tournament_logos/${tournament.logo}" alt="Tournament Logo" class="tournament-logo">
                    </div>
                    <div>
                        <span class="tournament-name">${tournament.name}</span>
                        <br>
                        <span class="tournament-info">${tournament.creator_name}</span>
                        <br>
                        <span class="tournament-info date">${moment(tournament.date).format('MM/DD/YYYY')}</span>
                        <br>
                        <span class="tournament-info">${tournament.games_count} Games</span>
                    </div>
                </div>
            `;
            container.append(tournamentCard);
        });
    } else {
        container.append('<p>No tournaments found.</p>');
    }
}

function cancelTournamentCreation() {
    $('#tournament-name').val('');
    $('#tournament-date').val('');
    $('#tournament-logo').val('');

    $('#add-tournament').hide();
    $('#current-tournaments').show();
}