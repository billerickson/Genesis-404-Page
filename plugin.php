<?php
/**
 * Genesis 404
 *
 * @package           Genesis_404
 * @author            Bill Erickson
 * @license           GPL-2.0+
 * @link              https://github.com/billerickson/Genesis-404-Page
 * @copyright         2012 Bill Erickson
 *
 * @wordpress-plugin
 * Plugin Name:       Genesis 404
 * Plugin URI:        https://github.com/billerickson/Genesis-404-Page
 * Description:       Customize the content of your 404 (not found) page.
 * Version:           1.5.0
 * Author:            Bill Erickson
 * Author URI:        http://www.billerickson.net
 * Text Domain:       genesis-404-page
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/billerickson/Genesis-404-Page
 * GitHub Branch:     master
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-be-genesis-404.php';
new BE_Genesis_404;

/**
 * Register an admin page for this plugin.
 *
 * @since 1.0.0
 * @author Bill Erickson
 */
function be_register_genesis_404_settings() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-be-genesis-404-settings.php';

	global $_be_genesis_404_settings;
	$_be_genesis_404_settings = new BE_Genesis_404_Settings;

}
add_action( 'genesis_admin_menu', 'be_register_genesis_404_settings'  );

// Register hooks that are fired when the plugin is activated and deactivated respectively.
register_activation_hook( __FILE__, array( 'BE_Genesis_404', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BE_Genesis_404', 'deactivate' ) );
