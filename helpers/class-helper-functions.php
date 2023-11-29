<?php
namespace COUPONEMAILS;
use Automattic\WooCommerce\Utilities\OrderUtil;

class Coupon_Emails_Helper_Functions
{
	static function	is_HPOS_in_use(){		
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			return true;
		} else {
			// Traditional CPT-based orders are in use.
			return false;
		}		
	}
	
	static function get_site_current_timezone()
	{
		// if site timezone string exists, return it.
		$timezone = trim( get_option( 'timezone_string' ) );
		if ( $timezone ) {
			return $timezone;
		}

		// get UTC offset, if it isn't set then return UTC.
		$utc_offset = trim( get_option( 'gmt_offset', 0 ) );

		if ( filter_var( $utc_offset, FILTER_VALIDATE_INT ) === 0 || '' === $utc_offset || is_null( $utc_offset ) ) {
			return 'UTC';
		}

		return $this->convert_utc_offset_to_timezone( $utc_offset );
	}

	static function load_template( $template, $args = array() )
	{
		$path = COUPON_EMAILS_PREFIX_BASE_PATH . 'public/templates/';
		wc_get_template( $template, $args, '', $path );
	}
	
	static function create_user_data($email)
	{
		$obj= new \stdClass();
		$obj->user_email = $email;
		$obj->user_firstname = '';
		$obj->user_lastname = '';
		$obj->ID = NULL;
		$obj->coupon = '';
		
		return $obj;
	}
	
}
?>