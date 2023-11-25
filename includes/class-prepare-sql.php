<?php

namespace COUPONEMAILS;

class Coupon_Emails_PrepareSQL
{
	protected $where;
	protected $days_since_sign;
	protected $type;

	protected function add_where($txt)
	{
		$this->where .=  $txt;
	}

	protected function get_where()
	{
		return $this->where;
	}

	public function __construct($type, $days_since_sign  = '<=')
	{
		$this->where = '
			WHERE 1=1 ';
		$this->days_since_sign = $days_since_sign;
		$this->type = $type;
	}
	
	public function get_users_from_emails($emails, $as_objects = false)
	{
		global $wpdb;
		$email_address = explode(",",$emails);
		if (! is_array($email_address))
			retrun ;
		$trimmed_array = array_map('trim', $email_address);
		$addresses = sprintf("'%s'", implode("','", $trimmed_array ) );
		
		if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
			$sql2 = "
			SELECT wco.customer_id AS user_id, COUNT(wco.ID) AS orders_count, ROUND(SUM(wco.total_amount),2) AS orders_total, DATE(MAX(wco.date_created_gmt)) AS last_order
			FROM {$wpdb->prefix}wc_orders AS wco
			WHERE wco.status = 'wc-completed' AND wco.customer_id IS NOT NULL
			GROUP BY wco.customer_id";
		} else {
			$sql2 = "
			SELECT upm.meta_value AS user_id, SUM(upmt.meta_value) AS orders_total, DATE(max(p.post_date)) AS last_order, COUNT(p.ID) AS orders_count
			FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
				JOIN {$wpdb->prefix}postmeta AS upmt ON upmt.post_id = p.ID AND upmt.meta_key = '_order_total'
			WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'
			GROUP BY upm.meta_value";
		}
		
		$sql ="
			SELECT umfn.meta_value AS user_firstname, umln.meta_value AS user_lastname, u.user_email, u.ID, orders.orders_count, orders.orders_total, orders.last_order
			FROM {$wpdb->prefix}users AS u
				JOIN {$wpdb->prefix}usermeta AS umfn ON u.ID =  umfn.user_id AND umfn.meta_key = 'first_name'
				JOIN {$wpdb->prefix}usermeta AS umln ON u.ID =  umln.user_id AND umln.meta_key = 'last_name'
				LEFT OUTER  JOIN ( $sql2 ) AS orders
			ON orders.user_id = u.ID
			WHERE u.user_email IN ($addresses)
			";
			Coupon_Emails_EmailFunctions::test_add_log('-- get_users_from_emails -- ' . $this->type . PHP_EOL  . $sql);
		if ($as_objects) {
			$result = $wpdb->get_results($sql, OBJECT);
		} else {
			$result = $wpdb->get_results($sql, ARRAY_A);
		}		

