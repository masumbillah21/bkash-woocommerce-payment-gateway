jQuery(document).ready(function($) {
    $('#check-connection').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        button.prop('disabled', true).text('Checking...');


        $.ajax({
            url: bwpgConfig.ajaxUrl,
            type: 'POST',
            data: {
                action: 'check_bkash_connection',
                nonce: bwpgConfig.nonce
            },
            
            success: function(response) {
                button.prop('disabled', false).text('Check Connection');
                alert(response.statusMessage);
            },
            error: function(xhr, status, error) {
                button.prop('disabled', false).text('Check Connection');
                console.log(xhr.responseText);
                alert('Error checking connection.');
            }
        });
    });
});
