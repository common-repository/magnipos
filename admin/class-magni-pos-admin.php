<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.magnipos.com/
 * @since      1.0.0
 *
 * @package    Magni_Pos
 * @subpackage Magni_Pos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Magni_Pos
 * @subpackage Magni_Pos/admin
 * @author     Magnigeeks <info@magnipos.com>
 */
use MagniPos\Admin\Admin_Helper;

class Magni_Pos_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Magni_Pos_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Magni_Pos_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/magni-pos-admin.css', array(), $this->version, 'all' );
	}

	// Add menu Setting
	public function magni_pos_setup_menu() {
		$magni_pos_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNTYuOTI0IiBoZWlnaHQ9IjU2LjkyNCIgdmlld0JveD0iMCAwIDU2LjkyNCA1Ni45MjQiPg0KICA8ZGVmcz4NCiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImxpbmVhci1ncmFkaWVudCIgeDE9IjAuMjE0IiB5MT0iMC4wODEiIHgyPSIwLjYzNyIgeTI9IjAuOTg5IiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCI+DQogICAgICA8c3RvcCBvZmZzZXQ9IjAiIHN0b3AtY29sb3I9IiMzMGUyOTgiLz4NCiAgICAgIDxzdG9wIG9mZnNldD0iMC4wNDIiIHN0b3AtY29sb3I9IiMyY2UzOWMiLz4NCiAgICAgIDxzdG9wIG9mZnNldD0iMC4yNCIgc3RvcC1jb2xvcj0iIzFmZTZhOCIvPg0KICAgICAgPHN0b3Agb2Zmc2V0PSIwLjQ5NSIgc3RvcC1jb2xvcj0iIzE3ZTdhZiIvPg0KICAgICAgPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMTVlOGIxIi8+DQogICAgPC9saW5lYXJHcmFkaWVudD4NCiAgPC9kZWZzPg0KICA8cGF0aCBpZD0iUGF0aF8yMjciIGRhdGEtbmFtZT0iUGF0aCAyMjciIGQ9Ik0xNTQuNDc4LDY4Ny40aDBBMjguNDYsMjguNDYsMCwwLDAsMTI2LDcxNS4wMDZ2MjkuMzE4aDcuMDY1VjcxNi42ODdjMC0uMzExLS4wMzEtLjYyMi0uMDMxLS45NjV2LS43NDdhMjEuNDM1LDIxLjQzNSwwLDEsMSwyMS40MTMsMjIuMTkxYy0uNCwwLS43NzgsMC0xLjE4My0uMDMxSDE0OC40NHYtNy43NWg0LjkxN2MuMzQyLjAzMS43MTYuMDMxLDEuMDg5LjAzMUExMy42OTQsMTMuNjk0LDAsMSwwLDE0MC43NTIsNzE1LjZoMHYyLjAyM2gwdjI2LjY3MmgtLjAzMXYuMDMxaDEzLjcyNWEyOC40NzEsMjguNDcxLDAsMCwwLDI4LjQ3OC0yOC40Nzh2LS4wMzFBMjguMzkyLDI4LjM5MiwwLDAsMCwxNTQuNDc4LDY4Ny40WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTEyNiAtNjg3LjQpIiBmaWxsPSJ1cmwoI2xpbmVhci1ncmFkaWVudCkiLz4NCjwvc3ZnPg0K';

		add_menu_page( 'MagniPOS', 'MagniPOS', 'manage_options', 'magnipos', array( $this, 'magni_pos_init_ui' ), $magni_pos_icon );
	}

	public function magni_pos_init_ui() {
		load_template( dirname( __FILE__ ) . '/templates/magni-pos-admin-dashboard.php' );
	}



	/**
	 * Check for activation.
	 */
    
	public function handle_registration() {
		$license_key =  get_option( 'magni_pos_license_key' );
		// Bail if already connected.
		if ( ! empty( $license_key ) ) {
		    return;
		}

		$nonce = Admin_Helper::get( 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'magni_pos_register_product' ) ) {
			return;
		}

		$status = Admin_Helper::get( 'status' );
        if ($status && $redirect_to = Admin_Helper::get_registration_url($status)) { //phpcs:ignore
			wp_redirect( $redirect_to );
			exit;
		}
	}
}
