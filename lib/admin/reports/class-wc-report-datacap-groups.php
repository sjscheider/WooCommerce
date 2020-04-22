<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-datacap-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/groups/class-wc-datacap-api-reports-groups.php");

/**
 * Class WC_Report_Datacap_Groups
 */
class WC_Report_Datacap_Groups extends WC_Report_Datacap_Abstract
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
    protected $requestClass = 'WC_Datacap_API_Reports_Groups';
}