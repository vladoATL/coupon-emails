<?php
namespace COUPONEMAILS;

class Coupon_Emails_Referral
{	
	protected $options;
	
	public $user_id;
	public $user_email;	
	public $user;
	public $referral_coupon_id;
	public $referral_coupon;
	protected $option_name;
	protected $ref_option_name;
	protected $confirm_option_name;
	public $email_error;
	
	public function __construct( $user_id = NULL )
	{	
		
		$this->option_name = "couponemails_referralemail";
		$this->ref_option_name = "referral";
		$this->confirm_option_name = "couponemails_referralconfirmation";
		$this->options = get_option($this->option_name . '_options');
		
		if ($user_id == NULL) {
			$current_user = wp_get_current_user();
			$this->user_id = $current_user->ID;
			$this->user_email = $current_user->user_email;
			$this->user = $current_user;
		} else {
			$user = get_user_by('id', $user_id);
			if ($user) {
				$this->user_id = $user->ID;
				$this->user_email = $user->user_email;
				$this->user = $user;
			}		
		}
		$this->referral_coupon_id = $this->get_referral_coupon_id();
		$this->referral_coupon =  $this->get_referral_coupon();
		$this->email_error = "";
	}
	
	public function create_referral_coupon($user = NULL){
		if ($user == NULL) {
			$user = wp_get_current_user();
			$is_referral = true;
		}
		
		if (empty($this->referral_coupon_id)) {
			$funcs = new \COUPONEMAILS\Coupon_Emails_EmailFunctions($this->option_name);
			$ref_coupon_code = $funcs->couponemails_get_unique_coupon($user, "ref_", "referral");

			if (! empty($ref_coupon_code)) {
				$referral_copon = new \COUPONEMAILS\Email_Coupon($ref_coupon_code);
				$referral_copon->set_reward_amount($referral_copon->get_amount());
				$referral_copon->set_reward_discount_type($referral_copon->get_discount_type());
				$referral_copon->set_discount_type('fixed_cart');
				$referral_copon->set_amount(0);
				$referral_copon->save();
			}
			$this->referral_coupon_id = $this->get_referral_coupon_id();
			$this->referral_coupon =  $this->get_referral_coupon();			
		}
	}
	
	public function is_email_registered($email)
	{
		$multiple = isset($this->options['multiple']) ? $this->options['multiple'] : 0;
		if (! $multiple) {			
			$funcs = new \COUPONEMAILS\Coupon_Emails_PrepareSQL($this->ref_option_name);
			
			$coupons = $funcs->get_coupon_codes_for_email($email, $this->ref_option_name);	

			if ($coupons) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function create_referral_couponemail($email, $html_body)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->email_error .= sprintf(esc_html__( "%s is invalid email format.", 'coupon-emails' ), $email) . "<br>";
			return;
		}
		
		if ($this->is_email_registered($email)) {
			$this->email_error .= sprintf(esc_html__( "A user with email %s has already received a referral email.", 'coupon-emails' ), $email) . "<br>";
			return; 
		}
		if (email_exists( $email ) && ! $this->options["existing"]) {
			$this->email_error .= sprintf(esc_html__( "The user with the email %s is already registered on our website.", 'coupon-emails' ), $email) . "<br>";
			return; 
		}
		$funcs = new \COUPONEMAILS\Coupon_Emails_EmailFunctions($this->option_name);
		$user = \COUPONEMAILS\Coupon_Emails_Helper_Functions::create_user_data($email);
		$coupon = $funcs->couponemails_create( $user, false, "" , $html_body);

		$coupon_array = array("coupon_id" => $funcs->new_coupon_id, 
						"coupon_code" => $funcs->coupon_code, 
						"created" => wp_date('j.n.Y'),
						"coupon_expiration" =>   $funcs->coupon_expiration, 
						"referred_by" => $this->user_email,
						"email" => $email);	
						
						
		add_post_meta( $funcs->new_coupon_id, 'referred_by_id', $this->user_id );
		add_post_meta( $this->referral_coupon_id, 'referral', $coupon_array );
		
		return $funcs;
	}
	
	public function get_referred_coupons()
	{
		$refs	= get_metadata( 'post', $this->referral_coupon_id,  "referral"  );
		return $refs;
	}		
	
