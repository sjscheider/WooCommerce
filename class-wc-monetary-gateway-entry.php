<?php
if (!defined('ABSPATH')) {
    exit;
}

/*
 * Admin custom CSS
 */
function load_custom_wp_admin_style($hook) {
    // Load only on WooCommerce Report - Monetary
    if($_SERVER["REQUEST_URI"] != '/wp-admin/admin.php?page=wc-reports&tab=monetary') return;

    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'style',  $plugin_url . "assets/css/monetary-styles.css");
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


define('WC_MONETARY_MODULE_NAME', 'woocommerce-gateway-monetary');
define('WC_MONETARY_PLUGIN_DIR', __DIR__);
define('WC_MONETARY_LANG_DIR', WC_MONETARY_PLUGIN_DIR . "/languages");
define("WC_MONETARY_VERSION", "1.0.0");
define("WC_MONETARY_PAYMENT_METHOD_ID", "monetary");
define("WC_MONETARY_ENTRY_FILE", __FILE__);

class WC_Monetary_Gateway_Entry
{
    /**
     * @var WC_Monetary_API
     */
    public static $api;

    /**
     * @var
     */
    protected $adminHandler;

    /**
     * WC_Monetary_Gateway_Entry constructor.
     */
    public function __construct()
    {
        $this->createApi();

        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/admin/class-wc-monetary-admin-handler.php');
        $this->adminHandler = new WC_Monetary_Admin_Handler(self::$api);
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

        load_plugin_textdomain(WC_MONETARY_MODULE_NAME, false, WC_MONETARY_LANG_DIR);

        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/class-wc-monetary-gateway.php');
        add_filter('woocommerce_payment_gateways', array($this, 'registerGateway'));

        add_filter('woocommerce_admin_reports', array($this, 'registerReports'));
        add_filter('wc_admin_reports_path', array($this, 'modifyReportsPath'), 0, 3);
    }

    /**
     * @return WC_Monetary_API
     */
    protected function createApi()
    {
        $pluginSettings = self::getGatewayConfig();

        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/class-wc-monetary-api.php');
        self::$api = new WC_Monetary_API();

        if (!isset($pluginSettings['public_key'], $pluginSettings['secret_key'], $pluginSettings['sandbox_mode'])) {
            return self::$api;
        }

        self::$api->setPublicKey($pluginSettings['public_key']);
        self::$api->setSecretKey($pluginSettings['secret_key']);
        self::$api->setIsSandbox($pluginSettings['sandbox_mode'] === "yes");

        return self::$api;
    }

    /**
     * @return array
     */
    public static function getGatewayConfig()
    {
        return get_option('woocommerce_monetary_settings', array()) ?: array();
    }

    /**
     * @param $methods
     * @return array
     */
    public function registerGateway($methods)
    {
        $methods[] = "WC_Monetary_Gateway";
        return $methods;
    }

    /**
     * @param $reports
     * @return array
     */
    public function registerReports($reports)
    {
        $new = require(WC_MONETARY_PLUGIN_DIR . "/lib/admin/monetary-reports.php");
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
        if (substr($name, 0, 9) !== "monetary-") {
            return $path;
        }

        return WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-" . $name . ".php";
    }

    /**
     * @return WC_Monetary_API
     */
    public static function getApiInstance()
    {
        return self::$api;
    }
}

$entry = new WC_Monetary_Gateway_Entry();
add_action('plugins_loaded', array($entry, 'bootstrap'), 0);
