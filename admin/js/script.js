jQuery(document).ready(function($) {
    // Single global instance of media uploader
    let customUploader = null;
    let hasUploaderEvents = false;

    // Color picker initialization
    $('.color-field').wpColorPicker();
    initializeColorPickers();

    // Hex to RGBA conversion
    function hexToRGBA(hex, alpha = 1) {
        // Remove the hash if present
        hex = hex.replace('#', '');
    
        // Parse the values
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
    
        // Return RGBA string
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function initializeColorPickers() {
        $('.color-field').each(function() {
            const $input = $(this);
            
            // Special handling for addon background color
            if ($input.attr('id') === 'distm_addon_bg_color') {
                $input.wpColorPicker({
                    change: function(event, ui) {
                        const rgbaColor = hexToRGBA(ui.color.toString(), 0.6);
                        $('.preview-frame .tm-addon-menu-wrapper').css('background-color', rgbaColor);
                    },
                    clear: function() {
                        $('.preview-frame .tm-addon-menu-wrapper').css('background-color', '');
                    }
                });
            } else {
                // Regular color picker initialization
                $input.wpColorPicker();
            }
        });
    }

    // Function to load initial states and set up preview
    function initializePreviewState() {
        // Handle transparency on scroll
        const transparencyEnabled = $('input[name="distm_settings[distm_enable_transparency]"]').is(':checked');
        $('.preview-frame .the-menu').toggleClass('tm-scrolling', transparencyEnabled);

        // Handle icons only (no text)
        const iconsOnlyEnabled = $('input[name="distm_settings[distm_disable_menu_text]"]').is(':checked');
        $('.preview-frame .the-menu').toggleClass('icon-only', iconsOnlyEnabled);

        // Handle menu style
        const menuStyle = $('select[name="distm_settings[distm_menu_style]"]').val();
        $('.preview-frame .tm-fixed-mobile-menu-wrapper')
            .removeClass('pill rounded flat')
            .addClass(menuStyle);

        // Handle addon menu style
        const addonStyle = $('select[name="distm_settings[distm_addon_menu_style]"]').val();
        $('.preview-frame .tm-addon-menu-wrapper')
            .removeClass('app-icon icon list')
            .addClass(addonStyle);

        // Handle addon background color with RGBA conversion
        const addonBgColor = $('#distm_addon_bg_color').val();
        if (addonBgColor) {
            const rgbaColor = hexToRGBA(addonBgColor, 0.6);
            $('.preview-frame .tm-addon-menu-wrapper').css('background-color', rgbaColor);
        }
    }

    // Function to update preview state
    function updatePreview(selector, cssClass, isEnabled) {
        const element = $(selector);
        if (element.length) {
            element.toggleClass(cssClass, isEnabled);
            // Force a redraw
            element[0].offsetHeight;
        }
    }

    // Function to set up live preview updates
    function initStyleUpdates() {
        // Transparency on scroll toggle
        $('input[name="distm_settings[distm_enable_transparency]"]').on('change', function() {
            const isEnabled = $(this).is(':checked');
            updatePreview('.preview-frame .the-menu', 'tm-scrolling', isEnabled);
        });

        // Icons only toggle
        $('#distm_disable_menu_text').on('change', function() {
            const isEnabled = $(this).is(':checked');
            updatePreview('.preview-frame .the-menu', 'icon-only', isEnabled);
        });

        // Menu style updates
        $('select[name="distm_settings[distm_menu_style]"]').on('change', function() {
            const menuStyle = $(this).val();
            const menuWrapper = $('.preview-frame .tm-fixed-mobile-menu-wrapper');
            menuWrapper.removeClass('pill rounded flat').addClass(menuStyle);
        });

        // Addon menu style updates
        $('select[name="distm_settings[distm_addon_menu_style]"]').on('change', function() {
            const addonStyle = $(this).val();
            const addonWrapper = $('.preview-frame .tm-addon-menu-wrapper');
            addonWrapper.removeClass('app-icon icon list').addClass(addonStyle);
        });
    }

    // Handle icon type radio selection
    $(document).on('change', '.icon-type-radio', function() {
        const container = $(this).closest('.featured-icon-wrapper, .field-custom');
        const uploadSection = container.find('.icon-upload-section');
        const dashiconSection = container.find('.dashicon-selection-section');
        
        if ($(this).val() === 'upload') {
            uploadSection.show();
            dashiconSection.hide();
        } else {
            uploadSection.hide();
            dashiconSection.show();
        }
    });

    // Media uploader initialization and handler
    $('.tm-upload-button').on('click', function(e) {
        e.preventDefault();
        
        if (!customUploader) {
            customUploader = wp.media({
                title: 'Select Icon',
                library: { type: ['image', 'image/svg+xml'] },
                button: { text: 'Use this icon' },
                multiple: false
            });

            customUploader.on('select', function() {
                const attachment = customUploader.state().get('selection').first().toJSON();
                const container = $('.featured-icon-wrapper');
                const inputField = container.find('#distm_featured_icon');
                const previewContainer = container.find('.icon-preview');
                const featuredIconContainer = $('.preview-frame .tm-featured-icon');
                
                inputField.val(attachment.url);

                if (attachment.url.toLowerCase().endsWith('.svg')) {
                    fetch(attachment.url)
                        .then(response => response.text())
                        .then(svgContent => {
                            previewContainer.html(`<img src="${attachment.url}" style="max-width: 40px; height: auto;">`);
                            featuredIconContainer.html(svgContent);
                        })
                        .catch(() => {
                            const fallbackIcon = '<span class="dashicons dashicons-menu"></span>';
                            previewContainer.html(fallbackIcon);
                            featuredIconContainer.html(fallbackIcon);
                        });
                } else {
                    const imgHtml = `<img src="${attachment.url}" alt="Featured Icon" />`;
                    previewContainer.html(`<img src="${attachment.url}" style="max-width: 40px; height: auto;">`);
                    featuredIconContainer.html(imgHtml);
                }

                container.find('input[value="upload"]').prop('checked', true).trigger('change');
            });
        }

        customUploader.open();
    });

    // Dashicon selection handler
    $(document).on('click', '.dashicon-option', function() {
        const container = $(this).closest('.featured-icon-wrapper, .field-custom');
        const selectedIcon = $(this).data('icon');
        const dashiconInput = container.find('.selected-dashicon');
        const previewContainer = container.find('.icon-preview');
        const featuredIconContainer = $('.preview-frame .tm-featured-icon');
        const dashiconHtml = `<span class="dashicons dashicons-${selectedIcon}"></span>`;
        
        // Remove selected class from all icons in this container
        container.find('.dashicon-option').removeClass('selected');
        // Add selected class to clicked icon
        $(this).addClass('selected');
        
        // Update the hidden input with the selected icon
        dashiconInput.val(selectedIcon);
        
        // Update preview if it exists
        if (previewContainer.length) {
            previewContainer.html(dashiconHtml);
        }
        
        // Update featured icon if this is the featured icon selector
        if (container.hasClass('featured-icon-wrapper') && featuredIconContainer.length) {
            featuredIconContainer.html(dashiconHtml);
        }
        
        // Ensure dashicon radio is selected
        container.find('input[value="dashicon"]').prop('checked', true);
    });

    // Search dashicons - improved version
    $(document).on('input', '.dashicon-search', function() {
        const container = $(this).closest('.dashicon-selection-section');
        const searchTerm = $(this).val().toLowerCase();
        const grid = container.find('.dashicon-grid');
        
        // Handle each category section
        grid.find('.dashicon-category-header').each(function() {
            const header = $(this);
            let hasVisibleIcons = false;
            
            // Find all icons until next header
            let currentElement = header.next();
            while (currentElement.length && !currentElement.hasClass('dashicon-category-header')) {
                if (currentElement.hasClass('dashicon-option')) {
                    const iconName = currentElement.data('icon') || '';
                    const iconTitle = currentElement.attr('title') || '';
                    const matches = iconName.toLowerCase().includes(searchTerm) || 
                                  iconTitle.toLowerCase().includes(searchTerm);
                    
                    currentElement.toggle(matches);
                    if (matches) hasVisibleIcons = true;
                }
                currentElement = currentElement.next();
            }
            
            // Show/hide header based on whether category has visible icons
            header.toggle(hasVisibleIcons);
        });
        
        // Handle separators
        grid.find('.dashicon-separator').toggle(!searchTerm);
    });

    // Initialize tabs
    function initializeTabs() {
        if ($('.tm-tabs').length === 0) {
            const $tabContainer = $('<div class="tm-tabs"></div>');
            const $tabList = $('<div class="tm-tab-list"></div>');
            
            $('.settings-section').each(function(index) {
                $(this).wrap('<div class="tm-tab-content"></div>');
            });
            
            $('.settings-section').each(function(index) {
                const title = $(this).find('.settings-section-title').text();
                const tabId = `tm-tab-${index}`;
                const $tab = $(`<button type="button" class="tm-tab" data-tab="${tabId}">${title}</button>`);
                
                $(this).closest('.tm-tab-content').attr('id', tabId);
                $tabList.append($tab);
            });

            $tabContainer.append($tabList);
            $('.settings-wrapper').prepend($tabContainer);

            const savedTab = localStorage.getItem('tmActiveTab');
            const $tabs = $('.tm-tab');
            const $contents = $('.tm-tab-content');
            
            if (savedTab && $(`#${savedTab}`).length) {
                $tabs.removeClass('active');
                $contents.removeClass('active');
                $(`.tm-tab[data-tab="${savedTab}"]`).addClass('active');
                $(`#${savedTab}`).addClass('active');
            } else {
                $tabs.first().addClass('active');
                $contents.first().addClass('active');
            }
        }
    }

    // Initialize everything
    initializeTabs();
    initializePreviewState();
    initStyleUpdates();

    // Tab click handler
    $(document).on('click', '.tm-tab', function(e) {
        e.preventDefault();
        const tabId = $(this).data('tab');
        
        $('.tm-tab').removeClass('active');
        $('.tm-tab-content').removeClass('active');
        $(this).addClass('active');
        $(`#${tabId}`).addClass('active');
        
        localStorage.setItem('tmActiveTab', tabId);
    });

    // Form submission handling
    $('form').on('submit', function(e) {
        const form = $(this);
        
        // Save active tab
        const activeTab = $('.tm-tab.active').data('tab');
        if (activeTab) {
            localStorage.setItem('tmActiveTab', activeTab);
        }

        // Handle excluded pages
        const excludedPagesSelect = $('#distm_exclude_pages');
        if (excludedPagesSelect.length) {
            const selectedPages = excludedPagesSelect.val() || [];
            form.find('input[name="distm_settings[distm_exclude_pages][]"]').remove();
            selectedPages.forEach(function(pageId) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'distm_settings[distm_exclude_pages][]',
                    value: pageId
                }));
            });
        }

        // Handle icon settings
        const iconWrapper = form.find('.featured-icon-wrapper');
        if (iconWrapper.length) {
            const iconType = iconWrapper.find('.icon-type-radio:checked').val();
            const dashiconValue = iconWrapper.find('.selected-dashicon').val();

            form.find('input[name="distm_settings[distm_featured_icon_type]"]').remove();
            form.find('input[name="distm_settings[distm_featured_dashicon]"]').remove();

            form.append($('<input>', {
                type: 'hidden',
                name: 'distm_settings[distm_featured_icon_type]',
                value: iconType || 'dashicon'
            }));

            if (iconType === 'dashicon' && dashiconValue) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: 'distm_settings[distm_featured_dashicon]',
                    value: dashiconValue
                }));
            }
        }

        // Handle unchecked checkboxes
        form.find('input[type="checkbox"]').each(function() {
            if (!$(this).is(':checked')) {
                form.append($('<input>', {
                    type: 'hidden',
                    name: $(this).attr('name'),
                    value: '0'
                }));
            }
        });
    });

    // Handle settings notices
    if ($('.settings-error').length) {
        $('.settings-error').insertBefore('.tm-tabs');
    }
});

function verifyLicense(licenseKey) {
    jQuery.ajax({
        url: distmAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'distm_verify_license',
            nonce: distmAjax.nonce,
            license_key: licenseKey
        },
        success: function(response) {
            if (response.success) {
                updateLicenseStatus(true);
            } else {
                updateLicenseStatus(false);
                console.error('License verification failed:', response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('License verification error:', error);
            updateLicenseStatus(false);
        }
    });
}