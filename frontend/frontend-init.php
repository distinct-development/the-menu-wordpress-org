<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue the plugin styles
function distm_enqueue_frontend_scripts() {
    $options = get_option('distm_settings', array());
    $excluded_pages = isset($options['distm_exclude_pages']) ? array_map('absint', (array)$options['distm_exclude_pages']) : array();

    // Default values
    $defaults = array(
        'distm_background_color' => '#333333',
        'distm_icon_color' => '#777777',
        'distm_label_color' => '#FFFFFF',
        'distm_featured_background_color' => '#446084',
        'distm_featured_icon_color' => '#FFFFFF',
        'distm_addon_bg_color' => '#000000',
        'distm_addon_label_color' => '#333333',
        'distm_addon_icon_color' => '#FFFFFF',
        'distm_addon_icon_bg' => '#446084'
    );

    // Helper function to validate and get color value
    function get_valid_color($color_value, $default_color) {
        if (empty($color_value) || $color_value === 'null' || !preg_match('/^#[a-f0-9]{6}$/i', $color_value)) {
            return $default_color;
        }
        return $color_value;
    }

    // Merge options with defaults
    $options = wp_parse_args($options, $defaults);

    // Validate each color value against defaults
    foreach ($defaults as $key => $default_value) {
        if (strpos($key, 'color') !== false || strpos($key, 'bg') !== false) {
            $options[$key] = get_valid_color($options[$key], $default_value);
        }
    }

    if (!is_admin()) {
        $current_page_id = get_queried_object_id();
        if (!in_array($current_page_id, $excluded_pages)) {
            $mobile_menu_enabled = !empty($options['distm_enable_mobile_menu']);
            $only_on_mobile = !empty($options['distm_only_on_mobile']);

            $should_load = $mobile_menu_enabled && (!$only_on_mobile || wp_is_mobile());

            if ($should_load) {
                // First enqueue the base stylesheet
                wp_enqueue_style('distm-style', plugins_url('css/style.css', __FILE__), array(), '1.0.3', 'all');
                
                // Create custom CSS with validated colors
                $custom_css = "
                :root {
                    --distm-background-color: " . esc_attr($options['distm_background_color']) . ";
                    --distm-icon-color: " . esc_attr($options['distm_icon_color']) . ";
                    --distm-label-color: " . esc_attr($options['distm_label_color']) . ";
                    --distm-featured-background-color: " . esc_attr($options['distm_featured_background_color']) . ";
                    --distm-featured-icon-color: " . esc_attr($options['distm_featured_icon_color']) . ";
                    --distm-addon-bg-color: " . esc_attr(distm_hex_to_rgba($options['distm_addon_bg_color'], '0.6')) . ";
                    --distm-addon-label-color: " . esc_attr($options['distm_addon_label_color']) . ";
                    --distm-addon-icon-color: " . esc_attr($options['distm_addon_icon_color']) . ";
                    --distm-addon-icon-bg: " . esc_attr($options['distm_addon_icon_bg']) . ";
                }
                #tm-pageLoader .custom-loader { 
                    background-color: " . esc_attr($options['distm_featured_background_color']) . "; 
                }  
                ";

                // Add the custom CSS after the main stylesheet
                wp_add_inline_style('distm-style', $custom_css);

                // Enqueue the script
                wp_enqueue_script('distm-frontend', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0.3', true);
            }
        }
    }
}
add_action('wp_enqueue_scripts', 'distm_enqueue_frontend_scripts', 100);

// Function to convert hex color to rgba (if this function doesn't exist elsewhere)
if (!function_exists('distm_hex_to_rgba')) {
    function distm_hex_to_rgba($color, $opacity = 1) {
        // If the color is empty or null, return the rgba of the default color
        if (empty($color) || $color === 'null') {
            $color = '#000000'; // Default to black if no color provided
        }

        // Remove # if present
        $color = ltrim($color, '#');

        // Parse the color
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return 'rgba(0,0,0,' . $opacity . ')'; // Return default if invalid hex
        }

        // Convert to rgb values
        $rgb = array_map('hexdec', $hex);

        // Return RGBA string
        return 'rgba(' . implode(',', $rgb) . ',' . $opacity . ')';
    }
}

