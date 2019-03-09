<?php

namespace SimplePay\Core\Admin\Pages;

use SimplePay\Core\Abstracts\Admin_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feeds settings.
 *
 * Handles form settings and outputs the settings page markup.
 *
 * @since 3.0.0
 */
class Keys extends Admin_Page {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->id           = 'keys';
		$this->option_group = 'settings';
		$this->label        = esc_html__( 'Stripe Setup', 'simple-pay' );
		$this->link_text    = esc_html__( 'Help docs for Stripe Keys Settings', 'simple-pay' );
		$this->link_slug    = ''; // TODO: Fill in slug, not in use currently (issue #301)
		$this->ga_content   = 'general-settings';

		$this->sections = $this->add_sections();
		$this->fields   = $this->add_fields();
	}

	/**
	 * Add sections.
	 *
	 * @since  3.0.0
	 *
	 * @return array
	 */
	public function add_sections() {

		return apply_filters( 'simpay_add_' . $this->option_group . '_' . $this->id . '_sections', array(
			'connect'      => array(
				'title' => '',
			),
			'mode'      => array(
				'title' => '',
			),
			'test_keys' => array(
				'title' => '',
			),
			'live_keys' => array(
				'title' => '',
			),
		) );
	}

	/**
	 * Add fields.
	 *
	 * @since  3.0.0
	 *
	 * @return array
	 */
	public function add_fields() {

		$fields       = array();
		$this->values = get_option( 'simpay_' . $this->option_group . '_' . $this->id );

		if ( ! empty( $this->sections ) && is_array( $this->sections ) ) {
			foreach ( $this->sections as $section => $a ) {

				$section = sanitize_key( $section );

				if ( 'connect' == $section ) {

					$stripe_connect_url = add_query_arg( array(
						'live_mode' => (int) ! simpay_is_test_mode(),
						'state' => str_pad( wp_rand( wp_rand(), PHP_INT_MAX ), 100, wp_rand(), STR_PAD_BOTH ),
						'customer_site_url' => admin_url( 'admin.php?page=simpay_settings&tab=keys' ),
					), 'https://wpsimplepay.com/?wpsp_gateway_connect_init=stripe_connect' );

					$show_connect_button = false;

					$mode = simpay_is_test_mode() ? __( 'test', 'simple-pay' ) : __( 'live', 'simple-pay' );

					if( simpay_is_test_mode() && ! simpay_check_keys_exist() ) {

						$show_connect_button = true;

					} elseif( ! simpay_check_keys_exist() ) {

						$show_connect_button = true;

					}

					if( $show_connect_button ) {

						$html = '<a href="'. esc_url( $stripe_connect_url ) .'" class="wpsp-stripe-connect"><span>' . __( 'Connect with Stripe', 'simple-pay' ) . '</span></a>';
						$html .= '<p>' . sprintf( __( 'Have questions about connecting with Stripe? See the <a href="%s" target="_blank" rel="noopener noreferrer">documentation</a>.', 'simple-pay' ), simpay_get_url( 'docs' ) . 'articles/stripe-setup/' ) . '</p>';
						
					} else {

						$html = sprintf( __( 'Your Stripe account is connected in %s mode. If you need to reconnect in %s mode, <a href="%s">click here</a>.', 'simple-pay' ), '<strong>' . $mode . '</strong>', $mode, esc_url( $stripe_connect_url ) );
					
					}

					$html .= '<p id="wpsp-api-keys-row-reveal">' . __( '<a href="#">Click here</a> to manage your API keys manually.', 'simple-pay' ) . '</p>';
					$html .= '<p id="wpsp-api-keys-row-hide">' . __( '<a href="#">Click here</a> to hide your API keys.', 'simple-pay' ) . '</p>';

					$fields[ $section ] = array(
						'test_mode' => array(
							'title'       => esc_html__( 'Connection Status', 'simple-pay' ),
							'type'        => 'custom-html',
							'html'        => $html,
							'name'        => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][test_mode]',
							'id'          => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-test-mode',
						),
					);
				} elseif  ( 'mode' == $section ) {
					$fields[ $section ] = array(
						'test_mode' => array(
							'title'       => esc_html__( 'Test Mode', 'simple-pay' ),
							'default'     => 'enabled',
							'type'        => 'radio',
							'options'     => array(
								'enabled'  => esc_html__( 'Enabled', 'simple-pay' ),
								'disabled' => esc_html__( 'Disabled', 'simple-pay' ),
							),
							'value'       => $this->get_option_value( $section, 'test_mode' ),						
							'name'        => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][test_mode]',
							'id'          => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-test-mode',
							'inline'      => 'inline',
							'description' => sprintf( wp_kses( __( 'While in test mode no live payments are processed. Make sure Test mode is enabled in your <a href="%1$s" target="_blank">Stripe dashboard</a> to view your test transactions.', 'simple-pay' ), array(
									'a' => array( 'href' => array(), 'target' => array() ),
								) ), esc_url( 'https://dashboard.stripe.com/' ) )
						),
					);
				} elseif ( 'test_keys' == $section ) {

					$fields[ $section ] = array(
						'publishable_key' => array(
							'title'   => esc_html__( 'Test Publishable Key', 'simple-pay' ),
							'type'    => 'standard',
							'subtype' => 'text',
							'name'    => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][publishable_key]',
							'id'      => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-publishable-key',
							'value'   => trim( $this->get_option_value( $section, 'publishable_key' ) ),
							'class'   => array(
								'regular-text',
							),
							'description' => esc_html__( 'Starts with', 'simple-pay' ) . ' <code>pk_test</code>',
						),
						'secret_key'      => array(
							'title'   => esc_html__( 'Test Secret Key', 'simple-pay' ),
							'type'    => 'standard',
							'subtype' => 'text',
							'name'    => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][secret_key]',
							'id'      => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-secret-key',
							'value'   => trim( $this->get_option_value( $section, 'secret_key' ) ),
							'class'   => array(
								'regular-text',
							),
							'description' => esc_html__( 'Starts with', 'simple-pay' ) . ' <code>sk_test</code>',
						),
					);
				} elseif ( 'live_keys' == $section ) {

					$fields[ $section ] = array(
						'publishable_key' => array(
							'title'   => esc_html__( 'Live Publishable Key', 'simple-pay' ),
							'type'    => 'standard',
							'subtype' => 'text',
							'name'    => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][publishable_key]',
							'id'      => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-publishable-key',
							'value'   => trim( $this->get_option_value( $section, 'publishable_key' ) ),
							'class'   => array(
								'regular-text',
							),
							'description' => esc_html__( 'Starts with', 'simple-pay' ) . ' <code>pk_live</code>',
						),
						'secret_key'      => array(
							'title'   => esc_html__( 'Live Secret Key', 'simple-pay' ),
							'type'    => 'standard',
							'subtype' => 'text',
							'name'    => 'simpay_' . $this->option_group . '_' . $this->id . '[' . $section . '][secret_key]',
							'id'      => 'simpay-' . $this->option_group . '-' . $this->id . '-' . $section . '-secret-key',
							'value'   => trim( $this->get_option_value( $section, 'secret_key' ) ),
							'class'   => array(
								'regular-text',
							),
							'description' => esc_html__( 'Starts with', 'simple-pay' ) . ' <code>sk_live</code>',
						),
					);
				}

			}
		}

		return apply_filters( 'simpay_add_' . $this->option_group . '_' . $this->id . '_fields', $fields );
	}

}
