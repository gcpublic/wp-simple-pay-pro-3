<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email extends Custom_Field {

	/**
	 * Email field constructor.
	 */
	public function __construct() {
	}

	/**
	 * Render Email field front-end HTML.
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$html = '';

		$id          = isset( $settings['id'] ) ? $settings['id'] : '';
		$label       = isset( $settings['label'] ) ? $settings['label'] : '';
		$placeholder = isset( $settings['placeholder'] ) ? $settings['placeholder'] : '';

		$id    = simpay_dashify( $id );
		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		$field = '<input type="email" id="' . esc_attr( $id ). '" class="simpay-email" placeholder="' . esc_attr( $placeholder ) . '" required="" /> ';

		$html .= '<div class="simpay-form-control simpay-email-container">';
		$html .= '<div class="simpay-email-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-email-wrap simpay-field-wrap">';
		$html .= $field;
		$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

}
