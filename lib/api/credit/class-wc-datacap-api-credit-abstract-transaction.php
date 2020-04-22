<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/response/class-wc-datacap-api-response-credit-transaction.php");

/**
 * Class WC_Datacap_API_Credit
 */
class WC_Datacap_API_Credit_Abstract_Transaction extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $responseClass = 'WC_Datacap_API_Response_Credit_Transaction';
}