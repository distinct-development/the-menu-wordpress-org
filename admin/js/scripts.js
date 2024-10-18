// Initialize the WP color picker on .color-field inputs
jQuery(document).ready(function($) {
    $('.color-field').wpColorPicker();
});

// Initialize the WP image library on .upload-button inputs
jQuery(document).ready(function($){
    $(document).on('click', '.upload-button', function(e) {
        e.preventDefault();
        var $button = $(this);

        // Create the media frame.
        var file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or upload icon',
            library: { type: 'image' }, // only allow images
            button: { text: 'Select' },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $button.prev('.widefat').val(attachment.url);
        });

        // Finally, open the modal
        file_frame.open();
    });
});