<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="simpay-custom-amount-label"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		global $post;

		$value = simpay_get_saved_meta( $post->ID, '_custom_amount_label' );

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_custom_amount_label',
			'id'          => 'simpay-custom-amount-label',
			'value'       => $value,
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

<tr>
	<td colspan="2">
		<p class="description">
			<?php esc_html_e( 'This is just a placeholder where the Custom Amount field, if enabled, will be displayed on the form.', 'simple-pay' ); ?>
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
			'name'       => '_simpay_custom_field[custom_amount][' . $counter . '][id]',
			'id'         => 'simpay-custom-amount-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
