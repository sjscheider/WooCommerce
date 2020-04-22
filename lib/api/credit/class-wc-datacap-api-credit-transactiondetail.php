<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/credit/class-wc-datacap-api-credit-abstract-transaction.php");

/**
 * Class WC_Datacap_API_Credit_TransactionDetail
 */
class WC_Datacap_API_Credit_TransactionDetail extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $uri = '/credit/{RefNo}';
}