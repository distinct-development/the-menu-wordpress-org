<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Register the plugin settings
function distm_register_my_menus() {
    register_nav_menus(
        array(
            'left-menu' => '[THE MENU] Left menu',
            'right-menu' => '[THE MENU] Right menu',
            'addon-menu' => '[THE MENU] Add-on menu'
        )
    );
}
add_action('init', 'distm_register_my_menus');

// Add custom fields to the menu items
function distm_add_custom_menu_fields($item_id, $item, $depth, $args) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $nonce_field = 'distm_menu_item_' . $item_id;
    wp_nonce_field('distm_menu_item_' . $item_id, $nonce_field);

    // Get existing meta values with proper fallbacks
    $icon = get_post_meta($item_id, '_menu_item_icon', true);
    $icon_type = get_post_meta($item_id, '_menu_item_icon_type', true);
    $dashicon = get_post_meta($item_id, '_menu_item_dashicon', true);

    // Set default values if not set
    if (empty($icon_type)) {
        $icon_type = 'dashicon';
    }
    if (empty($dashicon)) {
        $dashicon = 'menu';
    }
    ?>
    <div class="field-custom description description-wide">
        <h3 style="margin-bottom: 10px;"><span class="dashicons dashicons-align-full-width"></span> <?php esc_html_e('Menu Icon', 'the-menu'); ?></h3>
        
        <!-- Icon Type Selector -->
        <div class="icon-type-selector" style="margin-bottom: 10px;">
        <label class="icon-type-label">
            <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                value="dashicon" <?php checked($icon_type === 'dashicon' || empty($icon)); ?> class="icon-type-radio">
            <?php esc_html_e('Select dashicon', 'the-menu'); ?>
        </label>
        <label class="icon-type-label" style="margin-left: 15px;">
            <input type="radio" name="menu-item-icon-type[<?php echo esc_attr($item_id); ?>]" 
                value="upload" <?php checked($icon_type === 'upload' && !empty($icon)); ?> class="icon-type-radio">
            <?php esc_html_e('Upload icon', 'the-menu'); ?>
        </label>
        </div>

        <!-- Hidden field to ensure the dashicon value is always submitted -->
        <input type="hidden" 
            name="menu-item-dashicon[<?php echo esc_attr($item_id); ?>]" 
            class="selected-dashicon" 
            value="<?php echo esc_attr($dashicon); ?>">

        <!-- Upload Icon Section -->
        <div class="icon-upload-section" style="<?php echo $icon_type === 'dashicon' ? 'display: none;' : ''; ?>">
            <div class="icon-preview" style="margin-bottom: 10px;">
                <?php if (!empty($icon) && $icon_type === 'upload'): ?>
                    <?php echo wp_get_attachment_image(attachment_url_to_postid($icon), array(40, 40), false, array('style' => 'max-width: 40px; height: auto;')); ?>
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

                // Display commonly used icons first
                foreach ($dashicons as $icon_name => $label) {
                    $selected = ($dashicon === $icon_name) ? 'selected' : '';
                    printf(
                        '<span class="dashicons dashicons-%s dashicon-option %s" data-icon="%s" title="%s"></span>',
                        esc_attr($icon_name),
                        esc_attr($selected),
                        esc_attr($icon_name),
                        esc_attr($label)
                    );
                }

                // Add separator
                echo '<div class="dashicon-separator" style="width: 100%; border-bottom: 1px solid #ddd; margin: 10px 0; grid-column: 1/-1;"></div>';

                // Add category based icons
                $all_dashicons = array(
                    // Admin Menu
                    __('Admin Menu', 'the-menu') => array(
                        'menu', 'menu-alt', 'menu-alt2', 'menu-alt3',
                        'admin-site', 'admin-site-alt', 'admin-site-alt2', 'admin-site-alt3',
                        'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page',
                        'admin-comments', 'admin-appearance', 'admin-plugins', 'plugins-checked',
                        'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home',
                        'admin-generic', 'admin-collapse', 'filter', 'admin-customizer', 'admin-multisite'
                    ),
                
                    __('Welcome Screen', 'the-menu') => array(
                        'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus',
                        'welcome-comments', 'welcome-learn-more'
                    ),
                
                    __('Post Formats', 'the-menu') => array(
                        'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status',
                        'format-quote', 'format-chat', 'format-audio', 'format-standard', 'camera', 'camera-alt',
                        'images-alt', 'images-alt2', 'video-alt', 'video-alt2', 'video-alt3'
                    ),
                
                    __('Media', 'the-menu') => array(
                        'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document',
                        'media-interactive', 'media-spreadsheet', 'media-text', 'media-video',
                        'playlist-audio', 'playlist-video', 'controls-play', 'controls-pause',
                        'controls-forward', 'controls-skipforward', 'controls-back', 'controls-skipback',
                        'controls-repeat', 'controls-volumeon', 'controls-volumeoff',
                        'image-crop', 'image-filter', 'image-flip-horizontal', 'image-flip-vertical',
                        'image-rotate', 'image-rotate-left', 'image-rotate-right'
                    ),
                
                    __('Editor', 'the-menu') => array(
                        'editor-aligncenter', 'editor-alignleft', 'editor-alignright', 'editor-bold',
                        'editor-break', 'editor-code', 'editor-contract', 'editor-customchar',
                        'editor-expand', 'editor-help', 'editor-indent', 'editor-insertmore',
                        'editor-italic', 'editor-justify', 'editor-kitchensink', 'editor-ltr',
                        'editor-ol', 'editor-outdent', 'editor-paragraph', 'editor-paste-text',
                        'editor-paste-word', 'editor-quote', 'editor-removeformatting', 'editor-rtl',
                        'editor-spellcheck', 'editor-strikethrough', 'editor-table', 'editor-textcolor',
                        'editor-ul', 'editor-underline', 'editor-unlink', 'editor-video'
                    ),
                
                    __('Social', 'the-menu') => array(
                        'share', 'share-alt', 'share-alt2', 'rss', 'email',
                        'email-alt', 'email-alt2', 'networking', 'amazon',
                        'facebook', 'facebook-alt', 'google', 'instagram',
                        'linkedin', 'pinterest', 'podio', 'reddit', 'spotify',
                        'twitch', 'twitter', 'twitter-alt', 'whatsapp',
                        'xing', 'youtube'
                    ),
                
                    __('Interface', 'the-menu') => array(
                        'arrow-down', 'arrow-down-alt', 'arrow-down-alt2',
                        'arrow-left', 'arrow-left-alt', 'arrow-left-alt2',
                        'arrow-right', 'arrow-right-alt', 'arrow-right-alt2',
                        'arrow-up', 'arrow-up-alt', 'arrow-up-alt2',
                        'grid-view', 'list-view', 'screenoptions', 'info', 'insert',
                        'plus', 'plus-alt', 'plus-alt2', 'minus', 'dismiss',
                        'yes', 'yes-alt', 'no', 'no-alt',
                        'sort', 'update', 'redo', 'undo',
                        'visibility', 'hidden', 'move', 'lock', 'unlock',
                        'flag', 'star-empty', 'star-filled', 'star-half', 'sticky',
                        'warning', 'trash', 'external'
                    ),
                
                    __('Misc', 'the-menu') => array(
                        'location', 'location-alt', 'vault', 'shield', 'shield-alt',
                        'sos', 'search', 'slides', 'analytics', 'chart-pie',
                        'chart-bar', 'chart-line', 'chart-area', 'groups',
                        'businessman', 'businesswoman', 'businessperson', 'id',
                        'id-alt', 'products', 'awards', 'forms', 'testimonial',
                        'portfolio', 'book', 'book-alt', 'download', 'upload',
                        'backup', 'clock', 'lightbulb', 'microphone', 'desktop',
                        'laptop', 'tablet', 'smartphone', 'phone', 'store',
                        'album', 'palmtree', 'tickets-alt', 'money', 'money-alt',
                        'smiley', 'thumbs-up', 'thumbs-down', 'superhero', 'superhero-alt',
                        'layout', 'leftright', 'performance', 'universal-access', 'universal-access-alt',
                        'art', 'building', 'carrot', 'cloud',
                        'cloud-saved', 'cloud-upload', 'coffee', 'food', 'games',
                        'hammer', 'heart', 'hourglass', 'html', 'index-card',
                        'marker', 'nametag', 'open-folder', 'paperclip', 'pets',
                        'post-status', 'pressthis', 'tag', 'tagcloud', 'tickets',
                        'translation', 'wordpress', 'wordpress-alt',
                        'database', 'database-add', 'database-export', 'database-import',
                        'database-remove', 'database-view', 'fullscreen-alt', 'fullscreen-exit-alt',
                        'migrate', 'rest-api'
                    )
                );

                foreach ($all_dashicons as $category => $icons) {
                    printf(
                        '<div class="dashicon-category-header" style="width: 100%%; grid-column: 1/-1; margin: 10px 0 5px; font-weight: bold;">%s</div>',
                        esc_html($category)
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

    // Verify nonce
    $nonce_field = 'distm_menu_item_' . $menu_item_db_id;
    if (!isset($_POST[$nonce_field]) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), 'distm_menu_item_' . $menu_item_db_id)) {
        return;
    }

    // Get the icon URL and type
    $icon_url = isset($_POST['menu-item-icon'][$menu_item_db_id]) ? 
        esc_url_raw(wp_unslash($_POST['menu-item-icon'][$menu_item_db_id])) : '';
    
    $icon_type = isset($_POST['menu-item-icon-type'][$menu_item_db_id]) ?
        sanitize_text_field(wp_unslash($_POST['menu-item-icon-type'][$menu_item_db_id])) : 'dashicon';

    // Force icon type to dashicon if there's no URL
    if (empty($icon_url)) {
        $icon_type = 'dashicon';
    }
    
    // If we have a URL, force icon type to upload
    if (!empty($icon_url)) {
        $icon_type = 'upload';
        update_post_meta($menu_item_db_id, '_menu_item_icon', $icon_url);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_icon');
    }

    // Save icon type
    update_post_meta($menu_item_db_id, '_menu_item_icon_type', $icon_type);

    // Handle dashicon - always save a default
    $dashicon = isset($_POST['menu-item-dashicon'][$menu_item_db_id]) 
        ? sanitize_text_field(wp_unslash($_POST['menu-item-dashicon'][$menu_item_db_id]))
        : 'menu';
    update_post_meta($menu_item_db_id, '_menu_item_dashicon', $dashicon);
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_menu_fields', 10, 3);

