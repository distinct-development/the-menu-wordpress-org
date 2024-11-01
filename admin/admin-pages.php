<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Add the plugin settings page
function distm_add_admin_menu() {
    add_menu_page(
        __('The Menu', 'the-menu'),
        __('The Menu', 'the-menu'),
        'manage_options',
        'the-menu',
        'distm_the_menu_page',
        'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path(__FILE__) . 'assets/menu-logo.svg')),
        60
    );
}
add_action('admin_menu', 'distm_add_admin_menu');

function distm_admin_init() {
    // Register the setting
    register_setting(
        'distm_settings', // Option group - used in settings_fields()
        'distm_settings', // Option name in wp_options
        array(
            'sanitize_callback' => 'distm_settings_sanitize',
            'default' => array()
        )
    );
}
add_action('admin_init', 'distm_admin_init');

// Add the plugin settings page
function distm_the_menu_page() {
    global $distm_general_fields, $distm_customization_fields;
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'the-menu'));
    }

    if (isset($_POST['submit'])) {
        // Verify nonce
        check_admin_referer('distm_the_menu_settings_nonce', 'distm_the_menu_settings_nonce');

        $settings = array();
        $raw_input = filter_input(
            INPUT_POST, 
            'distm_settings', 
            FILTER_DEFAULT, 
            FILTER_REQUIRE_ARRAY
        );
        
        if (!empty($raw_input)) {
            $unslashed = wp_unslash($raw_input);
            $sanitized = map_deep($unslashed, 'sanitize_text_field');
            $settings = distm_settings_sanitize($sanitized);
        }
        
        update_option('distm_settings', $settings);
        add_settings_error(
            'distm_messages',
            'distm_message',
            esc_html__('Settings saved.', 'the-menu'),
            'updated'
        );
    }

    settings_errors('distm_messages');

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
        <div class="tm-banner-bg">
            <h1><span><?php echo_plugin_name(); ?></span></h1>
            <p class="version"><?php esc_html_e('Version', 'the-menu'); ?> <?php echo_plugin_version(); ?></p>
            <p><?php echo_plugin_description(); ?></p>
            <br>
            <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="btn">
                <span class="dashicons dashicons-edit-large"></span> 
                <?php esc_html_e('Edit menus', 'the-menu'); ?>
            </a>
        </div>
    </div>

    <div class="tm-wrap">
    <div class="tm-left-wrapper">
            <form method="post" action="options.php">
                <?php settings_fields('distm_settings'); ?>
                
                <div class="settings-wrapper">
                    <!-- General Settings Section -->
                    <div class="settings-section">
                        <h2 class="settings-section-title"><?php esc_html_e('General settings', 'the-menu'); ?></h2>
                        <div class="settings-grid">
                            <?php foreach ($distm_general_fields as $field): ?>
                                <div class="setting-field">
                                    <?php 
                                    call_user_func($field['callback'], [
                                        'label_for' => $field['id'],
                                        'description' => $field['args']['description'] ?? ''
                                    ]); 
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Customization Section -->
                    <div class="settings-section">
                        <h2 class="settings-section-title"><?php esc_html_e('Customise', 'the-menu'); ?></h2>
                        <div class="settings-grid">
                            <?php foreach ($distm_customization_fields as $field): 
                                $field_classes = ['setting-field'];
                                if ($field['callback'] === 'distm_color_picker_callback') {
                                    $field_classes[] = 'distm-colour-picker';
                                }
                                if (isset($field['class'])) {
                                    $field_classes = array_merge($field_classes, explode(' ', $field['class']));
                                }
                                $class_string = implode(' ', array_map('esc_attr', $field_classes));
                                ?>
                                <div class="<?php echo $class_string; ?>">
                                    <label class="setting-label" for="<?php echo esc_attr($field['id']); ?>">
                                        <?php echo esc_html($field['title']); ?>
                                    </label>
                                    <?php 
                                    call_user_func($field['callback'], [
                                        'label_for' => $field['id'],
                                        'choices' => $field['args']['choices'] ?? [],
                                        'description' => $field['args']['description'] ?? ''
                                    ]); 
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Page Exclusion Section -->
                    <div class="settings-section">
                        <h2 class="settings-section-title"><?php esc_html_e('Exclude pages', 'the-menu'); ?></h2>
                        <div class="settings-grid">
                            <div class="setting-field">
                                <label class="setting-label" for="distm_exclude_pages">
                                    <?php esc_html_e('Exclude menu on these pages', 'the-menu'); ?>
                                </label>
                                <?php 
                                distm_pages_field_callback([
                                    'label_for' => 'distm_exclude_pages',
                                    'description' => __('Select the pages where the menu should not be displayed.', 'the-menu')
                                ]); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php submit_button(__('Save settings', 'the-menu')); ?>
            </form>
        </div>

        <div class="tm-right-wrapper">
        <?php include(plugin_dir_path(__FILE__) . 'templates/menu-preview.php'); ?>

        </div>
    </div>

    <?php
    include_once('assets/logo-wrapper.php');
}

