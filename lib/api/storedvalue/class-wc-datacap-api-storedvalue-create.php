<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-abstract.php");

/**
 * Class WC_Datacap_API_StoredValue_Create
 * @method getAmount() Transaction Amount
 * @method setAmount(int $value) Transaction Amount
 * @method getNewIdentifier() New Account Alternate Identifier
 * @method setNewIdentifier(string $value) New Account Alternate Identifier
 * @method getInvoiceNo() Unique Transaction Identifier
 * @method setInvoiceNo(string $value) Unique Transaction Identifier
 * @method getPromo() Promotional Create
 * @method setPromo(bool $value) Promotional Create
 * @method getLocked() Account Locked
 * @method setLocked(bool $value) Account Locked
 * @method getCreditLimit() Enable Credit and Set Limit
 * @method setCreditLimit(int $value) Enable Credit and Set Limit
 */
class WC_Datacap_API_StoredValue_Create extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/storedvalue/create';
}