<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/class-wc-monetary-api-abstract.php");

/**
 * Class WC_Monetary_API_Admin_BatchClose
 * @method getBatchNo() Batch number returned in Batch Summary
 * @method setBatchNo(string $value) Batch number returned in Batch Summary
 * @method getBatchItemCount() Batch item count returned in Batch Summary
 * @method setBatchItemCount(string $value) Batch item count returned in Batch Summary
 * @method getNetBatchTotal() Net of all transactions in batch returned in Batch Summary
 * @method setNetBatchTotal(string $value) Net of all transactions in batch returned in Batch Summary
 */
class WC_Monetary_API_Admin_BatchClose extends WC_Monetary_API_Abstract
{
    /**
     * @var string
     */
    protected $method = self::POST;

    /**
     * @var string
     */
    protected $uri = '/admin/batchclose';
}