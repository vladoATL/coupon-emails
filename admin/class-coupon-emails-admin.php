<?php

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
class Coupon_Email_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
 	public function couponemails_clear_log() {
 		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_couponemails_nonce_log' ) ) 
		{
	 		delete_option( 'couponemails_logs' );
	 		die();
		}
	}
	
 	public function namedayemail_make_test() {		 
 		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_namedayemail_nonce_test' ) ) 
		{
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions("namedayemail");
			$funcs->	couponemails_create($user, true);
	 		die();
		}
	}
		
	public function birthdayemail_make_test()
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_birthdayemail_nonce_test' ) ) {
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions("birthdayemail");
			$funcs->	couponemails_create($user, true);
			die();
		}
	}		
		
	public function reorderemail_make_test()
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_reorder_nonce_test' ) ) {
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions("reorderemail");
			$funcs->	couponemails_create($user, true);
			die();
		}
	}

	public function onetimeemail_make_test()
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_onetime_nonce_test' ) ) {
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions("onetimeemail");
			$funcs->	couponemails_create($user, true);
			die();
		}
	}		

	public function afterorderemail_make_test()
	{			
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_afterorder_nonce_test' ) ) {
			$user = wp_get_current_user();
			$funcs = new \COUPONEMAILS\EmailFunctions("afterorderemail");
			$funcs->	couponemails_create($user, true);
			die();
		}
	}			
	
		
 	public function namedayemail_restore_settings($add_new = false) {
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_namedayemail_nonce' ) || $add_new == true) 
		{
			namedayemail_save_defaults($add_new);			
			die();
		}
	}		
	
	public function reorderemail_restore_settings($add_new = false)
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_reorderemail_nonce' ) || $add_new == true) {
			reorderemail_save_defaults($add_new);
			die();
		}
	}
	
	public function afterorderemail_restore_settings($add_new = false)
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_afterorderemail_nonce' ) || $add_new == true) {
			afterorderemail_save_defaults($add_new);
			die();
		}
	}
		
	public function onetimeemail_restore_settings($add_new = false)
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_onetimeemail_nonce' ) || $add_new == true) {
			onetimeemail_save_defaults($add_new);
			die();
		}
	}
			
	public function birthdayemail_restore_settings($add_new = false)
	{
		if ( isset( $_POST['nonce'] ) && '' !== $_POST['nonce'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_birthdayemail_nonce' ) || $add_new == true) {
			birthdayemail_save_defaults($add_new);
			die();
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
