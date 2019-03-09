<?php

namespace SimplePay\Pro\Payments;

use SimplePay\Core\Payments\Stripe_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Invoice
 *
 * @package SimplePay\Payments
 *
 * Wrapper of Stripe API Invoice class. Used to handle anything we need from there.
 */
class Invoice {

	// A variable to hold our Payment object details
	public $payment = null;

	/**
	 * Invoice constructor.
	 *
	 * @param Payment $payment The Payment object to associate with this Invoice object.
	 */
	public function __construct( Payment $payment ) {

		// Set our payment variable to the Payment object passed in.
		$this->payment = $payment;
	}

	/**
	 * @return mixed The Stripe Invoice object for the charge this is related to
	 */
	public function get_invoice() {

		if ( ! isset( $this->payment->charge ) ) {
			return;
		}

		return Stripe_API::request( 'Invoice', 'retrieve', $this->payment->charge->invoice );
	}

	/**
	 * Get the upcoming invoice data.
	 *
	 * @param $args - Any valid arguments that can be passed to the Stripe API
	 *
	 * @return mixed Stripe Upcoming Invoice object
	 */
	public function get_upcoming( $args ) {

		if ( ! empty( $args ) && is_array( $args ) ) {
			return Stripe_API::Request( 'Invoice', 'upcoming', $args );
		}

		return false;
	}


	/**
	 * Create an invoice item (like a setup fee)
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function create_invoice_item( $args ) {
		return Stripe_API::request( 'InvoiceItem', 'create', $args );
	}
}
