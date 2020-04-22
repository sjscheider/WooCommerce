<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WC_Monetary_Admin_Handler
 */
class WC_Monetary_Admin_Handler
{
    /**
     * @var WC_Monetary_API
     */
    protected $api;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * WC_Monetary_Admin_Handler constructor.
     * @param WC_Monetary_API $api
     */
    public function __construct(WC_Monetary_API $api)
    {
        add_action('woocommerce_order_status_on-hold_to_processing', array($this, 'maybe_capture_charge'));
        add_action('woocommerce_order_status_on-hold_to_completed', array($this, 'maybe_capture_charge'));
        add_filter('woocommerce_order_actions', array($this, 'add_capture_charge_order_action'));
        add_action('woocommerce_order_action_monetary_capture_charge', array($this, 'maybe_capture_charge'));

        $this->api = $api;
        $this->config = WC_Monetary_Gateway_Entry::getGatewayConfig();
    }

    /**
     * @param $actions
     * @return array
     */
    public function add_capture_charge_order_action($actions)
    {
        if (!isset($_REQUEST['post'])) {
            return $actions;
        }

        $order = wc_get_order($_REQUEST['post']);
        $payment_method = $order->get_payment_method();

        // bail if the order wasn't paid for with this gateway
        if ($payment_method !== WC_MONETARY_PAYMENT_METHOD_ID || get_post_meta($order->get_id(), WC_Monetary_Gateway::META_IS_CAPTURED, true)) {
            return $actions;
        }

        if (!is_array($actions)) {
            $actions = array();
        }

        $actions['monetary_capture_charge'] = esc_html__('Capture Charge', WC_MONETARY_MODULE_NAME);

        return $actions;
    }

    /**
     * @param int|WC_Order $order
     * @return bool
     */
    public function maybe_capture_charge($order)
    {
        if (!is_object($order)) {
            $order = wc_get_order($order);
        }

        if (get_post_meta($order->get_id(), WC_Monetary_Gateway::META_IS_CAPTURED, true)) {
            return true;
        }

        $this->capture_payment($order->get_id());
        return true;
    }

    /**
     * Capture payment when the order is changed from on-hold to complete or processing
     *
     * @param int $order_id
     */
    public function capture_payment($order_id)
    {
        $order  = wc_get_order($order_id);

        $payment_method = $order->get_payment_method();

        if ($payment_method !== WC_MONETARY_PAYMENT_METHOD_ID) {
            return;
        }

        $is_captured = get_post_meta($order_id, WC_Monetary_Gateway::META_IS_CAPTURED, true);
        $card_token = get_post_meta($order_id, WC_Monetary_Gateway::META_MONETARY_CARD_TOKEN, true);
        $po_number = get_post_meta($order_id, WC_Monetary_Gateway::META_MONETARY_PO_NUMBER, true);

        if ($is_captured) {
            $order->add_order_note(__('This order has already been captured.', WC_MONETARY_MODULE_NAME));
            return;
        }

        require_once(WC_MONETARY_PLUGIN_DIR . '/lib/api/credit/class-wc-monetary-api-credit-sale.php');
        $sale = new WC_Monetary_API_Credit_Sale();

        if (!$card_token) {
            $order->add_order_note(__('This order has no card token attached. This order cannot be captured.', WC_MONETARY_MODULE_NAME));
            return;
        }

        $sale->setToken($card_token);
        $sale->setAmount($order->get_total());

        if ($this->config[WC_Monetary_Gateway::CONFIG_LEVEL_II_ENABLED] === 'yes') {
            $sale->setTax(number_format($order->get_cart_tax(), 2, '.', ''));
            $sale->setCustomerCode($po_number);
        }

        /** @var WC_Monetary_API_Response_Credit_Transaction $response */
        $response = $this->api->send($sale);

        if (!in_array($response->getStatus(), array($response::STATUS_APPROVED, $response::STATUS_SUCCESS))) {
            $order->add_order_note(__('Unable to capture charge!', WC_MONETARY_MODULE_NAME) . ' ' . $response->getMessage());
            return;
        }

        update_post_meta($order_id, WC_Monetary_Gateway::META_IS_CAPTURED, '1');
        update_post_meta($order_id, WC_Monetary_Gateway::META_TRANSACTION_ID, $response->getRefNo());

        $order->add_order_note(sprintf(__('Charge complete (RefNo: %s)', WC_MONETARY_MODULE_NAME), $response->getRefNo()));
    }
}