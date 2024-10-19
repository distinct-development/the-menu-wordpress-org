<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Enqueue the plugin styles
function distm_enqueue_admin_scripts($hook_suffix) {
    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings' || $hook_suffix == 'nav-menus.php') {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('distm-admin-script', plugin_dir_url(__FILE__) . 'js/scripts.js', array('jquery', 'wp-color-picker', 'media-upload'), '1.0.1', true);
    }
    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings'){
        wp_enqueue_style('distm-admin-style', plugin_dir_url(__FILE__) . 'css/styles.css', array(), '1.0.1');
    }
}
add_action('admin_enqueue_scripts', 'distm_enqueue_admin_scripts');

// Add the plugin settings page
function distm_add_admin_menu() {
    add_menu_page(
        __('The Menu', 'the-menu'),
        __('The Menu', 'the-menu'),
        'manage_options',
        'the-menu',
        'distm_the_menu_page',
        'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path(__FILE__) . 'assets/menu-logo.svg')),
        90
    );
}
add_action('admin_menu', 'distm_add_admin_menu');

// Add the plugin settings page
function distm_the_menu_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['submit']) && check_admin_referer('distm_the_menu_settings_nonce', 'distm_the_menu_settings_nonce')) {
        $settings = isset($_POST['distm_settings']) ? array_map('sanitize_text_field', wp_unslash($_POST['distm_settings'])) : array();
        $settings = distm_settings_sanitize($settings);
        update_option('distm_settings', $settings);
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved.', 'the-menu') . '</p></div>';
    }

    function echo_plugin_name() {
        $plugin_file = plugin_dir_path(__DIR__) . 'the-menu.php';
        $plugin_data = get_plugin_data($plugin_file);
        if (!isset($plugin_data['Name'])) {
            esc_html_e('Error: Unable to retrieve plugin data.', 'the-menu');
            return;
        }
        echo esc_html($plugin_data['Name']);
    }

    function echo_plugin_description() {
        $plugin_file = plugin_dir_path(__DIR__) . 'the-menu.php';
        $plugin_data = get_plugin_data($plugin_file);
        if (!isset($plugin_data['Description'])) {
            esc_html_e('Error: Unable to retrieve plugin data.', 'the-menu');
            return;
        }
        echo wp_kses_post($plugin_data['Description']);
    }

    function echo_plugin_version() {
        $plugin_file = plugin_dir_path(__DIR__) . 'the-menu.php';
        $plugin_data = get_plugin_data($plugin_file);
        if (!isset($plugin_data['Version'])) {
            esc_html_e('Error: Unable to retrieve plugin data.', 'the-menu');
            return;
        }
        echo esc_html($plugin_data['Version']);
    }
    ?>
    <div class="tm-banner">
    <video autoplay muted loop playsinline id="myVideo">
        <source src="<?php echo esc_url(plugins_url('/assets/video.mp4', __FILE__)); ?>" type="video/mp4">
        <?php esc_html_e('Your browser does not support HTML5 video.', 'the-menu'); ?>
    </video>
    <div class="tm-banner-bg">
        <h1><span><?php echo_plugin_name(); ?></span></h1>
        <p class="version"><?php esc_html_e('Version', 'the-menu'); ?> <?php echo_plugin_version(); ?></p>
        <p><?php echo_plugin_description(); ?></p>
        <br>
        <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="btn"><span class="dashicons dashicons-edit-large"></span> <?php esc_html_e('Edit menus', 'the-menu'); ?></a>
    </div>
