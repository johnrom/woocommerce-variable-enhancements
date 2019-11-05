<?php
/**
 * Plugin Name: WooCommerce Variation Enhancements
 * Plugin URI: https://nimblelight.com/
 * Description: Enhancements for Woo Variation Functionality
 * Version: 3.7.0
 * Author: Nimblelight
 * Author URI: https://nimblelight.com
 * Text Domain: woocommerce-variable-enhancements
 *
 * @package WooCommerce_Variation_Enhancements
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class WooCommerce_Variation_Enhancements {
	public $plugin_slug = 'wcve';

	// @var string plugin path without trailing slash
	protected $plugin_path = false;
	// @var string plugin uri
	protected $plugin_url = false;

    public $admin;

    public function __construct() {
		spl_autoload_register( array( $this, 'autoloader') );

		$this->plugin_path 	= str_replace('\\', '/', plugin_dir_path( __FILE__ ) );
		$this->plugin_url 	= plugins_url( '/', __FILE__ );

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action('admin_notices', array( $this, 'enable_woocommerce_admin_notice' ) );

            return;
        }

        add_action('plugins_loaded', array( $this, 'initialize_classes' ) );
    }

    public function enable_woocommerce_admin_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e( 'Please enable WooCommerce in order to enable WooCommerce Variation Enhancements.', 'woocommerce-variable-enhancements' ); ?></p>
        </div>
        <?php
    }

    public function initialize_classes() {
        $this->variation_prices = new WCVE_Variation_Prices( $this );
        $this->variation_ajax = new WCVE_Variation_Ajax( $this );
    }

	//
	// Returns the plugin's path without a trailing slash
	// @return string
	//
	public function get_path( $subpath = '' ) {

		return $this->plugin_path . ltrim( $subpath, '/' );
	}

	//
	// AutoLoad Classes from /classes
	//
    function autoloader( $class_name ) {

        if ( 0 === stripos( $class_name . '_', $this->plugin_slug ) ) {
            $class_directory = $this->get_path('classes') . DIRECTORY_SEPARATOR;

			// if ( strrpos( $class_name, 'Controller' ) === strlen( $class_name ) - 10 ) {
			// 	$class_directory .= 'controllers' . DIRECTORY_SEPARATOR;
			// }

            $class_without_slug = str_replace( $class_name . '_', '', $class_name );
            $class_file = 'class-' . strtolower( str_replace( '_', '-', $class_without_slug ) ) . '.php';

            if ( file_exists( $class_directory . $class_file ) )
                require_once $class_directory . $class_file;
        }
    }
}

new WooCommerce_Variation_Enhancements();
