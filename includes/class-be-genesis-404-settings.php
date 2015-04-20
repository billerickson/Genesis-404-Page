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
 * Admin page class.
 *
 * @package Genesis_404
 * @author  Bill Erickson
 */
class BE_Genesis_404_Settings extends Genesis_Admin_Boxes {

	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Specify a unique page ID.
		$page_id = 'genesis-404';

		// Set it as a child to genesis, and define the menu and page titles
		$menu_ops = array(
			'submenu' => array(
				'parent_slug' => 'genesis',
				'page_title'  => __( 'Genesis 404 Page', 'genesis-404-page' ),
				'menu_title'  => __( '404 Page', 'genesis-404-page' ),
			)
		);

		// Set up page options. These are optional, so only uncomment if you want to change the defaults
		$page_ops = array(
			// 'screen_icon'       => 'options-general',
			// 'save_button_text'  => 'Save Settings',
			// 'reset_button_text' => 'Reset Settings',
			// 'save_notice_text'  => 'Settings saved.',
			// 'reset_notice_text' => 'Settings reset.',
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
	 * Set up sanitization filters.
	 *
	 * See genesis/lib/classes/sanitization.php for all available filters.
	 *
	 * @since 1.0.0
	 */
	public function sanitization_filters() {
		genesis_add_option_filter(
			'no_html',
			$this->settings_field,
			array(
				'title',
				'genesis_layout',
			)
		);

		genesis_add_option_filter(
			'safe_html',
			$this->settings_field,
			array(
				'content',
			)
		);
	}

	/**
	 * Register metaboxes on admin page.
	 *
	 * @since 1.0.0
	 */
	public function metaboxes() {

		add_meta_box( 'metabox_404', __( '404 Page', 'genesis-404-page' ), array( $this, 'metabox_404' ), $this->pagehook, 'main', 'high' );
		
		if( apply_filters( 'genesis_404_layout_box', true ) )
			add_meta_box( 'genesis_404_layout_box', __( 'Layout Settings', 'genesis' ), array( $this, 'layout_box' ), $this->pagehook, 'main', 'high' );

	}
	
	/**
	 * The layout metabox for the 404 page.
	 *
	 * @since 1.5.0
	 */
	public function layout_box() {
		$layout = esc_attr( $this->get_field_value( 'genesis_layout' ) );

		?>
		<div class="genesis-layout-selector">
			<p><input type="radio" name="<?php echo $this->get_field_name( 'genesis_layout' ); ?>" class="default-layout" id="default-layout" value="" <?php checked( $layout, '' ); ?> /> <label class="default" for="default-layout"><?php printf( __( 'Default Layout set in <a href="%s">Theme Settings</a>', 'genesis' ), menu_page_url( 'genesis', 0 ) ); ?></label></p>

			<p><?php genesis_layout_selector( array( 'name' => $this->get_field_name( 'genesis_layout' ), 'selected' => $layout, 'type' => 'site' ) ); ?></p>
		</div>

		<br class="clear" />
		<?php
	}
	
	/**
	 * 404 Metabox
	 *
	 * @since 1.0.0
	 */
	public function metabox_404() {
		?>
		<p><?php _e( 'The <i>404 Page</i> is the page shown when the intended page was not found. You can customise it here.', 'genesis-404-page' ); ?></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Page Title:', 'genesis-404-page' ); ?></label><br />
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo esc_attr( $this->get_field_value( 'title' ) ); ?>" size="27" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Page Content:', 'genesis-404-page' ); ?></label><br />
			<?php wp_editor( genesis_get_option( 'content', 'genesis-404' ), 'content', array( 'textarea_name' => $this->get_field_id( 'content' ), ) ); ?>
		</p>
		<?php
	}

	/**
	 * Contextual help content.
	 *
	 * @since 1.3.0
	 */
	public function help() {

		$screen = get_current_screen();

		$help =
			'<p>'  . __( 'To customize the 404 Not Found page in your Genesis child theme, enter a title and content.', 'genesis-404-page' ) . '</p>' .
			'<p>'  . __( 'If you want to display the search form, use the shortcode [genesis-404-search].', 'genesis-404-page' ) . '</p>';

		$screen->add_help_tab( array(
			'id'      => $this->pagehook,
			'title'   => __( 'Genesis 404 Page', 'genesis' ),
			'content' => $help,
		) );

		//* Add help sidebar
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'genesis' ) . '</strong></p>' .
			'<p><a href="http://wordpress.org/support/plugin/genesis-404-page" target="_blank" title="' . esc_attr( __( 'Get Support', 'genesis-404-page' ) ) . '">' . __( 'Get Support', 'genesis-404-page' ) . '</a></p>' .
			'<p><a href="https://github.com/billerickson/Genesis-404-Page" target="_blank" title="' . esc_attr( __( 'Plugin Source and Development', 'genesis-404-page' ) ) . '">' . __( 'Plugin Source and Development', 'genesis-404-page' ) . '</a></p>'
		);

	}
}
