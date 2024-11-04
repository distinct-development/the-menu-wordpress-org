<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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
    ?>
    <div class="rss-title">
        <div class="rss-icon"></div>
        <div class="rss-content">
            <h1>Help</h1>
            <p>Help page for The Menu</p>
        </div>
    </div>
    <div class="tm-wrap mt0">
        <div class="tm-wrapper">
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