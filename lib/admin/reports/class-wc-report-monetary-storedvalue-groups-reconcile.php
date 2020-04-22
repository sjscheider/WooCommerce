<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-monetary-api-reports-storedvalue-groups-reconcile.php");

/**
 * Class WC_Report_Monetary_StoredValue_Groups_Reconcile
 */
class WC_Report_Monetary_StoredValue_Groups_Reconcile extends WC_Report_Monetary_Abstract
{
    /**
     * @var string
     */
    protected $singular = 'transaction';

    /**
     * @var string
     */
    protected $plural = 'transactions';

    /**
     * @var string
     */
    protected $requestClass = 'WC_Monetary_API_Reports_StoredValue_Groups_Reconcile';

    /**
     * @return array
     */
    public function get_filters()
    {
        return array(
            'id' => array(
                'label' => 'Group Identifier',
                'type' => self::TYPE_TEXT,
                'default' => '',
                'required' => true
            ),
            'startDate' => array(
                'label' => 'Start Date',
                'type' => self::TYPE_DATE,
                'default' => date('Y-m-d', strtotime('-7 days')),
                'required' => true
            ),
            'endDate' => array(
                'label' => 'End Date',
                'type' => self::TYPE_DATE,
                'default' => date('Y-m-d'),
                'required' => true
            )
        );
    }

    /**
     * @return WC_Monetary_API_Abstract
     */
    public function prepare_request()
    {
        $request = parent::prepare_request();
        $request->addUrlParameter('id', $this->get_filter_value('id'));
        $request->addUrlParameter('startDate', $this->get_filter_value('startDate'));
        $request->addUrlParameter('endDate', $this->get_filter_value('endDate'));

        return $request;
    }
}