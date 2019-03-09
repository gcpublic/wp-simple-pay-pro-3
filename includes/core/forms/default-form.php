<?php

namespace SimplePay\Core\Forms;

use SimplePay\Core\Abstracts\Form;
use SimplePay\Core\Forms\Fields;
use SimplePay\Core\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Form.
 *
 * The default form view bundled with the plugin.
 *
 * @since 3.0.0
 */
class Default_Form extends Form {

	public $total_amount = '';

	/**
	 * Default_Form constructor.
	 *
	 * @param $id int
	 */
	public function __construct( $id ) {

		// Construct our base form from the parent class
		parent::__construct( $id );

	}

	/**
	 * Add hooks and filters for this form instance.
	 *
	 * Hooks get run once per form instance. See https://github.com/wpsimplepay/WP-Simple-Pay-Pro-3/issues/617
	 *
	 */
	public function register_hooks() {

		add_action( 'wp_footer', array( $this, 'set_script_variables' ), 0 );

	}

	/**
	 * Set the JS script variables specifically for this form
	 */
	public function set_script_variables() {

		$temp[ $this->id ] = array(
			'form'   => $this->get_form_script_variables(),
			'stripe' => array_merge( array(
				'amount' => $this->total_amount,
			), $this->get_stripe_script_variables() ),
		);

		$temp = apply_filters( 'simpay_form_' . absint( $this->id ) . '_script_variables', $temp, $this->id );

		// Add this temp script variables to our assets so if multiple forms are on the page they will all be loaded at once and be specific to each form
		Assets::get_instance()->script_variables( $temp );
	}

	/**
	 * Output for the form
	 */
	public function html() {

		$id                = 'simpay-form-' . $this->id;
		$form_display_type = simpay_get_saved_meta( $this->id, '_form_display_type' );

		do_action( 'simpay_before_form_display', $this );

		echo '<div id="simpay-' . $form_display_type . '-form-wrap-' . $this->id . '" class="simpay-' . $form_display_type . '-form-wrap simpay-form-wrap">';

			do_action( 'simpay_form_' . absint( $this->id ) . '_before_payment_form', $this );

			// Can add additional form tag attributes here using a filter.
			$more_form_atts = apply_filters( 'simpay_more_form_attributes', '' );

			echo '<form action="" method="post" class="' . $this->get_form_classes( $this->id ) . '" id="' . esc_attr( $id ) . '" data-simpay-form-id="' . esc_attr( $this->id ) . '" ' . esc_attr( $more_form_atts ) . '>';

				do_action( 'simpay_form_' . absint( $this->id ) . '_before_form_top', $this );

				if ( ! empty( $this->custom_fields ) && is_array( $this->custom_fields ) ) {
					echo $this->print_custom_fields();
				}

				// Hidden inputs to hold the Stripe token properties (id & email) appended to the form in public.js.

				// TODO Append these hidden inputs to form in public.js?
				echo '<input type="hidden" name="simpay_form_id" value="' . esc_attr( $this->id ) . '" />';
				echo '<input type="hidden" name="simpay_amount" value="" class="simpay-amount" />';

				if ( $this->enable_shipping_address ) {
					echo $this->shipping_fields();
				}

				// Form validation error message container
				echo '<div class="simpay-errors" id="' . esc_attr( $id ) . '-error"></div>';

				echo simpay_get_test_mode_badge();

				do_action( 'simpay_form_' . absint( $this->id ) . '_before_form_bottom', $this );

			// We echo the </form> instead of appending it so that the action hook can work correctly if they try to output something before the form close.
			echo '</form>';

			do_action( 'simpay_form_' . absint( $this->id ) . '_after_form_display', $this );

		echo '</div>'; // .simpay-{$form_display_type}-form-wrap
	}

	private function get_form_classes( $id ) {

		$classes = apply_filters( 'simpay_form_' . absint( $this->id ) . '_classes', array(
			'simpay-checkout-form',
			'simpay-form-' . absint( $this->id ),
		) );

		return trim( implode( ' ', array_map( 'trim', array_map( 'sanitize_html_class', array_unique( $classes ) ) ) ) );

	}

	/**
	 * Print out the custom fields.
	 *
	 * @return string
	 */
	public function print_custom_fields() {

		$html = '';

		if ( ! empty( $this->custom_fields ) && is_array( $this->custom_fields ) ) {

			foreach ( $this->custom_fields as $k => $v ) {
	
				/*
				 * These filters are deprecated but still here for backwards compatibility
				 */
				$html = apply_filters( 'simpay_custom_field_html', $html, $v );
				$html = apply_filters( 'simpay_custom_fields', $html, $v );
			}
		}

		$html = apply_filters( 'simpay_form_' . absint( $this->id ) . '_custom_fields', $html, $this );
		$html = apply_filters( 'simpay_form_custom_fields', $html, $this );

		return $html;
	}

	/**
	 * Output hidden fields to capture shipping information if enabled
	 *
	 * @return string
	 */
	public function shipping_fields() {

		$html = '';

		$html .= '<input type="hidden" name="simpay_shipping_name" class="simpay-shipping-name" />';
		$html .= '<input type="hidden" name="simpay_shipping_country" class="simpay-shipping-country" />';
		$html .= '<input type="hidden" name="simpay_shipping_zip" class="simpay-shipping-zip" />';
		$html .= '<input type="hidden" name="simpay_shipping_state" class="simpay-shipping-state" />';
		$html .= '<input type="hidden" name="simpay_shipping_address_line1" class="simpay-shipping-address-line1" />';
		$html .= '<input type="hidden" name="simpay_shipping_city" class="simpay-shipping-city" />';

		return $html;
	}

	/**
	 * Place to set our script variables for this form.
	 *
	 * @return array
	 */
	public function get_form_script_variables() {

		$custom_fields = simpay_get_saved_meta( $this->id, '_custom_fields' );
		$loading_text  = '';

		if ( isset( $custom_fields['payment_button'] ) && is_array( $custom_fields['payment_button'] ) ) {

			foreach ( $custom_fields['payment_button'] as $k => $v ) {
				if ( is_array( $v ) && array_key_exists( 'processing_text', $v ) ) {
					if ( isset( $v['processing_text'] ) && ! empty( $v['processing_text'] ) ) {
						$loading_text = $v['processing_text'];
						break;
					}
				}
			}
		}

		if ( empty( $loading_text ) ) {
			$loading_text = esc_html__( 'Please wait...', 'simple-pay' );
		}

		$integers['integers'] = array(
			'amount' => floatval( $this->amount ),
		);

		$strings['strings'] = array(
			'loadingText' => $loading_text,
		);

		$form_variables = array_merge( $integers, $strings );

		return $form_variables;
	}
}
