<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the max length for metadata fields
 */
function simpay_metadata_title_length() {
	return 40;
}

/**
 * Get the max length for the metadata description
 *
 * @return int
 */
function simpay_metadata_description_length() {
	return 500;
}

/**
 * Handle metadata truncation using calls to other DRY functions
 *
 * @param $type
 * @param $value
 *
 * @return bool|string
 */
function simpay_truncate_metadata( $type, $value ) {

	switch ( $type ) {
		case 'title':
			return substr( $value, 0, simpay_metadata_title_length() );
		case 'description':
			return substr( $value, 0, simpay_metadata_description_length() );
		default:
			return $value;
	}
}

/**
 * Calculate the tax of an amount when passed in the percentage value. Defaults to the form amount and tax_percent.
 *
 * @param string $amount
 * @param string $tax_percent
 *
 * @return string
 */
function simpay_calculate_tax_amount( $amount = '', $tax_percent = '' ) {

	global $simpay_form;

	// If the global does not exist and one of the parameters wasn't passed in then we leave now
	if ( ! isset( $simpay_form ) && ( empty( $amount ) || empty( $tax_percent ) ) ) {
		return 0;
	}

	if ( empty( $amount ) ) {
		$amount = simpay_unformat_currency( $simpay_form->amount );
	}

	if ( empty( $tax_percent ) ) {
		$tax_percent = floatval( $simpay_form->tax_percent );
	}

	$retval = round( $amount * ( $tax_percent / 100 ), simpay_get_decimal_places() );

	return $retval;
}

/**
 * Get the separator to use for fields that list multiple values
 * Affected Custom Fields: Dropdown values/amounts/quantities, radio values/amounts/quantities
 */
function simpay_list_separator() {
	return apply_filters( 'simpay_list_separator', ',' );
}

/**
 * Get the stored date format for the datepicker
 *
 * @return string
 */
function simpay_get_date_format() {

	$date_format = simpay_get_setting( 'date_format' );
	$date_format = ! empty( $date_format ) ? $date_format : 'mm/dd/yy';

	return $date_format;
}

