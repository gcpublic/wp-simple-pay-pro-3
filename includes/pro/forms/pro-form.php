<?php

namespace SimplePay\Pro\Forms;

use SimplePay\Core\Admin\MetaBoxes\Custom_Fields;
use SimplePay\Core\Forms\Default_Form;
use SimplePay\Pro\Payments\Plan;
use SimplePay\Pro\Payments\Subscription;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pro_Form extends Default_Form {

	public $printed_subscriptions = false;
	public $printed_custom_amount = false;

	/*****
	 *
	 * GLOBAL SETTINGS
	 *
	 *****/

	/** GENERAL **/

	public $tax_percent = 0;
	public $total_amount = 0;
	public $recurring_total_amount = 0;

	/** DISPLAY **/

	public $apply_button_style = '';

	/*****
	 *
	 * FORM SETTINGS
	 *
	 *****/
	public $date_format = '';

	/** PAYMENT OPTIONS **/

	public $amount_type = '';

	// Starting one-time amount value
	public $amount = '';

	/* custom amount options */
	public $is_one_time_custom_amount = false;
	public $minimum_amount = '';
	public $default_amount = '';
	public $custom_amount_label = '';

	// Statement descriptor setting
	public $statement_descriptor = '';

	/** CUSTOM FIELDS **/

	// Recurring amount toggle interval
	public $recurring_amount_toggle_frequency = 'month';
	public $recurring_amount_toggle_interval = 1;

	/** STRIPE CHECKOUT DISPLAY **/

	public $company_name = '';
	public $item_description = '';
	public $image_url = '';
	public $enable_remember_me = '';
	public $checkout_button_text = '';
	public $trial_button_text = '';
	public $verify_zip = '';
	public $enable_billing_address = '';
	public $enable_shipping_address = '';

	/** SUBSCRIPTION OPTIONS **/

	public $subscription_type = '';

	/* Single Plan */
	public $single_plan = '';
	public $plan = '';
	public $plan_interval = '';
	public $plan_interval_count = 1;

	/* Multi-plans */
	public $default_plan = '';
	public $plans = array();
	public $multi_plan_setup_fee = '';

	// Starting subscription amount value
	public $subscription_amount = '';

	public $has_subscription_custom_amount = '';
	public $subscription_minimum_amount = '';
	public $subscription_default_amount = '';
	public $subscription_interval = '';
	public $subscription_frequency = '';
	public $subscription_custom_amount_label = '';
	public $subscription_setup_fee = '';
	public $subscription_display_type = '';
	public $has_max_charges = false;
	public $subscription_max_charges = 0;

	/*****
	 *
	 * OTHER OPTIONS
	 *
	 *****/

	public $is_trial = false;
	public $custom_fields = array();
	public $tax_amount = 0;
	public $recurring_tax_amount = 0;
	public $parent = null;

	// No settings for these, only available with filters
	public $fee_percent = 0;
	public $fee_amount = 0;

	/**
	 * Form constructor.
	 *
	 * @param $id int
	 */
	public function __construct( $id ) {

		parent::__construct( $id );

		// TODO Need to set this property?
		// Set our form specific filter to apply to each setting
		$this->filter = 'simpay_form_' . $this->id;

		// Setup the global settings tied to this form
		$this->pro_set_global_settings();

		// Setup the post meta settings tied to this form
		$this->pro_set_post_meta_settings();

		// Read settings from Stripe plans before calculating plan & total amounts.
		if ( $this->is_subscription() ) {
			$this->set_plan_settings();
		}

	}

	public function register_hooks() {

		parent::register_hooks();

		add_action( 'simpay_form_' . $this->id . '_before_payment_form', array( $this, 'before_payment_form' ) );
		add_action( 'simpay_form_' . $this->id . '_after_form_display', array( $this, 'after_form_display' ) );
		add_filter( 'simpay_form_' . $this->id . '_custom_fields', array( $this, 'get_custom_fields_html' ), 10, 3 );
		add_action( 'simpay_form_' . $this->id . '_before_form_bottom', array( $this, 'pro_html' ) );

		add_filter( 'simpay_form_' . $this->id . '_classes', array( $this, 'pro_form_classes' ) );
		add_filter( 'simpay_form_' . $this->id . '_script_variables', array( $this, 'pro_get_form_script_variables' ), 10, 2 );
		add_filter( 'simpay_stripe_script_variables', array( $this, 'pro_set_stripe_script_variables' ) );
		add_filter( 'simpay_payment_button_class', array( $this, 'payment_button_class' ) );


	}

	public function payment_button_class( $classes ) {

		$button_action = ( 'overlay' == $this->get_form_display_type() ) ? 'simpay-modal-btn' : 'simpay-payment-btn';

		if ( isset( $classes['simpay-payment-btn'] ) ) {
			unset( $classes['simpay-payment-btn'] );
		}

		$classes[] = $button_action;

		return $classes;
	}

	public function pro_form_classes( $classes ) {

		$classes[] = 'simpay-checkout-form--' . $this->get_form_display_type();

		return $classes;
	}

	// HTML to render before form output depending on form display type.
	public function before_payment_form() {

		$html              = '';
		$heading_html      = '';
		$form_display_type = $this->get_form_display_type();
		$form_title        = simpay_get_saved_meta( $this->id, '_company_name', '' );
		$form_description  = simpay_get_saved_meta( $this->id, '_item_description', '' );


		// Add title & description text for Embedded & Overlay form types if they exist.

		if ( 'embedded' === $form_display_type || 'overlay' === $form_display_type ) {

			if ( ! empty( $form_title ) ) {
				$heading_html .= '<h3 class="simpay-form-title">' . esc_html( $form_title ) . '</h3>';
			}

			if ( ! empty( $form_description ) ) {
				$heading_html .= '<div class="simpay-form-description">' . esc_html( $form_description ) . '</div>';
			}
		}

		if ( 'embedded' === $form_display_type ) {

			$html .= '<div class="simpay-embedded-heading">';
			$html .= $heading_html;
			$html .= '</div>';

		} elseif ( 'overlay' === $form_display_type ) {

			$html .= '<label for="simpay-modal-control-' . esc_attr( $this->id ) . '" class="simpay-modal-control-open">' . $this->get_payment_button( $this->custom_fields ) . '</label>';
			$html .= '<input type="checkbox" id="simpay-modal-control-' . esc_attr( $this->id ) . '" class="simpay-modal-control">';
			$html .= '<div class="simpay-modal">';
			$html .= '<div class="simpay-modal__body">';
			$html .= '<div class="simpay-modal__content">';
			$html .= $heading_html;
			$html .= '<label for="simpay-modal-control-' . esc_attr( $this->id ) . '" class="simpay-modal-control-close">&#x2715;</label>';
		}

		echo $html;
	}

	// HTML to render after form output depending on form display type.
	public function after_form_display() {

		$html = '';

		if ( 'overlay' == $this->get_form_display_type() ) {
			$html .= '</div>';
			$html .= '</div>';
			$html .= '</div>';

			// Show a test mode badge here since the main one is only shown on the custom overlay.
			$html .= simpay_get_test_mode_badge();
		}

		echo $html;
	}

	// Helper function to get payment button out of the form
	private function get_payment_button( $fields ) {

		$html = '';

		foreach ( $fields as $k => $v ) {
			switch ( $v['type'] ) {
				case 'payment_button':
					$html .= \SimplePay\Core\Forms\Fields\Payment_Button::html( $v );
			}
		}

		return $html;
	}

	public function pro_html() {

		$html = '';

		// In case they have subscriptions but have not set the custom field for placement we will print it after the other custom fields.
		if ( ! $this->printed_subscriptions && $this->is_subscription() && 'user' === $this->subscription_type ) {
			$html .= $this->print_subscription_options( $this->has_subscription_custom_amount );
		}

		// Print custom amount field if this is not a subscription (subscription custom amount is handled in the print_subscription() function
		if ( ! $this->printed_custom_amount ) {
			if ( $this->is_one_time_custom_amount || $this->has_subscription_custom_amount ) {
				$html .= $this->print_custom_amount();
			}
		}

		if ( $this->is_subscription() ) {
			$html .= '<input type="hidden" name="simpay_multi_plan_id" value="" class="simpay-multi-plan-id" />';
			$html .= '<input type="hidden" name="simpay_multi_plan_setup_fee" value="" class="simpay-multi-plan-setup-fee" />';
			$html .= '<input type="hidden" name="simpay_max_charges" value="" class="simpay-max-charges" />';
		}

		// Add a hidden field to hold the tax value
		if ( $this->tax_percent > 0 && ! $this->is_subscription() ) {
			$html .= '<input type="hidden" name="simpay_tax_amount" value="" class="simpay-tax-amount" />';
		}

		echo $html;
	}

	/**
	 * Print the subscription options
	 *
	 * @param bool $custom_amount If a custom amount is found and should be printed
	 *
	 * @return string
	 */
	public function print_subscription_options( $custom_amount = false ) {

		$html              = '';
		$plan_select_label = simpay_get_saved_meta( $this->id, '_plan_select_form_field_label' );

		if ( 'single' === $this->subscription_type ) {

			if ( $custom_amount ) {
				$html .= $this->print_custom_amount();
			}

		} elseif ( 'user' === $this->subscription_type ) {

			$plans = $this->plans;

			if ( empty( $plans ) ) {
				$html = simpay_admin_error( '<div>' . esc_html__( 'You have not set any plans to choose from.', 'simple-pay' ) . '</div>' );

				$this->printed_subscriptions = true;

				return $html;
			}

			$html .= '<div class="simpay-plan-wrapper simpay-form-control">';

			// Add label
			if ( ! empty( $plan_select_label ) ) {
				$html .= '<div class="simpay-plan-select-label simpay-label-wrap"><label>' . esc_html( $plan_select_label ) . '</label></div>';
			}

			if ( 'radio' === $this->subscription_display_type ) {

				$html .= '<ul class="simpay-multi-plan-radio-group">';

				if ( ! empty( $plans ) && is_array( $plans ) ) {
					foreach ( $plans as $k => $v ) {

						// If $v is not an array skip this one
						if ( ! is_array( $v ) ) {
							continue;
						}

						if ( empty( $this->default_plan ) ) {
							$this->default_plan = $v['select_plan'];
						}

						if ( 'empty' === $v['select_plan'] ) {
							continue;
						}


						if ( isset( $v['plan_object'] ) ) {
							// Use the cached plan object that is set on the form save
							$plan = $v['plan_object'];
						} else {
							// If no cached object is found then revert to calling the Stripe API
							$plan = Plan::get_plan_by_id( $v['select_plan'] );
						}

						if ( ! $plan ) {
							$html .= simpay_admin_error( '<li>' . sprintf( wp_kses( __( 'The plan <strong>%1$s</strong> does not exist.', 'simple-pay' ), array( 'strong' => array() ) ), $v['select_plan'] ) . '</li>' );
							continue;
						}

						// Our plan is good and we can process the rest
						$plan_name           = $plan->name;
						$plan_amount         = simpay_convert_amount_to_dollars( $plan->amount );
						$plan_interval       = $plan->interval;
						$plan_interval_count = $plan->interval_count;
						$is_trial            = $plan->trial_period_days > 0 ? true : false;
						$max_charges         = isset( $v['max_charges'] ) && ! empty( $v['max_charges'] ) ? $v['max_charges'] : 0;

						if ( ! empty( $v['custom_label'] ) ) {
							$label = $v['custom_label'];
						} else {
							$label = $plan_name . ' ' . sprintf( _n( '%1$s/%3$s', '%1$s every %2$d %3$ss', $plan_interval_count, 'simple-pay' ), simpay_format_currency( $plan_amount, $plan->currency ), $plan_interval_count, $plan_interval );
						}

						$checked = $this->default_plan === $v['select_plan'] ? 'checked' : '';

						if ( 'checked' === $checked ) {
							$this->is_trial = $is_trial;
						}

						$html .= '<li><label><input class="simpay-multi-sub" type="radio" name="simpay_multi_plan_' . esc_attr( $this->id ) . '" value="' . esc_attr( $v['select_plan'] ) . '" data-plan-id="' . esc_attr( $v['select_plan'] ) . '" data-plan-amount="' . floatval( $plan_amount ) . '" data-plan-setup-fee="' . esc_attr( $v['setup_fee'] ) . '" data-plan-interval="' . esc_attr( $plan_interval ) . '" ' . ( $is_trial ? ' data-plan-trial="true" ' : '' ) . ' data-plan-interval-count="' . esc_attr( $plan_interval_count ) . '" ' . $checked . ' data-plan-max-charges="' . absint( $max_charges ) . '" />' . esc_html( apply_filters( 'simpay_plan_name_label', $label, $plan ) ) . '</label></li>';
					}
				}

				if ( $custom_amount ) {

					$html .= '<li><label><input data-plan-setup-fee="0" type="radio" class="simpay-multi-sub simpay-custom-plan-option" name="simpay_multi_plan_' . esc_attr( $this->id ) . '" data-plan-interval="' . esc_attr( $this->subscription_frequency ) . '" data-plan-interval-count="' . esc_attr( $this->subscription_interval ) . '" value="simpay_custom_plan" />' . esc_html( $this->subscription_custom_amount_label ) . '</label>';
					$html .= $this->print_custom_amount( false );
					$html .= '</li>';
				}

				$html .= '</ul>';

			} elseif ( 'dropdown' === $this->subscription_display_type ) {


				$html .= '<div class="simpay-form-control">';

				$html .= '<select>';

				if ( ! empty( $plans ) && is_array( $plans ) ) {
					foreach ( $plans as $k => $v ) {

						// If $v is not an array we need to skip it
						if ( ! is_array( $v ) ) {
							continue;
						}

						if ( empty( $this->default_plan ) ) {
							$this->default_plan = $v['select_plan'];
						}

						if ( 'empty' === $v['select_plan'] ) {
							continue;
						}

						if ( isset( $v['plan_object'] ) ) {
							// Use the cached plan object that is set on the form save
							$plan = $v['plan_object'];
						} else {
							// If no cached object is found then revert to calling the Stripe API
							$plan = Plan::get_plan_by_id( $v['select_plan'] );
						}

						if ( false === $plan ) {
							$html .= simpay_admin_error( '<li>' . sprintf( wp_kses( __( 'The plan <strong>%1$s</strong> does not exist.', 'simple-pay' ), array( 'strong' => array() ) ), $v['select_plan'] ) . '</li>' );
							continue;
						}

						// Our plan is good and we can process the rest
						$plan_name           = $plan->name;
						$plan_amount         = simpay_convert_amount_to_dollars( $plan->amount );
						$plan_interval       = $plan->interval;
						$plan_interval_count = $plan->interval_count;
						$is_trial            = $plan->trial_period_days > 0 ? true : false;
						$max_charges         = isset( $v['max_charges'] ) && ! empty( $v['max_charges'] ) ? $v['max_charges'] : 0;

						if ( ! empty( $v['custom_label'] ) ) {
							$label = $v['custom_label'];
						} else {
							$label = $plan_name . ' ' . sprintf( _n( '%1$s/%3$s', '%1$s every %2$d %3$ss', $plan_interval_count, 'simple-pay' ), simpay_format_currency( $plan_amount, $plan->currency ), $plan_interval_count, $plan_interval );
						}

						// This needs to check selected status for dropdown. Bit different than radio
						$selected = $this->default_plan === $v['select_plan'] ? 'selected' : '';

						if ( 'selected' === $selected ) {
							$this->is_trial = $is_trial;
						}

						$html .= '<option class="simpay-multi-sub" name="simpay_multi_plan_' . esc_attr( $this->id ) . '" value="' . esc_attr( $v['select_plan'] ) . '" data-plan-id="' . esc_attr( $v['select_plan'] ) . '" data-plan-amount="' . floatval( $plan_amount ) . '" data-plan-setup-fee="' . esc_attr( $v['setup_fee'] ) . '" ' . ( $is_trial ? ' data-plan-trial="true" ' : '' ) . ' data-plan-interval="' . esc_attr( $plan_interval ) . '" ' . $selected . ' data-plan-max-charges="' . absint( $max_charges ) . '">' . esc_html( apply_filters( 'simpay_plan_name_label', $label, $plan ) ) . '</option>';
					}
				}

				if ( $custom_amount ) {
					$html .= '<option data-plan-setup-fee="0" name="simpay_multi_plan_' . esc_attr( $this->id ) . '" value="simpay_custom_plan" class="simpay-multi-sub simpay-custom-plan-option" data-plan-interval="' . esc_attr( $this->subscription_frequency ) . '" data-plan-interval-count="' . esc_attr( $this->subscription_interval ) . '">' . esc_html( $this->subscription_custom_amount_label ) . '</option>';
				}

				$html .= '</select>';

				$html .= '</div>';

				if ( $custom_amount ) {
					$html .= $this->print_custom_amount();
				}
			}

			$html .= '</div>';

			// Set flag to know we have printed these
			$this->printed_subscriptions = true;
		}

		return $html;

	}

	/**
	 * Print a custom amount field.
	 *
	 * @param bool $print_wrapper Check if we should print the outer wrapper for the field or not.
	 *
	 * @return string
	 */
	public function print_custom_amount( $print_wrapper = true ) {

		$html = '';

		// Set default amount, input name, and label based on if this form is a subscription or not.
		if ( $this->is_subscription() ) {
			$min_amount     = $this->subscription_minimum_amount;
			$default_amount = $this->subscription_default_amount;
			$final_amount   = $this->subscription_amount;
			$input_name     = 'simpay_subscription_custom_amount';
			$label          = 'user' !== $this->subscription_type ? simpay_get_saved_meta( $this->id, '_plan_select_form_field_label' ) : '';
		} else {
			$min_amount     = $this->minimum_amount;
			$default_amount = $this->default_amount;
			$final_amount   = $this->amount;
			$input_name     = 'simpay_custom_amount';
			$label          = $this->custom_amount_label;
		}

		if ( $default_amount >= $min_amount ) {

			// Format custom amount input value with thousands & decimal separators, but not symbol.
			$custom_amount_input_value = simpay_format_currency( $final_amount, '', false );
		} else {
			// If default amount is less than minimum, then simply leave blank.
			$custom_amount_input_value = '';
		}

		// outer wrap div
		if ( $print_wrapper ) {
			$html .= '<div class="simpay-form-control">';
		}

		$field_id = esc_attr( simpay_dashify( $input_name ) ) . '-' . $this->id;

		// Label
		$html .= '<div class="simpay-custom-amount-label simpay-label-wrap">';
		$html .= '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $label ) . '</label>';
		$html .= '</div>';

		// Currency symbol placement & html
		$currency_symbol_placement = ( 'left' === $this->currency_position || 'left_space' === $this->currency_position ) ? 'left' : 'right';
		$currency_symbol_html      = '<span class="simpay-currency-symbol simpay-currency-symbol-' . $currency_symbol_placement . '">' . simpay_get_currency_symbol( $this->currency ) . '</span>';

		if ( 'left' === $currency_symbol_placement ) {
			$html .= $currency_symbol_html;
		}

		// Field output
		$html .= '<div class="simpay-custom-amount-wrap simpay-field-wrap">';

		// TODO Test custom input on mobile

		// Filter to allow changing to "number" input type.
		// "tel" input type brings up number pad but does not allow decimal entry on mobile browsers.
		$custom_amount_input_type = apply_filters( 'simpay_custom_amount_field_type', 'tel' );
		$custom_amount_input_type = ( $custom_amount_input_type !== 'tel' && $custom_amount_input_type !== 'number' ) ? 'tel' : $custom_amount_input_type;

		// Can add additional form tag attributes here using a filter.
		// If type="number", automatically add step="0.01" attribute.
		$custom_amount_input_atts = '';

		if ( $custom_amount_input_type === 'number' ) {
			$custom_amount_input_atts = 'step="0.01"';
		}

		$custom_amount_input_atts = apply_filters( 'simpay_custom_amount_input_attributes', $custom_amount_input_atts );

		$html .= '<input id="' . $field_id . '" name="' . esc_attr( $input_name ) . '" class="simpay-amount-input simpay-custom-amount-input simpay-custom-amount-input-symbol-' . $currency_symbol_placement . '" type="' . esc_attr( $custom_amount_input_type ) . '" value="' . esc_attr( $custom_amount_input_value ) . '" ' . $custom_amount_input_atts . ' />';

		// If this is a subscription then add a field we can keep track of the custom amount selection
		if ( $this->is_subscription() ) {
			$html .= '<input type="hidden" name="simpay_has_custom_plan" class="simpay-has-custom-plan" value="' . ( 'single' === $this->subscription_type ? 'true' : '' ) . '" />';
		}

		if ( 'right' === $currency_symbol_placement ) {
			$html .= $currency_symbol_html;
		}

		$html .= '</div>';

		// Close wrapper
		if ( $print_wrapper ) {
			$html .= '</div>';
		}

		// Set flag so we know this was already printed
		$this->printed_custom_amount = true;

		return $html;

	}

	/**
	 * Print out the custom fields.
	 *
	 * @return string
	 */
	public function get_custom_fields_html( $html, $form ) {
		
		if ( ! empty( $form->custom_fields ) && is_array( $form->custom_fields ) ) {

			foreach ( $form->custom_fields as $k => $item ) {

				switch ( $item['type'] ) {

					case 'customer_name':
						$html .= Fields\Customer_Name::html( $item );
						break;

					case 'email':
						$html .= Fields\Email::html( $item );
						break;

					case 'card':
						$html .= Fields\Card::html( $item );
						break;

					case 'address':
						$html .= Fields\Address::html( $item );
						break;

					case 'checkbox':
						$html .= Fields\Checkbox::html( $item );
						break;

					case 'coupon':
						$html .= Fields\Coupon::html( $item );
						break;

					case 'date':
						$html .= Fields\Date::html( $item );
						break;

					case 'dropdown':
						$html .= Fields\Dropdown::html( $item );
						break;

					case 'number':
						$html .= Fields\Number::html( $item );
						break;

					case 'radio':
						$html .= Fields\Radio::html( $item );
						break;

					case 'custom_amount':
						if ( $this->is_one_time_custom_amount ) {
							$html .= $this->print_custom_amount();
						}
						break;

					case 'plan_select':
						if ( $this->is_subscription() ) {
							$html .= $this->print_subscription_options( $this->has_subscription_custom_amount );
							Fields\Total_Amount::set_recurring_total( $this->recurring_total_amount );
						}
						break;

					case 'total_amount':
						Fields\Total_Amount::set_tax_amount( $this->tax_amount );

						// Set to subscription fee only if trial and not custom amount.
						if ( $this->is_trial && ! $this->is_one_time_custom_amount ) {
							Fields\Total_Amount::set_total( $this->subscription_setup_fee );
						} else {
							Fields\Total_Amount::set_total( $this->total_amount );
						}

						$html .= Fields\Total_Amount::html( $item );
						break;

					case 'text':
						$html .= Fields\Text::html( $item );
						break;

					case 'recurring_amount_toggle':
						$html .= Fields\Recurring_Amount_Toggle::html( $item );
						break;

					case 'checkout_button':

						// TODO Need to use set_total like 'total_amount' case?
						$html .= Fields\Checkout_Button::html( $item );
						break;

					case 'payment_button':
						if ( 'overlay' !== $this->get_form_display_type() ) {
							$html .= \SimplePay\Core\Forms\Fields\Payment_Button::html( $item );
						}
						break;

					default:
						$html .= apply_filters( 'simpay_custom_field_html_for_non_native_fields', '', $item, $form );
						break;
				}

			}

		}

		return $html;
	}

	/**
	 * Set the global settings options to the form attributes.
	 */
	public function pro_set_global_settings() {

		// Set all the global settings that have been saved here.
		// Doing this here allows us to make every setting filterable on a per-form basis

		// We have to use simpay_get_filtered() for these since this is the first time setting these values. That's why we can't use something like simpay_get_setting()
		// Basically, think of this as the construction of global $simpay_form, so anything that uses $simpay_form will not work because the global will still be null at this point.

		/** GENERAL **/

		/* Currency Options */
		$this->tax_percent = floatval( simpay_get_filtered( 'tax_percent', simpay_get_global_setting( 'tax_percent' ), $this->id ) );

		/* Date Options */
		$this->date_format = simpay_get_filtered( 'date_format', simpay_get_date_format(), $this->id );

		/** DISPLAY **/

		/* Front-end Styles */
		$this->apply_button_style = simpay_get_filtered( 'apply_button_style', simpay_get_global_setting( 'apply_button_style' ), $this->id );
	}

	/**
	 * Set the form settings options to the form attributes.
	 */
	public function pro_set_post_meta_settings() {

		// Set all the form settings that have been saved here.
		// Doing this here allows us to make every setting filterable on a per-form

		// We have to use simpay_get_filtered() for these since this is the first time setting these values. That's why we can't use something like simpay_get_setting()
		// Basically, think of this as the construction of global $simpay_form, so anything that uses $simpay_form will not work because the global will still be null at this point.

		// Custom Fields sorted by order
		$this->custom_fields = $this->sort_fields( Custom_Fields::get_fields( $this->id ) );

		// Set subscription type ('disabled' = NOT a subscription)
		$this->subscription_type = simpay_get_filtered( 'subscription_type', simpay_get_saved_meta( $this->id, '_subscription_type' ), $this->id );

		if ( $this->is_subscription() ) {

			/** SUBSCRIPTIONS **/

			// Single and multi-plans. Not sure how to handle this just yet so these are just placeholders.
			$this->single_plan = simpay_get_filtered( 'single_plan', simpay_get_saved_meta( $this->id, '_single_plan' ), $this->id );

			// Multi-plan subscription display style (radio, dropdown)
			$this->subscription_display_type = simpay_get_filtered( 'subscription_display_type', simpay_get_saved_meta( $this->id, '_multi_plan_display' ), $this->id );

			// Check if multi plans and set it
			if ( 'user' === $this->subscription_type ) {
				$this->default_plan = simpay_get_filtered( 'default_plan', simpay_get_saved_meta( $this->id, '_multi_plan_default_value' ), $this->id );
				$this->plans        = simpay_get_filtered( 'plans', simpay_get_saved_meta( $this->id, '_multi_plan' ), $this->id );
			}

			$this->subscription_amount              = 0;
			$this->subscription_minimum_amount      = simpay_unformat_currency( simpay_get_filtered( 'subscription_minimum_amount', simpay_get_saved_meta( $this->id, '_multi_plan_minimum_amount' ), $this->id ) );
			$this->subscription_default_amount      = simpay_unformat_currency( simpay_get_filtered( 'subscription_default_amount', simpay_get_saved_meta( $this->id, '_multi_plan_default_amount' ) ) );
			$this->subscription_interval            = intval( simpay_get_filtered( 'subscription_interval', simpay_get_saved_meta( $this->id, '_plan_interval' ), $this->id ) );
			$this->subscription_frequency           = simpay_get_filtered( 'subscription_frequency', simpay_get_saved_meta( $this->id, '_plan_frequency' ), $this->id );
			$this->has_subscription_custom_amount   = ( 'enabled' === simpay_get_filtered( 'subscription_custom_amount', simpay_get_saved_meta( $this->id, '_subscription_custom_amount' ), $this->id ) );
			$this->subscription_custom_amount_label = simpay_get_filtered( 'subscription_custom_amount_label', simpay_get_saved_meta( $this->id, '_custom_plan_label', esc_html__( 'Other amount', 'simple-pay' ) ), $this->id );
			$this->subscription_setup_fee           = simpay_unformat_currency( simpay_get_filtered( 'subscription_setup_fee', simpay_get_saved_meta( $this->id, '_setup_fee' ), $this->id ) );
			$this->subscription_max_charges         = intval( simpay_get_filtered( 'subscription_max_charges', simpay_get_saved_meta( $this->id, '_max_charges', 0 ), $this->id ) );

			if ( $this->subscription_max_charges > 0 ) {
				$this->has_max_charges = true;
			}

			if ( 'single' === $this->subscription_type ) {

				// When a custom amount is the only choice for a single subscription,
				// try setting the base amount to the default amount, then minimum amount if none.
				if ( $this->has_subscription_custom_amount ) {

					if ( $this->subscription_default_amount > $this->subscription_minimum_amount ) {
						$this->subscription_amount = $this->subscription_default_amount;
					} else {
						$this->subscription_amount = $this->subscription_minimum_amount;
					}

				} else {

					if ( false !== $this->single_plan && 'empty' !== $this->single_plan ) {
						$this->subscription_amount = simpay_convert_amount_to_dollars( Plan::get_plan_amount( $this->single_plan ) );
					}
				}

			} else {

				// If a non-custom subscription amount, retrieve the saved value from the selected plan.
				if ( false !== $this->default_plan && 'empty' !== $this->default_plan ) {
					$this->subscription_amount = simpay_convert_amount_to_dollars( Plan::get_plan_amount( $this->default_plan ) );
				}
			}

			// TODO Test amounts w/ trials
			// TODO Test amounts w/ recurring toggle

			// Calculate tax amount of first sub payment + setup fee.
			$this->tax_amount = simpay_calculate_tax_amount( $this->subscription_amount + $this->subscription_setup_fee );

			// Sum total amount from first sub payment + setup fee + tax amount.
			$this->total_amount = $this->subscription_amount + $this->subscription_setup_fee + $this->tax_amount;

			// Calculate tax amount of subsequent sub payment (no setup fee).
			$this->recurring_tax_amount = simpay_calculate_tax_amount( $this->subscription_amount );

			// Sum total amount from subsequenty sub payment (no setup fee) + tax amount.
			$this->recurring_total_amount = $this->subscription_amount + $this->recurring_tax_amount;

		} else {

			/** ONE-TIME PAYMENTS (set amount or custom amount) */

			$this->amount_type               = simpay_get_filtered( 'amount_type', simpay_get_saved_meta( $this->id, '_amount_type' ), $this->id );
			$this->is_one_time_custom_amount = simpay_get_filtered( 'one_time_custom_amount', ( ( 'one_time_custom' === $this->amount_type ) ? true : false ), $this->id );
			$this->minimum_amount            = simpay_unformat_currency( simpay_get_filtered( 'minimum_amount', simpay_get_saved_meta( $this->id, '_minimum_amount' ), $this->id ) );
			$this->default_amount            = simpay_unformat_currency( simpay_get_filtered( '_default_amount', simpay_get_saved_meta( $this->id, '_custom_amount_default' ), $this->id ) );
			$this->custom_amount_label       = simpay_get_filtered( 'custom_amount_label', simpay_get_saved_meta( $this->id, '_custom_amount_label' ), $this->id );

			if ( $this->is_one_time_custom_amount ) {

				// For custom amount, try setting the base amount to the default amount, then minimum amount if none.
				if ( $this->default_amount > $this->minimum_amount ) {
					$this->amount = $this->default_amount;
				} else {
					$this->amount = $this->minimum_amount;
				}

			} else {

				// If a non-custom one-time payment amount, retrieve the saved value.
				$this->amount = simpay_unformat_currency( simpay_get_filtered( 'amount', simpay_get_saved_meta( $this->id, '_amount', simpay_global_minimum_amount() ), $this->id ) );
			}

			// Calculate tax amount of one-time payment.
			$this->tax_amount = simpay_calculate_tax_amount( $this->amount );

			// Sum total amount from one-time payment + tax amount.
			$this->total_amount = $this->amount + $this->tax_amount;
		}

		/** CUSTOM FIELD SETTINGS */

		// Recurring amount toggle interval and frequency
		$this->recurring_amount_toggle_interval  = absint( $this->extract_custom_field_setting( 'recurring_amount_toggle', 'plan_interval', 1 ) );
		$this->recurring_amount_toggle_frequency = $this->extract_custom_field_setting( 'recurring_amount_toggle', 'plan_frequency', 'month' );

		/** CHECKOUT OVERLAY DISPLAY **/
		$this->trial_button_text = simpay_get_filtered( 'trial_button_text', simpay_get_saved_meta( $this->id, '_trial_button_text', esc_html__( 'Start Your Free Trial', 'simple-pay' ) ), $this->id );

		/** OTHER **/
		$this->fee_percent = floatval( simpay_get_filtered( 'fee_percent', 0, $this->id ) );
		$this->fee_amount  = simpay_unformat_currency( simpay_get_filtered( 'fee_amount', 0, $this->id ) );
	}

	/**
	 * Extract the value from a custom field setting if it exists
	 *
	 * @param        $field_type
	 * @param        $setting
	 * @param string $default
	 *
	 * @return string
	 */
	public function extract_custom_field_setting( $field_type, $setting, $default = '' ) {

		if ( is_array( $this->custom_fields ) && ! empty( $this->custom_fields ) ) {
			foreach ( $this->custom_fields as $k => $field ) {
				if ( $field_type === $field['type'] ) {
					return isset( $field[ $setting ] ) ? $field[ $setting ] : $default;
				}
			}
		}

		return $default;
	}

	/**
	 * Set the plan settings
	 */
	public function set_plan_settings() {

		$plans = $this->plans;

		if ( is_array( $plans ) ) {

			foreach ( $plans as $k => $v ) {

				if ( ! is_array( $v ) ) {
					continue;
				}

				if ( empty( $this->default_plan ) || $this->default_plan === 'empty' ) {
					$this->default_plan = $v['select_plan'];
				}

				$is_default = $this->default_plan === $v['select_plan'] ? 'checked' : '';

				if ( $is_default ) {
					$this->multi_plan_setup_fee = $v['setup_fee'];

					// Checked for cached plan and fallback to plan ID
					$plan = isset( $v['plan_object'] ) ? $v['plan_object'] : $v['select_plan'];

					$this->amount = simpay_convert_amount_to_dollars( Plan::get_plan_amount( $plan ) );
					break;
				}
			}
		}

		if ( 'single' === $this->subscription_type ) {
			if ( ! $this->has_subscription_custom_amount ) {

				$id          = get_post_meta( $this->id, '_single_plan', true );
				$cached_plan = get_post_meta( $this->id, '_single_plan_object', true );

				if ( ! empty( $id ) && 'empty' !== $id ) {

					if ( $cached_plan ) {
						// Use cached plan object if found
						$plan = $cached_plan;
					} else {
						// Default to calling Stripe API for plan if cached not found
						$plan = Plan::get_plan_by_id( $id );
					}

					if ( false !== $plan ) {
						// No need to convert here since Stripe returns it to us as we need it
						$this->plan                   = $plan->id;
						$this->amount                 = simpay_convert_amount_to_dollars( $plan->amount );
						$this->subscription_frequency = $plan->interval;
						$this->subscription_interval  = $plan->interval_count;
						$this->is_trial               = Subscription::has_trial( $plan );
					} else {
						echo 'An error occurred ' . $plan;
					}
				} else {
					esc_html_e( 'You have not selected a plan.', 'simple-pay' );
				}
			}
		}

		if ( 'user' === $this->subscription_type ) {
			$this->multi_plan_setup_fee = \SimplePay\Core\SimplePay()->session->get( 'multi_plan_setup_fee' );
		}
	}

	/**
	 * Check if this form has subscriptions enabled or not
	 *
	 * @return bool
	 */
	public function is_subscription() {
		return ( 'disabled' !== $this->subscription_type && ! empty( $this->subscription_type ) ? true : false );
	}

	/**
	 * Sort the custom fields by their order
	 *
	 * @param $arr
	 *
	 * @return array|string
	 */
	private function sort_fields( $arr ) {

		// If our array is empty then exit now
		if ( empty( $arr ) ) {
			return '';
		}

		$fields     = $arr;
		$new_fields = array();
		$order      = array();

		if ( is_array( $fields ) ) {
			foreach ( $fields as $key => $row ) {

				if ( is_array( $row ) ) {
					foreach ( $row as $k => $v ) {

						$order[] = isset( $v['order'] ) ? $v['order'] : 9999;

						$v['type']    = $key;
						$new_fields[] = $v;

					}
				}
			}
		}

		array_multisort( $order, SORT_ASC, $new_fields );

		return $new_fields;
	}

	/**
	 * Place to set our script variables for this form.
	 *
	 * @return array
	 */
	public function pro_get_form_script_variables( $arr, $id ) {

		$custom_fields = simpay_get_saved_meta( $this->id, '_custom_fields' );
		$loading_text  = '';

		$form_arr = $arr[ $id ]['form'];

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

		$checkout_button_text_no_amount = trim( str_replace('{{amount}}', '', $this->checkout_button_text ) );

		$bools['bools'] = array_merge( isset( $form_arr['bools'] ) ? $form_arr['bools'] : array(), array(
			'isSubscription' => $this->is_subscription(),
			'isTrial'        => $this->is_trial,
		) );

		// TODO Use object props for min amounts?

		$min     = simpay_get_saved_meta( $this->id, '_minimum_amount' );
		$min     = ! empty( $min ) ? $min : simpay_global_minimum_amount();
		$sub_min = simpay_get_saved_meta( $this->id, '_multi_plan_minimum_amount' );
		$sub_min = ! empty( $sub_min ) ? $sub_min : simpay_global_minimum_amount();

		$integers['integers'] = array_merge( isset( $form_arr['integers'] ) ? $form_arr['integers'] : array(), array(
			'setupFee'          => $this->subscription_setup_fee,
			'minAmount'         => $min,
			'totalAmount'       => $this->total_amount,
			'subMinAmount'      => $sub_min,
			'planIntervalCount' => $this->subscription_interval,
			'taxPercent'        => floatval( $this->tax_percent ),
			'feePercent'        => $this->fee_percent,
			'feeAmount'         => $this->fee_amount,
		) );

		$strings['strings'] = array_merge( isset( $form_arr['strings'] ) ? $form_arr['strings'] : array(), array(
			'subscriptionType'    => $this->subscription_type,
			'planInterval'        => $this->subscription_frequency,
			'freeTrialButtonText' => $this->trial_button_text,
			'checkoutButtonText'  => $checkout_button_text_no_amount, // Checkout button text without {{amount}}
			'loadingText'         => $loading_text,
			'dateFormat'          => $this->date_format,
			'formDisplayType'     => $this->get_form_display_type(),
		) );

		$i18n['i18n'] = array_merge( isset( $form_arr['i18n'] ) ? $form_arr['i18n'] : array(), array(
			/* translators: message displayed on front-end for amount below minimum amount for one-time payment custom amount field */
			'minCustomAmountError'    => sprintf( esc_html__( 'The minimum amount allowed is %s', 'simple-pay' ), simpay_format_currency( $min ) ),
			/* translators: message displayed on front-end for amount below minimum amount for subscription custom amount field */
			'subMinCustomAmountError' => sprintf( esc_html__( 'The minimum amount allowed is %s', 'simple-pay' ), simpay_format_currency( $sub_min ) ),
		) );

		$form_arr = array_merge( $form_arr, $integers, $strings, $bools, $i18n );

		$arr[ $id ]['form'] = $form_arr;

		return $arr;
	}

	/**
	 * Set all the script variables for the Stripe specific settings (the ones Stripe needs for the checkout form)
	 * in Pro. Extends get_stripe_script_variables() in core.
	 * TODO Currently not used.
	 *
	 * @return array
	 */
	public function pro_set_stripe_script_variables( $script_vars ) {

		return $script_vars;
	}

	private function get_form_display_type() {
		return simpay_get_saved_meta( $this->id, '_form_display_type', 'embedded' );
	}
}
