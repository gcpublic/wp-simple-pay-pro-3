<?php

namespace SimplePay\Pro\Forms\Fields;

use SimplePay\Core\Abstracts\Custom_Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends Custom_Field {

	public static $id, $meta_name, $label, $default, $options, $is_quantity, $is_amount, $amounts, $quantities, $name;

	/**
	 * Radio constructor.
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
		self::$default     = isset( $settings['default'] ) ? $settings['default'] : '';
		self::$options     = isset( $settings['options'] ) ? $settings['options'] : '';
		self::$is_quantity = isset( $settings['amount_quantity'] ) && 'quantity' === $settings['amount_quantity'] ? true : false;
		self::$is_amount   = isset( $settings['amount_quantity'] ) && 'amount' === $settings['amount_quantity'] ? true : false;
		self::$amounts     = isset( $settings['amounts'] ) ? $settings['amounts'] : '';
		self::$quantities  = isset( $settings['quantities'] ) && ! empty( $settings['quantities'] ) ? $settings['quantities'] : '';
		self::$name        = 'simpay_field[' . esc_attr( self::$meta_name ) . ']';

		// Now we dashify after we set other options that need the original ID
		self::$id = simpay_dashify( self::$id );
	}

	/**
	 * Print HTML output for radio buttons on the frontend
	 *
	 * @param $settings
	 *
	 * @return string
	 */
	public static function print_html( $settings ) {

		self::set_properties( $settings );

		$html = '';

		if ( self::$is_amount ) {
			$html .= self::amount_radio_html();
		} elseif ( self::$is_quantity ) {
			$html = self::quantity_radio_html();
		} else {

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

						$option_label  = $v;
						$field_options .= '<li><label><input type="radio" name="' . self::$name . '" ' . checked( $v, self::$default, false ) . ' value="' . esc_attr( $option_label ) . '" />' . esc_html( $option_label ) . '</label></li>';
					}
				}
			}

			$html .= '<div class="simpay-form-control simpay-radio-container">';
			$html .= '<div class="simpay-radio-label simpay-label-wrap">';
			$html .= '<label>';
			$html .= self::$label;
			$html .= '</label>';
			$html .= '</div>';
			$html .= '<div class="simpay-radio-wrap simpay-field-wrap">';
			$html .= '<ul>';
			$html .= $field_options;
			$html .= '</ul>';
			$html .= '</div>';
			$html .= '</div>';
		}

		return $html;

	}

	/**
	 * HTML for quantity radio field
	 *
	 * @return string
	 */
	public static function quantity_radio_html() {

		$html = '';

		$field_options = '';

		$error = '<div>' . esc_html__( 'You have entered non-numerical characters into your quantities.', 'simple-pay' ) . '</div>';

		if ( ! empty( self::$options ) ) {

			$options    = array_map( 'trim', explode( simpay_list_separator(), self::$options ) );
			$quantities = array_map( 'trim', explode( simpay_list_separator(), self::$quantities ) );

			// Make sure the number of options and quantities is equal before continuing
			if ( count( $options ) !== count( $quantities ) ) {
				return '<div>' . esc_html__( 'You have a mismatched number of options and quantities. Please correct this for the dropdown to appear.', 'simple-pay' ) . '</div>';
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

					$option_label = esc_html( $v );

					$field_options .= '<li><label><input type="radio" value="' . esc_attr( $option_label ) . '" name="' . self::$name . '" data-quantity="' . esc_attr( $quantity ) . '" ' . checked( $v, self::$default, false ) . ' > ' . $option_label . '</label></li>';
					$i++;
				}
			}
		}


		$html .= '<div class="simpay-form-control simpay-radio-container">';
		$html .= '<div class="simpay-radio-label simpay-label-wrap">';
		$html .= '<label>';
		$html .= self::$label;
		$html .= '</label>';
		$html .= '</div>';
		$html .= '<div class="simpay-radio-wrap simpay-field-wrap simpay-quantity-radio">';
		$html .= '<ul>';
		$html .= $field_options;
		$html .= '</ul>';
		$html .= '</div>';
		$html .= '<input type="hidden" name="simpay_quantity" class="simpay-quantity" value="" />';
		$html .= '</div>';

		return $html;
	}

	/**
	 * HTML for amount radio field
	 *
	 * @return string
	 */
	public static function amount_radio_html() {

		$html = '';

		$field_options = '';

		if ( ! empty( self::$options ) ) {

			$options = array_map( 'trim', explode( simpay_list_separator(), self::$options ) );
			$amounts = array_map( 'trim', explode( simpay_list_separator(), self::$amounts ) );

			$error = '<div>' . esc_html__( 'You have entered non-numerical characters into your amounts.', 'simple-pay' ) . '</div>';

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

					$option_label = esc_html( $v );

					$field_options .= '<li><label><input type="radio" value="' . esc_attr( $option_label ) . '" name="' . self::$name . '" data-amount="' . esc_attr( $amount ) . '" ' . checked( $v, self::$default, false ) . ' > ' . $option_label . '</label></li>';
					$i++;
				}
			}
		}


		$html .= '<div class="simpay-form-control simpay-radio-container">';
		$html .= '<div class="simpay-radio-label simpay-label-wrap">';
		$html .= '<label>';
		$html .= self::$label;
		$html .= '</label>';
		$html .= '</div>';
		$html .= '<div class="simpay-radio-wrap simpay-field-wrap simpay-amount-radio">';
		$html .= '<ul>';
		$html .= $field_options;
		$html .= '</ul>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

}
