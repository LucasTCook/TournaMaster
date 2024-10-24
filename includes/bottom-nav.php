<div class="bottom-nav">
    <a href="/profile" class="nav-item <?php if ($activePage == 'profile') { echo 'active'; } ?>">
        <i class="fas fa-user"></i> <!-- User icon for Profile -->
        <span>Profile</span>
    </a>
    <a href="/tournaments" class="nav-item <?php if ($activePage == 'tournaments') { echo 'active'; } ?>">
        <i class="fas fa-trophy"></i> <!-- Trophy icon for Tournaments -->
        <span>Tournaments</span>
    </a>
    <a href="/trophies" class="nav-item <?php if ($activePage == 'trophies') { echo 'active'; } ?>">
        <i class="fas fa-medal"></i> <!-- Medal icon for trophies -->
        <span>Trophies</span>
    </a>
    
    <!-- Conditionally display Manage option if the user is an admin -->
    <?php 
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): 
    ?>
        <a href="/manage" class="nav-item <?= $activePage == 'manage' ? 'active' : '' ?>">
            <i class="fas fa-cogs"></i> <!-- Manage icon (gear) -->
            <span>Manage</span>
        </a>
    <?php 
        endif; 
    ?>
</div>
