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

coupon_emails_activation();
delete_option('couponemails_options');
delete_option('couponemails_logs');
$fun = new \COUPONEMAILS\Coupon_Emails_EmailFunctions();
$types = $fun->get_types();
foreach ($types as $type) {
	delete_option($type . '_options');	
}
