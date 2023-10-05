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
 * Description:       Generate emails with unique coupons for birthdays, name days, after placing an order, send reminders, referral email and more with many customization options.
 * Version:           1.4.5
 * Author:            Vlado Laco
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coupon-emails
 * Domain Path:       /languages
 * Date of Start:	  16.8.2023
 */

namespace COUPONEMAILS;
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COUPON_EMAILS_VERSION', '1.4.5.1' );
define( 'MAX_TEST_EMAILS', '10' );
if (!str_contains(get_home_url(), 'test') && !str_contains(get_home_url(), 'stage') ) {
	define( 'ENABLE_SQL_LOGS', '0' );
} else {
	define( 'ENABLE_SQL_LOGS', '1' );
}
define( 'PREFIX_BASE_PATH', plugin_dir_path( __FILE__ ) );


add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );


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

register_activation_hook( __FILE__, '\COUPONEMAILS\activate_coupon_emails' );
register_deactivation_hook( __FILE__, '\COUPONEMAILS\deactivate_coupon_emails' );
register_deactivation_hook( __FILE__, '\COUPONEMAILS\couponemails_plugin_deactivation' );
register_activation_hook( __FILE__, '\COUPONEMAILS\couponemails_plugin_save_defaults' );


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
require_once plugin_dir_path( __FILE__ ) . 	'includes/class-expiration-reminder.php';
require_once plugin_dir_path( __FILE__ ) . 	'includes/class-review-reminder.php';

require_once plugin_dir_path( __FILE__ ) . 	'helpers/class-helper-functions.php';
require_once plugin_dir_path( __FILE__ ) . 	'models/Coupon_Card.php';
require_once plugin_dir_path( __FILE__ ) . 	'models/Email_Coupon.php';

require_once plugin_dir_path( __FILE__ ) .  'public/includes/class-referral.php';


\COUPONEMAILS\BirthdayField::register();


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), '\COUPONEMAILS\couponemails_settings_link' );

$options = get_option('couponemails_options');
$show_account_coupons = isset($options["show_account_coupons"]) ? $options["show_account_coupons"] : 0;
if ($show_account_coupons) {
	add_filter( 'woocommerce_account_menu_items',  '\COUPONEMAILS\register_my_coupons_menu_item', 10  );
} else {
	add_filter( 'woocommerce_account_menu_items', '\COUPONEMAILS\remove_account_coupons_menu_item' );
}

$enable_referral = isset($options["enable_referral"]) ? $options["enable_referral"] : 0;
if ($enable_referral) {
	add_filter( 'woocommerce_account_menu_items',  '\COUPONEMAILS\register_referral_menu_item', 10  );
} else {
	add_filter( 'woocommerce_account_menu_items', '\COUPONEMAILS\remove_referral_menu_item' );
}


function remove_referral_menu_item( $menu_links )
{
	unset( $menu_links[ 'referral' ] );
	return $menu_links;
}

function remove_account_coupons_menu_item( $menu_links )
{
	unset( $menu_links[ 'account-coupons' ] );
	return $menu_links;
}

function register_referral_menu_item( array $items )
{	 
	if ( ! is_user_logged_in() ) {
		return $items;
	}
	
	$filtered_items = array();
	foreach ( $items as $key => $item ) {
		$filtered_items[ $key ] = $item;
		// insert my accounts menu item after account details.
		if ( 'edit-account' === $key ) {
			$filtered_items[ 'referral' ] = __( 'My referrals','coupon-emails' );
		}
	}

	return $filtered_items;
}

function register_my_coupons_menu_item( array $items )
{
	if ( ! is_user_logged_in() ) {
		return $items;
	}

	$filtered_items = array();
	foreach ( $items as $key => $item ) {
		$filtered_items[ $key ] = $item;
		// insert my accounts menu item after account details.
		if ( 'edit-account' === $key ) {
			if (! is_plugin_active('advanced-coupons-for-woocommerce/advanced-coupons-for-woocommerce.php')) {
				$filtered_items[ 'account-coupons' ] = __( 'My Coupons','coupon-emails' );
			} else {
				unset( $filtered_items[ 'account-coupons' ] );
			}
		}
	}

	return $filtered_items;
}

function couponemails_plugin_save_defaults() {
    	namedayemail_save_defaults(true);	
		birthdayemail_save_defaults(true);
		reorderemail_save_defaults(true);
		afterorderemail_save_defaults(true);
		onetimeemail_save_defaults(true);
		couponemails_save_defaults(true);
		reviewreminderemail_save_defaults(true);
		expirationreminderemail_save_defaults(true);
		referralemail_save_defaults(true);
		referralconfirmationemail_save_defaults(true);
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
	'display_dob_fields'  =>	1,
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
<p style='font-size: 14px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
<p style='font-size: 14px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
	<p style='font-size: 18px;'>Thank for your order dated {last_order_date}. If you like our products, take advantage of this offer and order again. Here is the discount code:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
	<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special <strong>{percent}%</strong> discount on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px;font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Thanks for shopping with us {last_order_date}, {fname}!</p>
<p style='font-size: 18px;'>If you like our products, take advantage of this special offer. Login back into our store {site_name_url} and let others know what do you think of your purchase.</p>
<p style='font-size: 19px;font-weight:600;'>As a thank you, we'll send you a discount code that you can use the next time you buy our products from us.</p>
<p style='font-size: 18px;'>We'd love to hear your feedback.</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px; line-height: 95%;'>Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
	'send_time'  =>	'02:45',
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Don't throw away an excellent opportunity to save, {fname}!</p>
	<p style='font-size: 18px;'>We've recently sent you a rare discount coupon for your next purchase, which expires in {expires_in_days} days on {expires}:</p>
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>Come back to our website {site_name_url} and save!</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px; line-height: 95%;'>The validity of the voucher cannot be extended and can only be redeemed from the account of {email}.</p>" ,'Email Body', 'coupon-emails'	),
'coupon_cat' =>	_x('Expiration reminder','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'expirationreminderemail_options', $option_array );
	} else {
		update_option( 'expirationreminderemail_options', $option_array );
	}
}

function referralconfirmationemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, here is your reward!','Email Subject','coupon-emails') ,
	'header'  =>	_x('Thanks for recommendation!','Email Header','coupon-emails') ,
	'wc_template' =>	1,
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>We received an order from your friend {friend_firstname} {friend_lastname}!</p>
<p style='font-size: 18px;'>Thank you for recommending our products. As a gift for you, we have added {reward_amount} to the value of your coupon:</p> 
<p style='font-size: 24px;font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>Its total value is now {coupon_amount} and it is valid until {expires}.</p>
<p style='font-size: 18px;'>Come back to our website {site_name_url} and save!</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 14px; line-height: 95%;'>The validity of the voucher cannot be extended and can only be redeemed from the account of {email}.</p>" ,'Email Body', 'coupon-emails'	),
	);
	if ($add_new == true) {
		add_option( 'referralconfirmationemail_options', $option_array );
	} else {
		update_option( 'referralconfirmationemail_options', $option_array );
	}	
}

function referralemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'headline'	=>	_x("Send an email with a discount coupon to friends who don't know about us yet.",'My Account page','coupon-emails') ,
	'ref_explanation'	=>	_x("This is a coupon you can use on your next purchase with us. Its value increases every time someone uses the coupon from the email you sent. Your coupon will expire 30 days after the last time you send a referral email.",'My Account page','coupon-emails') ,
	'directions'	=>	_x("In the box below, enter the email addresses, separated by a comma, of friends to whom you want to send a recommendation of our products with a coupon for a 10% discount on their first purchase. Enter your personal text and press the Send button. If they send us an order, we will credit you 5% of the value of the products ordered. ",'My Account page','coupon-emails') ,
	'ref_description'  =>	_x('Referrer: {email}','Email Header','coupon-emails') ,
	'subject'	=>	_x('Hi, this is what I bought','Email Subject','coupon-emails') ,
	'header'  =>	_x('I recommend it!','Email Header','coupon-emails') ,
	'characters' =>	7,
	'ref_characters' =>	7,
	'wc_template' =>	1,
	'expires'	=>	14,
	'ref_expires'	=>	30,
	'disc_type' => 1,
	'ref_disc_type' => 1,
	'exclude_discounted' => 1,
	'ref_exclude_discounted' => 1,
	'ref_individual_use' => 1,
	'individual_use' => 1,
	'description' => _x('Referral: {email}','Coupon description','coupon-emails') ,
	'coupon_amount'	=>	10,
	'ref_coupon_amount'	=>	5,	
	'from_name'	=>	get_bloginfo('name'),
	'from_address'	=>	get_bloginfo('admin_email'),
	'bcc_address' => $current_user->user_email,
	'email_footer' => '{site_name_url}',
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>I want to share with you my satisfaction with great products!</p>
<p style='font-size: 18px;'>I am also sending a coupon for a {percent}% discount, which can be redeemed at {site_name_url} for the following {expires_in_days} days until {expires}:</p>
<div style='font-size: 24px;font-weight:800;'>{coupon}</div>
<div style='font-size: 18px;font-weight:600;'>{personal_text}</div>
<p style='font-size: 18px;'>Visit this website {site_url} and save!<br>
Greetings,<br>{referrer}</p><br>
<div style='font-size: 14px; line-height: 95%;'>Some products are excluded from the discount. The validity of the voucher cannot be extended.</div>" ,'Email Body', 'coupon-emails'	),
	'ref_coupon_cat' =>	_x('Referrer','Coupon category', 'coupon-emails'	) ,
	'coupon_cat' =>	_x('Referred','Coupon category', 'coupon-emails'	) ,
	);
	if ($add_new == true) {
		add_option( 'referralemail_options', $option_array );
	} else {
		update_option( 'referralemail_options', $option_array );
	}
}



function onetimeemail_save_defaults($add_new = false)
{
	$current_user = wp_get_current_user();

	$option_array = array(
	'subject'	=>	_x('{fname}, here is your discount coupon','Email Subject','coupon-emails') ,
	'header'  =>	_x('We are missing you!','Email Header','coupon-emails') ,
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
	'email_body'	=> _x("<p style='font-size: 20px;font-weight:600;'>Come back to our site, {fname}! We have a special price on our products for you!</p>
<p style='font-size: 18px;'>Take advantage of this discount code:</p>
<p style='font-size: 24px; font-weight:800;'>{coupon}</p>
<p style='font-size: 18px;'>During the next {expires_in_days} days (until {expires}) you can use it in our online store {site_name_url} and get a special discount of <strong>{percent}%</strong> on {products_cnt} non-discounted products.</p>
<p style='font-size: 18px; font-weight:600;'>ENJOY !</p>
<p style='font-size: 18px;'>The Team of {site_name}</p>
<p style='font-size: 16px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>
<p style='font-size: 18px; line-height: 95%;'>Login procedure on our website:<br>
1. click on My Account<br>
2. click on 'Lost password' and you will be able to choose your password to log in.</p>" ,'Email Body', 'coupon-emails'	) ,
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
				'expires'	=>	30,
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
<p style='font-size: 14px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
	'expires'	=>	30,
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
<p style='font-size: 14px; line-height: 95%;'>The coupon can only be used after logging into your account and cannot be used with other discounts. Some products are excluded from the discount.</p>" ,'Email Body', 'coupon-emails'	) ,
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
