document.addEventListener("DOMContentLoaded", function() {
    const qrBtn = document.getElementById("show-qr-btn");
    const qrModal = document.getElementById("qr-modal");

    qrBtn.addEventListener("click", function() {
        qrModal.classList.toggle("show");  // Toggle the 'show' class to display or hide the modal
    });

    // Optional: Close modal when clicking outside the QR code content
    qrModal.addEventListener("click", function(e) {
        if (e.target === qrModal) {
            qrModal.classList.remove("show");  // Hide the modal when clicking outside of the QR code box
        }
    });
});
