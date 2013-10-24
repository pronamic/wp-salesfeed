<?php
/*
Plugin Name: SalesFeed
Plugin URI: http://www.pronamic.eu/wordpress-plugins/salesfeed/
Description: Add a SalesFeed tracking code to your WordPress site. You need a SalesFeed account.

Version: 1.0.0
Requires at least: 3.0

Author: Pronamic
Author URI: http://www.pronamic.eu/

Text Domain: salesfeed
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/wp-salesfeed
*/

class Pronamic_WP_SalesFeed_Plugin {
	/**
	 * Constructs and initializes an SalesFeed plugin
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		add_action( 'wp_footer', array( $this, 'wp_footer' ) );

		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}

	/**
	 * Plugins loaded
	 */
	public function plugins_loaded() {
		load_plugin_textdomain( 'salesfeed', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Admin init
	 */
	public function admin_init() {
		// Settings - General
		add_settings_section(
			'salesfeed_general', // id
			__( 'General', 'salesfeed' ), // title
			'__return_false', // callback
			'salesfeed' // page
		);
		
		add_settings_field(
			'salesfeed_account_id', // id
			__( 'SalesFeed Account ID', 'salesfeed' ), // title
			array( __CLASS__, 'input_text' ), // callback
			'salesfeed', // page
			'salesfeed_general', // section
			array( 'label_for' => 'salesfeed_account_id' ) // args
		);
		
		register_setting( 'salesfeed', 'salesfeed_account_id' );
	}

	/**
	 * Admin menu
	 */
	public function admin_menu() {
		add_options_page(
			__( 'SalesFeed', 'salesfeed' ),
			__( 'SalesFeed', 'salesfeed' ),
			'manage_options',
			'salesfeed',
			array( $this, 'page_settings' )
		);
	}

	/**
	 * Page settings
	 */
	public function page_settings() {
		include plugin_dir_path( __FILE__ ) . '/admin/settings.php';
	}

	/**
	 * Input text
	 */
	public function input_text( $args ) {
		printf(
			'<input name="%s" id="%s" type="%s" value="%s" class="%s" />',
			esc_attr( $args['label_for'] ),
			esc_attr( $args['label_for'] ),
			esc_attr( 'text' ),
			esc_attr( get_option( $args['label_for'] ) ),
			'regular-text'
		);
	}

	/**
	 * Footer
	 */
	public function wp_footer() {
		$id = get_option( 'salesfeed_account_id' );

		if ( ! empty ( $id ) ): ?>
	
			<!-- SalesFeed by Pronamic - http://www.pronamic.eu/ -->
			<script type="text/javascript">
				_scoopi = {aid: '<?php echo esc_js( $id ); ?>'};
				var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//lead-analytics.nl/bootstrap.js';
				var node = document.getElementsByTagName('script')[0]; node.parentNode.insertBefore(s, node);
			</script>

		<?php endif;
	}
}

global $pronamic_salesfeed_plugin;

$pronamic_salesfeed_plugin = new Pronamic_WP_SalesFeed_Plugin();
