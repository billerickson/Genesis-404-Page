=== Genesis 404 Page ===
Contributors: billerickson, GaryJ
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EDYM76U6BTE5L
Tags: genesis, genesiswp, 404, 
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 1.5.0

Customize the content of the 404 Page within the Genesis Framework.

== Description ==

Customizing the contents of the _Page Not Found_ page in any WordPress theme can be tricky, and usually involes editing the 404.php template file. The Genesis Framework already abstracts the default content of the page into a function, and this plugin can unhook that and replace it with your own custom title and content.

Use [genesis-404-search] shortcode to add a search form to the page.

If you'd like to dynamically list content (ex: most recent posts), I recommend you install the [Display Posts Shortcode](http://www.wordpress.org/extend/plugins/display-posts-shortcode/) and use it in the 404 page's content.

== Installation ==

1. Upload `genesis-404-page` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Customize your 404 page's title and content in Genesis > 404 Page. 

== Screenshots ==

1. Admin screen showing the fields for custom 404 Not Found page title and content.

== Changelog ==

= 1.5.0 (2015-04-17) =
* Add genesis layout support (Joshua David Nelson, @joshuadnelson)

= 1.4.0 (2014-10-08) =
* Add setup for unit tests (Gary Jones).
* Add support for GitHub Updater plugin (Gary Jones).
* Improved appearance of page title input field (Gary Jones).
* Improved loading of language files (Gary Jones).
* Improve support for activation on multi-sites (Gary Jones).
* Improve documentation and code standards (Gary Jones).
* Improve code organization by classes into their own files (Gary Jones).
* Fix incorrect plugin URL (Gary Jones).
* Fix incorrect textdomains (Gary Jones).

= 1.3.0 (2014-04-17) =
* Updated editor for WordPress 3.9, props @joshuadnelson

= 1.2.0 (2012-05-25) =
* Add HTML5 Support (Bill Erickson).

= 1.1.0 (2013-01-11) =
* Add Search functionality (Bill Erickson).

= 1.0.0 (2012-03-27) =
* Initial release (Bill Erickson).

== Upgrade Notice ==

= 1.5.0 =
Add support for overriding the genesis layout on 404 pages.

= 1.4.0 =
Plugin mostly rewritten. Few small fixes made and improvements added.
