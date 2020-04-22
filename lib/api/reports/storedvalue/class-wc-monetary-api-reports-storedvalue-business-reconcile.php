<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/class-wc-monetary-api-reports-abstract.php");

/**
 * Class WC_Monetary_API_Reports_StoredValue_Business_Reconcile
 */
class WC_Monetary_API_Reports_StoredValue_Business_Reconcile extends WC_Monetary_API_Reports_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $url = "/v1/storedvalue/reconcile?StartDate={StartDate}&EndDate={EndDate}";
}