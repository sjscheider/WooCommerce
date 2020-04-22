<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-abstract.php");

/**
 * Class WC_Datacap_API_StoredValue_Load
 * @method getAccount() Account Number
 * @method setAccount(int $value) Account Number
 * @method getCVV() Account CVV
 * @method setCVV(string $value) Account CVV
 * @method getTrack2() Track2 Data (stripe)
 * @method setTrack2(string $value) Track2 Data (stripe)
 * @method getIdentifier() Account Alternate Identifier
 * @method setIdentifier(string $value) Account Alternate Identifier
 * @method getAmount() Transaction Amount
 * @method setAmount(int $value) Transaction Amount
 * @method getOverrideCVV() Override Account CVV
 * @method setOverrideCVV(bool $value) Override Account CVV
 * @method getInvoiceNo() Unique Transaction Identifier
 * @method setInvoiceNo(string $value) Unique Transaction Identifier
 * @method getOverrideDuplicate() Override Duplicate Transaction
 * @method setOverrideDuplicate(bool $value) Override Duplicate Transaction
 * @method getPromo() Promotional Load
 * @method setPromo(bool $value) Promotional Load
 */
class WC_Datacap_API_StoredValue_Load extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/storedvalue/load';
}