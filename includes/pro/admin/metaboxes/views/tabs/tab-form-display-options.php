<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<table>
	<thead>
	<tr>
		<th colspan="2"><?php esc_html_e( 'Form Display Options', 'simple-pay' ); ?></th>
	</tr>
	</thead>
	<tbody class="simpay-panel-section">

	<tr class="simpay-panel-field">
		<th>
			<label for="_form_display_type"><?php esc_html_e( 'Form Display Type', 'simple-pay' ); ?></label>
		</th>
		<td style="padding-top: 0;">

			<?php

			// TODO Description for each form display option.

			$form_display_type = simpay_get_saved_meta( $post->ID, '_form_display_type', 'embedded' );

			simpay_print_field( array(
				'type'    => 'radio',
				'name'    => '_form_display_type',
				'id'      => '_form_display_type',
				'value'   => $form_display_type,
				'class'   => array(
					'simpay-field-text',
					'simpay-multi-toggle',
				),
				'options' => array(
					'embedded'        => esc_html__( 'Embedded', 'simple-pay' ),
					'overlay'         => esc_html__( 'Overlay', 'simple-pay' ),
					'stripe_checkout' => esc_html__( 'Stripe Checkout', 'simple-pay' ),
				),
				'inline'  => 'inline',
				// Description for this field set below so we can use wp_kses() without clashing with the wp_kses() already being applied to simpay_print_field()
			) );
			?>

		</td>
	</tr>

	<tr class="simpay-panel-field">
		<th>
			<label for="_company_name"><?php esc_html_e( 'Company Name', 'simple-pay' ); ?></label>
		</th>
		<td>
			<?php

			simpay_print_field( array(
				'type'    => 'standard',
				'subtype' => 'text',
				'name'    => '_company_name',
				'id'      => '_company_name',
				'value'   => simpay_get_saved_meta( $post->ID, '_company_name', get_bloginfo( 'name' ) ),
				'class'   => array(
					'simpay-field-text',
				),
				'description' => __( 'Also used for the form heading.', 'simple-pay' ),
			) );
			?>
		</td>
	</tr>

	<tr class="simpay-panel-field">
		<th>
			<label for="_item_description"><?php esc_html_e( 'Item Description', 'simple-pay' ); ?></label>
		</th>
		<td>
			<?php

			simpay_print_field( array(
				'type'    => 'standard',
				'subtype' => 'text',
				'name'    => '_item_description',
				'id'      => '_item_description',
				'value'   => simpay_get_saved_meta( $post->ID, '_item_description' ),
				'class'   => array(
					'simpay-field-text',
				),
				'description' => __( 'Also used for the form subheading.', 'simple-pay' ),) );
			?>
		</td>
	</tr>

	<tr class="simpay-panel-field toggle-_form_display_type-stripe_checkout <?php echo 'stripe_checkout' ===
    $form_display_type ? '' : 'simpay-panel-hidden'; ?>">
		<th></th>
		<td>
			<p class="description">
				<?php printf( wp_kses( __( 'Configure your Stripe Checkout form in the <a href="%1$s" class="%2$s" data-show-tab="%3$s">Stripe Checkout Display</a> options.', 'simple-pay' ), array(
					'a' => array(
						'href'          => array(),
						'class'         => array(),
						'data-show-tab' => array(),
					),
				) ), '#', 'simpay-tab-link', 'simpay-stripe_checkout' ); ?>
			</p>
		</td>
	</tr>

	</tbody>
</table>

<?php echo simpay_docs_link( __( 'Help docs for Form Display Options', 'simple-pay' ), 'form-display-options', 'form-settings' );
