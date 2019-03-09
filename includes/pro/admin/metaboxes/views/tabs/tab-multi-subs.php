<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<tr class="simpay-multi-sub" data-field-key="<?php echo esc_attr( $plan_counter ); ?>" rel="<?php echo esc_attr( $plan_order ); ?>">
	<td class="sort-handle"><span class="dashicons dashicons-menu"></span></td>
	<td class="simpay-panel-hidden">
		<input type="hidden" name="<?php echo '_multi_plan[' . $plan_counter . '][order]'; ?>" class="plan-order"
		       value="<?php echo esc_attr( $plan_order ); ?>" />
	</td>
	<td class="simpay-multi-plan-select">
		<?php

		simpay_print_field( array(
			'type'       => 'select',
			'name'       => '_multi_plan[' . $plan_counter . '][select_plan]',
			'id'         => 'simpay-subscription-multi-plan-select-' . $plan_counter,
			'value'      => isset( $plans[ $plan_counter ]['select_plan'] ) ? $plans[ $plan_counter ]['select_plan'] : '',
			'options'    => array( 'empty' => '-- ' . esc_html__( 'Select', 'simple-pay' ) . ' --' ) + simpay_get_plan_list(),
			'attributes' => array(
				'data-field-key' => $plan_counter,
			),
			'class'      => array(
				'simpay-chosen-search',
			),
		) );

		?>
	</td>
	<td class="simpay-multi-plan-custom-label">
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_multi_plan[' . $plan_counter . '][custom_label]',
			'id'         => 'simpay-subscription-multi-plan-custom-label-' . $plan_counter,
			'value'      => isset( $plans[ $plan_counter ]['custom_label'] ) ? $plans[ $plan_counter ]['custom_label'] : '',
			'class'      => array(
				'simpay-label-input',
			),
			'attributes' => array(
				'data-field-key' => $plan_counter,
			),
		) );
		?>
	</td>
	<td class="simpay-multi-plan-setup-fee">

		<?php $position = simpay_get_currency_position(); ?>

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

		$attr = array(
			'data-field-key' => $plan_counter,
		);

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'tel',
			'name'       => '_multi_plan[' . $plan_counter . '][setup_fee]',
			'id'         => 'simpay-subscription-multi-plan-setup-fee-' . $plan_counter,
			'value'      => isset( $plans[ $plan_counter ]['setup_fee'] ) ? $plans[ $plan_counter ]['setup_fee'] : '',
			'attributes' => $attr,
			'class'      => $classes,
		) );
		?>

		<?php if ( 'right' === $position || 'right_space' === $position ) { ?>
			<span class="simpay-currency-symbol simpay-currency-symbol-right"><?php echo simpay_get_saved_currency_symbol(); ?></span>
		<?php } ?>
	</td>

	<td class="simpay-multi-plan-max-charges">
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_multi_plan[' . $plan_counter . '][max_charges]',
			'id'         => 'simpay-subscription-multi-plan-max-charges-' . $plan_counter,
			'value'      => isset( $plans[ $plan_counter ]['max_charges'] ) && ! empty( $plans[ $plan_counter ]['max_charges'] ) ? $plans[ $plan_counter ]['max_charges'] : 0,
			'class'      => array(
				'small-text',
			),
			'attributes' => array(
				'data-field-key' => $plan_counter,
				'min'            => 0,
				'step'           => 1,
			),
		) );
		?>
	</td>

	<td class="simpay-multi-plan-default">
		<?php

		simpay_print_field( array(
			'type'       => 'radio',
			'name'       => '_multi_plan[1][default]',
			'id'         => 'simpay-subscription-multi-plan-default-' . $plan_counter,
			'value'      => $default_plan,
			'attributes' => array(
				'data-field-key' => $plan_counter,
				'data-plan-id'   => isset( $plans[ $plan_counter ]['select_plan'] ) ? $plans[ $plan_counter ]['select_plan'] : '',
			),
			'options'    => array(
				'yes' => '',
			),
		) );

		?>
	</td>
	<td class="simpay-subscription-multi-plan-remove">
		<a href="#" class="simpay-remove-plan simpay-remove-icon" aria-label="<?php esc_attr_e( 'Remove plan', 'simple-pay' ); ?>" title="<?php esc_attr_e( 'Remove plan', 'simple-pay' ); ?>"></a>
	</td>
</tr>
