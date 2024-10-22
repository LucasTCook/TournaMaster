$(document).ready(function() {
    // Previous and Next button event listeners
    $('.bracket-button-container .bracket-button:first-child').on('click', function() {
        goToPreviousPage();
    });

    $('.bracket-button-container .bracket-button:last-child').on('click', function() {
        goToNextPage();
    });
    
    // Functions for navigating bracket pages
    function goToPreviousPage() {
        if (currentPage > 0) {
            currentPage--;
            updateBracket();
        }
    }

    function goToNextPage() {
        if (currentPage < totalPages - 1) {
            currentPage++;
            updateBracket();
        }
    }

    // Logic to update bracket display and button visibility
    function updateBracket() {
        $('.bracket-page').hide();
        $('#bracket-page-' + currentPage).show();

        if (currentPage === 0) {
            $('.bracket-button-container .bracket-button:first-child').hide();
        } else {
            $('.bracket-button-container .bracket-button:first-child').show();
        }

        if (currentPage === totalPages - 1) {
            $('.bracket-button-container .bracket-button:last-child').hide();
        } else {
            $('.bracket-button-container .bracket-button:last-child').show();
        }
    }

    // Initialize the bracket display
    let currentPage = 0;
    const totalPages = $('.bracket-page').length;
    updateBracket();
});
