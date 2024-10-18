<?php
function distinct_themenu_custom_colors() {
    $options = get_option('distinct_themenu_settings', array());
    
    // If the option doesn't exist or is empty, use default values
    if (empty($options)) {
        $options = array(
            'tm_background_color' => '#333333',
            'tm_icon_color' => '#777777',
            'tm_label_color' => '#FFFFFF',
            'tm_featured_background_color' => '#446084',
            'tm_featured_icon_color' => '#FFFFFF',
            'tm_addon_bg_color' => '#FFFFFF',
            'tm_addon_label_color' => '#333333',
            'tm_addon_icon_color' => '#446084',
            'tm_addon_icon_bg' => '#333333'
        );
    }

    $custom_css = ":root {
        --tm-background-color: " . esc_attr($options['tm_background_color']) . ";
        --tm-icon-color: " . esc_attr($options['tm_icon_color']) . ";
        --tm-label-color: " . esc_attr($options['tm_label_color']) . ";
        --tm-featured-background-color: " . esc_attr($options['tm_featured_background_color']) . ";
        --tm-featured-icon-color: " . esc_attr($options['tm_featured_icon_color']) . ";
        --tm-addon-bg-color: " . esc_attr(distinct_themenu_hex_to_rgba($options['tm_addon_bg_color'], 0.6)) . ";
        --tm-addon-label-color: " . esc_attr($options['tm_addon_label_color']) . ";
        --tm-addon-icon-color: " . esc_attr($options['tm_addon_icon_color']) . ";
        --tm-addon-icon-bg: " . esc_attr($options['tm_addon_icon_bg']) . ";
    }";

    wp_add_inline_style('distinct-themenu-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'distinct_themenu_custom_colors');

// Add this function if it doesn't exist
if (!function_exists('distinct_themenu_hex_to_rgba')) {
    function distinct_themenu_hex_to_rgba($color, $opacity = 1) {
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

function tm_add_fixed_menu() {
    $options = get_option('tm_settings');
    if (empty($options['tm_enable_mobile_menu'])) {
        return;
    }

    $excluded_pages = isset($options['tm_exclude_pages']) ? $options['tm_exclude_pages'] : array();
    
    // Ensure $excluded_pages is an array
    if (!is_array($excluded_pages)) {
        $excluded_pages = array($excluded_pages);
    }

    if (is_page() && in_array(get_queried_object_id(), $excluded_pages)) {
        return;
    }

    if (!empty($options['tm_only_on_mobile']) && !wp_is_mobile()) {
        return;
    }

    $additional_classes = '';
    if (!empty($options['tm_enable_transparency'])) {
        $additional_classes .= ' tm-scrolling';
    }
    if (!empty($options['tm_disable_menu_text'])) {
        $additional_classes .= ' icon-only';
    }
    $menu_style = $options['tm_menu_style'] ?? 'pill';
    $addon_menu_style = $options['tm_addon_menu_style'] ?? 'app-icon';
    $featured_icon_url = $options['tm_featured_icon'] ?? ''; 
    $is_svg = substr($featured_icon_url, -4) === '.svg';
    $link_url = !empty($options['tm_enable_addon_menu']) ? '#' : home_url('/');
    ?>

    <?php if (!empty($options['tm_enable_loader_animation'])) : ?>
    <div id="tm-pageLoader">
        <div>
            <span class="custom-loader"></span>
        </div>
    </div>
    <?php endif; ?>

    <div class="the-menu <?php echo esc_attr($additional_classes); ?>">
        <div class="tm-fixed-mobile-menu-wrapper <?php echo esc_attr($menu_style); ?>">
            <div id="tm-fixed-mobile-menu">
                <div class="tm-left-menu">
                <?php wp_nav_menu(array( 'theme_location' => 'left-menu', 'walker' => new TM_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
                </div>
                <div class="tm-featured-bg">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 140 60" preserveAspectRatio="none">
                    <path d="M139.2,0c-13.3,0-25.3,7.7-31.4,19.5-7.1,13.6-21.3,23-37.8,23s-30.7-9.3-37.8-23S14.2,0,.8,0h-.8v60h140V0h-.8Z"/>
                </svg>
                    <div class="tm-filler"></div>
                </div>
                <div class="tm-right-menu">
                    <?php wp_nav_menu(array( 'theme_location' => 'right-menu', 'walker' => new TM_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
                </div>
            </div>
        </div>
        <?php if (!empty($options['tm_enable_addon_menu'])): ?>
        <div class="tm-addon-menu-wrapper <?php echo esc_attr($addon_menu_style); ?> ">
            <div id="tm-addon-menu">
            <?php wp_nav_menu(array( 'theme_location' => 'addon-menu', 'walker' => new TM_Icon_Walker(), 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'));?>
            </div>
        </div>
        <?php endif; ?>
        <div class="tm-featured">
            <a href="<?php echo esc_url($link_url); ?>" class="tm-menu-item">
                <div class="tm-icon-wrapper">
                    <?php if (!empty($featured_icon_url)) : ?>
                        <div class="tm-featured-icon">
                            <?php if ($is_svg) : ?>
                                <?php echo wp_kses(tm_get_svg_content($featured_icon_url), tm_get_allowed_svg_tags()); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url($featured_icon_url); ?>" alt="<?php esc_attr_e('Featured Icon', 'tm-custom-menu'); ?>" />
                            <?php endif; ?>
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
add_action('wp_footer', 'tm_add_fixed_menu');

// Function to get allowed SVG tags (implement this according to your needs)
function tm_get_allowed_svg_tags() {
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