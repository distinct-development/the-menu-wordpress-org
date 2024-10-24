// Initialize the WP color picker on .color-field inputs
jQuery(document).ready(function($) {
    // Color picker initialization
    $('.color-field').wpColorPicker();

    // Media uploader for both featured icon and menu item icons
    function initializeMediaUploader(buttonSelector) {
        $(document).on('click', buttonSelector, function(e) {
            e.preventDefault();
            
            var button = $(this);
            var inputField = button.prev('input');
            
            // Check if we already have a media frame
            if (button.data('media-frame')) {
                button.data('media-frame').open();
                return;
            }

            // Create a new media frame
            var custom_uploader = wp.media({
                title: 'Select or Upload an Icon',
                library: { type: ['image', 'image/svg+xml'] },
                button: { text: 'Use this icon' },
                multiple: false
            });

            // When an image is selected, run a callback.
            custom_uploader.on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
            });

            // Open the uploader dialog
            custom_uploader.open();

            // Store the media frame for future use
            button.data('media-frame', custom_uploader);
        });
    }

    // Initialize media uploader for featured icon
    initializeMediaUploader('.tm-upload-button');

    // Initialize media uploader for menu item icons
    initializeMediaUploader('.upload-icon-button');
});