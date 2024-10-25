<?php 
    session_start();
    $activePage = 'profile'; 
    include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/profile.css">
<script src="../../js/profile.js" defer></script>

<div class="profile-container">
    <div class="profile-header">
        <!-- Profile Picture (Clickable) -->
        <div class="profile-pic-wrapper">
            <img src="../../images/profile-placeholder.png" 
                 alt="Profile Picture" 
                 class="profile-pic" 
                 id="profile-pic-preview">
            <!-- Hidden File Input -->
            <input type="file" id="profile-photo" accept="image/*" style="display: none;">
        </div>

        <h2 id="username-display" class="profile-name"></h2>
    </div>

</div>
<!-- QR Code Section -->
<div class="qr-code-container">
    <div class="qr-content">
        <div id="qrcode" class="qr-code"></div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
