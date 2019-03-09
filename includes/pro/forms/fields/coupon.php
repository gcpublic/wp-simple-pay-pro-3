<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Coupon extends Custom_Field {


	/**
	 * Coupon constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Print HTML for text field on frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$html = '';

		$id          = isset( $settings['id'] ) ? $settings['id'] : '';
		$label       = isset( $settings['label'] ) && ! empty( $settings['label'] ) ? $settings['label'] : '';
		$placeholder = isset( $settings['placeholder'] ) && ! empty( $settings['placeholder'] ) ? $settings['placeholder'] : '';

		// Get form ID from field ID.
		$form_id = null;
		if ( $id ) {
			$form_id_list = explode('_', $id);
			$form_id = $form_id_list[1];
		}

		$form_display_type = get_post_meta( $form_id, '_form_display_type', true );
		$loading_image     = SIMPLE_PAY_ASSETS . 'images/loading.gif';

		$field = '<input type="text" name="simpay_field[coupon]" class="simpay-coupon-field" placeholder="' . esc_attr( $placeholder ) . '" />';

		// Add Stripe's "blue" class if global setting set AND using Stripe Checkout form display type.
		$use_stripe_class  = ( 'stripe_checkout' === $form_display_type ) && ( 'stripe' === simpay_get_global_setting( 'apply_button_style' ) );

		$button = '<button class="simpay-apply-coupon ' . ( $use_stripe_class ? 'stripe-button-el' : '' ) . '"><span>' . esc_html__( 'Apply', 'simple-pay' ) . '</span></button>';

		$html .= '<div class="simpay-form-control simpay-coupon-container">';

		// Label
		$html .= '<div class="simpay-coupon-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';

		// Coupon field and apply button
		$html .= '<div class="simpay-coupon-wrap simpay-field-wrap">';
		$html .= $field . $button;
		$html .= '</div>';

		// Coupon message to show AJAX response message when a coupon is entered
		$html .= '<div>';
		$html .= '<span class="simpay-coupon-loading" style="display: none;"><img src="' . esc_attr( $loading_image ) . '" /></span>'; // Loading image
		$html .= '<span class="simpay-coupon-message"></span>'; // Message container
		$html .= '<span class="simpay-remove-coupon" style="display: none;"> (<a href="#">' . esc_html__( 'remove', 'simple-pay' ) . '</a>)</span>';
		$html .= '<input type="hidden" name="simpay_coupon" class="simpay-coupon" />';
		$html .= '</div>';

		$html .= wp_nonce_field( 'simpay_coupon_nonce', 'simpay_coupon_nonce', true, false );

		$html .= '</div>';

		return $html;
	}
}
