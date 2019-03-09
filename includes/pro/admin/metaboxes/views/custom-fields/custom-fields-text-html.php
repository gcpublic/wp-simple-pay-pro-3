<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-text-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[text][' . $counter . '][label]',
			'id'          => 'simpay-text-label-' . $counter,
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
		<label for="<?php echo 'simpay-text-placeholder-' . $counter; ?>"><?php esc_html_e( 'Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[text][' . $counter . '][placeholder]',
			'id'         => 'simpay-text-placeholder-' . $counter,
			'value'      => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
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
		<label for="<?php echo 'simpay-text-required-' . $counter; ?>"><?php esc_html_e( 'Required Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[text][' . $counter . '][required]',
			'id'         => 'simpay-text-required-' . $counter,
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
		<label for="<?php echo 'simpay-text-default-' . $counter; ?>"><?php esc_html_e( 'Default Value', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[text][' . $counter . '][default]',
			'id'         => 'simpay-text-default-' . $counter,
			'value'      => isset( $field['default'] ) ? $field['default'] : '',
			'class'      => array(
				'simpay-field-text',
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
		<label for="<?php echo 'simpay-text-multiline-' . $counter; ?>"><?php esc_html_e( 'Multi-line', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		$multiline = isset( $field['multiline'] ) ? $field['multiline'] : '';

		simpay_print_field( array(
			'type'        => 'checkbox',
			'name'        => '_simpay_custom_field[text][' . $counter . '][multiline]',
			'id'          => 'simpay-text-multiline-' . $counter,
			'value'       => $multiline,
			'attributes'  => array(
				'data-field-key' => $counter,
				'data-show'      => '.simpay-textbox-rows-wrap',
			),
			'class'       => array(
				'simpay-section-toggle',
			),
			'description' => esc_html__( 'Set to a paragraph text field. Limited to 500 characters by Stripe.', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field <?php echo( 'yes' !== $multiline ? 'simpay-panel-hidden' : '' ); ?> simpay-textbox-rows-wrap">
	<th>
		<label for="<?php echo 'simpay-text-rows-' . $counter; ?>"><?php esc_html_e( 'Rows', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'number',
			'name'        => '_simpay_custom_field[text][' . $counter . '][rows]',
			'id'          => 'simpay-text-rows-' . $counter,
			'value'       => isset( $field['rows'] ) ? $field['rows'] : '3',
			'class'       => array(
				'small-text',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'placeholder' => 3,
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-text-metadata-' . $counter; ?>"><?php esc_html_e( 'Stripe Metadata Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[text][' . $counter . '][metadata]',
			'id'          => 'simpay-text-metadata-' . $counter,
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
			'name'       => '_simpay_custom_field[text][' . $counter . '][id]',
			'id'         => 'simpay-text-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
