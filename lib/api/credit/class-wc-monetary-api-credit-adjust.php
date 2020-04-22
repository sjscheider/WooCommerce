<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/credit/class-wc-monetary-api-credit-abstract-transaction.php");

/**
 * Class WC_Monetary_API_Credit_Adjust
 * @method getToken() Monetary Token
 * @method setToken(string $value) Monetary Token
 * @method getAmount() Updated Transaction Amount
 * @method setAmount(string $value) Updated Transaction Amount
 * @method getTip() New or Updated Tip Amount
 * @method setTip(string $value) New or Updated Tip Amount
 * @method getOverrideDuplicate() Override Duplicate Transaction
 * @method setOverrideDuplicate(bool $value) Override Duplicate Transaction
 * @method getTrace() Echoed in Response
 * @method setTrace(string $value) Echoed in Response
 */
class WC_Monetary_API_Credit_Adjust extends WC_Monetary_API_Credit_Abstract_Transaction
{
    /**
     * @var string
     */
    protected $method = self::PUT;

    /**
     * @var string
     */
    protected $uri = '/credit/sale/{RefNo}';
}