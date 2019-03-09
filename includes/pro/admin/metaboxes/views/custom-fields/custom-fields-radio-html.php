<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-radio-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[radio][' . $counter . '][label]',
			'id'          => 'simpay-radio-label-' . $counter,
			'value'       => isset( $field['label'] ) ? $field['label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'description' => simpay_form_field_label_description(),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-radio-options-' . $counter; ?>"><?php esc_html_e( 'Options', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[radio][' . $counter . '][options]',
			'id'         => 'simpay-radio-options-' . $counter,
			'value'      => isset( $field['options'] ) ? $field['options'] : '',
			'class'      => array(
				'simpay-field-text',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php esc_html_e( 'A comma separated list of options to fill the radio button list with.', 'simple-pay' ); ?>
		</p>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-radio-default-' . $counter; ?>"><?php esc_html_e( 'Default Value', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[radio][' . $counter . '][default]',
			'id'         => 'simpay-radio-default-' . $counter,
			'value'      => isset( $field['default'] ) ? $field['default'] : '',
			'class'      => array(
				'simpay-field-text',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php esc_html_e( 'Option to be selected by default. Will be first in list if left blank or no match.', 'simple-pay' ); ?>
		</p>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-dropdown-type-' . $counter; ?>"><?php esc_html_e( 'Quantity or Amount Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		$type = isset( $field['amount_quantity'] ) ? $field['amount_quantity'] : 'not_used';

		simpay_print_field( array(
			'type'       => 'radio',
			'name'       => '_simpay_custom_field[radio][' . $counter . '][amount_quantity]',
			'id'         => 'simpay-radio-amount-quantity-' . $counter,
			'value'      => $type,
			'attributes' => array(
				'data-disable-amount-single' => 'amount',
			),
			'class'      => array(
				'simpay-multi-toggle',
				'simpay-disable-amount',
			),
			'options'    => array(
				'not_used' => esc_html__( 'Not Used', 'simple-pay' ),
				'quantity' => esc_html__( 'Quantity (Multiplier)', 'simple-pay' ),
				'amount'   => esc_html__( 'Amount (User Selects)', 'simple-pay' ),
			),
			'inline'     => 'inline',
		) );
		?>
	</td>
</tr>

<tr class="simpay-panel-field simpay-radio-quantities-wrap <?php echo( 'quantity' !== $type ? 'simpay-panel-hidden' : '' ); ?> toggle-simpay-radio-amount-quantity-<?php echo $counter; ?>-quantity">
	<th>
		<label for="<?php echo 'simpay-radio-quantities-' . $counter; ?>"><?php esc_html_e( 'Quantities', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[radio][' . $counter . '][quantities]',
			'id'         => 'simpay-radio-quantities-' . $counter,
			'value'      => isset( $field['quantities'] ) ? $field['quantities'] : '',
			'class'      => array(
				'simpay-field-text',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php esc_html_e( 'A comma separated list of quantity values to multiply the amount by (i.e. "1,5,10"). Needs to match the number of options and their order.', 'simple-pay' ); ?>
		</p>
	</td>
</tr>

<tr class="simpay-panel-field simpay-radio-amounts-wrap <?php echo( 'amount' !== $type ? 'simpay-panel-hidden' : '' ); ?> toggle-simpay-radio-amount-quantity-<?php echo $counter; ?>-amount">
	<th>
		<label for="<?php echo 'simpay-radio-amounts-' . $counter; ?>"><?php esc_html_e( 'Amounts', 'simple-pay' ); ?></label>
	</th>
	<td>

		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[radio][' . $counter . '][amounts]',
			'id'         => 'simpay-radio-amounts-' . $counter,
			'value'      => isset( $field['amounts'] ) ? $field['amounts'] : '',
			'class'      => array(
				'simpay-field-text',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php printf( esc_html__( 'A comma separated list of amounts without the "%s" symbol (i.e. "2.00,4.50,9.00"). Needs to match the number of options and their order.', 'simple-pay' ), simpay_get_saved_currency_symbol() ); ?>
		</p>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-radio-metadata-' . $counter; ?>"><?php esc_html_e( 'Stripe Metadata Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[radio][' . $counter . '][metadata]',
			'id'          => 'simpay-radio-metadata-' . $counter,
			'value'       => isset( $field['metadata'] ) ? $field['metadata'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
				'maxlength'      => simpay_metadata_title_length(),
			),
			'description' => simpay_metadata_label_description(),
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
			'name'       => '_simpay_custom_field[radio][' . $counter . '][id]',
			'id'         => 'simpay-radio-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
