<?php
/**
 * REST API Orders controller
 *
 * Handles requests to the /orders endpoint.
 *
 * @package WooCommerce\RestApi
 * @since    2.6.0
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

defined( 'ABSPATH' ) || exit;

/**
 * REST API Orders controller class.
 *
 * @package WooCommerce\RestApi
 * @extends WC_REST_Orders_V2_Controller
 */
class MP_REST_Orders_Controller extends WC_REST_Orders_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'magni-pos/v1';

	/**
	 * Register the routes for orders.
	 */
	public function register_routes() {
		parent::register_routes();

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/items',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'order_items_callback' ),
					// 'permission_callback' => 'my_custom_permission_callback',
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					// 'args'                => $this->get_collection_params(),
				),
				// 'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/gst-report',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'order_items_gst_report_callback' ),
					// 'permission_callback' => 'my_custom_permission_callback',
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					// 'args'                => $this->get_collection_params(),
				),
				// 'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}


	/**
	 * Get products/attributes/terms  callback.
	 *
	 * @param WP_REST_Request $request .
	 * @return array|WP_Error
	 */
	public function order_items_callback( WP_REST_Request $request ) {

		$params     = $request->get_params();
		$product_id = $params['product_id'];
		$page       = isset( $params['page'] ) ? $params['page'] : 1;
		$per_page   = isset( $params['per_page'] ) ? $params['per_page'] : 10;

		global $wpdb;

		// Calculate offset and limit based on page and per_page values
		$offset = ( $page - 1 ) * $per_page;
		$limit  = $per_page;

		// Modify the SQL query to include offset and limit clauses
		$query = $wpdb->prepare(
			"
        SELECT
            oi.order_id,
            o.post_date,
            o.post_date_gmt,
            oim2.meta_key,
            oim2.meta_value
        FROM {$wpdb->prefix}woocommerce_order_items AS oi
        JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
        ON oi.order_item_id = oim.order_item_id
        JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
        ON oim.order_item_id = oim2.order_item_id
        JOIN {$wpdb->prefix}posts AS o
        ON oi.order_id = o.ID
        WHERE oi.order_item_type = 'line_item'
        AND (
            oim.meta_key = '_product_id'
            OR oim.meta_key = '_variation_id'
        )
        AND oim.meta_value = %d
        AND (
            oim2.meta_key = '_reduced_stock'
            OR oim2.meta_key = '_increased_stock'
        )
        GROUP BY oi.order_id, oim2.meta_key, oim2.meta_value
        ORDER BY o.post_date_gmt DESC
        LIMIT %d OFFSET %d
        ",
			$product_id,
			$limit,
			$offset
		);

		$order_stock_changes = $wpdb->get_results( $query );

		return new WP_REST_Response(
			$order_stock_changes,
			200
		);
	}

	/**
	 * Get order items for gst report
	 *
	 * @param WP_REST_Request $request .
	 * @return array|WP_Error
	 */
	public function order_items_gst_report_callback( WP_REST_Request $request ) {

		$params = $request->get_params();

		$page       = isset( $params['page'] ) ? $params['page'] : 1;
		$per_page   = isset( $params['per_page'] ) ? $params['per_page'] : 10;
		$start_date = isset( $params['start_date'] ) ? $params['start_date'] : 10;
		$end_date   = isset( $params['end_date'] ) ? $params['end_date'] : 10;

		global $wpdb;

		// Calculate offset and limit based on page and per_page values
		$offset = ( $page - 1 ) * $per_page;
		$limit  = $per_page;

		// Modify the SQL query to include offset and limit clauses
		$query = $wpdb->prepare(
			"



            SELECT
    oi.*,
    p.ID,
    p.post_date,
    customer_meta.meta_value AS user_id,
    first_name_meta.meta_value AS first_name,
    last_name_meta.meta_value AS last_name,
    state_meta.meta_value AS billing_state,
    user_meta.meta_value AS gst_number,
    item_qty_meta.meta_value AS quantity,
    item_subtotal_meta.meta_value AS subtotal,
    item_total_meta.meta_value AS total,
    item_product_meta.meta_value AS product_id,
    item_variation_meta.meta_value AS variation_id,
    hsn_meta.meta_value AS hsn_sac,
    cgst_meta.meta_value AS mp_tax_cgst,
    sgst_meta.meta_value AS mp_tax_sgst,
    cess_meta.meta_value AS mp_tax_cess
FROM
{$wpdb->prefix}posts AS p
JOIN {$wpdb->prefix}woocommerce_order_items AS oi
ON
    oi.order_id = p.ID
LEFT JOIN {$wpdb->prefix}postmeta AS customer_meta
ON
    p.ID = customer_meta.post_id AND customer_meta.meta_key = '_customer_user'
LEFT JOIN {$wpdb->prefix}postmeta AS first_name_meta
ON
    p.ID = first_name_meta.post_id AND first_name_meta.meta_key = '_billing_first_name'
LEFT JOIN {$wpdb->prefix}postmeta AS last_name_meta
ON
    p.ID = last_name_meta.post_id AND last_name_meta.meta_key = '_billing_last_name'
LEFT JOIN {$wpdb->prefix}postmeta AS state_meta
ON
    p.ID = state_meta.post_id AND state_meta.meta_key = '_billing_state'
LEFT JOIN {$wpdb->prefix}usermeta AS user_meta
ON
    customer_meta.meta_value = user_meta.user_id AND user_meta.meta_key = 'gstin_number'
LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_qty_meta
ON
    oi.order_item_id = item_qty_meta.order_item_id AND item_qty_meta.meta_key = '_qty'
LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_subtotal_meta
ON
    oi.order_item_id = item_subtotal_meta.order_item_id AND item_subtotal_meta.meta_key = '_line_subtotal'
LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_total_meta
ON
    oi.order_item_id = item_total_meta.order_item_id AND item_total_meta.meta_key = '_line_total'
LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_product_meta
ON
    oi.order_item_id = item_product_meta.order_item_id AND item_product_meta.meta_key = '_product_id'
LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS item_variation_meta
ON
    oi.order_item_id = item_variation_meta.order_item_id AND item_variation_meta.meta_key = '_variation_id'
LEFT JOIN {$wpdb->prefix}postmeta AS hsn_meta
ON
    (
        CASE WHEN item_variation_meta.meta_value = 0 THEN item_product_meta.meta_value ELSE item_variation_meta.meta_value
    END
) = hsn_meta.post_id AND hsn_meta.meta_key = 'hsn_sac'
LEFT JOIN {$wpdb->prefix}postmeta AS cgst_meta
ON
    item_product_meta.meta_value = cgst_meta.post_id AND cgst_meta.meta_key = 'mp_tax_cgst'
LEFT JOIN {$wpdb->prefix}postmeta AS sgst_meta
ON
    item_product_meta.meta_value = sgst_meta.post_id AND sgst_meta.meta_key = 'mp_tax_sgst'
LEFT JOIN {$wpdb->prefix}postmeta AS cess_meta
ON
    item_product_meta.meta_value = cess_meta.post_id AND cess_meta.meta_key = 'mp_tax_cess'
WHERE
    oi.order_item_type = 'line_item' AND p.post_status = 'wc-completed' AND p.post_type = 'shop_order'
    AND p.post_date BETWEEN %s AND %s
    LIMIT %d OFFSET %d;
        ",
			$start_date,
			$end_date,
			$limit,
			$offset
		);

		$order_stock_changes = $wpdb->get_results( $query );

		return new WP_REST_Response(
			$order_stock_changes,
			200
		);
	}

	/**
	 * Calculate coupons.
	 *
	 * @throws WC_REST_Exception When fails to set any item.
	 * @param WP_REST_Request $request Request object.
	 * @param WC_Order        $order   Order data.
	 * @return bool
	 */
	protected function calculate_coupons( $request, $order ) {
		do_action( 'woocommerce_apply_coupon_order_rest', $order, $request );

		if ( ! isset( $request['coupon_lines'] ) ) {
			return false;
		}

		// Validate input and at the same time store the processed coupon codes to apply.

		$coupon_codes = array();
		$discounts    = new WC_Discounts( $order );

		$current_order_coupons      = array_values( $order->get_coupons() );
		$current_order_coupon_codes = array_map(
			function ( $coupon ) {
				return $coupon->get_code();
			},
			$current_order_coupons
		);

		foreach ( $request['coupon_lines'] as $item ) {
			if ( ! empty( $item['id'] ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_coupon_item_id_readonly', __( 'Coupon item ID is readonly.', 'woocommerce' ), 400 );
			}

			if ( empty( $item['code'] ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_coupon', __( 'Coupon code is required.', 'woocommerce' ), 400 );
			}

			$coupon_code = wc_format_coupon_code( wc_clean( $item['code'] ) );
			$coupon      = new WC_Coupon( $coupon_code );

			// Skip check if the coupon is already applied to the order, as this could wrongly throw an error for single-use coupons.
			if ( ! in_array( $coupon_code, $current_order_coupon_codes, true ) ) {
				$check_result = $discounts->is_coupon_valid( $coupon );

				if ( is_wp_error( $check_result ) ) {
					throw new WC_REST_Exception( 'woocommerce_rest_' . $check_result->get_error_code(), $check_result->get_error_message(), 400 );
				}
				// echo "Asdfasdf";

			}

			$coupon_codes[] = $coupon_code;
		}

		// Remove all coupons first to ensure calculation is correct.
		foreach ( $order->get_items( 'coupon' ) as $existing_coupon ) {
			$order->remove_coupon( $existing_coupon->get_code() );
		}

		// Apply the coupons.
		foreach ( $coupon_codes as $new_coupon ) {
			$results = $order->apply_coupon( $new_coupon );

			if ( is_wp_error( $results ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_' . $results->get_error_code(), $results->get_error_message(), 400 );
			}
		}

		return true;
	}

	/**
	 * Prepare a single order for create or update.
	 *
	 * @throws WC_REST_Exception When fails to set any item.
	 * @param  WP_REST_Request $request Request object.
	 * @param  bool            $creating If is creating a new object.
	 * @return WP_Error|WC_Data
	 */
	protected function prepare_object_for_database( $request, $creating = false ) {
		$id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
		$order     = new WC_Order( $id );
		$schema    = $this->get_item_schema();
		$data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

		// Handle all writable props.
		foreach ( $data_keys as $key ) {
			$value = $request[ $key ];

			if ( ! is_null( $value ) ) {
				switch ( $key ) {
					case 'coupon_lines':
					case 'status':
						// Change should be done later so transitions have new data.
						break;
					case 'billing':
					case 'shipping':
						$this->update_address( $order, $value, $key );
						break;
					case 'line_items':
					case 'shipping_lines':
					case 'fee_lines':
						if ( is_array( $value ) ) {
							foreach ( $value as $item ) {
								if ( is_array( $item ) ) {
									if ( $this->item_is_null( $item ) || ( isset( $item['quantity'] ) && 0 === $item['quantity'] ) ) {
										$order->remove_item( $item['id'] );
										do_action( 'mp_order_item_removed', $item['id'], $order );
									} else {
										$this->set_item( $order, $key, $item );
									}
								}
							}
						}
						break;
					case 'meta_data':
						if ( is_array( $value ) ) {
							foreach ( $value as $meta ) {
								$order->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
							}
						}
						break;
					default:
						if ( is_callable( array( $order, "set_{$key}" ) ) ) {
							$order->{"set_{$key}"}( $value );
						}
						break;
				}
			}
		}

		/**
		 * Filters an object before it is inserted via the REST API.
		 *
		 * The dynamic portion of the hook name, `$this->post_type`,
		 * refers to the object type slug.
		 *
		 * @param WC_Data         $order    Object object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $order, $request, $creating );
	}





	/**
	 * Save an object data.
	 *
	 * @since  3.0.0
	 * @throws WC_REST_Exception But all errors are validated before returning any data.
	 * @param  WP_REST_Request $request  Full details about the request.
	 * @param  bool            $creating If is creating a new object.
	 * @return WC_Data|WP_Error
	 */
	protected function save_object( $request, $creating = false ) {
		try {
			$object = $this->prepare_object_for_database( $request, $creating );

			if ( is_wp_error( $object ) ) {
				return $object;
			}

			// Make sure gateways are loaded so hooks from gateways fire on save/create.
			WC()->payment_gateways();

			if ( ! is_null( $request['customer_id'] ) && 0 !== $request['customer_id'] ) {
				// Make sure customer exists.
				if ( false === get_user_by( 'id', $request['customer_id'] ) ) {
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_customer_id', __( 'Customer ID is invalid.', 'woocommerce' ), 400 );
				}

				// Make sure customer is part of blog.
				if ( is_multisite() && ! is_user_member_of_blog( $request['customer_id'] ) ) {
					add_user_to_blog( get_current_blog_id(), $request['customer_id'], 'customer' );
				}
			}

			if ( $creating ) {
				$object->set_created_via( 'rest-api' );
				$object->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
				$object->calculate_totals();
			} else {
				// If items have changed, recalculate order totals.
				if ( isset( $request['billing'] ) || isset( $request['shipping'] ) || isset( $request['line_items'] ) || isset( $request['shipping_lines'] ) || isset( $request['fee_lines'] ) || isset( $request['coupon_lines'] ) ) {
					$object->calculate_totals( true );
				}
			}

			// Set coupons.
			$this->calculate_coupons( $request, $object );

			/**
			 * Adds custom order statuses to the WooCommerce order status dropdown menu.
			 *
			 * This function adds three custom order statuses to the existing array of
			 * order statuses that is used to populate the order status dropdown menu
			 * on the WooCommerce admin pages. The custom statuses are "Pending Purchase",
			 * "Ordered Purchase", and "Received Purchase".
			 *
			 * @param array $statuses The existing array of order statuses.
			 * @return array The updated array of order statuses.
			 */
			function add_custom_order_statuses( $statuses ) {
				// Add your custom order statuses to the existing array of statuses.
				$custom_statuses = array(
					'wc-pending-purchase'  => _x( 'Pending Purchase', 'Order status', 'magni-pos' ),
					'wc-ordered-purchase'  => _x( 'Ordered Purchase', 'Order status', 'magni-pos' ),
					'wc-received-purchase' => _x( 'Received Purchase', 'Order status', 'magni-pos' ),
				);
				return array_merge( $custom_statuses, $statuses );
			}
			// Add the callback function to the filter hook.
			add_filter( 'wc_order_statuses', 'add_custom_order_statuses', 10, 1 );
			// Set status.
			if ( ! empty( $request['status'] ) ) {
				$object->set_status( $request['status'] );
			}

			$object->save();

			// Actions for after the order is saved.
			if ( true === $request['set_paid'] ) {
				if ( $creating || $object->needs_payment() ) {
					$object->payment_complete( $request['transaction_id'] );
				}
			}
			remove_filter( 'wc_order_statuses', 'add_custom_order_statuses', 10, 1 );

			return $this->get_object( $object->get_id() );
		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	/**
	 * Get formatted item data.
	 *
	 * @param WC_Order $order WC_Data instance.
	 * @return array
	 */
	protected function get_formatted_item_data( $order ) {
		$item_data       = parent::get_formatted_item_data( $order );
		$cpt_hidden_keys = array();

		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$cpt_hidden_keys = ( new \WC_Order_Data_Store_CPT() )->get_internal_meta_keys();
		}

		// XXX: This might be removed once we finalize the design for internal keys vs meta vs props in COT.
		if ( ! empty( $item_data['meta_data'] ) ) {
			$item_data['meta_data'] = array_filter(
				$item_data['meta_data'],
				function ( $meta ) use ( $cpt_hidden_keys ) {
					return ! in_array( $meta->key, $cpt_hidden_keys, true );
				}
			);
		}

		return $item_data;
	}

	/**
	 * Prepare objects query.
	 *
	 * @since  3.0.0
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array
	 */
	protected function prepare_objects_query( $request ) {
		// This is needed to get around an array to string notice in WC_REST_Orders_V2_Controller::prepare_objects_query.
		$statuses = $request['status'];
		unset( $request['status'] );

		// Prevents WC_REST_Orders_V2_Controller::prepare_objects_query() from generating a meta_query for 'customer'.
		// which COT can handle as a native field.
		$cot_customer =
			( OrderUtil::custom_orders_table_usage_is_enabled() && isset( $request['customer'] ) )
			? $request['customer']
			: null;

		if ( $cot_customer ) {
			unset( $request['customer'] );
		}

		$args = parent::prepare_objects_query( $request );

		$args['post_status'] = array();
		foreach ( $statuses as $status ) {
			if ( in_array( $status, $this->get_order_statuses(), true ) ) {
				$args['post_status'][] = 'wc-' . $status;
			} elseif ( 'any' === $status ) {

				foreach ( $this->get_order_statuses() as $status_order ) {
					$args['post_status'][] = 'wc-' . $status_order;
				}
				break;
			} else {
				$args['post_status'][] = $status;
			}
		}

		// Put the statuses back for further processing (next/prev links, etc).
		$request['status'] = $statuses;

		// Add back 'customer' to args and request.
		if ( ! is_null( $cot_customer ) ) {
			$args['customer']    = $cot_customer;
			$request['customer'] = $cot_customer;
		}

		return $args;
	}

	/**
	 * Get objects.
	 *
	 * @param  array $query_args Query args.
	 * @return array
	 */
	protected function get_objects( $query_args ) {
		// Do not use WC_Order_Query for the CPT datastore.
		if ( ! OrderUtil::custom_orders_table_usage_is_enabled() ) {
			return parent::get_objects( $query_args );
		}

		$query   = new \WC_Order_Query(
			array_merge(
				$query_args,
				array(
					'paginate' => true,
				)
			)
		);
		$results = $query->get_orders();

		return array(
			'objects' => $results->orders,
			'total'   => $results->total,
			'pages'   => $results->max_num_pages,
		);
	}

	/**
	 * Get the Order's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = parent::get_item_schema();

		$schema['properties']['coupon_lines']['items']['properties']['discount']['readonly'] = true;
		$schema['properties']['status']['enum'] = array_merge( array( 'pending-purchase', 'ordered-purchase', 'received-purchase' ), $this->get_order_statuses() );

		return $schema;
	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['status'] = array(
			'default'           => 'any',
			'description'       => __( 'Limit result set to orders which have specific statuses.', 'woocommerce' ),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
				'enum' => array_merge( array( 'any', 'trash', 'wc-pending-purchase', 'wc-ordered-purchase', 'wc-received-purchase' ), $this->get_order_statuses() ),
			),
			'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}
}
