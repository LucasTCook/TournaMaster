<?php
include '../../includes/header.php';
@session_start();
?>

<script src="/js/login.js"></script>

<link rel="stylesheet" href="/css/login.css">

    <div id="notification" class="notification"></div>
    <div class="login-container">
        <div class="logo">
            <img src="/images/logo.png" alt="App Logo">
        </div>

        <form class="login-form" id="login-form">
            <div class="form-group">
                <input type="text" id="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" placeholder="Password" required>
            </div>
            <div class="submit-buttons-container">
                <div class="form-group">
                    <button type="submit" id="login-btn" class="success-btn">Sign In</button>
                </div>
                <div class="form-group">
                    <button type="button" id="create-account-btn" class="edit-btn">Create Account</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Hidden Create Account Panel -->
    <div id="create-account-panel" class="create-account-container hidden">
        <h2>Create Account</h2>
        <div class="create-account-form">
            <form id="create-account-form">
                <div class="form-group">
                    <input type="text" id="new-email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" id="new-username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" id="new-password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit" id="create-account-submit-btn" class="success-btn" disabled>Create Account</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Notification banners -->
    <div id="account-create-success-banner" class="notification-banner success">Account Created Successfully</div>
    <div id="account-create-error-banner" class="notification-banner error">Account Already Exists</div>
    <div id="login-error-banner" class="notification-banner error">Username or Password is invalid</div>

    </body>
</html>