// Add the fixed mobile menu to the front-end
function distm_add_fixed_menu() {
    $options = get_option('distm_settings', array());
    if (empty($options['distm_enable_mobile_menu'])) {
        return;
    }

    $excluded_pages = isset($options['distm_exclude_pages']) ? array_map('absint', (array)$options['distm_exclude_pages']) : array();

    if (is_page() && in_array(get_queried_object_id(), $excluded_pages)) {
        return;
    }

    if (!empty($options['distm_only_on_mobile']) && !wp_is_mobile()) {
        return;
    }

    $additional_classes = array();
    if (!empty($options['distm_enable_transparency'])) {
        $additional_classes[] = 'tm-scrolling';
    }
    if (!empty($options['distm_disable_menu_text'])) {
        $additional_classes[] = 'icon-only';
    }

    $valid_menu_styles = array('pill', 'rounded', 'flat');
    $menu_style = isset($options['distm_menu_style']) && in_array($options['distm_menu_style'], $valid_menu_styles) 
                ? $options['distm_menu_style'] 
                : 'pill';

    $valid_addon_menu_styles = array('app-icon', 'icon', 'list');
    $addon_menu_style = isset($options['distm_addon_menu_style']) && in_array($options['distm_addon_menu_style'], $valid_addon_menu_styles) 
                        ? $options['distm_addon_menu_style'] 
                        : 'app-icon';

    // Get featured icon URL with fallback to default
    $featured_icon_url = !empty($options['distm_featured_icon']) 
        ? esc_url($options['distm_featured_icon'])
        : plugin_dir_url(dirname(__FILE__)) . 'admin/assets/menu-logo.svg';
    
    $is_svg = substr($featured_icon_url, -4) === '.svg';
    $link_url = !empty($options['distm_enable_addon_menu']) ? '#' : home_url('/');
    ?>

    <?php if (!empty($options['distm_enable_loader_animation'])) : ?>
    <div id="tm-pageLoader">
        <div>
            <span class="custom-loader"></span>
        </div>
    </div>
    <?php endif; ?>

    <div class="the-menu <?php echo esc_attr(implode(' ', $additional_classes)); ?>">
        <div class="tm-fixed-mobile-menu-wrapper <?php echo esc_attr($menu_style); ?>">
            <div id="tm-fixed-mobile-menu">
                <div class="tm-left-menu">
                <?php wp_nav_menu(array( 'theme_location' => 'left-menu', 'walker' => new distm_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
                </div>
                <div class="tm-featured-bg">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 140 60" preserveAspectRatio="none">
                    <path d="M139.2,0c-13.3,0-25.3,7.7-31.4,19.5-7.1,13.6-21.3,23-37.8,23s-30.7-9.3-37.8-23S14.2,0,.8,0h-.8v60h140V0h-.8Z"/>
                </svg>
                    <div class="tm-filler"></div>
                </div>
                <div class="tm-right-menu">
                    <?php wp_nav_menu(array( 'theme_location' => 'right-menu', 'walker' => new distm_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
                </div>
            </div>
        </div>
        <?php if (!empty($options['distm_enable_addon_menu'])): ?>
        <div class="tm-addon-menu-wrapper <?php echo esc_attr($addon_menu_style); ?> ">
            <div id="tm-addon-menu">
            <?php wp_nav_menu(array( 'theme_location' => 'addon-menu', 'walker' => new distm_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
            </div>
        </div>
        <?php endif; ?>
        <div class="tm-featured">
            <a href="<?php echo esc_url($link_url); ?>" class="tm-menu-item">
                <div class="tm-icon-wrapper">
                    <div class="tm-featured-icon">
                        <?php // When displaying the featured icon
                        if ($is_svg) {
                            $svg_content = distm_get_svg_content($featured_icon_url);
                            if ($svg_content !== false) {
                                $allowed_svg_tags = array(
                                    'svg' => array(
                                        'class' => true,
                                        'aria-hidden' => true,
                                        'aria-labelledby' => true,
                                        'role' => true,
                                        'xmlns' => true,
                                        'width' => true,
                                        'height' => true,
                                        'viewbox' => true,
                                        'version' => true,
                                        'preserveaspectratio' => true
                                    ),
                                    'path' => array(
                                        'd' => true,
                                        'fill' => true,
                                    ),
                                    'g' => array(
                                        'fill' => true,
                                    ),
                                    // Add other allowed SVG elements as needed
                                );
                                echo wp_kses($svg_content, $allowed_svg_tags);
                            }
                        } else {
                            echo '<img src="' . esc_url($featured_icon_url) . '" alt="' . esc_attr__('Featured Icon', 'the-menu') . '" />';
                        } ?>
                    </div>
                    <svg class="tm-menu-close" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 50 50">
                        <path d="M30.7,25l16.4-16.4c1.6-1.6,1.6-4.1,0-5.7-1.6-1.6-4.1-1.6-5.7,0l-16.4,16.4L8.6,2.9c-1.6-1.6-4.1-1.6-5.7,0-1.6,1.6-1.6,4.1,0,5.7l16.4,16.4L2.9,41.4c-1.6,1.6-1.6,4.1,0,5.7h0c1.6,1.6,4.1,1.6,5.7,0l16.4-16.4,16.4,16.4c1.6,1.6,4.1,1.6,5.7,0h0c1.6-1.6,1.6-4.1,0-5.7l-16.4-16.4Z"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'distm_add_fixed_menu');

// Function to get allowed SVG tags for wp_kses
function distm_get_allowed_svg_tags() {
    return array(
        'svg' => array(
            'class' => true,
            'aria-hidden' => true,
            'aria-labelledby' => true,
            'role' => true,
            'xmlns' => true,
            'width' => true,
            'height' => true,
            'viewbox' => true,
        ),
        'g' => array('fill' => true),
        'title' => array('title' => true),
        'path' => array(
            'd' => true,
            'fill' => true,
        )
    );
}