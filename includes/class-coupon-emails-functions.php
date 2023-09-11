<?php
namespace COUPONEMAILS;

// don't call the file directly
if ( !defined( 'ABSPATH' ) )
	exit();

class EmailFunctions
{
	protected $type;
	protected $options_name;
	public $options_array;
	protected $emails_cnt;
	protected $product_name;

	public function __construct($type = "", $product_name = "")
	{
		$this->type = $type;
		$this->options_name = $type . '_options';
		$this->options_array = get_option($this->options_name);
		$this->emails_cnt = 0;
		$this->product_name = $product_name;
	}

	function couponemails_create($user, $istest = false, $coupon = "")
	{
		$success = true;
		$options = $this->options_array;
		$subject_user = $options['subject'];
		$html_body = $options['email_body'];
		$from_name = $options['from_name'];
		$from_address = $options['from_address'];
		$header  = $options['header'];

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
				if (empty($coupon) != "") 
					$coupon = $this->couponemails_get_unique_coupon($user);

				if (empty($coupon)) {
					$this->couponemails_add_log("No available coupons to create.");
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
		
		$html_body = $this->couponemails_replace_placeholders($html_body, $user, $options);
		$subject_user = $this->couponemails_replace_placeholders($subject_user, $user, $options);

		if ((!str_contains(get_home_url(), 'test') && !str_contains(get_home_url(), 'stage') && $options['test'] != 1) || $istest == true) {
			if (is_email($email)) {
				if ($istest == true) {
					$html_body = $html_body . "<p style='font-size: 9px;'>" .  sprintf(__( "This is a test email sent to %s instead of to ", 'coupon-emails' ), $email)
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
					$coupon_str =  ', coupon: ' . $coupon ;
				}
					
				if ($istest == true) {
					$this->couponemails_add_log("Test email sent to" . ': ' . $email  . " instead of to " . $user_email . $coupon_str ) ;
					$success = false;
				} else {
					$this->couponemails_add_log("Email sent to" . ': ' . $email . $coupon_str  ) ;
				}
			} else {
				$this->couponemails_add_log("Trying to send to incorrect or missing email address"  . ': ' . $email ) ;
				$success = false;
			}
		} else {
			$html_body = $html_body . "<p style='font-size: 9px;'>" .  sprintf(__( "This is a test email sent to %s instead of to ", 'coupon-emails' ), $admin_email)  . ': ' . $email . "</p>";

			if ($this->emails_cnt <= MAX_TEST_EMAILS ) {
				if ($options['wc_template'] == 1) {
					$this->couponemails_send_wc_email_html($subject_user, $admin_email, $html_body, $header);
				} else {
					$sendmail_user = wp_mail( $admin_email, $subject_user, $html_body, $headers_user );
				}
				$this->couponemails_add_log("Email has been sent as Test to"  . ' ' . $admin_email . " instead of to " . $email) ;
			} else {
				$this->couponemails_add_log("Email has been created but not sent to " . $email . " because of Test.") ;
			}

			$this->emails_cnt +=1;
			$success = false;
		}
		return $coupon; // $success;
	}

	function couponemails_replace_placeholders($content, $user, $options)
	{
		$date_format = get_option( 'date_format' );

		if (isset($options['days_before'])) {
			$days_before = is_numeric($options['days_before']) ? $options['days_before'] : 0;
		} else {
			$days_before =0;
		}
		$inflection = new Inflection();
		$first_name = isset($user->user_firstname) ? $user->user_firstname : "";
		$last_name = isset($user->user_lastname) ? $user->user_lastname : "";
		$replaced_text = str_replace(
		array(
		'{site_name}',
		'{site_url}',
		'{site_name_url}',
		'{expires_in_days}',
		'{expires}',
		'{for_date}',
		'{percent}',
		'{fname}',
		'{fname5}',
		'{lname}',
		'{products_cnt}',
		'{email}',
		'{reviewed_prod}',
		'{last_order_date}',
		),
		array(
		get_option( 'blogname' ),
		home_url(),
		'<a href=' . home_url() . '>' . get_option( 'blogname' ) . '</a>',
		isset($options['expires'] ) ? $options['expires'] : '' ,
		isset($options['expires'] ) ? date_i18n('j. F Y', strtotime('+' . $options['expires'] . ' days')) : '' ,
		date_i18n('j. F Y', strtotime('+' . $days_before . ' days')),
		isset($options['coupon_amount'] ) ? $options['coupon_amount'] : '' ,
		ucfirst(strtolower($first_name)),
		$inflection->inflect(ucfirst(strtolower($first_name)))[5],
		ucfirst(strtolower($last_name)),
		isset($options['max_products'] ) ? $options['max_products'] : '' ,
		strtolower($user-> user_email),
		$this->product_name,
		$this->get_last_order_date($user-> user_email),
		),
		$content
		);
		return $replaced_text;
	}

	function get_last_order_date($user_email)
	{
		global $wpdb;
		$sql = "SELECT max(DATE(p.post_date)) AS last_order_date
		FROM {$wpdb->prefix}posts AS p
		JOIN {$wpdb->prefix}postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
		JOIN {$wpdb->prefix}users AS u ON upm.meta_value = u.ID AND  u.user_email = '{$user_email}'
		WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'
		GROUP BY upm.meta_value" ;
		$order_date = $wpdb->get_var($sql);

		return date_i18n('j. F Y', strtotime($order_date));
	}

	function couponemails_add_log($entry)
	{
		$options = get_option('couponemails_options');
		if ($options['enable_logs'] == "1") {

			if ( is_array( $entry ) ) {
				$entry = json_encode( $entry );
			}
			$entry =$this->type . ": " . current_time( 'mysql' ) . " " .  $entry  ;
			$options = get_option('couponemails_logs');

			if (empty($options)) {
				add_option( 'couponemails_logs', array('logs'	=>	$entry) );
			} else {
				$log = $options['logs'];
				update_option( 'couponemails_logs',array('logs'	=>	$log . PHP_EOL .  $entry) );
			}
		}
	}

	static function test_add_log($entry)
	{
		if (ENABLE_SQL_LOGS == 1) {
			if ( is_array( $entry ) ) {
				$entry = json_encode( $entry );
			}
			$options = get_option('couponemails_logs');

			if (empty($options)) {
				add_option( 'couponemails_logs', array('logs'	=>	$entry) );
			} else {
				$log = $options['logs'];
				update_option( 'couponemails_logs',array('logs'	=>	$log . PHP_EOL .  $entry . PHP_EOL . PHP_EOL ) );
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

	function couponemails_get_unique_coupon($user)
	{
		global $wpdb;
		$options = $this->options_array;
		$coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");
		$characters = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
		$char_length = $options['characters'];
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
		$amount = $options['coupon_amount']; // Amount
		$discount = $options['disc_type'];

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

		$expiration_date = is_numeric( $options['expires']) ? $options['expires'] + 1 : 0;
		$expiry_date   = date('Y-m-d', strtotime('+' . $expiration_date . ' days'));
		$max_products = isset( $options['max_products']) ? $options['max_products'] : '';
		$description = $options['description'];
		$description = $this->couponemails_replace_placeholders($description, $user, $options);
		$free_shipping = isset( $options['free_shipping']) ? "yes" : 'no';
		$individual_use = isset( $options['individual_use']) ? "yes" : 'no';
		$exclude_discounted = isset( $options['exclude_discounted']) ? "yes" : 'no';
		$minimum_amount = isset( $options['$minimum_amount']) ? $options['$minimum_amount'] : '';
		$maximum_amount = isset( $options['$maximum_amount']) ? $options['$maximum_amount'] : '';

		$coupon = array(
		'post_title' => $generated_code,
		'post_content' => '',
		'post_status' => 'publish',
		'post_author' => 1,
		'post_type'     => 'shop_coupon',
		'post_excerpt' => $description, // __( 'Name day', 'coupon-emails' ) . ' ' . $user->user_firstname  . ' ' . $user->user_email,
		);
		$new_coupon_id = wp_insert_post( $coupon );

		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
		if (isset($options['exclude_prods']))
			update_post_meta( $new_coupon_id, 'exclude_product_ids', implode(",", $options['exclude_prods'] )  );
		if (isset($options['only_products']))
			update_post_meta( $new_coupon_id, 'product_ids', implode(",", $options['only_products']) );
		if (isset($options['exclude_cats']))
			update_post_meta( $new_coupon_id, 'exclude_product_categories',  $options['exclude_cats'] );
		if (isset($options['only_cats']))
			update_post_meta( $new_coupon_id, 'product_categories', implode(",", $options['only_cats']) );
		update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_discounted );
		update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
		update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_amount );
		update_post_meta( $new_coupon_id, 'usage_limit', '1' );
		update_post_meta( $new_coupon_id, 'limit_usage_to_x_items', $max_products );
		update_post_meta( $new_coupon_id, 'usage_limit_per_user', '1' );
		$expiry_date_unix = strtotime($expiry_date);
		update_post_meta( $new_coupon_id, 'date_expires', $expiry_date_unix );
		//update_post_meta( $new_coupon_id, 'date_expires_local', $expiry_date );
		update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
		update_post_meta( $new_coupon_id, 'customer_email', array($user->user_email) );
		update_post_meta( $new_coupon_id, 'customer_id', $user->id );
		
		update_post_meta( $new_coupon_id, '_acfw_enable_date_range_schedules', 'yes' );
		update_post_meta( $new_coupon_id, '_acfw_schedule_end', $expiry_date );
		// update_post_meta( $new_coupon_id, '_acfw_allowed_customers', $user->id );

		$cat_id = 0;
		if (isset($options['coupon_cat']))
			$cat_id = $this->couponemails_coupon_category($new_coupon_id, $options['coupon_cat']);

		return $generated_code;
	}

	function couponemails_coupon_category($new_coupon_id, $category)
	{
		global $wpdb;
		$cat_slug = sanitize_title($category);

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
		$names_array = ["namedayemail","birthdayemail","reorderemail","onetimeemail","afterorderemail","reviewedemail"];
		$cats_array = array();

		foreach ($names_array as $name) {
			$options = get_option($name . '_options');
			$cat_name = isset($options["coupon_cat"]) ? $options["coupon_cat"] : "";
			if (! empty($cat_name))
				$cats_array[] = $cat_name;
		}
		$names = sprintf("'%s'", implode("','", $cats_array ) );
		return $names;
	}


	function couponemails_get_stats()
	{
		global $wpdb;
		$cat_names = $this->couponemails_get_coupons_cat_names();
		$sql = "SELECT t.name, COUNT(p.ID) as total_count, used.used_count
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id AND tr.term_taxonomy_id IN (
				SELECT term_id FROM {$wpdb->prefix}terms AS t WHERE t.name IN ($cat_names) )
				JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS used_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND pmu.meta_value > 0
				GROUP BY tr.term_taxonomy_id
				) AS used ON tr.term_taxonomy_id = used.term_taxonomy_id

				WHERE p.post_type = 'shop_coupon'
				GROUP BY tr.term_taxonomy_id";
		//$this->test_add_log('-- ' . $this->type . PHP_EOL  . $sql);					
		$result = $wpdb->get_results($sql, OBJECT);
		return $result;
	}
	
/*	function couponemails_get_full_stats()
	{
		global $wpdb;
		$cat_names = $this->couponemails_get_coupons_cat_names();
		$sql = "SELECT t.name, COUNT(p.ID) as total_count, notexpired.notexpired_count, expired.expired_count, used.used_count
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id AND tr.term_taxonomy_id IN (
				SELECT term_id FROM {$wpdb->prefix}terms AS t WHERE t.name IN ($cat_names) )
				JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS expired_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND pm.meta_value < UNIX_TIMESTAMP() AND (pmu.meta_value = 0 OR pmu.meta_value IS  NULL)
				GROUP BY tr.term_taxonomy_id
				) AS expired ON tr.term_taxonomy_id = expired.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS notexpired_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND pm.meta_value >= UNIX_TIMESTAMP()
				GROUP BY tr.term_taxonomy_id
				) AS notexpired ON tr.term_taxonomy_id = notexpired.term_taxonomy_id

				LEFT OUTER JOIN (
				SELECT COUNT(ID) AS used_count, tr.term_taxonomy_id AS term_taxonomy_id
				FROM {$wpdb->prefix}posts AS p
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
				WHERE post_type = 'shop_coupon' AND pmu.meta_value > 0
				GROUP BY tr.term_taxonomy_id
				) AS used ON tr.term_taxonomy_id = used.term_taxonomy_id

				WHERE p.post_type = 'shop_coupon'
				GROUP BY tr.term_taxonomy_id";
		$this->test_add_log('-- ' . $this->type . PHP_EOL  . $sql);
		$result = $wpdb->get_results($sql, OBJECT);
		return $result;
	}*/
		
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
				WHERE post_type = 'shop_coupon'
				AND pm.meta_value > 0
				AND ( pmu.meta_value = 0 OR pmu.meta_value IS NULL )
				AND pm.meta_value + (" . $days_delete . "*86400) < UNIX_TIMESTAMP()
				ORDER BY pm.meta_value desc";
		
		$this->test_add_log('-- ' . $this->type . PHP_EOL  . $sql);				
		$coupon_ids = $wpdb->get_col($sql);
		$count = count($coupon_ids);

		if (sizeof($coupon_ids) == 0)
			return;

		$where_in = implode(",", $coupon_ids );
		$this->test_add_log('-- ' . $this->type . PHP_EOL  . $where_in);
		
		$sql_pm = "DELETE FROM $wpdb->postmeta WHERE post_id IN (" . $where_in . ")";	
		$sql_p = "DELETE FROM $wpdb->posts WHERE ID IN (" . $where_in . ")";
		$sql_tr = "DELETE FROM $wpdb->term_relationships WHERE object_id IN (" . $where_in . ")";	
		
		$wpdb->get_results($sql_pm);
		$wpdb->get_results($sql_p);
		$wpdb->get_results($sql_tr);

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

		$nd = new Namedays();

		$names = $nd->get_names_for_day($d, $m , false );
		if (empty($names))
			return;
		$names = implode(',',array_unique(explode(',', $names)));

		if ($prior_days == 0) {
			return  sprintf(__(  'Today %s is Name Day celebrated by',  'coupon-emails'), $d . '.' . $m . '.') . " : " . $names;
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
						echo $char;
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
			$sql = new PrepareSQL('reviewedemail');
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

	static function get_tab_top_color($type)
	{
		$type_option = $type . '_options';
		$options = get_option($type_option);
		
		switch ($type) :
			case 'onetimeemail':
			if (isset($options['test']) && $options['test']) {
				return "top-orange";
			} else {
				return "top-gray";
			}			
			break;
		case 'reminderemail':
			$options_array = array( 'reviewreminderemail','expirationreminderemail');
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