<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/class-wc-monetary-api.php');
require_once(WC_MONETARY_PLUGIN_DIR . '/lib/admin/class-wc-monetary-admin-handler.php');

/**
 * Class WC_Monetary_Gateway
 */
class WC_Monetary_Gateway extends WC_Payment_Gateway_CC
{
    const VISA = 'visa';
    const MASTERCARD = 'mastercard';
    const AMERICAN_EXPRESS = 'american express';
    const DISCOVER = 'discover';
    const AUTH_THEN_CAPTURE = 'auth_then_capture';
    const AUTH_AND_CAPTURE = 'auth_and_capture';

    const META_TRANSACTION_ID = '_transaction_id';
    const META_IS_CAPTURED = '_is_captured';
    const META_MONETARY_CARD_TOKEN = '_monetary_card_token';
    const META_MONETARY_AUTH_CODE = '_monetary_auth_code';
    const META_MONETARY_PO_NUMBER = '_monetary_po_number';

    const SECURITY_RAW_PAN = 'raw_pan';
    const SECURITY_TOKENIZATION = 'tokenization';

    const PROD_WEBTOKEN_URL = 'https://token.monetary.co/v1/client';
    const CERT_WEBTOKEN_URL = 'https://token-cert.monetary.co/v1/client';

    const CONFIG_ROOT = 'woocommerce_monetary_settings';
    const CONFIG_ENABLED = 'enabled';
    const CONFIG_TITLE = 'title';
    const CONFIG_DESCRIPTION = 'description';
    const CONFIG_PUBLIC_KEY = 'public_key';
    const CONFIG_SECRET_KEY = 'secret_key';
    const CONFIG_SANDBOX_MODE = 'sandbox_mode';
    const CONFIG_TRANSACTION_TYPE = 'transaction_type';
    const CONFIG_ACCEPTED_CARD_TYPES = 'accepted_card_types';
    const CONFIG_CLIENT_REFERENCE_NUMBER = 'client_reference_number';
    const CONFIG_LEVEL_II_ENABLED = 'level_ii_enabled';
    const CONFIG_ALLOW_SAVED_CARDS = 'allow_saved_cards';
    const CONFIG_CARD_SECURITY_MODEL = 'card_security_model';

    /**
     * @var string
     */
    protected $cardLast4 = '';

    /**
     * @var string
     */
    protected $cardBrand = '';

    /**
     * @var string
     */
    protected $cardExpMonth = '';

    /**
     * @var string
     */
    protected $cardExpYear = '';

    /**
     * @var string
     */
    protected $cardToken = '';

    /**
     * @var WC_Monetary_Admin_Handler
     */
    protected $adminHandler;

