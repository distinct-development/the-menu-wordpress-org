// Initialize menu visibility controls
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

    // Handle visibility dropdown changes
    function handleVisibilityChange(dropdown) {
        const rolesContainer = $(dropdown).closest('.menu-item-settings').find('.field-roles');
        if ($(dropdown).val() === 'logged_in') {
            rolesContainer.slideDown(300);
        } else {
            rolesContainer.slideUp(300);
        }
    }

    // Initialize visibility controls for existing menu items
    $('.edit-menu-item-visibility').each(function() {
        handleVisibilityChange(this);
    });

    // Handle visibility changes for dynamically added menu items
    $(document).on('change', '.edit-menu-item-visibility', function() {
        handleVisibilityChange(this);
    });

    // Handle menu item expansion
    $(document).on('click', '.item-edit', function() {
        const menuItem = $(this).closest('.menu-item');
        const visibilityDropdown = menuItem.find('.edit-menu-item-visibility');
        
        // Small delay to ensure the menu item is expanded
        setTimeout(function() {
            handleVisibilityChange(visibilityDropdown);
        }, 100);
    });

    // Initialize visibility on menu load
    $(document).on('menu-item-added', function(event, menuMarkup) {
        const visibilityDropdown = $(menuMarkup).find('.edit-menu-item-visibility');
        handleVisibilityChange(visibilityDropdown);
    });
});