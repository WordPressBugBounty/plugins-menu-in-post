=== Menu In Post ===
Contributors: linux4me2
Tags: display menu, menu, shortcode, menu in post, menu in page
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0-standalone.html

A simple but flexible plugin to allow the use of menus in posts and pages.

== Description ==

With Menu In Post, you can create shortcodes to display menus as hyperlinked lists or dropdowns in posts, pages, and shortcode widgets. Menus are easier to manage through the WordPress user interface (UI) than a list of links. You can update a menu in one place (Appearance > Menus) and it will be updated everywhere you have added a Menu In Post shortcode for it. 

= Features =

* Admin UI in Tools for quickly building shortcodes
* Display menus as lists or drop-downs
* Configurable depth-level for submenus
* Configurable ordering (via WordPress Menus UI)
* Set optional IDs and/or classes to menu containers and lists
* Append a text string to the URL of all menu items
* Compatible up to PHP 8.1
* Admin UI in Settings to tweak JavaScript loading and minification
* Works with block-enabled themes with option to display classic menu editor

= Use =

1. In WordPress Admin, look on the 'Tools' menu for Menu In Post Tools.
1. Use Menu In Post Tools' Shortcode Builder to build your menu shortcodes.
1. Copy the menu shortcode and paste it into a Shortcode Block in the Gutenberg editor in a page, post or widget.
1. Use the classic menu editor (Appearance > Menus) to add/edit menus.
1. Optionally, select options in Settings > Menu In Post. No changes are necessary to use the plugin.

= Privacy Notice =

Menu In Post does not:

* track users by stealth
* write personal user data to the database
* send data to external servers
* use cookies

= Translations =

You can translate Menu In Post on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/menu-in-post).

== Installation ==

1. Upload the entire `menu-in-post` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

You will find a 'Menu In Post' menu in your WordPress admin Tools panel after installation and activation.

== Screenshots ==

1. Menu In Post Tools
2. Add a Shortcode Block from the Widgets Section of the Add Block Menu to add a Shortcode Block.
3. A Menu In Post shortcode in a Shortcode Block.

== Changelog ==

= 1.3 =

* Add honoring WordPress menu item target with dropdown-style menus.

= 1.2.6 =

* Fix Undefined array key "mipshowappearancemenus" warning in admin.php when saving settings in non-block-enabled themes.

= 1.2.5 =

* Add wp_admin_notice() for admin notices for WP >= 6.4 with fallback for earlier versions.

= 1.2.4 =

* Updates for PHP 8.2 compatibility.

= 1.2.3 =

* Updates for PHP 8.2 compatibility.

= 1.2.2 =

* Added the option (Settings > Menu In Post) to display the classic menu editor (Appearance > Menus), required to add/edit menus for Menu In Post.

= 1.2.1 =

* Removed list tags from Options help to improve translation.

= 1.2.0 =

* Added Settings > Menu In Post to optionally configure loading of JavaScript (required for drop-down menus).
* Added uninstall script to remove data from wp_options table on plugin removal.
* Some code tweaks for PEAR compliance.

= 1.1.9 =

* Fixed deprecation notice for optional parameters preceding required parameters in PHP 8.x.

= 1.1.8 =

* Add selected attribute to drop-down option if current page. Thanks to @oliveirataquari for the request and @kojotkk for the code suggestion.

= 1.1.7 =

* PHP clean-up.
* Verify compatibility with PHP 8.0.
* Fix JavaScript for menu depth value in shortcode.
* CSS improvements for cleaner display of Tools Page.

= 1.1.6 =

* Fixed missing placeholder text in Admin no-JavaScript fallback.
* Added append to URL feature for anchors and/or variables.

= 1.1.5 =

* Updated JavaScript files.

= 1.1.4 =

* Added configurable placeholder text attribute to shortcode for dropdown-style menus.

= 1.1.3 =

* Added Copy Shortcode button. Minor admin CSS cleanup.

= 1.1.2 =

* Add backward compatibility for shortcodes with no style specified to default to list.

= 1.1.1 =

* Add missing JavaScript files.

= 1.1.0 =

* Added the option of selecting a menu style, either the default unordered list of links or a dropdown. Thanks to kingfisher64 for the suggestion.
* Minor code cleanup.

= 1.0.0 =

* Initial release.

