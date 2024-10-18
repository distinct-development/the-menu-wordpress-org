=== The Menu ===
Contributors: distinct-development, ryan-wiggill
Tags: menu, navigation, custom menu, mobile menu, responsive menu
Requires at least: 6.0
Tested up to: 6.6.2
Stable tag: 1.2.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance your WordPress site with customizable navigation options, mobile support, SVG icons, and extensive color choices.

== Description ==

The Menu plugin for WordPress offers a highly customizable, dynamic navigation solution designed to enhance your website's usability and aesthetic appeal. With features like mobile-friendly design, SVG icon integration, and color customization, it allows you to create a navigation experience that is both visually stunning and functionally robust.

Key Features:

* Mobile-friendly design
* SVG icon support
* Extensive color customization options
* Transparent menus that adapt on scroll
* Option to feature specific menu items with unique icons
* Ability to exclude menus from certain pages
* User role-based menu item visibility
* Additional "add-on" menu for extra navigation items

Whether you're looking to implement transparent menus that adapt on scroll, feature specific menu items with unique icons, or exclude menus from certain pages, The Menu provides all the tools you need.

It's perfect for website owners seeking to elevate their site's navigation with a touch of personal style and professional functionality.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/the-menu` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings -> The Menu screen to configure the plugin.

== Frequently Asked Questions ==

= How do I add icons to my menu items? =

You can add icons to your menu items by editing the menu in the WordPress admin area. Each menu item will have an option to upload an icon.

= Can I control who sees certain menu items? =

Yes, you can set visibility options for each menu item. You can choose to show items to everyone, only logged-in users, or only logged-out users. For logged-in users, you can further specify which user roles can see the item.

= Is the menu responsive? =

Yes, The Menu is designed to be fully responsive and mobile-friendly. You can even set it to only appear on mobile devices if desired.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets directory take precedence. For example, /assets/screenshot-1.png would win over /tags/4.3/screenshot-1.png (or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

== Changelog ==

= 1.2.1 - 04/08/2024 =

- WP version check
- Fixed an issue where if you disabled transparency on scroll it was still trying to detect scroll and was showing errors in console every time you scrolled

= 1.2.0 - 08/07/2024 =

- Added the ability to select who can see certain menu items (Everyone, Logged out users, or Logged in users) and if Logged in users is selected you are able to chose which roles can see the menu item.

= 1.1.19 - 02/07/2024 =

- WP version check

= 1.1.18 - 12/06/2024 =

- WP version check

= 1.1.17 - 27/05/2024 =

- API overhaul
- Tweaks to list option

= 1.1.15 - 15/05/2024 =

- Validation update

= 1.1.14 - 14/05/2024 =

- Update checker validation
- API connection security update

= 1.1.11 - 11/05/2024 =

- Updated license code

= 1.1.9 - 10/05/2024 =

- Add on menu styles
- Adding licensing and install tracking

= 1.1.7 - 03/05/2024 =

- Hot fix: page loader was on wrong z-index level
- Rounded css class
- iOS and Safari fix for featured icon it was not behaving as expected on deeper URLs and not opening the addon menu
- Renaming style classes to prevent future conflicts

= 1.1.4 - 27/04/2024 =

- Admin backend overhaul

= 1.1.2 - 26/04/2024 =

- Changed repository update URL

= 1.1.1 - 26/04/2024 =

- Changed repositories organisation

= 1.1.0 (Release) - 25/04/2024 =

- Finalised update repositories allowing Distinct to push updates for the plugin to installed versions of the plugin

== Upgrade Notice ==

= 1.2.2 =
This version fixes a security related bug. Upgrade immediately.