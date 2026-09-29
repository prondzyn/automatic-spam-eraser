=== Automatic SPAM Eraser ===
Contributors: prondzyn
Tags: comments, spam, eraser, cleaner, cron
Requires at least: 3.0.1
Tested up to: 7.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The plugin is adding a new WP-Cron event which automatically removes all spam comments older than 7 days.

== Description ==

The plugin is adding a new WP-Cron event which automatically removes all spam comments older than 7 days.

The number of days can be changed in Settings > Discussion (default: 7).

**For the proper functioning requires a properly working WP-Cron. If the plugin does not work please check you have WP-Cron correctly working.**

== Installation ==

1. Upload `automatic-spam-eraser.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.1 =
* Added: the number of days is now configurable in Settings > Discussion (default: 7).
* Fixed: on SQLite databases all spam comments were removed regardless of their age.
* Tested up to WordPress 7.1.

= 1.0 =
* Initial release.
