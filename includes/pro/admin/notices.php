<?php

namespace SimplePay\Pro\Admin;

use SimplePay\Core\Admin\Notices as CoreNotice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notices {

	public function __construct( $is_admin_screen ) {

		add_action( 'simpay_admin_notices', array( $this, 'show_notices_pro' ), 20 );

		$this->license_key_error( $is_admin_screen );
		$this->test_mode_change_warning();
	}

	/**
	 *
	 * Display the error message if no license key is found
	 *
	 */
	public function license_key_error( $is_admin_screen ) {

		$license_data = get_option( 'simpay_license_data' );

		if ( ( empty( $license_data ) || 'valid' !== $license_data->license ) && ( ( false !== $is_admin_screen && ( 'simpay_settings' === $is_admin_screen && isset( $_GET['tab'] ) && 'license' !== $_GET['tab'] ) || 'simpay' === $is_admin_screen ) ) ) {

			/* translators: This is part of the admin notice for missing license keys. The full string is "Your WP Simple Pay Pro 3 license key is invalid, inactive or missing. Please enter and activate your license key to enable automatic updates." */
			$notice_message = __( 'Your WP Simple Pay Pro 3 license key is invalid, inactive or missing. Please', 'simple-pay' );
			$notice_message .= ' <a href="' . admin_url( 'admin.php?page=simpay_settings&tab=license' ) . '">' . __( 'enter and activate', 'simple-pay' ) . '</a> ';
			$notice_message .= __( 'your license key to enable automatic updates.', 'simple-pay' );

			CoreNotice::print_notice( $notice_message, 'error' );
		}
	}

	/**
	 *
	 * Show warning if test mode setting has been changed
	 *
	 */
	public function test_mode_change_warning() {

		$test_mode_change = get_option( 'simpay_test_mode_changed' );

		if ( $test_mode_change ) {

			if ( 'enabled' === $test_mode_change ) {
				$notice_message = sprintf( wp_kses( __( 'It looks like you have switched to test mode. Make sure the correct subscription plans and coupons exist in your test <a href="%s" target="_blank">Stripe account</a>.', 'simple-pay' ), array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				) ), 'https://dashboard.stripe.com/test/dashboard' );
			} else {
				$notice_message = sprintf( wp_kses( __( 'It looks like you have switched to live mode. Make sure the correct subscription plans and coupons exist in your live <a href="%s" target="_blank">Stripe account</a>.', 'simple-pay' ), array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				) ), 'https://dashboard.stripe.com/live/dashboard' );
			}

			CoreNotice::print_notice( $notice_message, 'warning', 'mode_changed' );

			// Remove the option so the warning only shows until the user navigates from the page.
			delete_option( 'simpay_test_mode_changed' );
		}
	}

	/**
	 * Show Pro-only admin notices
	 */
	public function show_notices_pro() {

		// Show non-dismissable notice for requiring PHP 5.6 next update.

		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {

			$msg = '<p><strong>' . __( 'WP Simple Pay is increasing its PHP version requirement', 'simple-pay' ) . '</strong></p>';
			$msg .= '<p>' . sprintf( __( 'WP Simple Pay will be increasing its PHP requirement to version 5.6 or higher in version 3.4. It looks like you\'re using version %s, which means you will need to upgrade your version of PHP before upgrading. Newer versions of PHP are both faster and more secure. The version you\'re using <a href="%s" target="_blank">no longer receives security updates</a>, which is another great reason to update.', 'simple-pay' ), PHP_VERSION, 'http://php.net/eol.php' ) . '</p>';
			$msg .= '<p><strong>' . __( 'Which version should I upgrade to?', 'simple-pay' ) . '</strong></p>';
			$msg .= '<p>' . __( 'In order to be compatible with future versions of WP Simple Pay, you should update your PHP version to 5.6, 7.0, 7.1, or 7.2. On a normal WordPress site, switching to PHP 5.6 should never cause issues. We would however actually recommend you switch to PHP 7.1 or higher to receive the full speed and security benefits provided to more modern and fully supported versions of PHP. However, some plugins may not be fully compatible with PHP 7+, so more testing may be required.', 'simple-pay' ) . '</p>';
			$msg .= '<p><strong>' . __( 'Need help upgrading? Ask your web host!', 'simple-pay' ) . '</strong></p>';
			$msg .= '<p>' . sprintf( __( 'Many web hosts can give you instructions on how/where to upgrade your version of PHP through their control panel, or may even be able to do it for you. If you need to change hosts, please see <a href="%s" target="_blank">our hosting recommendations</a>.', 'simple-pay' ), 'https://wpsimplepay.com/recommended-wordpress-hosting/' ) . '</p>';

			CoreNotice::print_notice( $msg );
		}
	}
}
