<?php

if (!defined('ABSPATH')) {
    exit;
}

return apply_filters('wc_monetary_settings',
    array(
        WC_Monetary_Gateway::CONFIG_ENABLED => array(
            'title'       => __( 'Status', WC_MONETARY_MODULE_NAME ),
            'label'       => __( 'Enable Monetary Payment Gateway', WC_MONETARY_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),

        WC_Monetary_Gateway::CONFIG_TITLE => array(
            'title'       => __( 'Title', WC_MONETARY_MODULE_NAME ),
            'type'        => 'text',
            'description' => __( 'This controls the title which the user sees during checkout.', WC_MONETARY_MODULE_NAME ),
            'default'     => __( 'Credit Card (Monetary)', WC_MONETARY_MODULE_NAME ),
            'desc_tip'    => true,
        ),

        WC_Monetary_Gateway::CONFIG_DESCRIPTION => array(
            'title'       => __( 'Description', WC_MONETARY_MODULE_NAME ),
            'type'        => 'text',
            'description' => __( 'This controls the description which the user sees during checkout.', WC_MONETARY_MODULE_NAME ),
            'default'     => __( 'Pay with your credit card via Monetary.', WC_MONETARY_MODULE_NAME ),
            'desc_tip'    => true,
        ),


        'authentication' => array(
            'title'       => __( 'Authentication', WC_MONETARY_MODULE_NAME ),
            'type'        => 'title'
        ),

        WC_Monetary_Gateway::CONFIG_PUBLIC_KEY => array(
            'title'       => __( 'Public Key', WC_MONETARY_MODULE_NAME ),
            'type'        => 'text',
            'description' => __('Your assigned Monetary public key', WC_MONETARY_MODULE_NAME),
            'desc_tip'    => true,
        ),

        WC_Monetary_Gateway::CONFIG_SECRET_KEY => array(
            'title'       => __( 'Secret Key', WC_MONETARY_MODULE_NAME ),
            'type'        => 'password',
            'description' => __( 'Your assigned Monetary secret key', WC_MONETARY_MODULE_NAME ),
            'desc_tip'    => true,
        ),

        WC_Monetary_Gateway::CONFIG_SANDBOX_MODE => array(
            'title'       => __( 'Sandbox mode', WC_MONETARY_MODULE_NAME ),
            'label'       => __( 'Enable Sandbox Mode', WC_MONETARY_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __( 'Test your gateway with test cards using sandbox mode.', WC_MONETARY_MODULE_NAME ),
            'default'     => 'yes',
            'desc_tip'    => true,
        ),



        'general' => array(
            'title'       => __( 'General', WC_MONETARY_MODULE_NAME ),
            'type'        => 'title'
        ),

        WC_Monetary_Gateway::CONFIG_TRANSACTION_TYPE => array(
            'title'       => __('Transaction Type', WC_MONETARY_MODULE_NAME),
            'type'        => 'select',
            'description' => __('Choose how you want your transactions to be processed when a customer places an order.'),
            'options' => array(
                WC_Monetary_Gateway::AUTH_THEN_CAPTURE => __('Pre-auth on checkout, capture later', WC_MONETARY_MODULE_NAME),
                WC_Monetary_Gateway::AUTH_AND_CAPTURE => __('Auth + Capture on checkout', WC_MONETARY_MODULE_NAME)
            )
        ),

        WC_Monetary_Gateway::CONFIG_ACCEPTED_CARD_TYPES => array(
            'title'       => __('Accepted Credit Card Types', WC_MONETARY_MODULE_NAME),
            'type'        => 'multiselect',
            'description' => __('Choose which credit cards your customers are allowed to use in your store.'),
            'options' => array(
                WC_Monetary_Gateway::VISA => 'Visa',
                WC_Monetary_Gateway::MASTERCARD => 'MasterCard',
                WC_Monetary_Gateway::AMERICAN_EXPRESS => 'American Express',
                WC_Monetary_Gateway::DISCOVER => 'Discover'
            ),
            'default' => array(WC_Monetary_Gateway::VISA, WC_Monetary_Gateway::MASTERCARD, WC_Monetary_Gateway::AMERICAN_EXPRESS, WC_Monetary_Gateway::DISCOVER)
        ),

        WC_Monetary_Gateway::CONFIG_CLIENT_REFERENCE_NUMBER => array(
            'title'       => __('Client Reference Number', WC_MONETARY_MODULE_NAME),
            'type'        => 'text',
            'description' => __('This field is sent to Monetary with every transaction. Fields can be dynamically inserted.'),
            'desc_tip'    => false
        ),

        WC_Monetary_Gateway::CONFIG_LEVEL_II_ENABLED => array(
            'title'       => __('Enable Level II Transactions'),
            'label'       => __( 'Enable Level II Transactions', WC_MONETARY_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __(
                'Enable Level II transactions to potentially lower interchange rates and processing costs.<br />' .
                'This will add a "Purchase Order Number" field to checkout.'
            ),
        ),

        WC_Monetary_Gateway::CONFIG_CARD_SECURITY_MODEL => array(
            'title'       => __('Card Security Model', WC_MONETARY_MODULE_NAME),
            'type'        => 'select',
            'options'     => array(
                'raw_pan' => __('Raw PAN data', WC_MONETARY_MODULE_NAME),
                'tokenization' => __('Tokenization', WC_MONETARY_MODULE_NAME)
            ),
            'description' => __('Raw PAN data sends credit card information through your server to Monetary to complete transactions. This can lead to more PCI compliance requirements.<br />' .
                'Tokenization transforms sensitive credit card information into a one-time-use token that your server can send to Monetary in place of raw PAN data.', WC_MONETARY_MODULE_NAME),
            'desc_tip'    => false
        ),

        WC_Monetary_Gateway::CONFIG_ALLOW_SAVED_CARDS => array(
            'title'       => __('Allow Saved Cards'),
            'label'       => __( 'Allow users to save credit cards', WC_MONETARY_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __(
                '<strong>"Card Security Model" is required to be set to "Tokenization" for this field to be checked.</strong><br />' .
                'Allow users to save their credit cards.'
            ),
        )
    )
);
