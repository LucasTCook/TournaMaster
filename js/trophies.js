$(document).ready(function() {
    getTrophies();
});

function getTrophies() {
    $.ajax({
        url: '/scripts/get_user_trophies.php', // Your PHP script to fetch trophies
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#gold-badges').empty();
                response.trophies.forEach(function(trophy) {
                    const trophyHtml = `
                        <div class="trophy-container">
                            <img class="golden-border" src="/images/golden-border.webp" alt="Golden Border">
                            <img class="trophy" src="${trophy.game_image_url}" alt="Trophy">
                            <div class="game-name-container">
                                <span class="game-name">${trophy.game_name}</span>
                            </div>
                            <div class="tournament-info">
                                <span class="tournament-name">${trophy.tournament_name}</span>
                                <span class="tournament-date">${trophy.tournament_date}</span>
                            </div>
                        </div>
                    `;
                    $('#gold-badges').append(trophyHtml); // Append trophy to container
                });
            } else {
                console.log(response.error);
            }
        },
        error: function(error) {
            console.error("Error fetching trophies:", error);
        }
    });
};