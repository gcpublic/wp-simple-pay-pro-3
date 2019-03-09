<?php

namespace SimplePay\Pro\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Menus.
 *
 * Handles the plugin admin dashboard menus.
 *
 * @since 3.0.0
 */
class Menus {

	/**
	 * Set properties.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		add_filter( 'simpay_menu_title', array( $this, 'pro_menu_title' ) );

		// Remove the upgrade link
		add_action( 'admin_menu', function () {
			remove_submenu_page( 'simpay', 'simpay_upgrade' );
		} );
	}

	public function pro_menu_title() {
		return __( 'Simple Pay Pro', 'simple-pay' );
	}
}
