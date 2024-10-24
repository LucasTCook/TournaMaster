$(document).ready(function() {
    // Clear any previous QR code
    $('#qrcode').empty();

    $.ajax({
        url: '/scripts/get_session.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.username) {
                $('#username-display').html(response.username);
            }
            if (response.user_id) {
                // Generate the QR code using QRCode.js
                new QRCode(document.getElementById("qrcode"), {
                    text: String(response.user_id),
                    width: 200,
                    height: 200
                });
            }
        },
        error: function() {
            console.log('Failed to fetch user ID');
        }
    });    
});