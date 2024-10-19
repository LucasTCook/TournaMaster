<?php 
    session_start();
    $activePage = 'profile'; // Set active page to 'profile'
    include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/profile.css">
<script src="../../js/profile.js"></script>

<div class="profile-container">
    <!-- Profile Picture and Name -->
    <div class="profile-header">
        <img src="../../images/profile-placeholder.jpg" alt="Profile Picture" class="profile-pic">
        <h2 class="profile-name">John Doe</h2>
    </div>

    <!-- QR Code Button and Display -->
    <div class="qr-section">
        <button id="show-qr-btn">Show QR Code</button>
    </div>
</div>

<div id="qr-modal" class="qr-modal hidden">
    <div class="qr-modal-content">
        <!-- Placeholder for QR code (black box for now) -->
        <div class="qr-code"></div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
