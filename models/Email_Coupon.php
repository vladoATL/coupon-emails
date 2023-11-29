<?php
namespace COUPONEMAILS;
function register_email_coupon_class()
{
	class Email_Coupon extends \WC_Coupon
	{
/*
|--------------------------------------------------------------------------
| Class Properties
|--------------------------------------------------------------------------
*/

/**
* Stores advanced coupon data.
*
* @since 1.0
* @access private
* @var array
*/
		protected $_data = array();		
		protected $reward_amount;
		protected $reward_discount_type;

/**
* Class constructor.
*
* @since 1.0
* @access public
*
* @param mixed $code WC_Coupon ID, code or object.
* @throws \Exception Error message.
*/
		public function __construct( $code )
		{

			// if provided value is int, then get the equivalent coupon code.
			if ( is_int( $code ) ) {
				$code = absint( $code );
				$this->set_id( $code );
			} elseif ( is_string( $code ) ) {

				// check if code has a valid equivalent ID.
				$temp = wc_get_coupon_id_by_code( $code );
			}

			// make sure that the provided parameter is valid.
			if ( is_a( $code, 'WC_Coupon' ) || is_string( $code ) || is_int( $code ) ) {
				// construct parent object and set the code.
				parent::__construct( $code );
			} else {
				throw new \Exception( 'Invalid parameter provided for Email_Coupon. It either needs a coupon code string, coupon ID, or a WC_Coupon object.' );
			}
			
			$this->reward_amount = $this->get_meta( 'reward_amount', true );
			$this->reward_discount_type = $this->get_meta( 'reward_discount_type', true );
		}

/**
* Is coupon owned by user.
*
* @since 1.2.1
* @access public
*
* @param int $user_id User ID.
*
* @return bool
*/
		public function is_coupon_owned_by_user( $user_id )
		{
			// If $user_id is blank grab current user.
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}
			$user = get_user_by('id', $user_id);
			// Get Owners.
			$owned  = false;
			$owners = $this->get_meta( 'customer_email', false );

			// Return if no owners.
			if ( ! is_array( $owners ) || empty( $owners ) ) {
				return $owned;
			}
			
			$user_emails = maybe_unserialize($owners);
			// Check if user is owner.
			foreach ( $user_emails as $owner ) {
				if ( $user->user_email === $owner ) {
					$owned = true;
					break;
				}
			}

			return $owned;
		}

	
	/**
	* @since 4.5
	* @access public
	*
	* @return string Coupon schedule string.
	*/
	public function get_schedule_string()
	{
		$format         = apply_filters( 'schedule_string_date_format', get_option( 'date_format', 'F j, Y' ) );
		$schedule_end   = $this->get_data()['date_expires']; // $this->get_date_expires();
		$site_timezone  = new \DateTimeZone( Coupon_Emails_Helper_Functions::get_site_current_timezone() );
		$end_datetime   = $schedule_end ? new \WC_DateTime( $schedule_end, $site_timezone ) : null;

		if ( $end_datetime ) {
	/* Translators: %s: Schedule end date. */
	$message = sprintf( esc_html__( 'Valid until %s', 'coupon-emails' ), $end_datetime->date_i18n( $format ) );
			
		} else {
			$message = '';
		}

		return apply_filters( 'coupon_schedule_string', $message , $end_datetime, $format, $this );
	}	
	
	public function get_email_restriction(){
		$restrictions = $this->get_email_restrictions();
		if (!empty($restrictions)) {
			return esc_html__( 'Restricted to', 'coupon-emails' ) . ' ' . $restrictions[0];
		} else {
			return "";
		}
	}
	
	public function get_discount_value_string()
	{
		$coupon_types  = wc_get_coupon_types();
		$discount_type = $this->get_discount_type();
		$message = "";

	/**
	* Get proper discount type label.
	*/
		if ( isset( $coupon_types[ $discount_type ] ) ) {
			$discount_label = apply_filters( 'discount_label', strtolower( $coupon_types[ $discount_type ] ) );
		} else {
			$discount_label = apply_filters( 'discount_label_missing', $this->get_discount_type(), $this );
		}

	/**
	* Get proper amount string format.
	* fixed price values needs to be formatted as price.
	* percent value needs to have the correct formatting.
	*/
		switch ( $discount_type ) {
			case 'percent':
				$amount = sprintf(
				'%s%%',
				wc_trim_zeros(
				number_format(
				$this->get_amount(),
				wc_get_price_decimals(),
				wc_get_price_decimal_separator(),
				wc_get_price_thousand_separator()
				)
				)
				);
				break;
			case 'fixed_cart':
			case 'fixed_product':
				$amount = wc_price( $this->get_amount() );
				break;
			default:
				$amount = apply_filters( 'display_coupon_amount_value', $this->get_amount(), $discount_type, $this );
		}

		return ucfirst( $discount_label) . ": " . $amount ; //  apply_filters( 'single_coupon_discount_value', $message, $amount, $discount_label, $this );
	}
		
	public function get_coupon_url()
	{
		if ( get_post_status( $this->id ) === 'auto-draft' ) {
			return '';
		}

		$coupon_permalink = get_permalink( $this->id, true );
		$slug             =  $this->get_code();

		// sanitize for comma and colon.
		$slug = str_replace( array( ':', ',' ), array( '%3A', '%2C' ), $slug );

		// build permalink.
		$coupon_permalink = str_replace( '%shop_coupon%', $slug, $coupon_permalink );

		return $coupon_permalink;
	}	
	
	public function get_referred_by_id(){
		return get_metadata( 'post', $this->id, "referred_by_id", true );
	}
	
	public function get_reward_amount(){
		return $this->reward_amount;
	}
	
	public function set_reward_amount($reward_amount)
	{
		$this->add_meta_data('reward_amount', $reward_amount);
		$this->reward_amount = $reward_amount;
	}	
	public function get_reward_discount_type()
	{
		return $this->reward_discount_type;
	}

	public function set_reward_discount_type($reward_discount_type)
	{
		$this->add_meta_data('reward_discount_type', $reward_discount_type);
		$this->reward_discount_type = $reward_discount_type;
	}		
}
}
add_action('init', 'COUPONEMAILS\register_email_coupon_class');
?>