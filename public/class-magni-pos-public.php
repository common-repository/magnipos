<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.magnipos.com/
 * @since      1.0.0
 *
 * @package    Magni_Pos
 * @subpackage Magni_Pos/public
 */

use Automattic\WooCommerce\Client;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Magni_Pos
 * @subpackage Magni_Pos/public
 * @author     Magnigeeks <info@magnipos.com>
 */
class Magni_Pos_Public {

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
	 * The namespace for rest api.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $namespace;


	/**
	 * The firebase server key for notificatoin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $firebase_server_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name         = $plugin_name;
		$this->version             = $version;
		$this->namespace           = $this->plugin_name . '/v1';
		$this->firebase_server_key = 'AAAAeQQfopc:APA91bFclLwyfXL89dLyAAaIpg1k7IriGuXULZKeoyA7EDzwvy_fz5Y4xwZqWMzcZiw0wMj3t4x1AOIdhNV3YEr7KgeZ-uS0GRTcDSfkpS1pK_02eZWZAutnxOoR91VoOEvUmBcEFYZe';
		add_action( 'plugins_loaded', array( $this, 'create_supplier_role' ) );
	}
	/**
	 * To create supplier/employee role in WordPress.
	 */
	public function create_supplier_role() {
		// Check if the role "Supplier" exists.
		$supplier_role = get_role( 'supplier' );
		$employee_role = get_role( 'employee' );
		// If the role does not exist, create it.
		if ( $supplier_role === null ) {
			add_role(
				'supplier',
				__( 'Supplier', 'magni-pos' ),
				array(
					'read'               => true, // Allow supplier to read posts and pages.
					'edit_products'      => true, // Allow supplier to edit their own products.
					'delete_products'    => true, // Allow supplier to delete their own products.
					'upload_files'       => true, // Allow supplier to upload files.
					'manage_woocommerce' => true, // Allow supplier to manage WooCommerce orders and settings.
				)
			);
		}
		if ( $employee_role === null ) {
			add_role(
				'employee',
				__( 'Employee', 'magni-pos' ),
				array(
					'read'               => true,
					'edit_products'      => true,
					'delete_products'    => true,
					'upload_files'       => true,
					'manage_woocommerce' => true,
				)
			);
		}
	}

	/**
	 * Send error to api.
	 *
	 * @param String  $code .
	 * @param String  $message .
	 * @param Integer $status_code .
	 * @return WP_Error $response .
	 */
	public function send_error( $code, $message, $status_code ) {
		return new WP_Error( $code, $message, array( 'status' => $status_code ) );
	}

	/**
	 * White listing forgot password api for jwt plugin
	 *
	 * @param Array $endpoints .
	 * @return Array $endpoints modified .
	 */
	public function white_listing_endpoints( $endpoints ) {
		$custom_endpoints = array(
			'/wp-json/' . $this->namespace . '/forgot_password',
		);

		return array_unique( array_merge( $endpoints, $custom_endpoints ) );
	}

	/**
	 * Check required WooCommerce plugin for KDS Addon
	 *
	 * @since 1.0.0
	 */
	public function check_required_plugins() {

		if ( ! class_exists( 'WooCommerce' ) ) {

			$plugin_link = 'https://wordpress.org/plugins/WooCommerce/';
			echo '<div id="notice" class="error"><p>' . sprintf( __( 'MagniPOS requires <a href="%1$s" target="_blank">WooCommerce</a> plugin to be installed. Please install and activate it', 'magni-pos' ), esc_url( $plugin_link ) ) . '</p></div>';
			deactivate_plugins( 'magnipos/magni-pos.php' );
		}
	}

