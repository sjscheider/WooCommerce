<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/response/class-wc-monetary-api-response-credit-transaction.php");

/**
 * Class WC_Monetary_API_Credit
 */
class WC_Monetary_API_Credit_Abstract_Transaction extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $responseClass = 'WC_Monetary_API_Response_Credit_Transaction';
}