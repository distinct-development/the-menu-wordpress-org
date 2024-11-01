jQuery(document).ready(function($) {
    
    function updateImagePreview(container) {
        const iconType = container.find('.icon-type-radio:checked').val();
        const iconUrl = container.find('.edit-menu-item-icon').val();
        const previewContainer = container.find('.icon-preview');
        
        // Clear existing content
        previewContainer.empty();
    
        // Only show preview for upload type and when we have a URL
        if (iconUrl && iconUrl.length > 0) {
            previewContainer.html(`<img src="${iconUrl}" style="max-width: 40px; height: auto;">`);
        }
    }
    // Radio button change handler
    $(document).on('change', '.icon-type-radio', function() {
    const container = $(this).closest('.field-custom');
    const uploadSection = container.find('.icon-upload-section');
    const dashiconSection = container.find('.dashicon-selection-section');
    const iconType = this.value;
    
    if (iconType === 'upload') {
            dashiconSection.hide();
            uploadSection.show();
        } else if (iconType === 'dashicon') {
            uploadSection.hide();
            dashiconSection.show();
        }
    
    updateImagePreview(container);
    });
    
    // Initialize previews on page load
    $('.field-custom').each(function() {
    updateImagePreview($(this));
    });

    // Media uploader
    $(document).on('click', '.upload-icon-button', function(e) {
        e.preventDefault();
        const button = $(this);
        const inputField = button.prev('input');
        const container = button.closest('.field-custom');
        
        const uploader = wp.media({
            title: 'Select or Upload Icon',
            library: { type: ['image', 'image/svg+xml'] },
            button: { text: 'Use this icon' },
            multiple: false
        });
    
        uploader.on('select', function() {
            const attachment = uploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            container.find('input[value="upload"]').prop('checked', true).trigger('change');
            updateImagePreview(container);
        });
    
        uploader.open();
    });

    // Dashicon selection
    $(document).on('click', '.dashicon-option', function() {
        const container = $(this).closest('.field-custom');
        const selectedIcon = $(this).data('icon');
        const menuItemId = container.closest('.menu-item').find('.menu-item-data-db-id').val();
        
        // Update dashicon selection
        container.find('.selected-dashicon').val(selectedIcon);
        container.find('.dashicon-option').removeClass('selected');
        $(this).addClass('selected');

        // Update radio button and icon type
        container.find('input[value="dashicon"]').prop('checked', true);
        container.find(`input[name="menu-item-icon-type[${menuItemId}]"]`).val('dashicon');
        container.find(`input[name="menu-item-dashicon[${menuItemId}]"]`).val(selectedIcon);

        // Clear upload field
        container.find('.icon-preview').empty();
        container.find('.edit-menu-item-icon').val('');

        // Show/hide sections
        container.find('.icon-upload-section').hide();
        container.find('.dashicon-selection-section').show();
    });

    // Search dashicons
    $(document).on('input', '.dashicon-search', function() {
        const container = $(this).closest('.field-custom');
        const searchTerm = $(this).val().toLowerCase();
        const icons = container.find('.dashicon-option');
        const headers = container.find('.dashicon-category-header');
        const separator = container.find('.dashicon-separator');
        
        icons.each(function() {
            $(this).toggle($(this).data('icon').toLowerCase().includes(searchTerm));
        });
        
        headers.toggle(!searchTerm);
        separator.toggle(!searchTerm);
    });

    // Add necessary styles
    $('<style>').prop('type', 'text/css').html(`
        .dashicon-option {
            padding: 10px;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .dashicon-option:hover {
            background: #f0f0f0;
            transform: scale(1.2);
            border-radius: 10px;
        }
        .dashicon-option.selected {
            background: var(--tm-secondary-color, #2271b1);
            color: white;
            border-radius: 10px;
        }
        .icon-type-label {
            display: inline-block;
            margin-right: 15px;
        }
        .dashicon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
            gap: 5px;
            padding: 10px;
            border-radius: 10px;
        }
        .dashicon-selection-section {
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 15px;
        }
        .icon-preview {
            margin: 5px 0;
            min-height: 40px;
        }
        .dashicon-separator {
            grid-column: 1/-1;
            border-bottom: 1px solid #ddd;
            margin: 10px 0;
        }
        .icon-upload-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .icon-upload-section img {
            width: 30px;
            margin-top: 8px;
            height: auto;
        }
    `).appendTo('head');

    // Initial section visibility
    $('.field-custom').each(function() {
        const container = $(this);
        const iconType = container.find('.icon-type-radio:checked').val();
        
        if (iconType === 'upload') {
            container.find('.dashicon-selection-section').hide();
            container.find('.icon-upload-section').show();
        } else {
            container.find('.icon-upload-section').hide();
            container.find('.dashicon-selection-section').show();
        }
    });
});