<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Total_Amount extends Custom_Field {

	// TODO Need these functions & properties? Or can we just pull from form settings.
	// Same question for checkout-button form field.

	public static $total_amount = 0;
	public static $recurring_total = 0;
	public static $tax_amount = 0;

	/**
	 * Total_Amount constructor.
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

		global $simpay_form;

		$html = '';

		// Tax amount label (render above total amount)
		if ( isset( $settings['tax_amount'] ) && 'yes' === $settings['tax_amount'] && $simpay_form->tax_percent > 0 ) {
			$html .= self::print_tax_amount_label( $settings );
		}

		// Total amount label
		$html .= self::print_total_amount_label( $settings );

		// Recurring amount label
		if ( isset( $settings['recurring_total'] ) && 'yes' === $settings['recurring_total'] && $simpay_form->is_subscription() ) {
			$html .= self::print_recurring_total_label( $settings );
		}

		return $html;
	}

	/**
	 * HTML for the total amount label
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_total_amount_label( $settings ) {

		$label = isset( $settings['label'] ) && ! empty( $settings['label'] ) ? $settings['label'] : esc_html__( 'Total Amount:', 'simple-pay' );

		$html = '<div class="simpay-form-control simpay-total-amount-container">';
		$html .= '<div class="simpay-total-amount-label simpay-label-wrap">';
		$html .= $label . ' <span class="simpay-total-amount-value">' . simpay_format_currency( self::$total_amount, simpay_get_setting( 'currency' ) ) . '</span>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * HTML for the recurring total label
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_recurring_total_label( $settings ) {

		global $simpay_form;

		$label = isset( $settings['recurring_total_label'] ) && ! empty( $settings['recurring_total_label'] ) ? $settings['recurring_total_label'] : esc_html__( 'Recurring Total:', 'simple-pay' );

		// TODO i18n "every" (multiple places).

		if ( $simpay_form->has_subscription_custom_amount && ( $simpay_form->subscription_interval > 1 ) ) {
			$amount_text = '<span class="simpay-total-amount-recurring-value">' . simpay_format_currency( self::$recurring_total, simpay_get_setting( 'currency' ) ) . ' every ' . $simpay_form->subscription_interval . ' ' . $simpay_form->subscription_frequency . 's</span>';
		} else {
			$amount_text = '<span class="simpay-total-amount-recurring-value">' . simpay_format_currency( self::$recurring_total, simpay_get_setting( 'currency' ) ) . '/' . $simpay_form->subscription_frequency . '</span>';
		}

		$html = '<div class="simpay-form-control simpay-total-amount-recurring-container">';
		$html .= '<div class="simpay-total-amount-recurring-label simpay-label-wrap">';
		$html .= $label . ' ' . $amount_text;
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * HTML for the tax amount label
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_tax_amount_label( $settings ) {

		$label = isset( $settings['tax_amount_label'] ) && ! empty( $settings['tax_amount_label'] ) ? $settings['tax_amount_label'] : esc_html__( 'Tax Amount:', 'simple-pay' );

		$html = '<div class="simpay-form-control simpay-tax-amount-container">';
		$html .= '<div class="simpay-total-amount-tax-label simpay-label-wrap">';
		$html .= $label . ' <span class="simpay-tax-amount-value">' . simpay_format_currency( self::$tax_amount ) . '</span>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	// TODO Need these functions & properties? Or can we just pull from form settings.
	// Same question for checkout-button form field.

	public static function set_total( $amount ) {
		self::$total_amount = $amount;
	}

	public static function set_recurring_total( $amount ) {
		self::$recurring_total = $amount;
	}

	public static function set_tax_amount( $amount ) {
		self::$tax_amount = $amount;
	}
}
