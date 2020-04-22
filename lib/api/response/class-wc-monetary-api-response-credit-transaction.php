<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Monetary_API_Response_Credit_Transaction
 * @method getStatus()
 * @method setStatus(string $value)
 * @method getMessage()
 * @method setMessage(string $value)
 * @method getAccount()
 * @method setAccount(string $value)
 * @method getExpiration()
 * @method setExpiration(string $value)
 * @method getBrand()
 * @method setBrand(string $value)
 * @method getAuthCode()
 * @method setAuthCode(string $value)
 * @method getAmount()
 * @method setAmount(string $value)
 * @method getTip()
 * @method setTip(string $value)
 * @method getAuthorized()
 * @method setAuthorized(string $value)
 * @method getAVSResult()
 * @method setAVSResult(string $value)
 * @method getCVVResult()
 * @method setCVVResult(string $value)
 * @method getInvoiceNo()
 * @method setInvoiceNo(string $value)
 * @method getRefNo()
 * @method setRefNo(string $value)
 * @method getToken()
 * @method setToken(string $value)
 * @method getTrace()
 * @method setTrace(string $value)
 */
class WC_Monetary_API_Response_Credit_Transaction extends WC_Monetary_API_Response
{
    const STATUS_APPROVED = 'Approved';
    const STATUS_DECLINED = 'Declined';
    const STATUS_SUCCESS = 'Success';
    const STATUS_ERROR = 'Error';

    const BRAND_VISA = 'VISA';
    const BRAND_MASTERCARD = 'M/C';
    const BRAND_DISCOVER = 'DCVR';
    const BRAND_AMERICAN_EXPRESS = 'AMEX';
    const BRAND_DCLB = 'DCLB';
    const BRAND_JCB = 'JCB';
    const BRAND_OTHER = 'OTHER';

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return in_array($this->getStatus(), array(self::STATUS_APPROVED, self::STATUS_SUCCESS));
    }
}