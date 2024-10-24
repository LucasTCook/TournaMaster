$(document).ready(function() {
    const username = $('#username').val();
    $('#username-display').html(username);

    $('#show-qr-btn').on('click', function() {
        // Show the QR modal first
        $('#qr-modal').removeClass('hidden');

        // Use a slight delay to ensure DOM is updated before generating the QR code
        setTimeout(function() {
            // Clear any previous QR code
            $('#qrcode').empty();

            // AJAX request to get the user ID
            $.ajax({
                url: '/scripts/get_user_id.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.user_id) {
                        // Generate the QR code after ensuring the qrcode div exists
                        const qrcode = new QRCode(document.getElementById("qrcode"), {
                            text: response.user_id,
                            width: 200,
                            height: 200,
                        });
                    } else {
                        console.log(response.error);
                    }
                },
                error: function() {
                    console.log('Error fetching user ID');
                }
            });
        }, 100); // Small delay to ensure DOM rendering
    });

    // Optionally close the modal when clicked
    $('#qr-modal').on('click', function() {
        $(this).addClass('hidden');
    });

    showQRCode();
});

function showQRCode() {
    // Clear any previous QR code
    $('#qrcode').empty();

    // Fetch the user ID via AJAX
    $.ajax({
        url: '/scripts/get_user_id.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.user_id) {
                console.log(response.user_id);
                // Generate the QR code using QRCode.js
                new QRCode(document.getElementById("qrcode"), {
                    text: String(response.user_id),
                    width: 200,
                    height: 200
                });
            } else {
                console.log('Error: ' + response.error);
            }
        },
        error: function() {
            console.log('Failed to fetch user ID');
        }
    });
}