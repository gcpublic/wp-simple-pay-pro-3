<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-recurring-amount-toggle-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[recurring_amount_toggle][' . $counter . '][label]',
			'id'          => 'simpay-recurring-amount-toggle-label-' . $counter,
			'value'       => isset( $field['label'] ) ? $field['label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
				'placeholder'    => esc_html__( 'Make this a recurring amount', 'simple-pay' ),
			),
			'description' => esc_html__( 'The label that will appear on the checkbox.', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-recurring-amount-toggle-plan-interval-' . $counter; ?>"><?php esc_html_e( 'Interval/Frequency', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_simpay_custom_field[recurring_amount_toggle][' . $counter . '][plan_interval]',
			'id'         => 'simpay-recurring-amount-toggle-plan-interval-' . $counter,
			'value'      => isset ( $field['plan_interval'] ) ? absint( $field['plan_interval'] ) : 1,
			'class'      => array(
				'small-text',
			),
			'attributes' => array(
				'min' => 1,
			),
		) );

		simpay_print_field( array(
			'type'    => 'select',
			'name'    => '_simpay_custom_field[recurring_amount_toggle][' . $counter . '][plan_frequency]',
			'id'      => 'simpay-recurring-amount-toggle-plan-frequency-' . $counter,
			'value'   => isset( $field['plan_frequency'] ) ? $field['plan_frequency'] : 'month',
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

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-recurring-amount-toggle-max-charges-' . $counter; ?>"><?php esc_html_e( 'Max Charges', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'number',
			'name'        => '_simpay_custom_field[recurring_amount_toggle][' . $counter . '][max_charges]',
			'id'          => 'simpay-recurring-amount-toggle-max-charges-' . $counter,
			'value'       => isset( $field['max_charges'] ) ? $field['max_charges'] : '',
			'class'       => array(
				'small-text',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
				'min'            => 0,
				'step'           => 1,
			),
			'description' => esc_html__( 'The number of times this subscription should be charged. It will automatically cancel after it reaches the max number. Leave blank for indefinite.', 'simple-pay' ) . '<br>' . simpay_webhook_help_text(),
		) );

		?>
	</td>
</tr>

<!-- Hidden ID Field -->
<tr class="simpay-panel-field">
	<th>
		<?php esc_html_e( 'Field ID:', 'simple-pay' ); ?>
	</th>
	<td>
		<?php
		echo absint( $uid );

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'hidden',
			'name'       => '_simpay_custom_field[recurring_amount_toggle][' . $counter . '][id]',
			'id'         => 'simpay-recurring-amount-toggle-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
