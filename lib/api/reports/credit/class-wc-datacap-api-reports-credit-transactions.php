<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/class-wc-datacap-api-reports-abstract.php");

/**
 * Class WC_Datacap_API_Reports_Credit_Transactions
 */
class WC_Datacap_API_Reports_Credit_Transactions extends WC_Datacap_API_Reports_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $uri = '/V1/Credit/Transactions/{startDate}/{endDate}';
}