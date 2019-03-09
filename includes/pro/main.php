<?php

namespace SimplePay\Pro;

use SimplePay\Pro\Admin;
use SimplePay\Pro\Admin\Metaboxes\Settings;
use SimplePay\Pro\Forms\Ajax;
use SimplePay\Pro\Payments\Webhooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro SimplePay Class
 */
final class SimplePayPro {

	/**
	 * The single instance of this class
	 */
	protected static $_instance = null;

	/**
	 * Main Simple Pay instance
	 *
	 * Ensures only one instance of Simple Pay is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'simple-pay' ), '3.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'simple-pay' ), '3.0' );
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->load();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Filter common URLs so they run through core's get_url function as well.
		add_filter( 'simpay_get_url', array( $this, 'get_url' ), 10, 2 );
	}

	/**
	 * Load the plugin.
	 */
	public function load() {

		// Load pro shared back-end & front-end functions.
		require_once( SIMPLE_PAY_INC . 'pro/functions/shared.php' );
		require_once( SIMPLE_PAY_INC . 'pro/functions/countries.php' );

		// Load Lite helper class to update various differences between Lite and Pro.
		new Lite_Helper();
		new Webhooks();
		new Objects();
		new Assets();
		new Shortcodes();
		new Payments\Details();

		// Load frontend ajax
		new Ajax();

		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->load_admin();
		}
	}

	/**
	 * Load the plugin admin.
	 */
	public function load_admin() {

		// Load pro back-end only functions.
		require_once( SIMPLE_PAY_INC . 'pro/functions/admin.php' );

		new Plugin_Updater();
		new Admin\Menus();
		new Settings();
		new Admin\Pages();

		add_action( 'simpay_admin_notices', function ( $is_admin_screen ) {
			new Admin\Notices( $is_admin_screen );
		} );

		new Admin\Assets();

		// Admin ajax callbacks
		new Admin\Ajax();
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'simple-pay', false, plugin_basename( dirname( SIMPLE_PAY_MAIN_FILE ) ) . '/languages' );
	}

	/**
	 * Get common URLs (in addition to those in core).
	 */
	public function get_url( $url, $case ) {

		switch ( $case ) {
			case 'my-account':
				$url = 'https://wpsimplepay.com/my-account/';
				break;
		}

		return $url;
	}
}

/**
 * Start WP Simple Pay Pro.
 */
function SimplePayPro() {
	return SimplePayPro::instance();
}

SimplePayPro();
