<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/credit/class-wc-datacap-api-credit-abstract-transaction.php");

/**
 * Class WC_Datacap_API_Credit_Capture
 * @method getToken() Datacap Token
 * @method setToken(string $value) Datacap Token
 * @method getAmount() Updated Transaction Amount
 * @method setAmount(string $value) Updated Transaction Amount
 * @method getTip() New or Updated Tip Amount
 * @method setTip(string $value) New or Updated Tip Amount
 * @method getOverrideDuplicate() Override Duplicate Transaction
 * @method setOverrideDuplicate(bool $value) Override Duplicate Transaction
 * @method getTrace() Echoed in Response
 * @method setTrace(string $value) Echoed in Response
 */
class WC_Datacap_API_Credit_Capture extends WC_Datacap_API_Credit_Abstract_Transaction
{
    /**
     * @var string
     */
    protected $method = self::PUT;

    /**
     * @var string
     */
    protected $uri = '/credit/preauth/{RefNo}';
}