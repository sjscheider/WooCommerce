<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/class-wc-monetary-api-reports-abstract.php");

/**
 * Class WC_Monetary_API_Reports_StoredValue_Groups_Credit_Current
 */
class WC_Monetary_API_Reports_StoredValue_Groups_Credit_Current extends WC_Monetary_API_Reports_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $url = "/v1/storedvalue/groups/{id}/creditCurrent";
}