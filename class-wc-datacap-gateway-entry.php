<?php
if (!defined('ABSPATH')) {
    exit;
}

/*
 * Admin custom CSS
 */
function load_custom_wp_admin_style($hook) {
    // Load only on WooCommerce Report - Datacap
    if($_SERVER["REQUEST_URI"] != '/wp-admin/admin.php?page=wc-reports&tab=datacap') return;

    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'style',  $plugin_url . "assets/css/datacap-styles.css");
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


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

class WC_Datacap_Gateway_Entry
{
    /**
     * @var WC_Datacap_API
     */
    public static $api;

    /**
     * @var
     */
    protected $adminHandler;

    /**
     * WC_Datacap_Gateway_Entry constructor.
     */
    public function __construct()
    {
        $this->createApi();

        require_once(WC_DATACAP_PLUGIN_DIR . '/lib/admin/class-wc-datacap-admin-handler.php');
        $this->adminHandler = new WC_Datacap_Admin_Handler(self::$api);
    }

    /**
     * Plugin bootstrap
     */
    public function bootstrap()
    {
        // If woo commerce is not installed, exit.
        if (!class_exists('WC_Payment_Gateway_CC')) {
            return;
        }

        load_plugin_textdomain(WC_DATACAP_MODULE_NAME, false, WC_DATACAP_LANG_DIR);

        require_once(WC_DATACAP_PLUGIN_DIR . '/lib/class-wc-datacap-gateway.php');
        add_filter('woocommerce_payment_gateways', array($this, 'registerGateway'));

        add_filter('woocommerce_admin_reports', array($this, 'registerReports'));
        add_filter('wc_admin_reports_path', array($this, 'modifyReportsPath'), 0, 3);
    }

    /**
     * @return WC_Datacap_API
     */
    protected function createApi()
    {
        $pluginSettings = self::getGatewayConfig();

        require_once(WC_DATACAP_PLUGIN_DIR . '/lib/api/class-wc-datacap-api.php');
        self::$api = new WC_Datacap_API();

        if (!isset($pluginSettings['public_key'], $pluginSettings['secret_key'], $pluginSettings['sandbox_mode'])) {
            return self::$api;
        }

        self::$api->setPublicKey($pluginSettings['public_key']);
        self::$api->setSecretKey($pluginSettings['secret_key']);
	    self::$api->setPayApiV2Key($pluginSettings['pay_api_v2_key']);
	    self::$api->setPayApiVersion($pluginSettings['pay_api_version']);
        self::$api->setIsSandbox($pluginSettings['sandbox_mode'] === "yes", $pluginSettings['pay_api_version']);

        return self::$api;
    }

    /**
     * @return array
     */
    public static function getGatewayConfig()
    {
        return get_option('woocommerce_datacap_settings', array()) ?: array();
    }

    /**
     * @param $methods
     * @return array
     */
    public function registerGateway($methods)
    {
        $methods[] = "WC_Datacap_Gateway";
        return $methods;
    }

    /**
     * @param $reports
     * @return array
     */
    public function registerReports($reports)
    {
        $new = require(WC_DATACAP_PLUGIN_DIR . "/lib/admin/datacap-reports.php");
        return array_merge($reports, $new);
    }

    /**
     * @param $path
     * @param $name
     * @param $class
     * @return string
     */
    public function modifyReportsPath($path, $name, $class)
    {
        if (substr($name, 0, 8) !== "datacap-") {
            return $path;
        }

        return WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-" . $name . ".php";
    }

    /**
     * @return WC_Datacap_API
     */
    public static function getApiInstance()
    {
        return self::$api;
    }
}

$entry = new WC_Datacap_Gateway_Entry();
add_action('plugins_loaded', array($entry, 'bootstrap'), 0);
