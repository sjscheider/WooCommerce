<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-abstract.php");

/**
 * Class WC_Monetary_API_StoredValue_Void_Sale
 */
class WC_Monetary_API_StoredValue_Void_Sale extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/storedvalue/sale/{RefNo}/void';
}