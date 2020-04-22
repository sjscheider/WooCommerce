<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-abstract.php");

/**
 * Class WC_Monetary_API_Debit_Sale
 * @method getTrack2() Card Track2 Data (stripe)
 * @method setTrack2(string $value) Card Track2 Data (stripe)
 * @method getDerivedKey() Debit Derived Key
 * @method setDerivedKey(string $value) Debit Derived Key
 * @method getPINBlock() Debit PIN Block
 * @method setPINBlock(string $value) Debit PIN Block
 * @method getAmount() Transaction Amount
 * @method setAmount(string $value) Transaction Amount
 * @method getInvoiceNo() Unique Transaction Identifier
 * @method setInvoiceNo(string $value) Unique Transaction Identifier
 * @method getOverrideDuplicate() Override Duplicate Transaction
 * @method setOverrideDuplicate(string $value) Override Duplicate Transaction
 * @method getTrace() Echoed in Response
 * @method setTrace(string $value) Echoed in Response
 */
class WC_Monetary_API_Debit_Sale extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/debit/sale';
}