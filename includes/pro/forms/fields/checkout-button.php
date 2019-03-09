<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkout_Button extends Custom_Field {
	
	/**
	 * Checkout_Button constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Print the HTML for the checkout button on the frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$id   = isset( $settings['id'] ) ? $settings['id'] : '';
		$button_text = self::print_button_text( $settings );

		// Get the button style from the global display settings
		$general_options = get_option( 'simpay_settings_general' );
		$button_style    = isset( $general_options['styles']['checkout_button_style'] ) && 'stripe' === $general_options['styles']['checkout_button_style'] ? 'stripe-button-el' : '';

		$id = simpay_dashify( $id );

		$html = '<div class="simpay-form-control simpay-checkout-btn-container">';
		$html .= '<button id="' . esc_attr( $id ) . '" class="simpay-checkout-btn ' . esc_attr( $button_style ) . '" type="submit"><span>' . $button_text . '</span></button>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * HTML for the button text including total amount.
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_button_text( $settings ) {

		// TODO Handle trials -- "Start your free trial" text.

		$raw_amount = simpay_get_form_setting( 'total_amount' );
		$formatted_amount = simpay_format_currency( $raw_amount, simpay_get_setting( 'currency' ) );

		$text = isset( $settings['text'] ) && ! empty( $settings['text'] ) ? $settings['text'] : esc_html__( 'Pay {{amount}}', 'simple-pay' );
		$text = str_replace('{{amount}}', '<span class="simpay-total-amount-value">'. $formatted_amount .'</span>', $text);

		return $text;
	}
}
