<?php

namespace SimplePay\Pro\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pages {

	public $values = array();

	public function __construct() {
		add_filter( 'simpay_add_settings_display_fields', array( $this, 'subscription_confirmation_messages' ) );

		add_filter( 'pre_update_option_simpay_settings_keys', array( $this, 'set_admin_notice_options' ), 10, 2 );
	}

	/**
	 * Set the database options for aadmin notices
	 *
	 * @param $new_value
	 * @param $old_value
	 *
	 * @return mixed
	 */
	function set_admin_notice_options( $new_value, $old_value ) {

		// Check test mode
		if ( isset( $new_value['mode']['test_mode'] ) && $new_value['mode']['test_mode'] !== $old_value['mode']['test_mode'] && ! empty( $new_value['mode']['test_mode'] ) ) {
			update_option( 'simpay_test_mode_changed', $new_value['mode']['test_mode'] );
		}

		// Check live keys
		if ( isset( $new_value['live_keys']['secret_key'] ) && $new_value['live_keys']['secret_key'] !== $old_value['live_keys']['secret_key'] || $new_value['live_keys']['publishable_key'] !== $old_value['live_keys']['publishable_key'] ) {

			update_option( 'simpay_live_keys_changed', true );
		}

		// Check test keys
		if ( isset( $new_value['test_keys']['secret_key'] ) && $new_value['test_keys']['secret_key'] !== $old_value['test_keys']['secret_key'] || $new_value['test_keys']['publishable_key'] !== $old_value['test_keys']['publishable_key'] ) {

			update_option( 'simpay_test_keys_changed', true );
		}

		return $new_value;
	}

	public function subscription_confirmation_messages( $fields ) {

		$section      = 'payment_confirmation_messages';
		$option_group = 'settings';
		$id           = 'display';

		$this->values = get_option( 'simpay_' . $option_group . '_' . $id );

		// Default template for subscriptions details
		$subscription_details_template = simpay_get_editor_default( 'subscription' );
		$subscription_details_value    = $this->get_option_value( $section, 'subscription_details' );

		$trial_details_template = simpay_get_editor_default( 'trial' );
		$trial_details_value    = $this->get_option_value( $section, 'trial_details' );

		// Add subscription payment & free trial sign up editor fields.
		if ( simpay_subscriptions_enabled() ) {

			$fields[ $section ] = array_merge( $fields[ $section ], array(
				'subscription_details' => array(
					'title'       => esc_html__( 'Subscription without Trial', 'simple-pay' ),
					'type'        => 'editor',
					'name'        => 'simpay_' . $option_group . '_' . $id . '[' . $section . '][subscription_details]',
					'id'          => 'simpay-' . $option_group . '-' . $id . '-' . $section . '-subscription-details',
					'value'       => isset( $subscription_details_value ) && ! empty( $subscription_details_value ) ? $subscription_details_value : $subscription_details_template,
					'escaping'    => array( $this, 'escape_editor' ),
					'description' => $this->subscription_details_description(),
				),
				'trial_details'        => array(
					'title'       => esc_html__( 'Subscription with Free Trial', 'simple-pay' ),
					'type'        => 'editor',
					'name'        => 'simpay_' . $option_group . '_' . $id . '[' . $section . '][trial_details]',
					'id'          => 'simpay-' . $option_group . '-' . $id . '-' . $section . '-trial-details',
					'value'       => isset( $trial_details_value ) && ! empty( $trial_details_value ) ? $trial_details_value : $trial_details_template,
					'escaping'    => array( $this, 'escape_editor' ),
					'description' => $this->trial_details_description(),
				),
			) );
		}

		return $fields;
	}


	// TODO: This is in the core files too. Need to find a way to call the parent so we don't have a duplicate here
	public function escape_editor( $value ) {
		return wp_kses_post( $value );
	}

	/**
	 * Default Subscription details template
	 *
	 * @return string'
	 */
	public function subscription_details_description() {

		$html = '<div class="simpay-payment-details-description">';
		$html .= '<p class="description">' . esc_html__( 'Enter what your customers will see after a successful subscription plan payment.', 'simple-pay' ) . '</p>';
		$html .= '<p><strong>' . esc_html__( 'Available template tags:', 'simple-pay' ) . '</strong></p>';
		$html .= '<p><code>{recurring-amount}</code> - ' . esc_html__( 'The recurring amount to be charged each period of the subscription plan.', 'simple-pay' ) . '</p>';
		$html .= '<p><code>{max-charges}</code> - ' . esc_html__( 'The total number of max charges set for an installment plan.', 'simple-pay' ) . '</p>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Default Trial subscription details template
	 *
	 * @return string
	 */
	public function trial_details_description() {

		$html = '<div class="simpay-payment-details-description">';
		$html .= '<p class="description">' . esc_html__( 'Enter what your customers will see after a successful subscription trial sign up.', 'simple-pay' ) . '</p>';
		$html .= '<p><strong>' . esc_html__( 'Available template tags:', 'simple-pay' ) . '</strong></p>';
		$html .= '<p><code>{trial-end-date}</code> - ' . esc_html__( "The day the plan's free trial ends.", 'simple-pay' ) . '</p>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get option value.
	 *
	 * @since  3.0.0
	 * @access protected
	 *
	 * @param  string $section
	 * @param  string $setting
	 *
	 * @return string
	 */
	// TODO PRO: THis is in both lite and pro. find a way to avoid this.
	protected function get_option_value( $section, $setting ) {

		$option = $this->values;

		if ( ! empty( $option ) && is_array( $option ) ) {
			return isset( $option[ $section ][ $setting ] ) ? $option[ $section ][ $setting ] : '';
		}

		return '';
	}
}
