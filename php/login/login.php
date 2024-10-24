<?php
include '../../includes/header.php';
@session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials (this is just an example; you should replace this with your validation logic)
    if ($username === 'admin' && $password === 'password') {
        // Set session variable to indicate user is logged in
        $_SESSION['user_logged_in'] = true;

        // Redirect to profile
        header("Location: profile");
        exit();
    } else {
        // Handle invalid login attempt
        echo "Invalid username or password.";
    }
}
?>
<script src="../../js/login.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="../../css/login.css">
</head>
<body>
    <div id="notification" class="notification"></div>
    <div class="login-container">
        <div class="logo">
            <img src="../../images/logo.png" alt="App Logo">
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
    <div id="account-create-success-banner" class="notification-banner">Account Created Successfully</div>
    <div id="account-create-error-banner" class="notification-banner">Account Already Exists</div>

<?php include '../../includes/footer.php'; ?>