<?php

namespace SimplePay\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin_Updater {

	/**
	 * Plugin_Updater constructor.
	 */
	public function __construct() {

		// Load custom updater file.
		require_once 'edd-sl-plugin-updater.php';

		// Load plugin updater on admin_init with top priority.
		add_action( 'admin_init', array( $this, 'load_updater' ), 0 );

		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_action( 'admin_init', array( $this, 'check_license_still_valid' ) );
	}

	/**
	 * Load the EDD SL Updater class if a key is saved
	 */
	public function load_updater() {

		// retrieve our license key from the DB
		$key = trim( get_option( 'simpay_license_key' ) );
		$settings = get_option( 'simpay_settings_general' );

		if ( ! empty( $key ) ) {
			new \Simple_Pay_EDD_SL_Plugin_Updater( SIMPLE_PAY_STORE_URL, SIMPLE_PAY_MAIN_FILE, array(
				'version' => SIMPLE_PAY_VERSION,        // current version number
				'license' => $key,                      // license key (used get_option above to retrieve from DB)
				'item_id' => SIMPLE_PAY_ITEM_ID,        // Download ID of this plugin (using instead of Download Name)
				'author'  => 'Moonstone Media',          // author of this plugin,
				'beta'    => ! empty( $settings['general_misc']['beta_opt_in'] )
			) );
		}
	}

	/**
	 * Activate a license key.
	 */
	public static function activate_license() {

		// Don't proceed if activate button not clicked or license key is blank.
		if ( ! isset( $_POST['simpay_activate_license'] ) ) {
			return;
		}

		if ( ! isset( $_POST['simpay_settings_license']['key']['license_key'] ) ) {
			return;
		}

		// Retrieve license key from form field.
		$key = sanitize_key( trim( $_POST['simpay_settings_license']['key']['license_key'] ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $key,
			'item_id'    => SIMPLE_PAY_ITEM_ID,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( SIMPLE_PAY_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return;
		}

		// No error, so let's proceed.

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Update saved license key & data.
		update_option( 'simpay_license_key', $key );
		update_option( 'simpay_license_data', $license_data );
	}

	/**
	 * Deactivate a license key.
	 */
	public static function deactivate_license() {

		// Don't proceed if deactivate button not clicked or license key is blank.
		if ( ! isset( $_POST['simpay_deactivate_license'] ) ) {
			return;
		}

		if ( ! isset( $_POST['simpay_settings_license']['key']['license_key'] ) ) {
			return;
		}

		// Retrieve license key from form field.
		$key = sanitize_key( trim( $_POST['simpay_settings_license']['key']['license_key'] ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $key,
			'item_id'    => SIMPLE_PAY_ITEM_ID,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( SIMPLE_PAY_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return;
		}

		// No error, so let's proceed.

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( $license_data->license == 'deactivated' ) {

			// Remove saved license data, key & next check options.
			delete_option( 'simpay_license_data' );
			delete_option( 'simpay_license_key' );
			delete_option( 'simpay_license_next_check' );
		}
	}

	/**
	 * A function to check if a current license is still valid. Run check once every 24 hours, or 2 hours if an error
	 * is encountered.
	 */
	public function check_license_still_valid() {

		$simpay_license_next_check = get_option( 'simpay_license_next_check', false );

		if ( is_numeric( $simpay_license_next_check ) && ( $simpay_license_next_check > current_time( 'timestamp' ) ) ) {
			return;
		}

		$key = trim( get_option( 'simpay_license_key' ) );

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $key,
			'item_id'    => SIMPLE_PAY_ITEM_ID,
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = wp_remote_post( SIMPLE_PAY_STORE_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		) );

		if ( is_wp_error( $response ) ) {

			update_option( 'simpay_license_next_check', current_time( 'timestamp' ) + ( HOUR_IN_SECONDS * 2 ) );

			return;
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// Update saved license data & timestamp.
		update_option( 'simpay_license_data', $license_data );
		update_option( 'simpay_license_next_check', current_time( 'timestamp' ) + DAY_IN_SECONDS );
	}
}