		return $result;
	}
	
	public function get_coupon_codes_for_email( $email, $coupon_slug = "")
	{
		global $wpdb;
		$sql = "SELECT p.post_title AS coupon
		FROM {$wpdb->prefix}posts AS p
		JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id
		JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr.term_taxonomy_id
		JOIN {$wpdb->prefix}postmeta AS pme ON p.ID = pme.post_id AND pme.meta_key = 'referral'
		WHERE post_type = 'shop_coupon'  AND  p.post_status= 'publish'
		AND pme.meta_value LIKE '%{$email}%' ";
		
		if (! empty($coupon_slug)) {
			$sql .= " AND t.slug =  '$coupon_slug'";
		}
		$result = (array) $wpdb->get_results($sql);
		return $result;		
	}
	
	public function get_users_with_expired_coupons( $as_objects = false)
	{
		global $wpdb;
		$options = get_option($this->type . '_options');
		$day_before =  (isset( $options['days_before'])) ? $options['days_before'] : 0;
		$cat_str = '';
		$expired_cats_array = isset( $options['expiration_cats']) ? $options['expiration_cats'] : "";
		if (!empty($expired_cats_array)) $cat_str = implode(',', $expired_cats_array);
		
		$sql = "SELECT GROUP_CONCAT(t.name SEPARATOR ', ') AS coupon_cat, p.post_title AS coupon, pme.meta_value as user_email, 
		NULL as user_firstname, NULL as user_lastname, FROM_UNIXTIME(pm.meta_value , '%e.%c.%Y') AS expires, NULL as ID, p.ID as coupon_ID
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id 
				JOIN {$wpdb->prefix}terms AS t ON t.term_id = tr.term_taxonomy_id ";
				
		if ($cat_str != "")
			$sql .= "AND tr.term_taxonomy_id IN ($cat_str) ";
				
		$sql .="
				JOIN {$wpdb->prefix}postmeta AS pme ON p.ID = pme.post_id AND pme.meta_key = 'customer_email'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pmu ON p.ID = pmu.post_id AND pmu.meta_key = 'usage_count'
				LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'date_expires'
				WHERE post_type = 'shop_coupon'  AND  post_status= 'publish'
				AND DATE(FROM_UNIXTIME(pm.meta_value - $day_before * 86400 )) = CURDATE()
				AND (pmu.meta_value = 0 OR pmu.meta_value IS  NULL)
				GROUP BY p.ID";
				
				Coupon_Emails_EmailFunctions::test_add_log('-- get_users_with_expired_coupons -- ' . $this->type . PHP_EOL  . $sql);
		$result = $wpdb->get_results($sql, OBJECT);
		foreach ($result as  $value) {
			$value->user_email = maybe_unserialize($value->user_email)[0];
			$user = get_user_by( 'email', $value->user_email );
			$value->ID = $user->ID;
			$value->user_firstname = $user->first_name;
			$value->user_lastname = $user->last_name;
		}	
		if ( $as_objects) {
			return $result;
		} else {
			return (array) $result;
		}				
	}	
	
	public function get_users_filtered( $as_objects = false)
	{
		global $wpdb;

		$options = get_option($this->type . '_options');
		$emails =  isset( $options['email_address']) ? $options['email_address'] : "";
		$days_after_order =  isset( $options['days_after_order']) && is_numeric($options['days_after_order']) ? $options['days_after_order'] : "";
		
/*		Coupon_Emails_EmailFunctions::test_add_log('-- $days_after_order = '  . $days_after_order);	*/	
		$days_after_active =  isset( $options['days_after_active']) && is_numeric($options['days_after_active']) ? $options['days_after_active'] : "";
		$already_rated = isset( $options['already_rated']) ? $options['already_rated'] : "";
		if (! empty( $emails))
			return $this->get_users_from_emails ($emails, $as_objects);
		$minimum_orders = isset( $options['minimum_orders']) ? $options['minimum_orders'] : "";
		$previous_order = isset( $options['previous_order']) ? $options['previous_order'] : "0";
		if ($this->type != 'couponemails_onetimecoupon' && $this->type != 'couponemails_onetimecoupon')
			$minimum_orders = 1;
				
		$minimum_spent = isset( $options['minimum_spent']) ? $options['minimum_spent'] : "";
		$maximum_spent = isset( $options['maximum_spent']) ? $options['maximum_spent'] : "";
		$total_spent = $this->get_spent_string($minimum_spent, $maximum_spent);
		$with_no_name =  isset( $options['with_no_name']) ? $options['with_no_name'] : 0;
		$roles = isset( $options['roles']) ? $options['roles'] : "";
		$exclude_roles = isset( $options['exclude-roles']) ? $options['exclude-roles'] : "";
		$bought_products = isset( $options['bought_products']) ? $options['bought_products'] : "";
		$not_bought_products = isset( $options['not_bought_products']) ? $options['not_bought_products'] : "";
		$bought_cats = isset( $options['bought_cats']) ? $options['bought_cats'] : "";
		$not_bought_cats = isset( $options['not_bought_cats']) ? $options['not_bought_cats'] : "";
		if (!empty($minimum_orders) && is_numeric($minimum_orders) && $minimum_orders >0) {
			$select_orders_str = ", orders.orders_count, orders.orders_total, orders.last_order";
		} else {
			$select_orders_str = ", '' AS orders_count, '' AS orders_total, '' AS last_order";
		}	
		if ($previous_order>0) {
			$select_previous_order_str = $this->get_select_previous_order();
			$where_previous_order_str = $this->get_where_previous_order($previous_order);
		} else {
			$select_previous_order_str = "";
			$where_previous_order_str = "";			
		}
			
		if ($with_no_name == 1) {
			$no_fname = "";
			$no_lname = "";				

		} else {
			$no_fname = " AND umfn.meta_value <> '' ";
			$no_lname = " AND umln.meta_value <> '' ";		
		}
		$sql = "SELECT u.user_email, umfn.meta_value AS user_firstname, umln.meta_value AS user_lastname, 
				FROM_UNIXTIME(uma.meta_value, '%e.%c.%Y') AS activity, u.ID $select_orders_str $select_previous_order_str
				FROM $wpdb->users AS u
				JOIN $wpdb->usermeta AS umfn ON u.ID =  umfn.user_id AND umfn.meta_key = 'first_name' $no_fname
				JOIN $wpdb->usermeta AS umln ON u.ID =  umln.user_id AND umln.meta_key = 'last_name' $no_lname " .
				$this->get_join_capabilities_sql($roles, $exclude_roles) . 
				$this->get_join_last_activity_sql($days_after_active);
		if ($minimum_orders != 0) {
			$sql .= $this->get_join_products($bought_products) ;
			$sql .= $this->get_join_products($not_bought_products, true) ;
			$sql .= $this->get_join_categories($bought_cats) ;
			$sql .= $this->get_join_categories($not_bought_cats, true) ;
		}

		$sql .= $this->get_orders_sql($minimum_orders, $days_after_order, $total_spent, $already_rated, $where_previous_order_str) 	;
		
		Coupon_Emails_EmailFunctions::test_add_log('-- get_users_filtered -- ' . $this->type . PHP_EOL  . $sql);
		//return $sql;

		if ($as_objects) {
			$result = $wpdb->get_results($sql, OBJECT);
		} else {
			$result = $wpdb->get_results($sql, ARRAY_A);
		}		

		return $result;
	}

	function get_join_categories($categories, $exclude = false)
	{
		global $wpdb;

		$sql = "";
		if (! empty($categories) &&  is_array( $categories ) ) {

			if ($exclude) {
				$sql .= "
				LEFT OUTER JOIN (";
				$sufix = "_excluded";
				$field = ", COUNT(p.ID) AS orders_cnt";
				$this->add_where(' AND categories_excluded.orders_cnt IS null');
			} else {
				$sql .= "
				JOIN (";
				$sufix = "";
				$sufix = "";
				$field = "";
			}
			$cat_str = implode(',', $categories);
			if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
				$sql.= "
				SELECT o.customer_id AS user_id, p.ID AS prod_id, tr.term_taxonomy_id AS cat_id, t.name AS cat_name , COUNT(p.ID) AS orders_cnt
				FROM {$wpdb->prefix}wc_orders o
				JOIN {$wpdb->prefix}woocommerce_order_items oi ON o.ID = oi.order_id AND oi.order_item_type = 'line_item'
				JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id AND oim.meta_key = '_product_id'
				JOIN {$wpdb->prefix}posts p ON p.ID = oim.meta_value
				JOIN {$wpdb->prefix}term_relationships AS tr ON (p.ID = tr.object_id)
				AND  tr.term_taxonomy_id IN ($cat_str)
				JOIN {$wpdb->prefix}terms AS t ON tr.term_taxonomy_id = t.term_id
				JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id AND tt.taxonomy ='product_cat'
				WHERE
				o.status = 'wc-completed'
				GROUP BY  o.customer_id ";
			} else {
				$sql.= "
				SELECT pmc.meta_value AS user_id, p.ID AS prod_id, tr.term_taxonomy_id AS cat_id, t.name AS cat_name $field
					FROM {$wpdb->prefix}posts o
					JOIN {$wpdb->prefix}postmeta pmc ON o.ID = pmc.post_id AND pmc.meta_key = '_customer_user'
					JOIN {$wpdb->prefix}woocommerce_order_items oi ON o.ID = oi.order_id AND oi.order_item_type = 'line_item'
					JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id AND oim.meta_key = '_product_id'
					JOIN {$wpdb->prefix}posts p ON p.ID = oim.meta_value
					JOIN {$wpdb->prefix}term_relationships AS tr ON (p.ID = tr.object_id)
						AND  tr.term_taxonomy_id IN ($cat_str)
					JOIN {$wpdb->prefix}terms AS t ON tr.term_taxonomy_id = t.term_id
					JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = t.term_id AND tt.taxonomy ='product_cat'
				WHERE
					o.post_type = 'shop_order' AND o.post_status = 'wc-completed'
				GROUP BY  pmc.meta_value ";				
			}

			$sql .= ")
				AS categories$sufix
				ON categories$sufix.user_id  = u.ID ";
		}
		return $sql;
	}

	function get_join_products($bought_products, $exclude = false)
	{
		global $wpdb;

		$sql = "";
		if (! empty($bought_products) &&  is_array( $bought_products ) ) {

			if ($exclude) {
				$sql .= "
				LEFT OUTER JOIN (";
				$sufix = "_excluded";
				$field = ", COUNT(p.ID) AS orders_cnt";
				$this->add_where('  AND  products_excluded.orders_cnt IS null');
			} else {
				$sql .= "
				JOIN (";
				$sufix = "";
				$field = "";
			}
			$prod_str = implode(',', $bought_products);
			
			if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
				$sql .= "
				SELECT p.customer_id AS user_id $field
				FROM {$wpdb->prefix}wc_orders AS p
					join {$wpdb->prefix}woocommerce_order_items oi on p.ID = oi.order_id AND oi.order_item_type = 'line_item'
					join {$wpdb->prefix}woocommerce_order_itemmeta oim on oim.order_item_id = oi.order_item_id AND oim.meta_key = '_product_id'
					join {$wpdb->prefix}woocommerce_order_itemmeta oimv on oimv.order_item_id = oi.order_item_id AND oimv.meta_key = '_variation_id'
				WHERE
					STATUS = 'wc-completed' AND p.customer_id IS NOT NULL
					AND (oimv.meta_value IN ($prod_str) OR oim.meta_value IN ($prod_str))
				GROUP BY p.customer_id				
				";
			} else {
				$sql.= "
				SELECT pmc.meta_value AS user_id $field
			    FROM {$wpdb->prefix}posts p
					join {$wpdb->prefix}postmeta pmc on p.ID = pmc.post_id AND pmc.meta_key = '_customer_user'
					join {$wpdb->prefix}woocommerce_order_items oi on p.ID = oi.order_id AND oi.order_item_type = 'line_item'
					join {$wpdb->prefix}woocommerce_order_itemmeta oim on oim.order_item_id = oi.order_item_id AND oim.meta_key = '_product_id'
					join {$wpdb->prefix}woocommerce_order_itemmeta oimv on oimv.order_item_id = oi.order_item_id AND oimv.meta_key = '_variation_id'
			    WHERE
			        post_type = 'shop_order' AND post_status = 'wc-completed'
					AND (oimv.meta_value IN $prod_str OR oim.meta_value IN  $prod_str ))
				GROUP BY user_id
				";				
			}				

			$sql .= ")
				AS products$sufix
				ON products$sufix.user_id  = u.ID ";
		}

		return $sql;
	}

	function get_comment_sql($comment_id, $min_rating = 0, $in_roles = "", $not_in_roles = "",  $in_cats = "", $prod_id  = "")
	{
		global $wpdb;
		$sql = "SELECT COUNT(c.comment_ID)
				FROM {$wpdb->prefix}comments AS c
				LEFT OUTER JOIN {$wpdb->prefix}commentmeta AS cm ON c.comment_ID = cm.comment_id AND cm.meta_key = 'rating'
				LEFT OUTER JOIN {$wpdb->prefix}commentmeta AS ct ON c.comment_ID = ct.comment_id AND ct.meta_key = '_wp_trash_meta_status' 
				LEFT OUTER JOIN {$wpdb->prefix}commentmeta AS cs ON c.comment_ID = cs.comment_id AND cs.meta_key = 'couponemails_reviewed_email_sent' "
				. $this->get_join_capabilities_sql($in_roles, $not_in_roles,"c.user_id");

		if (! empty($in_cats)) {

			$sql .= "
			JOIN {$wpdb->prefix}term_relationships AS tr ON tr.object_id = c.comment_post_ID
			JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.term_id = tr.term_taxonomy_id AND tt.taxonomy ='product_cat' 
				AND tt.term_taxonomy_id IN ($in_cats) ";
			}		
	
			$sql .="	
			WHERE 1=1 
			AND ct.meta_value IS NULL -- not trashed
			AND cs.meta_value IS NULL -- not previously sent ";
			if ($min_rating == 0) $sql .= "AND cm.meta_value IS NOT NULL  -- must have rating ";
			if ($min_rating > 0) $sql .= "AND cm.meta_value BETWEEN $min_rating AND 5 ";
/*			if (!empty($approved))
			$sql .=" 		    
			AND c.comment_approved = $approved " ;*/
			if (! empty($prod_id))
				$sql .="
				AND c.comment_post_ID IN ($prod_id) ";
			$sql .="				
				AND c.comment_ID = $comment_id 
				GROUP BY c.comment_ID ";
				Coupon_Emails_EmailFunctions::test_add_log('-- get_comment_sql -- ' . $sql . PHP_EOL  );				
		return $sql;
	
	}
	function get_join_orders_sql($minimum_orders, $days_since_last_order = "", $total_spent = "")
	{
		global $wpdb;
		$min_orders_join = "";
		if (! empty($days_since_last_order)) {
			$since_last_order_date = date('Y-m-d', strtotime('-' . $days_since_last_order . ' days'));
		}		
		if ($minimum_orders > 0) {
			if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
				$minimum_orders_str = ", COUNT(p.ID) AS orders_count, ROUND(SUM(p.total_amount),2) AS orders_total, DATE(max(p.date_created_gmt)) AS last_order";			
			} else {
				$minimum_orders_str = ", COUNT(p.ID) AS orders_count, ROUND(SUM(upmt.meta_value),2) AS orders_total, DATE(max(p.post_date)) AS last_order";
				$min_orders_join = "JOIN $wpdb->postmeta AS upmt ON upmt.post_id = p.ID AND upmt.meta_key = '_order_total'";
			}
		}
		else if ($minimum_orders == 0) {
			$minimum_orders_str = ", COUNT(p.ID) AS orders_count ";
			$min_orders_join = "";
		}
		else {
			$minimum_orders_str = "";
			$min_orders_join = "";
		}
		$sql = " JOIN (";
		
		if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
			$sql .="SELECT p.customer_id AS user_id $minimum_orders_str
			FROM {$wpdb->prefix}wc_orders AS p
				WHERE p.status = 'wc-completed'";
				if ($this->type == 'couponemails_onetimeemail' || $this->type == 'couponemails_onetimecoupon') {
				if (! empty($days_since_last_order))
					$sql .= " AND DATE(p.date_created_gmt)  {$this->days_since_sign} '" . $since_last_order_date . "' ";
				if (! empty($total_spent) )
					$sql .= " AND p.total_amount " . $total_spent . " ";
			}
			$sql .=	"
				GROUP BY user_id
				HAVING 1=1";
			if ($minimum_orders > 0)
				$sql .= " AND COUNT(p.ID)  >= " . $minimum_orders;
				if ($this->type == 'couponemails_onetimeemail' || $this->type == 'couponemails_onetimecoupon') {
				if (! empty($days_since_last_order))
					$sql .= " AND DATE(max(p.date_created_gmt))  {$this->days_since_sign} '" . $since_last_order_date . "' ";
				if (! empty($total_spent) )
					$sql .= " AND SUM(p.total_amount) " . $total_spent . " ";
			}			
		} else {
			$sql .="SELECT upm.meta_value AS user_id $minimum_orders_str
			FROM $wpdb->posts AS p
				JOIN $wpdb->postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
				$min_orders_join
				WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'";
				if ($this->type == 'couponemails_onetimeemail' || $this->type == 'couponemails_onetimecoupon') {
				if (! empty($days_since_last_order))
					$sql .= " AND DATE(p.post_date)  {$this->days_since_sign} '" . $since_last_order_date . "' ";
				if (! empty($total_spent) )
					$sql .= " AND upmt.meta_value " . $total_spent . " ";
			}
			$sql .=	"
				GROUP BY upm.meta_value
				HAVING 1=1";
			if ($minimum_orders > 0)
				$sql .= " AND COUNT(p.ID)  >= " . $minimum_orders;
				if ($this->type == 'couponemails_onetimeemail' || $this->type == 'couponemails_onetimecoupon') {
				if (! empty($days_since_last_order))
					$sql .= " AND DATE(max(p.post_date))  {$this->days_since_sign} '" . $since_last_order_date . "' ";
				if (! empty($total_spent) )
					$sql .= " AND SUM(upmt.meta_value) " . $total_spent . " ";
			}
		}
		$sql .= ") AS orders
		ON orders.user_id = u.ID ";
		
		return $sql ;
	}

	function get_select_previous_order()
	{
		global $wpdb;
		if (\COUPONEMAILS\Coupon_Emails_Helper_Functions::is_HPOS_in_use()) {
			$sql = ", (SELECT date(pp.date_created_gmt) AS previous_order
				FROM {$wpdb->prefix}wc_orders AS pp
				WHERE pp.status = 'wc-completed' AND pp.customer_id = u.ID
				ORDER BY pp.date_created_gmt desc
				LIMIT 1,1) AS previous_order";
		} else {
			$sql = ", (SELECT pp.post_date AS previous_order FROM {$wpdb->prefix}posts AS pp
				JOIN {$wpdb->prefix}postmeta AS upmp ON upmp.post_id = pp.ID AND upmp.meta_key = '_customer_user'
				WHERE pp.post_type = 'shop_order' AND pp.post_status = 'wc-completed'
				AND upmp.meta_value = u.ID
				ORDER BY pp.post_date desc
				LIMIT 1,1) AS previous_order";
		}
		return $sql;				
	}
	
	function get_where_previous_order($previous_order)
	{		
		switch ($previous_order) {
			case 1:
				$start_date = '(curdate() - INTERVAL 1 WEEK )';
				break;
			case 2:
				$start_date = '(curdate() - INTERVAL 1 MONTH )';
				break;
			case 3:
				$start_date = '(curdate() - INTERVAL 1 QUARTER)';
				break;
			case 4:
				$start_date = '(curdate() - INTERVAL 2 QUARTER)';
				break;
			case 5:
				$start_date = '(curdate() - INTERVAL 1 YEAR)';
				break;
			default:
			$start_date = '';
				break;				
		}
		
		$sql = "HAVING previous_order BETWEEN $start_date AND CURDATE()";
		return $sql;		
	}

	function get_orders_sql($minimum_orders, $days_after_order = "", $total_spent = "", $already_rated = "", $previous_order = "")
	{
		global $wpdb;
/*		if (! isset($minimum_orders) || $minimum_orders == '')
			return '';*/
		$sql = "";

		if ($minimum_orders == 0) {
			$sql .= "LEFT OUTER " . $this->get_join_orders_sql($minimum_orders);
			$sql .= " WHERE orders.orders_count IS null " ;
		} else {
			$sql .= $this->get_join_orders_sql($minimum_orders, $days_after_order, $total_spent);
			$sql .= $this->get_where();
			
			if ($days_after_order != '' && $days_after_order > 0 && 
				('couponemails_reorder' == $this->type || 'couponemails_afterorder' == $this->type || 'couponemails_reviewreminder' == $this->type)) 
			{
				$since_last_order_date = date('Y-m-d', strtotime('-' . $days_after_order . ' days'));
				$sql .= " AND orders.last_order  = '$since_last_order_date'";
			}
			
			if ($already_rated != '' && $already_rated > 0)
			{
				$rev = new \COUPONEMAILS\Coupon_Emails_Reviewed('couponemails_reviewreminder');
				$users_rated =  $rev->get_reviewer_ids();
				if ($already_rated == 1) {
					$not = 'NOT';
				}
				$sql .= 'AND u.ID ' . $not . ' IN (' . $users_rated . ')';
			}
			
			
			$sql .="
			GROUP BY u.ID
			$previous_order
			ORDER BY orders.orders_total DESC  ";
		}		

		return $sql;
	}
	
	function get_join_last_activity_sql($days_after_active)
	{
		global $wpdb;

		$sql = "JOIN {$wpdb->prefix}usermeta AS uma ON u.ID = uma.user_id AND uma.meta_key = 'wc_last_active' ";
		
		if (empty(! $days_after_active))
			$sql .= "AND DATE(FROM_UNIXTIME(uma.meta_value + $days_after_active * 86400 )) < CURDATE() ";
		return $sql;
	}
	
	function get_join_capabilities_sql($roles, $exclude_roles, $id_str = "u.ID")
	{
		global $wpdb;
		if (empty($roles) && empty($exclude_roles))
			return '';
		$sql = "
			JOIN $wpdb->usermeta AS um ON $id_str = um.user_id AND um.meta_key = '{$wpdb->prefix}capabilities' ";
		$sql .=	$this->get_capabilities_sql($roles);
		$sql .=	$this->get_capabilities_sql($exclude_roles, true);
		return $sql;
	}

	function get_capabilities_sql($roles, $exclude = false)
	{
		global $wpdb;
		if (empty($roles) )
			return '';	
		if ( ! is_array( $roles ) )
			$roles_array = array_map('trim',explode( ",", $roles ));
		else
			$roles_array = $roles;

		$sql = ' AND  (';
		$i = 1;
		foreach ( $roles_array as $role ) {
			if ($exclude)
				$sql .= ' um.meta_value  NOT  LIKE    \'%"' . $role . '"%\' ';
			else
				$sql .= ' um.meta_value LIKE    \'%"' . $role . '"%\' ';
			if ( $i < count( $roles_array ) )
				if ($exclude)
			$sql .= ' AND ';
			else
				$sql .= ' OR ';
			$i++;
		}
		$sql .= ' ) ';

		return $sql;
	}

	function get_spent_string($minimum_spent, $maximum_spent)
	{
		if (! is_numeric($minimum_spent) && ! is_numeric($maximum_spent))
			return "";
		if (! is_numeric($minimum_spent) && is_numeric($maximum_spent))
			return "<=" .$maximum_spent;
		if ( is_numeric($minimum_spent) && ! is_numeric($maximum_spent))
			return ">=" .$minimum_spent;
		if ( is_numeric($minimum_spent) &&  is_numeric($maximum_spent)) {
			if ($minimum_spent == $maximum_spent)
				return "=" .$minimum_spent;
			if ($minimum_spent < $maximum_spent)
				return "BETWEEN " .$minimum_spent . " AND " . $maximum_spent;
			if ($minimum_spent < $maximum_spent)
				return "BETWEEN " .$maximum_spent . " AND " . $minimum_spent;
		}
	}
}
?>