<!DOCTYPE html>
<html>
<body>
    <div class="preview-container">
        <div class="preview-header"><span class="dashicons dashicons-visibility" style="color:var(--tm-secondary-color);"></span> Live preview</div>
        <div class="preview-frame">
            <div class="preview-content">
            <iframe 
                src="<?php echo esc_url(home_url('/')); ?>" 
                style="width: 100%; height: 100%; border: none; transform: scale(1); transform-origin: 0 0;"
            ></iframe>
                <div class="the-menu <?php echo esc_attr(get_option('distm_settings')['distm_disable_menu_text'] ? 'icon-only' : ''); ?>">
                    <div class="tm-fixed-mobile-menu-wrapper <?php echo esc_attr(get_option('distm_settings')['distm_menu_style'] ?? 'pill'); ?>">
                        <div id="tm-fixed-mobile-menu">
                            <div class="tm-left-menu">
                                <?php 
                                wp_nav_menu(array(
                                    'theme_location' => 'left-menu',
                                    'walker' => new distm_Icon_Walker(),
                                    'container' => false,
                                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'fallback_cb' => false
                                ));
                                ?>
                            </div>
                            <div class="tm-featured-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 140 60" preserveAspectRatio="none">
                                    <path d="M139.2,0c-13.3,0-25.3,7.7-31.4,19.5-7.1,13.6-21.3,23-37.8,23s-30.7-9.3-37.8-23S14.2,0,.8,0h-.8v60h140V0h-.8Z"/>
                                </svg>
                                <div class="tm-filler"></div>
                            </div>
                            <div class="tm-right-menu">
                                <?php 
                                wp_nav_menu(array(
                                    'theme_location' => 'right-menu',
                                    'walker' => new distm_Icon_Walker(),
                                    'container' => false,
                                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'fallback_cb' => false,
                                    'link' => false
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $settings = get_option('distm_settings', array());
                    $addon_menu_style = isset($settings['distm_addon_menu_style']) ? $settings['distm_addon_menu_style'] : 'app-icon';
                    ?>
                    <div class="tm-addon-menu-wrapper <?php echo esc_attr($addon_menu_style); ?>">
                        <div id="tm-addon-menu">
                            <?php 
                            wp_nav_menu(array(
                                'theme_location' => 'addon-menu',
                                'walker' => new distm_Icon_Walker(),
                                'container' => false,
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'fallback_cb' => false
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="tm-featured">
                        <a href="#" class="tm-menu-item">
                            <div class="tm-icon-wrapper">
                                <div class="tm-featured-icon">
                                    <?php 
                                    $settings = get_option('distm_settings');
                                    $icon_type = $settings['distm_featured_icon_type'] ?? 'dashicon';
                                    $dashicon = $settings['distm_featured_dashicon'] ?? 'menu';
                                    $icon_url = $settings['distm_featured_icon'] ?? '';
                                    
                                    if ($icon_type === 'dashicon') {
                                        echo '<span class="dashicons dashicons-' . esc_attr($dashicon) . '"></span>';
                                    } elseif ($icon_type === 'upload' && !empty($icon_url)) {
                                        if (substr($icon_url, -4) === '.svg') {
                                            echo wp_kses(distm_get_svg_content($icon_url), distm_get_allowed_svg_tags());
                                        } else {
                                            echo '<img src="' . esc_url($icon_url) . '" alt="Featured Icon" />';
                                        }
                                    } else {
                                        echo '<span class="dashicons dashicons-menu"></span>';
                                    }
                                    ?>
                                </div>
                                <svg class="tm-menu-close" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 50 50">
                                    <path d="M30.7,25l16.4-16.4c1.6-1.6,1.6-4.1,0-5.7-1.6-1.6-4.1-1.6-5.7,0l-16.4,16.4L8.6,2.9c-1.6-1.6-4.1-1.6-5.7,0-1.6,1.6-1.6,4.1,0,5.7l16.4,16.4L2.9,41.4c-1.6,1.6-1.6,4.1,0,5.7h0c1.6,1.6,4.1,1.6,5.7,0l16.4-16.4,16.4,16.4c1.6,1.6,4.1,1.6,5.7,0h0c1.6-1.6,1.6-4.1,0-5.7l-16.4-16.4Z"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="preview-notice">
        This preview updates in real-time as you modify the settings. Changes are shown immediately before saving.
    </div>

<script>

    document.addEventListener('DOMContentLoaded', function() {
    const settings = <?php echo json_encode(get_option('distm_settings')); ?>;
    const previewFrame = document.querySelector('.preview-frame');
    let customUploader = null;

    // Color mapping with defaults
    const colorMap = {
        'distm_background_color': ['--distm-background-color', '#333333'],
        'distm_icon_color': ['--distm-icon-color', '#777777'],
        'distm_label_color': ['--distm-label-color', '#FFFFFF'],
        'distm_featured_background_color': ['--distm-featured-background-color', '#446084'],
        'distm_featured_icon_color': ['--distm-featured-icon-color', '#FFFFFF'],
        'distm_addon_bg_color': ['--distm-addon-bg-color', '#000000'],
        'distm_addon_label_color': ['--distm-addon-label-color', '#FFFFFF'],
        'distm_addon_icon_color': ['--distm-addon-icon-color', '#FFFFFF'],
        'distm_addon_icon_bg': ['--distm-addon-icon-bg', '#446084']
    };

    // Set initial colors
    Object.entries(colorMap).forEach(([settingId, [cssVar, defaultColor]]) => {
        const savedColor = settings[settingId] || defaultColor;
        previewFrame.style.setProperty(cssVar, savedColor);
    });

    // Initialize color pickers
    initColorPickers();
    
    // Initialize other features
    initFeaturedIcon();
    initStyleUpdates();
    

    function initializeColors() {
        Object.entries(colorMap).forEach(([settingId, [cssVar, defaultColor]]) => {
            const savedColor = settings[settingId] || defaultColor;
            previewFrame.style.setProperty(cssVar, savedColor);

            const input = document.getElementById(settingId);
            if (input) {
                input.value = savedColor;
                jQuery(input).wpColorPicker('color', savedColor);
            }
        });
    }

    function initStyleUpdates() {
        const styleSelect = document.querySelector('select[name="distm_settings[distm_menu_style]"]');
        const addonStyleSelect = document.querySelector('select[name="distm_settings[distm_addon_menu_style]"]');
        const textToggle = document.querySelector('input[name="distm_settings[distm_disable_menu_text]"]');

        // Set initial addon menu style
        const addonMenuWrapper = document.querySelector('.tm-addon-menu-wrapper');
        if (addonMenuWrapper && settings.distm_addon_menu_style) {
            addonMenuWrapper.className = 'tm-addon-menu-wrapper ' + settings.distm_addon_menu_style;
        }

        if (styleSelect) {
            styleSelect.addEventListener('change', function() {
                const menuWrapper = document.querySelector('.tm-fixed-mobile-menu-wrapper');
                menuWrapper.className = 'tm-fixed-mobile-menu-wrapper ' + this.value;
            });
        }

        if (addonStyleSelect) {
            addonStyleSelect.addEventListener('change', function() {
                if (addonMenuWrapper) {
                    addonMenuWrapper.className = 'tm-addon-menu-wrapper ' + this.value;
                }
            });
        }

        if (textToggle) {
            textToggle.addEventListener('change', function() {
                const menu = document.querySelector('.the-menu');
                menu.classList.toggle('icon-only', this.checked);
            });
        }
    }

    function initColorPickers() {
        jQuery('.color-field').each(function() {
            const $input = jQuery(this);
            const settingId = $input.attr('id');
            const [cssVar, defaultColor] = colorMap[settingId] || ['', '#000000'];
            const savedColor = settings[settingId] || defaultColor;

            // Initialize the color picker
            $input.wpColorPicker({
                defaultColor: savedColor,
                change: function(event, ui) {
                    previewFrame.style.setProperty(cssVar, ui.color.toString());
                },
                clear: function() {
                    previewFrame.style.setProperty(cssVar, defaultColor);
                },
                palettes: true
            });

            // Set initial color
            previewFrame.style.setProperty(cssVar, savedColor);
            
            // Additional handler for real-time updates during color picking
            const $picker = $input.closest('.wp-picker-container');
            const $irisPicker = $picker.find('.iris-picker');
            
            $irisPicker.on('mousedown touchstart', function() {
                const updateColor = function() {
                    const currentColor = $input.iris('color');
                    if (currentColor) {
                        previewFrame.style.setProperty(cssVar, currentColor.toString());
                    }
                };
                
                $irisPicker.on('mousemove touchmove', updateColor);
                
                jQuery(window).one('mouseup touchend', function() {
                    $irisPicker.off('mousemove touchmove', updateColor);
                });
            });

            $picker.find('.wp-picker-input-wrap input[type="text"]').on('input', function() {
                const color = jQuery(this).val();
                if (color) {
                    previewFrame.style.setProperty(cssVar, color);
                }
            });
        });
    }

    // Initialize everything
    initializeColors();
    initColorPickers();
    initFeaturedIcon();
    initStyleUpdates();
});
</script>
</body>
</html>