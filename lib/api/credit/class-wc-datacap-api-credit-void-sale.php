<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/credit/class-wc-datacap-api-credit-abstract-transaction.php");

/**
 * Class WC_Datacap_API
 * @method getToken() Datacap Token
 * @method setToken(string $value) Datacap Token
 * @method getTrace() Echoed in Response
 * @method setTrace(string $value) Echoed in Response
 */
class WC_Datacap_API_Credit_Void_Sale extends WC_Datacap_API_Credit_Abstract_Transaction
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/credit/sale/{RefNo}/void';
}