</div>
    <div class="tm-wrap">
        <div class="tm-left-wrapper">
        <form action="" method="post">
            <?php
            settings_fields('distmGeneral');
            do_settings_sections('distmGeneral');
            wp_nonce_field('distm_the_menu_settings_nonce', 'distm_the_menu_settings_nonce');
            submit_button(__('Save Settings', 'the-menu'));
            ?>
        </form>
        </div>
        <div class="tm-right-wrapper">
            <h2><?php esc_html_e('Menu items', 'the-menu'); ?></h2>
            <p><?php esc_html_e("Three menu locations ('Left Menu', 'Right Menu', 'Add-on Menu') have been registered. You can add and edit menu items from the", 'the-menu'); ?> <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php esc_html_e('Menus page', 'the-menu'); ?></a> <?php esc_html_e('in WordPress. Follow the steps below to set up your menus:', 'the-menu'); ?></p>
            <ol>
                <li><?php esc_html_e('Go to the', 'the-menu'); ?> <strong><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>"><?php esc_html_e('Menus page', 'the-menu'); ?></a></strong> <?php esc_html_e('in the Appearance section of your WordPress admin panel.', 'the-menu'); ?></li>
                <li><?php esc_html_e("Click on 'create a new menu' at the top of the page to start building a new menu.", 'the-menu'); ?></li>
                <li><?php esc_html_e('Enter a name for your menu in the', 'the-menu'); ?> <strong><?php esc_html_e('Menu Name', 'the-menu'); ?></strong> <?php esc_html_e('box and click the', 'the-menu'); ?> <strong><?php esc_html_e('Create Menu', 'the-menu'); ?></strong> <?php esc_html_e('button.', 'the-menu'); ?></li>
                <li><?php esc_html_e('Once the menu is created, you can add items to it from the left-hand panels like Pages, Posts, Custom Links, and Categories by selecting the appropriate checkboxes and clicking', 'the-menu'); ?> <strong><?php esc_html_e('Add to Menu', 'the-menu'); ?></strong>.</li>
                <li><?php esc_html_e('After adding items, you can drag and drop them to arrange the order and structure of the menu.', 'the-menu'); ?></li>
                <li><?php esc_html_e("Scroll down to the bottom of the menu editor page to 'Menu Settings'.", 'the-menu'); ?></li>
                <li><?php esc_html_e('Under', 'the-menu'); ?> <strong><?php esc_html_e('Display location', 'the-menu'); ?></strong>, <?php esc_html_e('check the boxes for the locations where you want this menu to appear:', 'the-menu'); ?>
                    <ul>
                        <li><strong><?php esc_html_e('Left Menu:', 'the-menu'); ?></strong> <?php esc_html_e('Typically used for the primary navigation on the left side.', 'the-menu'); ?></li>
                        <li><strong><?php esc_html_e('Right Menu:', 'the-menu'); ?></strong> <?php esc_html_e('Ideal for a secondary navigation on the right side.', 'the-menu'); ?></li>
                        <li><strong><?php esc_html_e('Add-on Menu:', 'the-menu'); ?></strong> <?php esc_html_e("Useful for additional navigation needs that don't fit in the primary or secondary menus.", 'the-menu'); ?></li>
                    </ul>
                </li>
                <li><?php esc_html_e('Click the', 'the-menu'); ?> <strong><?php esc_html_e('Save Menu', 'the-menu'); ?></strong> <?php esc_html_e('button to save your menu.', 'the-menu'); ?></li>
            </ol>
        </div>
    </div>
    
<?php
    include_once('assets/logo-wrapper.php');
}

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

// Sanitize the plugin settings
function distm_add_custom_menu_fields($item_id, $item, $depth, $args) {
    $icon = get_post_meta($item_id, '_menu_item_icon', true);
    wp_nonce_field('distm_custom_menu_fields', 'distm_custom_menu_nonce');
    ?>
    <p class="field-custom description description-wide">
        <label for="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>">
            <h3 style="clear:both;"><span class="dashicons dashicons-align-full-width"></span> <?php esc_html_e('The Menu icon', 'the-menu'); ?></h3>
            <input type="text" id="edit-menu-item-icon-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-custom"
                style="width:70%;" name="menu-item-icon[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($icon); ?>" readonly/>
            <button type="button" class="button upload-icon-button" style="width:29%;"
                data-item-id="<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Upload', 'the-menu'); ?></button><br>
            <description style="opacity: 0.5;"><small><i><?php esc_html_e('You will be able to change icon colour on SVGs with simple paths only.', 'the-menu'); ?></i></small></description>
        </label>
    </p>
    <?php
}
add_filter('wp_nav_menu_item_custom_fields', 'distm_add_custom_menu_fields', 10, 4);


