<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-customer-name-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[customer_name][' . $counter . '][label]',
			'id'          => 'simpay-customer-name-label-' . $counter,
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
		<label for="<?php echo 'simpay-customer-name-placeholder-' . $counter; ?>"><?php esc_html_e( 'Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[customer_name][' . $counter . '][placeholder]',
			'id'         => 'simpay-customer-name-placeholder-' . $counter,
			'value'      => isset( $field['placeholder'] ) ? $field['placeholder'] : esc_attr__( 'Full name', 'simple-pay' ),
			'class'      => array(
				'simpay-field-text',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
			'description' => simpay_placeholder_description(),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-customer-name-required-' . $counter; ?>"><?php esc_html_e( 'Required Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[customer_name][' . $counter . '][required]',
			'id'         => 'simpay-customer-name-required-' . $counter,
			'value'      => isset( $field['required'] ) ? $field['required'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
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
			'name'       => '_simpay_custom_field[customer_name][' . $counter . '][id]',
			'id'         => 'simpay-customer-name-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
