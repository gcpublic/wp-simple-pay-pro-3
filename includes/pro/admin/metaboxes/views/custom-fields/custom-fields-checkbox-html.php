<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-checkbox-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[checkbox][' . $counter . '][label]',
			'id'          => 'simpay-checkbox-label-' . $counter,
			'value'       => isset( $field['label'] ) ? $field['label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'description' => esc_html__( 'Label displayed next to this checkbox on the payment form. May contain HTML.', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-checkbox-required-' . $counter; ?>"><?php esc_html_e( 'Required Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[checkbox][' . $counter . '][required]',
			'id'         => 'simpay-checkbox-required-' . $counter,
			'value'      => isset( $field['required'] ) ? ( $field['required'] == 'yes' ? 'yes' : 'no' ) : 'no',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-checkbox-default-' . $counter; ?>"><?php esc_html_e( 'Default to Checked', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[checkbox][' . $counter . '][default]',
			'id'         => 'simpay-checkbox-default-' . $counter,
			'value'      => isset( $field['default'] ) ? ( $field['default'] == 'yes' ? 'yes' : 'no' ) : 'no',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-checkbox-metadata-' . $counter; ?>"><?php esc_html_e( 'Stripe Metadata Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[checkbox][' . $counter . '][metadata]',
			'id'          => 'simpay-checkbox-metadata-' . $counter,
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
			'name'       => '_simpay_custom_field[checkbox][' . $counter . '][id]',
			'id'         => 'simpay-checkbox-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
