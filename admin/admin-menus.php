<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Register the plugin settings
function distm_register_my_menus() {
    register_nav_menus(
        array(
            'left-menu' => esc_html__('[THE MENU] Left Menu', 'the-menu'),
            'right-menu' => esc_html__('[THE MENU] Right Menu', 'the-menu'),
            'addon-menu' => esc_html__('[THE MENU] Add-on Menu', 'the-menu')
        )
    );
}
add_action('after_setup_theme', 'distm_register_my_menus');

// Add custom fields to the menu items
function distm_add_custom_menu_fields($item_id, $item, $depth, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $nonce_field = 'distm_menu_item_' . $item_id;
    wp_nonce_field('distm_menu_item_' . $item_id, $nonce_field);

    $icon = get_post_meta($item_id, '_menu_item_icon', true);
    $icon_type = get_post_meta($item_id, '_menu_item_icon_type', true);
    $dashicon = get_post_meta($item_id, '_menu_item_dashicon', true);

    // Set default values for new items
    if (empty($icon_type)) {
        $icon_type = 'dashicon';  // Set dashicon as default
        $dashicon = 'menu';       // Set a default dashicon
    }
    ?>
    <div class="field-custom description description-wide">
        <h3 style="margin-bottom: 10px;"><span class="dashicons dashicons-align-full-width"></span> <?php esc_html_e('Menu Icon', 'the-menu'); ?></h3>
        
        <!-- Icon Type Selector -->
        <div class="icon-type-selector" style="margin-bottom: 10px;">
            <label class="icon-type-label">
                <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                       value="dashicon" <?php checked($icon_type, 'dashicon'); ?> class="icon-type-radio">
                <?php esc_html_e('Select dashicon', 'the-menu'); ?>
            </label>
            <label class="icon-type-label" style="margin-left: 15px;">
                <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                       value="upload" <?php checked($icon_type, 'upload'); ?> class="icon-type-radio">
                <?php esc_html_e('Upload icon', 'the-menu'); ?>
            </label>
        </div>

        <!-- Upload Icon Section -->
        <div class="icon-upload-section" style="<?php echo $icon_type === 'dashicon' ? 'display: none;' : ''; ?>">
            <div class="icon-preview" style="margin-bottom: 10px;">
                <?php if (!empty($icon) && $icon_type === 'upload'): ?>
                    <img src="<?php echo esc_url($icon); ?>" style="max-width: 40px; height: auto;">
                <?php endif; ?>
            </div>
            <input type="text" 
                id="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>" 
                class="widefat code edit-menu-item-icon"
                style="width: 70%;" 
                name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" 
                value="<?php echo $icon_type !== 'dashicon' ? esc_attr($icon) : ''; ?>" 
                readonly/>
            <button type="button" 
                class="button upload-icon-button" 
                style="width: 29%;"
                data-item-id="<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Upload', 'the-menu'); ?></button>
        </div>

        <!-- Dashicon Selection Section -->
        <div class="dashicon-selection-section" style="<?php echo $icon_type === 'upload' ? 'display: none;' : ''; ?>">
            <input type="text" 
                class="dashicon-search" 
                placeholder="<?php esc_attr_e('Search dashicons...', 'the-menu'); ?>"
                style="width: 100%; margin-bottom: 10px;">
            <div class="dashicon-grid" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                <?php
                // Common Dashicons that might be suitable for menu items
                $dashicons = array(
                    'menu' => __('Menu (Default)', 'the-menu'),
                    'admin-home' => __('Home', 'the-menu'),
                    'admin-users' => __('Users', 'the-menu'),
                    'admin-post' => __('Post', 'the-menu'),
                    'admin-settings' => __('Settings', 'the-menu'),
                    'admin-appearance' => __('Appearance', 'the-menu'),
                    'cart' => __('Cart', 'the-menu'),
                    'category' => __('Category', 'the-menu'),
                    'dashboard' => __('Dashboard', 'the-menu'),
                    'email' => __('Email', 'the-menu'),
                    'calendar' => __('Calendar', 'the-menu'),
                    'format-gallery' => __('Gallery', 'the-menu'),
                    'heart' => __('Heart', 'the-menu'),
                    'info' => __('Info', 'the-menu'),
                    'location' => __('Location', 'the-menu'),
                    'phone' => __('Phone', 'the-menu'),
                    'portfolio' => __('Portfolio', 'the-menu'),
                    'products' => __('Products', 'the-menu'),
                    'search' => __('Search', 'the-menu'),
                    'share' => __('Share', 'the-menu'),
                    'star-empty' => __('Star', 'the-menu'),
                    'tag' => __('Tag', 'the-menu'),
                    'video-alt' => __('Video', 'the-menu'),
                    // Add more commonly used icons here
                );
                // First display commonly used icons
                foreach ($dashicons as $icon_name => $label) {
                    $selected = ($dashicon === $icon_name) ? 'selected' : '';
                    echo sprintf(
                        '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                        esc_attr($icon_name),
                        esc_attr($selected),
                        esc_attr($icon_name),
                        esc_attr($label)
                    );
                }

                // Then add a separator
                echo '<div class="dashicon-separator" style="width: 100%; border-bottom: 1px solid #ddd; margin: 10px 0; grid-column: 1/-1;"></div>';

                // Add the rest of the dashicons
                $all_dashicons = array(
                    'album', 'align-center', 'align-left', 'align-right', 'analytics',
                    'archive', 'art', 'awards', 'backup', 'book', 'book-alt',
                    'building', 'businessman', 'camera', 'chart-area', 'chart-bar',
                    'chart-line', 'chart-pie', 'clipboard', 'clock', 'cloud',
                    'desktop', 'edit', 'editor-help', 'email-alt', 'facebook',
                    'facebook-alt', 'feedback', 'flag', 'format-aside', 'format-audio',
                    'format-chat', 'format-image', 'format-quote', 'format-video',
                    'forms', 'groups', 'id', 'images-alt', 'images-alt2', 'index-card',
                    'layout', 'lightbulb', 'list-view', 'location-alt', 'lock',
                    'marker', 'media-archive', 'media-audio', 'media-code', 'media-default',
                    'media-document', 'media-interactive', 'media-spreadsheet',
                    'media-text', 'media-video', 'megaphone', 'microphone',
                    'migrate', 'money', 'palmtree', 'performance', 'plus',
                    'portfolio', 'post-status', 'pressthis', 'randomize', 'redo',
                    'rss', 'schedule', 'screenoptions', 'share-alt', 'share-alt2',
                    'shield', 'slides', 'smartphone', 'smiley', 'sort',
                    'sos', 'star-filled', 'star-half', 'store', 'tablet',
                    'tagcloud', 'testimonial', 'text', 'thumbs-down', 'thumbs-up',
                    'translation', 'twitter', 'universal-access', 'unlock',
                    'update', 'upload', 'vault', 'video-alt2', 'video-alt3',
                    'visibility', 'welcome-add-page', 'welcome-comments',
                    'welcome-learn-more', 'welcome-view-site', 'welcome-widgets-menus',
                    'wordpress', 'wordpress-alt', 'yes'
                );

                foreach ($all_dashicons as $icon_name) {
                    if (!isset($dashicons[$icon_name])) {  // Skip if already shown above
                        $selected = ($dashicon === $icon_name) ? 'selected' : '';
                        echo sprintf(
                            '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                            esc_attr($icon_name),
                            esc_attr($selected),
                            esc_attr($icon_name),
                            esc_attr($icon_name)
                        );
                    }
                }
                ?>
            </div>
            <input type="hidden" 
                name="menu-item-dashicon[<?php echo esc_attr($item_id); ?>]" 
                class="selected-dashicon" 
                value="<?php echo esc_attr($dashicon); ?>">
        </div>
    </div>
    <?php
}
add_filter('wp_nav_menu_item_custom_fields', 'distm_add_custom_menu_fields', 10, 4);

