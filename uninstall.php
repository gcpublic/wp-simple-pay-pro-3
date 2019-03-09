<?php

// Exit if not uninstalling from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$general = get_option( 'simpay_settings_general' );

// Check save settings option before removing everything
if ( ! isset( $general['general_misc']['save_settings'] ) ) {

	// First remove the payment confirmation pages
	$success_page = $general['general']['success_page'];
	$failure_page = $general['general']['failure_page'];

	wp_delete_post( $success_page, true );
	wp_delete_post( $failure_page, true );

	// Remove main options
	delete_option( 'simpay_settings' );

	// Remove misc options
	delete_option( 'simpay_license_key' );
	delete_option( 'simpay_license_data' );
	delete_option( 'simpay_license_next_check' );
	delete_option( 'simpay_check_license_timer' );
	delete_option( 'simpay_use_php_sessions' );

	delete_option( 'simpay_dismiss_keys_changed' );
	delete_option( 'simpay_dismiss_mode_changed' );
	delete_option( 'simpay_dismiss_ssl' );
	delete_option( 'simpay_dismiss_dropping_php53_bitcoin' );

	delete_option( 'simpay_test_mode_changed' );
	delete_option( 'simpay_live_keys_changed' );
	delete_option( 'simpay_test_keys_changed' );

	delete_option( 'simpay_version' );
	delete_option( 'simpay_version_upgraded_from' );

	// Remove settings options
	delete_option( 'simpay_settings_general' );
	delete_option( 'simpay_settings_keys' );
	delete_option( 'simpay_settings_payment_methods' );
	delete_option( 'simpay_settings_display' );
	delete_option( 'simpay_settings_shipping_billing' );
	delete_option( 'simpay_settings_license' );

	delete_option( 'simpay_stripe_connect_account_id' );

	// Remove legacy options
	delete_option( 'simpay_preview_form_id' );

	// Delete form posts.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'simple-pay' );" );

	// Delete forms postmeta.
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

}
