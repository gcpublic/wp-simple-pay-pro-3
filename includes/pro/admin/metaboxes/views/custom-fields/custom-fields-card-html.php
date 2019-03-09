<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-card-label-' . $counter; ?>"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[card][' . $counter . '][label]',
			'id'          => 'simpay-card-label-' . $counter,
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

<tr class="simpay-panel-field toggle-_form_display_type-embedded toggle-_form_display_type-overlay">
	<th>
		<label for="<?php echo 'simpay-card-verify-zip-' . $counter; ?>"><?php esc_html_e( 'Verify Zip/Postal Code', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		global $post;

		// Used for checking if this is a new form so we can make verify zip checked by default
		$custom_keys = get_post_custom_keys( $post->ID );

		if ( empty( $custom_keys ) ) {
			$verify_zip = 'no';
		} else {
			$verify_zip = isset( $field['verify_zip'] ) ? $field['verify_zip'] : '';
		}

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[card][' . $counter . '][verify_zip]',
			'id'         => 'simpay-card-verify-zip-' . $counter,
			'value'      => $verify_zip,
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>

		<p class="description">
			<?php esc_html_e( 'Inline Zip/Postal code will not be enabled if an Address field is present on the payment form.', 'simple-pay' ); ?>
		</p>
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
			'name'       => '_simpay_custom_field[card][' . $counter . '][id]',
			'id'         => 'simpay-card-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
