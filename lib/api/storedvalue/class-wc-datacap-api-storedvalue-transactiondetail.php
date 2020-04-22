<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-abstract.php");

/**
 * Class WC_Datacap_API_StoredValue_TransactionDetail
 */
class WC_Datacap_API_StoredValue_TransactionDetail extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $uri = '/storedvalue/{RefNo}';
}