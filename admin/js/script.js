jQuery(document).ready(function($) {
    // Color picker initialization
    $('.color-field').wpColorPicker();

    // Handle icon type radio selection (works for both menu items and featured icon)
    $(document).on('change', '.icon-type-radio', function() {
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const uploadSection = container.find('.icon-upload-section');
        const dashiconSection = container.find('.dashicon-selection-section');
        
        if ($(this).val() === 'upload') {
            uploadSection.slideDown(300);
            dashiconSection.slideUp(300);
        } else {
            uploadSection.slideUp(300);
            dashiconSection.slideDown(300);
        }
    });

    // Media uploader for icons
    function initializeMediaUploader(buttonSelector) {
        $(document).on('click', buttonSelector, function(e) {
            e.preventDefault();
            
            const button = $(this);
            const inputField = button.prev('input');
            const previewContainer = button.closest('.icon-upload-section').find('.icon-preview');
            
            // Create or reuse media frame
            let custom_uploader = button.data('media-frame');
            
            if (!custom_uploader) {
                custom_uploader = wp.media({
                    title: 'Select or Upload Icon',
                    library: { type: ['image', 'image/svg+xml'] },
                    button: { text: 'Use this icon' },
                    multiple: false
                });

                // When an image is selected
                custom_uploader.on('select', function() {
                    const attachment = custom_uploader.state().get('selection').first().toJSON();
                    inputField.val(attachment.url);
                    
                    // Update preview
                    previewContainer.html(`<img src="${attachment.url}" style="max-width: 40px; height: auto;">`);

                    // Select upload radio button
                    button.closest('.field-custom, .featured-icon-wrapper').find('input[value="upload"]').prop('checked', true).trigger('change');
                });

                button.data('media-frame', custom_uploader);
            }

            custom_uploader.open();
        });
    }

    // Initialize media uploader for both menu items and featured icon
    initializeMediaUploader('.upload-icon-button, .tm-upload-button');

    // Dashicon search functionality
    $(document).on('input', '.dashicon-search', function() {
        const searchTerm = $(this).val().toLowerCase();
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const icons = container.find('.dashicon-option');
        
        icons.each(function() {
            const iconName = $(this).data('icon').toLowerCase();
            $(this).toggle(iconName.includes(searchTerm));
        });
    });

    // Dashicon selection
    $(document).on('click', '.dashicon-option', function() {
        const container = $(this).closest('.field-custom, .featured-icon-wrapper');
        const selectedIcon = $(this).data('icon');
        
        // Update hidden input
        container.find('.selected-dashicon').val(selectedIcon);
        
        // Update visual selection
        container.find('.dashicon-option').removeClass('selected');
        $(this).addClass('selected');

        // Select dashicon radio button
        container.find('input[value="dashicon"]').prop('checked', true).trigger('change');

        // Clear upload preview and input
        container.find('.icon-preview').empty();
        container.find('.edit-menu-item-icon, input[name*="distm_featured_icon"]').val('');
    });

    // Ensure form submission includes dashicon values
    $(document).on('submit', '#update-nav-menu, .tm-wrap form', function() {
        $('.dashicon-selection-section').each(function() {
            const selectedIcon = $(this).find('.dashicon-option.selected').data('icon');
            if (selectedIcon) {
                $(this).find('.selected-dashicon').val(selectedIcon);
            }
        });
    });

    // Mobile menu functionality
    const targetElement = document.querySelector('.tm-scrolling');

    function setOpacity(opacity) {
        if (targetElement) {
            targetElement.style.opacity = opacity;
        }
    }

    let scrollTimeout = null;

    if (targetElement) {
        window.addEventListener('scroll', () => {
            setOpacity(0.2);
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                setOpacity(1);
            }, 150);
        });
    }

    // Addon menu toggle
    const icon = document.querySelector('.tm-featured');
    const menuPopup = document.querySelector('.tm-addon-menu-wrapper');
    const featuredBg = document.querySelector('.tm-featured-bg');

    if (icon && menuPopup) {
        icon.addEventListener('click', function() {
            const isShown = menuPopup.classList.contains('show');
            if (!isShown) {
                menuPopup.classList.add('show');
                menuPopup.style.display = 'flex';
                menuPopup.style.animation = 'slideIn 0.5s forwards';
                icon.classList.add('active');
                if (featuredBg) featuredBg.classList.add('expanded');
            } else {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                if (featuredBg) featuredBg.classList.remove('expanded');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!icon.contains(event.target) && !menuPopup.contains(event.target) && menuPopup.classList.contains('show')) {
                menuPopup.style.animation = 'slideOut 0.5s forwards';
                icon.classList.remove('active');
                if (featuredBg) featuredBg.classList.remove('expanded');
                setTimeout(() => {
                    menuPopup.classList.remove('show');
                    menuPopup.style.display = 'none';
                }, 500);
            }
        });
    }

    // Page loader
    const links = document.querySelectorAll('.the-menu a');
    links.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
                return;
            }

            var pageLoader = document.getElementById('tm-pageLoader');
            if (pageLoader) {
                pageLoader.style.display = 'block';
                setTimeout(function() {
                    window.location.href = href;
                }, 100);
            }
            e.preventDefault();
        });
    });

    // Add styles for the dashicon grid
    $('<style>')
        .prop('type', 'text/css')
        .html(`
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
            }
            .dashicon-option.selected {
                background: #2271b1;
                color: white;
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
            }
            .dashicon-selection-section {
                background: #fff;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
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
        `)
        .appendTo('head');
});