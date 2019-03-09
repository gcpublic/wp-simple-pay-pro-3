<?php

function change_payment_description() {

	global $post;

	// Check if subscriptions are enabled
	$subscription_type = simpay_get_saved_meta( $post->ID, '_subscription_type', 'disabled' );

	// Use these for checking radio/dropdown amount fields
	$custom_fields        = simpay_get_saved_meta( $post->ID, '_custom_fields' );
	$dropdown_amount      = false;
	$radio_amount         = false;
	$amount_type_disabled = false;
	$html                 = '';

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

	if ( 'disabled' !== $subscription_type || $dropdown_amount || $radio_amount ) {
		$attr['disabled']     = 'disabled';
		$amount_type_disabled = true;
		add_filter( 'simpay_amount_options_classes', 'disable_one_type_payment_options' );
	}

	// Messaging to display if subscriptions capabilities detected (i.e. biz license or higher).

	if ( simpay_subscriptions_enabled() ) {

		$html .= sprintf( wp_kses( __( '<a href="%1$s" class="%2$s" data-show-tab="%3$s">See Subscription Options</a> to set a recurring amount.', 'simple-pay' ), array(
			'a' => array(
				'href'          => array(),
				'class'         => array(),
				'data-show-tab' => array(),
			),
		) ), '#', 'simpay-tab-link', 'simpay-subscription_options' );

		$html .= '<br />';

		// If subscriptions are being used, one-time amounts don't apply, so show warning.

		if ( $amount_type_disabled ) {
			echo '<span class="simpay-important">';

			if ( 'disabled' !== $subscription_type ) {
				$html .= esc_html__( 'Subscriptions are currently enabled.', 'simple-pay' );
			} elseif ( $dropdown_amount ) {
				$html .= esc_html__( 'A Dropdown Select custom field using amount is enabled.', 'simple-pay' );
			} elseif ( $radio_amount ) {
				$html .= esc_html__( 'A Radio Button Select custom field using amount is enabled.', 'simple-pay' );
			}

			$html .= '</span><br />';
		}
	} else {

		// Messaging to display if subscriptions capabilities are not allowed (i.e. personal license).

		$html .= printf( wp_kses( __( '<a href="%s" target="_blank">Upgrade your license</a> to connect Stripe subscriptions to your payment forms.', 'simple-pay' ), array(
			'a' => array(
				'href'   => array(),
				'target' => array(),
			),
		) ), simpay_my_account_url( 'form-settings' ) );
	}

	return $html;
}

add_filter( 'simpay_amount_type_description', 'change_payment_description' );

function add_custom_amount_option( $arr ) {
	return array_merge( $arr, array(
		'one_time_custom' => esc_html__( 'One-Time Custom Amount', 'simple-pay' ),
	) );
}

add_filter( 'simpay_payment_options', 'add_custom_amount_option' );

function disable_one_type_payment_options( $classes ) {
	$classes .= ' simpay-panel-hidden';

	return $classes;
}

function add_custom_amount_sub_settings() {

	global $post;

	$position             = simpay_get_currency_position();
	$amount_type          = '';
	$amount_type_disabled = '';
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

				// Check saved currency and set default to 100 or 1 accordingly and set steps and class.
				$minimum_amount = simpay_get_saved_meta( $post->ID, '_minimum_amount', simpay_global_minimum_amount() );

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

add_action( 'simpay_after_amount_options', 'add_custom_amount_sub_settings' );
