<?php
namespace COUPONEMAILS;

// don't call the file directly
if ( !defined( 'ABSPATH' ) )
	exit();

class Coupon_Emails_EmailFunctions
{
	protected $type;
	protected $options_name;
	public $options_array;
	protected $emails_cnt;
	protected $product_name;
	protected $types_array;
	public $new_coupon_id;
	public $coupon_code;
	public $coupon_expiration;

	public function __construct($type = "", $product_name = "")
	{
		if (! empty($type)) {
			$this->type = $type;
			$this->options_name = $type . '_options';			
			$this->options_array = get_option($this->options_name);
		}
		$this->emails_cnt = 0;
		$this->product_name = $product_name;
		$this->types_array = ["couponemails_nameday","couponemails_birthday","couponemails_reorder","couponemails_onetimecoupon","couponemails_onetimeemail","couponemails_referralconfirmation",
		"couponemails_afterorder","couponemails_reviewed","couponemails_expirationreminder", "couponemails_referralemail","couponemails_reviewreminder" , "referral", "heureka"];
	}

	function couponemails_create($user, $istest = false, $args = array(), $html_body = "")
	{				
		$success = true;
		$options = $this->options_array;
		if (! $options) {
			Coupon_Emails_EmailFunctions::test_add_log('-- couponemails_create error -- ' . $this->type . PHP_EOL );
			return;
		}
		$subject_user = $options['subject'];		
		$html_body = empty($html_body) ? $options['email_body'] : $html_body;
		$from_name = $options['from_name'];
		$from_address = $options['from_address'];
		$header  = $options['header'];
		$coupon = "";
		
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); 
		}
		if (empty($coupon)) {
			if (isset($user->coupon)) {
				$coupon = $user->coupon;
			}
		}
		
		if ($istest == true) {
			$headers_user   = $this->couponemails_headers($from_name, $from_address,"", "", true);
			$email = $options['bcc_address'];
			$user_email = $user->user_email;
		} else {
			$headers_user   = $this->couponemails_headers($from_name, $from_address,"", "", false);
			$email = $user->user_email;
		}
		
		
		$char_length = isset($options['characters']) ? $options['characters'] : "";
		if ($char_length != 0 || ! empty($coupon)) {
			if ((array)$user) {
				if (empty($coupon)) {
					$coupon = $this->couponemails_get_unique_coupon($user);
				} else {
					$cat_id = $this->couponemails_coupon_category($user->coupon_ID, $this->type);
				}
				if (empty($coupon)) {
					$this->couponemails_add_log(esc_html_x( 'No available coupons to create.', 'Log file', 'coupon-emails' ) );
					$success = false;
					return $success;
				}
				$html_body = str_replace('{coupon}',$coupon,$html_body);
			}
		} else {
			$html_body = str_replace('{coupon}','',$html_body);
		}

		if (isset($options['bcc_address'])) {
			$admin_email = $options['bcc_address'];
		} else {
			$admin_email = get_bloginfo('admin_email');
		}
		
		$html_body = $this->couponemails_replace_placeholders($html_body, $user, $options, $args);
		$subject_user = $this->couponemails_replace_placeholders($subject_user, $user, $options, $args);

		if ((!str_contains(get_home_url(), 'test') && !str_contains(get_home_url(), 'stage') && $options['test'] != 1) || $istest == true) {
			if (is_email($email)) {
				if ($istest == true) {
					$html_body = $html_body . "<p style='font-size: 9px;'>" .  sprintf(esc_html__( "This is a test email sent to %s instead of to ", 'coupon-emails' ), $email)
					. ': ' . $user_email . "</p>";
				}
				if (isset($options['wc_template']) && $options['wc_template'] == 1) {
					$this->couponemails_send_wc_email_html($subject_user, $email, $html_body, $header);
				} else {
					$sendmail_user = wp_mail( $email, $subject_user, $html_body, $headers_user );
				}
				if (empty($coupon)) {
					$coupon_str = "";
				} else {
					if ( $coupon == "TESTCOUPON") {
						$coupon_str = "";
					} else {
						$coupon_str = ", " . esc_html_x("coupon", "Log file", "coupon-emails") . ": " . $coupon ;
					}
				}
					
				if ($istest == true) {

					$this->couponemails_add_log(sprintf( esc_html_x( "Test email sent to %s instead of to", "Log file", "coupon-emails" ), $email ) . " " . $user_email . $coupon_str);
					$success = false;
				} else {
					$this->couponemails_add_log( esc_html_x("Email sent to", "Log file", "coupon-emails")  . ': ' . $email . $coupon_str  ) ;
				}
			} else {
				$this->couponemails_add_log( esc_html_x("Cannot send email to incorrect or missing address" , "Log file", "coupon-emails") . ': ' . $email ) ;
				$success = false;
			}
		} else {
			$html_body = $html_body . "<p style='font-size: 9px;'>" .  sprintf(esc_html__( "This is a test email sent to %s instead of to ", 'coupon-emails' ), $admin_email)  . ': ' . $email . "</p>";

			if ($this->emails_cnt <= COUPON_EMAILS_MAX_TEST_EMAILS ) {
				if ($options['wc_template'] == 1) {
					$this->couponemails_send_wc_email_html($subject_user, $admin_email, $html_body, $header);
				} else {
					$sendmail_user = wp_mail( $admin_email, $subject_user, $html_body, $headers_user );
				}
				$this->couponemails_add_log(sprintf( esc_html_x( "Test email sent to %s instead of to", "Log file", "coupon-emails" ), $admin_email ) . " " . $email );
			} else {
				$this->couponemails_add_log(sprintf( esc_html_x( "An email was created but not sent to %s because the number of test emails exceeded", "Log file", "coupon-emails" ), $email ) . " " . COUPON_EMAILS_MAX_TEST_EMAILS);
			}

			$this->emails_cnt +=1;
			$success = false;
		}

		return $coupon; // $success;
	}

	function couponemails_replace_placeholders($content, $user, $options, $args = array())
	{
		$date_format = get_option( 'date_format' );
		$friend_firstname = '{friend_firstname}';
		$friend_lastname = '{friend_lastname}';
		$reward_amount = '{reward_amount}';
		$coupon_amount = isset($options['coupon_amount'] ) ? $options['coupon_amount'] : '{percent}';
		$expires_in_days = isset($options['expires'] ) ? $options['expires'] : '{expires_in_days}';
		$expires = isset($options['expires'] ) ? date_i18n('j. F Y', strtotime('+' . $options['expires'] . ' days')) : '{expires}' ;

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		if (isset($options['days_before'])) {
			$days_before = is_numeric($options['days_before']) ? $options['days_before'] : 0;
		} else {
			$days_before =0;
		}
		$current_user = wp_get_current_user();
		$inflection = new Coupon_Emails_Inflection();
		$first_name =  isset($user->user_firstname) &&  ! empty($user->user_firstname)  ?  $user->user_firstname : $user->user_email ;
		$last_name = isset($user->user_lastname) &&  ! empty($user->user_lastname)  ? $user->user_lastname : "";
		$replaced_text = str_replace(
		array(
		'{site_name}',
		'{site_url}',
		'{site_name_url}',
		'{expires_in_days}',
		'{expires}',
		'{for_date}',
		'{percent}',
		'{coupon_amount}',
		'{fname}',
		'{fname5}',
		'{lname}',
		'{products_cnt}',
		'{email}',
		'{reviewed_prod}',
		'{last_order_date}',
		'{referrer}',
		'{friend_firstname}',
		'{friend_lastname}',
		'{reward_amount}',
		),
		array(
		get_option( 'blogname' ),
		home_url(),
		'<a href=' . home_url() . '>' . get_option( 'blogname' ) . '</a>',
		$expires_in_days,				
		$expires,
		date_i18n('j. F Y', strtotime('+' . $expires_in_days . ' days')),
		$coupon_amount,
		$coupon_amount,
		ucfirst(strtolower($first_name)),
		$inflection->inflect(ucfirst(strtolower($first_name)))[5],
		ucfirst(strtolower($last_name)),
		isset($options['max_products'] ) ? $options['max_products'] : '' ,
		strtolower($user-> user_email),
		$this->product_name,
		$this->get_last_order_date($user-> user_email),
		$current_user->user_firstname . " " . $current_user->user_lastname,
		$friend_firstname,
		$friend_lastname,
		is_numeric($reward_amount) ? wc_price($reward_amount) : $reward_amount,
		),
		$content
		);

		return $replaced_text;
	}

	function get_last_order_date($user_email)
	{
		global $wpdb;
		
		if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
			$sql = "SELECT max(DATE(wco.date_created_gmt)) AS last_order_date
				FROM {$wpdb->prefix}wc_orders AS wco
				JOIN {$wpdb->prefix}users AS u ON wco.customer_id = u.ID AND  u.user_email = '{$user_email}'
				WHERE wco.status = 'wc-completed'
				GROUP BY wco.customer_id;";
		} else {
			$sql = "SELECT max(DATE(p.post_date)) AS last_order_date
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
				JOIN {$wpdb->prefix}users AS u ON upm.meta_value = u.ID AND  u.user_email = '{$user_email}'
				WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'
				GROUP BY upm.meta_value" ;			
		}		
		
		$order_date = $wpdb->get_var($sql);

		return date_i18n('j. F Y', strtotime($order_date));
	}

	function couponemails_add_log($entry)
	{
		$options = get_option('couponemails_options');
		if ($options['enable_logs'] == "1") {
			$name = $this->couponemails_get_coupons_cat_name($this->type);

			if ( is_array( $entry ) ) {
				$entry = json_encode( $entry );
			}
			$entry =$name . ": " . current_time( 'mysql' ) . " " .  $entry  ;
			$options = get_option('couponemails_logs');

			if (! $options ||  empty($options)) {
				add_option( 'couponemails_logs', array('logs'	=>	$entry) );
			} else {
				$log = $options['logs'];
				update_option( 'couponemails_logs',array('logs'	=>	$log . PHP_EOL .  $entry) );
			}
		}
	}

	static function test_add_log($entry)
	{
		if (COUPON_EMAILS_ENABLE_SQL_LOGS == 1) {
			if ( is_array( $entry ) ) {
				$entry = json_encode( $entry );
			}
			$options = get_option('couponemails_logs');
			$entry = current_time( 'mysql' ) . ": " .  $entry  ;
			if (! $options ||  empty($options)) {
				add_option( 'couponemails_logs', array('logs'	=>	$entry) );
			} else {
				$log = $options['logs'];
				update_option( 'couponemails_logs',array('logs'	=>	$log . PHP_EOL .  $entry . PHP_EOL ) );
			}
		}
	}

	function couponemails_headers($from_name, $from_address, $email_cc, $email_bcc, $istest = false)
	{
		$headers_user   = array();
		$headers_user[] = 'MIME-Version: 1.0' . "\r\n";
		$headers_user[] = 'Content-type:text/html;charset=UTF-8' . "\r\n";
		$headers_user[] = 'From: ' . $from_name . ' <' . $from_address . '>' . "\r\n";

		if ( ! empty( $email_cc ) ) {
			$headers_user[] = 'Cc: ' . $email_cc . "\r\n";
		}
		if ( ! empty( $email_bcc ) && $istest) {
			$headers_user[] = 'Bcc: ' . $email_bcc . "\r\n";
		}
		return $headers_user;
	}

	function couponemails_get_unique_coupon($user, $prefix = "", $cat_slug = "")
	{
		global $wpdb;
		$options = $this->options_array;
		$cat_slug = empty($cat_slug) ?  $this->type : $cat_slug ;
		$coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");
		$characters = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
		$char_length = $options[$prefix.'characters'];
		if ($char_length == 0)
			return "";
		$stp = 0;
		$max_stp = 10000;
		for ( $i = 0; $i < 1; $i++ ) {
			$generated_code  = substr( str_shuffle( $characters ), 0, $char_length );
			// Check if the generated code doesn't exist yet
			if ($stp > $max_stp)
				return;
			if ( in_array( $generated_code, $coupon_codes ) ) {
				$stp++;
				$i--; // continue the loop and generate a new code
			} else {
				break; // stop the loop: The generated coupon code doesn't exist already
			}
		}
		$amount = $options[$prefix.'coupon_amount']; // Amount
		$discount = $options[$prefix.'disc_type'];

		switch ($discount) {
			case 1:
				$discount_type = 'percent';
				break;
			case 2:
				$discount_type = 'fixed_cart';
				break;
			case 3:
				$discount_type = 'fixed_product';
				break;
			case 4:
				$discount_type = 'percent_product';
				break;
		}
		if (isset( $options[$prefix.'expires']) ) {
			$expiration_date = is_numeric( $options[$prefix.'expires']) ? $options[$prefix.'expires'] + 1 : 0;} else {
				$expiration_date = 0;	
			}
		$expiry_date   = date('Y-m-d', strtotime('+' . $expiration_date . ' days'));
		$max_products = isset( $options[$prefix.'max_products']) ? $options[$prefix.'max_products'] : '';
		$description = $options[$prefix.'description'];
		$description = $this->couponemails_replace_placeholders($description, $user, $options);
		$free_shipping = isset( $options[$prefix.'free_shipping']) ? "yes" : 'no';
		$individual_use = isset( $options[$prefix.'individual_use']) ? "yes" : 'no';
		$exclude_discounted = isset( $options[$prefix.'exclude_discounted']) ? "yes" : 'no';
		$minimum_amount = isset( $options[$prefix.'$minimum_amount']) ? $options[$prefix.'$minimum_amount'] : '';
		$maximum_amount = isset( $options[$prefix.'$maximum_amount']) ? $options[$prefix.'$maximum_amount'] : '';

		$coupon = array(
		'post_title' => $generated_code,
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type'     => 'shop_coupon',
		'post_excerpt' => $description, 
		);
		$new_coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
		if (isset($options[$prefix.'exclude_prods']))
			update_post_meta( $new_coupon_id, 'exclude_product_ids', implode(",", $options[$prefix.'exclude_prods'] )  );
		if (isset($options[$prefix.'only_products']))
			update_post_meta( $new_coupon_id, 'product_ids', implode(",", $options[$prefix.'only_products']) );
		if (isset($options[$prefix.'exclude_cats']))
			update_post_meta( $new_coupon_id, 'exclude_product_categories',  $options[$prefix.'exclude_cats'] );
		if (isset($options[$prefix.'only_cats']))
			update_post_meta( $new_coupon_id, 'product_categories', implode(",", $options[$prefix.'only_cats']) );
		update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_discounted );
		update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
		update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
		update_post_meta( $new_coupon_id, 'usage_limit', '1' );
		update_post_meta( $new_coupon_id, 'limit_usage_to_x_items', $max_products );
		update_post_meta( $new_coupon_id, 'usage_limit_per_user', '1' );
		$expiry_date_unix = strtotime($expiry_date);
		update_post_meta( $new_coupon_id, 'date_expires', $expiry_date_unix );
		//update_post_meta( $new_coupon_id, 'date_expires_local', $expiry_date );
		$this->coupon_expiration = $expiry_date;
		update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
		if ($this->type != 'couponemails_referralemail' || $prefix == "ref_") {				
			update_post_meta( $new_coupon_id, 'customer_email', array($user->user_email) );
		}
		update_post_meta( $new_coupon_id, 'customer_id', $user->user_id );
		
		update_post_meta( $new_coupon_id, '_acfw_enable_date_range_schedules', 'yes' );
		update_post_meta( $new_coupon_id, '_acfw_schedule_end', $expiry_date );
		// update_post_meta( $new_coupon_id, '_acfw_allowed_customers', $user->ID );
		if (! in_array($cat_slug, array("couponemails_referralconfirmation"))) {
			$cat_id = $this->couponemails_coupon_category($new_coupon_id, $cat_slug );
		}
		$this->new_coupon_id = $new_coupon_id;
		$this->coupon_code = $generated_code;
		return $generated_code;
	}

	function couponemails_coupon_category($new_coupon_id, $cat_slug)
	{
		global $wpdb;
		if (!$new_coupon_id ) {
			return;
		}
		$type_option = $this->type . '_options';
		$options = get_option($type_option);
		$category = $options['coupon_cat'];
		$term_id = $this->couponemails_get_cat_slug_id($cat_slug) ;
		if ( empty($term_id)) {
			$term_id = $this->couponemails_coupon_category_create($category, $cat_slug) ;
		}

		$sql = "INSERT INTO {$wpdb->prefix}term_relationships
							SET object_id = $new_coupon_id, term_taxonomy_id =
							(SELECT pt.term_id FROM {$wpdb->prefix}term_taxonomy AS pt
							INNER JOIN {$wpdb->prefix}terms AS t ON t.term_id = pt.term_id
							WHERE t.slug =  '$cat_slug')";
		$wpdb->query($sql);
		return $term_id;
	}

	static function is_coupon_in_category($coupon_code, $cat_slug)
	{
		global $wpdb;
		$sql = "SELECT p.ID FROM {$wpdb->prefix}term_taxonomy AS pt
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = pt.term_id
				JOIN {$wpdb->prefix}term_relationships AS tr ON tr.term_taxonomy_id = pt.term_taxonomy_id
				JOIN {$wpdb->prefix}posts AS p ON p.ID = tr.object_id
				WHERE t.slug =  '$cat_slug'
				AND p.post_title = '$coupon_code'";
				$term_id = $wpdb->get_var($sql);
				if ($term_id>0) {
					return true;
				} else {
					return false;
				}
	}
	function couponemails_coupon_category_create($category, $cat_slug)
	{
		global $wpdb;
		$sql = "INSERT INTO {$wpdb->prefix}terms SET name = '$category', slug = '$cat_slug' ";
		$wpdb->query($sql);
		$term_id = $wpdb->insert_id;
		$sql = "INSERT INTO {$wpdb->prefix}term_taxonomy SET term_id = $term_id, taxonomy = 'shop_coupon_cat' ";
		$wpdb->query($sql);
		$term_taxonomy_id = $wpdb->insert_id;
		return $term_taxonomy_id;
	}

	function couponemails_get_coupons_cat_names()
	{
		$cats_array = array();
		foreach ($this->types_array as $name) {
			$options = get_option($name . '_options');
			$cat_name = isset($options["coupon_cat"]) ? $options["coupon_cat"] : "";
			if (! empty($cat_name))
				$cats_array[] = $cat_name;
		}
		$names = sprintf("'%s'", implode("','", $cats_array ) );
		return $names;
	}

	function couponemails_get_coupons_cat_slugs()
	{
		$names = sprintf("'%s'", implode("','", $this->types_array ) );
		return $names ;
	}

	function couponemails_get_coupons_cat_name($name)
	{
		$options = get_option($name . '_options');
		$cat_name = isset($options["coupon_cat"]) ? $options["coupon_cat"] : "";
		if ( empty($cat_name)) {
			$cat_name = $name;
		}	
		return $cat_name;
	}

	function couponemails_get_stats()
	{
		global $wpdb;
		$cat_names = $this->couponemails_get_coupons_cat_slugs();
		$sql = "SELECT t.name, COUNT(p.ID) as total_count, used.used_count
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id 
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr.term_taxonomy_id AND t.slug IN ($cat_names)
				JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS used_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND pmu.meta_value > 0
				GROUP BY tr.term_taxonomy_id
				) AS used ON tr.term_taxonomy_id = used.term_taxonomy_id

				WHERE p.post_type = 'shop_coupon' AND  p.post_status= 'publish'
				GROUP BY tr.term_taxonomy_id";
				// $this->test_add_log('-- couponemails_get_stats -- ' . $this->type . PHP_EOL  . $sql);					
		$result = $wpdb->get_results($sql, OBJECT);
		return $result;
	}
	
	function couponemails_get_full_stats()
	{
		global $wpdb;
		$cat_names = $this->couponemails_get_coupons_cat_names();
		$sql = "SELECT t.name, COUNT(p.ID) as total_count, notexpired.notexpired_count, expired.expired_count, used.used_count
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr. ON tt.term_taxonomy_id = tr.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS expired_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND  post_status= 'publish' AND pm.meta_value < UNIX_TIMESTAMP() AND (pmu.meta_value = 0 OR pmu.meta_value IS  NULL)
				GROUP BY tr.term_taxonomy_id
				) AS expired ON tr.term_taxonomy_id = expired.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS notexpired_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND  post_status= 'publish' AND pm.meta_value >= UNIX_TIMESTAMP()
				GROUP BY tr.term_taxonomy_id
				) AS notexpired ON tr.term_taxonomy_id = notexpired.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS used_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND  post_status= 'publish' AND pmu.meta_value > 0
				GROUP BY tr.term_taxonomy_id
				) AS used ON tr.term_taxonomy_id = used.term_taxonomy_id

				WHERE p.post_type = 'shop_coupon' AND  p.post_status= 'publish'
				GROUP BY tr.term_taxonomy_id";
				$this->test_add_log('-- couponemails_get_full_stats -- ' . $this->type . PHP_EOL  . $sql);
		$result = $wpdb->get_results($sql, OBJECT);
		return $result;
	}
		
	function couponemails_get_cat_slug_id($cat_slug)
	{
		global $wpdb;
		$sql = "SELECT pt.term_id FROM {$wpdb->prefix}term_taxonomy AS pt
				INNER JOIN {$wpdb->prefix}terms AS t ON t.term_id = pt.term_id
				WHERE t.slug =  '$cat_slug'";
		$term_id = $wpdb->query($sql);
		return $term_id;
	}

	function couponemails_send_wc_email_html($subject, $recipient, $body, $heading = false )
	{
		$template = 'emails/nameday.php';
		$mailer = WC()->mailer();
		$options = $this->options_array;
		$headers = $this->couponemails_headers(
		isset($options['from_name']) ? $options['from_name'] : '',
		isset($options['from_address']) ? $options['from_address'] : '',
		isset($options['cc_address']) ? $options['cc_address'] : '',
		isset($options['bcc_address']) ? $options['bcc_address'] : ''
		);
		$content = wc_get_template_html( $template, array(
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer,
		'content'  => $body,
		),'',  plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/' );
		$mailer->send( $recipient, $subject, $content, $headers );
	}

	function couponemails_delete_expired()
	{
		global $wpdb;
		$options = get_option('couponemails_options');
		$days_delete =  ((isset($options['days_delete']) && !empty($options['days_delete'])) ? $options['days_delete'] : 0);
		if ($days_delete == 0)
			return;
		$sql = "SELECT ID FROM $wpdb->posts AS p
				JOIN $wpdb->postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				LEFT JOIN $wpdb->postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				WHERE post_type = 'shop_coupon' AND  post_status= 'publish'
				AND pm.meta_value > 0
				AND ( pmu.meta_value = 0 OR pmu.meta_value IS NULL )
				AND pm.meta_value + (" . $days_delete . "*86400) < UNIX_TIMESTAMP()
				ORDER BY pm.meta_value desc";
				
		$coupon_ids = $wpdb->get_col($sql);
		$count = count($coupon_ids);

		if (sizeof($coupon_ids) == 0)
			return;

		$where_in = implode(",", $coupon_ids );
		$this->test_add_log('-- couponemails_delete_expired - ' . $this->type . PHP_EOL . $sql . PHP_EOL . $where_in);
		
		$sql_pm = "DELETE FROM $wpdb->postmeta WHERE post_id IN (" . $where_in . ")";	
		$sql_p = "DELETE FROM $wpdb->posts WHERE ID IN (" . $where_in . ")";
		$sql_tr = "DELETE FROM $wpdb->term_relationships WHERE object_id IN (" . $where_in . ")";	
		
		$wpdb->get_results($sql_pm);
		$wpdb->get_results($sql_p);
		$wpdb->get_results($sql_tr);
		
		delete_option('couponemails_time_to_send');

		$this->couponemails_add_log(sprintf( _n( 'One expired unused coupon was deleted.', '%s expired unused coupons were deleted.', $count,  'coupon-emails'), $count));
	}

	function namedayemail_get_next_names()
	{
		$options = $this->options_array;
		$prior_days =$options['days_before'];
		if (! isset($prior_days)) {
			$prior_days = 0;
		}
		$str_nameday =  date('Y-m-d',strtotime('+' . $prior_days . ' day'));
		$dateValue = strtotime($str_nameday);
		$m = intval(date("m", $dateValue));
		$d = intval(date("d", $dateValue));

		$nd = new Coupon_Emails_Namedays();

		$names = $nd->get_names_for_day($d, $m , false );
		if (empty($names))
			return;
		$names = implode(',',array_unique(explode(',', $names)));

		if ($prior_days == 0) {
			return  sprintf(esc_html__(  'Today %s is Name Day celebrated by',  'coupon-emails'), $d . '.' . $m . '.') . " : " . $names;
		} else {
			return  $d . "." . $m . ". - " . sprintf( _n( 'Tomorrow is Name Day celebrated by', 'In %s days is Name Day celebrated by', $prior_days, 'coupon-emails' ), $prior_days )  . " " . $names;
		}
	}

	function csvstring_to_json($string, $separatorChar = ',', $enclosureChar = '"', $newlineChar = "\n")
	{
		// @author: Klemen Nagode
		$array = array();
		$size = strlen($string);
		$columnIndex = 0;
		$rowIndex = 0;
		$fieldValue="";
		$isEnclosured = false;
		for ($i=0; $i<$size;$i++) {

			$char = $string[$i];
			$addChar = "";

			if ($isEnclosured) {
				if ($char==$enclosureChar) {

					if ($i+1<$size && $string[$i+1]==$enclosureChar) {
						// escaped char
						$addChar=$char;
						$i++; // dont check next char
					} else {
						$isEnclosured = false;
					}
				} else {
					$addChar=$char;
				}
			} else {
				if ($char==$enclosureChar) {
					$isEnclosured = true;
				} else {

					if ($char==$separatorChar) {

						$array[$rowIndex][$columnIndex] = $fieldValue;
						$fieldValue="";

						$columnIndex++;
					} elseif ($char==$newlineChar) {
						echo esc_html($char);
						$array[$rowIndex][$columnIndex] = $fieldValue;
						$fieldValue="";
						$columnIndex=0;
						$rowIndex++;
					} else {
						$addChar=$char;
					}
				}
			}
			if ($addChar!="") {
				$fieldValue.=$addChar;
			}
		}

		if ($fieldValue) {
			// save last field
			$array[$rowIndex][$columnIndex] = $fieldValue;
		}

		$b_array = array();

		foreach ($array as $b) {
			$c = strtotime('2023-' . $b[0]);
			$d = date('j.n', $c);
			$b_array[$d] = $b[1];
		}
		//return $b_array;

		$str = "";
		foreach ($b_array as $key => $value ) {
			$str .= '"' . $key . '": "' . $value . '",' . PHP_EOL;
		}

		return $str;
	}

	function reviews_filtered($user_id, $comment_main_prod_ID, $comment_ID)
	{
		global $wpdb;
		$options = $this->options_array;
		$isOK = false;
		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {
			$sql = new Coupon_Emails_PrepareSQL('couponemails_reviewed');
			$categories = isset( $options['bought_cats']) ? $options['bought_cats'] : "";
			$cat_str = !empty($categories) ? implode(',', $categories) : "";
			$products =  isset( $options['bought_products']) ? $options['bought_products'] : "";
			$prod_str = !empty($products) ? implode(',', $products) : "";
			$roles = isset( $options['roles']) ? $options['roles'] : "";
			$exclude_roles = isset( $options['exclude-roles']) ? $options['exclude-roles'] : "";
			$stars = isset( $options['stars']) ? $options['stars'] : 0;

			$sql_str = $sql->get_comment_sql($comment_ID, $stars, $roles, $exclude_roles,  $cat_str,  $prod_str)	;

			$id = $wpdb->get_var($sql_str);

			if (isset($id)) {
				$isOK = true;
			}
		}
		return $isOK;
	}

	function get_types(){
		return $this->types_array;
	}
	
	static function get_tab_top_color($type)
	{
		$type_option = $type . '_options';
		$options = get_option($type_option);
		
		switch ($type) :
		case 'couponemails_onetimecoupon':
			if (isset($options['test']) && $options['test']) {
				return "top-orange";
			} else {
				return "top-gray";
			}			
			break;
		case 'couponemails_referralemail':
			$options_r = get_option("couponemails_referralemail_options");
			if (isset($options['enabled']) && $options['enabled']) {
				if (isset($options_r['enable_referral']) && $options_r['enable_referral']) {
					return "top-green";
				} else {
					return "top-gray";
				}				
			} else {
				if (isset($options_r['enable_referral']) && $options_r['enable_referral']) {
					return "top-orange";
				} else {
					return "top-gray";
				}				
			}					
			break;
		case 'heurekaemail':
			$options_r = get_option("heurekareviewedcouponemail_options");
	
				if (isset($options_r['enabled']) && $options_r['enabled']) {
					return "top-green";
				} else {
					
					$options_a = get_option("heurekareviewreminderemail_options");
					if (isset($options_a['enabled']) && $options_a['enabled']) {
						return "top-orange";
					} else {
						if (isset($options_r['enabled']) && $options_r['enabled']) {
							return "top-gray";
						} else {
							return "top-red";
						}						
					}									
				}

			break;			
		case 'reminderemail':
		$options_array = array( 'couponemails_reviewreminder','couponemails_expirationreminder');
			$isenabled = 0;
			$istest = 0;
			foreach ($options_array as $option) {
				$options = get_option($option . '_options');
				if (isset($options['enabled']) && $options['enabled']) {
					$isenabled = 1;
				} 				
				if (isset($options['test']) && $options['test']) {
					$istest = 1;
				}			
			}
			if ($isenabled == 1  && $istest == 1) {
				return "top-green";				
			}
			if ($isenabled == 1) {
				return "top-green";
			}
			if ($isenabled == 0 && $istest == 1) {
				return "top-orange";
			}	
			if ($isenabled == 0 && $istest == 0) {
				return "top-red";
			}				
			return;	
			break;			
		default:			
			if (isset($options['enabled']) && $options['enabled']) {
				if (isset($options['test']) && $options['test']) {
					return "top-orange-green";
				} else {
					return "top-green";
				}
			}  else {
				return "top-red";
			}	
			break;
		endswitch;		

	}
}
?>