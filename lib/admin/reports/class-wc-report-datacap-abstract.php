<?php

if (!defined( 'ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Class WC_Report_Datacap_Abstract
 */
abstract class WC_Report_Datacap_Abstract extends WP_List_Table
{
    const TYPE_DATE = 'date';
    const TYPE_TEXT = 'text';

    /**
     * @var string
     */
    protected $singular = 'record';

    /**
     * @var string
     */
    protected $plural = 'records';

    /**
     * @var string
     */
    protected $requestClass = '';

    /**
     * @var bool
     */
    protected $datePickerLoaded = false;

    /**
     * @var bool
     */
    protected $isReadyToDisplay = null;

    /**
     * WC_Report_Datacap_Abstract constructor.
     * @param array $args
     */
    public function __construct($args = array())
    {
        parent::__construct(array_merge(array(
            'singular'  => $this->singular,
            'plural'    => $this->plural,
            'ajax'      => false,
        ), $args));
    }

    /**
     * @return array
     */
    public function get_filters()
    {
        return array();
    }

    /**
     * @param $key
     * @return string
     */
    public function get_filter_value($key)
    {
        $filters = $this->get_filters();
        return isset($_POST[$key]) ? $_POST[$key] : $filters[$key]['default'];
    }

    /**
     * Load datepicker library
     */
    protected function load_datepicker_library()
    {
        if ($this->datePickerLoaded) {
            return;
        }

        wp_enqueue_style('jquery-ui');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        echo <<<JS
<script type="text/javascript">
jQuery(document).ready(function($) {
    $(".datacap-filter-datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });
});
</script>
JS;


        $this->datePickerLoaded = true;
    }

    /**
     * Show filters on page
     */
    protected function show_filters()
    {
        $filters = $this->get_filters();
        if (empty($filters)) {
            return;
        }

        echo '<form method="POST" class="datacap-report-filters">';

        foreach ($filters as $id => $filter) {
            echo (isset($filter['required']) && $filter['required'] ? '<strong>' . $filter['label'] . '</strong>' : $filter['label']);

            if ($filter['type'] === self::TYPE_DATE) {
                $this->load_datepicker_library();
                echo ' <input type="text" class="datacap-filter-datepicker" name="' . $id . '" value="' . $this->get_filter_value($id) . '"> ';
            }

            if ($filter['type'] === self::TYPE_TEXT) {
                echo ' <input type="text" name="' . $id . '" value="' . $this->get_filter_value($id) . '"> ';
            }
        }

        echo ' <input type="submit" name="submit_filters" class="button" value="Filter" />';

        echo '</form>';
    }

    /**
     * No items found text.
     */
    public function no_items()
    {
        _e('No ' . $this->plural . ' found.', WC_DATACAP_MODULE_NAME);
    }

    public function ready_to_display()
    {
        if ($this->isReadyToDisplay !== null) {
            return $this->isReadyToDisplay;
        }

        $filters = $this->get_filters();

        foreach ($filters as $id => $filter) {
            if (isset($filter['required']) && $filter['required']) {
                if ($this->get_filter_value($id) === '') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Output the report.
     */
    public function output_report()
    {
        $this->show_filters();

        $this->prepare_items();
        echo '<div id="poststuff" class="woocommerce-reports-wide">';
        $this->display();
        echo '</div>';
    }

    public function display_rows_or_placeholder()
    {
        if (!$this->ready_to_display()) {
            echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
            echo __('Please fill in the required filters above to show report data.', WC_DATACAP_MODULE_NAME);
            echo '</td></tr>';
            return;
        }

        parent::display_rows_or_placeholder();
    }

    public function get_columns()
    {
        return array();
    }

    /**
     * @return WC_Datacap_API_Reports_Abstract
     */
    protected function prepare_request()
    {
        $request = new $this->requestClass();
        return $request;
    }

    /**
     * @param object $item
     * @param string $column
     * @return string
     */
    protected function column_default($item, $column)
    {
        return isset($item[$column]) ? $item[$column] : '';
    }

    /**
     * @param $header
     * @return string
     */
    protected function get_column_header_title($header)
    {
        return __(ltrim(preg_replace('/[A-Z]/', ' $0', $header)), WC_DATACAP_MODULE_NAME);
    }

    public function prepare_items()
    {
        $request = $this->prepare_request();
        $response = WC_Datacap_Gateway_Entry::getApiInstance()->send($request);
        $body = $response->getBody();

        if (empty($body)) {
            return;
        }

        // Get first object, and get keys for columns
        $firstRow = $body[0];
        $headers = array_keys($firstRow);
        $columns = array();

        foreach ($headers as $header) {
            $columns[$header] = $this->get_column_header_title($header);
        }

        $this->_column_headers = array($columns, array(), array());
        $this->items = $body;
    }
}