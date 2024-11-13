<?php
if (!defined('ABSPATH')) exit;

function distm_add_help_submenu() {
    add_submenu_page(
        'the-menu',
        __('Help', 'the-menu'),
        __('Help', 'the-menu'),
        'manage_options',
        'distm-help',
        'distm_help_page'
    );
}
add_action('admin_menu', 'distm_add_help_submenu');



function distm_help_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'the-menu'));
    }

    

    ?>
    <div class="rss-title">
        <div class="rss-icon"></div>
        <div class="rss-content">
            <h1><?php esc_html_e('Help', 'the-menu'); ?></h1>
            <p><?php esc_html_e('Learn how to use The Menu effectively', 'the-menu'); ?></p>
        </div>
    </div>
    <div class="tm-wrap mt0">
        <div class="tm-wrapper help-content">
            <div class="help-section">
                <h2><?php esc_html_e('Quick start guide', 'the-menu'); ?></h2>
                <ol class="step-list" style="counter-reset: step-counter;">
                    <li><?php esc_html_e('Enable The Menu in the plugin settings', 'the-menu'); ?></li>
                    <li><?php esc_html_e('Create and assign menus to Left, Right, or Add-on locations', 'the-menu'); ?></li>
                    <li><?php esc_html_e('Add icons to your menu items', 'the-menu'); ?></li>
                    <li><?php esc_html_e('Customise colors and styles', 'the-menu'); ?></li>
                    <li><?php esc_html_e('Test on mobile devices', 'the-menu'); ?></li>
                </ol>
                
                <p>
                    <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button">
                        <?php esc_html_e('Create your menus →', 'the-menu'); ?>
                    </a>
                </p>
            </div>

            <div class="help-section">
                <h2><?php esc_html_e('Menu locations', 'the-menu'); ?></h2>
                <div class="help-grid">
                    <div class="help-card">
                        <h4><?php esc_html_e('Left menu', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Primary navigation items displayed on the left side. Best for your most important links.', 'the-menu'); ?></p>
                    </div>
                    <div class="help-card">
                        <h4><?php esc_html_e('Right menu', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Secondary navigation items shown on the right. Ideal for utility links like account, search, or cart.', 'the-menu'); ?></p>
                    </div>
                    <div class="help-card">
                        <h4><?php esc_html_e('Add-on menu', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Additional items that appear when clicking the center button. Perfect for less frequently used links.', 'the-menu'); ?></p>
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h2><?php esc_html_e('Adding icons', 'the-menu'); ?></h2>
                <div class="help-grid">
                    <div class="help-card">
                        <h4><?php esc_html_e('Using dashicons', 'the-menu'); ?></h4>
                        <ol>
                            <li><?php esc_html_e('Edit a menu item', 'the-menu'); ?></li>
                            <li><?php esc_html_e('Select "Dashicon" as icon type', 'the-menu'); ?></li>
                            <li><?php esc_html_e('Choose from available icons', 'the-menu'); ?></li>
                        </ol>
                    </div>
                    <div class="help-card">
                        <h4><?php esc_html_e('Custom icons', 'the-menu'); ?></h4>
                        <ol>
                            <li><?php esc_html_e('Edit a menu item', 'the-menu'); ?></li>
                            <li><?php esc_html_e('Select "Upload" as icon type', 'the-menu'); ?></li>
                            <li><?php esc_html_e('Upload an SVG icon', 'the-menu'); ?></li>
                        </ol>
                    </div>
                </div>
                
                <div class="tip-box">
                    <strong><?php esc_html_e('Tip:', 'the-menu'); ?></strong>
                    <?php esc_html_e('SVG icons work best for crisp display at all sizes.', 'the-menu'); ?>
                </div>
            </div>

            <div class="help-section">
                <h2><?php esc_html_e('Menu styles', 'the-menu'); ?></h2>
                <div class="help-grid">
                    <div class="help-card">
                        <h4><?php esc_html_e('Pill style', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Floating menu with rounded edges. Creates a modern, app-like appearance.', 'the-menu'); ?></p>
                    </div>
                    <div class="help-card">
                        <h4><?php esc_html_e('Rounded style', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Attached to bottom with rounded top corners. Offers a clean, professional look.', 'the-menu'); ?></p>
                    </div>
                    <div class="help-card">
                        <h4><?php esc_html_e('Flat style', 'the-menu'); ?></h4>
                        <p><?php esc_html_e('Full-width menu attached to bottom. Maximizes space for menu items.', 'the-menu'); ?></p>
                    </div>
                </div>
            </div>

            <div class="help-section">
                <h2><?php esc_html_e('Troubleshooting', 'the-menu'); ?></h2>
                
                <div class="warning-box">
                    <h4><?php esc_html_e('Menu not showing?', 'the-menu'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Verify "Enable menu" is checked in settings', 'the-menu'); ?></li>
                        <li><?php esc_html_e('Check if current page is excluded', 'the-menu'); ?></li>
                        <li><?php esc_html_e('If using mobile-only mode, test on a mobile device', 'the-menu'); ?></li>
                    </ul>
                </div>

                <div class="warning-box">
                    <h4><?php esc_html_e('Icons not working?', 'the-menu'); ?></h4>
                    <ul>
                        <li><?php esc_html_e('Ensure icons are properly uploaded/selected', 'the-menu'); ?></li>
                        <li><?php esc_html_e('Verify custom icons are in SVG format', 'the-menu'); ?></li>
                        <li><?php esc_html_e('Check icon color contrast settings', 'the-menu'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="help-section">
                <h2><?php esc_html_e('Need more help?', 'the-menu'); ?></h2>
                <p>
                    <strong><?php esc_html_e('Support:', 'the-menu'); ?></strong> 
                    <a href="https://wordpress.org/support/plugin/the-menu/" target="_blank">Create a topic</a>
                </p>
            </div>
        </div>
    </div>
    <?php
    include_once('assets/logo-wrapper.php');
}