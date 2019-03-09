<?php

namespace SimplePay\Pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Objects factory.
 *
 * Helper class to get the right type of object used across the plugin.
 *
 * @since 3.0.0
 */
class Objects {

	/**
	 * Constructor.
	 *
	 * Add default objects.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		if ( is_admin() ) {
			add_filter( 'simpay_get_admin_pages', array( $this, 'license_tab' ) );

			add_filter( 'simpay_object_loader', array( $this, 'load_license_page' ), 10, 4 );
		}
	}

	public function license_tab( $admin_pages ) {

		if ( is_array( $admin_pages ) ) {
			return array_merge( $admin_pages, array(
				'settings' => array(
					'license',
					'keys',
					'general',
					'display',
				),
			) );
		}
	}

	public function load_license_page( $final, $type, $namespace, $class_name ) {

		if ( 'admin-page' === $type && 'license' === strtolower( $class_name ) ) {
			$namespace = '\\' . __NAMESPACE__ . '\Admin\Pages\\';

			$final = $namespace . $class_name;
		}

		if ( 'field' === $type ) {
			if ( 'license' === strtolower( $class_name ) || 'custom_html' === strtolower( $class_name ) ) {
				$namespace = '\\' . __NAMESPACE__ . '\Admin\Fields\\';

				$final = $namespace . $class_name;
			}
		}

		return $final;
	}
}
