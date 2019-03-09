<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Customer_Name extends Custom_Field {


	/**
	 * Customer Name field constructor.
	 */
	public function __construct() {
	}

	/**
	 * Render Customer Name field front-end HTML.
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
		$required    = isset( $settings['required'] ) ? 'required=""' : '';

		$id    = simpay_dashify( $id );
		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';

		$field = '<input type="text" id="' . esc_attr( $id ). '" class="simpay-customer-name" placeholder="' . esc_attr( $placeholder ) . '" ' . $required . ' maxlength="500" /> ';

		$html .= '<div class="simpay-form-control simpay-customer-name-container">';
		$html .= '<div class="simpay-customer-name-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-customer-name-wrap simpay-field-wrap">';
		$html .= $field;
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

}
