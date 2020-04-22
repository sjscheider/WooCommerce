<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/class-wc-datacap-api-abstract.php");

/**
 * Class WC_Datacap_API_StoredValue_Set
 * @method getAccount() Account Number
 * @method setAccount(int $value) Account Number
 * @method getCVV() Account CVV
 * @method setCVV(string $value) Account CVV
 * @method getTrack2() Track2 Data (stripe)
 * @method setTrack2(string $value) Track2 Data (stripe)
 * @method getIdentifier() Account Alternate Identifier
 * @method setIdentifier(string $value) Account Alternate Identifier
 * @method getOverrideCVV() Override Account CVV
 * @method setOverrideCVV(bool $value) Override Account CVV
 * @method getNewIdentifier() New Account Alternate Identifier
 * @method setNewIdentifier(string $value) New Account Alternate Identifier
 * @method getLocked() Account Locked
 * @method setLocked(bool $value) Account Locked
 * @method getCreditLimit() Enable Credit and Set Limit
 * @method setCreditLimit(int $value) Enable Credit and Set Limit
 */
class WC_Datacap_API_StoredValue_Set extends WC_Datacap_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/storedvalue/set';
}