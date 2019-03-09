<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Card extends Custom_Field {


	/**
	 * Card field constructor.
	 */
	public function __construct() {
	}

	/**
	 * Render Card field front-end HTML.
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		$html = '';

		$id          = isset( $settings['id'] ) ? $settings['id'] : '';
		$label       = isset( $settings['label'] ) ? $settings['label'] : '';
		$hide_postal = isset( $settings['verify_zip'] ) ? 'false' : 'true';

		$id    = simpay_dashify( $id );
		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
		$field = '<div id="simpay-card-element-' . esc_attr( $id ) . '" class="simpay-card-wrap simpay-field-wrap" data-hide-postal="'.  esc_attr( $hide_postal )  .'"></div>';

		$html .= '<div class="simpay-form-control simpay-form-control--card simpay-card-container">';
		$html .= '<div class="simpay-card-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= $field;

		$html .= '</div>';

		return $html;
	}

}
