=== LuckyWP ACF Menu Field ===
Contributors: theluckywp
Donate link: https://theluckywp.com/
Tags: advanced custom fields, acf, menu, menus, nav menu
Requires at least: 4.7
Tested up to: 6.8.1
Stable tag: 1.0.3
Requires PHP: 5.6.20
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add navigation menu field type to Advanced Custom Fields

== Description ==

Add [navigation menu](https://codex.wordpress.org/Navigation_Menus) field type to [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/).

#### Features

* Customizable return value: ID, object (WP_Term) or HTML (use function [wp_nav_menu()](https://developer.wordpress.org/reference/functions/wp_nav_menu/)).
* Hook filter `lwpamf_wp_nav_menu_args` to change arguments of [wp_nav_menu()](https://developer.wordpress.org/reference/functions/wp_nav_menu/) function.

### Compatibility

LuckyWP ACF Menu Field is compatible with:

* Advanced Custom Fields 5
* Advanced Custom Fields PRO 5
* Advanced Custom Fields 6
* Advanced Custom Fields PRO 6

== Installation ==

#### Installing from the WordPress control panel

1. Go to the page "Plugins &gt; Add New".
2. Input the name "LuckyWP ACF Menu Field" in the search field
3. Find the "LuckyWP ACF Menu Field" plugin in the search result and click on the "Install Now" button, the installation process of plugin will begin.
4. Click "Activate" when the installation is complete.

#### Installing with the archive

1. Go to the page "Plugins &gt; Add New" on the WordPress control panel
2. Click on the "Upload Plugin" button, the form to upload the archive will be opened.
3. Select the archive with the plugin and click "Install Now".
4. Click on the "Activate Plugin" button when the installation is complete.

#### Manual installation

1. Upload the folder `luckywp-acf-menu-field` to a directory with the plugin, usually it is `/wp-content/plugins/`.
2. Go to the page "Plugins &gt; Add New" on the WordPress control panel
3. Find "LuckyWP ACF Menu Field" in the plugins list and click "Activate".

### After activation

Into ACF field type will appear option "Menu" (group "Relational").

== Screenshots ==

1. Edit Field Configuration
2. Menu Field

== Changelog ==

= 1.0.3 — 2025-06-10 =
* Adapt to WordPress 6.8.

= 1.0.2 — 2024-11-28 =
* Fix deprecated notices.
* Test with WordPress 6.7.

= 1.0.1 — 2024-11-11 =
* Fix "LuckyWP Plugins" page.

= 1.0 — 2020-05-26 =
+ Initial release.
