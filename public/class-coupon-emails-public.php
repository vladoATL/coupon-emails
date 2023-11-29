<?php
namespace COUPONEMAILS;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://starlogic.net
 * @since      1.2.1
 *
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/public
 */

class Coupon_Emails_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.2.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.2.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.2.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.2.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coupon-emails-public.css', array(), $this->version, 'all' );
	}

	function account_coupons_page()
	{
		$card = new \COUPONEMAILS\Coupon_Emails_Coupon_Card();
		$args = array(
		'owned'       => $card->get_coupon_cards_owned_by_customer( array( 'display_type' => 'owned', 'columns'      => 2,)),
		'used' 		  => $card->get_coupon_cards_used( array( 'columns' => 2 ) ),		
		'expired'     => $card->get_coupon_cards_owned_by_customer( array( 'display_type' => 'expired', 'columns'      => 2,)),
		'labels'      => array(
			'expired'   => esc_html__( 'Unused / Expired Coupons', 'coupon-emails' ),
			'owned'       => esc_html__( 'Applicable coupons', 'coupon-emails' ),
			'used' => esc_html__( 'Used Coupons', 'coupon-emails' ),
			'none'        => esc_html__( 'You have no coupons.', 'coupon-emails' ),
			),
		);

		Coupon_Emails_Helper_Functions::load_template( 'account-coupons.php', $args);		
	}
	
	function referral_page()
	{
		include('partials/referral-public-display.php'); 	
	}
	
	function account_coupons_page_add_endpoint()
	{
		add_rewrite_endpoint( 'account-coupons',  EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}	
	
	function referral_page_add_endpoint()
	{
		add_rewrite_endpoint( 'referral',  EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}
		
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.2.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coupon-emails-public.js', array( 'jquery' ), $this->version, false );
	}
	
	public function coupon_emails_order_status_changed($order_id, $old_status, $new_status, $order)
	{
		if (( $new_status == "completed" )
			|| ( $old_status == "completed" )) {
			//your code here
			$applied_coupons = $order->get_coupon_codes() ;
			
			foreach ($applied_coupons as $code) {
				if (\COUPONEMAILS\Coupon_Emails_EmailFunctions::is_coupon_in_category($code, 'referralemail')) {
					$minus = 1;
					if ( $old_status == "completed" ) {
						$minus = -1;
					}

					$order_total = ($order->get_total() - $order->get_shipping_total()) * $minus;
					$coupon = new \COUPONEMAILS\Email_Coupon($code);
					$referred_by_id = $coupon-> get_referred_by_id();
					$user = wp_get_current_user();
					$display_name = $user->display_name;
					$referral = new \COUPONEMAILS\Coupon_Emails_Referral($referred_by_id);
					$c = $referral->update_referral_coupon($code,$display_name,$order_total);
					if ($minus > 0) {
						$d = $referral->send_referral_order_confirmation($order_total, $user);
					}
				}
			}
		}		
	}

}
