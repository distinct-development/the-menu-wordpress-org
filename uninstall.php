<?php
if (!defined('ABSPATH')) exit;

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Function to check if we should delete data
function distm_should_delete_data() {
    $settings = get_option('distm_settings');
    return isset($settings['distm_delete_data']) && $settings['distm_delete_data'];
}

// Only proceed if the user opted to delete data
if (distm_should_delete_data()) {
    // Remove all plugin options
    delete_option('distm_settings');
    delete_option('the-menu_license_key');
    
    // Remove all license status transients (for all users)
    // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    global $wpdb;
    // Prepare patterns with wildcards properly
    $distm_pattern1 = $wpdb->esc_like('_transient_the-menu_license_status') . '%';
    $distm_pattern2 = $wpdb->esc_like('_transient_timeout_the-menu_license_status') . '%';
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
            $distm_pattern1,
            $distm_pattern2
        )
    );
    // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

    // Meta keys to remove
    $distm_meta_keys = array(
        '_menu_item_icon',
        '_menu_item_icon_type',
        '_menu_item_dashicon',
        '_menu_item_visibility',
        '_menu_item_roles',
        '_menu_item_capabilities',
        '_menu_item_filter_by'
    );

    // Get all menu items
    $distm_menu_items = get_posts(array(
        'post_type' => 'nav_menu_item',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ));

    // Delete meta for each menu item
    foreach ($distm_menu_items as $distm_menu_item_id) {
        foreach ($distm_meta_keys as $distm_meta_key) {
            delete_post_meta($distm_menu_item_id, $distm_meta_key);
        }
    }

    // Clear any cached data
    wp_cache_flush();

    // Clean up wp_options (removing duplicates from earlier deletion)
    $distm_option_keys = array(
        'distm_plugin_version'
    );

    foreach ($distm_option_keys as $distm_option) {
        delete_option($distm_option);
    }
    
    // Final cache flush to ensure everything is clean
    wp_cache_flush();
}