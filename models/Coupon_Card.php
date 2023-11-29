<?php
namespace COUPONEMAILS;


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Model that houses the logic of the Coupon Card module.
 * Public Model.
 *
 * @since 1.2.1
 */
 class Coupon_Emails_Coupon_Card
 {
    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    /**
     * @since 1.2.1
     * @access public
     */
    public $coupons = array();

    public $owned = array();
    
	public $expired = array();

    /**
     * Property that holds used or expired coupon by customer.
     *
     * @since 1.2.1
     * @access public
     * @var $coupons array
     */
    public $used = array();

    /**
     * Class constructor.
     *
     * @access public
     *
     */
    public function __construct(  ) {

    }

    /**
     * Get default attributes for coupon blocks.
     *
     * @since 1.2.1
     * @access public
     */
    public function get_default_attributes() {
        return array(
            'order_by'          => 'date/desc',
            'columns'           => 3,
            'count'             => 999, // Maximum number of coupons.
            'contentVisibility' => (object) array(
                'discount_value' => true,
                'description'    => true,
                'usage_limit'    => true,
                'schedule'       => true,
				'restriction'	 => false,
            ),
            'isPreview'         => false,
            'className'         => '',
        );
    }

	public function load_coupons_list_template( $coupons, $block_class, $attributes, $type = '' )
	{
		extract( $attributes ); 

		$classnames     = array( 'coupons-list-block' );
		$classnames[]   = $block_class;
		$column_percent = 100 / $columns;
		$styles         = array(
		'grid-template-columns:' . str_repeat( ' ' . $column_percent . '%', $columns ),
		'max-width: ' . ( $columns * 300 ) . 'px',
		);

		if ( isset( $className ) ) {
			$classnames[] = $className;
		}
		Coupon_Emails_Helper_Functions::load_template( 
		'coupons-list.php',
		array(
		'coupons'           => $coupons,
		'classnames'        => $classnames,
		'columns'           => $columns,
		'styles'            => $styles,
		'contentVisibility' => (object) $contentVisibility,
		'type'				=> $type,
		'single_class' 		=> 'two-columns-coupons',
		)
		);
	}
	
	/**
	* Load single coupon template.
	*
	* @since 3.1
	* @access public
	*
	* @param Advanced_Coupon $coupon     Coupon object.
	* @param object          $visibility Coupon visibility options.
	* @param string          $classname  Custom classname.
	*/
	public function load_single_coupon_template( $coupon, $visibility, $type, $classname = '' )
	{
		// don't proceed if the coupon doesn't exist.
		if ( ! $coupon->get_id() ) {
			return;
		}

		$schedule_string = $coupon->get_schedule_string();

		// make sure that content visibility values are not of type string.
		foreach ( $visibility as $key => $value ) {
			if ( 'true' === $value ) {
				$visibility->$key = true;
			} elseif ( 'false' === $value ) {
				$visibility->$key = false;
			}
		}

		$classnames = array(
		'single-coupon-block',
		'coupon-type-' . $coupon->get_discount_type(),
		);

		if ( $classname ) {
			$classnames[] = $classname;
		}

		if ($type != "owned") {
			$visibility->usage_limit = 0;
		}
	
		Coupon_Emails_Helper_Functions::load_template(
		'single-coupon.php',
		array(
		'coupon'             => $coupon,
		'has_usage_limit'    => $visibility->usage_limit && (int) $coupon->get_usage_limit(),
		'has_description'    => $visibility->description && $coupon->get_description(),
		'has_discount_value' => $visibility->discount_value && ( $coupon->get_amount() ),
		'has_schedule'       => $visibility->schedule && $schedule_string,
		'has_restriction'    => $visibility->restriction && (! empty($coupon->get_email_restriction())),
		'schedule_string'    => $schedule_string,
		'classnames'         => $classnames,
		'type'				 => $type,
		'single_class' 		=> '',
		)
		);
	}	
	
    /**
     * Get coupon cards markup owned by customer.
     *
     * @since 1.2.1
     * @access public
     *
     * @param array $attributes Block attributes.
     *
     * @return string
     */
    public function get_coupon_cards_owned_by_customer( $attributes = array() ) {
        // Merge attributes.
        $attributes = array_replace_recursive( $this->get_default_attributes(), $attributes );

        // Get owned coupons.
        $coupons = $this->get_customer_coupons_by_attributes( $attributes );

        // if current user has no coupons, then return an empty string.
        if ( empty( $coupons ) ) {
            return '';
        }

		// If no coupons are owned, then return an empty string.
		if ( empty( $coupons ) ) {
			return '';
		}
		ob_start();
		$this->load_coupons_list_template(
		$coupons,
		'coupons-by-customer-block',
		$attributes,
		$attributes['display_type'],
		);
		return ob_get_clean();	
    }

	public function get_customer_coupons_by_attributes( $attributes )
	{
		switch ( $attributes['display_type'] ) {
			case 'owned':
				$coupons = $this->_get_coupons_assigned_to_customer( $attributes['order_by'], $attributes['count'], $attributes['display_type'] );				
				break;

			case 'expired':
				$coupons  = $this->_get_coupons_assigned_to_customer( $attributes['order_by'], $attributes['count'], $attributes['display_type'] );
				break;

			default:
				$coupons = array();
				break;
		}		

		return $coupons;
	}
	
	function _get_coupons_assigned_to_customer( $order_by = 'date/desc', $count = 10, $type = "" )
	{
		global $wpdb;
		switch ($type) {
			case 'owned':
				$expired = "AND DATE(FROM_UNIXTIME(pm.meta_value )) >= CURDATE()";
				break;
			case 'expired':
				$expired = "AND DATE(FROM_UNIXTIME(pm.meta_value )) < CURDATE()";
				break;
			default:
				$expired = "";				
				break;			
		}
		// get proper sort query.
		$sort_queries = array(
		'date/desc'  => 'c.post_date_gmt DESC',
		'date/asc'   => 'c.post_date_gmt ASC',
		'title/asc'  => 'c.post_title ASC',
		'expire/asc' => 'cm2.meta_value ASC',
		);
		$sort_query   = isset( $sort_queries[ $order_by ] ) ? $sort_queries[ $order_by ] : $sort_queries['date/desc'];

		// build the query.
		// phpcs:disable
		$current_user = wp_get_current_user();
		$query = $wpdb->prepare(
		"SELECT c.ID FROM {$wpdb->posts} AS c
            INNER JOIN {$wpdb->postmeta} AS cm ON (cm.post_id = c.ID AND cm.meta_key = '%s')
			LEFT OUTER JOIN {$wpdb->prefix}postmeta AS pm ON c.ID = pm.post_id AND pm.meta_key = 'date_expires'
            WHERE c.post_type = 'shop_coupon'
                AND c.post_status = 'publish'
				AND cm.meta_value LIKE '%" . $current_user->user_email . "%' 
				$expired
            ORDER BY {$sort_query}
            LIMIT %d OFFSET 0",
			'customer_email',			
			$count
		);
		// phpcs:enable

		$coupons = array_map(
		function ( $id ) {
			return new Email_Coupon( $id );
		},
		$wpdb->get_col( $query ) // phpcs:ignore
		);
	
		return $coupons;
	}

    /**
     * Get coupon cards used or expired.
     *
     * @since 1.2.1
     * @access public
     *
     * @param array $attributes Block attributes.
     *
     * @return string
     */
    public function get_coupon_cards_used( $attributes = array() ) {
        // Query used and expired coupons.
        $posts = get_posts(
            array(
				'post_type'      => 'shop_coupon',
				'post_status' 	=> 'publish',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'usage_count',
						'value'   => 0,
						'compare' => '>',
					),
					array(
						'key'     => 'date_expires',
						'value'   => current_time( 'mysql' ),
						'compare' => '<',
						'type'    => 'DATETIME',
					),
				),
            )
        );

        // Validate posts.
        if ( empty( $posts ) ) {
            return '';
        }

        // Transform posts into coupon instance.
        $used = array();
        foreach ( $posts as $post ) {
            // Get coupon instance.
            $coupon      = new Email_Coupon( $post->ID );
            $get_used_by = array_map( 'intval', $coupon->get_used_by() );

            // Validate coupon.
            if (
                in_array( get_current_user_id(), $get_used_by, true ) || // Check if used coupon is owned by customer.
                $coupon->is_coupon_owned_by_user( get_current_user_id() ) // Check if expired coupon is owned by customer.
            ) {
                $used[ $post->ID ] = new Email_Coupon( $post->ID );
            }
        }
        $this->used = $used;

        // If no coupons are used or expired, then return an empty string.
        if ( empty( $used ) ) {
            return '';
        }

        // Get coupon cards markup.
        ob_start();
            $this->load_coupons_list_template(
                $used,
                '',
                array_replace_recursive( $this->get_default_attributes(), $attributes )
            );
        return ob_get_clean();
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
    */

    /**
     * Execute Coupon_Label class.
     *
     */
/*    public function run() {
        // Custom Fields.
        add_action( 'woocommerce_coupon_options', array( $this, 'display_coupon_card_custom_field' ), 10, 2 ); // Add coupon available field to coupon General tab.
        add_action( 'before_save_coupon', array( $this, 'save_coupon_card_field_value' ), 10, 2 ); // Save coupon tab data.
    }*/
}
