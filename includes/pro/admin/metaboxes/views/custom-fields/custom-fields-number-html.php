<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-number-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[number][' . $counter . '][label]',
			'id'          => 'simpay-number-label-' . $counter,
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
		<label for="<?php echo 'simpay-number-placeholder-' . $counter; ?>"><?php esc_html_e( 'Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_simpay_custom_field[number][' . $counter . '][placeholder]',
			'id'         => 'simpay-number-placeholder-' . $counter,
			'value'      => isset( $field['placeholder'] ) ? $field['placeholder'] : '',
			'class'      => array(
				'small-text',
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
		<label for="<?php echo 'simpay-number-required-' . $counter; ?>"><?php esc_html_e( 'Required Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[number][' . $counter . '][required]',
			'id'         => 'simpay-number-required-' . $counter,
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
		<label for="<?php echo 'simpay-number-default-' . $counter; ?>"><?php esc_html_e( 'Default Value', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_simpay_custom_field[number][' . $counter . '][default]',
			'id'         => 'simpay-number-default-' . $counter,
			'value'      => isset( $field['default'] ) ? $field['default'] : '',
			'class'      => array(
				'small-text',
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
		<label for="<?php echo 'simpay-number-minimum-' . $counter; ?>"><?php esc_html_e( 'Minimum', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_simpay_custom_field[number][' . $counter . '][minimum]',
			'id'         => 'simpay-number-minimum-' . $counter,
			'value'      => isset( $field['minimum'] ) ? $field['minimum'] : '',
			'class'      => array(
				'small-text',
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
		<label for="<?php echo 'simpay-number-maximum-' . $counter; ?>"><?php esc_html_e( 'Maximum', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'number',
			'name'       => '_simpay_custom_field[number][' . $counter . '][maximum]',
			'id'         => 'simpay-number-maximum-' . $counter,
			'value'      => isset( $field['maximum'] ) ? $field['maximum'] : '',
			'class'      => array(
				'small-text',
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
		<label for="<?php echo 'simpay-number-quantity-' . $counter; ?>"><?php esc_html_e( 'Quantity Field', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[number][' . $counter . '][quantity]',
			'id'         => 'simpay-number-quantity-' . $counter,
			'value'      => isset( $field['quantity'] ) ? $field['quantity'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php esc_html_e( 'Enable to multiply the amount by this value.', 'simple-pay' ); ?>
		</p>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-number-metadata-' . $counter; ?>"><?php esc_html_e( 'Stripe Metadata Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[number][' . $counter . '][metadata]',
			'id'          => 'simpay-number-metadata-' . $counter,
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
			'name'       => '_simpay_custom_field[number][' . $counter . '][id]',
			'id'         => 'simpay-number-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
