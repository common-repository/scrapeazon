/**
 * Admin code for dismissing notifications.
 *
 */
jQuery(document).ready(function($) {
    $(document).on('click', '#szsunset .notice-dismiss', function( event ) {
        data = {
            action : 'sz_display_dismissible_admin_notice',
        };
 
    $.post(ajaxurl, data, function (response) {
            console.log(response, 'scrapeazon notice dismissed!');
        });
    });
});

jQuery(document).ready(function($) {
    $(document).on('click', '#szeol .notice-dismiss', function( event ) {
        data = {
            action : 'sz_display_dismissible_admin_notice',
        };
 
    $.post(ajaxurl, data, function (response) {
            console.log(response, 'scrapeazon notice dismissed!');
        });
    });
});