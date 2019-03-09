=== WP Simple Pay Pro 3 ===
Requires at least: 4.8
Tested up to: 5.0
Requires PHP: 5.4
License: GPLv2 or later

== Description ==

WP Simple Pay Pro 3 - Add Stripe payment and subscription forms to your WordPress site in minutes.

== Changelog ==

= 3.3.6 - January 8, 2019 =

* Fix: Adjust form object to ID conversion.
* Tweak: Allow entering of Stripe API keys manually before first time authorizing with Stripe Connect.

= 3.3.5 - January 6, 2019 =

* Fix: Corrected tax amount calculation regardless of comma or period decimal separator.
* Fix: Corrected Stripe Checkout amount when using enter key to submit immediately after entering a new custom amount.
* Fix: Payment form rendered more than the intended amount when used multiple times on the same page.
* Tweak: When custom amount input type set to "number" using a filter, the step attribute will be set to "0.01" to allow decimals.
* Dev: Check and convert form object to ID when necessary.

= 3.3.4 - December 28, 2018 =

* New: You can now easily connect your Stripe account with Stripe Connect. See your settings page for more details.
* New: Display admin notice about upcoming PHP 5.6 requirement if running an older version.
* Fix: Don't set focus to the first field in embedded forms (only in overlay forms).
* Fix: Reset checkout button text back to original after card validation fails.
* Fix: Prevent JavaScript errors when trying to validate hidden shipping address fields.
* Fix: Prevent Stripe.js version conflict with Restrict Content Pro (and possibly other plugins using Stripe.js).
* Fix: Correct rounding issue in total amount label with subscription plans that contain cents.
* Dev: Updated non-specific form filter hooks with added form ID.
* Dev: Added filter hook for additional custom field types.

= 3.3.3 - December 5, 2018 =

* Fix: Coupon codes used with set single plan subscription forms improperly calculated.
* Fix: Trial subscriptions were charging immediately in some cases.
* Fix: Setup fees for user-selectable subscriptions miscalculated in the initial payment label.
* Fix: Allow decimal numbers to be used for quantity multiplier custom fields.

= 3.3.2 - December 3, 2018 =

* Fix: Error adding metadata to invoices after finalizing in some cases.

= 3.3.1 - December 2, 2018 =

* Fix: Amount off coupon codes improperly calculated.
* Fix: Embedded and Overlay custom fields not saving to Stripe metadata.

= 3.3.0 - November 27, 2018 =

* New: Added embedded and overlay custom form display options. See https://wpsimplepay.com/embedded-overlay-custom-form-display/
* Dev: Remove excessive post meta calls.
* Tweak: Updated to Stripe PHP library v6.23.0.

= 3.2.5 - November 12, 2018 =

* Fix: Incorrect variable name in new beta opt-in option.

= 3.2.4 - November 12, 2018 =

* New: Added setting to opt into WP Simple Pay Pro beta releases.

= 3.2.3 - October 3, 2018 =

* Dev: Added filter hook to add or modify arguments when a Stripe customer is created.
* Dev: Added filter hook to add or modify arguments when a Stripe subscription is created.
* Dev: Added filter hook to allow additional attributes to be added to the payment form tag.
* Dev: Updated and hardened webhook code incorporating updates as of Stripe API 2018-09-06.
* Dev: Updated to Stripe PHP library v6.19.1.

= 3.2.2 - August 3, 2018 =

* Fix: Subscriptions with trials incorrectly charging right away as of Stripe API 2018-05-21.
* Dev: Updated to Stripe PHP library v6.15.0.

= 3.2.1 - July 27, 2018 =

* Fix: Recurring amount label doesn't properly factor in discounts from repeating or multi-month coupon codes.
* Fix: Sanitize custom amount plan IDs so they only contain alphanumeric and _- characters on creation (added in Stripe API 2018-02-05).
* Dev: System report: Update Stripe API endpoint for the TLS 1.2 compatibility check.
* Dev: Updated to Stripe PHP library v6.11.0.

= 3.2.0 - July 13, 2018 =

* Fix: Custom amount 1-cent rounding error in some cases.
* Fix: Adding metadata to invoices after sending causes PHP error in some cases.
* Fix: Displayed tax amount, total amount and recurring amount labels weren't accurate in some cases.
* Tweak: Display more than 100 subscription (product) plans when selecting within payment form settings.
* Dev: Updated to Stripe PHP library v6.10.2.
* Dev: Updated Accounting JS & Chosen JS libraries.

