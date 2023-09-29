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
delete_option('namedayemail_options');
delete_option('onetimeemail_options');
delete_option('reorderemail_options');
delete_option('birthdayemail_options');
delete_option('afterorderemail_options');
delete_option('reviewedemail_options')
delete_option('reviewreminderemail_options');
delete_option('expirationreminderemail_options');
delete_option('referralemail_options');
delete_option('referralconfirmationemail_options');