	public function send_referral_order_confirmation($order_total, $referred_user)
	{
		
		$confirm_option  = get_option($this->confirm_option_name . '_options');
		if (isset($confirm_option['enabled']) && $confirm_option['enabled'] == 1) {
			//$refs = $this->get_referred_coupons();
			$amount = $this->referral_coupon->get_reward_amount();
			$coupon_amount = $this->referral_coupon->get_amount() ;
			$expires = $this->referral_coupon->get_date_expires();
			$coupon_code = $this->referral_coupon->get_code();
			
			switch ( $this->referral_coupon->get_reward_discount_type() ) {
				case 'percent':
					$reward_amount = $order_total / 100 * $amount;
					break;
				case 'fixed_cart':
				case 'fixed_product':
					$reward_amount = $amount;
					break;
				default:
					$reward_amount = $amount;
			}
		
			$funcs = new \COUPONEMAILS\Coupon_Emails_EmailFunctions($this->confirm_option_name);
			$args = array(
			"coupon" 		=> strtoupper($coupon_code),
			"expires"		=> date_i18n('j. F Y', $expires),
			"reward_amount"	=> wc_price($reward_amount),
			"coupon_amount"	=> wc_price($coupon_amount),
			"friend_firstname"	=> $referred_user->user_firstname,
			"friend_lastname"	=> $referred_user->user_lastname,						
			);
			
			$funcs->couponemails_create($this->user, false, $args);				
		}
	}
	
	public function update_referral_coupon($coupon_code, $name, $order_total)
	{
		$refs = $this->get_referred_coupons();
		$amount = $this->referral_coupon->get_reward_amount();
		

		switch ( $this->referral_coupon->get_reward_discount_type() ) {
			case 'percent':
				$reward = $order_total / 100 * $amount;
				break;
			case 'fixed_cart':
			case 'fixed_product':
				$reward = $amount;
				break;
			default:
				$reward = $amount;
		}	

		foreach ($refs as $key => $value) {
			$oldvalue = $value;		
					
			if (strtoupper($value['coupon_code']) == strtoupper($coupon_code)) {
				$this->increase_referral_coupon_amount($reward);
				if ($reward < 0) {
					$reward = 0;
				}				
				$value["name"] = $name;
				$value["reward"] = $reward;
				update_post_meta($this->referral_coupon_id, 'referral', $value, $oldvalue);
				return $value;
			}
		}

	}
	
	public function increase_referral_coupon_amount($amount)
	{
		$this->referral_coupon->set_amount($this->referral_coupon->get_amount() + $amount);
		$this->referral_coupon->save();
	}
	
	public function get_referral_coupon(){
		$coupon = new Email_Coupon($this->referral_coupon_id);
		return $coupon;
	}
	
	public function get_referral_coupon_id()
	{
		global $wpdb;
		$email = '"' . $this->user_email . '"';
		$sql = "SELECT p.ID as coupon_ID
		FROM {$wpdb->prefix}posts AS p
		JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id
		JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr.term_taxonomy_id
		JOIN {$wpdb->prefix}postmeta AS pme ON p.ID = pme.post_id AND pme.meta_key = 'customer_email'
		LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
		LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
		WHERE post_type = 'shop_coupon' AND  p.post_status= 'publish'
		AND DATE(FROM_UNIXTIME(pm.meta_value )) >= CURDATE()
		AND pme.meta_value LIKE '%{$email}%'
				AND (pmu.meta_value = 0 OR pmu.meta_value IS  NULL)
				AND t.slug = 'referral'
				AND p.post_status = 'publish'
				GROUP BY p.ID
				ORDER BY pm.meta_value
				LIMIT 1";

		$coupon_ids = $wpdb->get_col($sql);
		if (! empty($coupon_ids)) {
			return $coupon_ids[0];
		} else {
			return "";
		}
		
	}
	
	function get_term_id_by_slug($slug, $ref = false)
	{
		global $wpdb;
		$sql = "SELECT t.term_id FROM {$wpdb->prefix}terms AS t
		WHERE t.slug = '{$slug}'";
		$ids = $wpdb->get_col($sql);
		if (empty($ids)) {
			if ($ref) {
				$pref = "ref_";
			} else {
				$pref = "";
			}
			$category = $this->options[$pref . 'coupon_cat'];
			$term_taxonomy_id = Coupon_Emails_EmailFunctions::couponemails_coupon_category_create($category, $slug);
			return $term_taxonomy_id;
		}
		return $ids[0];		
	}
	
	function get_reffered_users(){
		global $wpdb;
		$sql = "SELECT t.meta_value FROM {$wpdb->prefix}postmeta AS t
		WHERE t.meta_key =  'referral'";
		$result = (array) $wpdb->get_results($sql);
		return $result;
	}
}
?>