<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-datacap-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-datacap-api-reports-storedvalue-business-credit-effective.php");

/**
 * Class WC_Report_Datacap_StoredValue_Business_Credit_Effective
 */
class WC_Report_Datacap_StoredValue_Business_Credit_Effective extends WC_Report_Datacap_Abstract
{
    /**
     * @var string
     */
    protected $requestClass = 'WC_Datacap_API_Reports_StoredValue_Business_Credit_Effective';

    /**
     * @return array
     */
    public function get_filters()
    {
        return array(
            'effectiveDate' => array(
                'type' => self::TYPE_DATE,
                'label' => 'Effective Date',
                'default' => date('Y-m-d'),
                'required' => true
            )
        );
    }

    /**
     * @return WC_Datacap_API_Abstract
     */
    public function prepare_request()
    {
        $request = parent::prepare_request();
        $request->addUrlParameter('effectiveDate', $this->get_filter_value('effectiveDate'));

        return $request;
    }
}