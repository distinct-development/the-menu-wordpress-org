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
    $visibility = get_post_meta($item_id, '_menu_item_visibility', true);
    $roles = get_post_meta($item_id, '_menu_item_roles', true);
    
    if (!is_array($roles)) {
        $roles = array();
    }
    
    ?>
    <p class="field-custom description description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>">
            <h3 style="clear:both;"><span class="dashicons dashicons-align-full-width"></span> <?php esc_html_e('The Menu icon', 'the-menu'); ?></h3>
            <input type="text" 
                id="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>" 
                class="widefat code edit-menu-item-custom"
                style="width:70%;" 
                name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" 
                value="<?php echo esc_attr($icon); ?>" 
                readonly/>
            <button type="button" 
                class="button upload-icon-button" 
                style="width:29%;"
                data-item-id="<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Upload', 'the-menu'); ?></button>
        </label>
    </p>
    <?php
}
add_filter('wp_nav_menu_item_custom_fields', 'distm_add_custom_menu_fields', 10, 4);

// Save the custom menu fields
function distm_save_custom_menu_fields($menu_id, $menu_item_db_id, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $nonce_field = 'distm_menu_item_' . $menu_item_db_id;
    if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), 'distm_menu_item_' . $menu_item_db_id)) {
        return;
    }

    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        $icon_url = sanitize_text_field(wp_unslash($_POST['menu-item-icon'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_url);
    }

    if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
        $visibility = sanitize_text_field(wp_unslash($_POST['menu-item-visibility'][$menu_item_db_id]));
        if (in_array($visibility, array('everyone', 'logged_in', 'logged_out'), true)) {
            update_post_meta($menu_item_db_id, '_menu_item_visibility', $visibility);
        }
    }

    if (isset($_POST['menu-item-roles'][$menu_item_db_id])) {
        $roles = array_map('sanitize_text_field', wp_unslash($_POST['menu-item-roles'][$menu_item_db_id]));
        $valid_roles = array_intersect($roles, array_keys(wp_roles()->roles));
        update_post_meta($menu_item_db_id, '_menu_item_roles', $valid_roles);
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
        $icon_url = get_post_meta($item->ID, '_menu_item_icon', true);
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url = $item->url;

        $icon_html = '';
        if (!empty($icon_url)) {
            if (substr($icon_url, -4) === '.svg') {
                $svg_content = distm_get_svg_content($icon_url);
                if ($svg_content !== false) {
                    $icon_html = $svg_content;
                }
            } else {
                $icon_html = '<img src="' . esc_url($icon_url) . '" alt="' . esc_attr($title) . ' ' . esc_attr__('Icon', 'the-menu') . '" class="tm-menu-icon" />';
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