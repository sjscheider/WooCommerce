<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/credit/class-wc-monetary-api-credit-abstract-transaction.php");

/**
 * Class WC_Monetary_API_Credit_Void_Return
 * @method getToken() Monetary Token
 * @method setToken(string $value) Monetary Token
 * @method getTrace() Echoed in Response
 * @method setTrace(string $value) Echoed in Response
 */
class WC_Monetary_API_Credit_Void_Return extends WC_Monetary_API_Credit_Abstract_Transaction
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/credit/return/{RefNo}/void';
}