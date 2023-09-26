<?php
namespace COUPONEMAILS;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://perties.sk
 * @since      1.0.0
 *
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Coupon_Emails
 * @subpackage Coupon_Emails/includes
 * @author     Vlado Laco <vlado@perties.sk>
 */
class Coupon_Emails {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Coupon_Email_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	protected $max_test_emails;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'COUPON_EMAILS_VERSION' ) ) {
			$this->version = COUPON_EMAILS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coupon-emails';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		if ( defined( 'MAX_TEST_EMAILS' ) ) {
			$this->max_test_emails = MAX_TEST_EMAILS;
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coupon_Email_Loader. Orchestrates the hooks of the plugin.
	 * - Coupon_Email_i18n. Defines internationalization functionality.
	 * - Coupon_Emails_Admin. Defines all hooks for the admin area.
	 * - Coupon_Emails_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupon-emails-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupon-emails-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coupon-emails-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		 require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coupon-emails-public.php';

		$this->loader = new Coupon_Email_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Coupon_Email_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Coupon_Email_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Coupon_Emails_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );		
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'coupon_emails_menu' );
		$this->loader->add_action('admin_init',  $plugin_admin, 'coupon_emails_init'  );
		$this->loader->add_action('transition_comment_status',  $plugin_admin, 'review_approve_comment_callback', 10 ,3  );
		$this->loader->add_action('comment_post',  $plugin_admin, 'review_comment_posted_callback' , 10, 3);
		
		$this->loader->add_action( 'wp_ajax_email_restore_settings', $plugin_admin, 'email_restore_settings' );		
		$this->loader->add_action( 'wp_ajax_couponemails_clear_log', $plugin_admin, 'couponemails_clear_log' );
		$this->loader->add_action( 'wp_ajax_email_make_test', $plugin_admin, 'email_make_test' );		
		$this->loader->add_action( 'wp_ajax_onetimeemails_send', $plugin_admin, 'onetimeemails_send' );		
		
		if ( is_plugin_active( 'site-reviews/site-reviews.php' ) ) {
			$this->loader->add_action('site-reviews/review/created',  $plugin_admin, 'site_reviews_comment_posted_callback' , 11, 2);
			$this->loader->add_action( 'post_updated', $plugin_admin, 'site_reviews_approve_comment_callback', 10, 3 );
		}			
		
		$this->loader->add_action('admin_init',  $plugin_admin, 'register_shop_coupon_cat_taxonomy' , 0 );	
	}	
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Coupon_Emails_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'woocommerce_account_account-coupons_endpoint', $plugin_public,  'account_coupons_page'  );
		$this->loader->add_action( 'woocommerce_account_referral_endpoint', $plugin_public,  'referral_page'  );
		$this->loader->add_action( 'init', $plugin_public, 'account_coupons_page_add_endpoint' );	
		$this->loader->add_action( 'init', $plugin_public, 'referral_page_add_endpoint' );	
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_public, 'coupon_emails_order_status_changed', 99, 4 );
		/*		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );*/
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Coupon_Email_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