= 3.1.19 - May 15, 2018 =

* Tweak: PHP 5.4 or higher now required.
* Tweak: If the custom amount plan is selected in a user-selects plan form, focus input and blank out value.
* Dev: Updated to Stripe PHP library v6.7.1.

= 3.1.18 - April 2, 2018 =

* Fix: Detection and warning about upcoming PHP 5.4 requirement.
* Fix: Error when activating plugin with WP-CLI.
* Tweak: PHP 5.3 or higher now required.
* Tweak: Removed Bitcoin support inline with Stripe (https://stripe.com/blog/ending-bitcoin-support).

= 3.1.17 - March 20, 2018 =

* Fix: Integrate Stripe Plans with Stripe Products as introduced in Stripe API 2018-02-05.
* Tweak: Only check software license in admin max once per day.
* Dev: System report: Add mbstring (Multibyte String) check.

= 3.1.16 - February 22, 2018 =

* Feature: Add Romanian translation.
* Fix: "Start your free trial" checkout button text not appearing when a tax rate is in place.
* Fix: PHP error with software licensing updater in some cases.
* Fix: Dequeue all public CSS when "Default Plugin Styles" option is disabled.
* Dev: Updated to Stripe PHP library v5.9.2.

= 3.1.15 - January 23, 2018 =

* Fix: Detection and warning about PHP 5.3 requirement.
* Fix: Load custom sessions class in admin as it's still used for subscription plan setup fees.

= 3.1.14 - January 22, 2018 =

* Fix: Fix and simplify payment form previews.
* Fix: Translations weren't getting loaded properly (missing load_plugin_textdomain call).
* Dev: Don't load custom sessions class in admin.
* Dev: Updated to Stripe PHP library v5.9.0.

= 3.1.13 - January 3, 2018 =

* Fix: (Better) session handling to work across various hosts. Back to using the current version of WP Session Manager (https://github.com/ericmann/wp-session-manager) (2.0.2) with the option of switching to native PHP sessions.
* Fix: Force use of native PHP sessions when hosting with Pantheon.
* Fix: Custom fields (except for first) were losing metabox settings content.
* Dev: Updated to Stripe PHP library v5.8.0.
* Dev: Updated jQuery Validation & Chosen JS libraries.

= 3.1.12 - December 12, 2017 =

* Fix: Check for an existing session before starting a new one.

= 3.1.11 - December 12, 2017 =

* Fix: Session handling updated to work across various managed hosts. Now uses code from WP Native PHP Sessions (https://github.com/pantheon-systems/wp-native-php-sessions) over previously used WP Session Manager (https://github.com/ericmann/wp-session-manager).
* Fix: PHP 7.2 incompatibility - Misuse of reserved keyword "Object".
* Dev: Added filters to provide alternate custom field front-end HTML and admin UI.
* Dev: Updated action fired after a subscription is created to include the initial charge.
* Dev: Added action hook for adding metabox setting panel content.
* Dev: Added jQuery trigger to fire after front-end form validation is setup (in order to add custom validation rules).
* Dev: Updated to Stripe PHP library v5.7.0.

= 3.1.10 - November 20, 2017 =

* Fix: Recurring total amount miscalculation when tax in use.
* Fix: Ordering of plans when User Selects Plan option is set was not saving the order correctly in some cases.
* Fix: jQuery conflict where "jQuery" prefix was improperly used instead of "$".
* Fix: Payment receipt session error message produced by a shortcode was improperly appearing in WP admin.

= 3.1.9 - November 13, 2017 =

* Fix: Duplicate field rendering when custom amount field present and amount type toggled off.
* Fix: Tax metadata not saving when custom fields are present.
* Fix: Rounding issue with tax percent set to 4 decimals used with zero-decimal currencies.
* Fix: Coupon code now works with recurring toggle.
* Dev: Updated to Stripe PHP library v5.6.0.
* Dev: Updated to EDD SL Plugin Updater v1.6.15.

= 3.1.8 - October 31, 2017 =

* Fix: Add metadata for tax percent option.
* Fix: Recurring toggle now works properly.
* Fix: Default for total amount now defaults to minimum amount when using a custom amount input.
* UI: Stay on selected form settings tab after saving.
* UI: Updated grammar in error message.
* Dev: Added simpay_charge_error_message filter.
* Dev: Updated to Stripe PHP library v5.5.1.

= 3.1.7 - October 25, 2017 =

* Feature: Added option to set the payment success page (or redirect URL) per payment form.
* Fix: Super rare case where a certain amount value was off by one cent when using dropdowns as an amount field.
* Fix: Add fallback check for wp_doing_ajax() introduced in WP 4.7.
* Dev: Better handling of alternate Stripe API keys.
* Dev: Add better extensibility to webhook handling with some new and refined action hooks.
* Dev: Updated to Stripe PHP library v5.4.0.

= 3.1.6 - September 28, 2017 =

* Feature: Add support for zero amount and less than 50 currency unit subscription plans.
* Fix: Refresh license key check on license page to make it easier for upgraded accounts to see changes right away.
* Fix: Make sure automatic updates work on multi-site.
* Tweak: Add an error message if trying to activate Pro with Lite already installed.
* Dev: Overhaul to plugin code structure.
* Dev: Add filter simpay_payment_button_class to add or remove classes from the on-page form payment button.
* Dev: Add metadata to the charge_created and subscription_created action hooks.
* Dev: Updated to Stripe PHP library v5.2.3.

= 3.1.5 - September 8, 2017 =

* Fix: Metadata values for radio field + custom amount will show the label instead of "on".
* Fix: Prevent activation when WP Simple Pay Lite is active to avoid a fatal error.

= 3.1.4 - August 29, 2017 =

* Fix: Live mode keys will now load properly.
* Updated to Stripe PHP library v5.2.0.

= 3.1.3 - August 28, 2017 =

* Fix: Numeric only plan IDs will now work correctly.
* Tweak: JavaScript updates to improve performance.
* Dev: Update success page redirect filter to allow for external URLs.
* Dev: Make simpay_fee_amount filter also work as a form-specific filter.
* Dev: Make simpay_fee_percent filter also work as a form-specific filter.
* Dev: Add simpay_plan_name_label filter.
* Updated to Stripe PHP library v5.1.3.

= 3.1.2 - July 24, 2017 =

* Fix: Correct a JavaScript bug that was breaking forms.

= 3.1.1 - July 24, 2017 =

* Feature: Added setting to control Tax Rate Percent.
* Fix: Fix bug with invoices showing an initial $0.00 charge in some cases.
* Fix: Send Stripe API Version information with requests.
* Fix: Remove payment confirmation pages on full uninstall.
* Tweak: Automatic cache exclusion for payment confirmation pages.
* Dev: Add simpay_cache_exclusion_uris filter.
* Dev: Add per-form filter for new tax percent setting.

= 3.1.0 - July 13, 2017 =

* Feature: Installment plans added for subscriptions.
* Feature: Add a setting to control free trial button text.
* Fix: Remove support for Alipay since it is no longer supported through Stripe Checkout.
* Fix: Added plugin information to Stripe API calls.
* Tweak: Make recurring total label show the correct amount when multiplied by a quantity.
* UI: Minor tweaks to the multi-plan admin area.
* Dev: Updated to Stripe PHP library v5.1.1.

= 3.0.3 - June 29, 2017 =

* UI: Update field label description for checkbox custom field.
* UI: Add a placeholder setting for coupon fields.
* Fix: Make sure metadata gets added to the subscription if it has a trial period.
* Fix: Get the processing text setting to work correctly.
* Dev: Add 3 new action hooks.
* Dev: Updated to Stripe PHP library v5.0.0.

= 3.0.2 - June 21, 2017 =

* Fix: Make trial details template load correctly for multi-plans.

= 3.0.1 - June 21, 2017 =

* Fix: Bug with fee amount filter causing issues with zero-decimal currencies.
* Fix: Subscription custom amount field will properly take the default value.
* Fix: Custom amount default fields can now be left blank.
* Fix: Allow HTML in the custom field checkbox label.
* Fix: Checkout overlay will load properly now in IE.
* Dev: Updated to Stripe PHP library v4.13.0.

= 3.0.0 - June 13, 2017 =

* A brand spankin' new rewrite from the ground up. Too many updates to list here.

== Upgrade Notice ==
