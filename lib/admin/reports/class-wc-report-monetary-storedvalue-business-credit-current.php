<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-monetary-api-reports-storedvalue-business-credit-current.php");

/**
 * Class WC_Report_Monetary_StoredValue_Business_Credit_Current
 */
class WC_Report_Monetary_StoredValue_Business_Credit_Current extends WC_Report_Monetary_Abstract
{
    /**
     * @var string
     */
    protected $requestClass = 'WC_Monetary_API_Reports_StoredValue_Business_Credit_Current';
}