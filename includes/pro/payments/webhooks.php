<?php

namespace SimplePay\Pro\Payments;

use SimplePay\Core\Payments\Stripe_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Webhooks {

	private $id;

	public function __construct() {

		$this->id = 'stripe';

		add_action( 'init', array( $this, 'simple_pay_event_listener' ) );

	}

	/**
	 * Installment plans feature.
	 * If SP is active the Stripe PHP library should not have to be loaded.
	 * https://stripe.com/docs/recipes/installment-plan
	 * https://stripe.com/docs/webhooks
	 */

	public function simple_pay_event_listener() {

		if ( isset( $_GET['simple-pay-listener'] ) && $_GET['simple-pay-listener'] == 'stripe' ) {

			// Retrieve the request's body and parse it as JSON
			$body = @file_get_contents( 'php://input' );
			$event_json = json_decode( $body );

			if ( isset( $event_json->id ) ) {

				// Return HTTPS 200 OK status.
				// Stripe PHP examples use http_response_code( 200 ) -- supported by PHP 5.4 or greater.
				// EDD & Woo Stripe use status_header( 200 ) -- a core WP function.
				status_header( 200 );

				//error_log( 'Simple Pay - Event Found.  ' );

				// this will be used to retrieve the event from Stripe
				$event_id = $event_json->id;

				try {

					// to verify this is a real event, we re-retrieve the event from Stripe
					$event = Stripe_API::request( 'Event', 'retrieve', $event_id );

					do_action( 'simpay_webhook_event', $event );

					// Successful invoice paid against a subscription.
					if ( isset( $event ) && $event->type == 'invoice.payment_succeeded' ) {

						//error_log( 'Simple Pay - Invoice Payment Succeeded.  ' );

						// Get the invoice object and subscription
						// TODO Ref subscription in first line item of invoice instead? Don't think it matters though.
						// See https://stripe.com/docs/recipes/installment-plan

						$invoice      = $event->data->object;
						$subscription = Stripe_API::request( 'Subscription', 'retrieve', $invoice->subscription );

						do_action( 'simpay_webhook_invoice_payment_succeeded', $event, $invoice, $subscription );

						if ( isset( $subscription ) && isset( $subscription->metadata['simpay_charge_max'] ) ) {

							//error_log( 'Simple Pay - Has an installment plan.  ' );

							$max_charges  = $subscription->metadata['simpay_charge_max'];
							$charge_count = $subscription->metadata['simpay_charge_count'];

							$charge_count++;

							do_action( 'simpay_webhook_after_installment_increase' );

							//error_log( 'Simple Pay - Max charges: ' . $max_charges . '  ' );
							//error_log( 'Simple Pay - New charge count: ' . $charge_count . '  ' );

							// Update the total count metadata
							$subscription->metadata['simpay_charge_count'] = absint( $charge_count );
							$subscription->save();

							if ( $charge_count >= $max_charges ) {

								//error_log( 'Simple Pay - Max charges hit, cancelling subscription.  ' );

								// Cancel subscription
								$subscription->cancel();

								//error_log( 'Simple Pay - Subscription cancellation successful.  ' );

								do_action( 'simpay_webhook_after_subscription_cancel' );
							}
						}
					}

					// Thought this was the better spot to return HTTPS 200 OK status.
					// Stripe PHP examples use http_response_code( 200 ) -- supported by PHP 5.4 or greater.
					// EDD & Woo Stripe use status_header( 200 ) -- a core WP function.
					// status_header( 200 );

					die( '1' ); // Completed successfully

				} catch ( \Exception $e ) {

					// Set error message and HTTPS 500 error status.
					return new \WP_Error( 'invalid_request', 'Invalid webhook data. Webhook could not be processed.', array( 'status_header' => 500 ) );
				}
			}
		}
	}
}