function distm_add_menu_nonce_field() {
    wp_nonce_field('distm_custom_menu_fields', 'distm_custom_menu_nonce');
}
add_action('wp_nav_menu_item_custom_fields', 'distm_add_menu_nonce_field', 5, 4);

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
    
    $capabilities = get_post_meta($item_id, '_menu_item_capabilities', true);
    if (!is_array($capabilities)) {
        $capabilities = array();
    }
    
    $filter_by = get_post_meta($item_id, '_menu_item_filter_by', true);
    if (empty($filter_by)) {
        $filter_by = 'role'; // Default to role-based filtering
    }
    
    $visibility_options = array(
        'everyone' => __('Everyone', 'the-menu'),
        'logged_in' => __('Logged in users', 'the-menu'),
        'logged_out' => __('Logged out users', 'the-menu')
    );

    $available_roles = wp_roles()->roles;
    
    // Get all WordPress capabilities
    $all_capabilities = array();
    foreach ($available_roles as $role) {
        if (isset($role['capabilities']) && is_array($role['capabilities'])) {
            foreach ($role['capabilities'] as $cap => $val) {
                if ($val) {
                    $all_capabilities[$cap] = $cap;
                }
            }
        }
    }
    
    // Sort capabilities alphabetically
    ksort($all_capabilities);
    ?>
    <p class="field-visibility description description-wide">
        <label for="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>">
            <?php esc_html_e('Visibility', 'the-menu'); ?><br />
            <select id="edit-menu-item-visibility-<?php echo esc_attr($item_id); ?>" 
                    class="widefat code edit-menu-item-visibility" 
                    name="menu-item-visibility[<?php echo esc_attr($item_id); ?>]">
                <?php foreach ($visibility_options as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($visibility, $key); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>

    <div class="field-filter-type description description-wide" style="<?php echo $visibility === 'logged_in' ? '' : 'display:none;'; ?>">
        <label>
            <?php esc_html_e('Filter by:', 'the-menu'); ?><br />
            <input type="radio" name="menu-item-filter-by[<?php echo esc_attr($item_id); ?>]" 
                   value="role" <?php checked($filter_by === 'role'); ?> class="filter-type-radio">
            <?php esc_html_e('User Roles', 'the-menu'); ?>
            
            <input type="radio" name="menu-item-filter-by[<?php echo esc_attr($item_id); ?>]" 
                   value="capability" <?php checked($filter_by === 'capability'); ?> class="filter-type-radio" style="margin-left: 15px;">
            <?php esc_html_e('User Capabilities', 'the-menu'); ?>
        </label>
    </div>

    <div class="field-roles description description-wide" style="<?php echo ($visibility === 'logged_in' && $filter_by === 'role') ? '' : 'display:none;'; ?>">
        <label>
            <?php esc_html_e('User Roles (for logged in users)', 'the-menu'); ?><br />
            <?php foreach ($available_roles as $role_key => $role) : ?>
                <input type="checkbox" 
                       name="menu-item-roles[<?php echo esc_attr($item_id); ?>][]" 
                       value="<?php echo esc_attr($role_key); ?>" 
                       <?php checked(in_array($role_key, $roles)); ?>>
                <?php echo esc_html($role['name']); ?><br>
            <?php endforeach; ?>
            <small><i style="opacity: 0.5;line-height:1em;">
                <?php esc_html_e('Select which roles can see this menu item. If none are selected, all roles can see it.', 'the-menu'); ?>
            </i></small>
        </label>
    </div>
    
    <div class="field-capabilities description description-wide" style="<?php echo ($visibility === 'logged_in' && $filter_by === 'capability') ? '' : 'display:none;'; ?>">
        <label>
            <?php esc_html_e('User Capabilities (for logged in users)', 'the-menu'); ?><br />
            <input type="text" class="capability-search" placeholder="<?php esc_attr_e('Search capabilities...', 'the-menu'); ?>" style="width: 100%; margin-bottom: 5px;">
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 5px;" class="capability-list">
                <?php foreach ($all_capabilities as $cap) : ?>
                    <div class="capability-item">
                        <input type="checkbox" 
                               name="menu-item-capabilities[<?php echo esc_attr($item_id); ?>][]" 
                               id="capability-<?php echo esc_attr($item_id); ?>-<?php echo esc_attr($cap); ?>"
                               value="<?php echo esc_attr($cap); ?>" 
                               <?php checked(in_array($cap, $capabilities)); ?>>
                        <label for="capability-<?php echo esc_attr($item_id); ?>-<?php echo esc_attr($cap); ?>"><?php echo esc_html($cap); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small><i style="opacity: 0.5;line-height:1em;">
                <?php esc_html_e('Select which capabilities are required to see this menu item. If none are selected, all users can see it.', 'the-menu'); ?>
            </i></small>
        </label>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#edit-menu-item-visibility-<?php echo esc_js($item_id); ?>').on('change', function() {
                var $filterTypeField = $(this).closest('.menu-item-settings').find('.field-filter-type');
                var $rolesField = $(this).closest('.menu-item-settings').find('.field-roles');
                var $capabilitiesField = $(this).closest('.menu-item-settings').find('.field-capabilities');
                
                if ($(this).val() === 'logged_in') {
                    $filterTypeField.slideDown();
                    
                    // Show the appropriate field based on the filter type selection
                    var filterType = $('input[name="menu-item-filter-by[<?php echo esc_js($item_id); ?>]"]:checked').val();
                    if (filterType === 'role') {
                        $rolesField.slideDown();
                        $capabilitiesField.slideUp();
                    } else {
                        $rolesField.slideUp();
                        $capabilitiesField.slideDown();
                    }
                } else {
                    $filterTypeField.slideUp();
                    $rolesField.slideUp();
                    $capabilitiesField.slideUp();
                }
            });
            
            // Handle filter type change
            $('input[name="menu-item-filter-by[<?php echo esc_js($item_id); ?>]"]').on('change', function() {
                var $rolesField = $(this).closest('.menu-item-settings').find('.field-roles');
                var $capabilitiesField = $(this).closest('.menu-item-settings').find('.field-capabilities');
                
                if ($(this).val() === 'role') {
                    $rolesField.slideDown();
                    $capabilitiesField.slideUp();
                } else {
                    $rolesField.slideUp();
                    $capabilitiesField.slideDown();
                }
            });
            
            // Handle capability search
            $('.capability-search').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                var $capabilityList = $(this).closest('.field-capabilities').find('.capability-item');
                
                if (searchTerm === '') {
                    // Show all when search term is empty
                    $capabilityList.show();
                } else {
                    // Filter capabilities based on search term
                    $capabilityList.each(function() {
                        var capabilityText = $(this).text().toLowerCase();
                        if (capabilityText.indexOf(searchTerm) > -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
        });
    </script>
    <?php
}
add_action('wp_nav_menu_item_custom_fields', 'distm_add_custom_visibility_field', 10, 4);


// Save the custom visibility field
function distm_save_custom_visibility_field($menu_id, $menu_item_db_id) {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    if (!isset($_POST['distm_custom_menu_nonce']) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['distm_custom_menu_nonce'])), 'distm_custom_menu_fields')) {
        return;
    }

    // Save visibility setting
    if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
        $visibility = sanitize_text_field(wp_unslash($_POST['menu-item-visibility'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_visibility', $visibility);
    }

    // Save filter by setting
    if (isset($_POST['menu-item-filter-by'][$menu_item_db_id])) {
        $filter_by = sanitize_text_field(wp_unslash($_POST['menu-item-filter-by'][$menu_item_db_id]));
        update_post_meta($menu_item_db_id, '_menu_item_filter_by', $filter_by);
    }

    // Save user roles
    $roles = isset($_POST['menu-item-roles'][$menu_item_db_id]) ? 
            array_map('sanitize_text_field', wp_unslash($_POST['menu-item-roles'][$menu_item_db_id])) : 
            array();
    update_post_meta($menu_item_db_id, '_menu_item_roles', $roles);
    
    // Save user capabilities
    $capabilities = isset($_POST['menu-item-capabilities'][$menu_item_db_id]) ? 
            array_map('sanitize_text_field', wp_unslash($_POST['menu-item-capabilities'][$menu_item_db_id])) : 
            array();
    update_post_meta($menu_item_db_id, '_menu_item_capabilities', $capabilities);
}
add_action('wp_update_nav_menu_item', 'distm_save_custom_visibility_field', 10, 2);

// WordPress Walker for the menu
class DISTM_Icon_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        return;
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        return;
    }
    
    // Helper function to check if a menu item should be shown based on visibility settings
    private function check_item_visibility($item_id) {
        $visibility = get_post_meta($item_id, '_menu_item_visibility', true);
        if (empty($visibility)) {
            $visibility = 'everyone';
        }
        
        $roles = get_post_meta($item_id, '_menu_item_roles', true);
        if (!is_array($roles)) {
            $roles = array();
        }
        
        $capabilities = get_post_meta($item_id, '_menu_item_capabilities', true);
        if (!is_array($capabilities)) {
            $capabilities = array();
        }
        
        $filter_by = get_post_meta($item_id, '_menu_item_filter_by', true);
        if (empty($filter_by)) {
            $filter_by = 'role';
        }

        $show_item = false;
        if ($visibility === 'everyone') {
            $show_item = true;
        } elseif ($visibility === 'logged_in' && is_user_logged_in()) {
            if ($filter_by === 'role') {
                // Check user roles
                if (empty($roles) || array_intersect($roles, wp_get_current_user()->roles)) {
                    $show_item = true;
                }
            } else {
                // Check user capabilities
                $current_user = wp_get_current_user();
                if (empty($capabilities)) {
                    // If no capabilities are selected, show to all logged-in users
                    $show_item = true;
                } else {
                    // Check if user has any of the required capabilities
                    foreach ($capabilities as $capability) {
                        if (user_can($current_user, $capability)) {
                            $show_item = true;
                            break;
                        }
                    }
                }
            }
        } elseif ($visibility === 'logged_out' && !is_user_logged_in()) {
            $show_item = true;
        }
        
        return $show_item;
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth !== 0) {
            return;
        }

        $icon_type = get_post_meta($item->ID, '_menu_item_icon_type', true);
        if (!in_array($icon_type, ['dashicon', 'upload'])) {
            $icon_type = 'dashicon';
        }
        
        $dashicon = get_post_meta($item->ID, '_menu_item_dashicon', true);
        if (empty($dashicon)) {
            $dashicon = 'menu';
        }
        
        $icon_url = esc_url(get_post_meta($item->ID, '_menu_item_icon', true));
        $title = apply_filters('the_title', $item->title, $item->ID);
        $url = $item->url;
        $icon_html = '';

        // Get custom menu classes
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        
        // Add cart class if this is the cart page
        if (distm_is_woocommerce_loaded() && is_object($item) && isset($item->object_id)) {
            $cart_page_id = wc_get_page_id('cart');
            if ($item->object_id == $cart_page_id || (isset($item->url) && trailingslashit($item->url) === trailingslashit(wc_get_cart_url()))) {
                $classes[] = 'menu-item-type-cart';
            }
        }
        
        $classes[] = 'tm-menu-item-' . $item->ID;
        $class_names = join(' ', array_filter($classes));

        if ($icon_type === 'dashicon') {
            $icon_html = sprintf(
                '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                esc_attr($dashicon)
            );
        } elseif ($icon_type === 'upload' && !empty($icon_url)) {
            if (substr($icon_url, -4) === '.svg') {
                $svg_content = distm_get_svg_content($icon_url);
                if ($svg_content !== false) {
                    $icon_html = $svg_content;
                } else {
                    $icon_html = sprintf(
                        '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                        'menu'
                    );
                }
            } else {
                $icon_html = wp_get_attachment_image(attachment_url_to_postid($icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($title), esc_attr__('Icon', 'the-menu'))));
            }
        } else {
            $icon_html = '<span class="dashicons dashicons-menu" aria-hidden="true"></span>';
        }

        // Check if this item should be shown based on visibility settings
        $show_item = $this->check_item_visibility($item->ID);

        if ($show_item) {
            // Check if this is an addon menu item with submenu items
            $options = get_option('distm_settings', array());
            $addon_menu_style = isset($options['distm_addon_menu_style']) ? $options['distm_addon_menu_style'] : 'app-icon';
            $is_addon_menu = isset($args->theme_location) && $args->theme_location === 'addon-menu';
            
            // Check if this item has children
            $has_children = in_array('menu-item-has-children', $classes);
            
            if ($is_addon_menu && $addon_menu_style === 'app-icon' && $has_children) {
                // This is a parent menu item in the addon menu with app-icon style
                $output .= '<li class="' . esc_attr($class_names) . ' tm-folder-item">';
                
                // Get the hide_text option
                $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];
                
                // Create a folder-like container
                $output .= '<div class="tm-folder-container">';
                
                // Add the folder background
                $output .= '<div class="tm-folder-background">';
                
                // Add up to 9 preview icons
                $output .= '<div class="tm-folder-preview">';
                
                // Get the children of this menu item
                $children = $this->get_children($item, $args);
                $preview_count = 0;
                
                foreach ($children as $child) {
                    // Check visibility for each child item
                    if (!$this->check_item_visibility($child->ID)) {
                        continue; // Skip this child if it shouldn't be shown
                    }
                
                    if ($preview_count >= 9) break;
                    
                    $child_icon_type = get_post_meta($child->ID, '_menu_item_icon_type', true);
                    if (!in_array($child_icon_type, ['dashicon', 'upload'])) {
                        $child_icon_type = 'dashicon';
                    }
                    
                    $child_dashicon = get_post_meta($child->ID, '_menu_item_dashicon', true);
                    if (empty($child_dashicon)) {
                        $child_dashicon = 'menu';
                    }
                    
                    $child_icon_url = esc_url(get_post_meta($child->ID, '_menu_item_icon', true));
                    $child_title = apply_filters('the_title', $child->title, $child->ID);
                    
                    $child_icon_html = '';
                    
                    if ($child_icon_type === 'dashicon') {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            esc_attr($child_dashicon)
                        );
                    } elseif ($child_icon_type === 'upload' && !empty($child_icon_url)) {
                        if (substr($child_icon_url, -4) === '.svg') {
                            $svg_content = distm_get_svg_content($child_icon_url);
                            if ($svg_content !== false) {
                                $child_icon_html = $svg_content;
                            } else {
                                $child_icon_html = sprintf(
                                    '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                    'menu'
                                );
                            }
                        } else {
                            $child_icon_html = wp_get_attachment_image(attachment_url_to_postid($child_icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($child_title), esc_attr__('Icon', 'the-menu'))));
                        }
                    } else {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            'menu'
                        );
                    }
                    
                    $output .= '<div class="tm-folder-preview-icon">' . $child_icon_html . '</div>';
                    $preview_count++;
                }
                
                $output .= '</div>'; // End tm-folder-preview
                $output .= '</div>'; // End tm-folder-background
                
               
                
                // Add a hidden container for the submenu items
                $output .= '<div class="tm-folder-content-wrapper" data-parent-id="' . esc_attr($item->ID) . '">';
                $output .= '<div class="tm-folder-header">';
                if (!$hide_text) {
                    $output .= '<div class="tm-folder-header-title">' . esc_html($title) . '</div>';
                }
                $output .= '</div>';
                $output .= '<div class="tm-folder-content">';
                
                $output .= '<div class="tm-folder-items">';
                
                // Add the submenu items
                foreach ($children as $child) {
                    // Check visibility for each child item
                    if (!$this->check_item_visibility($child->ID)) {
                        continue; // Skip this child if it shouldn't be shown
                    }
                
                    $child_icon_type = get_post_meta($child->ID, '_menu_item_icon_type', true);
                    if (!in_array($child_icon_type, ['dashicon', 'upload'])) {
                        $child_icon_type = 'dashicon';
                    }
                    
                    $child_dashicon = get_post_meta($child->ID, '_menu_item_dashicon', true);
                    if (empty($child_dashicon)) {
                        $child_dashicon = 'menu';
                    }
                    
                    $child_icon_url = esc_url(get_post_meta($child->ID, '_menu_item_icon', true));
                    $child_title = apply_filters('the_title', $child->title, $child->ID);
                    $child_url = $child->url;
                    
                    $child_icon_html = '';
                    
                    if ($child_icon_type === 'dashicon') {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            esc_attr($child_dashicon)
                        );
                    } elseif ($child_icon_type === 'upload' && !empty($child_icon_url)) {
                        if (substr($child_icon_url, -4) === '.svg') {
                            $svg_content = distm_get_svg_content($child_icon_url);
                            if ($svg_content !== false) {
                                $child_icon_html = $svg_content;
                            } else {
                                $child_icon_html = sprintf(
                                    '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                    'menu'
                                );
                            }
                        } else {
                            $child_icon_html = wp_get_attachment_image(attachment_url_to_postid($child_icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($child_title), esc_attr__('Icon', 'the-menu'))));
                        }
                    } else {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            'menu'
                        );
                    }
                    
                    $output .= '<a href="' . esc_url($child_url) . '" class="tm-folder-item-link" onclick="window.location.href=\'' . esc_url($child_url) . '\';">';
                    $output .= '<div class="tm-folder-item-icon">' . $child_icon_html . '</div>';
                    if (!$hide_text) {
                        $output .= '<div class="tm-folder-item-title">' . esc_html($child_title) . '</div>';
                    }
                    $output .= '</a>';
                }
                
                $output .= '</div>'; // End tm-folder-items
                $output .= '</div>'; // End tm-folder-content
                
                $output .= '</div>'; // End tm-folder-content-wrapper
                // Add the folder title
                if (!$hide_text) {
                    $output .= '<div class="tm-folder-title">' . esc_html($title) . '</div>';
                }
                $output .= '</li>';
            } elseif ($is_addon_menu && $addon_menu_style === 'icon' && $has_children) {
                // This is a parent menu item in the addon menu with icon style
                $output .= '<li class="' . esc_attr($class_names) . ' tm-folder-item">';
                
                // Get the hide_text option
                $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];
                
                // Create a folder-like container
                $output .= '<div class="tm-folder-container">';
                
                // Add the folder background
                $output .= '<div class="tm-folder-background">';
                
                // Add up to 9 preview icons
                $output .= '<div class="tm-folder-preview">';
                
                // Get the children of this menu item
                $children = $this->get_children($item, $args);
                $preview_count = 0;
                
                foreach ($children as $child) {
                    // Check visibility for each child item
                    if (!$this->check_item_visibility($child->ID)) {
                        continue; // Skip this child if it shouldn't be shown
                    }
                
                    if ($preview_count >= 9) break;
                    
                    $child_icon_type = get_post_meta($child->ID, '_menu_item_icon_type', true);
                    if (!in_array($child_icon_type, ['dashicon', 'upload'])) {
                        $child_icon_type = 'dashicon';
                    }
                    
                    $child_dashicon = get_post_meta($child->ID, '_menu_item_dashicon', true);
                    if (empty($child_dashicon)) {
                        $child_dashicon = 'menu';
                    }
                    
                    $child_icon_url = esc_url(get_post_meta($child->ID, '_menu_item_icon', true));
                    $child_title = apply_filters('the_title', $child->title, $child->ID);
                    
                    $child_icon_html = '';
                    
                    if ($child_icon_type === 'dashicon') {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            esc_attr($child_dashicon)
                        );
                    } elseif ($child_icon_type === 'upload' && !empty($child_icon_url)) {
                        if (substr($child_icon_url, -4) === '.svg') {
                            $svg_content = distm_get_svg_content($child_icon_url);
                            if ($svg_content !== false) {
                                $child_icon_html = $svg_content;
                            } else {
                                $child_icon_html = sprintf(
                                    '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                    'menu'
                                );
                            }
                        } else {
                            $child_icon_html = wp_get_attachment_image(attachment_url_to_postid($child_icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($child_title), esc_attr__('Icon', 'the-menu'))));
                        }
                    } else {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            'menu'
                        );
                    }
                    
                    $output .= '<div class="tm-folder-preview-icon">' . $child_icon_html . '</div>';
                    $preview_count++;
                }
                
                $output .= '</div>'; // End tm-folder-preview
                $output .= '</div>'; // End tm-folder-background
                
                // Add a hidden container for the submenu items
                $output .= '<div class="tm-folder-content-wrapper" data-parent-id="' . esc_attr($item->ID) . '">';
                $output .= '<div class="tm-folder-header">';
                if (!$hide_text) {
                    $output .= '<div class="tm-folder-header-title">' . esc_html($title) . '</div>';
                }
                $output .= '</div>';
                $output .= '<div class="tm-folder-content">';
                
                $output .= '<div class="tm-folder-items">';
                
                // Add the submenu items
                foreach ($children as $child) {
                    // Check visibility for each child item
                    if (!$this->check_item_visibility($child->ID)) {
                        continue; // Skip this child if it shouldn't be shown
                    }
                
                    $child_icon_type = get_post_meta($child->ID, '_menu_item_icon_type', true);
                    if (!in_array($child_icon_type, ['dashicon', 'upload'])) {
                        $child_icon_type = 'dashicon';
                    }
                    
                    $child_dashicon = get_post_meta($child->ID, '_menu_item_dashicon', true);
                    if (empty($child_dashicon)) {
                        $child_dashicon = 'menu';
                    }
                    
                    $child_icon_url = esc_url(get_post_meta($child->ID, '_menu_item_icon', true));
                    $child_title = apply_filters('the_title', $child->title, $child->ID);
                    $child_url = $child->url;
                    
                    $child_icon_html = '';
                    
                    if ($child_icon_type === 'dashicon') {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            esc_attr($child_dashicon)
                        );
                    } elseif ($child_icon_type === 'upload' && !empty($child_icon_url)) {
                        if (substr($child_icon_url, -4) === '.svg') {
                            $svg_content = distm_get_svg_content($child_icon_url);
                            if ($svg_content !== false) {
                                $child_icon_html = $svg_content;
                            } else {
                                $child_icon_html = sprintf(
                                    '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                    'menu'
                                );
                            }
                        } else {
                            $child_icon_html = wp_get_attachment_image(attachment_url_to_postid($child_icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($child_title), esc_attr__('Icon', 'the-menu'))));
                        }
                    } else {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            'menu'
                        );
                    }
                    
                    $output .= '<a href="' . esc_url($child_url) . '" class="tm-folder-item-link" onclick="window.location.href=\'' . esc_url($child_url) . '\';">';
                    $output .= '<div class="tm-folder-item-icon">' . $child_icon_html . '</div>';
                    if (!$hide_text) {
                        $output .= '<div class="tm-folder-item-title">' . esc_html($child_title) . '</div>';
                    }
                    $output .= '</a>';
                }
                
                $output .= '</div>'; // End tm-folder-items
                $output .= '</div>'; // End tm-folder-content
                
                $output .= '</div>'; // End tm-folder-content-wrapper
                // Add the folder title
                if (!$hide_text) {
                    $output .= '<div class="tm-folder-title">' . esc_html($title) . '</div>';
                }
                $output .= '</li>';
            } elseif ($is_addon_menu && $addon_menu_style === 'list' && $has_children) {
                // This is a parent menu item in the addon menu with list/accordion style
                $output .= '<li class="' . esc_attr($class_names) . ' tm-accordion-item">';
                
                // Get the hide_text option
                $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];
                
                // Create the accordion header
                $output .= '<div class="tm-accordion-header">';
                
                // Add the parent icon
                $icon_type = get_post_meta($item->ID, '_menu_item_icon_type', true);
                if (!in_array($icon_type, ['dashicon', 'upload'])) {
                    $icon_type = 'dashicon';
                }
                
                $dashicon = get_post_meta($item->ID, '_menu_item_dashicon', true);
                if (empty($dashicon)) {
                    $dashicon = 'menu';
                }
                
                $icon_url = esc_url(get_post_meta($item->ID, '_menu_item_icon', true));
                
                $icon_html = '';
                
                if ($icon_type === 'dashicon') {
                    $icon_html = sprintf(
                        '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                        esc_attr($dashicon)
                    );
                } elseif ($icon_type === 'upload' && !empty($icon_url)) {
                    if (substr($icon_url, -4) === '.svg') {
                        $svg_content = distm_get_svg_content($icon_url);
                        if ($svg_content !== false) {
                            $icon_html = $svg_content;
                        } else {
                            $icon_html = sprintf(
                                '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                'menu'
                            );
                        }
                    } else {
                        $icon_html = wp_get_attachment_image(attachment_url_to_postid($icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($title), esc_attr__('Icon', 'the-menu'))));
                    }
                } else {
                    $icon_html = '<span class="dashicons dashicons-menu" aria-hidden="true"></span>';
                }
                
                $output .= '<div class="tm-accordion-icon">' . $icon_html . '</div>';
                
                // Add the title if not hidden
                if (!$hide_text) {
                    $output .= '<div class="tm-accordion-title">' . esc_html($title) . '</div>';
                }
                
                // Add the toggle button
                $output .= '<div class="tm-accordion-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></div>';
                
                $output .= '</div>'; // End tm-accordion-header
                
                // Add the accordion content
                $output .= '<div class="tm-accordion-content">';
                
                // Get the children of this menu item
                $children = $this->get_children($item, $args);
                
                // Add the submenu items
                foreach ($children as $child) {
                    // Check visibility for each child item
                    if (!$this->check_item_visibility($child->ID)) {
                        continue; // Skip this child if it shouldn't be shown
                    }
                
                    $child_icon_type = get_post_meta($child->ID, '_menu_item_icon_type', true);
                    if (!in_array($child_icon_type, ['dashicon', 'upload'])) {
                        $child_icon_type = 'dashicon';
                    }
                    
                    $child_dashicon = get_post_meta($child->ID, '_menu_item_dashicon', true);
                    if (empty($child_dashicon)) {
                        $child_dashicon = 'menu';
                    }
                    
                    $child_icon_url = esc_url(get_post_meta($child->ID, '_menu_item_icon', true));
                    $child_title = apply_filters('the_title', $child->title, $child->ID);
                    $child_url = $child->url;
                    
                    $child_icon_html = '';
                    
                    if ($child_icon_type === 'dashicon') {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            esc_attr($child_dashicon)
                        );
                    } elseif ($child_icon_type === 'upload' && !empty($child_icon_url)) {
                        if (substr($child_icon_url, -4) === '.svg') {
                            $svg_content = distm_get_svg_content($child_icon_url);
                            if ($svg_content !== false) {
                                $child_icon_html = $svg_content;
                            } else {
                                $child_icon_html = sprintf(
                                    '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                                    'menu'
                                );
                            }
                        } else {
                            $child_icon_html = wp_get_attachment_image(attachment_url_to_postid($child_icon_url), array(40, 40), false, array('class' => 'tm-menu-icon', 'alt' => sprintf('%s %s', esc_attr($child_title), esc_attr__('Icon', 'the-menu'))));
                        }
                    } else {
                        $child_icon_html = sprintf(
                            '<span class="dashicons dashicons-%s" aria-hidden="true"></span>',
                            'menu'
                        );
                    }
                    
                    $output .= '<a href="' . esc_url($child_url) . '" class="tm-accordion-item-link">';
                    $output .= '<div class="tm-accordion-item-icon">' . $child_icon_html . '</div>';
                    if (!$hide_text) {
                        $output .= '<div class="tm-accordion-item-title">' . esc_html($child_title) . '</div>';
                    }
                    $output .= '</a>';
                }
                
                $output .= '</div>'; // End tm-accordion-content
                $output .= '</li>';
            } else {
                // Regular menu item
                $output .= '<li class="' . esc_attr($class_names) . '">';
                $options = get_option('distm_settings');
                $hide_text = isset($options['distm_disable_menu_text']) && $options['distm_disable_menu_text'];

                // Combine icon and title into a single link
                $output .= '<a href="' . esc_url($url) . '">' . $icon_html;
                
                // Check if this is a WooCommerce cart menu item
                $wc_cart = distm_get_wc_cart();
                if (distm_is_woocommerce_loaded() && $wc_cart && in_array('menu-item-type-cart', $classes)) {
                    $cart_count = $wc_cart->get_cart_contents_count();
                    if ($cart_count > 0) {
                        $output .= '<span class="tm-cart-count">' . esc_html($cart_count) . '</span>';
                    }
                }
                
                // Add the title if not hidden
                if (!$hide_text) {
                    $output .= '<span class="tm-menu-item-title">' . esc_html($title) . '</span>';
                }
                
                $output .= '</a>';
                $output .= '</li>';
            }
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0) {
            return;
        }
    }
    
    // Helper function to get children of a menu item
    function get_children($item, $args) {
        $children = array();
        
        if (empty($args->menu)) {
            return $children;
        }
        
        $menu_items = wp_get_nav_menu_items($args->menu);
        if (!$menu_items) {
            return $children;
        }
        
        foreach ($menu_items as $menu_item) {
            if ($menu_item->menu_item_parent == $item->ID) {
                $children[] = $menu_item;
            }
        }
        
        return $children;
    }
}


