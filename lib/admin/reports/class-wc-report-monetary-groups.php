<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/groups/class-wc-monetary-api-reports-groups.php");

/**
 * Class WC_Report_Monetary_Groups
 */
class WC_Report_Monetary_Groups extends WC_Report_Monetary_Abstract
{
    /**
     * @var string
     */
    protected $singular = 'group';

    /**
     * @var string
     */
    protected $plural = 'groups';

    /**
     * @var string
     */
    protected $requestClass = 'WC_Monetary_API_Reports_Groups';
}