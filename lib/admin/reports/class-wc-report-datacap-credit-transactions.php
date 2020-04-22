<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_DATACAP_PLUGIN_DIR . "/lib/admin/reports/class-wc-report-datacap-abstract.php");
require_once(WC_DATACAP_PLUGIN_DIR . "/lib/api/reports/credit/class-wc-datacap-api-reports-credit-transactions.php");

/**
 * Class WC_Report_Datacap_Credit_Transactions
 */
class WC_Report_Datacap_Credit_Transactions extends WC_Report_Datacap_Abstract
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
    protected $requestClass = 'WC_Datacap_API_Reports_Credit_Transactions';

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
     * @param object $item
     * @param string $column
     * @return string
     */
    public function column_default($item, $column)
    {
        $val = parent::column_default($item, $column);

        if ($column === "Captured") {
            return ($val == "1" ? "Yes" : "No");
        }

        if ($column === "Amount" && is_numeric($val)) {
            return number_format($val, 2, '.', '');
        }

        return $val;
    }

    public function get_column_header_title($header)
    {
        if ($header === "DatetimeLocal") {
            $header = "Date";
        }

        return parent::get_column_header_title($header);
    }

    /**
     * @return WC_Datacap_API_Abstract
     */
    public function prepare_request()
    {
        $request = parent::prepare_request();
        $request->addUrlParameter('startDate', $this->get_filter_value('startDate'));
        $request->addUrlParameter('endDate', $this->get_filter_value('endDate'));

        return $request;
    }
}