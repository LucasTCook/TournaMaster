$(document).ready(function() {
    // Initialize variables for navigation
    // let currentPage = 0;
    // const allPages = $('.bracket-page');
    // const totalPages = allPages.filter(":visible").length;

    // // Find the first visible round and set it as the starting page
    // currentPage = allPages.index(allPages.filter(":visible").first());

    // console.log(allPages.index(allPages.filter(":visible").first()));

    // // Functions for navigating bracket pages
    // function goToPreviousPage() {
    //     if (currentPage > 0) {
    //         currentPage--;
    //         updateBracket();
    //     }
    // }

    // function goToNextPage() {
    //     if (currentPage < totalPages - 1) {
    //         currentPage++;
    //         updateBracket();
    //     }
    // }

    // // Logic to update bracket display and button visibility
    // function updateBracket() {
    //     allPages.hide();  // Hide all bracket pages
    //     $(allPages[currentPage]).show();  // Show only the current page

    //     // Toggle button visibility
    //     if (currentPage === 0) {
    //         $('.bracket-button-container .bracket-button:first-child').hide();
    //     } else {
    //         $('.bracket-button-container .bracket-button:first-child').show();
    //     }

    //     if (currentPage === totalPages - 1) {
    //         $('.bracket-button-container .bracket-button:last-child').hide();
    //     } else {
    //         $('.bracket-button-container .bracket-button:last-child').show();
    //     }
    // }

    // // Previous and Next button event listeners
    // $('.bracket-button-container .bracket-button:first-child').on('click', goToPreviousPage);
    // $('.bracket-button-container .bracket-button:last-child').on('click', goToNextPage);

    // // Initialize the bracket display based on the first visible page
    // updateBracket();
});
