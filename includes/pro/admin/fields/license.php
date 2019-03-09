<?php

namespace SimplePay\Pro\Admin\Fields;

use SimplePay\Core\Abstracts\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * License input field.
 *
 * @since 3.0.0
 */
class License extends Field {


	/**
	 * Construct.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field
	 */
	public function __construct( $field ) {

		$this->type_class = 'simpay-field-license';

		parent::__construct( $field );
	}

	/**
	 * Outputs the field markup.
	 *
	 * @since 3.0.0
	 */
	public function html() {

		$license_data = get_option( 'simpay_license_data' );
		$status       = false !== $license_data ? $license_data->license : false;

		if ( $status !== 'valid' ) {
			$display_activate   = 'display: inline-block';
			$display_deactivate = 'display: none';
			$message_color      = '#f00';
		} else {
			$display_activate   = $active = 'display: none';
			$display_deactivate = 'display: inline-block';
			$message_color      = '#46b450';
		}

		?>

		<div class="simpay-license-field">

			<input type="text"
			       name="<?php echo $this->name; ?>"
			       id="<?php echo $this->id; ?>"
			       value="<?php echo $this->value; ?>"
			       class="<?php echo $this->class; ?>" />

			<span class="simpay-license-buttons">

				<button class="button-secondary simpay-license deactivate" name="simpay_deactivate_license" style="<?php echo $display_deactivate; ?>">
			        <i class="simpay-icon-spinner simpay-icon-spin" style="display: none;"></i><?php esc_html_e( 'Deactivate', 'simple-pay' ); ?>
				</button>

				<button class="button-primary simpay-license activate" name="simpay_activate_license" style="<?php echo $display_activate; ?>">
					<i class="simpay-icon-spinner simpay-icon-spin" style="display: none;"></i><?php esc_html_e( 'Activate', 'simple-pay' ); ?>
				</button>
			</span>

			<p class="description">
				<?php esc_html_e( 'A valid license key is required for access to automatic updates and support.', 'simple-pay' ); ?>
			</p>

			<?php if ( ! empty( $status ) && ! empty( $this->value ) ): ?>
				<p>
					<span style="color: <?php echo $message_color; ?>;"><?php
						printf( esc_html__( 'License status: %s', 'simple-pay' ), $status );

						if ( 'valid' !== $status && isset( $license_data->error ) ) {
							echo ' - ' . esc_html( $license_data->error );
						}
						?>
					</span>
				</p>
			<?php endif; ?>

			<?php if ( ! simpay_subscriptions_enabled() ): ?>
				<p style="margin: 20px 0 10px 0;">
					<?php esc_html_e( 'Want to connect Stripe subscriptions to your payment forms?', 'simple-pay' ); ?>
				</p>
				<p>
					<a class="simpay-upgrade-btn simpay-license-page-upgrade-btn"
					   href="<?php echo simpay_my_account_url( 'general-settings' ); ?>" target="_blank"><?php esc_html_e( 'Upgrade Your License Now', 'simple-pay' ); ?></a>
				</p>
			<?php endif; ?>

		</div>
		<?php
	}
}
