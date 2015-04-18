<?php
/**
 * Genesis 404
 *
 * @package   Genesis_404
 * @author    Bill Erickson
 * @license   GPL-2.0+
 * @link      https://github.com/billerickson/Genesis-404-Page
 * @copyright 2015 Bill Erickson
 */

/**
 * Plugin class.
 *
 * @package Genesis_404
 * @author  Bill Erickson
 */
class BE_Genesis_404 {

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @type string
	 */
	const PLUGIN_SLUG = 'genesis-404-page';

	/**
	 * Initialize the plugin by setting localization and new site activation hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Check to see if should be used
		add_action( 'genesis_meta', array( $this, 'maybe_custom_404' ) );

		// Search Shortcode
		add_shortcode( 'genesis-404-search', array( $this, 'search_shortcode' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since 1.3.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is
	 *                              disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide  ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}
				restore_current_blog();
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 1.3.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is
	 *                              disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}
				restore_current_blog();
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since 1.3.0
	 *
	 * @param int     $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) )
			return;

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * * not archived
	 * * not spam
	 * * not deleted
	 *
	 * @since 1.3.0
	 *
	 * @return array|false The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 1.3.0
	 */
	private static function single_activate() {
		if ( 'genesis' !== basename( TEMPLATEPATH ) ) {
			deactivate_plugins( plugin_dir_path( plugin_basename( __FILE__ ) ) . 'plugin.php' );
			wp_die( sprintf( __( 'Sorry, you can&rsquo;t activate unless you have installed <a href="%s">Genesis</a>', 'genesis-404-page' ), 'http://www.billerickson.net/get-genesis' ) );
		}
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 1.3.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.3.0
	 */
	public function load_plugin_textdomain() {
		$domain = self::PLUGIN_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Check if custom 404 should be used.
	 *
	 * @since 1.0.0
	 * @author Bill Erickson
	 */
	public function maybe_custom_404() {
		if( !is_404() )
			return;
		
		// Replace content
		if ( genesis_get_option( 'content', 'genesis-404' ) ) {
			remove_action( 'genesis_loop', 'genesis_404' );
			add_action( 'genesis_loop', array( $this, 'loop' ) );
		}
		
		// Set layout
		if ( genesis_get_option( 'genesis_layout', 'genesis-404' ) ) {
			add_filter( 'genesis_pre_get_option_site_layout', array( $this, 'custom_layout' ) );
		}
	}

	/**
	 * Genesis 404 Loop.
	 *
	 * Defers to markup-type specific methods.
	 *
	 * @since 1.0.0
	 * @author Bill Erickson
	 */
	public function loop() {
		$title   = genesis_get_option( 'title', 'genesis-404' );
		$content = genesis_get_option( 'content', 'genesis-404' );

		if ( function_exists( 'genesis_html5' ) && genesis_html5() ) {
			$this->loop_xhtml( $title, $content );
		} else {
			$this->loop_html5( $title, $content );
		}
	}

	/**
	 * Echo 404 loop markup as XHTML.
	 *
	 * @since 1.3.0
	 *
	 * @param  string $title   Page title.
	 * @param  string $content Page content.
	 */
	protected function loop_xhtml( $title, $content ) {
		echo '<article class="page type-page status-publish entry" itemscope="" itemtype="http://schema.org/CreativeWork">';

		if ( ! empty( $title ) ) {
			echo '<header class="entry-header"><h1 class="entry-title" itemprop="headline">' . esc_attr( $title ) . '</h1></header>';
		}

		do_action( 'genesis_404_before_content' );

		if ( ! empty( $content ) ) {
			echo '<div class="entry-content" itemprop="text">' . apply_filters( 'the_content', $content ) . '</div>';
		}

		do_action( 'genesis_404_after_content' );

		echo '</article>';
	}

	/**
	 * Echo 404 loop markup as HTML5.
	 *
	 * @since 1.3.0
	 *
	 * @param  string $title   Page title.
	 * @param  string $content Page content.
	 */
	protected function loop_html5( $title, $content ) {
		echo '<div class="post hentry">';

		if ( ! empty( $title ) ) {
			echo '<h1 class="entry-title">' . esc_attr( $title ) . '</h1>';
		}

		do_action( 'genesis_404_before_content' );

		if ( ! empty( $content ) ) {
			echo '<div class="entry-content">' . apply_filters( 'the_content', $content ) . '</div>';
		}

		do_action( 'genesis_404_after_content' );

		echo '</div>';
	}
	
	/**
	 * Set the custom genesis layout.
	 *
	 * @since 1.5.0
	 * @author Joshua David Nelson, josh@joshuadnelson.com
	 *
	 * @param string $layout The current layout.
	 *
	 * @return string $layout The modified layout.
	 */
	public function custom_layout( $layout ) {
		$layout_404 = genesis_get_option( 'genesis_layout', 'genesis-404' );
		
		// Return the original layout if 'default' is selected
		if ( $layout_404 == 'default' )
			return $layout;
		
		// Return the custom layout
		return $layout_404;
		
	}

	/**
	 * Search shortcode.
	 *
	 * @since 1.1.0
	 * @author Bill Erickson
	 */
	public function search_shortcode() {
		return '<div class="genesis-404-search">' . get_search_form( false ) . '</div>';
	}

}
