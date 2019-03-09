<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do intval on counter here so we don't have to run it each time we use it below. Saves some function calls.
$counter = absint( $counter );

?>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-billing-container-label' . $counter; ?>"><?php esc_html_e( 'Billing Address Heading', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][billing-container-label]',
			'id'          => 'simpay-address-billing-container-label-' . $counter,
			'value'       => isset( $field['billing-container-label'] ) ? $field['billing-container-label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'description' => esc_html__( 'Heading displayed above the entire billing address.', 'simple-pay' ),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-label-street' . $counter; ?>"><?php esc_html_e( 'Street Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][label-street]',
			'id'          => 'simpay-address-label-street-' . $counter,
			'value'       => isset( $field['label-street'] ) ? $field['label-street'] : '',
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
		<label for="<?php echo 'simpay-address-placeholder-street' . $counter; ?>"><?php esc_html_e( 'Street Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[address][' . $counter . '][placeholder-street]',
			'id'         => 'simpay-address-placeholder-street-' . $counter,
			'value'      => isset( $field['placeholder-street'] ) ? $field['placeholder-street'] : esc_attr__( 'Street', 'simple-pay' ),
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
		<label for="<?php echo 'simpay-address-label-city' . $counter; ?>"><?php esc_html_e( 'City Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][label-city]',
			'id'          => 'simpay-address-label-city-' . $counter,
			'value'       => isset( $field['label-city'] ) ? $field['label-city'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-placeholder-city' . $counter; ?>"><?php esc_html_e( 'City Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[address][' . $counter . '][placeholder-city]',
			'id'         => 'simpay-address-placeholder-city-' . $counter,
			'value'      => isset( $field['placeholder-city'] ) ? $field['placeholder-city'] : esc_attr__( 'City', 'simple-pay' ),
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
		<label for="<?php echo 'simpay-address-label-state' . $counter; ?>"><?php esc_html_e( 'State/Province/Region Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][label-state]',
			'id'          => 'simpay-address-label-state-' . $counter,
			'value'       => isset( $field['label-state'] ) ? $field['label-state'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-placeholder-state' . $counter; ?>"><?php esc_html_e( 'State/Province Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[address][' . $counter . '][placeholder-state]',
			'id'         => 'simpay-address-placeholder-state-' . $counter,
			'value'      => isset( $field['placeholder-state'] ) ? $field['placeholder-state'] : esc_attr__( 'State', 'simple-pay' ),
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
		<label for="<?php echo 'simpay-address-label-zip' . $counter; ?>"><?php esc_html_e( 'Zip/Postal Code Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][label-zip]',
			'id'          => 'simpay-address-label-zip-' . $counter,
			'value'       => isset( $field['label-zip'] ) ? $field['label-zip'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-placeholder-zip' . $counter; ?>"><?php esc_html_e( 'Zip/Postal Code Placeholder', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[address][' . $counter . '][placeholder-zip]',
			'id'         => 'simpay-address-placeholder-zip-' . $counter,
			'value'      => isset( $field['placeholder-zip'] ) ? $field['placeholder-zip'] : esc_attr__( 'Zip/Postal Code', 'simple-pay' ),
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
		<label for="<?php echo 'simpay-address-label-country' . $counter; ?>"><?php esc_html_e( 'Country Label', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'standard',
			'subtype'    => 'text',
			'name'       => '_simpay_custom_field[address][' . $counter . '][label-country]',
			'id'         => 'simpay-address-label-country-' . $counter,
			'value'      => isset( $field['label-country'] ) ? $field['label-country'] : '',
			'class'      => array(
				'simpay-field-text',
				'simpay-label-input',
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
		<label for="<?php echo 'simpay-address-default-country' . $counter; ?>"><?php esc_html_e( 'Default Country', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'select',
			'name'       => '_simpay_custom_field[address][' . $counter . '][default-country]',
			'id'         => 'simpay-address-default-country-' . $counter,
			'value'      => isset( $field['default-country'] ) ? $field['default-country'] : '',
			'class'      => array(
				'simpay-field-dropdown',
			),
			'attributes' => array(
				'data-field-key' => $counter,
			),
			'options'    => simpay_get_country_list(),
		) );

		?>
	</td>
</tr>


<!-- Required checkbox (default to checked) -->
<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-required-' . $counter; ?>"><?php esc_html_e( 'Address Required', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[address][' . $counter . '][required]',
			'id'         => 'simpay-address-required-' . $counter,
			'value'      => isset( $field['required'] ) ? $field['required'] : 'yes',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>

<!-- Collect shipping address checkbox -->
<tr class="simpay-panel-field">
	<th>
		<label for="<?php echo 'simpay-address-collect-shipping-' . $counter; ?>"><?php esc_html_e( 'Collect Shipping Address', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

        $collect_shipping = isset( $field['collect-shipping'] ) ? $field['collect-shipping'] : '';

		simpay_print_field( array(
			'type'       => 'checkbox',
			'name'       => '_simpay_custom_field[address][' . $counter . '][collect-shipping]',
			'id'         => 'simpay-address-collect-shipping-' . $counter,
			'value'      => $collect_shipping,
			'attributes' => array(
				'data-field-key' => $counter,
				'data-show'      => '.simpay-address-shipping-address-heading-wrap',
			),
			'class'       => array(
				'simpay-section-toggle',
			),
		) );

		?>
	</td>
</tr>

<tr class="simpay-panel-field simpay-address-shipping-address-heading-wrap <?php echo( 'yes' !== $collect_shipping ? 'simpay-panel-hidden' : '' ); ?>">
	<th>
		<label for="<?php echo 'simpay-address-shipping-container-label' . $counter; ?>"><?php esc_html_e( 'Shipping Address Heading', 'simple-pay' ); ?></label>
	</th>
	<td>
		<?php

		simpay_print_field( array(
			'type'        => 'standard',
			'subtype'     => 'text',
			'name'        => '_simpay_custom_field[address][' . $counter . '][shipping-container-label]',
			'id'          => 'simpay-address-shipping-container-label-' . $counter,
			'value'       => isset( $field['shipping-container-label'] ) ? $field['shipping-container-label'] : '',
			'class'       => array(
				'simpay-field-text',
				'simpay-label-input',
			),
			'attributes'  => array(
				'data-field-key' => $counter,
			),
			'description' => esc_html__( 'Heading displayed above the entire shipping address.', 'simple-pay' ),
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
			'name'       => '_simpay_custom_field[address][' . $counter . '][id]',
			'id'         => 'simpay-address-id-' . $counter,
			'value'      => isset( $field['id'] ) ? $field['id'] : '',
			'attributes' => array(
				'data-field-key' => $counter,
			),
		) );

		?>
	</td>
</tr>
