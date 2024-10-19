$(document).ready(function() {
    $('#signin-btn').click(function(event) {
        event.preventDefault(); // Prevent the form from submitting normally

        var username = $('#username').val();
        var password = $('#password').val();

        // Simulate sign-in success (Replace this with your actual authentication logic)
        if (username && password) {
            // Simulate a successful login and redirect to the dashboard
            window.location.href = '/dashboard';
        } else {
            alert('Please enter both username and password');
        }
    });

    $('#create-account-btn').click(function(event) {
        event.preventDefault();

        var username = $('#username').val();
        var password = $('#password').val();
        
        // Simulate backend response
        var accountExists = (username === 'existinguser');
        
        if (accountExists) {
            showNotification('Account already exists', 'error');
        } else {
            showNotification('Account Created', 'success');
        }
    });
    
    // Function to show the notification (you can reuse this across files if needed)
    function showNotification(message, type) {
        var notification = $('#notification');
        notification.removeClass('success error'); 
        notification.addClass(type); 
        notification.text(message);
        notification.slideDown();

        setTimeout(function() {
            notification.slideUp();
        }, 3000);
    }
});
