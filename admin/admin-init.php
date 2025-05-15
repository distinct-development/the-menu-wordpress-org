<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function distm_check_required_files() {
    // Verify requirements
    $requirements_met = true;
    $errors = array();

    // Check PHP version
    if (version_compare(PHP_VERSION, '7.0', '<')) {
        $requirements_met = false;
        $errors[] = 'PHP 7.0 or higher is required. Current version is ' . PHP_VERSION;
    }

    // Check required files
    $required_files = array(
        '../admin/admin-init.php',
        '../admin/admin-pages.php',
        '../admin/admin-menus.php',
        '../frontend/frontend-init.php',
        '../admin/assets/menu-logo.svg'
    );

    foreach ($required_files as $file) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            $requirements_met = false;
            $errors[] = 'Required file missing: ' . $file;
        }
    }

    // If requirements aren't met, deactivate the plugin and show error
    if (!$requirements_met) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            '<p>' . wp_kses_post(implode('</p><p>', array_map('esc_html', $errors))) . '</p>' .
            '<p>' . esc_html__('The Menu plugin cannot be activated. Please fix the issues above and try again.', 'the-menu') . '</p>',
            esc_html__('Plugin Activation Error', 'the-menu'),
            array('back_link' => true)
        );
    }
}

// Enqueue the plugin styles and scripts
function distm_enqueue_admin_scripts($hook_suffix) {
    // Get plugin version for cache busting
    $plugin_version = get_plugin_data(__DIR__ . '/../the-menu.php')['Version'];
    
    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings' || $hook_suffix == 'the-menu_page_distm-help') {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script(
            'distm-admin-script', 
            plugin_dir_url(__FILE__) . 'js/script.js', 
            array('jquery', 'wp-color-picker'), 
            $plugin_version, 
            true // Load in footer
        );
        
        if (is_admin()) {
            // Add these lines to load frontend styles for the preview
            if ($hook_suffix == 'toplevel_page_the-menu') {
                wp_enqueue_style(
                    'distm-frontend-style', 
                    plugin_dir_url(dirname(__FILE__)) . 'frontend/css/style.css', 
                    array(), 
                    $plugin_version
                );
                wp_enqueue_script(
                    'distm-frontend-script', 
                    plugin_dir_url(dirname(__FILE__)) . 'frontend/js/script.js', 
                    array('jquery'), 
                    $plugin_version, 
                    true // Load in footer
                );
                wp_enqueue_style('dashicons');
            }
        }
    }

    if ($hook_suffix == 'toplevel_page_the-menu' || $hook_suffix == 'the-menu_page_the-menu-license-settings' || $hook_suffix == 'the-menu_page_distm-help') {
        wp_enqueue_style(
            'distm-admin-style', 
            plugin_dir_url(__FILE__) . 'css/style.css', 
            array(), 
            $plugin_version
        );
    }

    if ($hook_suffix == 'nav-menus.php') {
        wp_enqueue_media();
        wp_enqueue_script(
            'distm-wp-menus', 
            plugin_dir_url(__FILE__) . 'js/wp-menu.js', 
            array('jquery'), 
            $plugin_version, 
            true // Load in footer
        );
    }

    if ($hook_suffix !== 'toplevel_page_the-menu') return;

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script(
        'iris', 
        admin_url('js/iris.min.js'), 
        array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), 
        $plugin_version, 
        true // Load in footer
    );
    
    // Make sure jQuery UI and Touch Punch are loaded
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-slider');
    wp_enqueue_script('jquery-touch-punch');

    // Add nonce for AJAX operations
    wp_localize_script('distm-admin-script', 'distmAjax', array(
        'nonce' => wp_create_nonce('distm_ajax_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));

    // Add nonce for menu operations
    wp_localize_script('distm-wp-menus', 'distmMenus', array(
        'nonce' => wp_create_nonce('distm_menu_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'distm_enqueue_admin_scripts');

// Sanitize the plugin settings
function distm_get_svg_content($url) {
    if (substr($url, -4) !== '.svg') {
        return false;
    }

    // Generate a unique cache key based on the URL
    $cache_key = 'distm_svg_' . md5($url);
    
    // Try to get the SVG content from cache first
    $cached_svg = wp_cache_get($cache_key, 'distm_svg_cache');
    if ($cached_svg !== false) {
        return $cached_svg;
    }
    
    // Check if this is a local file in the WordPress media library
    $attachment_id = attachment_url_to_postid($url);
    if ($attachment_id) {
        $file_path = get_attached_file($attachment_id);
        if ($file_path && file_exists($file_path)) {
            $svg_content = file_get_contents($file_path);
        } else {
            return false;
        }
    } else {
        // Handle remote URLs
        $response = wp_remote_get($url, array(
            'timeout' => 15, // Increased timeout
            'sslverify' => true // Enable SSL verification
        ));
        
        if (is_wp_error($response)) {
            return false;
        }

        $svg_content = wp_remote_retrieve_body($response);
        if (empty($svg_content)) {
            return false;
        }
    }
    
    // Basic optimization of SVG content
    $svg_content = distm_optimize_svg($svg_content);
    
    // Cache the optimized SVG for 24 hours
    wp_cache_set($cache_key, $svg_content, 'distm_svg_cache', 24 * HOUR_IN_SECONDS);
    
    return $svg_content;
}

/**
 * Optimize SVG content for better performance
 * 
 * @param string $svg_content The SVG content to optimize
 * @return string The optimized SVG content
 */
function distm_optimize_svg($svg_content) {
    // Remove comments, whitespace, and unnecessary attributes
    $svg_content = preg_replace('/<!--.*?-->/s', '', $svg_content);
    $svg_content = preg_replace('/\s+/', ' ', $svg_content);
    $svg_content = preg_replace('/\s*([{}|:;,])\s*/', '$1', $svg_content);
    
    // Remove unnecessary attributes
    $svg_content = preg_replace('/\s+(?:xmlns:.*?|data-.*?|class|id)="[^"]*"/', '', $svg_content);
    
    // Remove empty elements
    $svg_content = preg_replace('/<([^>]+)>\s*<\/\1>/', '', $svg_content);
    
    // Ensure viewBox is present for proper scaling
    if (strpos($svg_content, 'viewBox') === false && preg_match('/width="([^"]+)"\s+height="([^"]+)"/', $svg_content, $matches)) {
        $width = $matches[1];
        $height = $matches[2];
        $svg_content = preg_replace('/<svg/', '<svg viewBox="0 0 ' . $width . ' ' . $height . '"', $svg_content, 1);
    }
    
    return $svg_content;
}