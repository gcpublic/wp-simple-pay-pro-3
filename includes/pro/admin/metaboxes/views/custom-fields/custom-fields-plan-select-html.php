<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="simpay-plan-select-form-field-label"><?php esc_html_e( 'Form Field Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_plan_select_form_field_label',
			'id'          => 'simpay-plan-select-form-field-label',
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

<tr>
	<td colspan="2">
		<p class="description">
			<?php esc_html_e( 'This is just a placeholder where the Subscription multi-plan select field, if enabled, will be displayed on the form. If you enabled a custom amount field within the plan selection, it will also be displayed here.', 'simple-pay' ); ?>
		</p>
		<p>
			<?php printf( wp_kses( __( '<a href="%1$s" class="%2$s" data-show-tab="%3$s">See Subscription Options</a> to set a recurring amount.', 'simple-pay' ), array(
			'a' => array(
				'href'          => array(),
				'class'         => array(),
				'data-show-tab' => array(),
			),
		) ), '#', 'simpay-tab-link', 'simpay-subscription_options' ); ?>
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
			'name'       => '_simpay_custom_field[plan_select][' . $counter . '][id]',
			'id'         => 'simpay-plan-select-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );
		?>
	</td>
</tr>
