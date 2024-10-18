<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue the plugin styles
function distm_enqueue_styles() {
    // Check if we're on the front-end
    if (!is_admin()) {
        wp_enqueue_style('distm-style', plugin_dir_url(__FILE__) . '../css/style.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'distm_enqueue_styles');

// Add custom colors to the plugin
function distm_custom_colors() {
    $options = get_option('distm_settings', array());
    
    // Default values
    $defaults = array(
        'distm_background_color' => '#333333',
        'distm_icon_color' => '#777777',
        'distm_label_color' => '#FFFFFF',
        'distm_featured_background_color' => '#446084',
        'distm_featured_icon_color' => '#FFFFFF',
        'distm_addon_bg_color' => 'rgba(255,255,255,0.6)',
        'distm_addon_label_color' => '#333333',
        'distm_addon_icon_color' => '#446084',
        'distm_addon_icon_bg' => '#333333'
    );

    // Merge and sanitize options
    $options = wp_parse_args($options, $defaults);
    foreach ($options as $key => $value) {
        if (strpos($key, 'color') !== false) {
            $options[$key] = sanitize_hex_color($value);
        }
    }

    $custom_css = "
    .the-menu {
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
    #tm-pageLoader .custom-loader { background-color: " . esc_attr($options['distm_featured_background_color']) . "!important; }
    
    /* Apply colors directly to elements */
    #tm-fixed-mobile-menu { background-color: var(--distm-background-color); }
    .the-menu .tm-icon-wrapper svg path { fill: var(--distm-icon-color); }
    .the-menu a { color: var(--distm-label-color); }
    .tm-featured .tm-menu-item .tm-icon-wrapper, .custom-loader { background-color: var(--distm-featured-background-color)!important; }
    .tm-featured svg path { fill: var(--distm-featured-icon-color); }
    .tm-addon-menu-wrapper { background-color: var(--distm-addon-bg-color); }
    #tm-addon-menu a { color: var(--distm-addon-label-color); }
    #tm-addon-menu svg path { fill: var(--distm-addon-icon-color); }
    #tm-addon-menu svg, #tm-addon-menu img { background-color: var(--distm-addon-icon-bg); }
    ";

    wp_add_inline_style('distm-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'distm_custom_colors', 20);

// Function to convert hex color to rgba
if (!function_exists('distm_hex_to_rgba')) {
    function distm_hex_to_rgba($color, $opacity = 1) {
        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if(empty($color))
            return $default; 

        //Sanitize $color if "#" is provided 
        if ($color[0] == '#' ) {
            $color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return $default;
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
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

    $featured_icon_url = isset($options['distm_featured_icon']) ? esc_url($options['distm_featured_icon']) : '';
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
                    <?php if (!empty($featured_icon_url)) : ?>
                        <div class="tm-featured-icon">
                            <?php if ($is_svg) : ?>
                                <?php echo wp_kses(distm_get_svg_content($featured_icon_url), distm_get_allowed_svg_tags()); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url($featured_icon_url); ?>" alt="<?php esc_attr_e('Featured Icon', 'the-menu'); ?>" />
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div class="tm-featured-icon">
                            <?php echo wp_kses(distm_get_svg_content(plugin_dir_url(__FILE__) . '../admin/assets/menu.svg'), distm_get_allowed_svg_tags()); ?>
                        </div>
                    <?php endif; ?>
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