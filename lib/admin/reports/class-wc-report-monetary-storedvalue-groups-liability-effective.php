<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-monetary-api-reports-storedvalue-groups-liability-effective.php");

/**
 * Class WC_Report_Monetary_StoredValue_Groups_Liability_Effective
 */
class WC_Report_Monetary_StoredValue_Groups_Liability_Effective extends WC_Report_Monetary_Abstract
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
    protected $requestClass = 'WC_Monetary_API_Reports_StoredValue_Groups_Liability_Effective';

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
            'effectiveDate' => array(
                'label' => 'Effective Date',
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
        $request->addUrlParameter('effectiveDate', $this->get_filter_value('effectiveDate'));

        return $request;
    }
}