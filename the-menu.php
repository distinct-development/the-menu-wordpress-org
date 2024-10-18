<?php

namespace Distinct\TheMenu;

/*
Plugin Name: The Menu
Plugin URI: https://github.com/distinct-development/the-menu-wordpress-org
Description: Enhance your WordPress site with The Menu plugin. This tool offers customizable navigation options with mobile support, SVG icons, and extensive color choices. Perfect for creating a visually appealing and user-friendly menu system.
Version: 1.2.2
Author: Distinct
License: GPL-2.0-or-later
Author URI: https://distinct.africa
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 4.3
Requires PHP: 7.0
*/

include_once('admin/tm-settings.php');
include_once('include/tm-menu.php');

function tm_enqueue_frontend_scripts() {
    $options = get_option('tm_settings');
    $excluded_pages = isset($options['tm_exclude_pages']) ? $options['tm_exclude_pages'] : array();

    // Ensure $excluded_pages is an array
    if (!is_array($excluded_pages)) {
        $excluded_pages = array($excluded_pages);
    }

    // Check if the current page is not in the excluded pages list
    $current_page_id = get_queried_object_id();
    if (!in_array($current_page_id, $excluded_pages)) {
        // Check if mobile menu is enabled
        $mobile_menu_enabled = isset($options['tm_enable_mobile_menu']) ? $options['tm_enable_mobile_menu'] : false;
        
        // Check if it should only be shown on mobile
        $only_on_mobile = isset($options['tm_only_on_mobile']) ? $options['tm_only_on_mobile'] : false;

        // Determine if we should load the assets
        $should_load = $mobile_menu_enabled && (!$only_on_mobile || wp_is_mobile());

        if ($should_load) {
            // Enqueue styles
            wp_enqueue_style('the-menu', plugins_url('css/style.css', __FILE__), array(), '1.0.0', 'all');

            // Enqueue scripts
            wp_enqueue_script('tm-frontend', plugins_url('js/scripts.js', __FILE__), array('jquery'), '1.0.0', true);
        }
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\tm_enqueue_frontend_scripts');

if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

class Plugin_License_Validator {
    private static $instance = null;
    private $plugin_slug;
    private $plugin_name;
    private $license_option_key;
    private $text_domain;
    private $api_endpoint;

    private function __construct($plugin_slug, $plugin_name, $text_domain) {
        $this->plugin_slug = $plugin_slug;
        $this->plugin_name = $plugin_name;
        $this->license_option_key = $plugin_slug . '_license_key';
        $this->text_domain = $text_domain;
        $this->api_endpoint = 'https://plugins.distinct.africa/api/register-plugin.php';

        add_action('admin_notices', array($this, 'license_status_notice'));
        add_action('admin_init', array($this, 'clear_license_cache'));
        add_filter('plugin_row_meta', array($this, 'modify_plugin_meta'), 10, 2);
        add_action('admin_menu', array($this, 'add_license_submenu'));
        add_action('admin_init', array($this, 'register_license_settings'));
        
        // Add this line to hook the license check handler
        add_action('admin_post_the_menu_check_license', array($this, 'handle_license_check'));
    }

    public static function get_instance($plugin_slug, $plugin_name, $text_domain) {
        if (self::$instance === null) {
            self::$instance = new self($plugin_slug, $plugin_name, $text_domain);
        }
        return self::$instance;
    }

    public function validate_license() {
        $cache_key = $this->plugin_slug . '_license_status';
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached === 'valid';
        }
    
        $license_key = get_option($this->license_option_key);
        $site_url = get_site_url();
        $plugin_data = get_plugin_data(__DIR__ . '/' . $this->plugin_slug . '.php');
        $version = $plugin_data['Version'];
    
        $response = wp_remote_post($this->api_endpoint, array(
            'body' => wp_json_encode(array(
                'license_key' => $license_key,
                'site_url' => $site_url,
                'version' => $version,
                'plugin_id' => 3            )),
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $license_key
            )
        ));
    
        if (is_wp_error($response)) {
            return false;
        }
    
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body);
        $is_valid = isset($data->status) && $data->status === 'success';
        set_transient($cache_key, $is_valid ? 'valid' : 'invalid', 12 * HOUR_IN_SECONDS);
        return $is_valid;
    }

    public function license_status_notice() {
        $screen = get_current_screen();
        if ($screen->id !== 'the-menu_page_the-menu-license-settings') {
            return;
        }

        if (isset($_GET['license_check_result']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'the_menu_license_check')) {
            $status = sanitize_text_field($_GET['license_check_result']) === 'valid' ? 'valid' : 'invalid';
            $class = $status === 'valid' ? 'notice notice-success' : 'notice notice-error';
            $message = $status === 'valid' 
                ? __('License validated successfully.', 'the-menu')
                : __('Invalid license or site is not registered.', 'the-menu');
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
        }
    }

    public function handle_license_check() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'the-menu'));
        }

        check_admin_referer('the_menu_check_license_nonce');

        $license_status = $this->validate_license();
        $redirect_url = add_query_arg(
            array(
                'license_check_result' => $license_status ? 'valid' : 'invalid',
                '_wpnonce' => wp_create_nonce('the_menu_license_check')
            ),
            wp_get_referer()
        );

        wp_safe_redirect($redirect_url);
        exit;
    }

    public function clear_license_cache() {
        if (isset($_GET['license_check_result']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'the_menu_license_check') && current_user_can('manage_options')) {
            delete_transient($this->plugin_slug . '_license_status');
        }
    }

    public function license_key_field_callback() {
        $license_key = get_option($this->license_option_key);
        $status = $this->validate_license() ? 'valid' : 'invalid';
        $status_text = $status === 'valid' ? __('Active', 'the-menu') : __('Unlicensed', 'the-menu');
        $status_class = $status === 'valid' ? 'tm-status-pill-active' : 'tm-status-pill-inactive';
        
        echo '<input type="text" id="' . esc_attr($this->license_option_key) . '" name="' . esc_attr($this->license_option_key) . '" value="' . esc_attr($license_key) . '" class="regular-text" />';
        echo ' <div class="tm-license-zone"><span class="tm-status-pill ' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
        echo ' <a href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=the_menu_check_license'), 'the_menu_check_license_nonce')) . '" style="text-decoration:none;"><span class="dashicons dashicons-image-rotate"></span></a></div>';
    }

    public function modify_plugin_meta($plugin_meta, $plugin_file) {
        if ($plugin_file == plugin_basename(__FILE__)) {
            $is_registered = $this->validate_license();
            $clear_cache_url = wp_nonce_url(admin_url('plugins.php?action=the_menu_check_license'), 'the_menu_check_license_nonce');
            $status = $is_registered ? '<span style="color: green;"><span class="dashicons dashicons-yes-alt"></span> Registered</span>' : '<span>Unlicensed:</span> Premium features planned <a href="admin.php?page=the-menu-license-settings">Manage license</a> <a href="' . $clear_cache_url . '" title="Refresh License Status"><span class="dashicons dashicons-image-rotate" style="font-size:1.2em;margin-top:2px;"></span></a>';
            foreach ($plugin_meta as &$meta) {
                if (strpos($meta, 'Version') !== false) {
                    $meta .= ' | ' . $status;
                    break;
                }
            }
        }
        return $plugin_meta;
    }

    public function add_license_submenu() {
        add_submenu_page(
            $this->plugin_slug,
            'License settings',
            'License',
            'manage_options',
            $this->plugin_slug . '-license-settings',
            array($this, 'license_settings_page')
        );
    }

    public function license_settings_page() {
        ?>
        <div class="rss-title">
            <div class="rss-icon"></div>
            <div class="rss-content">
                <h1><?php echo esc_html($this->plugin_name); ?></h1>
                <p>License key settings</p>
            </div>
        </div>
        <div class="tm-wrap mt0">
            <div class="tm-wrapper">
                <?php
                    echo '<form action="options.php" method="post">';
                    settings_fields($this->plugin_slug . '_license_settings');
                    do_settings_sections($this->plugin_slug . '_license_settings');
                    submit_button('Save license key');
                    echo '</form>';
                ?>
            </div>
        </div>
        <?php
        include_once 'admin/assets/logo-wrapper.php';
    }

    public function register_license_settings() {
        register_setting($this->plugin_slug . '_license_settings', $this->license_option_key);
        add_settings_section(
            $this->plugin_slug . '_license_section',
            $this->plugin_name . ': License key settings',
            null,
            $this->plugin_slug . '_license_settings'
        );
        add_settings_field(
            $this->plugin_slug . '_license_key_field',
            'License Key',
            array($this, 'license_key_field_callback'),
            $this->plugin_slug . '_license_settings',
            $this->plugin_slug . '_license_section'
        );
    }
}

function load_the_menu() {
    Plugin_License_Validator::get_instance('the-menu', 'The Menu', 'the_menu');
}
add_action('plugins_loaded', __NAMESPACE__ . '\\load_the_menu');
