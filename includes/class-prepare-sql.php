<?php

namespace COUPONEMAILS;

class PrepareSQL
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
		$sql ="
			SELECT umfn.meta_value AS user_firstname, umln.meta_value AS user_lastname, u.user_email, u.ID, orders.orders_count, orders.orders_total, orders.last_order
			FROM {$wpdb->prefix}users AS u
				JOIN {$wpdb->prefix}usermeta AS umfn ON u.ID =  umfn.user_id AND umfn.meta_key = 'first_name'
				JOIN {$wpdb->prefix}usermeta AS umln ON u.ID =  umln.user_id AND umln.meta_key = 'last_name'
				LEFT OUTER  JOIN (SELECT upm.meta_value AS user_id, SUM(upmt.meta_value) AS orders_total, DATE(max(p.post_date)) AS last_order, COUNT(p.ID) AS orders_count
			FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
				JOIN {$wpdb->prefix}postmeta AS upmt ON upmt.post_id = p.ID AND upmt.meta_key = '_order_total'
			WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'
			GROUP BY upm.meta_value
			) AS orders
			ON orders.user_id = u.ID
			WHERE u.user_email IN ($addresses)
			";
			EmailFunctions::test_add_log('-- ' . $this->type . PHP_EOL  . $sql);
		if ($as_objects) {
			$result = $wpdb->get_results($sql, OBJECT);
		} else {
			$result = $wpdb->get_results($sql, ARRAY_A);
		}		

		return $result;
	}
	public function get_users_filtered( $as_objects = false)
	{
		global $wpdb;

		$options = get_option($this->type . '_options');
		$emails =  isset( $options['email_address']) ? $options['email_address'] : "";
		$days_after_order =  isset( $options['days_after_order']) ? $options['days_after_order'] : "";
		if (! empty( $emails))
			return $this->get_users_from_emails ($emails, $as_objects);
		$minimum_orders = isset( $options['minimum_orders']) ? $options['minimum_orders'] : "";
		if ($this->type != 'onetimeemail') 	$minimum_orders = 1;
				
		$minimum_spent = isset( $options['minimum_spent']) ? $options['minimum_spent'] : "";
		$maximum_spent = isset( $options['maximum_spent']) ? $options['maximum_spent'] : "";
		$total_spent = $this->get_spent_string($minimum_spent, $maximum_spent);

		$roles = isset( $options['roles']) ? $options['roles'] : "";
		$exclude_roles = isset( $options['exclude-roles']) ? $options['exclude-roles'] : "";
		$bought_products = isset( $options['bought_products']) ? $options['bought_products'] : "";
		$not_bought_products = isset( $options['not_bought_products']) ? $options['not_bought_products'] : "";
		$bought_cats = isset( $options['bought_cats']) ? $options['bought_cats'] : "";
		$not_bought_cats = isset( $options['not_bought_cats']) ? $options['not_bought_cats'] : "";
		if (!empty($minimum_orders) && is_numeric($minimum_orders) && $minimum_orders >0) {
			$select_orders_str = ", orders.orders_count, orders.orders_total, orders.last_order";
		} else {
			$select_orders_str = "";
		}

		$sql = "SELECT u.user_email, umfn.meta_value AS user_firstname, umln.meta_value AS user_lastname, u.ID $select_orders_str
				FROM $wpdb->users AS u
				JOIN $wpdb->usermeta AS umfn ON u.ID =  umfn.user_id AND umfn.meta_key = 'first_name'
				JOIN $wpdb->usermeta AS umln ON u.ID =  umln.user_id AND umln.meta_key = 'last_name'  " .
		$this->get_join_capabilities_sql($roles, $exclude_roles) ;
		if ($minimum_orders != 0) {
			$sql .= $this->get_join_products($bought_products) ;
			$sql .= $this->get_join_products($not_bought_products, true) ;
			$sql .= $this->get_join_categories($bought_cats) ;
			$sql .= $this->get_join_categories($not_bought_cats, true) ;
		}

		$sql .= $this->get_orders_sql($minimum_orders, $days_after_order, $total_spent) 	;


		EmailFunctions::test_add_log('-- ' . $this->type . PHP_EOL  . $sql);
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
			$sql.= "SELECT pmc.meta_value AS user_id $field
			    FROM {$wpdb->prefix}posts p
					join {$wpdb->prefix}postmeta pmc on p.ID = pmc.post_id AND pmc.meta_key = '_customer_user'
					join {$wpdb->prefix}woocommerce_order_items oi on p.ID = oi.order_id AND oi.order_item_type = 'line_item'
					join {$wpdb->prefix}woocommerce_order_itemmeta oim on oim.order_item_id = oi.order_item_id AND oim.meta_key = '_product_id'
					join {$wpdb->prefix}woocommerce_order_itemmeta oimv on oimv.order_item_id = oi.order_item_id AND oimv.meta_key = '_variation_id'
			    WHERE
			        post_type = 'shop_order' AND post_status = 'wc-completed'
					AND (oimv.meta_value IN (" . $prod_str . ") OR oim.meta_value IN (" . $prod_str . "))
				GROUP BY user_id";
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
				LEFT OUTER JOIN wp7r_commentmeta AS cs ON c.comment_ID = cs.comment_id AND cs.meta_key = 'reviewedemail_sent' "
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
			AND cs.meta_value IS NULL -- not previously sent
			AND cm.meta_value IS NOT NULL  -- must have rating ";
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
			EmailFunctions::test_add_log('-- ' . $sql . PHP_EOL  );				
		return $sql;
	
	}
	function get_join_orders_sql($minimum_orders, $days_since_last_order = "", $total_spent = "")
	{
		global $wpdb;
		if (! empty($days_since_last_order)) {
			$since_last_order_date = date('Y-m-d', strtotime('-' . $days_since_last_order . ' days'));
		}
		if ($minimum_orders > 0)
			$minimum_orders_str = ", COUNT(p.ID) AS orders_count, ROUND(SUM(upmt.meta_value),2) AS orders_total, DATE(max(p.post_date)) AS last_order";
		else if ($minimum_orders == 0)
			$minimum_orders_str = ", COUNT(p.ID) AS orders_count ";
		else
			$minimum_orders_str = "";
		$sql = " JOIN (";
		$sql .="SELECT upm.meta_value AS user_id $minimum_orders_str
			FROM $wpdb->posts AS p
				JOIN $wpdb->postmeta AS upm ON upm.post_id = p.ID AND upm.meta_key = '_customer_user'
				JOIN $wpdb->postmeta AS upmt ON upmt.post_id = p.ID AND upmt.meta_key = '_order_total'
				WHERE p.post_type = 'shop_order' AND p.post_status = 'wc-completed'";
				if ( $this->type != 'onetimeemail') {
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
			if ($this->type == 'onetimeemail') {
				if (! empty($days_since_last_order))
					$sql .= " AND DATE(max(p.post_date))  {$this->days_since_sign} '" . $since_last_order_date . "' ";
				if (! empty($total_spent) )
					$sql .= " AND SUM(upmt.meta_value) " . $total_spent . " ";
			}
		$sql .= ") AS orders
		ON orders.user_id = u.ID ";

		return $sql;
	}

	function get_orders_sql($minimum_orders, $days_after_order = "", $total_spent = "")
	{
		global $wpdb;
		if (! isset($minimum_orders) || $minimum_orders == '')
			return '';
		$sql = "";

		if ($minimum_orders == 0) {
			$sql .= "LEFT OUTER " . $this->get_join_orders_sql($minimum_orders);
			$sql .= " WHERE orders.orders_count IS null " ;
		} else {
			$sql .= $this->get_join_orders_sql($minimum_orders, $days_after_order, $total_spent);
			$sql .= $this->get_where();
			$sql .="
			GROUP BY u.ID
			ORDER BY orders.orders_total DESC  ";
		}

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