<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-total-amount-label-' . $counter; ?>"><?php esc_html_e( 'Total Amount Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[total_amount][' . $counter . '][label]',
			'id'          => 'simpay-total-amount-label-' . $counter,
			'value'       => isset( $field['label'] ) ? $field['label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'placeholder' => esc_attr__( 'Total Amount:', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-total-amount-recurring-total-' . $counter; ?>"><?php esc_html_e( 'Show Recurring Total', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		$recurring_total = isset( $field['recurring_total'] ) ? $field['recurring_total'] : '';

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[total_amount][' . $counter . '][recurring_total]',
			'id'         => 'simpay-total-amount-recurring-total-' . $counter,
			'value'      => $recurring_total,
			'attributes' => array(
				'data-field-key' => $counter,
				'data-show'      => '.simpay-recurring-total-label-wrap',
			),
			'class'      => array(
				'simpay-section-toggle',
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field simpay-recurring-total-label-wrap <?php echo( 'yes' !== $recurring_total ? 'simpay-panel-hidden' : '' ); ?>">
	<th>
		<label for="<?php echo 'simpay-total-recurring-total-label-' . $counter; ?>"><?php esc_html_e( 'Recurring Total Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[total_amount][' . $counter . '][recurring_total_label]',
			'id'          => 'simpay-total-amount-recurring-total-label-' . $counter,
			'value'       => isset( $field['recurring_total_label'] ) ? $field['recurring_total_label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'placeholder' => esc_attr__( 'Recurring Total:', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-total-amount-tax-amount-' . $counter; ?>"><?php esc_html_e( 'Show Tax Amount', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		$tax_amount = isset( $field['tax_amount'] ) ? $field['tax_amount'] : '';

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[total_amount][' . $counter . '][tax_amount]',
			'id'         => 'simpay-total-amount-tax-amount-' . $counter,
			'value'      => $tax_amount,
			'attributes' => array(
				'data-field-key' => $counter,
				'data-show'      => '.simpay-total-amount-tax-amount-label-wrap',
			),
			'class'      => array(
				'simpay-section-toggle',
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field simpay-total-amount-tax-amount-label-wrap <?php echo( 'yes' !== $tax_amount ? 'simpay-panel-hidden' : '' ); ?>">
	<th>
		<label for="<?php echo 'simpay-total-amount-tax-amount-label-' . $counter; ?>"><?php esc_html_e( 'Tax Amount Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[total_amount][' . $counter . '][tax_amount_label]',
			'id'          => 'simpay-total-amount-tax-amount-label-' . $counter,
			'value'       => isset( $field['tax_amount_label'] ) ? $field['tax_amount_label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'placeholder' => esc_attr__( 'Total Tax:', 'simple-pay' ),
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
			'name'       => '_simpay_custom_field[total_amount][' . $counter . '][id]',
			'id'         => 'simpay-total-amount-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
