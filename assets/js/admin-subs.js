/* global jQuery */

var spSubAdmin = {};

( function( $ ) {
	'use strict';

	var body,
		spSubSettings;

	spSubAdmin = {

		init: function() {

			// We need to initialize these here because that's when the document is finally ready
			body = $( document.body );
			spSubSettings = body.find( '#subscription-options-settings-panel' );

			this.loadMultiPlanSubscriptions();

			// Initialize sortable fields for multi-plans
			this.initSortablePlans( spSubSettings.find( '.simpay-multi-subscriptions tbody' ) );

			// Add plan button
			spSubSettings.find( '.simpay-add-plan' ).on( 'click.simpayAddPlan', function( e ) {

				e.preventDefault();

				spSubAdmin.addPlan( e );
			} );

			// Remove Plan action
			spSubSettings.find( '.simpay-panel-field' ).on( 'click.simpayRemovePlan', '.simpay-remove-plan', function( e ) {
				spSubAdmin.removePlan( $( this ), e );
			} );

			// Update default subscription
			spSubSettings.find( '.simpay-multi-subscriptions' ).on( 'click.simpayUpdateDefaultPlan', '.simpay-multi-plan-default input[type="radio"]', function( e ) {
				spSubAdmin.updateDefaultPlan( $( this ) );
			} );

			// Trigger update of plan ID on change of select
			spSubSettings.find( '.simpay-multi-subscriptions' ).on( 'change.simpayUpdatePlanSelect', '.simpay-multi-plan-select', function( e ) {
				spSubAdmin.updatePlanSelect( $( this ) );
			} );

			// Enable/Disable single subscription plan dropdown
			spSubSettings.find( '#_subscription_custom_amount' ).find( 'input[type="radio"]' ).on( 'change.simpayToggleSubscription', function( e ) {
				spSubAdmin.togglePlans( $( this ) );
			} );

			// Trigger for default plan value if none are selected
			if ( '' === spSubSettings.find( '#simpay-multi-plan-default-value' ).val() ) {
				spSubSettings.find( '.simpay-multi-plan-default input[type="radio"]:first' ).trigger( 'click.simpayUpdateDefaultPlan' );
			}
		},

		initSortablePlans: function( el ) {

			el.sortable( {
				items: 'tr',
				cursor: 'move',
				axis: 'y',
				handle: 'td.sort-handle',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				stop: function( e, ui ) {
					spSubAdmin.orderPlans();
				}
			} );
		},

		loadMultiPlanSubscriptions: function() {

			var simpayPlans = spSubSettings.find( '.simpay-multi-sub' ).get();

			simpayPlans.sort( function( a, b ) {
				var compA = parseInt( $( a ).attr( 'rel' ), 10 );
				var compB = parseInt( $( b ).attr( 'rel' ), 10 );
				return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
			} );

			spSubSettings.find( simpayPlans ).each( function( idx, itm ) {
				spSubSettings.find( '.simpay-multi-subscriptions tbody' ).append( itm );
			} );
		},

		togglePlans: function( el ) {

			// TODO DRY

			if ( 'enabled' === el.val() && el.is( ':checked' ) ) {
				body.find( '#_single_plan' ).prop( 'disabled', true ).trigger( 'chosen:updated' );
			} else {
				body.find( '#_single_plan' ).removeProp( 'disabled' ).trigger( 'chosen:updated' );
			}
		},

		updatePlanSelect: function( el ) {

			var fieldKey = el.parent().data( 'field-key' );

			if ( spSubSettings.find( '#simpay-subscription-multi-plan-default-' + fieldKey + '-yes' ).is( ':checked' ) ) {
				spSubSettings.find( '#simpay-multi-plan-default-value' ).val( el.find( 'option:selected' ).val() );
			}
		},

		updateDefaultPlan: function( el ) {

			var plan = el.closest( '.simpay-multi-plan-default' ).parent().find( '.simpay-multi-plan-select' ).find( 'option:selected' ).val();

			spSubSettings.find( '#simpay-multi-plan-default-value' ).val( plan );
		},

		orderPlans: function() {

			spSubSettings.find( '.simpay-multi-sub' ).each( function( index, el ) {

				var planIndex = parseInt( $( el ).index( '.simpay-multi-sub' ) );

				spSubSettings.find( '.plan-order', el ).val( planIndex );
			} );
		},

		addPlan: function( e ) {

			var wrapper = spSubSettings.find( '.simpay-multi-subscriptions tbody' ); // Main table
			var currentKey = parseInt( spSubSettings.find( '.simpay-multi-sub:last' ).data( 'field-key' ) ) + 1; // Counter from tr

			var data = {
				action: 'simpay_add_plan',
				counter: currentKey,
				addPlanNonce: body.find( '#simpay_add_plan_nonce' ).val()
			};

			e.preventDefault();

			$.ajax( {
				url: ajaxurl,
				method: 'POST',
				data: data,
				success: function( response ) {

					wrapper.append( response );
					wrapper.find( 'select' ).chosen();

				},
				error: function( response ) {
					spShared.debugLog( response );
				}
			} );
		},

		removePlan: function( el, e ) {
			e.preventDefault();

			el.closest( '.simpay-multi-sub' ).remove();
		}
	};

	$( document ).ready( function( $ ) {
		spSubAdmin.init();
	} );

}( jQuery ) );
