<?php 
    session_start();
    $activePage = 'trophies';
    include '../../includes/header.php'; 
?>

<link rel="stylesheet" href="../../css/trophies.css">
<script src="../../js/trophies.js"></script>

<div class="trophies-container">
    <h3 class="trophies-header">Trophies</h3>
    <div class="trophies-header-buttons">
        <div class="trophies-selection gold" onclick="showBadges('gold')">
            <span class="trophies-selection-title">Gold</span>
        </div>
        <div class="trophies-selection silver" onclick="showBadges('silver')">
            <span class="trophies-selection-title">Silver</span>
        </div>
        <div class="trophies-selection bronze" onclick="showBadges('bronze')">
            <span class="trophies-selection-title">Bronze</span>
        </div>
    </div>
</div>

<!-- Badge Sections -->
<div class="trophies-grid-container">
    <!-- Gold Badges -->
    <div id="gold-badges" class="trophies-grid">
        <div class="trophy gold-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy gold-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy gold-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Mortal Kombat</div>
        </div>
        <div class="trophy gold-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Wario Ware</div>
        </div>
        <div class="trophy gold-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Atari Bowling</div>
        </div>
        
    </div>

    <!-- Silver Badges -->
    <div id="silver-badges" class="trophies-grid hidden">
    <div class="trophy silver-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy silver-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy silver-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Mortal Kombat</div>
        </div>
        <div class="trophy silver-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Wario Ware</div>
        </div>
        <div class="trophy silver-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Atari Bowling</div>
        </div>
    </div>

    <!-- Bronze Badges -->
    <div id="bronze-badges" class="trophies-grid hidden">
    <div class="trophy bronze-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy bronze-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Super Smash Bros. Karma Unleased</div>
        </div>
        <div class="trophy bronze-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024<br>Mortal Kombat</div>
        </div>
        <div class="trophy bronze-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Wario Ware</div>
        </div>
        <div class="trophy bronze-border">
            <!-- <div class="top-bar">Jan 2022</div> -->
            <div class="bottom-bar">Feb 2024 - Atari Bowling</div>
        </div>
    </div>
</div>




<?php include '../../includes/footer.php'; ?>
