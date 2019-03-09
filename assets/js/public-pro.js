/* global simplePayForms, spGeneral, jQuery, Stripe */

var simpayAppPro = {};

( function( $ ) {
	'use strict';

	var body;

	simpayAppPro = {

		// Pro public JS init function simply executes add'l functions when certain events are triggered from Lite public JS.
		init: function() {

			// Set main vars on init.
			body = $( document.body );

			body.on( 'simpayBindCoreFormEventsAndTriggers', function( e, spFormElem, formData ) {

				// Disable all inputs while loading, but don't change the button color.
				simpayAppPro.disableForm( spFormElem, formData, false );

				// Act on the pre-existing custom amount value.
				simpayAppPro.processCustomAmountInput( spFormElem, formData );

				// Update total amount, which in turn sets total & recurring amount labels.
				simpayAppPro.updateTotalAmountLabel( spFormElem, formData );

				simpayAppPro.bindProFormEventsAndTriggers( spFormElem, formData );
				simpayAppPro.handleFieldFocus( spFormElem );
				simpayAppPro.focusFirstField();

				// Initialize date field
				simpayAppPro.initDateField( spFormElem, formData );

				if ( simpayApp.isStripeCheckoutForm( formData ) ) {

					// Enable all inputs after Stripe Checkout form done loading.
					simpayAppPro.enableForm( spFormElem, formData );
				} else {

					// Setup Stripe Elements form (not using Stripe Checkout classic).
					// Core simpayApp.setupStripeCheckout() will not have run.
					// In turn should enable all inputs.
					simpayAppPro.setupStripeElementsForm( spFormElem, formData );
				}
			} );

			body.on( 'simpayFinalizeCoreAmount', simpayAppPro.setProFinalAmount );
		},

		setupStripeElementsForm: function( spFormElem, formData ) {

			var stripeElements, stripeCard,
				errorEl = spFormElem.find( '.simpay-errors' ),
				cardEl = spFormElem.find( '.simpay-card-wrap' );

			// Override button element class that will trigger payment form submit.
			formData.submitBtnClass = 'simpay-checkout-btn';

			// Create a new instance of the Stripe object for each form.
			spFormElem.stripeInstance = Stripe( formData.stripeParams.key );
			stripeElements = spFormElem.stripeInstance.elements();

			stripeCard = simpayAppPro.createCard( spFormElem, cardEl, stripeElements );

			/** Set additional formData properties. */

			// Set the tax amount.
			formData.taxAmount = simpayAppPro.calculateTaxAmount( formData.amount, formData.taxPercent );

			// Set an empty couponCode attribute now
			formData.couponCode = '';

			/** Setup Stripe Elements card field. */

			if ( cardEl.length ) {

				// Validate Elements card field.
				stripeCard.on( 'change', function( e ) {

					// Don't show message unless full number filled out or form submitted.
					if ( e.error ) {
						errorEl.html( e.error.message );
					} else {
						errorEl.empty();
					}
				} );
			}

			// Enable all inputs after Stripe Elements form (especially the Elements card field) is done loading.
			stripeCard.on( 'ready', function( e ) {
				simpayAppPro.enableForm( spFormElem, formData );
			} );

			/** Form submitted through checkout button click or Enter key. */

			function submitElementsForm() {

				// Pass customer name & billing address here as optional Stripe token data.
				// Both are optional. Email is retrieved in handleStripeElementsToken function.
				var billingAddressContainer = spFormElem.find( '.simpay-billing-address-container' );

				var cardData = {
					name: spFormElem.find( '.simpay-customer-name' ).first().val(),
					address_line1: billingAddressContainer.find( '.simpay-address-street' ).val(),
					address_city: billingAddressContainer.find( '.simpay-address-city' ).val(),
					address_state: billingAddressContainer.find( '.simpay-address-state' ).val(),
					address_zip: billingAddressContainer.find( '.simpay-address-zip' ).val(),
					address_country: billingAddressContainer.find( '.simpay-address-country' ).val()
				};

				// Init flag for form validation state.
				formData.isValid = true;

				// Trigger custom event right before executing payment.
				// For Pro version client-side validation and other client-side changes.
				spFormElem.trigger( 'simpayBeforeStripePayment', [ spFormElem, formData ] );

				// Now check validation state flag before continuing.
				if ( !formData.isValid ) {
					return;
				}

				simpayApp.setCoreFinalAmount( spFormElem, formData );

				// Send the final amount to Stripe params.
				// Stripe expects amounts in cents (100 for $1.00 USD / no decimals), so convert here.
				formData.stripeParams.amount = spShared.convertToCents( formData.finalAmount );

				// Set the same cents value to hidden input for later form submission.
				spFormElem.find( '.simpay-amount' ).val( formData.stripeParams.amount );

				// Disable all inputs while submitting & after form validation.
				// Also set submit button loading text & change it's color.
				simpayAppPro.disableForm( spFormElem, formData, true );

				spFormElem.stripeInstance.createToken( stripeCard, cardData ).then( function( result ) {

					if ( result.error ) {

						errorEl.html( result.error.message );
						simpayAppPro.enableForm( spFormElem, formData );
					} else {

						// .token includes card data
						handleStripeElementsToken( result.token );
					}
				} );
			}

			/**
			 * Stripe Elements token handler
			 *
			 * https://stripe.com/docs/stripe-js/reference#stripe-create-token
			 * Stripe Checkout token handler in public.js
			 *
			 * @param token
			 */

			function handleStripeElementsToken( token ) {

				var cusEmail = spFormElem.find( '.simpay-email' ).first().val();

				// Set values to hidden elements to pass via POST when submitting the form for payment.
				$( '<input>' ).attr( {
					type: 'hidden',
					name: 'simpay_stripe_token',
					value: token.id
				} ).appendTo( spFormElem );

				$( '<input>' ).attr( {
					type: 'hidden',
					name: 'simpay_stripe_email',
					value: cusEmail
				} ).appendTo( spFormElem );

				// Reset form submit handler to prevent an infinite loop.
				// Then finally submit the form.
				spFormElem.off( 'submit' );
				spFormElem.submit();
			}

			/** Original form submit handler */

			spFormElem.on( 'submit', function( e ) {
				var plainInputsValid = true;

				e.preventDefault();

				// Trigger HTML5 validation UI on the form if Stripe Elements card input fails validation.
				// Ref form submit handler in https://stripe.github.io/elements-examples/
				spFormElem.find( 'input' ).each( function( i, el ) {

					// "el" is native DOM element here.
					if ( el.checkValidity && !el.checkValidity() ) {
						plainInputsValid = false;
						return;
					}
				} );

				if ( !plainInputsValid ) {
					triggerBrowserValidation();
					return;
				}

				submitElementsForm();
			} );

			// Ref triggerBrowserValidation in https://stripe.github.io/elements-examples/
			function triggerBrowserValidation() {

				// The only way to trigger HTML5 form validation UI is to fake a user submit event.
				var submit = $( '<input>' ).attr( {
					type: 'submit',
					style: 'display: none'
				} ).appendTo( spFormElem );

				submit.click();
				submit.remove();
			}
		},

		isCustomAmountFieldValid: function( spFormElem, formData ) {

			var customAmountInput = spFormElem.find( '.simpay-custom-amount-input' );
			var errorEl = spFormElem.find( '.simpay-errors' );

			var customAmountVal,
				minAmount,
				isValid;

			// Exit if no custom amount field.
			if ( 0 === customAmountInput.length ) {
				return true;
			}

			// Compare amount in cents.
			customAmountVal = spShared.unformatCurrency( customAmountInput.val() );

			// Subscriptions minimum amount requirement is separate from one-time minimum amount requirement.
			if ( formData.isSubscription ) {
				minAmount = spShared.unformatCurrency( formData.subMinAmount );
			} else {
				minAmount = spShared.unformatCurrency( formData.minAmount );
			}

			// Make sure custom amount meets minimum value.
			// Give does: ( ( -1 < amount ) && ( amount >= min_amount ) )
			isValid = ( ( -1 < customAmountVal ) && ( customAmountVal >= minAmount ) );

			if ( isValid ) {

				errorEl.empty();
				customAmountInput.removeClass( 'simpay-input-error' );

				return true;

			} else {

				// Set error message.
				if ( formData.isSubscription ) {
					errorEl.html( formData.subMinCustomAmountError );
				} else {
					errorEl.html( formData.minCustomAmountError );
				}

				// Change amount input border color w/ CSS class.
				customAmountInput.addClass( 'simpay-input-error' );

				return false;
			}
		},

		bindProFormEventsAndTriggers: function( spFormElem, formData ) {

			var customAmountInput = spFormElem.find( '.simpay-custom-amount-input' );

			// Custom amount focus out (blur)
			// Will run after .simpay-amount-input > blur.validateAndUpdateAmount in shared.js
			customAmountInput.on( 'blur.simpayCustomAmountInput', function( e ) {

				simpayAppPro.isCustomAmountFieldValid( spFormElem, formData );

				// Update the custom amount variable to what was entered and then update the total amount label.
				simpayAppPro.processCustomAmountInput( spFormElem, formData );
				simpayAppPro.updateTotalAmountLabel( spFormElem, formData );
			} );

			// Custom amount focus in
			customAmountInput.on( 'focus.simpayCustomAmountInput', function( e ) {
				simpayAppPro.handleCustomAmountFocusIn( spFormElem, formData );
			} );

			// Apply coupons
			spFormElem.find( '.simpay-apply-coupon' ).on( 'click.simpayApplyCoupon', function( e ) {
				e.preventDefault();

				simpayAppPro.applyCoupon( spFormElem, formData );
				simpayAppPro.updateTotalAmountLabel( spFormElem, formData );
			} );

			// Remove Coupon
			spFormElem.find( '.simpay-remove-coupon' ).on( 'click.simpayRemoveCoupon', function( e ) {
				e.preventDefault();

				simpayAppPro.removeCoupon( spFormElem, formData );
			} );

			// Radio and dropdown amount change
			spFormElem.find( '.simpay-amount-dropdown, .simpay-amount-radio' ).on( 'change.simpayAmountSelect', function( e ) {
				simpayAppPro.updateAmountSelect( spFormElem, formData );
			} );

			// Radio and dropdown quantity change
			spFormElem.find( '.simpay-quantity-dropdown, .simpay-quantity-radio' ).on( 'change.simpayQuantitySelect', function() {
				simpayAppPro.updateQuantitySelect( spFormElem, formData );
			} );

			// Number field quantity update
			spFormElem.find( '.simpay-quantity-input' ).on( 'keyup.simpayNumberQuantity, change.simpayNumberQuantity, input.simpayNumberQuantity', function( e ) {
				simpayAppPro.updateQuantitySelect( spFormElem, formData );
				simpayAppPro.updateTotalAmountLabel( spFormElem, formData );
			} );

			// Subscription multi-plan options
			spFormElem.find( '.simpay-multi-sub, .simpay-plan-wrapper select' ).on( 'change.simpayMultiPlan', function( e ) {
				simpayAppPro.changeMultiSubAmount( spFormElem, formData );
			} );

			// Update total amount label when a coupon has been applied or removed.
			// When coupon is applied it retrieves values from Stripe and updates via ajax post.
			spFormElem.on( 'simpayCouponApplied simpayCouponRemoved', function( e ) {
				simpayAppPro.updateTotalAmountLabel( spFormElem, formData );
			} );

			// Update total amount label when a custom field tied to amount or quantity changes,
			// but only if no coupon code is present.
			spFormElem.on( 'simpayDropdownAmountChange simpayDropdownAmountChange simpayRadioAmountChange simpayDropdownQuantityChange simpayRadioQuantityChange simpayNumberQuantityChange simpayMultiPlanChanged', function( e ) {

				if ( ( undefined === formData.couponCode ) || ( '' === formData.couponCode.trim() ) ) {
					simpayAppPro.updateTotalAmountLabel( spFormElem, formData );
				}
			} );

			// Toggle shipping address form based on same address toggle checkbox.
			spFormElem.find( '.simpay-same-address-toggle' ).on( 'change.simpaySameAddressToggle', function( e ) {
				simpayAppPro.toggleShippingAddressFields( spFormElem, formData );
			} );

			spFormElem.on( 'simpayBeforeStripePayment', function( e, spFormElem, formData ) {
				simpayAppPro.beforeSubmitPayment( spFormElem, formData );
			} );

			/** Trigger some events on intial page load. **/

			// TODO Check if all these JS triggers need to fire on init.

			// Trigger change event on dropdown/radio amount field
			spFormElem.find( '.simpay-amount-dropdown, .simpay-amount-radio' ).trigger( 'change.simpayAmountSelect' );

			// Trigger change event on dropdown/radio quantity field
			spFormElem.find( '.simpay-quantity-dropdown, .simpay-quantity-radio' ).trigger( 'change.simpayQuantitySelect' );

			// Trigger multi-plan selection on page load to set initial amount
			spFormElem.find( '.simpay-multi-sub:checked, .simpay-plan-wrapper select:selected' ).trigger( 'change.simpayMultiPlan' );

			// Trigger quantity to update
			spFormElem.find( '.simpay-quantity-input' ).trigger( 'input.simpayNumberQuantity' );

			body.trigger( 'simpayBindProFormEventsAndTriggers', [ spFormElem, formData ] );
		},

		handleFieldFocus: function( spFormElem ) {

			var fields = spFormElem.find( '.simpay-form-control' );

			fields.each( function( i, el ) {
				var field = $( el );

				field.on( 'focusin', setFocus );
				field.on( 'focusout', removeFocus );

				function setFocus() {
					field.addClass( 'is-focused' );
				}

				function removeFocus() {

					// Wait for DatePicker plugin
					setTimeout( function() {
						field.removeClass( 'is-focused' );

						if ( field.val() ) {
							field.addClass( 'is-filled' );
						} else {
							field.removeClass( 'is-filled' );
						}
					}, 300 );
				}
			} );

		},

		createCard: function( spFormElem, cardEl, stripeElements ) {

			var card;

			// Hide postal if Card field verify zip setting is unchecked or...
			var hidePostal = cardEl.data( 'hide-postal' );

			// If a billing address field exists (overrides Card field setting).
			if ( spFormElem.find( '.simpay-address-zip' ).length ) {
				hidePostal = true;
			}

			card = stripeElements.create( 'card', simpayAppPro.getCardConfig( hidePostal ) );

			card.mount( cardEl[ 0 ] );
			simpayAppPro.handleCardFocus( card, cardEl );

			return card;
		},

		// Stripe Elements card input styles should come from _mixings.scss & _variables.scss.
		// Can't set placeholder on combo card field.
		// See https://github.com/moonstonemedia/WP-Simple-Pay-Pro-3/commit/b0a70876389f24fdafc29aeecdc1b9e9637f3fef
		getCardConfig: function( hidePostal ) {
			return {
				hidePostalCode: hidePostal,
				style: {
					base: {
						color: '#32325d', // $input-text-color
						fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif', // @mixin font-checkout()
						fontSize: '15px',
						fontSmoothing: 'antialiased',
						fontWeight: 500,

						'::placeholder': {
							color: '#aab7c4' // $input-placeholder-color
						}
					},
					invalid: {
						color: '#fa755a', // $error-text-color
						iconColor: '#fa755a' // $error-text-color
					}
				}
			};
		},

		handleCardFocus: function( card, cardEl ) {
			card.on( 'focus', function( e ) {
				cardEl.parents( '.simpay-form-control' ).addClass( 'is-focused' );
			} );

			card.on( 'blur', function( e ) {
				cardEl.parents( '.simpay-form-control' ).removeClass( 'is-focused' );
			} );
		},

		focusFirstField: function() {

			// Focus on first field in custom overlay forms, but only upon launch.
			// Do not focus on first field in embedded forms as they could be lower on the page.
			$( '.simpay-modal-control' ).on( 'change', function() {
				var firstModalField = $( this ).next( '.simpay-modal' ).find( 'input' ).first();
				firstModalField.focus();
				firstModalField.select();
				firstModalField.parents( '.simpay-form-control' ).addClass( 'is-focused' );
			} );
		},

		handleCustomAmountFocusIn: function( spFormElem, formData ) {

			var selectOption = spFormElem.find( '.simpay-custom-plan-option' );
			var customAmountInput = spFormElem.find( '.simpay-custom-amount-input' );

			// Check what type of display we are dealing with and select the option accordingly
			if ( selectOption.is( 'input' ) ) {

				// Radio option
				selectOption.prop( 'checked', true );

			} else if ( selectOption.is( 'option' ) ) {

				// Dropdown (select) option
				selectOption.prop( 'selected', true );
			}

			// Update multi-sub amount
			formData.useCustomPlan = true;

			spFormElem.find( '.simpay-has-custom-plan' ).val( 'true' );

			formData.customPlanAmount = spShared.convertToCents( customAmountInput.val() );
		},

		// Actions to perform before final payment submission.
		// Includes client-side validation and other client-side changes.
		// Executed by jQuery trigger before final form submit (Core & Pro).
		beforeSubmitPayment: function( spFormElem, formData ) {

			var billingAddressContainer = spFormElem.find( '.simpay-billing-address-container' );
			var shippingAddressContainer = spFormElem.find( '.simpay-shipping-address-container' );

			// Process custom amount input in case enter key used while focused on input.
			simpayAppPro.processCustomAmountInput( spFormElem, formData );

			// Custom amount validation
			if ( !simpayAppPro.isCustomAmountFieldValid( spFormElem, formData ) ) {
				formData.isValid = false;
				return;
			}

			// Trigger for even more validation if custom needed.
			// TODO Create/modify code snippets for this.
			spFormElem.trigger( 'simpayFormValidationInitialized' );

			// Change the panel label for a trial.
			if ( formData.isTrial && ( 0 === formData.finalAmount ) ) {
				formData.stripeParams.panelLabel = formData.freeTrialButtonText;
			} else {
				formData.stripeParams.panelLabel = formData.oldPanelLabel;
			}

			// Update shipping address fields if marked same as billing address.
			if ( spFormElem.find( '.simpay-same-address-toggle' ).is( ':checked' ) ) {

				shippingAddressContainer.find( '.simpay-address-street' ).val( billingAddressContainer.find( '.simpay-address-street' ).val() );
				shippingAddressContainer.find( '.simpay-address-city' ).val( billingAddressContainer.find( '.simpay-address-city' ).val() );
				shippingAddressContainer.find( '.simpay-address-state' ).val( billingAddressContainer.find( '.simpay-address-state' ).val() );
				shippingAddressContainer.find( '.simpay-address-zip' ).val( billingAddressContainer.find( '.simpay-address-zip' ).val() );
				shippingAddressContainer.find( '.simpay-address-country' ).val( billingAddressContainer.find( '.simpay-address-country' ).val() );

				shippingAddressContainer
					.find( ':disabled' ).removeProp( 'disabled' );
			}
		},

		initDateField: function( spFormElem, formData ) {

			var dateInputEl = spFormElem.find( '.simpay-date-input' );

			dateInputEl.datepicker();
			dateInputEl.datepicker( 'option', 'dateFormat', formData.dateFormat );
		},

		processCustomAmountInput: function( spFormElem, formData ) {

			var customAmountInput = spFormElem.find( '.simpay-custom-amount-input' ),
				unformattedAmount;

			// Exit if no custom amount field.
			if ( 0 === customAmountInput.length ) {
				return;
			}

			unformattedAmount = customAmountInput.val();

			if ( formData.isSubscription ) {

				formData.customAmount = spShared.unformatCurrency( '' !== unformattedAmount ? unformattedAmount : formData.subMinAmount );
				formData.planAmount = formData.customAmount;
				formData.customPlanAmount = formData.planAmount;

			} else {
				formData.customAmount = spShared.unformatCurrency( '' !== unformattedAmount ? unformattedAmount : formData.minAmount );
			}

			// Apply any coupons that may exist
			simpayAppPro.applyCoupon( spFormElem, formData );
		},

		// Additinal calculations for the internal final amount property value.
		// Triggered from Core setCoreFinalAmount() & when custom field is updated.
		// Internal properties are always set in cents format (no decimals).
		setProFinalAmount: function( e, spFormElem, formData ) {

			var tempFinalAmount = formData.amount;

			if ( ( undefined !== formData.customAmount ) && ( formData.customAmount > 0 ) ) {
				tempFinalAmount = formData.customAmount;
			}

			if ( ( 'undefined' !== typeof formData.isSubscription ) && formData.isSubscription ) {

				// Check for single subscription
				if ( 'single' === formData.subscriptionType ) {

					// Check if we are using a custom plan or a regular plan and change amount accordingly
					if ( 'undefined' !== typeof formData.customPlanAmount ) {
						tempFinalAmount = formData.customPlanAmount;
					} else {
						tempFinalAmount = formData.amount;
					}

					// Set planAmount to be used in coupon code calculations
					formData.planAmount = tempFinalAmount;

				} else {

					// Check if we are using a custom plan or a regular plan and change amount accordingly
					if ( ( 'undefined' !== typeof formData.useCustomPlan ) && formData.useCustomPlan ) {
						tempFinalAmount = formData.customPlanAmount;
					} else {
						tempFinalAmount = formData.planAmount;
					}
				}

				if ( formData.isTrial ) {
					tempFinalAmount = 0;
				}

				// TODO DRY setupFee retrieval?

				// Normal setupFee
				if ( 'undefined' !== typeof formData.setupFee ) {

					// Add the total of all setup fees to the finalAmount
					tempFinalAmount = tempFinalAmount + formData.setupFee;
				}

				// Individual plan setupFee
				if ( 'undefined' !== typeof formData.planSetupFee ) {

					// Add the total of all setup fees to the finalAmount
					tempFinalAmount = tempFinalAmount + spShared.unformatCurrency( formData.planSetupFee );
				}
			}

			if ( 'undefined' !== typeof formData.quantity ) {
				tempFinalAmount = tempFinalAmount * formData.quantity;
			}

			// Check for coupon discount
			if ( 'undefined' !== typeof formData.discount ) {
				tempFinalAmount = tempFinalAmount - formData.discount;
			}

			// Only add fee or fee percent if we are not using a subscription
			if ( ( 'undefined' !== typeof formData.isSubscription ) && !formData.isSubscription ) {

				if ( formData.feePercent > 0 ) {
					tempFinalAmount = tempFinalAmount + ( tempFinalAmount * ( formData.feePercent / 100 ) );
				}

				// Add additional fee amount (from user filters currently)
				if ( formData.feeAmount > 0 ) {
					tempFinalAmount = tempFinalAmount + formData.feeAmount;
				}
			}

			if ( formData.taxPercent > 0 ) {

				// For trials, we'll only have an initial tax amount & final amount if there's a setup fee.
				formData.taxAmount = simpayAppPro.calculateTaxAmount( tempFinalAmount, formData.taxPercent );

				// Add final rounded tax amount.
				tempFinalAmount += formData.taxAmount;

				// Set tax amount to hidden input for later form submission.
				spFormElem.find( '.simpay-tax-amount' ).val( formData.taxAmount );
			}

			formData.finalAmount = tempFinalAmount;
		},

		applyCoupon: function( spFormElem, formData ) {

			// Set our variables before we do anything else
			var couponField = spFormElem.find( '.simpay-coupon-field' ),
				data,
				responseContainer = spFormElem.find( '.simpay-coupon-message' ),
				loadingImage = spFormElem.find( '.simpay-coupon-loading' ),
				removeCoupon = spFormElem.find( '.simpay-remove-coupon' ),
				hiddenCouponElem = spFormElem.find( '.simpay-coupon' ),
				couponCode = '',
				couponMessage = '',
				amount = formData.amount,
				setupFee = 0;

			// Make sure a coupon exists either by entry or has not been removed and set the proper couponCode
			if ( !couponField.val() ) {

				if ( !formData.couponCode ) {
					return;
				} else {
					couponCode = formData.couponCode;
				}
			} else {
				couponCode = couponField.val();
			}

			// Check for subscription amount (include setup fee)
			if ( formData.isSubscription ) {

				// TODO DRY setupFee retrieval?

				if ( 'undefined' !== typeof formData.setupFee ) {

					// Normal setup fee
					setupFee = formData.setupFee;

				} else if ( 'undefined' !== typeof formData.planSetupFee ) {

					// Individual plan setup fee
					setupFee = spShared.unformatCurrency( formData.planSetupFee );
				}

				if ( formData.useCustomPlan ) {
					amount = formData.customPlanAmount + setupFee;
				} else {
					amount = formData.planAmount + setupFee;
				}
			} else {

				// Set amount var for non-subscription custom amount.
				if ( ( 'undefined' !== formData.customAmount ) && ( formData.customAmount > 0 ) ) {
					amount = formData.customAmount;
				}
			}

			// Also check for quantity multiplier before calculating discount
			if ( 'undefined' !== typeof formData.quantity ) {
				amount = amount * formData.quantity;
			}

			// AJAX params
			data = {
				action: 'simpay_get_coupon',
				coupon: couponCode,
				amount: amount,
				couponNonce: spFormElem.find( '#simpay_coupon_nonce' ).val()
			};

			// Clear the response container and hide the remove coupon link
			responseContainer.text( '' );
			removeCoupon.hide();

			// Clear textbox
			couponField.val( '' );

			// Show the loading image
			loadingImage.show();

			$.ajax( {
				url: spGeneral.strings.ajaxurl,
				method: 'POST',
				data: data,
				dataType: 'json',
				success: function( response ) {

					// Set the coupon code attached to this form to the couponCode being used here
					formData.couponCode = couponCode;

					// Set an attribute to store the discount so we can subtract it later
					formData.discount = response.discount;

					// Coupon message for frontend
					couponMessage = response.coupon.code + ': ';

					// Output different text based on the type of coupon it is - amount off or a percentage
					if ( 'percent' === response.coupon.type ) {
						couponMessage += response.coupon.amountOff + spGeneral.i18n.couponPercentOffText;
					} else if ( 'amount' === response.coupon.type ) {
						couponMessage += spShared.formatCurrency( response.coupon.amountOff, true ) + ' ' + spGeneral.i18n.couponAmountOffText;
					}

					$( '.coupon-details' ).remove();

					// Update the coupon message text
					responseContainer.append( couponMessage );

					// Create a hidden input to send our coupon details for Stripe metadata purposes
					$( '<input />', {
						name: 'simpay_coupon_details',
						type: 'hidden',
						value: couponMessage,
						class: 'simpay-coupon-details'
					} ).appendTo( responseContainer );

					// Show remove coupon link
					removeCoupon.show();

					// Add the coupon to our hidden element for processing
					hiddenCouponElem.val( couponCode );

					// Hide the loading image
					loadingImage.hide();

					// Trigger custom event when coupon apply done.
					spFormElem.trigger( 'simpayCouponApplied' );
				},
				error: function( response ) {

					var errorMessage = '';

					spShared.debugLog( 'Coupon error', response.responseText );

					if ( response.responseText ) {
						errorMessage = response.responseText;
					}

					// Show invalid coupon message
					responseContainer.append( $( '<p />' ).addClass( 'simpay-field-error' ).text( errorMessage ) );

					// Hide loading image
					loadingImage.hide();
				}
			} );
		},

		removeCoupon: function( spFormElem, formData ) {

			spFormElem.find( '.simpay-coupon-loading' ).hide();
			spFormElem.find( '.simpay-remove-coupon' ).hide();
			spFormElem.find( '.simpay-coupon-message' ).text( '' );
			spFormElem.find( '.simpay-coupon' ).val( '' );

			formData.couponCode = '';
			formData.discount = 0;

			// Trigger custom event when coupon apply done.
			spFormElem.trigger( 'simpayCouponRemoved' );
		},

		updateAmountSelect: function( spFormElem, formData ) {

			if ( spFormElem.find( '.simpay-amount-dropdown' ).length ) {

				// Update the amount to the selected dropdown amount
				formData.amount = spFormElem.find( '.simpay-amount-dropdown' ).find( 'option:selected' ).data( 'amount' );
				spFormElem.trigger( 'simpayDropdownAmountChange' );

			} else if ( spFormElem.find( '.simpay-amount-radio' ) ) {

				// Update the amount to the selected radio button
				formData.amount = spFormElem.find( '.simpay-amount-radio' ).find( 'input[type="radio"]:checked' ).data( 'amount' );
				spFormElem.trigger( 'simpayRadioAmountChange' );
			}

			// Update any coupons
			simpayAppPro.applyCoupon( spFormElem, formData );
		},

		updateQuantitySelect: function( spFormElem, formData ) {

			formData.quantity = 1;

			if ( spFormElem.find( '.simpay-quantity-dropdown' ).length ) {

				// Update the amount to the selected dropdown amount
				formData.quantity = parseFloat( spFormElem.find( '.simpay-quantity-dropdown' ).find( 'option:selected' ).data( 'quantity' ) );
				spFormElem.trigger( 'simpayDropdownQuantityChange' );

			} else if ( spFormElem.find( '.simpay-quantity-radio' ).length ) {

				// Update the amount to the selected radio button
				formData.quantity = parseFloat( spFormElem.find( '.simpay-quantity-radio' ).find( 'input[type="radio"]:checked' ).data( 'quantity' ) );
				spFormElem.trigger( 'simpayRadioQuantityChange' );

			} else if ( spFormElem.find( '.simpay-quantity-input' ).length ) {

				formData.quantity = parseFloat( spFormElem.find( '.simpay-quantity-input' ).val() );
				spFormElem.trigger( 'simpayNumberQuantityChange' );
			}

			if ( formData.quantity < 1 ) {
				formData.quantity = 1;
			}

			// Update hidden quantity field
			spFormElem.find( '.simpay-quantity' ).val( formData.quantity );

			// Apply any coupons
			simpayAppPro.applyCoupon( spFormElem, formData );
		},

		updateTotalAmountLabel: function( spFormElem, formData ) {

			var totalLabelText;

			simpayApp.setCoreFinalAmount( spFormElem, formData );

			// Convert amount to dollars (decimals) & add currency symbol, etc.
			totalLabelText = spShared.formatCurrency( formData.finalAmount, true );
			spFormElem.find( '.simpay-total-amount-value' ).text( totalLabelText );

			simpayAppPro.updateRecurringAmountLabel( spFormElem, formData );
			simpayAppPro.updateTaxAmountLabel( spFormElem, formData );
		},

		updateRecurringAmountLabel: function( spFormElem, formData ) {

			// Don't include setup fee in recurring amount or when calculating recurring tax amount.
			// TODO Need to adjust for once vs multi-month/forever coupons. #360

			var recurringBaseAmount = formData.planAmount * formData.quantity;
			var recurringTaxAmount = simpayAppPro.calculateTaxAmount( recurringBaseAmount, formData.taxPercent );
			var recurringAmountFinal = recurringBaseAmount + recurringTaxAmount;
			var recurringAmountFormatted = spShared.formatCurrency( recurringAmountFinal, true );

			if ( formData.planIntervalCount > 1 ) {
				recurringAmountFormatted += ' every ' + formData.planIntervalCount + ' ' + formData.planInterval + 's';
			} else {
				recurringAmountFormatted +=  '/' + formData.planInterval ;
			}

			spFormElem.find( '.simpay-total-amount-recurring-value' ).text( recurringAmountFormatted );
		},

		updateTaxAmountLabel: function( spFormElem, formData ) {
			spFormElem.find( '.simpay-tax-amount-value' ).text( spShared.formatCurrency( formData.taxAmount, true ) );
		},

		calculateTaxAmount: function( amount, percent ) {
			return Math.abs( accounting.toFixed( amount * ( percent / 100 ), spGeneral.integers.decimalPlaces ) );
		},

		changeMultiSubAmount: function( spFormElem, formData ) {

			var selectedOption = '',
				errorEl = spFormElem.find( '.simpay-errors' ),
				customAmountInput = spFormElem.find( '.simpay-custom-amount-input' ),
				wrapperElem = spFormElem.find( '.simpay-plan-wrapper' ),
				options = wrapperElem.find( '.simpay-multi-sub' ),
				planId,
				planSetupFee,
				planAmount,
				planInterval,
				planIntervalCount,
				planTrial,
				planMaxCharges;

			// Check if it is a dropdown or a radio button setup and act accordingly
			if ( options.first().is( 'option' ) ) {

				// Dropdown
				selectedOption = options.filter( ':selected' );
			} else {

				// Radio buttons
				selectedOption = options.filter( ':checked' );
			}

			planId = selectedOption.data( 'plan-id' ) || '';
			planSetupFee = selectedOption.data( 'plan-setup-fee' ) || 0;
			planAmount = selectedOption.data( 'plan-amount' )  || 0;
			planInterval = selectedOption.data( 'plan-interval' ) || '';
			planIntervalCount = selectedOption.data( 'plan-interval-count' ) || 1;
			planTrial = ( undefined !== selectedOption.data( 'plan-trial' ) );
			planMaxCharges = selectedOption.data( 'plan-max-charges' ) || 0;

			if ( planTrial ) {
				formData.amount = 0;
			}

			// Update formData plan attributes
			formData.planId = planId;
			formData.planSetupFee = planSetupFee; // Add the overall setup fee + the individual setup fee together for one total setupFee amount
			formData.planAmount = planAmount;
			formData.planInterval = planInterval;
			formData.planIntervalCount = planIntervalCount;
			formData.isTrial = planTrial;

			// Update custom amount checker
			if ( selectedOption.hasClass( 'simpay-custom-plan-option' ) ) {
				spFormElem.find( '.simpay-has-custom-plan' ).val( 'true' );
			} else {
				spFormElem.find( '.simpay-has-custom-plan' ).val( '' );
			}

			// Reset custom amount validation.
			errorEl.empty();
			customAmountInput.removeClass( 'simpay-input-error' );

			formData.useCustomPlan = ( 'simpay_custom_plan' === selectedOption.val() );

			// If the custom amount plan is selected, focus input & blank out value.
			// If an existing plan is selected, don't focus input & set input to plan value.
			if ( formData.useCustomPlan ) {
				customAmountInput.val( '' );
				customAmountInput.focus();
			} else {
				customAmountInput.val( spShared.formatCurrency( planAmount ) );
			}

			// Update hidden fields
			spFormElem.find( '.simpay-multi-plan-id' ).val( planId );
			spFormElem.find( '.simpay-multi-plan-setup-fee' ).val( planSetupFee );
			spFormElem.find( '.simpay-max-charges' ).val( planMaxCharges );

			// Custom trigger after completed
			spFormElem.trigger( 'simpayMultiPlanChanged' );

			// Apply any coupons entered
			simpayAppPro.applyCoupon( spFormElem, formData );
		},

		toggleShippingAddressFields: function( spFormElem, formData ) {

			var shippingAddressContainer = spFormElem.find( '.simpay-shipping-address-container' );

			// Show (and enable) or hide (and disabled) shipping address fields based on same address checkbox value.
			if ( spFormElem.find( '.simpay-same-address-toggle' ).is( ':checked' ) ) {

				shippingAddressContainer
					.hide()
					.find( ':enabled' ).prop( 'disabled', true );
			} else {

				shippingAddressContainer
					.show()
					.find( ':disabled' ).removeProp( 'disabled' );
			}
		},

		disableForm: function( spFormElem, formData, setSubmitButtonAsLoading ) {

			var submitBtn = spFormElem.find( '.simpay-checkout-btn' );

			// Disable the form submit button upon initial form load or form submission.
			submitBtn.prop( 'disabled', true );

			// When submitting the form only, set it's loading text and add a class for UI feedback
			// while POST-ing to Stripe.
			if ( true === setSubmitButtonAsLoading ) {

				submitBtn.addClass( 'simpay-disabled' )
					.find( 'span' ).text( formData.loadingText );
			}

			// TODO Can't disable form elements where their values need to be retrieved from a form post. Ref #582, #584
			//spFormElem.find( ':enabled' ).not( ':hidden' ).prop( 'disabled', true );
		},

		enableForm: function( spFormElem, formData ) {

			// Reverse what disableForm function does.
			var submitBtn = spFormElem.find( '.simpay-checkout-btn' );

			// Re-enable button
			submitBtn.removeProp( 'disabled' )
				.removeClass( 'simpay-disabled' );

			// Reset button text back to original if needed during validation.
			if ( formData.loadingText == submitBtn.find( 'span' ).text() ) {
				submitBtn.find( 'span' ).text( formData.checkoutButtonText );
			}

			// Enable all disabled form elements (except hidden fields).
			spFormElem.find( ':disabled' ).removeProp( 'disabled' );
		}
	};

	// Call init directly instead of doc ready as it needs to establish triggers after Lite public JS has run.
	simpayAppPro.init();

}( jQuery ) );
