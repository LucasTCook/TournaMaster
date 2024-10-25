$(document).ready(function () {
    populateProfile();
    // QR Code Generation Logic
    $('#qrcode').empty();

    $.ajax({
        url: '/scripts/get_session.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.username) {
                $('#username-display').html(response.username);
            }
            if (response.user_id) {
                new QRCode(document.getElementById("qrcode"), {
                    text: String(response.user_id),
                    width: 200,
                    height: 200
                });
            }
        },
        error: function () {
            console.log('Failed to fetch user ID');
        }
    });

    // Photo Upload Logic
    const profilePicPreview = $('#profile-pic-preview');
    const uploadPhotoInput = $('#profile-photo');

    // Trigger file input when profile picture is clicked
    profilePicPreview.on('click', function () {
        uploadPhotoInput.click();
    });

    // Handle File Input Change
    uploadPhotoInput.on('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('profile-photo', file);
            $.ajax({
                url: '/scripts/upload_profile_photo.php',
                method: 'POST',
                dataType: 'json',
                data: formData, // Use FormData object
                processData: false, // Prevent jQuery from converting the data
                contentType: false, // Prevent jQuery from setting contentType
                success: function (response) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        profilePicPreview.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                    
                }
            });
        }
    });
});

function populateProfile(){
    $.ajax({
        url: '/scripts/get_profile.php',
        method: 'GET',
        success: function (response) {
            if (response){
                $('#profile-pic-preview').attr('src','/images/uploads/profile_photos/'+ response)
            } else{
                $('#profile-pic-preview').attr('src','/images/profile-placeholder.png')
            }
        },
        error: function () {
            console.log('Failed to fetch user ID');
        }
    });
}
