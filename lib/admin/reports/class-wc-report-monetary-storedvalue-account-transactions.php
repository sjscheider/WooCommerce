<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_MONETARY_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-monetary-abstract.php");
require_once(WC_MONETARY_PLUGIN_DIR . "/lib/api/reports/storedvalue/class-wc-monetary-api-reports-storedvalue-account-transactions.php");

/**
 * Class WC_Report_Monetary_StoredValue_Account_Transactions
 */
class WC_Report_Monetary_StoredValue_Account_Transactions extends WC_Report_Monetary_Abstract
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
    protected $requestClass = 'WC_Monetary_API_Reports_StoredValue_Account_Transactions';

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
     * @return WC_Monetary_API_Abstract
     */
    public function prepare_request()
    {
        $request = parent::prepare_request();
        $request->addUrlParameter('Id', $this->get_filter_value('id'));

        return $request;
    }
}