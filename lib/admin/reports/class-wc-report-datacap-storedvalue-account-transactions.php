<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-datacap-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-datacap-api-reports-storedvalue-account-transactions.php");

/**
 * Class WC_Report_Datacap_StoredValue_Account_Transactions
 */
class WC_Report_Datacap_StoredValue_Account_Transactions extends WC_Report_Datacap_Abstract
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
    protected $requestClass = 'WC_Datacap_API_Reports_StoredValue_Account_Transactions';

    /**
     * @return array
     */
    public function get_filters()
    {
        return array(
            'id' => array(
                'type' => self::TYPE_TEXT,
                'label' => 'Account ID',
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
        $request->addUrlParameter('Id', $this->get_filter_value('id'));

        return $request;
    }
}