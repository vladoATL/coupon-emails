<?php
namespace COUPONEMAILS;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://perties.sk
 * @since      1.0.0
 *
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/admin
 * @author     Vlado Laco <vlado@perties.sk>
 */
class Coupon_Emails_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	
	private $coumn_exists;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;	
		$this->coumn_exists = false;	
	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coupon-emails-admin.css', array(), $this->version, 'all' );
	}

	/**Function to add menu. */
	public function coupon_emails_menu() {
		$menu_list = _x('Coupon Emails','menu', 'coupon-emails');
		add_submenu_page( 'woocommerce-marketing', $menu_list, $menu_list, 'manage_options', 'couponemails', array( $this, 'couponemails_menu_init' ) );	
	}
	public function couponemails_menu_init()
	{
		require plugin_dir_path( __FILE__ ) . 'partials/coupon-emails-admin-display.php';
	}
	public function coupon_emails_init(){
		register_setting( 'namedayemail_plugin_options', 'namedayemail_options', 
		    array('sanitize_callback' => array( $this, 'namedayemail_validate_options' ),)
		 );
		register_setting( 'couponemails_plugin_log_options', 'couponemails_logs', 
			array('sanitize_callback' => array( $this, 'namedayemail_validate_log_options' ),) 
		 );	
		 register_setting( 'couponemails_plugin_options', 'couponemails_options',
		 array('sanitize_callback' => array( $this, 'couponemails_validate_options' ),)
		 );		
		 register_setting( 'birtdayemail_plugin_options', 'birthdayemail_options',
		 array('sanitize_callback' => array( $this, 'birthdayemail_validate_options' ),)
		 );	
		 register_setting( 'onetimeemail_plugin_options', 'onetimeemail_options',
		 array('sanitize_callback' => array( $this, 'onetimeemail_validate_options' ),)
		 );	
		 register_setting( 'reorderemail_plugin_options', 'reorderemail_options',
		 array('sanitize_callback' => array( $this, 'reorderemail_validate_options' ),)
		 );	
		 register_setting( 'afterorderemail_plugin_options', 'afterorderemail_options',
		 array('sanitize_callback' => array( $this, 'afterorderemail_validate_options' ),)
		 );	
		 register_setting( 'reviewedemail_plugin_options', 'reviewedemail_options',
		 array('sanitize_callback' => array( $this, 'reviewedemail_validate_options' ),)
		 );	
		 register_setting( 'reviewreminderemail_plugin_options', 'reviewreminderemail_options',
		 array('sanitize_callback' => array( $this, 'reviewreminderemail_validate_options' ),)
		 );
		 register_setting( 'expirationreminderemail_plugin_options', 'expirationreminderemail_options',
		 array('sanitize_callback' => array( $this, 'expirationreminderemail_validate_options' ),)
		 );	
		 register_setting( 'referralemail_plugin_options', 'referralemail_options',
		 array('sanitize_callback' => array( $this, 'referralemail_validate_options' ),)
		 );		
		 register_setting( 'referralconfirmationemail_plugin_options', 'referralconfirmationemail_options',
		 array('sanitize_callback' => array( $this, 'referralconfirmationemail_validate_options' ),)
		 );			  	 
	}
	function referralconfirmationemail_validate_options($input)
	{
		return $input;
	}	
	function referralemail_validate_options($input)
	{
		return $input;
	}	
	function reviewedemail_validate_options($input)
	{
		return $input;
	}	
	function afterorderemail_validate_options($input)
	{
		return $input;
	}	
	function onetimeemail_validate_options($input)
	{
		return $input;
	}
	function reorderemail_validate_options($input)
	{
		return $input;
	}		
	function namedayemail_validate_options($input) 	{
		return $input;
	}
	function birthdayemail_validate_options($input)
	{
		return $input;
	}	
	function namedayemail_validate_log_options($input) {
		return $input;
	}
	function couponemails_validate_options($input)
	{
		return $input;
	}
	function reviewreminderemail_validate_options($input)
	{
		return $input;
	}	
	function expirationreminderemail_validate_options($input)
	{
		return $input;
	}	
 	public function couponemails_clear_log() {
 		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_couponemails_nonce_log' ) ) 
		{
	 		delete_option( 'couponemails_logs' );
	 		die();
		}
	}
	
	public function review_comment_posted_callback($comment_id, $comment_approved, $comment)
	{
		$comment_data = (Object) $comment;		
		if ($comment_data->comment_approved == 1)
		{
			$this->	reviewedemail_create_coupon($comment_data, $comment_id);
		}
	}
	
	public function site_reviews_comment_posted_callback($review, $command)
	{	
		$reviewed_obj = new COUPONEMAILS\Reviewed($review->ID ,"site-review");
		$reviewed = $reviewed_obj->get_review();
		$reviewed->inserted = 1;
		if (get_post_status($review->ID) == 'publish') {
			$reviewed->approved = 1;
		}
		$this->site_reviews_create_coupon($reviewed);
	}		
	
	function site_reviews_create_coupon($reviewed)
	{
		$funcs = new \COUPONEMAILS\EmailFunctions("reviewedemail", $reviewed->product);
		$meta = get_post_meta($reviewed->comment_ID,"reviewedemail_sent", true);
		if (! empty($meta)) {
			$funcs->couponemails_add_log(sprintf( _x( "%s has already received coupon for this review", "Log file", "coupon-emails" ), $reviewed->author_email ) . ": " . $meta);
			return 0;
		}
		$isOK =  $reviewed->filter_site_review($reviewed->user_id, $reviewed->main_prod_ID, $reviewed->comment_ID);
		if ($isOK) {
			$user = get_user_by( 'id', $reviewed->user_id );
			$coupon = $funcs->	couponemails_create($user);
			$meta_id = update_post_meta($reviewed->comment_ID, "reviewedemail_sent", $coupon);
		}			
	}	
	public function review_approve_comment_callback($new_status, $old_status, $comment)
	{
		if ($old_status != $new_status) {
			if ($new_status == 'approved') {
				$this->	reviewedemail_create_coupon($comment);
			}
		}		
	}
	
	public function site_reviews_approve_comment_callback($post_ID, $post_after, $post_before)
	{
		$post_type = get_post_type($post_ID);
		if ($post_type != "site-review") return;
		if (get_post_status($post_before) != get_post_status($post_after)) {
			if (get_post_status($post_after) == 'publish') {
				$reviewed_obj = new COUPONEMAILS\Reviewed($post_ID ,"site-review");
				$reviewed = $reviewed_obj->get_review();
				$reviewed->approved = 1;
				$reviewed->inserted = 0;
				$this->site_reviews_create_coupon($reviewed);
			}
		}			
		
	}
		
	function reviewedemail_create_coupon($comment, $comment_ID = 0)
	{				
		if ($comment_ID == 0) $comment_ID = $comment->comment_ID ;
		$user_id = $comment->user_id;
		$comment_main_prod_ID = $comment->comment_post_ID;
		$comment_author_email = $comment->comment_author_email;
		$product = wc_get_product( $comment_main_prod_ID );
		$product_name = $product->get_title();			
		$funcs = new \COUPONEMAILS\EmailFunctions("reviewedemail", $product_name);
		$meta = get_comment_meta($comment_ID,"reviewedemail_sent", true);
		if (! empty($meta)) {
			$funcs->couponemails_add_log(sprintf( _x( "%s has already received coupon for this review", "Log file", "coupon-emails" ), $reviewed->comment_author_email ) . ": " . $meta);
			return 0;
		}
		$isOK =  $funcs->reviews_filtered($user_id, $comment_main_prod_ID, $comment_ID);
		if ($isOK) {
			$user = get_user_by( 'id', $user_id );
			$coupon = $funcs->	couponemails_create($user);
			$meta_id = update_comment_meta($comment_ID,"reviewedemail_sent",$coupon);
		}		
	}
	
	public function onetimeemails_send(){
		//\COUPONEMAILS\EmailFunctions::test_add_log('-- onetimeemails_send -- '  . PHP_EOL  );	
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce']
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_onetime_nonce_send' ) ) {
			
			$onetimes = new \COUPONEMAILS\Onetimes();
			$result = $onetimes->send_to_users_filtered();	
			wp_die();
		}		
	}
	
	public function email_make_test()
	{		
		if ( isset( $_POST['option_name'] )) {
			$option_name = $_POST['option_name'];
		} else {
			wp_die();
		}

		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] 
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_' .$option_name . '_nonce_test' ) ) 
		{
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions($option_name);
			if (in_array($option_name, array('expirationreminderemail','referralconfirmationemail'))) {
				$args = array("coupon" => "TESTCOUPON");
				$funcs->	couponemails_create($user, true, $args);
			} else {
				$funcs->	couponemails_create($user, true);	
			}
			wp_die();
		}
	}		
		
	public function email_restore_settings($add_new = false)
	{
		if ( isset( $_POST['option_name'] )) {
			$option_name = $_POST['option_name'];
		} else {
			wp_die();
		}
				
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce']
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_' .$option_name . '_nonce' ) || $add_new == true) 
		{				
			switch ($option_name) {
				case "namedayemail":
					namedayemail_save_defaults($add_new);
					break;
				case "reviewedemail":
					reviewedemail_save_defaults($add_new);
					break;
				case "reorderemail":
					reorderemail_save_defaults($add_new);
					break;
				case "afterorderemail":
					afterorderemail_save_defaults($add_new);
					break;
				case "onetimeemail":
					onetimeemail_save_defaults($add_new);
					break;
				case "birthdayemail":
					birthdayemail_save_defaults($add_new);
					break;
				case "reviewreminderemail":
					reviewreminderemail_save_defaults($add_new);
					break;		
				case "expirationreminderemail":
					expirationreminderemail_save_defaults($add_new);
					break;	
				case "referralemail":
					referralemail_save_defaults($add_new);
					break;	
				case "referralconfirmationemail":
					referralconfirmationemail_save_defaults($add_new);
					break;									
				}						
			wp_die();
		}
	}
		
	public function add_coupon_category_column( $columns )
	{
		foreach ( $columns as $key => $column ) {
			if ( $key === 'coupon_categories' ) {
				$this->coumn_exists = true;
				break;
			} 
		}

		if (! $this->coumn_exists) {
			$columns['coupon_categories'] = __( 'Categories', 'coupon-emails' );
		}
		return $columns;		
	}
	
	public function add_coupon_email_column( $columns )
	{
		$columns['restricted_email'] = __( 'Emails', 'coupon-emails' );
		return $columns;
	}
		
	public function coupon_category_column_content( $column, $coupon_id )
	{		
		if ( 'coupon_categories' !== $column ) {
			return;
		}		
		if ( $this->coumn_exists) {
			return;
		}

		$categories = get_the_terms( $coupon_id, 'shop_coupon_cat' );

		if ( ! is_array( $categories ) || empty( $categories ) ) {
			echo '–';
			return;
		}

		$content = array_map(
		function ( $term ) {
			$filter_link = admin_url( 'edit.php?post_type=shop_coupon&' . 'shop_coupon_cat' . '=' . $term->slug );
			return sprintf( '<a href="%s">%s</a>', $filter_link, $term->name  );
		},
		$categories
		);

		echo wp_kses_post( implode( ', ', $content ) );
	}
	
	public function coupon_email_column_content( $column, $coupon_id )
	{
		if ( 'restricted_email' !== $column ) {
			return;
		}

		//$emails = get_the_terms( $coupon_id, 'customer_email' );
		$emails = get_metadata( 'post', $coupon_id, "customer_email" );
		if ( ! is_array( $emails ) || empty( $emails ) ) {
			echo '–';
			return;
		}

		$content = array_map(
		function ( $term ) {
			$filter_link = admin_url( 'edit.php?post_type=shop_coupon&' . 'shop_coupon_cat' . '=' . $term->slug );
			return sprintf( '<a href="%s">%s</a>', $filter_link, $term->name  );
		},
		$emails
		);

		echo wp_kses_post( implode( ', ', $emails[0] ) );
		//echo print_r($emails, true);
	}
		
	public function add_shop_coupon_category_filter( $post_type )
	{
		global $wp_query;
		
		if ( $this->coumn_exists) {
			return;
		}
		if ( 'shop_coupon' !== $post_type ) {
			return;
		}

		$args = array(
		'pad_counts'         => true,
		'show_count'         => true,
		'hierarchical'       => true,
		'hide_empty'         => false,
		'show_uncategorized' => true,
		'orderby'            => 'name',
		'selected'           => isset( $wp_query->query_vars[ 'shop_coupon_cat'] ) ? $wp_query->query_vars[ 'shop_coupon_cat'] : '',
		'show_option_none'   => __( 'Select a category', 'coupon-emails' ),
		'option_none_value'  => '',
		'value_field'        => 'slug',
		'taxonomy'           => 'shop_coupon_cat',
		'name'               => 'shop_coupon_cat',
		'class'              => 'dropdown_shop_coupon_cat',
		);

		wp_dropdown_categories( $args );
	}
			
	/**
	* Register coupon categories taxonomy.
	*
	* @since 1.0
	*/
	function register_shop_coupon_cat_taxonomy()
	{
		$taxonomy_exist = taxonomy_exists( 'shop_coupon_cat' );
		if (! $taxonomy_exist) {
			$labels = array(
			'name'                       => _x( 'Coupon Categories', 'Taxonomy General Name', 'coupon-emails' ),
			'singular_name'              => _x( 'Coupon Category', 'Taxonomy Singular Name', 'coupon-emails' ),
			'menu_name'                  => __( 'Coupon Categories', 'coupon-emails' ),
			'all_items'                  => __( 'All Categories', 'coupon-emails' ),
			'parent_item'                => __( 'Parent Category', 'coupon-emails' ),
			'parent_item_colon'          => __( 'Parent Category:', 'coupon-emails' ),
			'new_item_name'              => __( 'New Category Name', 'coupon-emails' ),
			'add_new_item'               => __( 'Add New Category', 'coupon-emails' ),
			'edit_item'                  => __( 'Edit Category', 'coupon-emails' ),
			'update_item'                => __( 'Update Category', 'coupon-emails' ),
			'view_item'                  => __( 'View Category', 'coupon-emails' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'coupon-emails' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'coupon-emails' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'coupon-emails' ),
			'popular_items'              => __( 'Popular Categories', 'coupon-emails' ),
			'search_items'               => __( 'Search Categories', 'coupon-emails' ),
			'not_found'                  => __( 'Not Found', 'coupon-emails' ),
			'no_terms'                   => __( 'No categories', 'coupon-emails' ),
			'items_list'                 => __( 'Categories list', 'coupon-emails' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'coupon-emails' ),
			);

			$capabilities = array(
			'manage_terms' => 'manage_woocommerce',
			'edit_terms'   => 'manage_woocommerce',
			'delete_terms' => 'manage_woocommerce',
			'assign_terms' => 'manage_woocommerce',
			);

			$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'capabilities'      => $capabilities,
			'show_in_rest'      => true,
			);

			register_taxonomy( 'shop_coupon_cat', array( 'shop_coupon' ), $args );
		}
	}	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coupon_Email_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coupon_Email_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		 wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coupon-emails-admin.js', array( 'jquery' ), $this->version, false );
	}
	
}
