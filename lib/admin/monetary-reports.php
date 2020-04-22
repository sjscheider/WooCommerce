<?php

if (!defined('ABSPATH')) {
    exit;
}

return apply_filters( 'wc_monetary_reports',
    array(
        'monetary' => array(
            'title' => 'Monetary Gateway',
            'reports' => array(
                'monetary_credit_transactions' => array(
                    'title' => 'Credit Transactions',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_groups' => array(
                    'title' => 'Groups',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_account_transactions' => array(
                    'title' => 'Account Transactions',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_credit_current' => array(
                    'title' => 'Business Credit Current',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_credit_effective' => array(
                    'title' => 'Business Credit Effective',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_liability_current' => array(
                    'title' => 'Business Liability Current',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_liability_effective' => array(
                    'title' => 'Business Liability Effective',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_reconcile' => array(
                    'title' => 'Business Reconcile',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_business_transactions' => array(
                    'title' => 'Business Transactions',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_credit_current' => array(
                    'title' => 'Groups Credit Current',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_credit_effective' => array(
                    'title' => 'Groups Credit Effective',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_liability_current' => array(
                    'title' => 'Groups Liability Current',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_liability_effective' => array(
                    'title' => 'Groups Liability Effective',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_reconcile' => array(
                    'title' => 'Groups Reconcile',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                ),
                'monetary_storedvalue_groups_transactions' => array(
                    'title' => 'Groups Transactions',
                    'description' => '',
                    'hide_title' => false,
                    'callback' => array(
                        'WC_Admin_Reports',
                        'get_report'
                    )
                )
            )
        )
    )
);
