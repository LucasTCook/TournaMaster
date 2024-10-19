$(document).ready(function() {
    $('#signin-form').on('submit', function(e) {
        e.preventDefault();

        var username = $('#username').val();
        var password = $('#password').val();

        if (username && password) {
            alert('Sign-in successful! Username: ' + username);
        } else {
            alert('Please enter both username and password.');
        }
    });
});
