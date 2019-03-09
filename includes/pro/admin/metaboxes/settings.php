<?php

namespace SimplePay\Pro\Admin\Metaboxes;

use SimplePay\Core\Payments\Stripe_API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {


	public function __construct() {

		$this->set_admin_tabs();


		add_action( 'simpay_amount_options', array( $this, 'add_amount_options_radio' ) );

		add_action( 'simpay_admin_after_amount_options', array( $this, 'add_custom_amount_options' ), 0 );

		add_filter( 'simpay_amount_options_classes', array( $this, 'add_amount_hidden_class' ) );
	}

	public function add_amount_hidden_class( $classes ) {

		global $post;

		$amount_type = simpay_get_saved_meta( $post->ID, '_amount_type', 'one_time_set' );


		$check = ( ( 'one_time_set' !== $amount_type ) || false !== $this->amount_type_disabled() ) ? 'simpay-panel-hidden' : '';

		return $classes . ' ' . $check;
	}

	private function amount_type_disabled() {

		global $post;

		// Check if subscriptions are enabled
		$subscription_type = simpay_get_saved_meta( $post->ID, '_subscription_type', 'disabled' );

		// Use these for checking radio/dropdown amount fields
		$custom_fields        = simpay_get_saved_meta( $post->ID, '_custom_fields' );
		$dropdown_amount      = false;
		$radio_amount         = false;
		$amount_type_disabled = false;

		if ( ! empty( $custom_fields ) && is_array( $custom_fields ) ) {
			// Check for dropdown amount field
			if ( array_key_exists( 'dropdown', $custom_fields ) ) {
				if ( ! empty( $custom_fields['dropdown'] ) && is_array( $custom_fields['dropdown'] ) ) {
					foreach ( $custom_fields['dropdown'] as $k => $v ) {
						if ( is_array( $v ) && array_key_exists( 'amount_quantity', $v ) ) {
							if ( isset( $v['amount_quantity'] ) && 'amount' === $v['amount_quantity'] ) {
								$dropdown_amount = true;
							}
							break;
						}
					}
				}
			}
			// Check for radio amount field
			if ( array_key_exists( 'radio', $custom_fields ) ) {
				if ( ! empty( $custom_fields['radio'] ) && is_array( $custom_fields['radio'] ) ) {
					foreach ( $custom_fields['radio'] as $k => $v ) {
						if ( is_array( $v ) && array_key_exists( 'amount_quantity', $v ) ) {
							if ( isset( $v['amount_quantity'] ) && 'amount' === $v['amount_quantity'] ) {
								$radio_amount = true;
							}
							break;
						}
					}
				}
			}
		}
		if ( 'disabled' !== $subscription_type ) {
			return 'subscription';
		} else if ( $dropdown_amount ) {
			return 'dropdown_amount';
		} else if ( $radio_amount ) {
			return 'radio_amount';
		}

		return false;
	}

	public function add_custom_amount_options() {

		global $post;

		$position = simpay_get_currency_position();

		$amount_type = simpay_get_saved_meta( $post->ID, '_amount_type', 'one_time_set' );

		$amount_type_disabled = false !== $this->amount_type_disabled() ? true : false;

		?>
		<table class="<?php echo ( ( 'one_time_custom' !== $amount_type ) || $amount_type_disabled ) ? 'simpay-panel-hidden' : ''; ?> toggle-_amount_type-one_time_custom">
			<tbody>
			<tr class="simpay-panel-field">
				<th>
					<label for="_minimum_amount"><?php esc_html_e( 'Minimum Custom Amount', 'simple-pay' ); ?></label>
				</th>
				<td>
					<?php if ( 'left' === $position || 'left_space' === $position ) { ?>
						<span class="simpay-currency-symbol simpay-currency-symbol-left"><?php echo simpay_get_saved_currency_symbol(); ?></span>
					<?php } ?>

					<?php

					// Classes
					$classes = array(
						'simpay-field-tiny',
						'simpay-amount-input',
						'simpay-minimum-amount-required',
					);

					// Check saved currency and set default to 100 or 1 accordingly and set steps and class
					$minimum_amount = simpay_get_saved_meta( $post->ID, '_minimum_amount', simpay_global_minimum_amount());

					simpay_print_field( array(
						'type'        => 'standard',
						'subtype'     => 'tel',
						'name'        => '_minimum_amount',
						'id'          => '_minimum_amount',
						'value'       => $minimum_amount,
						'class'       => $classes,
						'placeholder' => simpay_format_currency( simpay_global_minimum_amount(), simpay_get_setting( 'currency' ), false ),
					) );

					?>

					<?php if ( 'right' === $position || 'right_space' === $position ) { ?>
						<span class="simpay-currency-symbol simpay-currency-symbol-right"><?php echo simpay_get_saved_currency_symbol(); ?></span>
					<?php } ?>
				</td>
			</tr>

			<tr class="simpay-panel-field">
				<th>
					<label for="_custom_amount_default"><?php esc_html_e( 'Default Custom Amount', 'simple-pay' ); ?></label>
				</th>
				<td>
					<?php if ( 'left' === $position || 'left_space' === $position ) { ?>
						<span class="simpay-currency-symbol simpay-currency-symbol-left"><?php echo simpay_get_saved_currency_symbol(); ?></span>
					<?php } ?>

					<?php

					// Classes
					$classes = array(
						'simpay-field-tiny',
						'simpay-amount-input',
						'simpay-allow-blank-amount',
						'simpay-minimum-amount-required',
					);

					// Set the default amount
					$custom_amount_default = simpay_get_saved_meta( $post->ID, '_custom_amount_default', '' );
					simpay_print_field( array(
						'type'       => 'standard',
						'subtype'    => 'tel',
						'name'       => '_custom_amount_default',
						'id'         => '_custom_amount_default',
						'value'      => $custom_amount_default,
						'class'      => $classes,

						// Description set below
					) );
					?>

					<?php if ( 'right' === $position || 'right_space' === $position ) { ?>
						<span class="simpay-currency-symbol simpay-currency-symbol-right"><?php echo simpay_get_saved_currency_symbol(); ?></span>
					<?php } ?>

					<p class="description">
						<?php esc_html_e( 'The custom amount field will load with this amount set by default.', 'simple-pay' ); ?>
					</p>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	public function add_amount_options_radio() {

		global $post;

		?>

		<tr class="simpay-panel-field">
			<th>
				<label for="_amount"><?php esc_html_e( 'Amount Type', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php
				$amount_type = simpay_get_saved_meta( $post->ID, '_amount_type', 'one_time_set' );
				$attr        = array();

				$amount_type_disabled = $this->amount_type_disabled();

				if ( false !== $amount_type_disabled ) {
					$attr['disabled'] = 'disabled';
				}

				simpay_print_field( array(
					'type'       => 'radio',
					'name'       => '_amount_type',
					'id'         => '_amount_type',
					'value'      => $amount_type,
					'class'      => array(
						'simpay-field-text',
						'simpay-multi-toggle',
					),
					'options'    => array(
						'one_time_set'    => esc_html__( 'One-Time Set Amount', 'simple-pay' ),
						'one_time_custom' => esc_html__( 'One-Time Custom Amount', 'simple-pay' ),
					),
					'inline'     => 'inline',
					'attributes' => $attr,
					// Description for this field set below so we can use wp_kses() without clashing with the wp_kses() already being applied to simpay_print_field()
				) );
				?>

				<p class="description">
					<?php
					// Messaging to display if subscriptions capabilities detected (i.e. biz license or higher).
					if ( simpay_subscriptions_enabled() ) {
						printf( wp_kses( __( '<a href="%1$s" class="%2$s" data-show-tab="%3$s">See Subscription Options</a> to set a recurring amount.', 'simple-pay' ), array(
							'a' => array(
								'href'          => array(),
								'class'         => array(),
								'data-show-tab' => array(),
							),
						) ), '#', 'simpay-tab-link', 'simpay-subscription_options' );
						echo '<br />';
						// If subscriptions are being used, one-time amounts don't apply, so show warning.
						if ( false !== $amount_type_disabled ) {
							echo '<span class="simpay-important">';
							if ( 'subscription' === $amount_type_disabled ) {
								esc_html_e( 'Subscriptions are currently enabled.', 'simple-pay' );
							} elseif ( 'dropdown_amount' === $amount_type_disabled ) {
								esc_html_e( 'A Dropdown Select custom field using amount is enabled.', 'simple-pay' );
							} elseif ( 'radio_amount' === $amount_type_disabled ) {
								esc_html_e( 'A Radio Button Select custom field using amount is enabled.', 'simple-pay' );
							}
							echo '</span><br />';
						}
					} else {
						// Messaging to display if subscriptions capabilities are not allowed (i.e. personal license).
						printf( wp_kses( __( '<a href="%s" target="_blank">Upgrade your license</a> to connect Stripe subscriptions to your payment forms.', 'simple-pay' ), array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
							),
						) ), simpay_my_account_url( 'form-settings' ) );
					}
					?>
				</p>
			</td>
		</tr>
		<?php
	}

	public function set_admin_tabs() {

		add_filter( 'simpay_form_display_template', function () {
			return SIMPLE_PAY_INC . 'pro/admin/metaboxes/views/tabs/tab-custom-form-fields.php';
		} );

		add_filter( 'simpay_subscription_options_template', function ( $file ) {
			if ( simpay_subscriptions_enabled() ) {
				return SIMPLE_PAY_INC . 'pro/admin/metaboxes/views/tabs/tab-subscription-options.php';
			} else {
				return $file;
			}
		} );

		add_filter( 'simpay_form_options_template', function () {
			return SIMPLE_PAY_INC . 'pro/admin/metaboxes/views/tabs/tab-form-display-options.php';
		} );
	}


}
