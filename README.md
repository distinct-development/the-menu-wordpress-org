# The Menu - WordPress navigation plugin
Enhance your WordPress site with customisable navigation options, mobile support, SVG icons, and extensive colour choices.
## Description
The Menu plugin for WordPress offers a highly customisable, dynamic navigation solution designed to enhance your website's usability and aesthetic appeal. With features like mobile-friendly design, SVG icon integration, and colour customisation, it allows you to create a navigation experience that is both visually stunning and functionally robust.
### Key features:

Mobile-friendly design
SVG icon support
Extensive colour customisation options
Transparent menus that adapt on scroll
Option to feature specific menu items with unique icons
Ability to exclude menus from certain pages
User role-based menu item visibility
Additional "add-on" menu for extra navigation items

Whether you're looking to implement transparent menus that adapt on scroll, feature specific menu items with unique icons, or exclude menus from certain pages, The Menu provides all the tools you need.
It's perfect for website owners seeking to elevate their site's navigation with a touch of personal style and professional functionality.
## Installation

Upload the plugin files to the /wp-content/plugins/the-menu directory, or install the plugin through the WordPress plugins screen directly.
Activate the plugin through the 'Plugins' screen in WordPress.
Use the Settings -> The Menu screen to configure the plugin.

## Frequently asked questions
### How do I add icons to my menu items?
You can add icons to your menu items by editing the menu in the WordPress admin area. Each menu item will have an option to upload an icon.
### Can I control who sees certain menu items?
Yes, you can set visibility options for each menu item. You can choose to show items to everyone, only logged-in users, or only logged-out users. For logged-in users, you can further specify which user roles can see the item.
### Is the menu responsive?
Yes, The Menu is designed to be fully responsive and mobile-friendly. You can even set it to only appear on mobile devices if desired.
## Requirements

- WordPress 6.0 or higher
- PHP 7.0 or higher

## License

This project is licensed under the GPL-2.0-or-later License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please visit our [support forum](https://wordpress.org/support/plugin/the-menu/) or contact us at hello@distinct.africa.

## Changelog

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