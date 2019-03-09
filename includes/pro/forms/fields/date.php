<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Date extends Custom_Field {

	/**
	 * Date constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Print the HTMLO for the date fields to frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$html = '';

		$id          = isset( $settings['id'] ) ? $settings['id'] : '';
		$meta_name   = isset( $settings['metadata'] ) && ! empty( $settings['metadata'] ) ? $settings['metadata'] : $id;
		$label       = isset( $settings['label'] ) ? $settings['label'] : '';
		$placeholder = isset( $settings['placeholder'] ) ? $settings['placeholder'] : '';
		$required    = isset( $settings['required'] ) ? 'required=""' : '';
		$default     = isset( $settings['default'] ) ? $settings['default'] : '';
		$name        = 'simpay_field[' . esc_attr( $meta_name ) . ']';

		$id = simpay_dashify( $id );

		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		$field = '<input type="text" class="simpay-date-input" name="' . $name . '" id="' . esc_attr( $id ) . '" placeholder="' . esc_attr( $placeholder ) . '" value="' . esc_attr( $default ) . '"' . esc_attr( $required ) . '" />';

		$html .= '<div class="simpay-form-control simpay-date-container">';
		$html .= '<div class="simpay-date-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-date-wrap simpay-field-wrap">';
		$html .= $field;
		$html .= '</div>';
		$html .= '</div>';

		return $html;

	}

}
