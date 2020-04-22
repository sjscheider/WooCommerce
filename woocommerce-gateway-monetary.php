<?php
/*
 * Plugin Name: WooCommerce Monetary Payment Gateway
 * Version: 1.0
 * Plugin URI: https://monetary.co/
 * Description: Accept payments via Monetary
 * Author: Monetary
 * Author URI: https://monetary.co/
 * Requires at least: 3.0
 * Tested up to: 4.0
 *
 * Text Domain: woocommerce-gateway-monetary
 * Domain Path: /lang/
 *
 * @author Monetary
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WC_MONETARY_MODULE_NAME', 'woocommerce-gateway-monetary');
define('WC_MONETARY_PLUGIN_DIR', __DIR__);
define('WC_MONETARY_LANG_DIR', WC_MONETARY_PLUGIN_DIR . "/languages");
define("WC_MONETARY_VERSION", "1.0.0");
define("WC_MONETARY_PAYMENT_METHOD_ID", "monetary");
define("WC_MONETARY_ENTRY_FILE", __FILE__);

require_once(WC_MONETARY_PLUGIN_DIR . "/class-wc-monetary-gateway-entry.php");
$entry = new WC_Monetary_Gateway_Entry();
add_action('plugins_loaded', array($entry, 'bootstrap'), 0);
