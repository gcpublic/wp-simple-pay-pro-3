<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkbox extends Custom_Field {

	/**
	 * Checkbox constructor.
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

		$id        = isset( $settings['id'] ) ? $settings['id'] : '';
		$meta_name = isset( $settings['metadata'] ) && ! empty( $settings['metadata'] ) ? $settings['metadata'] : $id;
		$label     = isset( $settings['label'] ) ? $settings['label'] : '';
		$required  = isset( $settings['required'] ) ? 'required=""' : '';
		$default   = isset( $settings['default'] ) ? 'checked="checked"' : '';
		$name      = 'simpay_field[' . esc_attr( $meta_name ) . ']';

		$id = simpay_dashify( $id );

		$label = '<label for="' . esc_attr( simpay_dashify( $id ) ) . '">' . $label . '</label>';
		$field = '<input type="checkbox" name="' . $name . '" id="' . esc_attr( $id ) . '" class="simpay-checkbox" ' . $required . ' ' . $default . '" />';

		$html .= '<div class="simpay-form-control simpay-checkbox-container">';
		$html .= '<div class="simpay-checkbox-wrap simpay-field-wrap">';
		$html .= $field . ' ' . $label;
		$html .= '</div>';
		$html .= '</div>';

		return $html;

	}

}
