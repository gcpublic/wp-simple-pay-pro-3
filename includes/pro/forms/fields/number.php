<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Number extends Custom_Field {

	/**
	 * Number constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Print HTML output for number field to frontend
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
		$placeholder = isset( $settings['placeholder'] ) ? intval( $settings['placeholder'] ) : '';
		$required    = isset( $settings['required'] ) ? 'required=""' : '';
		$default     = isset( $settings['default'] ) ? intval( $settings['default'] ) : '';
		$minimum     = isset( $settings['minimum'] ) ? intval( $settings['minimum'] ) : '';
		$maximum     = isset( $settings['maximum'] ) ? intval( $settings['maximum'] ) : '';
		$quantity    = isset( $settings['quantity'] ) ? $settings['quantity'] : '';
		$name        = 'simpay_field[' . esc_attr( $meta_name ) . ']';

		$id = simpay_dashify( $id );

		$classes = '';


		if ( ! empty( $quantity ) ) {
			$classes .= 'simpay-quantity-input';
		}

		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';

		// Field always uses these
		$field = '<input type="number" name="' . $name . '" id="' . esc_attr( $id ) . '" class="' . $classes . '" ';

		// Add placeholder only if it is set in the settings
		if ( ! empty( $placeholder ) ) {
			$field .= 'placeholder="' . esc_attr( $placeholder ) . '" ';
		}

		// Add min attribute if set in the settings
		if ( ! empty ( $minimum ) ) {
			$field .= 'min="' . esc_attr( $minimum ) . '" ';
		}

		// Add max attribute if set in the settings
		if ( ! empty( $maximum ) ) {
			$field .= 'max="' . esc_attr( $maximum ) . '" ';
		}

		// Add value attribute if default is set in the settings
		if ( ! empty( $default ) ) {
			$field .= 'value="' . esc_attr( $default ) . '" ';
		}

		$field .= $required;

		// Close field
		$field .= ' />';

		if ( ! empty( $quantity ) ) {
			$field .= '<input type="hidden" name="simpay_quantity" class="simpay-quantity" value="" />';
		}

		$html .= '<div class="simpay-form-control simpay-number-container">';
		$html .= '<div class="simpay-number-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-number-wrap simpay-field-wrap">';
		$html .= $field;
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

}
