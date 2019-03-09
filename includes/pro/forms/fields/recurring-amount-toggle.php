<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Recurring_Amount_Toggle extends Custom_Field {

	/**
	 * Recurring_Amount_Toggle constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Print the HTML for checkboxes on the frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$html = '';

		$id    = isset( $settings['id'] ) ? $settings['id'] : '';
		$label = isset( $settings['label'] ) && ! empty( $settings['label'] ) ? $settings['label'] : esc_html__( 'Make this a recurring amount', 'simple-pay' );
		$name  = 'recurring_amount_toggle';

		$id = simpay_dashify( $id );

		$label = '<label for="' . esc_attr( simpay_dashify( $id ) ) . '">' . esc_html( $label ) . '</label>';
		$field = '<input type="checkbox" name="' . $name . '" id="' . esc_attr( $id ) . '" />';

		$html .= '<div class="simpay-form-control simpay-recurring-amount-toggle-container">';
		$html .= '<div class="simpay-checkbox-wrap simpay-field-wrap">';
		$html .= $field . ' ' . $label;
		$html .= '</div>';

		$html .= '</div>';

		return $html;

	}

}
