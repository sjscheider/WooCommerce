<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/class-wc-monetary-api-reports-abstract.php");

class WC_Monetary_API_Reports_StoredValue_Business_Transactions extends WC_Monetary_API_Reports_Abstract
{
    protected $method = self::GET;
    protected $url = "/v1/storedvalue/transactions?StartDate={StartDate}&EndDate={EndDate}";
}