<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://perties.sk
 * @since      1.0.0
 *
 * @package    Coupon_Emails
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('couponemails_options');
delete_option('couponemails_logs');

delete_option("couponemails_nameday_options");
delete_option("couponemails_birthday_options");
delete_option("couponemails_reorder_options");
delete_option("couponemails_onetimecoupon_options");
delete_option("couponemails_onetimeemail_options");
delete_option("couponemails_referralconfirmation_options");
delete_option("couponemails_afterorder_options");
delete_option("couponemails_reviewed_options");
delete_option("couponemails_expirationreminder_options"); 
delete_option("couponemails_referralemail_options");
delete_option("couponemails_reviewreminder_options");

