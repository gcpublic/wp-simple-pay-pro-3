<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-date-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[date][' . $counter . '][label]',
			'id'          => 'simpay-date-label-' . $counter,
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
		<label for="<?php echo 'simpay-date-required-' . $counter; ?>"><?php esc_html_e( 'Required Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[date][' . $counter . '][required]',
			'id'         => 'simpay-date-required-' . $counter,
			'value'      => isset( $field['required'] ) ? $field['required'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-date-default-' . $counter; ?>"><?php esc_html_e( 'Default Value', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[date][' . $counter . '][default]',
			'id'         => 'simpay-date-default-' . $counter,
			'value'      => isset( $field['default'] ) ? $field['default'] : '',
			'class'      => array(
				'date-time-text',
				'simpay-date-input',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-date-metadata-' . $counter; ?>"><?php esc_html_e( 'Stripe Metadata Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[date][' . $counter . '][metadata]',
			'id'          => 'simpay-date-metadata-' . $counter,
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
			'name'       => '_simpay_custom_field[date][' . $counter . '][id]',
			'id'         => 'simpay-date-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
