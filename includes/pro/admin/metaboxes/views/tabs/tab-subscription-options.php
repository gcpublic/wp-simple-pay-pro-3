<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	<table>
		<thead>
		<tr>
			<th colspan="2"><?php esc_html_e( 'Subscription Options', 'simple-pay' ); ?></th>
		</tr>
		</thead>
		<tbody class="simpay-panel-section">
		<tr class="simpay-panel-field">
			<th>
				<label for="_subscription_type"><?php esc_html_e( 'Subscription Plans', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				$subscription_type = simpay_get_saved_meta( $post->ID, '_subscription_type', 'disabled' );

				simpay_print_field( array(
					'type'       => 'radio',
					'name'       => '_subscription_type',
					'id'         => '_subscription_type',
					'value'      => $subscription_type,
					'class'      => array(
						'simpay-field-text',
						'simpay-multi-toggle',
						'simpay-disable-amount',
					),
					'options'    => array(
						'single'   => esc_html__( 'Set Single Plan', 'simple-pay' ),
						'user'     => esc_html__( 'User Selects Plan', 'simple-pay' ),
						'disabled' => esc_html__( 'Disabled', 'simple-pay' ),
					),
					'inline'     => 'inline',
					'attributes' => array(
						'data-disable-amount-check' => 'disabled',
					),
				) );
				?>
			</td>
		</tr>

		<?php

		$test_mode = simpay_is_test_mode();

		if ( $test_mode && simpay_check_keys_exist() ) {

			?>

			<tr class="simpay-panel-field toggle-_subscription_type-user toggle-_subscription_type-single <?php echo 'disabled' === $subscription_type ? 'simpay-panel-hidden' : ''; ?>">
				<td colspan="2">
					<p class="description">
						<?php
						printf( wp_kses( __( 'While in <strong>Test Mode</strong> you may only select plans from your <a href="%1$s" target="_blank">Stripe Test Plans</a>. Before switching to Live Mode, make sure the same plans (with the exact same IDs) are created in your <a href="%2$s" target="_blank">Stripe Live Plans</a>.', 'simple-pay' ), array(
							'a'      => array(
								'href'   => array(),
								'target' => array(),
							),
							'strong' => array(),
						) ), 'https://dashboard.stripe.com/test/plans', 'https://dashboard.stripe.com/plans' );
						?>
					</p>
				</td>
			</tr>

		<?php } ?>

		<tr class="simpay-panel-field <?php echo ( 'single' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-single">
			<th>
				<label for="_single_plan"><?php esc_html_e( 'Select Plan', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				if ( simpay_check_keys_exist() ) {

					// Attributes
					$attr = array();

					$custom_amount = simpay_get_saved_meta( $post->ID, '_subscription_custom_amount' );

					if ( 'enabled' === $custom_amount ) {
						$attr['disabled'] = 'disabled';
					}

					simpay_print_field( array(
						'type'       => 'select',
						'name'       => '_single_plan',
						'id'         => '_single_plan',
						'value'      => simpay_get_saved_meta( $post->ID, '_single_plan', 'empty' ),
						'class'      => array(
							'simpay-field-text',
							'simpay-chosen-search',
						),
						'options'    => array( 'empty' => '-- ' . esc_html__( 'Select', 'simple-pay' ) . ' --' ) + simpay_get_plan_list(),
						'attributes' => $attr,
					) );
				} else {

					echo '<p class="simpay-errors">' . sprintf( wp_kses( __( 'You need to enter your Stripe API Keys for your plan list to be pulled automatically. <a href="%s">Enter your keys here</a>', 'simple-pay' ), array(
							'a' => array(
								'href' => array(),
							),
						) ), admin_url( 'admin.php?page=simpay_settings&tab=keys' ) ) . '</p>';
				}
				?>
			</td>
		</tr>

		<tr class="simpay-panel-field <?php echo ( 'user' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-user">
			<th>
				<label for="_multi_plan_display"><?php esc_html_e( 'Display Style', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				simpay_print_field( array(
					'type'    => 'radio',
					'name'    => '_multi_plan_display',
					'id'      => '_multi_plan_display',
					'value'   => simpay_get_saved_meta( $post->ID, '_multi_plan_display', 'radio' ),
					'class'   => array(
						'simpay-field-text',
					),
					'options' => array(
						'radio'    => esc_html__( 'Radio', 'simple-pay' ),
						'dropdown' => esc_html__( 'Dropdown', 'simple-pay' ),
					),
					'inline'  => 'inline',
				) );
				?>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="simpay-inner-table <?php echo ( 'user' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-user simpay-multi-subs-wrap" id="simpay-multi-plans">
		<tr class="simpay-panel-field">
			<td colspan="2">

				<?php if ( simpay_check_keys_exist() ) { ?>

					<table class="simpay-multi-subscriptions">
						<thead>
						<tr>
							<th></th>
							<th class="simpay-multi-plan-select"><?php esc_html_e( 'Select Plan', 'simple-pay' ); ?></th>
							<th>
								<?php

								esc_html_e( 'Custom Label', 'simple-pay' );

								?>
							</th>
							<th>
								<?php

								esc_html_e( 'Setup Fee', 'simple-pay' );

								?>
							</th>
							<th>
								<?php

								esc_html_e( 'Max Charges', 'simple-pay' );

								?>
							</th>
							<th>
								<?php

								esc_html_e( 'Default', 'simple-pay' );

								?>
							</th>
							<th>&nbsp;</th>
						</tr>
						</thead>
						<tbody>
						<?php

						$default = simpay_get_saved_meta( $post->ID, '_multi_plan_default_value' );

						simpay_print_field( array(
							'type'    => 'standard',
							'subtype' => 'hidden',
							'name'    => '_multi_plan_default_value',
							'id'      => 'simpay-multi-plan-default-value',
							'value'   => $default,
						) );

						$plans = simpay_get_saved_meta( $post->ID, '_multi_plan', array() );

						$num_plans = is_array( $plans ) ? count( $plans ) : 0;


						if ( ! empty( $num_plans ) ) {

							for ( $i = 0; $i < $num_plans; $i++ ) {

								$plan_counter = $i + 1;

								$plan_order = intval( isset( $plans[ $plan_counter ]['order'] ) ? $plans[ $plan_counter ]['order'] : 1 );

								$default_plan = isset( $plans[ $plan_counter ]['select_plan'] ) && $plans[ $plan_counter ]['select_plan'] === $default ? 'yes' : '';

								include( 'tab-multi-subs.php' );
							}

						} else {
							$plan_counter = 1;

							$plan_order = intval( isset( $plans[ $plan_counter ]['order'] ) ? $plans[ $plan_counter ]['order'] : 1 );

							$default_plan = isset( $plans[ $plan_counter ]['select_plan'] ) ? $plans[ $plan_counter ]['select_plan'] : 'yes';

							include( 'tab-multi-subs.php' );
						}
						?>
						</tbody>
					</table>

					<?php $add_plan_nonce = wp_create_nonce( 'simpay_add_plan_nonce' ); ?>
					<input type="hidden" id="simpay_add_plan_nonce" value="<?php echo esc_attr( $add_plan_nonce ); ?>" />
					<button class="simpay-add-plan button button-secondary"><?php esc_html_e( 'Add Plan', 'simple-pay' ); ?></button>

				<?php } else {
					echo '<p class="simpay-errors">' . sprintf( wp_kses( __( 'You need to enter your Stripe API Keys before you can start adding plans. <a href="%s">Enter your keys here</a>', 'simple-pay' ), array(
							'a' => array(
								'href' => array(),
							),
						) ), admin_url( 'admin.php?page=simpay_settings&tab=keys' ) ) . '</p>';
				} ?>
			</td>
		</tr>
	</table>

	<table class="simpay-inner-table <?php echo ( 'disabled' === $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-user toggle-_subscription_type-single">
		<tbody>

		<tr class="simpay-panel-field">
			<th>
				<label for="_subscription_custom_amount"><?php esc_html_e( 'Custom Amount', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				$multi_plan_custom_amount = simpay_get_saved_meta( $post->ID, '_subscription_custom_amount', 'disabled' );

				simpay_print_field( array(
					'type'    => 'radio',
					'name'    => '_subscription_custom_amount',
					'id'      => '_subscription_custom_amount',
					'value'   => $multi_plan_custom_amount,
					'class'   => array(
						'simpay-field-text',
						'simpay-multi-toggle',
					),
					'options' => array(
						'enabled'  => esc_html__( 'Enabled', 'simple-pay' ),
						'disabled' => esc_html__( 'Disabled', 'simple-pay' ),
					),
					'inline'  => 'inline',
				) );

				?>
			</td>
		</tr>
		</tbody>
	</table>

	<table class="simpay-inner-table toggle-_subscription_custom_amount-enabled <?php echo ( 'enabled' !== $multi_plan_custom_amount ) ? 'simpay-panel-hidden' : ''; ?>">
		<tbody>
		<tr class="simpay-panel-field toggle-_subscription_type-single toggle-_subscription_type-user <?php echo ( 'single' !== $subscription_type && 'user' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?>">
			<th>
				<label for="_multi_plan_minimum_amount"><?php esc_html_e( 'Minimum Amount', 'simple-pay' ); ?></label>
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
				$multi_plan_minimum_amount = simpay_get_saved_meta( $post->ID, '_multi_plan_minimum_amount', simpay_format_currency( simpay_global_minimum_amount(), simpay_get_setting( 'currency' ), false ) );

				simpay_print_field( array(
					'type'        => 'standard',
					'subtype'     => 'tel',
					'name'        => '_multi_plan_minimum_amount',
					'id'          => '_multi_plan_minimum_amount',
					'value'       => $multi_plan_minimum_amount,
					'class'       => $classes,
					'placeholder' => simpay_format_currency( simpay_global_minimum_amount(), simpay_get_setting( 'currency' ), false ),
				) );

				?>

				<?php if ( 'right' === $position || 'right_space' === $position ) { ?>
					<span class="simpay-currency-symbol simpay-currency-symbol-right"><?php echo simpay_get_saved_currency_symbol(); ?></span>
				<?php } ?>
			</td>
		</tr>

		<tr class="simpay-panel-field toggle-_subscription_type-single  <?php echo ( 'single' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?>">
			<th>
				<label for="_multi_plan_default_amount"><?php esc_html_e( 'Default Amount', 'simple-pay' ); ?></label>
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

				// Check saved currency and set default to 100 or 1 accordingly and set steps and class.
				$multi_plan_default_amount = simpay_get_saved_meta( $post->ID, '_multi_plan_default_amount', '' );

				simpay_print_field( array(
					'type'       => 'standard',
					'subtype'    => 'tel',
					'name'       => '_multi_plan_default_amount',
					'id'         => '_multi_plan_default_amount',
					'value'      => $multi_plan_default_amount,
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

		<tr class="simpay-panel-field toggle-_subscription_custom_amount-enabled">
			<th>
				<label for="_plan_interval"><?php esc_html_e( 'Interval/Frequency', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				simpay_print_field( array(
					'type'       => 'standard',
					'subtype'    => 'number',
					'name'       => '_plan_interval',
					'id'         => '_plan_interval',
					'value'      => simpay_get_saved_meta( $post->ID, '_plan_interval', 1 ),
					'class'      => array(
						'small-text',
					),
					'attributes' => array(
						'min' => 1,
					),
				) );

				simpay_print_field( array(
					'type'    => 'select',
					'name'    => '_plan_frequency',
					'id'      => '_plan_frequency',
					'value'   => simpay_get_saved_meta( $post->ID, '_plan_frequency', 'empty' ),
					'class'   => array(
						'simpay-plan-frequency',
					),
					'options' => array(
						'month' => esc_html__( 'Month(s)', 'simple-pay' ),
						'week'  => esc_html__( 'Week(s)', 'simple-pay' ),
						'day'   => esc_html__( 'Day(s)', 'simple-pay' ),
						'year'  => esc_html__( 'Year(s)', 'simple-pay' ),
					),
				) );

				?>
			</td>
		</tr>

		<tr class="simpay-panel-field <?php echo ( 'user' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-user">
			<th>
				<label for="_custom_plan_label"><?php esc_html_e( 'Custom Amount Plan Label', 'simple-pay' ); ?></label>
			</th>
			<td>
				<?php

				simpay_print_field( array(
					'type'        => 'standard',
					'subtype'     => 'text',
					'name'        => '_custom_plan_label',
					'id'          => '_custom_plan_label',
					'value'       => simpay_get_saved_meta( $post->ID, '_custom_plan_label' ),
					'class'       => array(
						'simpay-field-text',
					),
					'placeholder' => esc_attr__( 'Other amount', 'simple-pay' ),
				) );
				?>
			</td>
		</tr>

		</tbody>
	</table>

	<table class="simpay-inner-table <?php echo ( 'single' !== $subscription_type && 'user' !== $subscription_type ) ? 'simpay-panel-hidden' : ''; ?> toggle-_subscription_type-single toggle-_subscription_type-user">
		<tbody>
		<tr class="simpay-panel-field">
			<th>
				<label for="_setup_fee"><?php esc_html_e( 'Plan Setup Fee', 'simple-pay' ); ?></label>
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

				$setup_fee = simpay_get_saved_meta( $post->ID, '_setup_fee', '0' );

				simpay_print_field( array(
					'type'    => 'standard',
					'subtype' => 'tel',
					'name'    => '_setup_fee',
					'id'      => '_setup_fee',
					'value'   => $setup_fee,
					'class'   => $classes,
					// description below
				) );
				?>

				<?php if ( 'right' === $position || 'right_space' === $position ) { ?>
					<span class="simpay-currency-symbol simpay-currency-symbol-right"><?php echo simpay_get_saved_currency_symbol(); ?></span>
				<?php } ?>

				<p class="description">
					<?php esc_html_e( 'An optional amount to add to the first payment only.', 'simple-pay' ); ?>
				</p>
			</td>
		</tr>

		<tr class="simpay-panel-field">
			<th>
				<label for="_max_charges"><?php esc_html_e( 'Max Charges', 'simple-pay' ); ?></label>
			</th>
			<td>

				<?php

				// Classes
				$classes = array(
					'small-text',
				);

				$max_charges = simpay_get_saved_meta( $post->ID, '_max_charges', '0' );

				simpay_print_field( array(
					'type'        => 'standard',
					'subtype'     => 'number',
					'name'        => '_max_charges',
					'id'          => '_max_charges',
					'value'       => absint( $max_charges ),
					'class'       => $classes,
					'attributes'  => array(
						'min'  => 0,
						'step' => 1,
					),
					'description' => esc_html__( 'The number of times this subscription should be charged. It will automatically cancel after it reaches the max number. Leave blank for indefinite.', 'simple-pay' ) . '<br>' . simpay_webhook_help_text(),
				) );
				?>
			</td>
		</tr>
		</tbody>
	</table>

<?php echo simpay_docs_link( __( 'Help docs for Subscription Options', 'simple-pay' ), 'subscription-options', 'form-settings' );
