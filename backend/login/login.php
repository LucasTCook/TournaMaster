<?php
include '../../includes/header.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate user credentials (this is just an example; you should replace this with your validation logic)
    if ($username === 'admin' && $password === 'password') {
        // Set session variable to indicate user is logged in
        $_SESSION['user_logged_in'] = true;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Handle invalid login attempt
        echo "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="../../css/signin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="notification" class="notification"></div>
    <div class="signin-container">
        <div class="logo">
            <img src="../../images/logo.png" alt="App Logo">
        </div>

        <form class="signin-form" id="signin-form">
            <div class="form-group">
                <input type="text" id="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" placeholder="Password" required>
            </div>
            <div class="submit-buttons-container">
                <div class="form-group">
                    <button type="submit" id="signin-btn">Sign In</button>
                </div>
                <div class="form-group">
                    <button type="submit" id="create-account-btn">Create Account</button>
                </div>
            </div>
        </form>
    </div>

    <script src="../../js/signin.js"></script>
    
</body>
</html>

<?php include '../../includes/footer.php'; ?>