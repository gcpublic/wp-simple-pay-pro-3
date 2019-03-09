<?php

use SimplePay\Pro\Payments\Plan;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check the user's license to see if subscriptions are enabled or not
 *
 * @return bool
 */
function simpay_subscriptions_enabled() {

	$license_data = get_option( 'simpay_license_data' );

	if ( ! empty( $license_data ) && 'valid' === $license_data->license ) {
		$price_id = $license_data->price_id;

		if ( '1' !== $price_id ) {
			return true;
		}
	}

	return false;
}

/**
 * Get a list of all the Stripe plans
 */
function simpay_get_plan_list() {

	// Make sure the API keys exist before we try to load the plan list
	if ( simpay_check_keys_exist() ) {

		$plans = Plan::get_plans();

		if ( ! empty( $plans ) && is_array( $plans ) ) {

			$options = array();

			foreach ( $plans as $k => $v ) {

				$nickname       = $v['nickname']; // New pricing plan name (as of Stripe API 2018-02-05)
				$legacy_name    = $v['name']; // Legacy plan name attribute (before Stripe API 2018-02-05)
				$id             = $v['id'];
				$currency       = $v['currency'];
				$amount         = $v['amount'];
				$interval       = $v['interval'];
				$interval_count = $v['interval_count'];
				$decimals       = 0;

				// Display "PlanName - $##/month". Omit product name & plan ID.
				// If no plan name (nickname attr), try (legacy) name attr, then finally plan id attr.

				// TODO Display "ProductName/PlanName - $##/month". Omit plan ID. ...at some point?
				// Would need to access Products in Stripe API.
				
				$plan_name = $nickname;

				if ( empty( $plan_name ) ) {
					if ( ! empty( $legacy_name ) ) {
						$plan_name = $legacy_name;
					} else {
						$plan_name = $id;
					}
				}

				if ( ! simpay_is_zero_decimal( $currency ) ) {
					$amount   = $amount / 100;
					$decimals = 2;
				}

				// Put currency symbol + amount in one string to make it easier
				$amount = simpay_get_currency_symbol( $currency ) . number_format( $amount, $decimals );

				$options[ $id ] = $plan_name . ' - ' . sprintf( _n( '%1$s/%3$s', '%1$s every %2$d %3$ss', $interval_count, 'simple-pay' ), $amount, $interval_count, $interval );
			}

			asort( $options );

			return $options;
		}
	}

	return array();
}

/**
 * Get the description for webhooks
 */
function simpay_webhook_help_text() {

	$html = '<p class="description">' . esc_html__( 'In order for Max Charges to function correctly, you must set up a Stripe webhook endpoint.', 'simple-pay' ) . '<br>';
	$html .= '<strong>' . sprintf( esc_html__( 'Your webhook URL: %s', 'simple-pay' ), simpay_get_webhook_url() ) . '</strong><br>';
	$html .= '<a href="' . simpay_docs_link( '', 'installment-plans', '', true ) . '" target="_blank">' . esc_html__( 'See our documentation for a step-by-step guide.', 'simple-pay' ) . '</a></p>';

	return $html;
}

/**
 * Return the webhook URL specific for this user's site
 */
function simpay_get_webhook_url() {
	return trailingslashit( get_bloginfo( 'url' ) ) . '?simple-pay-listener=stripe';
}

/**
 * Get the description for Form Field Label
 */
function simpay_form_field_label_description() {
	return esc_html__( 'Label displayed above this field on the payment form. Leave blank if using only placeholders.', 'simple-pay' );
}

/**
 * Get the description for Placeholder
 */
function simpay_placeholder_description() {
	return esc_html__( 'i.e. inline label', 'simple-pay' );
}

/**
 * Get the description for Stripe Metadata Label
 */
function simpay_metadata_label_description() {
	return esc_html__( 'Used to identify this field within Stripe payment records. Not displayed on the payment form.', 'simple-pay' );
}

/**
 * My Account/License upgrade URL with GA campaign values.
 *
 * @param string $ga_content
 *
 * @return string
 */
function simpay_my_account_url( $ga_content ) {

	return simpay_ga_url( simpay_get_url( 'my-account' ), $ga_content, true );
}
