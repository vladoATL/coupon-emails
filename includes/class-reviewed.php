<?php
namespace COUPONEMAILS;

class Reviewed
{
	public $comment_ID ;
	public $user_id;
	public $main_prod_ID ;
	public $author_email;
	public $product ;
	public $review_type ;
	public $rating;
	public $approved;
	public $inserted;

	public function __construct($comment_ID = "", $type = "woocommerce")
	{
		$this->review_type = $type;
		$this->comment_ID = $comment_ID;
	}

	public function get_review()
	{
		switch ($this->review_type) {
			case "site-review":
				$meta = get_post_meta($this->comment_ID, "_submitted");
				$this->main_prod_ID = $meta[0]["_post_id"];
				$this->rating = $meta[0]["rating"];
				$this->user_id = $meta[0]["author_id"];
				$product = wc_get_product($this->main_prod_ID);
				$this->product  = $product->get_title();
				$user = get_user_by( 'id', $this->user_id );
				$this->author_email = $user->user_email;
				if ( get_post_status ( $this->comment_ID ) == 'public' ) {
					$this->approved = 1;
				} else {
					$this->approved = 0;
				}
				break;

			default:
				break;
		}

		return $this;
	}


	public function filter_site_review()
	{
		global $wpdb;
		if ($this->approved == 0) return false;
		
		$funcs = new \COUPONEMAILS\EmailFunctions("reviewedemail", $this->product);
		$options = $funcs->options_array;

		if ( !empty($options['enabled']) && '1' == $options['enabled'] ) {

			$categories = isset( $options['bought_cats']) ? $options['bought_cats'] : "";
			$cat_str = !empty($categories) ? implode(',', $categories) : "";
			$products =  isset( $options['bought_products']) ? $options['bought_products'] : "";
			$prod_str = !empty($products) ? implode(',', $products) : "";
			$roles = isset( $options['roles']) ? $options['roles'] : "";
			$exclude_roles = isset( $options['exclude-roles']) ? $options['exclude-roles'] : "";
			$stars = isset( $options['stars']) ? $options['stars'] : 0;

			$rating = $this->rating;
			
			if ($rating<$stars)
				return false;
			if (! empty($cat_str)) {
				if ($this->is_in_categories($this->main_prod_ID, $cat_str) == 0) {
					return false;
				}
			}
			if (! empty($prod_str)) {
				if (! in_array($this->main_prod_ID, $products)) {
					return false;
				}
			}

			if (! empty($roles) || ! empty($exclude_roles)) {
				$user = get_userdata( $this->user_id );
				$user_roles = empty( $user ) ? array() : $user->roles;
				$is_in_roles =  (bool) array_intersect($roles, $user_roles );
				$is_in_exclude_roles =  (bool) array_intersect($exclude_roles, $user_roles);
				if (!($is_in_roles && ! $is_in_exclude_roles)) {
					return false;
				}				
			}

			return true;
		}
		return false;
	}

	function is_in_categories($product_id, $cat_str)
	{
		global $wpdb;
		$sql = "SELECT count(p.ID) FROM {$wpdb->prefix}posts p
				INNER JOIN {$wpdb->prefix}term_relationships r ON r.object_id=p.ID
				INNER JOIN {$wpdb->prefix}term_taxonomy t ON t.term_taxonomy_id = r.term_taxonomy_id AND t.taxonomy = 'product_cat'
				INNER JOIN {$wpdb->prefix}terms wt on wt.term_id = t.term_id AND wt.term_id IN ($cat_str)
				WHERE 1=1
				AND p.post_type = 'product'
				AND p.ID = $product_id
				GROUP BY p.ID";
				
		$cnt = $wpdb->get_var($sql);
		
		if (isset($cnt)) {
			return 1;
		} else {
			return 0;
		}		
	}

	
	public function filter_comment($user_id)
	{
		global $wpdb;
		$funcs = new \COUPONEMAILS\EmailFunctions("reviewedemail", $this->product);
		$options = $funcs->options_array;
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

			$sql_str = $sql->get_comment_sql($this->comment_ID, $stars, $roles, $exclude_roles,  $cat_str,  $prod_str)	;

			$id = $wpdb->get_var($sql_str);

			if (isset($id)) {
				$isOK = true;
			}
		}
		return $isOK;		
	}
	
	public function get_users_filtered($as_objects = false)
	{
		$sql = new PrepareSQL('reviewedemail', '=');
		return $sql->get_users_filtered();		
	}	
	
	public function get_reviewer_ids()
	{
		global $wpdb;
		$result = array();
		$ids = array();
		
    	$sql = "SELECT c.user_id AS ID
				FROM {$wpdb->prefix}comments AS c
				WHERE c.comment_approved = 1  AND c.comment_type = 'review'
				GROUP BY c.user_id ";				
		$result = $wpdb->get_results($sql, ARRAY_A);
		foreach ($result as $item) {
			$ids[] = $item['ID'];
		}
		//return implode(',',$ids) ;
		
		
		$sql = "SELECT pm.meta_value AS submitted
				FROM {$wpdb->prefix}posts AS p
				JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = '_submitted'
				WHERE p.post_type = 'site-review'
				GROUP BY pm.meta_value ";
		$result2 = $wpdb->get_results($sql, ARRAY_A);

		if ( ! empty($result2)) {
			foreach ($result2 as $item) {
				$a = maybe_unserialize($item['submitted'] );
				$user_id = '';
				if (isset( $a["author_id"]) ) {
					$user_id = $a["author_id"];
				} elseif (isset( $a["user_id"])){
					$user_id = $a["user_id"];
				}
					
				if (! empty($user_id)) {
					if (!in_array($user_id, $ids)) {
						$ids[] = $user_id;
					}
				}			
			}			
		}
		return implode(',',$ids) ;
	}
}
?>