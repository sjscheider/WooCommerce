<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-monetary-api-reports-storedvalue-business-transactions.php");

/**
 * Class WC_Report_Monetary_StoredValue_Business_Transactions
 */
class WC_Report_Monetary_StoredValue_Business_Transactions extends WC_Report_Monetary_Abstract
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
    protected $requestClass = 'WC_Monetary_API_Reports_StoredValue_Business_Transactions';

    /**
     * @return array
     */
    public function get_filters()
    {
        return array(
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
        $request->addUrlParameter('startDate', $this->get_filter_value('startDate'));
        $request->addUrlParameter('endDate', $this->get_filter_value('endDate'));

        return $request;
    }
}