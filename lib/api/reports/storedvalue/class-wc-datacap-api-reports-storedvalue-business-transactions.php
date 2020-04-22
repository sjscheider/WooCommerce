<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/class-wc-datacap-api-reports-abstract.php");

class WC_Datacap_API_Reports_StoredValue_Business_Transactions extends WC_Datacap_API_Reports_Abstract
{
    protected $method = self::GET;
    protected $url = "/v1/storedvalue/transactions?StartDate={StartDate}&EndDate={EndDate}";
}