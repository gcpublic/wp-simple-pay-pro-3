<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dropdown extends Custom_Field {

	public static $id, $meta_name, $label, $required, $default, $options, $is_quantity, $is_amount, $quantities, $amounts, $name;

	/**
	 * Dropdown constructor.
	 */
	public function __construct() {
		// No constructor needed, but to keep consistent will keep it here but just blank
	}

	/**
	 * Set the field properties
	 *
	 * @param $settings
	 */
	public static function set_properties( $settings ) {

		self::$id          = isset( $settings['id'] ) ? $settings['id'] : '';
		self::$meta_name   = isset( $settings['metadata'] ) && ! empty( $settings['metadata'] ) ? $settings['metadata'] : self::$id;
		self::$label       = isset( $settings['label'] ) ? $settings['label'] : '';
		self::$required    = isset( $settings['required'] ) ? $settings['required'] : '';
		self::$default     = isset( $settings['default'] ) ? $settings['default'] : '';
		self::$options     = isset( $settings['options'] ) ? $settings['options'] : '';
		self::$is_quantity = isset( $settings['amount_quantity'] ) && 'quantity' === $settings['amount_quantity'] ? true : false;
		self::$is_amount   = isset( $settings['amount_quantity'] ) && 'amount' === $settings['amount_quantity'] ? true : false;
		self::$quantities  = isset( $settings['quantities'] ) && ! empty( $settings['quantities'] ) ? $settings['quantities'] : '';
		self::$amounts     = isset( $settings['amounts'] ) ? $settings['amounts'] : '';
		self::$name        = 'simpay_field[' . esc_attr( self::$meta_name ) . ']';
	}

	/**
	 * Print HTML output for dropdown field on frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		self::set_properties( $settings );

		$html = '';

		if ( self::$is_amount ) {
			$html = self::amount_dropdown_html();
		} elseif ( self::$is_quantity ) {
			$html = self::quantity_dropdown_html();
		} else {

			$id = simpay_dashify( self::$id );

			$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( self::$label ) . '</label>';

			$field_options = '';

			if ( ! empty( self::$options ) ) {

				$options = array_map( 'trim', explode( simpay_list_separator(), self::$options ) );

				// Check if the default entered is actually a possible option and if it is NOT then we set it to empty so that the first option will end up being the default
				self::$default = ( ! empty( self::$default ) && false !== array_search( self::$default, $options ) ? self::$default : '' );

				if ( ! empty( $options ) && is_array( $options ) ) {
					foreach ( $options as $k => $v ) {

						if ( empty( self::$default ) ) {
							self::$default = $v;
						}

						$field_options .= '<option ' . selected( $v, self::$default, false ) . '>' . esc_html( $v ) . '</option>';
					}
				}
			}

			$html .= '<div class="simpay-form-control simpay-dropdown-container">';
			$html .= '<div class="simpay-dropdown-label simpay-label-wrap">';
			$html .= $label;
			$html .= '</div>';
			$html .= '<div class="simpay-dropdown-wrap simpay-field-wrap">';
			$html .= '<select name="' . self::$name . '" id="' . esc_attr( $id ) . '" ' . esc_attr( self::$required ) . '">';
			$html .= $field_options;
			$html .= '</select>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * HTML for quantity dropdown field
	 *
	 * @return string
	 */
	public static function quantity_dropdown_html() {

		$html = '';

		$id = simpay_dashify( self::$id );

		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( self::$label ) . '</label>';

		$field_options = '';

		$error = '<div>' . esc_html__( 'You have entered non-numerical characters into your quantities.', 'simple-pay' ) . '</div>';

		if ( ! empty( self::$options ) ) {

			$options    = array_map( 'trim', explode( simpay_list_separator(), self::$options ) );
			$quantities = array_map( 'trim', explode( simpay_list_separator(), self::$quantities ) );

			// Make sure the number of options and amounts is equal before continuing
			if ( count( $options ) !== count( $quantities ) ) {
				return '<div>' . esc_html__( 'You have a mismatched number of options and amounts. Please correct this for the dropdown to appear.', 'simple-pay' ) . '</div>';
			}

			$i = 0;

			// Check if the default entered is actually a possible option and if it is NOT then we set it to empty so that the first option will end up being the default
			self::$default = ( ! empty( self::$default ) && false !== array_search( self::$default, $options ) ? self::$default : '' );

			if ( ! empty( $options ) && is_array( $options ) ) {
				foreach ( $options as $k => $v ) {

					if ( empty( $v ) ) {
						$i++;
						continue;
					}

					if ( empty( self::$default ) ) {
						self::$default = $v;
					}

					$quantity = $quantities[ $i ];

					if ( 0 == intval( $quantity ) ) {
						return $error;
					}

					$field_options .= '<option data-quantity="' . esc_attr( $quantity ) . '" ' . selected( $v, self::$default, false ) . '>' . esc_html( $v ) . '</option>';
					$i++;
				}
			}
		}

		$html .= '<div class="simpay-form-control simpay-dropdown-container">';
		$html .= '<div class="simpay-dropdown-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-dropdown-wrap simpay-field-wrap">';
		$html .= '<select class="simpay-quantity-dropdown" name="' . self::$name . '" id="' . esc_attr( $id ) . '" ' . esc_attr( self::$required ) . '">';
		$html .= $field_options;
		$html .= '</select>';
		$html .= '<input type="hidden" name="simpay_quantity" class="simpay-quantity" value="" />';
		$html .= '</div>';
		$html .= '</div>';


		return $html;
	}

	/**
	 * HTML for amount dropdown field
	 *
	 * @return string
	 */
	public static function amount_dropdown_html() {

		$html = '';

		$id = simpay_dashify( self::$id );

		$label = '<label for="' . esc_attr( $id ) . '">' . esc_html( self::$label ) . '</label>';

		$field_options = '';

		$error = '<div>' . esc_html__( 'You have entered non-numerical characters into your amounts.', 'simple-pay' ) . '</div>';

		if ( ! empty( self::$options ) ) {

			$options = array_map( 'trim', explode( simpay_list_separator(), self::$options ) );
			$amounts = array_map( 'trim', explode( simpay_list_separator(), self::$amounts ) );

			// Make sure the number of options and amounts is equal before continuing
			if ( count( $options ) !== count( $amounts ) ) {
				return '<div>' . esc_html__( 'You have a mismatched number of options and amounts. Please correct this for the dropdown to appear.', 'simple-pay' ) . '</div>';
			}

			$i = 0;

			// Check if the default entered is actually a possible option and if it is NOT then we set it to empty so that the first option will end up being the default
			self::$default = ( ! empty( self::$default ) && false !== array_search( self::$default, $options ) ? self::$default : '' );

			if ( ! empty( $options ) && is_array( $options ) ) {
				foreach ( $options as $k => $v ) {

					if ( empty( $v ) ) {
						$i++;
						continue;
					}

					if ( empty( self::$default ) ) {
						self::$default = $v;
					}

					$amount = $amounts[ $i ];

					if ( ! is_int( $amount ) && simpay_is_zero_decimal() ) {
						return $error;
					} else if ( 0 == floatval( $amount ) ) {
						return $error;
					}

					$field_options .= '<option data-amount="' . esc_attr( $amount ) . '" ' . selected( $v, self::$default, false ) . '>' . esc_html( $v ) . '</option>';
					$i++;
				}
			}
		}

		$html .= '<div class="simpay-form-control simpay-dropdown-container">';
		$html .= '<div class="simpay-dropdown-label simpay-label-wrap">';
		$html .= $label;
		$html .= '</div>';
		$html .= '<div class="simpay-dropdown-wrap simpay-field-wrap simpay-amount-dropdown">';
		$html .= '<select name="' . self::$name . '" id="' . esc_attr( $id ) . '" ' . esc_attr( self::$required ) . '>';
		$html .= $field_options;
		$html .= '</select>';
		$html .= '</div>';
		$html .= '</div>';


		return $html;
	}

}
