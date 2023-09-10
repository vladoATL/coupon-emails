<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             0.3.0
 * @package           Coupon_Emails
 *
 * @wordpress-plugin
 * Plugin Name:       Coupon Emails
 * Description:       Automatically generates emails with unique coupons for customers' birthdays, their name days and after makeing an order with many customization options.
 * Version:           1.0.2
 * Author:            Vlado Laco
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coupon-emails
 * Domain Path:       /languages
 * Date of Start:	  16.8.2023
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COUPON_EMAILS_VERSION', '1.0.2.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-coupon-emails-activator.php
 */
function activate_coupon_emails() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails-activator.php';
	Coupon_Email_Activator::activate();	
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-coupon-emails-deactivator.php
 */
function deactivate_coupon_emails() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails-deactivator.php';
	Coupon_Email_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_coupon_emails' );
register_deactivation_hook( __FILE__, 'deactivate_coupon_emails' );
register_deactivation_hook( __FILE__, 'couponemails_plugin_deactivation' );
register_activation_hook( __FILE__, 'couponemails_plugin_save_defaults' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails-functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails-inflection.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon-emails-cron.php';

require_once plugin_dir_path( __FILE__ ) .  'includes/class-birthdays.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-afterorder.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-namedays.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-birthdayfield.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-reorders.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-onetimes.php';
require_once plugin_dir_path( __FILE__ ) .  'includes/class-calendars.php';
require_once plugin_dir_path( __FILE__ ) . 	'includes/class-prepare-sql.php';
require_once plugin_dir_path( __FILE__ ) . 	'includes/class-reviewed.php';


\COUPONEMAILS\BirthdayField::register();


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'couponemails_settings_link' );

function couponemails_plugin_save_defaults() {
    	namedayemail_save_defaults(true);	
		birthdayemail_save_defaults(true);
		reorderemail_save_defaults(true);
		afterorderemail_save_defaults(true);
		onetimeemail_save_defaults(true);
		couponemails_save_defaults(true);
		reviewreminderemail_save_defaults(true);
		expirationreminderemail_save_defaults(true);
}

function couponemails_save_defaults($add_new = false)
{
	$option_array = array(
	'enable_logs'	=>	1,
	);
	if ($add_new == true) {
		add_option( 'couponemails_options', $option_array );
	} else {
		update_option( 'couponemails_options', $option_array );
	}
}

function birthdayemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, here is your birthday gift','Email Subject','coupon-emails') ,
	'header'  =>	_x('Congratulations','Email Header','coupon-emails') ,
	'days_before'	=>	1,
	'characters' =>	7,
	'wc_template' =>	1,
	'once_year' =>	1,
	'test' =>	1,
	'send_time'  =>	'05:30',
	'expires'	=>	14,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'disc_type' => 1,
	'individual_use' => 1,
	'description' => _x('Birthday {fname} {lname}: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	25,
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Have a nice birthday, {fname}!</p>
<p style='font-size: 18px;'>Take advantage of this birthday discount code:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special <strong>{percent}%</strong> discount on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('Birth day email','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'birthdayemail_options', $option_array );
	} else {
		update_option( 'birthdayemail_options', $option_array );
	}
}

function reorderemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x("{fname}, it's time to order with discount","Email Subject","coupon-emails") ,
	'header'  =>	_x('Your discount','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'test' =>	1,
	'days_after_order' =>	365,
	'send_time'  =>	'06:00',
	'expires'	=>	14,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'disc_type' => 1,
	'individual_use' => 1,
	'description' => _x('Reorder {fname} {lname}: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	10,
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>We have a special discount for you, {fname}!</p>
	<p style='font-size: 18px;'>Last time you ordered with us {last_order_date}. Take advantage of this  discount code and order again:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special <strong>{percent}%</strong> discount on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('Reorder email','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'reorderemail_options', $option_array );
	} else {
		update_option( 'reorderemail_options', $option_array );
	}
}

function afterorderemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, thank you for your order','Email Subject','coupon-emails') ,
	'header'  =>	_x('Your discount','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'test' =>	1,
	'days_after_order' =>	7,
	'send_time'  =>	'05:30',	
	'expires'	=>	14,
	'minimum_orders' => 1,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'disc_type' => 1,
	'individual_use' => 1,
	'description' => _x('After order {fname} {lname}: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	15,
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>We have a special discount for you, {fname}!</p>
	<p style='font-size: 18px;'>Thank you
	for your order dated  {last_order_date}. If you like our products, take advantage of this offer and order again. Here is the discount code:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
	<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special <strong>{percent}%</strong> discount on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('After order email','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'afterorderemail_options', $option_array );
	} else {
		update_option( 'afterorderemail_options', $option_array );
	}
}

function reviewreminderemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, share your thoughts!','Email Subject','coupon-emails') ,
	'header'  =>	_x('Your discount','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'test' =>	1,
	'days_after_order' =>	4,
	'send_time'  =>	'02:30',
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Thanks for shopping with us, {fname}!</p>
<p style='font-size: 18px;'>If you like our products, take advantage of this special offer. Login back into our store {site_name_url} and let others know what do you think of your purchase.</p>
<p style='font-size: 19px;font-weight:600;'>As a thank you, we'll send you a discount code that you can use the next time you buy our products from us.</p>
<p style='font-size: 18px;'>We'd love to hear your feedback.</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'reviewreminderemail_options', $option_array );
	} else {
		update_option( 'reviewreminderemail_options', $option_array );
	}
}

function expirationreminderemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, your coupon expires soon!','Email Subject','coupon-emails') ,
	'header'  =>	_x('Hurry up!','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'test' =>	1,
	'days_before' =>	1,
	'send_time'  =>	'01:30',
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Don't throw away an excellent opportunity to save, {fname}!</p>
	<p style='font-size: 18px;'>We've recently sent you a rare discount coupon for your next purchase, which expires on {for_date}:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>Come back to our website {site_name_url} and save!</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The validity of the voucher cannot be extended and can only be redeemed from the account of {email}.</p>" ,'Email Body', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'expirationreminderemail_options', $option_array );
	} else {
		update_option( 'expirationreminderemail_options', $option_array );
	}
}

function onetimeemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, here is your discount coupon','Email Subject','coupon-emails') ,
	'header'  =>	_x('Your discount','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'roles' => array('customer'),
	'test' =>	1,
	'expires'	=>	14,
	'minimum_orders' => 1,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'disc_type' => 1,
	'individual_use' => 1,
	'description' => _x('One time {fname} {lname}: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	15,
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>We have a special discount for you, {fname}!</p>
<p style='font-size: 18px;'>Take advantage of this discount code:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special discount of <strong>{percent}%</strong> on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('One-time email','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'onetimeemail_options', $option_array );
	} else {
		update_option( 'onetimeemail_options', $option_array );
	}
}

function namedayemail_save_defaults($add_new = false){
			$current_user = wp_get_current_user();
			
			$option_array = array(
				'subject'	=>	_x('{fname}, here is your name day gift','Email Subject','coupon-emails') ,
				'header'  =>	_x('Congratulations','Email Header','coupon-emails') ,
				'days_before'	=>	1,
				'characters' =>	7,
				'wc_template' =>	1,
				'test' =>	1,
				'send_time'  =>	'05:00',
				'expires'	=>	31,
				'from_name'	=>	get_bloginfo('name'),
				'from_address'	=>	get_bloginfo('admin_email'),
				'bcc_address' => $current_user->user_email,
				'email_footer' => '{site_name_url}',
				'disc_type' => 1,
				'individual_use' => 1,
				'description' => _x('Name Day {fname}: {email}','Coupon description','coupon-emails') ,
				'coupon_amount'	=>	10,
				'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Have a nice name day, {fname}!</p>
<p style='font-size: 18px;'>Take advantage of this name day discount code:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days you can use it in our online store {site_name_url} and get a special discount of <strong>{percent}%</strong> on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ALL THE BEST !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
				'coupon_cat' =>	_x('Name day email','Coupon category', 'coupon-emails'	) ,
			);
			if ($add_new == true) {
				add_option( 'namedayemail_options', $option_array );	
			} else {
				update_option( 'namedayemail_options', $option_array );	
			}	
	}

function reviewedemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, thank you for your review','Email Subject','coupon-emails') ,
	'header'  =>	_x('Here is your gift','Email Header','coupon-emails') ,
	'characters' =>	7,
	'wc_template' =>	1,
	'test' =>	1,
	'send_time'  =>	'06:30',
	'expires'	=>	31,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'disc_type' => 1,
	'individual_use' => 1,
	'description' => _x('After review {fname} {lname}: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	10,
	'email_body'	=> _x("<p style='font-size: 18px;'>{fname}, thank you for your review of our product</p>
<p style='font-size: 20px;font-weight:600;'>{reviewed_prod}</p>
<p style='font-size: 18px;'>Take advantage of this thank you discount code for your next purchase:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special discount of <strong>{percent}%</strong> on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ALL THE BEST !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded  from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('After Review','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'reviewedemail_options', $option_array );
	} else {
		update_option( 'reviewedemail_options', $option_array );
	}
}
	

function couponemails_plugin_deactivation() {
    wp_clear_scheduled_hook( 'namedayemail_cron' );
	wp_clear_scheduled_hook( 'birthdayemail_cron' );
	wp_clear_scheduled_hook( 'reorderemail_cron' );
	wp_clear_scheduled_hook( 'onetimeemail_cron' );
}

function couponemails_settings_link( array $links ) {
    $url = get_admin_url() . "admin.php?&page=couponemails";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'woocommerce') . '</a>';
      $links[] = $settings_link;
    return $links;
  } 
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_coupon_emails() {

	$plugin = new Coupon_Emails();
	$plugin->run();

}
run_coupon_emails();
