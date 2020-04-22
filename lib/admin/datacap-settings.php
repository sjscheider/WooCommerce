<?php

if (!defined('ABSPATH')) {
    exit;
}

return apply_filters('wc_datacap_settings',
    array(
        WC_Datacap_Gateway::CONFIG_ENABLED => array(
            'title'       => __( 'Status', WC_DATACAP_MODULE_NAME ),
            'label'       => __( 'Enable Datacap Payment Gateway', WC_DATACAP_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no',
        ),

        WC_Datacap_Gateway::CONFIG_TITLE => array(
            'title'       => __( 'Title', WC_DATACAP_MODULE_NAME ),
            'type'        => 'text',
            'description' => __( 'This controls the title which the user sees during checkout.', WC_DATACAP_MODULE_NAME ),
            'default'     => __( 'Credit Card (Datacap)', WC_DATACAP_MODULE_NAME ),
            'desc_tip'    => true,
        ),

        WC_Datacap_Gateway::CONFIG_DESCRIPTION => array(
            'title'       => __( 'Description', WC_DATACAP_MODULE_NAME ),
            'type'        => 'text',
            'description' => __( 'This controls the description which the user sees during checkout.', WC_DATACAP_MODULE_NAME ),
            'default'     => __( 'Pay with your credit card via Datacap.', WC_DATACAP_MODULE_NAME ),
            'desc_tip'    => true,
        ),


        'authentication' => array(
            'title'       => __( 'Authentication', WC_DATACAP_MODULE_NAME ),
            'type'        => 'title'
        ),

        WC_Datacap_Gateway::CONFIG_PUBLIC_KEY => array(
            'title'       => __( 'Token Key', WC_DATACAP_MODULE_NAME ),
            'type'        => 'text',
            'description' => __('Your assigned Datacap token key', WC_DATACAP_MODULE_NAME),
            'desc_tip'    => true,
        ),

        WC_Datacap_Gateway::CONFIG_SECRET_KEY => array(
            'title'       => __( 'Datacap ecommerce MID', WC_DATACAP_MODULE_NAME ),
            'type'        => 'password',
            'description' => __( 'Your assigned Datacap ecommerce MID', WC_DATACAP_MODULE_NAME ),
            'desc_tip'    => true,
        ),

        WC_Datacap_Gateway::CONFIG_SANDBOX_MODE => array(
            'title'       => __( 'Sandbox mode', WC_DATACAP_MODULE_NAME ),
            'label'       => __( 'Enable Sandbox Mode', WC_DATACAP_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __( 'Test your gateway with test cards using sandbox mode.', WC_DATACAP_MODULE_NAME ),
            'default'     => 'yes',
            'desc_tip'    => true,
        ),



        'general' => array(
            'title'       => __( 'General', WC_DATACAP_MODULE_NAME ),
            'type'        => 'title'
        ),

        WC_Datacap_Gateway::CONFIG_TRANSACTION_TYPE => array(
            'title'       => __('Transaction Type', WC_DATACAP_MODULE_NAME),
            'type'        => 'select',
            'description' => __('Choose how you want your transactions to be processed when a customer places an order.'),
            'options' => array(
                WC_Datacap_Gateway::AUTH_THEN_CAPTURE => __('Pre-auth on checkout, capture later', WC_DATACAP_MODULE_NAME),
                WC_Datacap_Gateway::AUTH_AND_CAPTURE => __('Auth + Capture on checkout', WC_DATACAP_MODULE_NAME)
            )
        ),

        WC_Datacap_Gateway::CONFIG_ACCEPTED_CARD_TYPES => array(
            'title'       => __('Accepted Credit Card Types', WC_DATACAP_MODULE_NAME),
            'type'        => 'multiselect',
            'description' => __('Choose which credit cards your customers are allowed to use in your store.'),
            'options' => array(
                WC_Datacap_Gateway::VISA => 'Visa',
                WC_Datacap_Gateway::MASTERCARD => 'MasterCard',
                WC_Datacap_Gateway::AMERICAN_EXPRESS => 'American Express',
                WC_Datacap_Gateway::DISCOVER => 'Discover'
            ),
            'default' => array(WC_Datacap_Gateway::VISA, WC_Datacap_Gateway::MASTERCARD, WC_Datacap_Gateway::AMERICAN_EXPRESS, WC_Datacap_Gateway::DISCOVER)
        ),

        WC_Datacap_Gateway::CONFIG_CLIENT_REFERENCE_NUMBER => array(
            'title'       => __('Client Reference Number', WC_DATACAP_MODULE_NAME),
            'type'        => 'text',
            'description' => __('This field is sent to Datacap with every transaction. Fields can be dynamically inserted.'),
            'desc_tip'    => false
        ),

        WC_Datacap_Gateway::CONFIG_LEVEL_II_ENABLED => array(
            'title'       => __('Enable Level II Transactions'),
            'label'       => __( 'Enable Level II Transactions', WC_DATACAP_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __(
                'Enable Level II transactions to potentially lower interchange rates and processing costs.<br />' .
                'This will add a "Purchase Order Number" field to checkout.'
            ),
        ),

        WC_Datacap_Gateway::CONFIG_CARD_SECURITY_MODEL => array(
            'title'       => __('Card Security Model', WC_DATACAP_MODULE_NAME),
            'type'        => 'select',
            'options'     => array(
                'tokenization' => __('Tokenization', WC_DATACAP_MODULE_NAME)
            ),
            'description' => __('Tokenization transforms sensitive credit card information into a one-time-use token that your server can send to Datacap in place of raw PAN data.', WC_DATACAP_MODULE_NAME),
            'desc_tip'    => false
        ),

        WC_Datacap_Gateway::CONFIG_ALLOW_SAVED_CARDS => array(
            'title'       => __('Allow Saved Cards'),
            'label'       => __( 'Allow users to save credit cards', WC_DATACAP_MODULE_NAME ),
            'type'        => 'checkbox',
            'description' => __(
                '<strong>"Card Security Model" is required to be set to "Tokenization" for this field to be checked.</strong><br />' .
                'Allow users to save their credit cards.'
            ),
        )
    )
);
