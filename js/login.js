$(document).ready(function() {
    // Show Create Account Panel when "Create Account" button is clicked
    $('#create-account-btn').on('click', function() {
        $('.login-container').hide();
        $('#create-account-panel').fadeIn();
    });

    // Handle Create Account form submission
    $('#create-account-form').on('submit', function(e) {
        e.preventDefault();
        const newUsername = $('#new-username').val();
        const newPassword = $('#new-password').val();
        const newEmail = $('#new-email').val();

        $.ajax({
            type: 'POST',
            url: '/scripts/create_account.php',
            data: { username: newUsername, password: newPassword , email: newEmail},
            success: function(response) {
                if (response === 'success') {
                    $('#new-username').val('');
                    $('#new-password').val('');
                    $('#new-email').val('');
                    $('#account-create-error-banner').hide();
                    $('#account-create-success-banner').fadeIn();
                    $('.login-container').fadeIn();
                    $('#create-account-panel').hide();
                    setTimeout(function() {
                        $('#account-create-success-banner').fadeOut();
                    }, 3000);
                } else if (response === 'exists') {
                    $('#account-create-success-banner').hide();
                    $('#account-create-error-banner').fadeIn();
                    setTimeout(function() {
                        $('#account-create-error-banner').fadeOut();
                    }, 3000);
                }
            }
        });
    });

    $('#new-username').on('keyup', function() {
        const input = $(this).val();
        const maskedInput = input.replace(/[^a-zA-Z0-9_-]/g, ''); // Remove invalid characters
        $(this).val(maskedInput);  // Set the cleaned input back
    });

    function validateForm() {
        const username = $('#new-username').val();
        const email = $('#new-email').val();
        const password = $('#new-password').val();
        
        const usernameValid = /^[a-zA-Z0-9_-]+$/.test(username);  // Alphanumeric, _ or -
        const emailValid = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);  // Email format
        const passwordValid = password.length >= 6;  // Password length validation
        
        // Enable button if all fields are valid
        if (usernameValid && emailValid && passwordValid) {
            $('#create-account-submit-btn').prop('disabled', false);
        } else {
            $('#create-account-submit-btn').prop('disabled', true);
        }
    }

    // Validate form on input change
    $('#new-username, #new-email, #new-password').on('input', validateForm);
});