    /**
     * WC_Gateway_Monetary constructor.
     */
    public function __construct()
    {
        $this->id = WC_MONETARY_PAYMENT_METHOD_ID;
        $this->icon = "";
        $this->has_fields = true;
        $this->supports = array('products', 'refunds');

        $this->init_form_fields();
        $this->init_settings();

        if ($this->is_tokenization_enabled()) {
            $this->supports = array_merge($this->supports, array('tokenization'));
        }

        if ($this->can_save_cards()) {
            $this->supports = array_merge($this->supports, array('add_payment_method'));
        }

        $this->title = $this->get_option(self::CONFIG_TITLE);
        $this->description = $this->get_option(self::CONFIG_DESCRIPTION);

        $this->method_title = 'Credit Card (Monetary)';
        $this->method_description = $this->description;

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_credit_card_form_end', array($this, 'append_to_credit_card_form'));
        add_action('woocommerce_credit_card_form_fields', array($this, 'add_credit_card_fields'), 0, 2);
        add_action('woocommerce_available_payment_gateways', array($this, 'check_saved_cards_enabled_add_payment'));
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public function validate_allow_saved_cards_field($key, $value)
    {
        if ($value === 'yes' || $value === '1') {
            if ($this->get_option(self::CONFIG_CARD_SECURITY_MODEL) !== self::SECURITY_TOKENIZATION) {
                throw new Exception(__("Card Security Model must be set to Tokenization for Allow Saved Cards to be enabled."));
            }
        }

        return ($value === 'yes' || $value === '1' ? 'yes' : 'no');
    }

    /**
     * @param string $paymentMethod
     * @return void
     */
    public function append_to_credit_card_form($paymentMethod)
    {
        if ($this->id !== $paymentMethod || !$this->is_tokenization_enabled()) {
            return;
        }

        echo sprintf("<input type='hidden' id='%s-public-key' value='%s' />", $this->id, htmlentities($this->get_option(self::CONFIG_PUBLIC_KEY))) .
            "<input type='hidden' value='' data-token='card_number' />" .
            "<input type='hidden' value='' data-token='cvv' />" .
            "<input type='hidden' value='' data-token='exp_month' />" .
            "<input type='hidden' value='' data-token='exp_year' />" .
            sprintf("<input type='hidden' value='' id='%s-token' name='%s_token' />", $this->id, $this->id) .
            sprintf("<input type='hidden' value='' id='%s-brand' name='%s_brand' />", $this->id, $this->id) .
            sprintf("<input type='hidden' value='' id='%s-exp-month' name='%s_exp_month' />", $this->id, $this->id) .
            sprintf("<input type='hidden' value='' id='%s-exp-year' name='%s_exp_year' />", $this->id, $this->id) .
            sprintf("<input type='hidden' value='' id='%s-last-4' name='%s_last_4' />", $this->id, $this->id);
    }

    /**
     * @param array $fields
     * @param string $paymentMethod
     * @return array
     */
    public function add_credit_card_fields($fields, $paymentMethod)
    {
        if ($paymentMethod !== $this->id) {
            return $fields;
        }

        // Alter previous fields to add a validation class
        foreach ($fields as $id => $field) {

            if (!in_array($field, array($this->id . '-'))) {
                continue;
            }

            $fields[$id] = preg_replace('/p class=\\"(.*?)\\"/', 'p class="$1 validate-required"', $field);
        }

        if (is_checkout() || is_checkout_pay_page()) {
            $fields = array_merge($fields, array(
                'monetary-po-number' => '<p class="form-row form-row-wide">
                    <label for="' . esc_attr($this->id) . '-po-number">' . esc_html__('Purchase Order Number', WC_MONETARY_MODULE_NAME) . '</label>
                    <input id="' . esc_attr($this->id) . '-po-number" name="' . $this->id . '_po_number" class="input-text" inputmode="numeric" autocorrect="no" autocapitalize="no" spellcheck="no" placeholder="' . __('Purchase Order Number', WC_MONETARY_MODULE_NAME) . '" />
                </p>',
            ));
        }

        if (is_add_payment_method_page()) {
            $fields = array_merge($fields, array(
                'monetary-billing-zip' => '<p class="form-row form-row-first">
                    <label for="' . esc_attr($this->id) . '-billing-zip">' . esc_html__('Billing Zip', 'woocommerce') . '</label>
                    <input id="' . esc_attr($this->id) . '-billing-zip" name="' . $this->id . '_billing_zip" class="input-text" inputmode="numeric" autocorrect="no" autocapitalize="no" spellcheck="no" placeholder="' . __('Billing Zip', 'woocommerce') . '" />
                </p>',
            ));
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function get_icon()
    {
        $icons = implode('', array_values($this->get_payment_icons()));
        return apply_filters('woocommerce_gateway_icon', $icons, $this->id);
    }

    /**
     * @return array
     */
    protected function get_payment_icons()
    {
        /** @var array $allowed */
        $allowed = $this->get_option(self::CONFIG_ACCEPTED_CARD_TYPES);

        $urls = array(
            self::VISA => '<img src="' . WC()->plugin_url() . '/assets/images/icons/credit-cards/visa.svg" class="' . $this->id . '-visa-icon stripe-icon" alt="Visa" />',
            self::AMERICAN_EXPRESS => '<img src="' . WC()->plugin_url() . '/assets/images/icons/credit-cards/amex.svg" class="' . $this->id . '-amex-icon ' . $this->id . '-icon" alt="American Express" />',
            self::MASTERCARD => '<img src="' . WC()->plugin_url() . '/assets/images/icons/credit-cards/mastercard.svg" class="' . $this->id . '-mastercard-icon ' . $this->id . '-icon" alt="Mastercard" />',
            self::DISCOVER => '<img src="' . WC()->plugin_url() . '/assets/images/icons/credit-cards/discover.svg" class="' . $this->id . '-discover-icon ' . $this->id . '-icon" alt="Discover" />',
        );

        return array_intersect_key($urls, array_flip($allowed));
    }

    /**
     * @param $gateways
     * @return mixed
     */
    public function check_saved_cards_enabled_add_payment($gateways)
    {
        if (!is_add_payment_method_page()) {
            return $gateways;
        }

        if (isset($gateways[$this->id])) {
            if (!$this->can_save_cards()) {
                unset($gateways[$this->id]);
            }
        }

        return $gateways;
    }

    /**
     * @return bool
     */
    public function is_tokenization_enabled()
    {
        return $this->get_option(self::CONFIG_CARD_SECURITY_MODEL) === self::SECURITY_TOKENIZATION;
    }

    /**
     * @return bool
     */
    public function is_level_ii_enabled()
    {
        return $this->get_option(self::CONFIG_LEVEL_II_ENABLED) === 'yes';
    }

    /**
     * @return bool
     */
    public function should_auth_and_capture()
    {
        return $this->get_option(self::CONFIG_TRANSACTION_TYPE) === self::AUTH_AND_CAPTURE;
    }

    /**
     * @param $monetaryBrand
     * @return bool
     */
    protected function monetary_card_brand_to_wc($monetaryBrand)
    {
        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/response/class-wc-monetary-api-response-credit-transaction.php');

        $brands = array(
            WC_Monetary_API_Response_Credit_Transaction::BRAND_VISA => self::VISA,
            WC_Monetary_API_Response_Credit_Transaction::BRAND_MASTERCARD => self::MASTERCARD,
            WC_Monetary_API_Response_Credit_Transaction::BRAND_DISCOVER => self::DISCOVER,
            WC_Monetary_API_Response_Credit_Transaction::BRAND_AMERICAN_EXPRESS => self::AMERICAN_EXPRESS,
//            WC_Monetary_API_Response_Credit_Transaction::BRAND_DCLB => 'diners',
//            WC_Monetary_API_Response_Credit_Transaction::BRAND_JCB => 'jcb',
            WC_Monetary_API_Response_Credit_Transaction::BRAND_OTHER => 'other'
        );

        return (isset($brands[$monetaryBrand]) ? $brands[$monetaryBrand] : $brands[WC_Monetary_API_Response_Credit_Transaction::BRAND_OTHER]);
    }

    /**
     * @return bool
     */
    public function can_save_cards()
    {
        return $this->get_option(self::CONFIG_ALLOW_SAVED_CARDS) === 'yes' && $this->is_tokenization_enabled();
    }

    /**
     * @param $cardNumber
     * @return bool
     */
    public function can_use_card($cardNumber)
    {
        /** @var array $allowed */
        $detection = array(
            self::VISA => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            self::MASTERCARD => "/^5[1-5][0-9]{14}$/",
            self::AMERICAN_EXPRESS => "/^3[47][0-9]{13}$/",
            self::DISCOVER => "/^6(?:011|5[0-9]{2})[0-9]{12}$/"
        );

        foreach ($detection as $type => $regex) {
            if (preg_match($regex, $cardNumber)) {
                return $this->can_use_brand($type);
            }
        }

        return false;
    }

    public function can_use_brand($brand)
    {
        /** @var array $allowed */
        $allowed = $this->get_option(self::CONFIG_ACCEPTED_CARD_TYPES);
        return in_array($brand, $allowed);
    }

    /**
     * @param $brand
     * @return bool
     */
    public function can_use_monetary_brand($brand)
    {
        return $this->can_use_brand($this->monetary_card_brand_to_wc($brand));
    }

    /**
     * Init form fields.
     */
    public function init_form_fields()
    {
        parent::init_form_fields();

        $this->form_fields = require(WC_MONETARY_PLUGIN_DIR . '/lib/admin/monetary-settings.php');
    }

    /**
     * @return bool
     */
    public function is_available()
    {
        return $this->get_option(self::CONFIG_ENABLED) === 'yes' &&
            $this->get_option(self::CONFIG_PUBLIC_KEY) &&
            $this->get_option(self::CONFIG_SECRET_KEY);
    }

    /**
     * @return mixed
     */
    public function add_payment_method()
    {
        $token = $this->get_post_key($this->id . '_token');
        $brand = $this->get_post_key($this->id . '_brand');
        $expMonth = $this->get_post_key($this->id . '_exp_month');
        $expYear = $this->get_post_key($this->id . '_exp_year');
        $last4 = $this->get_post_key($this->id . '_last_4');
        $billingZip = $this->get_post_key($this->id . '_billing_zip');

        if (!$token || !$brand || !$expMonth || !$expYear || !$last4 || !$billingZip) {
            wc_add_notice(__('There was a problem adding this card.', 'woocommerce'), 'error');
            return null;
        }

        if (!$this->can_use_monetary_brand($brand)) {
            wc_add_notice(__('You cannot use this card type at this store (' . $brand . ')', WC_MONETARY_MODULE_NAME), 'error');
            return null;
        }

        try {
            $cardToken = $this->verify_new_card($token, $billingZip);
        } catch (Exception $e) {
            wc_add_notice(sprintf(__('An error occurred while verifying your card details. If this problem persists, please contact support. (%s)', WC_MONETARY_MODULE_NAME), $e->getMessage()), 'error');
            return null;
        }

        $token = $this->add_card($cardToken, $this->monetary_card_brand_to_wc($brand), $last4, $expMonth, $expYear);

        if ($token === false) {
            wc_add_notice(__('There was a problem adding this card.', 'woocommerce'), 'error');
            return null;
        }

        return array(
            'result' => 'success',
            'redirect' => wc_get_endpoint_url('payment-methods')
        );
    }

    /**
     * @param $otu
     * @param $billingZip
     * @return string
     * @throws Exception
     */
    protected function verify_new_card($otu, $billingZip)
    {
        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/credit/class-wc-monetary-api-credit-authonly.php');
        $request = new WC_Monetary_API_Credit_Authonly();
        $request->setToken($otu);
        $request->setAmount("0.00");
        $request->setZip($billingZip);

        /** @var WC_Monetary_API_Response_Credit_Transaction $response */
        $response = WC_Monetary_Gateway_Entry::getApiInstance()->send($request);

        if ($response->isSuccessful()) {
            return $response->getToken();
        }

        throw new Exception($response->getMessage());
    }

    /**
     * @param $code
     * @param $brand
     * @param $last4
     * @param $month
     * @param $year
     * @return bool|WC_Payment_Token_CC
     */
    protected function add_card($code, $brand, $last4, $month, $year)
    {
        $currentUserId = get_current_user_id();

        $token = new WC_Payment_Token_CC();
        $token->set_token($code);
        $token->set_gateway_id($this->id);
        $token->set_card_type(strtolower($brand));
        $token->set_last4($last4);
        $token->set_expiry_month($month);
        $token->set_expiry_year($year);

        if ($currentUserId > 0) {
            $token->set_user_id($currentUserId);
        }

        if (!$token->save()) {
            return false;
        }

        return $token;
    }

    /**
     * Payment fields
     */
    public function payment_fields()
    {
        parent::payment_fields();

        wp_enqueue_style(
            'wc-monetary-styles',
            plugins_url('assets/css/monetary.css', WC_MONETARY_ENTRY_FILE),
            null
        );

        if ($this->is_tokenization_enabled()) {
            wp_enqueue_script(
                'wc-monetary-webtoken',
                $this->get_webtoken_url(),
                null
            );
        }

        wp_enqueue_script(
            'wc-monetary-tokenization',
            plugins_url('assets/js/frontend/checkout.js', WC_MONETARY_ENTRY_FILE),
            null
        );
    }

    public function saved_payment_methods()
    {
        if ($this->can_save_cards()) {
            return parent::saved_payment_methods();
        }
    }

    public function save_payment_method_checkbox()
    {
        if ($this->can_save_cards()) {
            return parent::save_payment_method_checkbox();
        }
    }

    /**
     * @return string
     */
    public function get_webtoken_url()
    {
        return ($this->get_option(self::CONFIG_SANDBOX_MODE) === 'yes') ? self::CERT_WEBTOKEN_URL : self::PROD_WEBTOKEN_URL;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function get_post_key($key, $default = null)
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        return $_POST[$key];
    }

    /**
     * @param int $order_id
     * @return mixed
     */
    public function process_payment($order_id)
    {
        $saveNewCard = $this->get_post_key('wc-' . $this->id . '-new-payment-method');
        $poNumber = $this->get_post_key($this->id . '_po_number') ?: '';

        $order = new WC_Order($order_id);

        if ($this->should_auth_and_capture()) {
            $response = $this->execute_sale_request($order);
        } else {
            $response = $this->execute_auth_request($order);
        }

        if ($response === null) {
            return null;
        }

        if (!$response->isSuccessful()) {
            wc_add_notice(__('Payment error:', WC_MONETARY_MODULE_NAME) . sprintf(' Your transaction was unsuccessful. Please verify your payment details. (%s)', $response->getMessage()), 'error');
            return null;
        }

        /**
         * Make order adjustments based on the request type
         */
        $order->update_meta_data(self::META_MONETARY_CARD_TOKEN, $response->getToken());
        $order->update_meta_data(self::META_MONETARY_AUTH_CODE, $response->getAuthCode());

        if ($this->is_level_ii_enabled() && strlen($poNumber) > 0) {
            $order->update_meta_data(self::META_MONETARY_PO_NUMBER, $poNumber);
        }

        if ($this->should_auth_and_capture()) {
            $order->payment_complete($response->getRefNo());
        } else {
            if ($order->has_status(array('pending', 'failed'))) {
                wc_reduce_stock_levels($order->get_id());
            }

            try {
                $order->set_transaction_id($response->getRefNo());
            } catch (WC_Data_Exception $e) { }

            $order->update_status('on-hold', sprintf(__('Charge authorized. (Refno: %s). Process order to take payment, or cancel to remove the pre-authorization.', WC_MONETARY_MODULE_NAME), $response->getRefNo()));
            $order->save();
        }

        /**
         * Save the new card if we need to
         */
        if ($this->can_save_cards() && $saveNewCard && $response->getToken()) {
            $this->cardBrand = $this->monetary_card_brand_to_wc($response->getBrand());
            $this->cardLast4 = substr($response->getAccount(), -4);
            $this->add_card($response->getToken(), $this->cardBrand, $this->cardLast4, $this->cardExpMonth, $this->cardExpYear);
        }

        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        );
    }

    /**
     * @param WC_Order $order
     * @return null|WC_Monetary_API_Response_Credit_Transaction
     */
    protected function execute_sale_request($order)
    {
        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/credit/class-wc-monetary-api-credit-sale.php');
        $request = new WC_Monetary_API_Credit_Sale();

        if ($this->setup_api_request_parameters($request, $order) === null) {
            return null;
        }

        /** @var WC_Monetary_API_Response_Credit_Transaction $response */
        $response = WC_Monetary_Gateway_Entry::getApiInstance()->send($request);
        return $response;
    }

    /**
     * @param WC_Order $order
     * @return null|WC_Monetary_API_Response_Credit_Transaction
     */
    protected function execute_auth_request($order)
    {
        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/credit/class-wc-monetary-api-credit-authonly.php');
        $request = new WC_Monetary_API_Credit_Authonly();

        if ($this->setup_api_request_parameters($request, $order) === null) {
            return null;
        }

        /** @var WC_Monetary_API_Response_Credit_Transaction $response */
        $response = WC_Monetary_Gateway_Entry::getApiInstance()->send($request);
        return $response;
    }

    /**
     * @param WC_Monetary_API_Abstract $request
     * @param WC_Order $order
     * @return mixed
     */
    protected function setup_api_request_parameters($request, $order)
    {
        $savedCardId = $this->get_post_key('wc-' . $this->id . '-payment-token');

        $cardNumber = str_replace(' ', '', $this->get_post_key($this->id . '-card-number'));
        $cardExp = $this->get_post_key($this->id . '-card-expiry');
        $cardExpMonth = ($cardExp && stristr($cardExp, '/') ? trim(stristr($cardExp, '/', true)) : null);
        $cardExpYear = ($cardExp && stristr($cardExp, '/') ? trim(substr(stristr($cardExp, '/'), 1)) : null);
        $cardCvc = $this->get_post_key($this->id . '-card-cvc');
        $areCardFieldsFilled = isset($cardNumber, $cardExpMonth, $cardExpYear, $cardCvc);

        $tokenCode = $this->get_post_key($this->id . '_token');
        $tokenBrand = $this->get_post_key($this->id . '_brand');
        $tokenExpMonth = $this->get_post_key($this->id . '_exp_month');
        $tokenExpYear = $this->get_post_key($this->id . '_exp_year');
        $tokenLast4 = $this->get_post_key($this->id . '_last_4');
        $hasToken = isset($tokenCode, $tokenBrand, $tokenExpMonth, $tokenExpYear, $tokenLast4);

        if (!$areCardFieldsFilled && !$hasToken && ($savedCardId === 'new' || !is_numeric($savedCardId))) {
            wc_add_notice(__('Payment error:', WC_MONETARY_MODULE_NAME) . ' Please verify you have filled in all of the required fields.', 'error');
            return null;
        }

        $request->setAmount($order->get_total());
        $request->setZip($order->get_billing_postcode());

        if ($this->is_level_ii_enabled() && $this->should_auth_and_capture()) {
            $this->setup_level_ii_request_parameters($request, $order);
        }

        if ($savedCardId !== 'new' && is_numeric($savedCardId)) {
            $token = WC_Payment_Tokens::get($savedCardId);

            if ($token->get_user_id() !== get_current_user_id()) {
                wc_add_notice(__('Please make sure your card details have been entered correctly and that your browser supports JavaScript.', 'woocommerce' ), 'error');
                return null;
            }

            $canUseCard = $this->can_use_brand($token->get_meta('card_type'));
            $request->setToken($token->get_token());
        } elseif ($hasToken && $this->is_tokenization_enabled()) {
            $request->setToken($tokenCode);

            $canUseCard = $this->can_use_brand($this->monetary_card_brand_to_wc($tokenBrand));
            $this->cardExpMonth = $tokenExpMonth;
            $this->cardExpYear = $tokenExpYear;
        } else {
            $canUseCard = $this->can_use_card($cardNumber);

            $request->setAccount($cardNumber);
            $request->setExpiration($cardExpMonth . $cardExpYear);
            $request->setCVV($cardCvc);

            $this->cardExpMonth = $cardExpMonth;
            $this->cardExpYear = $cardExpYear;
        }

        if (isset($canUseCard) && !$canUseCard) {
            wc_add_notice('This credit card type cannot be used at this store.', 'error');
            return null;
        }

        return $request;
    }

    /**
     * @param WC_Monetary_API_Abstract $request
     * @param WC_Order $order
     */
    protected function setup_level_ii_request_parameters($request, $order)
    {
        $poNumber = $this->get_post_key($this->id . '_po_number') ?: '';

        $request->setTax(number_format($order->get_cart_tax(), 2, '.', ''));
        $request->setCustomerCode($poNumber);
    }

    /**
     * @param WC_Order $order
     * @return string
     */
    protected function get_invoice_number($order)
    {
        $fields = array(
            '{order_id}' => $order->get_id(),
            '{total}' => $order->get_total(),
            '{shipping_firstname}' => $order->get_shipping_first_name(),
            '{shipping_lastname}' => $order->get_shipping_last_name()
        );

        return str_replace(array_keys($fields), array_values($fields), $this->get_option(self::CONFIG_CLIENT_REFERENCE_NUMBER));
    }

    /**
     * @param int $order_id
     * @param null $amount
     * @param string $reason
     * @return mixed
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        $order = new WC_Order($order_id);

        $transactionId = get_post_meta($order_id, self::META_TRANSACTION_ID, true);
        $cardToken = get_post_meta($order_id, self::META_MONETARY_CARD_TOKEN, true);

        if (!$cardToken) {
            return new WP_Error('monetary_refund_error', 'A refund is not possible because this order does not have a token assigned to it.');
        }

        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/credit/class-wc-monetary-api-credit-return.php');
        $request = new WC_Monetary_API_Credit_Return();
        $request->setToken($cardToken);
        $request->setAmount($amount);

        /** @var WC_Monetary_API_Response_Credit_Transaction $response */
        $response = WC_Monetary_Gateway_Entry::getApiInstance()->send($request, array(
            'RefNo' => $transactionId
        ));

        if (!$response->isSuccessful()) {
            return new WP_Error('monetary_refund_error', sprintf('Your refund request was unsuccessful. Message: %s', $response->getMessage()));
        }

        $order->add_order_note(sprintf(__('This order has been refunded %s. RefNo: %s', WC_MONETARY_MODULE_NAME), $amount, $response->getRefNo()));

        return true;
    }
}