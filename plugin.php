<?php
/**
 * Plugin Name: Genesis 404
 * Plugin URI: https://github.com/billerickson/Genesis-404-Plugin
 * Description: Customize the content of your 404 page.
 * Version: 1.3
 * Author: Bill Erickson
 * Author URI: http://www.billerickson.net
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

class BE_Genesis_404 {
	var $instance;
	
	/**
	 * Construct
	 *
	 * Registers our activation hook and init hook
	 *
	 * @since 1.0
	 * @author Bill Erickson
	 */
	function __construct() {
		$this->instance =& $this;
		register_activation_hook( __FILE__, array( $this, 'activation_hook' ) );
		add_action( 'init', array( $this, 'init' ) );	
	}
	
	/**
	 * Activation Hook
	 *
	 * Confirm site is using Genesis
	 *
	 * @since 1.0
	 * @author Bill Erickson
	 */
	function activation_hook() {
		if ( 'genesis' != basename( TEMPLATEPATH ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( sprintf( __( 'Sorry, you can&rsquo;t activate unless you have installed <a href="%s">Genesis</a>', 'genesis-title-toggle'), 'http://www.billerickson.net/get-genesis' ) );
		}
	}

	/**
	 * Init
	 *
	 * Register all our functions to the appropriate hook
	 *
	 * @since 1.0
	 * @author Bill Erickson
	 */
	function init() {

		// Translations
		load_plugin_textdomain( 'genesis-404', false, basename( dirname( __FILE__ ) ) . '/languages' );
		
		// Check to see if should be used
		add_action( 'genesis_meta', array( $this, 'maybe_custom_404' ) );
		
		// Search Shortcode
		add_shortcode( 'genesis-404-search', array( $this, 'search_shortcode' ) );

	}	

	/**
	 * Check if custom 404 should be used
	 *
	 * @since 1.0
	 * @author Bill Erickson
	 */
	function maybe_custom_404() {
		if( is_404() && genesis_get_option( 'content', 'genesis-404' ) ) {
		
			remove_action( 'genesis_loop', 'genesis_404' );
			add_action( 'genesis_loop', array( $this, 'be_genesis_404_loop' ) );
			
		}
	}
	
	/**
	 * Genesis 404 Loop
	 *
	 * @since 1.0
	 * @author Bill Erickson
	 */
	function be_genesis_404_loop() {
		
		$title = esc_attr( genesis_get_option( 'title', 'genesis-404' ) );
		$content = genesis_get_option( 'content', 'genesis-404' );
		
		// HTML 5
		if( function_exists( 'genesis_html5' ) && genesis_html5() ) {
			echo '<article class="page type-page status-publish entry" itemscope="" itemtype="http://schema.org/CreativeWork">';
			
			if( !empty( $title ) )
				echo '<header class="entry-header"><h1 class="entry-title" itemprop="headline">' . $title . '</h1></header>';
			
			do_action( 'genesis_404_before_content' );
			
			if( !empty( $content ) )	
				echo '<div class="entry-content" itemprop="text">' . apply_filters( 'the_content', $content ) . '</div>';
				
			do_action( 'genesis_404_after_content' );
				
			echo '</article>';
		
		// HTML 4
		} else {
		
			echo '<div class="post hentry">';
			
			if (!empty( $title ) )
				echo '<h1 class="entry-title">' . $title . '</h1>';
				
			do_action( 'genesis_404_before_content' );
				
			if( !empty( $content ) )
				echo '<div class="entry-content">' . apply_filters( 'the_content', $content ) . '</div>';
				
			do_action( 'genesis_404_after_content' );
	
			echo '</div>';
		}
	
	}
	
	/**
	 * Search Shortcode
	 *
	 * @since 1.1
	 * @author Bill Erickson
	 */
	function search_shortcode() {
		return '<div class="genesis-404-search">' . get_search_form( false ) . '</div>';
	}


}

new BE_Genesis_404; 

 
/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Child Theme Settings page.
 *
 * @since 1.0
 * @author Bill Erickson
 */
function be_register_genesis_404_settings() {

	class BE_Genesis_404_Settings extends Genesis_Admin_Boxes {
	
		/**
		 * Create an admin menu item and settings page.
		 * @since 1.0.0
		 */
		function __construct() {
	
			// Specify a unique page ID. 
			$page_id = 'genesis-404';
	
			// Set it as a child to genesis, and define the menu and page titles
			$menu_ops = array(
				'submenu' => array(
					'parent_slug' => 'genesis',
					'page_title'  => __( 'Genesis - 404 Page', 'genesis-404' ),
					'menu_title'  => __( '404 Page', 'genesis-404' ),
				)
			);
	
			// Set up page options. These are optional, so only uncomment if you want to change the defaults
			$page_ops = array(
			//	'screen_icon'       => 'options-general',
			//	'save_button_text'  => 'Save Settings',
			//	'reset_button_text' => 'Reset Settings',
			//	'save_notice_text'  => 'Settings saved.',
			//	'reset_notice_text' => 'Settings reset.',
			);		
	
			// Give it a unique settings field. 
			// You'll access them from genesis_get_option( 'option_name', 'child-settings' );
			$settings_field = 'genesis-404';
	
			// Set the default values
			$default_settings = array(
				'title'   => '',
				'content' => '',
			);
	
			// Create the Admin Page
			$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );
	
			// Initialize the Sanitization Filter
			add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitization_filters' ) );
	
		}
	
		/** 
		 * Set up Sanitization Filters
		 * @since 1.0.0
		 *
		 * See /lib/classes/sanitization.php for all available filters.
		 */	
		function sanitization_filters() {
	
			genesis_add_option_filter( 'no_html', $this->settings_field,
				array( 
					'title',
				) );
			genesis_add_option_filter( 'safe_html', $this->settings_field,
				array(
					'content',
				) );
		}
	
		/**
		 * Register metaboxes on Child Theme Settings page
		 * @since 1.0.0
		 */
		function metaboxes() {
	
			add_meta_box('metabox_404', __( '404 Page', 'genesis-404' ), array( $this, 'metabox_404' ), $this->pagehook, 'main', 'high');
	
		}
	
		/**
		 * 404 Metabox
		 * @since 1.0.0
		 */
		function metabox_404() {
	
		echo '<p>' . __( 'Page Title', 'genesis-404' ) . '<br />
		<input type="text" name="' . $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' .  esc_attr( $this->get_field_value( 'title' ) ) . '" size="27" /></p>';
	
	
		echo '<p>' . __( 'Page Content', 'genesis-404' ) . '</p>';
		wp_editor( genesis_get_option( 'content', 'genesis-404' ), 'content', array( 'textarea_name' => $this->get_field_id( 'content' ), ) );  
		}
	
	
	}
	
	global $_be_genesis_404_settings;
	$_be_genesis_404_settings = new BE_Genesis_404_Settings;	 	
	
}	
add_action( 'genesis_admin_menu', 'be_register_genesis_404_settings'  ); 
