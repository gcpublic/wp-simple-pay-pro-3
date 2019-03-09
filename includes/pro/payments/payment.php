<?php

namespace SimplePay\Pro\Payments;

use SimplePay\Core\Abstracts\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Payment
 *
 * @package SimplePay\Payments
 *
 * A container class to hold all of the various data that might be tied to one single payment.
 */
class Payment extends \SimplePay\Core\Payments\Payment {

	// Public
	public $invoice = '';
	public $has_quantity = false;
	public $quantity = null;
	public $coupon = '';
	public $subscription = null;
	public $recurring_amount_toggle = false;

	// TODO DRY/Simplify contructor logic between core & pro.

	/**
	 * Payment constructor.
	 *
	 * @param Form|null $form
	 * @param string    $action
	 */
	public function __construct( Form $form, $action = '' ) {

		/**
		 * Don't call the parent constructor. We will handle all of the calls in the order we need.
		 */

		// Charge the right thing
		add_filter( 'simpay_charge', array( $this, 'pro_charge' ) );

		// Run this always so our main Payment class has the right attributes set up that other classes can take advantage of
		$this->pro_set_attributes();

		// If the Form object is not set then we want to skip any processing
		if ( 'charge' === $action ) {

			// Fire action before processing form.
			do_action( 'simpay_pre_process_form' );

			// Process PRO form
			$this->pro_process_form();

			$this->set_payment_session();
			$this->handle_redirect();
		}
	}

	public function pro_charge( $charge ) {

		global $simpay_form;

		if ( $simpay_form->is_subscription() || $this->recurring_amount_toggle ) {
			return new Subscription( $this );
		}

		return $charge;
	}

	/**
	 * Process the form for payment
	 */
	public function pro_process_form() {

		global $simpay_form;

		// Check if a quantity was set
		if ( isset( $_POST['simpay_quantity'] ) ) {

			$quantity = intval( $_POST['simpay_quantity'] );

			if ( $quantity < 1 ) {
				$quantity = 1;
			}

			$this->has_quantity = true;
			$this->quantity     = $quantity;
		}

		$this->recurring_amount_toggle = isset( $_POST['recurring_amount_toggle'] ) && 'on' === $_POST['recurring_amount_toggle'] ? true : false;

		// Check for a tax amount
		if ( $simpay_form->tax_percent > 0 ) {

			if ( isset ( $_POST['simpay_tax_amount'] ) && ! empty( $_POST['simpay_tax_amount'] ) ) {

				$simpay_form->tax_amount = html_entity_decode( simpay_format_currency( absint( $_POST['simpay_tax_amount'] ) ) );

				// Update the metadata so these values can be displayed somewhere
				$this->metadata['simpay_tax_amount']  = $simpay_form->tax_amount;
				$this->metadata['simpay_tax_percent'] = $simpay_form->tax_percent;
			}
		}

		// Call the parent class form processing last because this is where the charge gets checked and created
		$this->process_form();
	}

	/**
	 * Set all of the attributes we need to attach to this Payment.
	 */
	public function pro_set_attributes() {

		// Call the parent class function
		$this->set_attributes();

		// Set coupon if one exists
		if ( isset( $_POST['simpay_field'] ) ) {

			$fields = $_POST['simpay_field'];

			if ( isset( $fields['coupon'] ) ) {
				// A coupon field was found so set it here
				$this->coupon = sanitize_text_field( $fields['coupon'] );
			}
		}
	}

	/**
	 * Set the subscription property of this instance to a specif subscription id
	 *
	 * @param $id
	 */
	public function set_subscription( $id ) {
		$this->subscription = Subscription::get_subscription_by_id( $id );
	}
}