	/**
	 * Check required WooCommerce plugin for KDS Addon
	 *
	 * @since 1.0.0
	 * @param array $status for order status.
	 */
	public function add_custom_shop_order_post_statuses( $status ) {

		return array_merge(
			array(
				'wc-pending-purchase'  => array(
					'label'                     => _x( 'Pending Purchase', 'Order status', 'woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					/* translators: %s: number of orders */
					'label_count'               => _n_noop( 'Pending Purchase <span class="count">(%s)</span>', 'Pending Purchase <span class="count">(%s)</span>', 'woocommerce' ),
				),

				'wc-ordered-purchase'  => array(
					'label'                     => _x( 'Ordered Purchase', 'Order status', 'woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					/* translators: %s: number of orders */
					'label_count'               => _n_noop( 'Ordered Purchase <span class="count">(%s)</span>', 'Ordered Purchase <span class="count">(%s)</span>', 'woocommerce' ),
				),

				'wc-received-purchase' => array(
					'label'                     => _x( 'Received Purchase', 'Order status', 'woocommerce' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					/* translators: %s: number of orders */
					'label_count'               => _n_noop( 'Received Purchase <span class="count">(%s)</span>', 'Received Purchase <span class="count">(%s)</span>', 'woocommerce' ),
				),
			),
			$status
		);
	}

	/**
	 * Add the endpoints to the API
	 */
	public function add_magni_pos_api_routes() {

		require_once __DIR__ . '/class-mp-report-top-sellers-controller.php';
		$controller = new MP_Report_Top_Sellers_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-product-variations-controller.php';
		$controller = new MP_Product_Variations_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-product-attributes-controller.php';
		$controller = new MP_Product_Attributes_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-product-attribute-terms-controller.php';
		$controller = new MP_Product_Attribute_Terms_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-order-refunds-controller.php';
		$controller = new MP_Order_Refunds_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-rest-api-controller.php';
		$controller = new MP_Rest_Api_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-customer-api-controller.php';
		$controller = new MP_Customer_Api_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-rest-orders-controller.php';
		$controller = new MP_REST_Orders_Controller();
		$controller->register_routes();

		require_once __DIR__ . '/class-mp-supplier-api-controller.php';
		$controller = new MP_Supplier_Api_Controller();
		$controller->register_routes();
	}


	/**
	 * To send new order notification to user .
	 *
	 * @param int    $order_id order id .
	 * @param Object $order new woocommerce order .
	 */
	public function new_order_notification( $order_id, $order ) {
		global $wpdb;
		$user_id        = get_current_user_id();
		$post_device_id = $order->get_meta( 'pos_device_id' );
		$order_from     = $order->get_meta( 'order_from' );
		$title          = esc_html__( 'You have received a new order !!', 'magni-pos' );
		$body           = sprintf( esc_html__( 'Order Id #%s', 'magni-pos' ), $order_id );

		$firebase_tokens = array();
		if ( empty( $order_from ) || $order_from == 'magni_pos' ) {
			$firebase_tokens = (array) $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s And user_id != %s",
					'mp_firebase_token',
					$user_id,
				),
			);
		} else {
			$firebase_tokens = (array) $wpdb->get_col(
				$wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
					'mp_firebase_token',
				),
			);
		}

		$notification_array = array(
			'title' => $title,
			'body'  => $body,
		);
		if ( empty( $post_device_id ) ) {
			$notification_array['sound']              = 'slow_spring_board.wav';
			$notification_array['android_channel_id'] = 'new_order_channel';
			$notification_array['channel_id']         = 'new_order_channel';
		}

		$data = array(
			'click_action'   => 'FLUTTER_NOTIFICATION_CLICK',
			'order_id'       => $order_id,
			'title'          => $title,
			'body'           => $body,
			'post_device_id' => $post_device_id,
			'type'           => 'new_order',
		);

		$fields = array(
			'registration_ids'  => $firebase_tokens,
			'data'              => $data,
			'notification'      => $notification_array,
			'content_available' => true,
			'priority'          => 'high',

		);
		$this->send_notification( $fields );
	}


	public function new_customer_billing_added( $billing_data ) {
		global $wpdb;
		$user_id       = get_current_user_id();
		$created_debit = esc_html__( 'Amount Credited', 'magni-pos' );
		$amount        = $billing_data['amount'];
		$due_date      = $billing_data['due_date'];
		$order_id      = $billing_data['order_id'];
		$user          = get_user_by( 'id', $billing_data['user_id'] );
		$customer_name = $user->display_name;
		if ( $amount < 0 ) {
			$created_debit = esc_html__( 'Amount Debited', 'magni-pos' );
		}
		$currency_symbol = get_woocommerce_currency();
		$title           = $currency_symbol . ' ' . $amount . ' ' . $created_debit;
		$body            = sprintf( __( 'Customer %s\'s account updated', 'magni-pos' ), $customer_name );
		if ( ! empty( $order_id ) ) {
			$body = $body . ' ' . sprintf( __( 'for order id #%s', 'magni-pos' ), $order_id );
		}
		if ( ! empty( $due_date ) ) {
			$body = $body . ' ' . sprintf( __( 'due is %s', 'magni-pos' ), $due_date );
		}

		$firebase_tokens = array();
		$firebase_tokens = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
				'mp_firebase_token',
			),
		);
        // error_log("asdfasd ".$wpdb->last_query);

		$notification_array = array(
			'title' => $title,
			'body'  => $body,
		);

		$data = array(
			'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
			'billing_id'   => $billing_data['id'],
			'title'        => $title,
			'body'         => $body,
			'type'         => 'bill_added',
		);

		$fields = array(
			'registration_ids'  => $firebase_tokens,
			'data'              => $data,
			'notification'      => $notification_array,
			'content_available' => true,
			'priority'          => 'high',

		);
		$this->send_notification( $fields );
	}


	public function send_notification( $fields ) {
		// error_log( 'sending notification :: ' . print_r( $fields, true ) );
		global $wpdb;
		$firebase_tokens = $fields['registration_ids'];
		if ( empty( $firebase_tokens ) ) {
			return;
		}
		$fields = json_encode( $fields );

		$headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'key=' . $this->firebase_server_key,
		);
		$args    = array(
			'headers' => $headers,
			'body'    => $fields,
			'method'  => 'POST',
		);

		$response = wp_remote_post( 'https://fcm.googleapis.com/fcm/send', $args );
        // error_log( 'response on notification sending :: '.print_r($response,true) );

		$responses = array();
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log( 'error on notification sending :: ' . $error_message );
		} else {
			$responses = json_decode( wp_remote_retrieve_body( $response ), true );
		}

		if ( ! empty( $responses ) ) {
			$response   = $responses['results'];
			$delete_ids = array();
			if ( is_array( $response ) ) {
				foreach ( $response as $key => $value ) {
					if ( array_key_exists( 'error', $value ) && 'NotRegistered' == $value['error'] ) {
						array_push( $delete_ids, $firebase_tokens[ $key ] );
					}
				}
			}
			if ( $delete_ids ) {
				$wpdb->query(
					"DELETE FROM {$wpdb->usermeta} WHERE meta_value IN  ( '" . implode( "','", $delete_ids ) . "')",
				);
			}
		}
	}

}
