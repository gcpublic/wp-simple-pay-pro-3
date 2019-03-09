<?php

namespace SimplePay\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Upgrade class
 *
 * This class handles database upgrade routines between versions
 *
 * @package     WP Simple Pay
 * @copyright   Copyright (c) 2018, Sandhills Development
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3
 */
class Upgrades {

	private $version = '';
	private $upgraded = false;

	/**
	 * Upgrades constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->version = preg_replace( '/[^0-9.].*/', '', get_option( 'simpay_version' ) );

		add_action( 'admin_init', array( $this, 'init' ), - 9999 );

	}

	/**
	 * Trigger updates and maybe update the Pro version number
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		$this->v33_upgrades();

		// If upgrades have occurred or the DB version is differnt from the version constant
		if ( $this->upgraded || $this->version <> SIMPLE_PAY_VERSION ) {
			update_option( 'simpay_version_upgraded_from', $this->version );
			update_option( 'simpay_version', SIMPLE_PAY_VERSION );
		}

	}

	/**
	 * Process Pro 3.3 upgrades
	 *
	 * @access private
	 * @return void
	 */
	private function v33_upgrades() {

		if ( version_compare( $this->version, '3.3', '<' ) ) {

			$forms = get_posts( array( 'post_type' => 'simple-pay', 'posts_per_page' => - 1 ) );
			if ( $forms ) {
				foreach ( $forms as $form ) {

					if ( ! get_post_meta( $form->ID, '_form_display_type', true ) ) {

						update_post_meta( $form->ID, '_form_display_type', 'stripe_checkout' );

					}
				}
			}

		}
	}

}