// Save the custom menu fields
function distm_save_custom_menu_fields($menu_id, $menu_item_db_id, $args) {
    if (!isset($_POST['distm_custom_menu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['distm_custom_menu_nonce'])), 'distm_custom_menu_fields')) {
        return;
    }

    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        $icon_url = sanitize_text_field(wp_unslash($_POST['menu-item-icon'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_url);
    }
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_menu_fields', 10, 3);

// Sanitize the plugin settings
function distm_get_svg_content($url) {
    if (substr($url, -4) !== '.svg') {
        return false;
    }

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return false;
    }

    $svg_content = wp_remote_retrieve_body($response);
    if (empty($svg_content)) {
        return false;
    }

    return $svg_content;
}

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

function distm_filter_null_values($value) {
    return $value !== null;
}

// Initialize the plugin settings
function distm_settings_init() {
    register_setting('distmGeneral', 'distm_settings', array(
        'sanitize_callback' => 'distm_settings_sanitize',
        'default' => array()
    ));

    add_settings_section(
        'distm_general_section', 
        __('General settings', 'the-menu'), 
        'distm_general_section_callback', 
        'distmGeneral'
    );

    add_settings_section(
        'distm_customization_section', 
        __('Customise', 'the-menu'), 
        'distm_customization_section_callback', 
        'distmGeneral'
    );

    $general_fields = [
        ['id' => 'distm_enable_mobile_menu', 'title' => __('Enable menu', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section'],
        ['id' => 'distm_only_on_mobile', 'title' => __('Only on mobile', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section'],
        ['id' => 'distm_enable_transparency', 'title' => __('Enable transparency on scroll', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('This will make the menu transparent during scroll.', 'the-menu')]],
        ['id' => 'distm_enable_loader_animation', 'title' => __('Enable loader animation', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Displays a loader animation to prevent "double-clicks" The Menu.', 'the-menu')]],
        ['id' => 'distm_enable_addon_menu', 'title' => __('Enable addon menu', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Enables an additional menu for more links.', 'the-menu')]],
        ['id' => 'distm_disable_menu_text', 'title' => __('Disable menu text', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Display only icons in the menu, no text.', 'the-menu')]]
    ];

    $customization_fields = [
        ['id' => 'distm_menu_style', 'title' => __('Menu style', 'the-menu'), 'callback' => 'distm_dropdown_field_callback', 'section' => 'distm_customization_section', 'args' => ['choices' => ['pill' => __('Pill', 'the-menu'), 'rounded' => __('Rounded', 'the-menu'), 'flat' => __('Flat', 'the-menu')], 'description' => __('Choose the general style for The Menu.', 'the-menu')]],
        ['id' => 'distm_background_color', 'title' => __('Background colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the background color for the menu.', 'the-menu')]],
        ['id' => 'distm_icon_color', 'title' => __('Icon colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the color for the menu icons.', 'the-menu')]],
        ['id' => 'distm_label_color', 'title' => __('Label colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the color for the text labels in the menu.', 'the-menu')]],
        ['id' => 'distm_featured_icon', 'title' => __('Featured icon', 'the-menu'), 'callback' => 'distm_upload_field_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Upload a custom icon to be used as the featured menu item.', 'the-menu')]],
        ['id' => 'distm_featured_background_color', 'title' => __('Featured background color', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the background color for the featured menu item.', 'the-menu')]],
        ['id' => 'distm_featured_icon_color', 'title' => __('Featured icon colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the icon color for the featured menu item.', 'the-menu')]],
        ['id' => 'distm_addon_menu_style', 'title' => __('Addon menu style', 'the-menu'), 'callback' => 'distm_dropdown_field_callback', 'section' => 'distm_customization_section', 'args' => ['choices' => ['app-icon' => __('App icon', 'the-menu'), 'icon' => __('Icon', 'the-menu'), 'list' => __('List', 'the-menu')], 'description' => __('Choose the general style for the add-on menu items.', 'the-menu')]],
        ['id' => 'distm_addon_bg_color', 'title' => __('Add-on background colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the background color for the add-on menu.', 'the-menu')]],
        ['id' => 'distm_addon_label_color', 'title' => __('Add-on label colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the label color for the add-on menu items.', 'the-menu')]],
        ['id' => 'distm_addon_icon_bg', 'title' => __('Add-on icon colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the icon color for the add-on menu items.', 'the-menu')]],
        ['id' => 'distm_addon_icon_color', 'title' => __('Add-on icon background colour', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Set the icon background color for the add-on menu items.', 'the-menu')]]
    ];
    
    add_settings_section(
        'distm_page_exclusion_section', 
        __('Page exclusion settings', 'the-menu'), 
        'distm_page_exclusion_section_callback', 
        'distmGeneral'
    );

    add_settings_field(
        'distm_exclude_pages',
        __('Exclude menu on these pages', 'the-menu'),
        'distm_pages_field_callback',
        'distmGeneral',
        'distm_page_exclusion_section',
        [
            'label_for' => 'distm_exclude_pages',
            'class' => 'distm_row'
        ]
    );

    foreach ($general_fields as $field) {
        add_settings_field(
            $field['id'],
            $field['title'],
            $field['callback'],
            'distmGeneral',
            $field['section'], 
            [
                'label_for' => $field['id'],
                'class' => 'distm_row',
                'description' => $field['args']['description'] ?? ''
            ]
        );
    }
    
    foreach ($customization_fields as $field) {
        add_settings_field(
            $field['id'],
            $field['title'],
            $field['callback'],
            'distmGeneral',
            $field['section'],
            [
                'label_for' => $field['id'],
                'class' => 'distm_row',
                'choices' => $field['args']['choices'] ?? [],
                'description' => $field['args']['description'] ?? ''
            ]
        );
    }
}
add_action('admin_init', 'distm_settings_init');

// Add the page exclusion settings
function distm_pages_field_callback($args) {
    $options = get_option('distm_settings');
    $selected_pages = isset($options['distm_exclude_pages']) ? (array)$options['distm_exclude_pages'] : array();

    $pages = get_pages();
    echo "<select id='distm_exclude_pages' name='distm_settings[distm_exclude_pages][]' multiple='multiple' class='widefat' style='height: 150px;'>";
    foreach ($pages as $page) {
        $selected = in_array($page->ID, $selected_pages) ? 'selected' : '';
        echo "<option value='" . esc_attr($page->ID) . "' " . esc_attr($selected) . ">" . esc_html($page->post_title) . "</option>";
    }
    echo "</select>";
    echo "<p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> " . esc_html__('Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'the-menu') . "</small></p>";
}

// Sanitize the plugin settings
function distm_settings_sanitize($input) {
    $sanitized_input = array();
    foreach ($input as $key => $value) {
        switch ($key) {
            case 'distm_exclude_pages':
                $sanitized_input[$key] = !empty($value) ? array_map('absint', (array)$value) : array();
                break;
            case 'distm_background_color':
            case 'distm_icon_color':
            case 'distm_label_color':
            case 'distm_featured_background_color':
            case 'distm_featured_icon_color':
            case 'distm_addon_bg_color':
            case 'distm_addon_label_color':
            case 'distm_addon_icon_bg':
            case 'distm_addon_icon_color':
                $sanitized_input[$key] = sanitize_hex_color($value);
                break;
            case 'distm_featured_icon':
                $sanitized_input[$key] = esc_url_raw($value);
                break;
            case 'distm_enable_mobile_menu':
            case 'distm_only_on_mobile':
            case 'distm_enable_transparency':
            case 'distm_enable_loader_animation':
            case 'distm_enable_addon_menu':
            case 'distm_disable_menu_text':
                $sanitized_input[$key] = isset($value) ? (bool)$value : false;
                break;
            default:
                if (is_array($value)) {
                    $sanitized_input[$key] = array_map('sanitize_text_field', $value);
                } else {
                    $sanitized_input[$key] = sanitize_text_field($value);
                }
        }
    }
    return $sanitized_input;
}

// Callback functions
function distm_general_section_callback() {
    esc_html_e('General settings for The Menu plugin.', 'the-menu');
}

function distm_customization_section_callback() {
    esc_html_e('Customise the styles and colours for The Menu.', 'the-menu');
}

function distm_checkbox_field_callback($args) {
    $options = get_option('distm_settings');
    $value = $options[$args['label_for']] ?? '';
    $checked = $value ? 'checked' : '';
    echo "<input id='" . esc_attr($args['label_for']) . "' name='distm_settings[" . esc_attr($args['label_for']) . "]' type='checkbox' value='1' " . esc_attr($checked) . " class='" . esc_attr($args['class']) . "'>";
    if (!empty($args['description'])) {
        echo "<label for='" . esc_attr($args['label_for']) . "'>" . esc_html($args['description']) . "</label>";
    }
}

function distm_dropdown_field_callback($args) {
    $options = get_option('distm_settings');
    $selected_value = $options[$args['label_for']] ?? '';

    echo "<select id='" . esc_attr($args['label_for']) . "' name='distm_settings[" . esc_attr($args['label_for']) . "]'>";
    foreach ($args['choices'] as $key => $label) {
        $selected = ($selected_value === $key) ? 'selected' : '';
        echo "<option value='" . esc_attr($key) . "' " . esc_attr($selected) . ">" . esc_html($label) . "</option>";
    }
    echo "</select>";

    if (!empty($args['description'])) {
        echo "<p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> " . esc_html($args['description']) . "</small></p>";
    }
}

function distm_color_picker_callback($args) {
    $options = get_option('distm_settings');
    $value = $options[$args['label_for']] ?? '';
    echo "<input type='text' id='" . esc_attr($args['label_for']) . "' name='distm_settings[" . esc_attr($args['label_for']) . "]' value='" . esc_attr($value) . "' class='color-field' />";
    if (isset($args['description'])) {
        echo "<p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> " . esc_html($args['description']) . "</small></p>";
    }
}

function distm_upload_field_callback($args) {
    $options = get_option('distm_settings');
    $value = $options[$args['label_for']] ?? '';

    echo "<input type='text' id='" . esc_attr($args['label_for']) . "' name='distm_settings[" . esc_attr($args['label_for']) . "]' style='width:70%;margin-right:5px;' value='" . esc_attr($value) . "' class='widefat code edit-menu-item-custom' readonly/>";
    echo "<button type='button' class='button tm-upload-button'>" . esc_html__('Upload', 'the-menu') . "</button>";

    if (!empty($args['description'])) {
        echo "<p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> " . esc_html($args['description']) . "</small></p>";
    }
}

function distm_page_exclusion_section_callback() {
    esc_html_e('Select the pages where the menu should not be displayed.', 'the-menu');
}

function distm_allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'distm_allow_svg_upload');


// Sanitize SVG uploads
function distm_sanitize_svg($file) {
    // Check if the file is an SVG
    if ($file['type'] === 'image/svg+xml') {
        $response = wp_remote_get($file['tmp_name']);
        
        if (is_wp_error($response)) {
            return $file; 
        }

        $file_content = wp_remote_retrieve_body($response);

        $dom = new DOMDocument();
        $dom->loadXML($file_content);

        // Remove potentially harmful elements and attributes
        $scripting_elements = array('script', 'use', 'foreignObject');
        $elements = $dom->getElementsByTagName('*');

        foreach ($elements as $element) {
            if (in_array($element->tagName, $scripting_elements)) {
                $element->parentNode->removeChild($element);
            }
            // Remove on* attributes
            $attributes = $element->attributes;
            for ($i = $attributes->length - 1; $i >= 0; $i--) {
                $attribute = $attributes->item($i);
                if (strpos($attribute->name, 'on') === 0) {
                    $element->removeAttribute($attribute->name);
                }
            }
        }

        // Save sanitized SVG back to the temp file using WP_Filesystem
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once (ABSPATH . '/wp-admin/includes/file.php');
            WP_Filesystem();
        }
        $wp_filesystem->put_contents($file['tmp_name'], $dom->saveXML());
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'distm_sanitize_svg');