// Save the custom menu fields
function distm_save_custom_menu_fields($menu_id, $menu_item_db_id, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    // Check if our nonce is set.
    $nonce_field = 'distm_menu_item_' . $menu_item_db_id;
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), 'distm_menu_item_' . $menu_item_db_id)) {
        return;
    }

    // Save icon type
    if (isset($_POST['menu-item-icon-type'][$menu_item_db_id])) {
        $icon_type = sanitize_text_field(wp_unslash($_POST['menu-item-icon-type'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_icon_type', $icon_type);

        // If icon type is dashicon, clear the uploaded icon URL
        if ($icon_type === 'dashicon') {
            update_post_meta($menu_item_db_id, '_menu_item_icon', '');
        }
    }

    // Save uploaded icon URL
    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        $icon_url = sanitize_text_field(wp_unslash($_POST['menu-item-icon'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_url);
    }

    // Save selected dashicon
    if (isset($_POST['menu-item-dashicon'][$menu_item_db_id])) {
        $dashicon = sanitize_text_field(wp_unslash($_POST['menu-item-dashicon'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_dashicon', $dashicon);
    }
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_menu_fields', 10, 3);

// Add the custom visibility field
function distm_add_custom_visibility_field($item_id, $item, $depth, $args) {
    $visibility = get_post_meta($item_id, '_menu_item_visibility', true);
    if (empty($visibility)) {
        $visibility = 'everyone';
    }
    $roles = get_post_meta($item_id, '_menu_item_roles', true);
    if (!is_array($roles)) {
        $roles = array();
    }
    
    $visibility_options = array(
        'everyone' => __('Everyone', 'the-menu'),
        'logged_in' => __('Logged in users', 'the-menu'),
        'logged_out' => __('Logged out users', 'the-menu')
    );

    $available_roles = wp_roles()->roles;
    ?>
    <p class="field-visibility description description-wide">
        <label for="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('Visibility', 'the-menu'); ?><br />
            <select id="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-visibility" name="menu-item-visibility[<?php echo esc_attr($item_id); ?>]">
                <?php foreach ($visibility_options as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($visibility, $key); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>

    <div class="field-roles description description-wide" style="<?php echo $visibility === 'logged_in' ? '' : 'display:none;'; ?>">
        <label for="edit-menu-item-roles-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('User Roles (for logged in users)', 'the-menu'); ?><br />
            <?php foreach ($available_roles as $role_key => $role) : ?>
                <input type="checkbox" id="edit-menu-item-role-<?php echo esc_attr($role_key . '-' . $item_id); ?>" name="menu-item-roles[<?php echo esc_attr($item_id); ?>][]" value="<?php echo esc_attr($role_key); ?>" <?php checked(in_array($role_key, $roles)); ?>>
                <label for="edit-menu-item-role-<?php echo esc_attr($role_key . '-' . $item_id); ?>"><?php echo esc_html($role['name']); ?></label><br>
            <?php endforeach; ?>
            <small><description style="opacity: 0.5;line-height:1em;"><i><?php esc_html_e('Select who can see this menu item. If none are selected, all roles can see it.', 'the-menu'); ?></i></description></small>
        </label>
    </div>
    <?php
    wp_add_inline_script('distm-admin-script', "
        jQuery(document).ready(function($) {
            $('#edit-menu-item-visibility-<?php echo esc_js($item_id); ?>').change(function() {
                if ($(this).val() === 'logged_in') {
                    $(this).closest('.menu-item-settings').find('.field-roles').show();
                } else {
                    $(this).closest('.menu-item-settings').find('.field-roles').hide();
                }
            });
        });
    ");
}
add_filter('wp_nav_menu_item_custom_fields', 'distm_add_custom_visibility_field', 10, 4);

// Save the custom visibility field
function distm_save_custom_visibility_field($menu_id, $menu_item_db_id, $args) {
    if (!isset($_POST['distm_custom_menu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['distm_custom_menu_nonce'])), 'distm_custom_menu_fields')) {
        return;
    }

    if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
        $visibility = sanitize_text_field(wp_unslash($_POST['menu-item-visibility'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_visibility', $visibility);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_visibility');
    }
    
    if (isset($_POST['menu-item-roles'][$menu_item_db_id])) {
        $roles = array_map('sanitize_text_field', wp_unslash($_POST['menu-item-roles'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_roles', $roles);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_roles');
    }
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_visibility_field', 10, 3);

// WordPress Walker for the menu
class DISTM_Icon_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth=0, $args=null, $id=0) {
        $icon_type = get_post_meta($item->ID, '_menu_item_icon_type', true);
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url = $item->url;
        $icon_html = '';

        if ($icon_type === 'dashicon') {
            $dashicon = get_post_meta($item->ID, '_menu_item_dashicon', true);
            if (!empty($dashicon)) {
                $icon_html = sprintf('<span class="dashicons dashicons-%s" aria-hidden="true"></span>', esc_attr($dashicon));
            }
        } else {
            $icon_url = get_post_meta($item->ID, '_menu_item_icon', true);
            if (!empty($icon_url)) {
                if (substr($icon_url, -4) === '.svg') {
                    $svg_content = distm_get_svg_content($icon_url);
                    if ($svg_content !== false) {
                        $icon_html = $svg_content;
                    }
                } else {
                    $icon_html = sprintf('<img src="%s" alt="%s %s" class="tm-menu-icon" />', 
                        esc_url($icon_url), 
                        esc_attr($title), 
                        esc_attr__('Icon', 'the-menu')
                    );
                }
            }
        }

        $visibility = get_post_meta($item->ID, '_menu_item_visibility', true);
        if (empty($visibility)) {
            $visibility = 'everyone';
        }
        $roles = get_post_meta($item->ID, '_menu_item_roles', true);
        if (!is_array($roles)) {
            $roles = array();
        }

        $show_item = false;
        if ($visibility === 'everyone') {
            $show_item = true;
        } elseif ($visibility === 'logged_in' && is_user_logged_in()) {
            if (empty($roles) || array_intersect($roles, wp_get_current_user()->roles)) {
                $show_item = true;
            }
        } elseif ($visibility === 'logged_out' && !is_user_logged_in()) {
            $show_item = true;
        }

        if ($show_item) {
            $output .= '<li class="tm-menu-item-' . esc_attr($item->ID) . '">';
            
            // Get the 'don't display menu text' setting
            $options = get_option('distm_settings');
            $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];

            $output .= '<a href="' . esc_url($url) . '">' . $icon_html;
            
            // Only add the title span if 'don't display menu text' is not enabled
            if (!$hide_text) {
                $output .= '<span class="tm-menu-item-title">' . esc_html($title) . '</span>';
            }
            
            $output .= '</a>';
        }
    }
}