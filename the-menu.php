<?php
/*
Plugin Name: The Menu: Custom mobile navigation with icons
Plugin URI: https://github.com/distinct-development/the-menu-wordpress-org
Description: Create beautiful mobile navigation menus with custom icons, role-based visibility, and extensive style options for your WordPress site.
Version: 1.2.23
Author: Distinct
License: GPL-2.0-or-later
Author URI: https://plugins.distinct.africa
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.2.23
Requires PHP: 7.0
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin constants
define('DISTM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DISTM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Define security and performance constants
define('DISTM_MAX_SVG_SIZE', 1048576); // 1MB maximum SVG file size

// =============================================
// CORE PLUGIN INITIALIZATION - NO TRANSLATIONS
// =============================================

// Register the activation hook
register_activation_hook(__FILE__, 'distm_activate_plugin');

function distm_activate_plugin() {
    if (!current_user_can('activate_plugins')) {
        wp_die('Sorry, you do not have sufficient permissions to activate plugins.');
    }

    if (!wp_doing_ajax() && !in_array($GLOBALS['pagenow'], array('plugins.php', 'update.php'))) {
        return;
    }
    
    // Flush rewrite rules and clear any cached styles
    flush_rewrite_rules();
    wp_cache_flush();

    // Include the file with the check function
    require_once(DISTM_PLUGIN_DIR . 'admin/admin-init.php');
    distm_check_required_files();
    distm_check_languages_directory();
    
    // Get the current version from options
    $current_version = get_option('distm_plugin_version', '0');
    
    // If this is a new installation or updating from a version before icon types
    if (version_compare($current_version, '1.2.8', '<')) {
        distm_migrate_menu_item_icon_types();
    }
}

// Check if languages directory exists and create it if it doesn't
function distm_check_languages_directory() {
    $languages_dir = plugin_dir_path(__FILE__) . 'languages';
    if (!file_exists($languages_dir)) {
        wp_mkdir_p($languages_dir);
    }
}

// Note: distm_check_required_files() is defined in admin/admin-init.php

function distm_migrate_menu_item_icon_types() {
    // Get all menu items
    $menu_items = get_posts(array(
        'post_type' => 'nav_menu_item',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));

    foreach ($menu_items as $menu_item_id) {
        // Get existing icon URL
        $icon_url = get_post_meta($menu_item_id, '_menu_item_icon', true);
        
        // If there's an icon URL, set type to 'upload'
        if (!empty($icon_url) && $icon_url !== '') {
            update_post_meta($menu_item_id, '_menu_item_icon_type', 'upload');
        }
    }

    // Update plugin version in options to track migration
    update_option('distm_plugin_version', '1.2.23');
}

// Add a function to handle plugin updates - NO translations here
function distm_check_for_updates() {
    $current_version = get_option('distm_plugin_version', '0');
    
    // If updating from a version before icon types
    if (version_compare($current_version, '1.2.8', '<')) {
        distm_migrate_menu_item_icon_types();
    }
    
    // Update to current version
    update_option('distm_plugin_version', '1.2.23');
}
add_action('plugins_loaded', 'distm_check_for_updates');

// Load required WordPress files
if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Include only essential non-translatable admin functions with no translation strings
function distm_include_essential_files() {
    require_once(DISTM_PLUGIN_DIR . 'admin/admin-init.php');
}
add_action('plugins_loaded', 'distm_include_essential_files', 5);

// =========================================
// MAIN PLUGIN LOADING
// =========================================
// Note: WordPress automatically loads plugin translations since WP 4.6
// No manual load_plugin_textdomain() call is needed
function distm_plugin_init() {
    // Now that translations are loaded, we can include files with translatable strings
    require_once(DISTM_PLUGIN_DIR . 'admin/admin-pages.php');
    require_once(DISTM_PLUGIN_DIR . 'admin/admin-menus.php'); 
    require_once(DISTM_PLUGIN_DIR . 'admin/admin-help.php');
    require_once(DISTM_PLUGIN_DIR . 'frontend/frontend-init.php');
}
add_action('init', 'distm_plugin_init', 5);

// Register menus without translations - must happen on init
function distm_register_my_menus() {
    register_nav_menus(
        array(
            'left-menu' => '[THE MENU] Left menu',
            'right-menu' => '[THE MENU] Right menu',
            'addon-menu' => '[THE MENU] Add-on menu'
        )
    );
}
add_action('init', 'distm_register_my_menus', 2);
