<?php

namespace SimplePay\Pro\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	public function __construct() {

		add_filter( 'simpay_before_register_admin_scripts', array( $this, 'add_admin_scripts' ), 10, 2 );

		add_filter( 'simpay_before_register_admin_styles', array( $this, 'add_admin_styles' ), 10, 2 );
	}

	public function add_admin_scripts( $scripts, $min ) {

		$scripts['simpay-admin-pro'] = array(
			'src'    => SIMPLE_PAY_ASSETS . 'js/admin-pro' . $min . '.js',
			'deps'   => array( 'jquery', 'jquery-ui-datepicker', 'simpay-admin' ),
			'ver'    => SIMPLE_PAY_VERSION,
			'footer' => false,
		);

		$scripts['simpay-admin-subs'] = array(
			'src'    => SIMPLE_PAY_ASSETS . 'js/admin-subs' . $min . '.js',
			'deps'   => array( 'simpay-admin' ),
			'ver'    => SIMPLE_PAY_VERSION,
			'footer' => false,
		);

		return $scripts;
	}

	public function add_admin_styles( $styles, $min ) {

		$styles['simpay-jquery-ui-cupertino'] = array(
			'src'   => SIMPLE_PAY_ASSETS . 'css/jquery-ui-cupertino' . $min . '.css',
			'deps'  => array(),
			'ver'   => SIMPLE_PAY_VERSION,
			'media' => 'all',
		);

		$styles['simpay-admin-pro'] = array(
			'src'   => SIMPLE_PAY_ASSETS . 'css/admin-pro' . $min . '.css',
			'deps'  => array(),
			'ver'   => SIMPLE_PAY_VERSION,
			'media' => 'all',
		);

		return $styles;
	}
}
