<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://perties.sk
 * @since      1.0.0
 *
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/includes
 * @author     Vlado Laco <vlado@perties.sk>
 */
class Coupon_Email_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		add_filter( 'woocommerce_account_menu_items', 'remove_account_coupons_links' );
	}
	
	function remove_account_coupons_links( $menu_links )
	{
		unset( $menu_links[ 'account-coupons' ] ); 
		return $menu_links;
	}
}