$GLOBALS['distm_general_fields'] = [
    ['id' => 'distm_enable_mobile_menu', 'title' => __('Enable menu', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Enable menu', 'the-menu')]],
    ['id' => 'distm_only_on_mobile', 'title' => __('Only on mobile', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Only on mobile', 'the-menu')]],
    ['id' => 'distm_enable_transparency', 'title' => __('Enable transparency on scroll', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Enable transparency on scroll', 'the-menu')]],
    ['id' => 'distm_enable_loader_animation', 'title' => __('Enable loader animation', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Display a loader animation to prevent \'double-clicks\'', 'the-menu')]],
    ['id' => 'distm_enable_addon_menu', 'title' => __('Enable addon menu', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Enable addon menu menu to display more links', 'the-menu')]],
    ['id' => 'distm_disable_menu_text', 'title' => __('Disable menu text', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('Display only icons in the menu, no text', 'the-menu')]],
    ['id' => 'distm_delete_data', 'title' => __('Delete data on uninstall', 'the-menu'), 'callback' => 'distm_checkbox_field_callback', 'section' => 'distm_general_section', 'args' => ['description' => __('If checked, all plugin data and settings will be removed when deleting or uninstalling the plugin from the plugins page', 'the-menu')]]
];

$GLOBALS['distm_customization_fields'] = [
    ['id' => 'distm_menu_style', 'title' => __('Menu style', 'the-menu'), 'callback' => 'distm_dropdown_field_callback', 'section' => 'distm_customization_section', 'args' => ['choices' => ['pill' => __('Pill', 'the-menu'), 'rounded' => __('Rounded', 'the-menu'), 'flat' => __('Flat', 'the-menu')], 'description' => __('Choose the general style for The Menu.', 'the-menu')]],
    ['id' => 'distm_background_color', 'title' => __('Menu bar', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_icon_color', 'title' => __('Icons', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_label_color', 'title' => __('Labels', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_featured_icon', 'title' => __('Featured icon', 'the-menu'), 'callback' => 'distm_upload_field_callback', 'section' => 'distm_customization_section', 'args' => ['description' => __('Choose an icon or dashicon to be used as the featured menu item.', 'the-menu')]],
    ['id' => 'distm_featured_background_color', 'title' => __('Featured background', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_featured_icon_color', 'title' => __('Featured icon', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_addon_menu_style', 'title' => __('Addon menu style', 'the-menu'), 'callback' => 'distm_dropdown_field_callback', 'section' => 'distm_customization_section', 'args' => ['choices' => ['app-icon' => __('App icon', 'the-menu'), 'icon' => __('Icon', 'the-menu'), 'list' => __('List', 'the-menu')], 'description' => __('Choose the general style for the add-on menu items.', 'the-menu')]],
    ['id' => 'distm_addon_bg_color', 'title' => __('Add-on background', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_addon_label_color', 'title' => __('Add-on labels', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_addon_icon_color', 'title' => __('Add-on icons', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section'],
    ['id' => 'distm_addon_icon_bg', 'title' => __('Add-on icons background', 'the-menu'), 'callback' => 'distm_color_picker_callback', 'section' => 'distm_customization_section']
];

// Initialize the plugin settings
function distm_settings_init() {
    global $distm_general_fields, $distm_customization_fields;

    // Register settings fields
    add_settings_section(
        'distm_page_exclusion_section',
        '',
        '',
        'distm_settings' // Change from 'distmGeneral' to 'distm_settings'
    );

    add_settings_field(
        'distm_exclude_pages',
        __('Exclude menu on these pages', 'the-menu'),
        'distm_pages_field_callback',
        'distm_settings', // Change from 'distmGeneral' to 'distm_settings'
        'distm_page_exclusion_section',
        array(
            'label_for' => 'distm_exclude_pages',
            'class' => 'distm_row'
        )
    );
}

// Default settings with all required values
function distm_get_settings() {
    // Define default settings
    $defaults = array(
        'distm_enable_mobile_menu' => false,
        'distm_menu_style' => 'pill',
        'distm_background_color' => '#333333',
        'distm_icon_color' => '#777777',
        'distm_label_color' => '#FFFFFF',
        'distm_featured_background_color' => '#446084',
        'distm_featured_icon_color' => '#FFFFFF',
        'distm_featured_icon' => '',
        'distm_addon_menu_style' => 'app-icon',
        'distm_addon_bg_color' => '#000000',
        'distm_addon_label_color' => '#FFFFFF',
        'distm_addon_icon_bg' => '#446084',
        'distm_addon_icon_color' => '#FFFFFF',
        'distm_enable_transparency' => false,
        'distm_enable_loader_animation' => false,
        'distm_enable_addon_menu' => false,
        'distm_disable_menu_text' => false,
        'distm_only_on_mobile' => false,
        'distm_delete_data' => false,
        'distm_exclude_pages' => array()
    );

    // Get saved settings
    $saved = get_option('distm_settings', array());
    
    // Merge with defaults, ensuring all keys exist
    $settings = wp_parse_args($saved, $defaults);
    
    // Ensure boolean values are properly cast
    $boolean_keys = array(
        'distm_enable_mobile_menu',
        'distm_enable_transparency',
        'distm_enable_loader_animation',
        'distm_enable_addon_menu',
        'distm_disable_menu_text',
        'distm_only_on_mobile',
        'distm_delete_data'
    );
    
    foreach ($boolean_keys as $key) {
        $settings[$key] = (bool) $settings[$key];
    }
    
    // Ensure color values are properly formatted
    $color_keys = array(
        'distm_background_color',
        'distm_icon_color',
        'distm_label_color',
        'distm_featured_background_color',
        'distm_featured_icon_color',
        'distm_addon_bg_color',
        'distm_addon_label_color',
        'distm_addon_icon_bg',
        'distm_addon_icon_color'
    );
    
    foreach ($color_keys as $key) {
        if (empty($settings[$key])) {
            $settings[$key] = $defaults[$key];
        } else {
            // Ensure color starts with #
            if ($settings[$key][0] !== '#') {
                $settings[$key] = '#' . ltrim($settings[$key], '#');
            }
        }
    }

    return $settings;
}

// Add the page exclusion settings
function distm_pages_field_callback($args) {
    $options = get_option('distm_settings');
    $selected_pages = isset($options['distm_exclude_pages']) ? (array)$options['distm_exclude_pages'] : array();

    // Get all pages
    $pages = get_pages(array(
        'sort_column' => 'menu_order,post_title',
        'hierarchical' => true
    ));

    // Group pages by parent
    $page_tree = array();
    foreach ($pages as $page) {
        if ($page->post_parent == 0) {
            $page_tree[$page->ID] = array(
                'page' => $page,
                'children' => array()
            );
        } else {
            if (isset($page_tree[$page->post_parent])) {
                $page_tree[$page->post_parent]['children'][] = $page;
            } else {
                // If parent isn't found (rare case), add to top level
                $page_tree[$page->ID] = array(
                    'page' => $page,
                    'children' => array()
                );
            }
        }
    }

    echo '<div class="page-exclusion-grid">';
    
    // Render parent pages and their children
    foreach ($page_tree as $parent_id => $data) {
        $parent = $data['page'];
        $children = $data['children'];
        
        echo '<div class="page-exclusion-item parent-item">';
        echo '<label class="page-checkbox-label">';
        printf(
            '<input type="checkbox" name="distm_settings[distm_exclude_pages][]" value="%d" %s>',
            esc_attr($parent->ID),
            checked(in_array($parent->ID, $selected_pages), true, false)
        );
        echo '<span class="page-title">' . esc_html($parent->post_title) . '</span>';
        echo '</label>';

        // Render child pages if any exist
        if (!empty($children)) {
            echo '<div class="child-pages">';
            foreach ($children as $child) {
                echo '<label class="page-checkbox-label child-label">';
                printf(
                    '<input type="checkbox" name="distm_settings[distm_exclude_pages][]" value="%d" %s>',
                    esc_attr($child->ID),
                    checked(in_array($child->ID, $selected_pages), true, false)
                );
                echo '<span class="page-title">' . esc_html($child->post_title) . '</span>';
                echo '</label>';
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    echo '</div>';

    if (!empty($args['description'])) {
        echo '<p class="description" style="margin-top: 15px;"><span class="dashicons dashicons-info"></span> ' . 
             esc_html($args['description']) . '</p>';
    }

    // Add styles for the page exclusion grid
    ?>
    <style>
        
    </style>
    <?php
}

// Sanitize the plugin settings
function distm_settings_sanitize($input) {
    if (!is_array($input)) {
        return distm_get_settings();
    }

    $defaults = distm_get_settings();
    $sanitized = array();

    // Define field types for simpler processing
    $field_types = array(
        // Boolean fields
        'booleans' => array(
            'distm_enable_mobile_menu',
            'distm_enable_transparency',
            'distm_enable_loader_animation',
            'distm_enable_addon_menu',
            'distm_disable_menu_text',
            'distm_only_on_mobile',
            'distm_delete_data'
        ),
        // Color fields
        'colors' => array(
            'distm_background_color',
            'distm_icon_color',
            'distm_label_color',
            'distm_featured_background_color',
            'distm_featured_icon_color',
            'distm_addon_bg_color',
            'distm_addon_label_color',
            'distm_addon_icon_bg',
            'distm_addon_icon_color'
        ),
        // Select/dropdown fields with valid options
        'selects' => array(
            'distm_menu_style' => array('pill', 'rounded', 'flat'),
            'distm_addon_menu_style' => array('app-icon', 'icon', 'list')
        )
    );

    foreach ($input as $key => $value) {
        // Handle boolean fields
        if (in_array($key, $field_types['booleans'], true)) {
            $sanitized[$key] = (bool) $value;
            continue;
        }

        // Handle color fields
        if (in_array($key, $field_types['colors'], true)) {
            $color = sanitize_hex_color($value);
            $sanitized[$key] = $color ?: $defaults[$key];
            continue;
        }

        // Handle select fields
        if (isset($field_types['selects'][$key])) {
            $sanitized[$key] = in_array($value, $field_types['selects'][$key], true) ? $value : $defaults[$key];
            continue;
        }

        // Handle special cases
        switch ($key) {
            case 'distm_exclude_pages':
                $sanitized[$key] = !empty($value) ? array_map('absint', (array)$value) : array();
                break;

            case 'distm_featured_icon':
                $sanitized[$key] = esc_url_raw($value);
                break;

            case 'distm_featured_icon_type':
                $sanitized[$key] = in_array($value, array('dashicon', 'upload'), true) ? $value : 'dashicon';
                break;

            case 'distm_featured_dashicon':
                $sanitized[$key] = sanitize_text_field($value);
                break;

            default:
                // For any other fields, use basic sanitization
                $sanitized[$key] = sanitize_text_field($value);
        }
    }

    // Ensure all default keys exist in sanitized output
    $sanitized = wp_parse_args($sanitized, $defaults);

    return $sanitized;
}

function distm_checkbox_field_callback($args) {
    $settings = distm_get_settings();
    $value = $settings[$args['label_for']] ?? false;
    ?>
    <div class="setting-field-input">
        <label class="setting-checkbox">
            <input type="checkbox" 
                   id="<?php echo esc_attr($args['label_for']); ?>" 
                   name="distm_settings[<?php echo esc_attr($args['label_for']); ?>]" 
                   value="1" 
                   <?php checked($value, true); ?>>
            <?php if (!empty($args['description'])): ?>
                <span class='description' style='font-weight:normal;'><?php echo esc_html($args['description']); ?></span>
            <?php endif; ?>
        </label>
    </div>
    <?php
}

function distm_dropdown_field_callback($args) {
    $settings = distm_get_settings();
    $value = $settings[$args['label_for']] ?? '';
    ?>
    <div class="setting-field-input">
        <select id="<?php echo esc_attr($args['label_for']); ?>" 
                name="distm_settings[<?php echo esc_attr($args['label_for']); ?>]">
            <?php foreach ($args['choices'] as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($args['description'])): ?>
            <p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> <?php echo esc_html($args['description']); ?></small></p>
        <?php endif; ?>
    </div>
    <?php
}

function distm_color_picker_callback($args) {
    $settings = distm_get_settings();
    $value = $settings[$args['label_for']] ?? '';
    ?>
    <div class="setting-field-input">
        <input type="text" 
               id="<?php echo esc_attr($args['label_for']); ?>" 
               name="distm_settings[<?php echo esc_attr($args['label_for']); ?>]" 
               value="<?php echo esc_attr($value); ?>" 
               class="color-field">
        <?php if (!empty($args['description'])): ?>
            <p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> <?php echo esc_html($args['description']); ?></small></p>
        <?php endif; ?>
    </div>
    <?php
}

function distm_upload_field_callback($args) {
    $options = get_option('distm_settings', array());
    
    // Get icon type with backward compatibility check
    if (!empty($options['distm_featured_icon']) && empty($options['distm_featured_icon_type'])) {
        $icon_type = 'upload';
    } else {
        $icon_type = isset($options['distm_featured_icon_type']) ? $options['distm_featured_icon_type'] : 'dashicon';
    }
    
    // Get icon values with fallbacks
    $icon_url = isset($options['distm_featured_icon']) ? $options['distm_featured_icon'] : '';
    $dashicon = isset($options['distm_featured_dashicon']) ? $options['distm_featured_dashicon'] : 'menu';
    
    ?>
    <div class="featured-icon-wrapper">
        <!-- Icon Type Selector -->
        <div class="icon-type-selector" style="margin-bottom: 10px;">
            <label class="icon-type-label">
                <input type="radio" name="distm_settings[distm_featured_icon_type]" 
                       value="dashicon" <?php checked($icon_type, 'dashicon'); ?> class="icon-type-radio">
                <?php esc_html_e('Select dashicon', 'the-menu'); ?>
            </label>
            <label class="icon-type-label" style="margin-left: 15px;">
                <input type="radio" name="distm_settings[distm_featured_icon_type]" 
                       value="upload" <?php checked($icon_type, 'upload'); ?> class="icon-type-radio">
                <?php esc_html_e('Upload icon', 'the-menu'); ?>
            </label>
        </div>

        <!-- Upload Icon Section -->
        <div class="icon-upload-section" style="<?php echo $icon_type === 'dashicon' ? 'display: none;' : ''; ?>">
            
            <input type="text" 
                id="distm_featured_icon" 
                name="distm_settings[distm_featured_icon]" 
                style="width:70%;margin-right:5px;" 
                value="<?php echo esc_attr($icon_url); ?>" 
                class="widefat code edit-menu-item-custom" 
                readonly/>
            <button type="button" class="button tm-upload-button"><?php esc_html_e('Upload', 'the-menu'); ?></button>
        </div>

        <!-- Dashicon Selection Section -->
        <div class="dashicon-selection-section" style="<?php echo $icon_type === 'upload' ? 'display: none;' : ''; ?>">
            <input type="text" 
                class="dashicon-search" 
                placeholder="<?php esc_attr_e('Search dashicons...', 'the-menu'); ?>"
                style="width: 100%; margin-bottom: 10px;">
            <div class="dashicon-grid" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                <?php
                // Commonly used menu icons displayed first
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
                    'format-video' => __('Video', 'the-menu'),
                );

                // All available dashicons organized by category with corrected class names
                $all_dashicons = array(
                    // Admin Menu
                    'admin-menu' => array(
                        'menu', 'menu-alt', 'menu-alt2', 'menu-alt3',
                        'admin-site', 'admin-site-alt', 'admin-site-alt2', 'admin-site-alt3',
                        'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page',
                        'admin-comments', 'admin-appearance', 'admin-plugins', 'plugins-checked',
                        'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home',
                        'admin-generic', 'admin-collapse', 'filter', 'admin-customizer', 'admin-multisite'
                    ),
                    
                    // Welcome Screen
                    'welcome' => array(
                        'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus',
                        'welcome-comments', 'welcome-learn-more'
                    ),
                    
                    // Post Formats & Media
                    'post-formats' => array(
                        'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status', 'format-quote',
                        'format-chat', 'format-audio', 'camera-alt', 'camera', 'images-alt',
                        'images-alt2', 'video-alt', 'video-alt2', 'video-alt3'
                    ),
                    
                    // Media
                    'media' => array(
                        'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document',
                        'media-interactive', 'media-spreadsheet', 'media-text', 'media-video',
                        'playlist-audio', 'playlist-video', 'controls-play', 'controls-pause',
                        'controls-forward', 'controls-skipforward', 'controls-back', 'controls-skipback',
                        'controls-repeat', 'controls-volumeon', 'controls-volumeoff'
                    ),
                    
                    // Image Editing
                    'image-editing' => array(
                        'image-crop', 'image-rotate', 'image-rotate-left', 'image-rotate-right',
                        'image-flip-vertical', 'image-flip-horizontal', 'filter',
                        'undo', 'redo'
                    ),
                    
                    // Block Editor
                    'block-editor' => array(
                        'align-full-width', 'align-pull-left', 'align-pull-right',
                        'align-wide', 'block-default', 'button', 'cloud-saved',
                        'cloud-upload', 'columns', 'cover-image', 'ellipsis',
                        'embed-audio', 'embed-generic', 'embed-photo', 'embed-post',
                        'embed-video', 'exit', 'heading', 'html', 'info-outline',
                        'insert', 'insert-after', 'insert-before', 'remove',
                        'saved', 'shortcode', 'table-col-after', 'table-col-before',
                        'table-col-delete', 'table-row-after', 'table-row-before',
                        'table-row-delete'
                    ),
                    
                    // TinyMCE
                    'tinymce' => array(
                        'editor-bold', 'editor-italic', 'editor-ul', 'editor-ol', 'editor-ol-rtl', 'editor-quote',
                        'editor-alignleft', 'editor-aligncenter', 'editor-alignright', 'editor-insertmore',
                        'editor-spellcheck', 'editor-expand', 'editor-contract', 'editor-kitchensink',
                        'editor-underline', 'editor-justify', 'editor-textcolor', 'editor-paste-word',
                        'editor-paste-text', 'editor-removeformatting', 'editor-video', 'editor-customchar',
                        'editor-outdent', 'editor-indent', 'editor-help', 'editor-strikethrough', 'editor-unlink',
                        'editor-rtl', 'editor-ltr', 'editor-break', 'editor-code', 'editor-paragraph', 'editor-table'
                    ),
                    
                    // Posts Screen
                    'posts' => array(
                        'align-left', 'align-right', 'align-center', 'align-none',
                        'lock', 'unlock', 'calendar', 'calendar-alt', 'visibility',
                        'hidden', 'post-status', 'edit', 'trash', 'sticky'
                    ),
                    
                    // Sorting
                    'sorting' => array(
                        'external', 'arrow-up', 'arrow-down', 'arrow-right', 'arrow-left',
                        'arrow-up-alt', 'arrow-down-alt', 'arrow-right-alt', 'arrow-left-alt',
                        'arrow-up-alt2', 'arrow-down-alt2', 'arrow-right-alt2', 'arrow-left-alt2',
                        'sort', 'leftright', 'randomize', 'list-view', 'excerpt-view',
                        'grid-view', 'move'
                    ),
                    
                    // Social
                    'social' => array(
                        'share', 'share-alt', 'share-alt2', 'rss', 'email',
                        'email-alt', 'email-alt2', 'networking', 'amazon',
                        'facebook', 'facebook-alt', 'google', 'instagram',
                        'linkedin', 'pinterest', 'podio', 'reddit', 'spotify',
                        'twitch', 'twitter', 'twitter-alt', 'whatsapp',
                        'xing', 'youtube'
                    ),
                    
                    // WordPress.org
                    'wordpress-org' => array(
                        'hammer', 'art', 'migrate', 'performance', 'universal-access',
                        'universal-access-alt', 'tickets', 'nametag', 'clipboard',
                        'heart', 'megaphone', 'schedule', 'wordpress-alt', 'wordpress', 'rest-api',
                        'code-standards'
                    ),
                    
                    // Notifications
                    'notifications' => array(
                        'bell', 'yes', 'yes-alt', 'no', 'no-alt',
                        'plus', 'plus-alt', 'plus-alt2', 'minus', 'dismiss',
                        'marker', 'star-filled', 'star-half', 'star-empty',
                        'flag', 'warning'
                    ),
                    
                    // Miscellaneous
                    'miscellaneous' => array(
                        'location', 'location-alt', 'vault', 'shield', 'shield-alt',
                        'sos', 'search', 'slides', 'analytics', 'chart-pie',
                        'chart-bar', 'chart-line', 'chart-area', 'groups',
                        'businessman', 'businesswoman', 'businessperson', 'id',
                        'id-alt', 'products', 'awards', 'forms', 'testimonial',
                        'portfolio', 'book', 'book-alt', 'download', 'upload',
                        'backup', 'clock', 'lightbulb', 'microphone', 'desktop',
                        'laptop', 'tablet', 'smartphone', 'phone', 'index-card',
                        'carrot', 'building', 'store', 'album', 'palmtree',
                        'tickets-alt', 'money', 'money-alt', 'smiley', 'thumbs-up',
                        'thumbs-down', 'layout', 'paperclip', 'color-picker',
                        'edit-large', 'edit-page', 'airplane', 'bank', 'beer',
                        'calculator', 'car', 'coffee', 'drumstick', 'food',
                        'fullscreen-alt', 'fullscreen-exit-alt', 'games', 'hourglass',
                        'open-folder', 'pdf', 'pets', 'printer', 'privacy',
                        'superhero', 'superhero-alt'
                    )
                );

                // Display the common icons first
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

                // Add separator
                echo '<div class="dashicon-separator" style="width: 100%; border-bottom: 1px solid #ddd; margin: 10px 0; grid-column: 1/-1;"></div>';

                // Display all other icons by category
                foreach ($all_dashicons as $category => $icons) {
                    // Add category header - Fixed sprintf format
                    printf(
                        '<div class="dashicon-category-header" style="width: 100%%; grid-column: 1/-1; margin: 10px 0 5px; font-weight: bold;">%s</div>',
                        esc_html(ucfirst(str_replace('-', ' ', $category)))
                    );
                    
                    foreach ($icons as $icon_name) {
                        if (!isset($dashicons[$icon_name])) {  // Skip if already shown in common icons
                            $selected = ($dashicon === $icon_name) ? 'selected' : '';
                            printf(
                                '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                                esc_attr($icon_name),
                                esc_attr($selected),
                                esc_attr($icon_name),
                                esc_attr($icon_name)
                            );
                        }
                    }
                }
                ?>
            </div>
            <input type="hidden" 
                name="distm_settings[distm_featured_dashicon]" 
                class="selected-dashicon" 
                value="<?php echo esc_attr($dashicon); ?>">
        </div>
    </div>
    <?php
    if (!empty($args['description'])) {
        echo "<p class='description' style='opacity: 0.5;font-style:italic;'><small><span class='dashicons dashicons-info'></span> " . esc_html($args['description']) . "</small></p>";
    }
}

function distm_page_exclusion_section_callback() {
    $options = get_option('distm_settings');
    $selected_pages = isset($options['distm_exclude_pages']) ? (array)$options['distm_exclude_pages'] : array();

    // Get all pages
    $pages = get_pages(array(
        'sort_column' => 'menu_order,post_title',
        'hierarchical' => true
    ));

    // Group pages by parent
    $page_tree = array();
    foreach ($pages as $page) {
        if ($page->post_parent == 0) {
            $page_tree[$page->ID] = array(
                'page' => $page,
                'children' => array()
            );
        } else {
            if (isset($page_tree[$page->post_parent])) {
                $page_tree[$page->post_parent]['children'][] = $page;
            } else {
                // If parent isn't found (rare case), add to top level
                $page_tree[$page->ID] = array(
                    'page' => $page,
                    'children' => array()
                );
            }
        }
    }

    echo '<div class="page-exclusion-grid">';
    
    // Render parent pages and their children
    foreach ($page_tree as $parent_id => $data) {
        $parent = $data['page'];
        $children = $data['children'];
        
        echo '<div class="page-exclusion-item parent-item">';
        echo '<label class="page-checkbox-label">';
        printf(
            '<input type="checkbox" name="distm_settings[distm_exclude_pages][]" value="%d" %s>',
            esc_attr($parent->ID),
            checked(in_array($parent->ID, $selected_pages), true, false)
        );
        echo '<span class="page-title">' . esc_html($parent->post_title) . '</span>';
        echo '</label>';

        // Render child pages if any exist
        if (!empty($children)) {
            echo '<div class="child-pages">';
            foreach ($children as $child) {
                echo '<label class="page-checkbox-label child-label">';
                printf(
                    '<input type="checkbox" name="distm_settings[distm_exclude_pages][]" value="%d" %s>',
                    esc_attr($child->ID),
                    checked(in_array($child->ID, $selected_pages), true, false)
                );
                echo '<p class="page-title">' . esc_html($child->post_title) . '</p>';
                echo '</label>';
            }
            echo '</div>';
        }
        
        echo '</div>';
    }
    
    echo '</div>';

    if (!empty($args['description'])) {
        echo '<p class="description" style="margin-top: 15px;"><span class="dashicons dashicons-info"></span> ' . 
             esc_html($args['description']) . '</p>';
    }
    ?>
    <?php
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