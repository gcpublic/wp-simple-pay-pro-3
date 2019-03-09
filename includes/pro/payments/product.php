<?php

namespace SimplePay\Pro\Payments;

use SimplePay\Core\Payments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plan
 *
 * @package SimplePay\Payments
 *
 * Wrapper of Stripe API Product class. Used to get information about products.
 */
class Product {

	/**
	 * Product constructor.
	 */
	public function __construct() {

	}

	/**
	 * Remove a specific product by ID
	 *
	 * @param $product_id - ID of the product to remove
	 */
	public static function remove( $product_id ) {

		$product = Payments\Stripe_API::request( 'Product', 'retrieve', $product_id );

		$product->delete();
	}
}
