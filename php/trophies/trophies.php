<?php 
    session_start();
    $activePage = 'trophies';
    include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/trophies.css">
<script src="../../js/trophies.js"></script>

<div class="trophies-container">
    <h3 class="trophies-header">Trophies</h3>
</div>

<!-- Badge Sections -->
<div class="trophies-grid-container">
    <div id="gold-badges" class="trophies-grid"></div>
</div>




<?php include '../../includes/footer.php'; ?>
