<?php
/**
 * Plugin Name: WP Simple Pay Pro 3
 * Plugin URI:  https://wpsimplepay.com
 * Description: Add high conversion Stripe payment forms to your WordPress site in minutes.
 * Author: WP Simple Pay
 * Author URI:  https://wpsimplepay.com
 * Version: 3.3.6
 * Text Domain: simple-pay
 * Domain Path: /i18n
 */

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright 2014-2018 Moonstone Media Group. All rights reserved.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SIMPLE_PAY_VERSION' ) ) {
	define( 'SIMPLE_PAY_VERSION', '3.3.6' );
}

if ( ! defined( 'SIMPLE_PAY_PLUGIN_NAME' ) ) {
	define( "SIMPLE_PAY_PLUGIN_NAME", 'WP Simple Pay Pro 3' );
}

// Stripe API version should be in 'YYYY-MM-DD' format.
if ( ! defined( 'SIMPLE_PAY_STRIPE_API_VERSION' ) ) {
	define( 'SIMPLE_PAY_STRIPE_API_VERSION', '2018-11-08' );
}

if ( ! defined( 'SIMPLE_PAY_MAIN_FILE' ) ) {
	define( 'SIMPLE_PAY_MAIN_FILE', __FILE__ );
}

if ( ! defined( 'SIMPLE_PAY_URL' ) ) {
	define( 'SIMPLE_PAY_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SIMPLE_PAY_ASSETS' ) ) {
	define( 'SIMPLE_PAY_ASSETS', plugin_dir_url( __FILE__ ) . 'assets/' );
}

if ( ! defined( 'SIMPLE_PAY_DIR' ) ) {
	define( 'SIMPLE_PAY_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SIMPLE_PAY_INC' ) ) {
	define( 'SIMPLE_PAY_INC', plugin_dir_path( __FILE__ ) . 'includes/' );
}

if ( ! defined( 'SIMPLE_PAY_STORE_URL' ) ) {
	define( 'SIMPLE_PAY_STORE_URL', 'https://wpsimplepay.com/' );
}

if ( ! defined( 'SIMPLE_PAY_ITEM_NAME' ) ) {
	define( 'SIMPLE_PAY_ITEM_NAME', 'WP Simple Pay Pro 3' );
}

if ( ! defined( 'SIMPLE_PAY_ITEM_ID' ) ) {
	define( 'SIMPLE_PAY_ITEM_ID', 177993 );
}

if ( ! defined( 'SIMPLE_PAY_MIN_PHP_VER' ) ) {
	define( 'SIMPLE_PAY_MIN_PHP_VER', '5.4' );
}

/**
 * Show warning if Lite version is active.
 */
function simpay_deactivate_lite_notice() {
	?>

	<div class="error">
		<p>
			<?php printf( __( 'You must <a href="%1$s">deactivate WP Simple Pay Lite</a> in order to use %2$s.', 'simple-pay' ), wp_nonce_url( 'plugins.php?action=deactivate&plugin=stripe%2Fstripe-checkout.php&plugin_status=all&paged=1&s=', 'deactivate-plugin_stripe/stripe-checkout.php' ), SIMPLE_PAY_ITEM_NAME ); ?>
		</p>
	</div>

	<?php
}

if ( class_exists( 'SimplePay\Core\SimplePay' ) ) {
	add_action( 'admin_notices', 'simpay_deactivate_lite_notice' );

	return;
}

/**
 * Show an error message for PHP version < SIMPLE_PAY_MIN_PHP_VER and don't load the plugin.
 */
if ( ! function_exists( 'simpay_admin_php_notice' ) ) {
	function simpay_admin_php_notice() {
		?>

		<div class="error">
			<p>
				<?php printf( esc_html__( '%1$s requires %2$s or higher.', 'simple-pay' ), SIMPLE_PAY_ITEM_NAME, 'PHP ' . SIMPLE_PAY_MIN_PHP_VER ); ?>
			</p>
		</div>

		<?php
	}
}

if ( version_compare( PHP_VERSION, SIMPLE_PAY_MIN_PHP_VER, '<' ) ) {
	add_action( 'admin_notices', 'simpay_admin_php_notice' );

	return;
}

// Autoloader
require_once( SIMPLE_PAY_DIR . 'vendor/autoload.php' );
require_once( SIMPLE_PAY_INC . 'autoload.php' );

// Core & Pro main plugin files
require_once( SIMPLE_PAY_INC . 'core/main.php' );
require_once( SIMPLE_PAY_INC . 'pro/main.php' );
