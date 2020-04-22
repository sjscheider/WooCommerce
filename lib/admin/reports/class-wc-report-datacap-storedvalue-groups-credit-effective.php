<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-datacap-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-datacap-api-reports-storedvalue-groups-credit-effective.php");

/**
 * Class WC_Report_Datacap_StoredValue_Groups_Credit_Effective
 */
class WC_Report_Datacap_StoredValue_Groups_Credit_Effective extends WC_Report_Datacap_Abstract
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
    protected $requestClass = 'WC_Datacap_API_Reports_StoredValue_Groups_Credit_Effective';

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
            )
        );
    }

    /**
     * @return WC_Datacap_API_Abstract
     */
    public function prepare_request()
    {
        $request = parent::prepare_request();
        $request->addUrlParameter('id', $this->get_filter_value('id'));

        return $request;
    }
}