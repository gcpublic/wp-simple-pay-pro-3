<?php

namespace SimplePay\Pro\Admin;

use SimplePay\Pro\Admin\Metaboxes\Custom_fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin ajax.
 *
 * @since 3.0.0
 */
class Ajax {

	/**
	 * Set up ajax hooks.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {


		add_action( 'wp_ajax_simpay_add_field', array( __CLASS__, 'add_field' ) );

		add_action( 'wp_ajax_simpay_add_plan', array( __CLASS__, 'add_plan' ) );
	}

	/**
	 * Add a new metabox for custom fields settings
	 */
	public static function add_field() {

		// Check the nonce first
		check_ajax_referer( 'simpay_custom_fields_nonce', 'addFieldNonce' );

		ob_start();

		$type = isset( $_POST['fieldType'] ) ? sanitize_key( strtolower( $_POST['fieldType'] ) ) : '';

		$counter = isset( $_POST['counter'] ) ? intval( $_POST['counter'] ) : 0;
		$uid     = $counter;

		// Load new metabox depending on what type was selected
		if ( ! empty( $type ) ) {

			try {
				Custom_Fields::print_custom_field( $type, '', $counter, $uid );
			} catch ( \Exception $e ) {
				wp_send_json_error( array( 'success' => false, 'message' => $e ) );
			}
		} else {
			wp_send_json_error( array( 'success' => false ) );
		}

		ob_end_flush();

		die();
	}

	/**
	 * Add new plan for Subscription multi-plans section
	 */
	public function add_plan() {

		check_ajax_referer( 'simpay_add_plan_nonce', 'addPlanNonce' );

		ob_start();

		// Plan counter & order are used in tab-multi-subs file that's included.
		$plan_counter = isset( $_POST['counter'] ) ? intval( $_POST['counter'] ) : 0;
		$plan_order = isset( $_POST['counter'] ) ? intval( $_POST['counter'] ) : 0;

		include( 'metaboxes/views/tabs/tab-multi-subs.php' );

		ob_end_flush();

		die();

	}

}