/**
 * Handle icon upload via AJAX
 * 
 * @return void
 */
function distm_handle_icon_upload() {
    // Verify nonce with proper sanitization
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'distm_ajax_nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }

    // Verify user capabilities
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Insufficient permissions');
        exit;
    }

    // Process the upload
    $attachment_id = media_handle_upload('icon_file', 0);
    if (is_wp_error($attachment_id)) {
        wp_send_json_error($attachment_id->get_error_message());
        exit;
    }

    $attachment_url = wp_get_attachment_url($attachment_id);
    wp_send_json_success(array('url' => esc_url($attachment_url)));
}
add_action('wp_ajax_distm_upload_icon', 'distm_handle_icon_upload');

/**
 * Update menu item settings via AJAX
 * 
 * @return void
 */
function distm_update_menu_item() {
    // Verify nonce with proper sanitization
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'distm_menu_nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }

    // Verify user capabilities
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error('Insufficient permissions');
        exit;
    }

    // Validate menu item ID
    $menu_item_id = isset($_POST['menu_item_id']) ? absint($_POST['menu_item_id']) : 0;
    if (!$menu_item_id) {
        wp_send_json_error('Invalid menu item ID');
        exit;
    }

    // Sanitize and unslash all input data
    $icon_type = isset($_POST['icon_type']) ? 
        sanitize_text_field(wp_unslash($_POST['icon_type'])) : '';
    
    $icon_url = isset($_POST['icon_url']) ? 
        esc_url_raw(wp_unslash($_POST['icon_url'])) : '';
    
    $dashicon = isset($_POST['dashicon']) ? 
        sanitize_text_field(wp_unslash($_POST['dashicon'])) : '';

    // Validate icon type
    if (!empty($icon_type) && !in_array($icon_type, array('dashicon', 'upload'), true)) {
        wp_send_json_error('Invalid icon type');
        exit;
    }

    // Update post meta with sanitized values
    update_post_meta($menu_item_id, '_menu_item_icon_type', $icon_type);
    update_post_meta($menu_item_id, '_menu_item_icon', $icon_url);
    update_post_meta($menu_item_id, '_menu_item_dashicon', $dashicon);

    wp_send_json_success('Menu item updated successfully');
}
add_action('wp_ajax_distm_update_menu_item', 'distm_update_menu_item');

