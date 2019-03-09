<?php

namespace SimplePay\Pro\Payments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use SimplePay\Pro\Payments as Pro_Payments;

/**
 * Pro Payment Details Class
 *
 * Handles all of the payment details output after a transaction.
 */
class Details {

	public $payment = null;

	public function __construct() {

		add_filter( 'simpay_payment_details_template_tags', array( $this, 'add_pro_tags' ), 10, 2 );
		add_filter( 'simpay_details_order_date', array( $this, 'trial_order_date' ) );
		add_filter( 'simpay_process_template_tag_type', array( $this, 'process_template_tag_type' ) );
		add_filter( 'simpay_get_editor_content', array( $this, 'simpay_get_editor_content' ), 10, 3 );
	}

	public function simpay_get_editor_content( $html, $type, $display_options ) {

		switch ( $type ) {
			case 'subscription':
				return isset( $display_options['payment_confirmation_messages']['subscription_details'] ) ? $display_options['payment_confirmation_messages']['subscription_details'] : simpay_get_editor_default( 'subscription' );
			case 'trial':
				return isset( $display_options['payment_confirmation_messages']['trial_details'] ) ? $display_options['payment_confirmation_messages']['trial_details'] : simpay_get_editor_default( 'trial' );
			default:
				return '';
		}
	}

	public function process_template_tag_type( $type ) {

		global $simpay_form;

		if ( $simpay_form->is_subscription() ) {

			if ( $simpay_form->is_trial ) {
				$type = 'trial';
			} else {
				$type = 'subscription';
			}
		}

		return $type;
	}

	public function trial_order_date( $charge_date ) {

		global $simpay_form;

		if ( $simpay_form->is_trial ) {

			return isset( $this->payment->subscription->created ) ? $this->payment->subscription->created : '';
		} else {

			return $charge_date;
		}
	}

	/**
	 * Setup all the tags available in the Payment Confirmation editor for the Pro version.
	 * Convert all amounts from cents to dollars (100 = $1.00 USD) before displaying.
	 * Values will be coming from Stripe in cents (non-decimal) format.
	 */
	public function add_pro_tags( $tags, $payment ) {

		global $simpay_form;

		$this->payment = $payment;

		$tags = array_merge( $tags, array(
			'recurring-amount' => array(
				'type'  => array( 'subscription', 'trial' ),
				'value' => ( $simpay_form->is_subscription() ? $this->get_recurring_amount() : '' ),
			),

			'trial-end-date' => array(
				'type'  => array( 'trial' ),
				'value' => $simpay_form->is_trial ? $this->get_trial_end_date() : '',
			),

			'max-charges' => array(
				'type'  => array( 'subscription', 'trial' ),
				'value' => ( isset( $simpay_form->subscription_max_charges ) && $simpay_form->subscription_max_charges > 0 ) ? $simpay_form->subscription_max_charges : '',
			),

			'tax-amount' => array(
				'type'  => array( 'all' ),
				'value' => isset( $simpay_form->tax_amount ) ? simpay_format_currency( simpay_convert_amount_to_dollars( $simpay_form->tax_amount ), $this->payment->get_currency() ) : '',
			),

			'total-amount' => array(
				'type'  => array( 'all' ),
				'value' => isset( $this->payment->charge->amount ) ? simpay_format_currency( simpay_convert_amount_to_dollars( $this->payment->charge->amount ), $this->payment->get_currency() ) : '',
			),
		) );

		return $tags;
	}

	/**
	 * Process the details needed for a subscription charge.
	 *
	 * @return string HTML output for subscription details
	 */
	public function get_recurring_amount() {

		// Create an Invoice object so we can get all the details we need and have it available to save changes to
		$invoice = new Pro_Payments\Invoice( $this->payment );

		// Get all the information we need from the Invoice and Subscription
		$customer_id    = $this->payment->customer->id;
		$subscription   = $this->payment->subscription;
		$interval_count = $subscription->plan->interval_count;
		$interval       = $subscription->plan->interval;

		// Get the upcoming Invoice from Stripe so we can get the correct recurring total if a coupon was entered
		$upcoming_invoice = $invoice->get_upcoming( array(
			'customer'     => $customer_id,
			'subscription' => $subscription->id,
		) );

		// Get the recurring total and the setup fee
		$recurring_amount = $upcoming_invoice->total;

		// Use future recurring charge amount.
		$formatted_amount = simpay_format_currency( simpay_convert_amount_to_dollars( $recurring_amount ), $this->payment->get_currency() );

		// Some crazy i18n to check if it is singular or plural and output accordingly all while being able to translate every part of the string.
		return sprintf( _n( '%1$s/%3$s', '%1$s every %2$d %3$ss', $interval_count, 'simple-pay' ), $formatted_amount, $interval_count, $interval );
	}

	/**
	 * Get the trial end date string
	 *
	 * @return string
	 */
	public function get_trial_end_date() {

		global $simpay_form;

		if ( $simpay_form->is_subscription() ) {
			$sub_id       = $this->payment->subscription->id;
			$subscription = $this->payment->customer->subscriptions->retrieve( $sub_id );

			return date_i18n( get_option( 'date_format' ), $subscription->trial_end );
		}

		return '';
	}
}
