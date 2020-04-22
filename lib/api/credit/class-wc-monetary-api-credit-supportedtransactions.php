<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/credit/class-wc-monetary-api-credit-abstract-transaction.php");

/**
 * Class WC_Monetary_API_Credit_SupportedTransactions
 */
class WC_Monetary_API_Credit_SupportedTransactions extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $uri = '/credit';
}