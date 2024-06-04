<?php
/*
 * Plugin Name: WooCommerce Datacap Payment Gateway
 * Version: 1.4.0
 * Plugin URI: https://dcap.com/
 * Description: Accept payments via Datacap
 * Author: Datacap
 * Author URI: https://dcap.com/
 * WC requires at least: 3.0
 * WC tested up to: 8.2.1
 *
 * Text Domain: woocommerce-gateway-datacap
 * Domain Path: /lang/
 *
 * @author Datacap
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if (!defined('WC_DATACAP_MODULE_NAME')) {
	define('WC_DATACAP_MODULE_NAME', 'woocommerce-gateway-datacap');
}
if (!defined('WC_DATACAP_PLUGIN_DIR')) {
	define('WC_DATACAP_PLUGIN_DIR', __DIR__);
}
if (!defined('WC_DATACAP_LANG_DIR')) {
	define('WC_DATACAP_LANG_DIR', WC_DATACAP_PLUGIN_DIR . "/languages");
}
if (!defined('WC_DATACAP_VERSION')) {
	define("WC_DATACAP_VERSION", "1.4.0");
}
if (!defined('WC_DATACAP_PAYMENT_METHOD_ID')) {
	define("WC_DATACAP_PAYMENT_METHOD_ID", "datacap");
}
if (!defined('WC_DATACAP_ENTRY_FILE')) {
	define("WC_DATACAP_ENTRY_FILE", __FILE__);
}

require_once(WC_DATACAP_PLUGIN_DIR . "/class-wc-datacap-gateway-entry.php");
$entry = new WC_Datacap_Gateway_Entry();
add_action('plugins_loaded', array($entry, 'bootstrap'), 0);
