document.addEventListener("DOMContentLoaded", function() {
    let currentPage = 0;
    const totalPages = document.querySelectorAll('.trophies-page').length; // Number of pages
    const trophiesGrid = document.getElementById("trophies-grid");
    const dots = document.querySelectorAll(".dot");

    // Initial setup
    updateDots();

    // Handle swipe start
    let touchStartX = 0;

    trophiesGrid.addEventListener("touchstart", function(e) {
        touchStartX = e.touches[0].clientX;
    });

    // Handle swipe move
    trophiesGrid.addEventListener("touchend", function(e) {
        const touchEndX = e.changedTouches[0].clientX;
        handleSwipe(touchStartX, touchEndX);
    });

    // Detect swipe direction and trigger page change
    function handleSwipe(startX, endX) {
        const swipeThreshold = 50; // Minimum swipe distance

        if (endX < startX - swipeThreshold) {
            // Swipe left (next page)
            goToNextPage();
        } else if (endX > startX + swipeThreshold) {
            // Swipe right (previous page)
            goToPrevPage();
        }
    }

    function goToNextPage() {
        if (currentPage < totalPages - 1) {
            currentPage++;
            updateGrid();
        }
    }

    function goToPrevPage() {
        if (currentPage > 0) {
            currentPage--;
            updateGrid();
        }
    }

    function updateGrid() {
        const width = trophiesGrid.clientWidth / totalPages; // Divide the width by the total number of pages
        trophiesGrid.style.transform = `translateX(-${currentPage * width}px)`; // Shift to show the correct page
        updateDots();
    }

    function updateDots() {
        dots.forEach((dot, index) => {
            dot.classList.toggle("active", index === currentPage);
        });
    }
});

function showBadges(category) {
    // Hide all badge sections
    document.getElementById('gold-badges').classList.add('hidden');
    document.getElementById('silver-badges').classList.add('hidden');
    document.getElementById('bronze-badges').classList.add('hidden');

    // Show the selected category
    document.getElementById(category + '-badges').classList.remove('hidden');
}

