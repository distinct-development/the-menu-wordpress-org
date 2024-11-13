# The Menu: Custom mobile navigation with icons

Create beautiful mobile navigation menus with custom icons, role-based visibility, and extensive style options for your WordPress site.

## Description

Transform your WordPress site's mobile navigation with The Menu - a powerful mobile-friendly navigation solution featuring custom icons, role-based visibility, and extensive customisation options. Perfect for creating professional bottom navigation bars, sticky menus, and app-like mobile experiences. This responsive menu plugin offers SVG icon support, smooth animations, and seamless integration with popular themes and page builders.

The Menu plugin is designed to enhance your website's usability and aesthetic appeal, providing a navigation experience that's both visually stunning and functionally robust. Whether you're building a mobile-first website, an online shop, or a Progressive Web App (PWA), The Menu adapts to your needs.

### Core features

- Mobile-optimised bottom navigation bar
- Custom SVG icon support and built-in Dashicons
- Extensive colour customisation options
- Transparent menus that adapt on scroll
- Role-based menu item visibility
- Additional "add-on" menu for extra navigation items
- App-like mobile navigation experience
- Smooth animations and transitions
- Compatible with popular page builders
- PWA-friendly design
- Responsive and touch-optimised
- Custom icon upload support
- Page-specific menu visibility
- Multiple menu styles (Pill, Rounded, Flat)

### Perfect for

- Mobile-first websites
- Online shops
- Restaurant menus
- Progressive Web Apps
- Service businesses
- Portfolio sites
- Business directories
- Event websites
- Community platforms
- Educational sites

### Professional customisation

The Menu offers extensive customisation options to match your brand:

- Custom colours for icons and backgrounds
- Multiple menu styles and layouts
- Icon-only or icon-with-text display
- Transparent scroll effects
- Custom icon uploads
- Built-in Dashicons support

### Advanced features

- **Role-based visibility:** Show different menu items to different user roles
- **Page exclusions:** Choose which pages show or hide the menu
- **Add-on menu:** Additional menu space for extra navigation items
- **Mobile-only option:** Display the menu only on mobile devices
- **Loading animations:** Smooth transitions between pages
- **SVG colour controls:** Customise icon colours directly
- **Multiple menu positions:** Left, right, and add-on menu locations
- **Transparent effects:** Menu adapts as users scroll

### Compatible with popular tools

Works seamlessly with:
- WooCommerce
- Elementor
- Divi Builder
- Gutenberg
- Popular WordPress themes
- Custom theme frameworks
- Other navigation plugins

## Requirements

- WordPress 6.0 or higher
- PHP 7.0 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/the-menu` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to 'The Menu' in your admin sidebar to configure the plugin
4. Set up your menus under 'Appearance > Menus'

### Quick start guide

1. Create a new menu in WordPress
2. Assign it to one of The Menu's locations (Left, Right, or Add-on)
3. Add icons to your menu items
4. Customise colours and styles
5. Configure visibility settings
6. Test on mobile devices

## Frequently Asked Questions

### How do I add icons to menu items?

You can add icons in two ways:
1. Upload custom SVG icons through the menu item settings
2. Select from the built-in Dashicons library

Each menu item has these options when editing in the WordPress admin area.

### Can I control who sees certain menu items?

Yes, you have granular control over menu item visibility:
- Show to everyone
- Show only to logged-in users
- Show only to logged-out users
- Show to specific user roles
- Hide on specific pages

### Is the menu responsive?

Yes, The Menu is fully responsive and mobile-optimised. You can:
- Set it to appear only on mobile devices
- Configure different styles for various screen sizes
- Optimise for both portrait and landscape orientations

### Will it work with my theme?

The Menu is designed to work with any properly coded WordPress theme. It's been tested with:
- Popular theme frameworks
- Custom themes
- WooCommerce themes
- Page builder themes

### Can I customise the colours?

Yes, you have complete control over:
- Background colours
- Icon colours
- Text colours
- Featured item colours
- Add-on menu colours
- Transparency effects

### Does it support touch gestures?

Yes, The Menu is optimised for touch devices with:
- Smooth touch interactions
- Touch-friendly hit areas
- Mobile-optimised animations

## License

This project is licensed under the GPL-2.0-or-later License.

## Support

For support, please visit our [support forum](https://wordpress.org/support/plugin/the-menu/) or contact us at hello@distinct.africa.

## Changelog

### 1.2.13 - 13/11/2024

- With the release of WordPress 6.7 we have checked that The Menu is still running as expected... it is.
- Updated Walker classed to not display submenu items, there is no support for submenus at this stage.
- Updated the help page to be more descriptive.
- FIXED: Dashicon colours didn't update in the add-on menu, these now update to your selected icon colour.
- FIXED: Icon upload didn't save in the native menu editor, this has now been corrected.

### 1.2.12 - 08/11/2024

- Enhancements for first time users looking at the live preview
- You can now click menu locations to create menus for that location
- In the preview clicking an item will link correct menu for the user to edit it
- Tweaks and enhancements to flex on the addon-menu to make it a bit mor asthetically pleasing

### 1.2.11 - 01/11/2024

- New live preview system with real-time updates for all styling changes
- New tab-based settings organization for better UX
- SVG sanitization and security improvements
- Fixed icon sizing inconsistencies
- New WordPress Walker class for preview functionality

### 1.2.10 - 29/10/2024

- [HOTFIX] Dashicon and menu styling was a little off on some sites
- Now allows scrolling in the addon menu

### 1.2.9 - 29/10/2024

- [NOTE] Please check your site once updated, the new dashicons functionallity may override current uploaded icons, which means you may need to re-select them.
- Added default dashicons to select for menu icons incase you don't have svg icons to upload.
- Fixed icon colours in the add-on menu, it was changing paths colours only and now it looks for 'circle', 'rect', etc. as well.
- Add-on menu items weren't lining up nicely when link text wrapping varied, this has been fixed so that they line up.
- When trying to set the visibility of a menu item to logged in users you had to save the menu and then open the menu item again to be able to select roles, this has now been updated to show immediately.

### 1.2.7 - 23/10/2024

- Added loading of default colours if no settings are set on plugin activation
- Added delete data on uninstall option. If checked, all plugin data and settings will be removed when deleting the plugin from the plugins page. This will not affect data if you remove files via FTP or deactivate the plugin.
- Better handling of plugin activation and deactivation
- FIXED: On some sites when a featured icon was not set it could cause a fatal error.

### 1.2.6 - 21/10/2024

- FIXED: There was an issue where excluded pages weren't being saved, this has been resolved.
- Updated UI branding to match the wordpress.org plugin page branding

### 1.2.5 - 19/10/2024

- FIXED: Media library wouldn't open when trying to upload a featured icon

### 1.2.4 - 19/10/2024
- Improved the overall security and robustness of the plugin.
- Updated the contributors of the plugin.

### 1.2.3 - 18/10/2024
- Improved the overall security and robustness of the plugin by ensuring proper sanitization of user input and escaping of output. The functionality of the plugin remains the same, but it's now more resilient against potential security issues.


### 1.2.2 - 07/08/2024
- This version fixes a security related bug.

### 1.2.1 - 04/08/2024
- WP version check
- Fixed an issue where if you disabled transparency on scroll it was still trying to detect scroll and was showing errors in console every time you scrolled

### 1.2.0 - 08/07/2024
- Added the ability to select who can see certain menu items (Everyone, Logged out users, or Logged in users) and if Logged in users is selected you are able to chose which roles can see the menu item.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Authors

- [Distinct Development](https://github.com/distinct-development)
- [Ryan Wiggill](https://github.com/ryan-wiggill)