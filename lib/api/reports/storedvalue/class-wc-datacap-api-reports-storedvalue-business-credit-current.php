<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/class-wc-datacap-api-reports-abstract.php");

/**
 * Class WC_Datacap_API_Reports_StoredValue_Business_Credit_Current
 */
class WC_Datacap_API_Reports_StoredValue_Business_Credit_Current extends WC_Datacap_API_Reports_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $url = "/v1/storedvalue/creditCurrent";
}