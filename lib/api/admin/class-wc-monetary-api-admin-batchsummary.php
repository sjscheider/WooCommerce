<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-abstract.php");

/**
 * Class WC_Monetary_API_Admin_BatchSummary
 */
class WC_Monetary_API_Admin_BatchSummary extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::GET;

    /**
     * @var string
     */
    protected $uri = '/admin/batchsummary';
}