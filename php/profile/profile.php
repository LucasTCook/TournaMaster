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
        <h2 id="username-display" class="profile-name"></h2>
        <input type="hidden" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
    </div>
</div>
<div class="qr-code-container">
    <div class="qr-content">
        <div id="qrcode" class="qr-code"></div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
