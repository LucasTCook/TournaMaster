function showBadges(category) {
    // Hide all badge sections
    $('#gold-badges').addClass('hidden');
    $('#silver-badges').addClass('hidden');
    $('#bronze-badges').addClass('hidden');

    // Show the selected category
    $('#' + category + '-badges').removeClass('hidden');
}

