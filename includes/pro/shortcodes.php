<?php

namespace SimplePay\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pro Shortcodes Class
 *
 * Register and handle custom shortcodes.
 */
class Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'simpay_payment_receipt_html', array( $this, 'pro_payment_receipt' ) );
		add_filter( 'simpay_session_error', array( $this, 'check_session_errors' ) );
	}

	public function check_session_errors( $bool ) {

		$trial           = \SimplePay\Core\SimplePay()->session->get( 'trial' );
		$subscription_id = \SimplePay\Core\SimplePay()->session->get( 'subscription_id' );

		if ( $subscription_id || $trial ) {
			return false;
		}

		return $bool;
	}

	public function pro_payment_receipt( $payment ) {

		$trial           = \SimplePay\Core\SimplePay()->session->get( 'trial' );
		$subscription_id = \SimplePay\Core\SimplePay()->session->get( 'subscription_id' );

		if ( $subscription_id || $trial ) {

			$payment->set_subscription( $subscription_id );
		}
	}
}