/**
 * Verify license key via AJAX
 * 
 * @return void
 */
function distm_verify_license() {
    // Verify nonce with proper sanitization
    if (!isset($_POST['nonce']) || 
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'distm_ajax_nonce')) {
        wp_send_json_error('Invalid nonce');
        exit;
    }

    // Verify user capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        exit;
    }

    // Sanitize and validate license key
    $license_key = isset($_POST['license_key']) ? 
        sanitize_text_field(wp_unslash($_POST['license_key'])) : '';
    
    if (empty($license_key)) {
        wp_send_json_error('Invalid license key');
        exit;
    }

    // Your existing license verification logic here
    $result = distm_validate_license($license_key);
    
    wp_send_json_success($result);
}
add_action('wp_ajax_distm_verify_license', 'distm_verify_license');

// Add a helper function to check if WooCommerce is fully loaded
function distm_is_woocommerce_loaded() {
    return class_exists('WooCommerce') && function_exists('WC') && function_exists('wc_get_page_id') && function_exists('wc_get_cart_url');
}

// Add a helper function to get the WooCommerce cart
function distm_get_wc_cart() {
    if (class_exists('WooCommerce')) {
        global $woocommerce;
        if (isset($woocommerce) && is_object($woocommerce) && isset($woocommerce->cart)) {
            return $woocommerce->cart;
        }
    }
    return null;
}

