<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.magnipos.com/
 * @since      1.0.0
 *
 * @package    Magni_Pos
 * @subpackage Magni_Pos/includes
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
 * @package    Magni_Pos
 * @subpackage Magni_Pos/includes
 * @author     Magnigeeks <info@magnipos.com>
 */
class Magni_Pos {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Magni_Pos_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'MAGNI_POS_VERSION' ) ) {
			$this->version = MAGNI_POS_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'magni-pos';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->create_extra_column();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Magni_Pos_Loader. Orchestrates the hooks of the plugin.
	 * - Magni_Pos_i18n. Defines internationalization functionality.
	 * - Magni_Pos_Admin. Defines all hooks for the admin area.
	 * - Magni_Pos_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-magni-pos-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-magni-pos-i18n.php';
		require_once plugin_dir_path( __DIR__ ) . 'vendor/autoload.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-magni-pos-admin.php';
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-admin-helper.php';

		/**
		 * The class responsible for defining all actions that releated to deleted data.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-mp-deleted-data-controller.php';

		/**
		 * The class responsible for defining all actions that releated to modify query.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-mp-modify-query-controller.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-magni-pos-public.php';
		require_once plugin_dir_path( __DIR__ ) . 'public/class-mp-order-stock-controller.php';

		require_once plugin_dir_path( __DIR__ ) . 'public/jwt/class-magni-pos-auth.php';
		require_once plugin_dir_path( __DIR__ ) . 'public/jwt/class-magni-pos-devices.php';

		$this->loader = new Magni_Pos_Loader();
	}

	/**
	 * Create extra column on table to get data for modify date .
	 *
	 * Table woocommerce_attribute_taxonomies
	 * column last_update.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function create_extra_column() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';

		$is_column = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = %s AND column_name = %s',
				$table_name,
				'mp_last_update',
			),
		);
		if ( empty( $is_column ) ) {
			$wpdb->query( "ALTER TABLE `{$table_name}` ADD `mp_last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP" );
		}
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Magni_Pos_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Magni_Pos_i18n();

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
		$plugin_admin = new Magni_Pos_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'magni_pos_setup_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'handle_registration' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public           = new Magni_Pos_Public( $this->get_plugin_name(), $this->get_version() );
		$deleted_data_controller = new MP_Deleted_Data_Controller();
		$modify_query_controller = new MP_Modify_Query_Controller();
		$order_stock_controller  = new MP_Order_Stock_Controller();

		$this->loader->add_action( 'admin_notices', $plugin_public, 'check_required_plugins' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'add_magni_pos_api_routes' );
		$this->loader->add_action( 'woocommerce_new_order', $plugin_public, 'new_order_notification', 10, 2 );
		
        $this->loader->add_action( 'magnipos_customer_billing_added', $plugin_public, 'new_customer_billing_added', 10, 1 );

		$this->loader->add_action( 'woocommerce_after_order_object_save', $modify_query_controller, 'calculate_profit_order', 10, 2 );
		$this->loader->add_action( 'woocommerce_refund_created', $modify_query_controller, 'new_refund_created', 10, 2 );

		$this->loader->add_filter( 'woocommerce_rest_prepare_report_sales', $modify_query_controller, 'get_custom_wc_report_sale', 10, 3 );
		$this->loader->add_filter( 'woocommerce_rest_prepare_shop_order_object', $modify_query_controller, 'custom_wc_rest_prepare_shop_order_object', 10, 1 );
		$this->loader->add_filter( 'woocommerce_rest_prepare_shop_order_refund_object', $modify_query_controller, 'custom_wc_rest_prepare_shop_order_object', 10, 1 );
		$this->loader->add_filter( 'woocommerce_rest_prepare_product_cat', $modify_query_controller, 'get_custom_wc_cat_child_count', 10, 3 );

		$this->loader->add_filter( 'woocommerce_reports_get_order_report_data_args', $modify_query_controller, 'get_wc_order_report_data_args', 10, 1 );

		$this->loader->add_action( 'delete_term', $deleted_data_controller, 'save_deleted_term', 10, 5 );
		$this->loader->add_action( 'after_delete_post', $deleted_data_controller, 'save_deleted_post', 10, 2 );
		$this->loader->add_action( 'delete_attachment', $deleted_data_controller, 'save_deleted_post', 10, 2 );
		$this->loader->add_action( 'delete_user', $deleted_data_controller, 'save_deleted_user', 10, 1 );
		$this->loader->add_action( 'woocommerce_tax_rate_deleted', $deleted_data_controller, 'save_deleted_tax', 10, 1 );
		$this->loader->add_action( 'woocommerce_attribute_deleted', $deleted_data_controller, 'save_deleted_attribute', 10, 3 );

		$this->loader->add_action( 'woocommerce_attribute_updated', $modify_query_controller, 'update_last_update_column', 10, 3 );

		$this->loader->add_filter( 'woocommerce_rest_product_query', $modify_query_controller, 'add_modified_after_filter_to_post', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_orders_prepare_object_query', $modify_query_controller, 'add_modified_after_filter_to_post', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_product_variation_query', $modify_query_controller, 'add_modified_after_filter_to_post', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_product_cat_query', $modify_query_controller, 'add_modified_after_filter_to_meta', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_product_tag_query', $modify_query_controller, 'add_modified_after_filter_to_meta', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_customer_query', $modify_query_controller, 'add_customer_filter_to_meta', 10, 2 );
		$this->loader->add_filter( 'woocommerce_rest_orders_prepare_object_query', $modify_query_controller, 'add_shop_order_filter_to_meta', 10, 2 );

		$this->loader->add_filter( 'woocommerce_customer_get_order_count', $modify_query_controller, 'custom_customer_get_order_count', 10, 2 );
		$this->loader->add_filter( 'woocommerce_customer_get_total_spent', $modify_query_controller, 'custom_customer_get_total_spent', 10, 2 );

		$this->loader->add_action( 'woocommerce_rest_insert_shop_order_object', $modify_query_controller, 'add_post_author_to_order', 10, 2 );

		$this->loader->add_action( 'woocommerce_product_options_pricing', $modify_query_controller, 'add_custom_purchase_price_woocommerce', 10, 2 );
		$this->loader->add_action( 'woocommerce_process_product_meta', $modify_query_controller, 'save_custom_purchase_price_woocommerce', 10, 2 );

		$this->loader->add_filter( 'edit_terms', $modify_query_controller, 'add_modified_date_terms_meta', 10, 1 );
		$this->loader->add_filter( 'create_term', $modify_query_controller, 'add_modified_date_terms_meta', 10, 1 );

		$this->loader->add_filter( 'jwt_auth_whitelist', $plugin_public, 'white_listing_endpoints' );

		$auth    = new Magni_Pos_Auth();
		$devices = new Magni_Pos_Devices();

		$this->loader->add_action( 'rest_api_init', $auth, 'register_rest_routes' );
		$this->loader->add_filter( 'rest_api_init', $auth, 'add_cors_support' );
		$this->loader->add_filter( 'rest_pre_dispatch', $auth, 'rest_pre_dispatch', 10, 3 );
		$this->loader->add_filter( 'determine_current_user', $auth, 'determine_current_user' );

		$this->loader->add_action( 'init', $modify_query_controller, 'woocommerce_stock_amount_filters', 999999 );
		$this->loader->add_filter( 'woocommerce_rest_shop_order_schema', $modify_query_controller, 'rest_shop_order_schema' );
		$this->loader->add_filter( 'woocommerce_rest_product_schema', $modify_query_controller, 'rest_product_schema' );
		$this->loader->add_filter( 'woocommerce_rest_product_variation_schema', $modify_query_controller, 'rest_product_schema' );
		$this->loader->add_filter( 'posts_where', $modify_query_controller, 'add_search_criteria_to_wp_query_where', 20, 1 );
		$this->loader->add_filter( 'woocommerce_get_catalog_ordering_args', $modify_query_controller, 'stock_status_value_on_order_item_view' );

		$this->loader->add_action( 'woocommerce_variation_options_pricing', $modify_query_controller, 'add_variation_options_pricing', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $modify_query_controller, 'save_variation_options_pricing', 10, 2 );
		$this->loader->add_filter( 'woocommerce_available_variation', $modify_query_controller, 'add_custom_field_variation_data', 10, 1 );
        $this->loader->add_filter( 'woocommerce_reports_get_order_report_query', $modify_query_controller, 'add_custom_order_report_query', 10, 1 );

		// supplier module.
		$this->loader->add_filter( 'woocommerce_register_shop_order_post_statuses', $plugin_public, 'add_custom_shop_order_post_statuses', 10, 1 );
		$this->loader->add_filter( 'woocommerce_order_status_changed', $order_stock_controller, 'update_order_status_callback', 10, 3 );
		// $this->loader->add_filter( 'mp_order_item_removed', $order_stock_controller, 'mp_order_item_removed_callbacked', 10, 2 );
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
	 * @return    Magni_Pos_Loader    Orchestrates the hooks of the plugin.
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
