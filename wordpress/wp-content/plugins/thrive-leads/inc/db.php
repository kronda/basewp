<?php
/**
 * handles database operations
 */

global $tvedb;

/**
 * encapsulates the global $wpdb object
 *
 * Class Thrive_Leads_DB
 */
class Thrive_Leads_DB
{
    /**
     * @var wpdb the $wpdb instance
     */
    protected $wpdb = null;

    /**
     * class constructor
     */
    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * forward the call to the $wpdb object
     *
     * @param $methodName
     * @param $args
     * @return mixed
     */
    public function __call($methodName, $args)
    {
        return call_user_func_array(array($this->wpdb, $methodName), $args);
    }

    /**
     * unserialize fields from an array
     *
     * @param array $array where to search the fields
     * @param array $fields fields to be unserialized
     *
     * @return array the modified array containing the unserialized fields
     */
    protected function _unserialize_fields($array, $fields = array())
    {

        foreach ($fields as $field) {
            if (!isset($array[$field])) {
                continue;
            }
            /* the serialized fields should be trigger_config and tcb_fields */
            $array[$field] = empty($array[$field]) ? array() : unserialize($array[$field]);
            $array[$field] = wp_unslash($array[$field]);

            /* extra checks to ensure we'll have consistency */
            if (!is_array($array[$field])) {
                $array[$field] = array();
            }
        }

        return $array;
    }

    /**
     *
     * replace table names in form of {table_name} with the prefixed version
     *
     * @param $sql
     * @param $params
     * @return false|null|string
     */
    public function prepare($sql, $params)
    {
        $prefix = tve_leads_table_name('');
        $sql = preg_replace('/\{(.+?)\}/', '`' . $prefix . '$1' . '`', $sql);

        return $this->wpdb->prepare($sql, $params);
    }

    /**
     * Insert a new event in the log table
     *
     * @param array $data
     * @param int $active_test , if any
     * @return  id
     */
    public function insert_event($data, $active_test)
    {
        if (!isset($data['date'])) {
            $data['date'] = date('Y-m-d H:i:s');
        }

        $this->wpdb->insert(tve_leads_table_name('event_log'), $data);
        $log_id = $this->wpdb->insert_id;

        if ($active_test) {
            $this->update_test_item_data($data, $active_test, '+');
        }

        return $log_id;
    }

    /**
     * get an event log by id
     *
     * @param int $event_id
     * @return mixed
     */
    public function get_event($event_id)
    {
        return $this->wpdb->get_row($this->prepare("SELECT * FROM {event_log} WHERE id = %d", array($event_id)), ARRAY_A);
    }

    /**
     * increment / decrement a test item number of unique_impressions|conversions|impressions
     *
     * @param array $data tracking data -> the field that needs updating is calculated based on the event_type field from data
     * @param mixed $test_model
     * @param string $use_case can be either "+" or "-" for increment and decrement
     */
    public function update_test_item_data($data, $test_model, $use_case = '-')
    {
        $params = array();
        switch ($data['event_type']) {
            case TVE_LEADS_IMPRESSION:
                $field = '`impressions`';
                break;
            case TVE_LEADS_UNIQUE_IMPRESSION:
                $field = '`unique_impressions`';
                break;
            case TVE_LEADS_CONVERSION:
                $field = '`conversions`';
                break;
            default:
                return;
        }

        if (!in_array($use_case, array('+', '-'))) {
            return;
        }

        if ($use_case == '-') {
            $operation = "{$field} = IF( {$field} = 0, 0, {$field} - 1 )";
        } else {
            $operation = "{$field} = {$field} {$use_case} 1";
        }

        $sql = "UPDATE {split_test_items} SET {$operation} WHERE test_id = %d";

        $id = is_object($test_model) ? $test_model->id : (is_array($test_model) ? $test_model['id'] : $test_model);
        $params [] = intval($id);

        /* actually, this should always be filled in */
        if (!empty($data['variation_key'])) {
            $sql .= " AND variation_key = %d";
            $params [] = $data['variation_key'];
        }
        if (!empty($data['main_group_id'])) {
            $sql .= " AND main_group_id = %d";
            $params [] = $data['main_group_id'];
        }
        if (!empty($data['form_type_id'])) {
            $sql .= " AND form_type_id = %d";
            $params [] = $data['form_type_id'];
        }

        $this->wpdb->query($this->prepare($sql, $params));
    }

    /**
     * delete event log by id
     *
     * @param int $id
     */
    public function delete_event($id)
    {
        $id = intval($id);

        $this->wpdb->delete(tve_leads_table_name('event_log'), array('id' => $id));
    }

    /**
     * counts event logs based on a filter
     * @param array $filter
     * @return mixed
     */
    public function count_events($filter)
    {
        $sql = "SELECT COUNT( DISTINCT log.id ) FROM {event_log} AS `log` WHERE 1 ";

        $params = array();

        //when the filter is_unique is set to 1, we don't filter by event type
        if (!empty($filter['event_type']) && empty($filter['is_unique'])) {
            $sql .= "AND `event_type` = %d ";
            $params [] = $filter['event_type'];
        }

        if (!empty($filter['main_group_id'])) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['form_type_id'])) {
            $sql .= "AND `form_type_id` = %d ";
            $params [] = $filter['form_type_id'];
        }

        if (!empty($filter['variation_key'])) {
            $sql .= "AND `variation_key` = %d ";
            $params [] = $filter['variation_key'];
        }

        if (isset($filter['archived_log'])) {
            $sql .= "AND `archived` = %d ";
            $params [] = $filter['archived_log'];
        }

        if (!empty($filter['is_unique'])) {
            $sql .= "AND `is_unique` = %d ";
            $params [] = $filter['is_unique'];
        }

        if (!empty($filter['date'])) {
            switch ($filter['date']) {
                case 'today':
                    $midnight = date('Y-m-d 00:00:00');
                    $now = date('Y-m-d H:i:s');
                    $sql .= " AND `date` BETWEEN '{$midnight}' AND '{$now}'";
                    break;
            }
        }

        if (isset($filter['archived'])) {
            $sql .= " AND `archived` = %d";
            $params [] = $filter['archived'];
        }

        return $this->wpdb->get_var($this->prepare($sql, $params));
    }

    /**
     * Returns a count of event_types from a group in a time period
     *
     * @param $filter Array of filters for the result
     * @return Array with number of conversions per group_id in a period of time
     */
    public function tve_leads_get_report_data_count_event_type($filter)
    {
        $date_interval = '';
        switch ($filter['interval']) {
            case 'month':
                $date_interval = 'CONCAT(MONTHNAME(`log`.`date`)," ", YEAR(`log`.`date`)) as date_interval';
                break;
            case 'week':
                $year = "IF( WEEKOFYEAR(`log`.`date`) = 1 AND MONTH(`log`.`date`) = 12, 1 + YEAR(`log`.`date`), YEAR(`log`.`date`) )";
                $date_interval = "CONCAT('Week ', WEEKOFYEAR(`log`.`date`), ', ', {$year}) as date_interval";
                break;
            case 'day':
                $date_interval = 'DATE(`log`.`date`) as date_interval';
                break;
        }

        $sql = "SELECT IFNULL(COUNT( DISTINCT log.id ), 0) AS log_count, event_type, log." . $filter['data_group'] . " AS data_group, {$date_interval}
                FROM " . tve_leads_table_name('event_log') . " AS `log` WHERE 1 ";

        $params = array();

        if (!empty($filter['event_type'])) {
            $sql .= "AND `event_type` = %d ";
            $params [] = $filter['event_type'];
        }

        if (!empty($filter['main_group_id']) && $filter['main_group_id'] > 0) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['form_type_id'])) {
            $sql .= "AND `form_type_id` = %d ";
            $params [] = $filter['form_type_id'];
        }

        if (!empty($filter['variation_key'])) {
            $sql .= "AND `variation_key` = %d ";
            $params [] = $filter['variation_key'];
        }

        //we filter the log data and retrieve only from the specified data group, form_type or variation ids
        if (!empty($filter['group_ids']) && !empty($filter['data_group'])) {
            $sql .= "AND `" . $filter['data_group'] . "` IN (" . implode(', ', $filter['group_ids']) . ") ";
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $filter['end_date'] .= ' 23:59:59';

            $sql .= "AND `date` BETWEEN %s AND %s ";
            $params [] = $filter['start_date'];
            $params [] = $filter['end_date'];
        }

        if (!empty($filter['is_unique'])) {
            $sql .= " AND ( is_unique = 1 OR event_type = %d ) ";
            $params [] = TVE_LEADS_CONVERSION;
        } else if (is_array($filter['group_by']) && in_array('event_type', $filter['group_by'])) {
            $sql .= " AND ( event_type = %d OR event_type = %d ) ";
            $params [] = TVE_LEADS_UNIQUE_IMPRESSION;
            $params [] = TVE_LEADS_CONVERSION;
        }

        if (isset($filter['archived_log'])) {
            $sql .= "AND `archived` = %d ";
            $params [] = $filter['archived_log'];
        }

        if (!empty($filter['group_by']) && count($filter['group_by']) > 0) {
            $sql .= 'GROUP BY ' . implode(', ', $filter['group_by']);
        }

        $sql .= ' ORDER BY `log`.`date` DESC';

        return $this->wpdb->get_results($this->prepare($sql, $params));
    }

    /**
     * Returns date info from the log table
     *
     * @param $filter Array of filters for the result
     * @param $return_count Boolean If true, this function will return only the count of the query
     * @return Requested info from the log table
     */
    public function tve_leads_get_log_data_info($filter, $return_count = false)
    {

        $sql = "SELECT " .
            ($return_count ? "COUNT(*) AS count" : implode(', ', $filter['select_fields'])) .
            " FROM " . tve_leads_table_name('event_log') . " AS `log` WHERE 1 ";
        $params = array();

        if (!empty($filter['event_type'])) {
            $sql .= "AND `event_type` = %d ";
            $params [] = $filter['event_type'];
        }

        if (!empty($filter['main_group_id']) && $filter['main_group_id'] > 0) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['form_type_id'])) {
            $sql .= "AND `form_type_id` = %d ";
            $params [] = $filter['form_type_id'];
        }

        if (!empty($filter['variation_key'])) {
            $sql .= "AND `variation_key` = %d ";
            $params [] = $filter['variation_key'];
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $filter['end_date'] .= ' 23:59:59';
            $sql .= "AND DATE(`date`) BETWEEN  %s AND %s ";
            $params [] = $filter['start_date'];
            $params [] = $filter['end_date'];
        }

        if (isset($filter['archived_log'])) {
            $sql .= "AND `archived` = %d ";
            $params [] = $filter['archived_log'];
        }

        $sql .= ' ORDER BY `log`.`date` DESC';
        if (!$return_count && !empty($filter['itemsPerPage']) && !empty($filter['page'])) {
            $sql .= " LIMIT %d, %d ";
            $params [] = $filter['itemsPerPage'] * ($filter['page'] - 1);
            $params [] = $filter['itemsPerPage'];
        }

        if ($return_count == true) {
            return $this->wpdb->get_row($this->prepare($sql, $params))->count;
        } else {
            return $this->wpdb->get_results($this->prepare($sql, $params));
        }
    }

    public function tve_leads_get_top_referring_links($filter, $return_count = false)
    {
        $sql = "SELECT COUNT(DISTINCT id) as conversions, referrer as referring_url
                FROM " . tve_leads_table_name('event_log') . "
                WHERE referrer!='' ";

        /* $sql = "SELECT COUNT(DISTINCT id) as conversions, IFNULL(referrer, 'own site') as referring_url
                FROM " . tve_leads_table_name('event_log') . "
                WHERE 1 "; */

        if (!empty($filter['event_type'])) {
            $sql .= "AND `event_type` = %d ";
            $params [] = $filter['event_type'];
        }

        if (!empty($filter['main_group_id']) && $filter['main_group_id'] > 0) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $filter['end_date'] .= ' 23:59:59';
            $sql .= "AND DATE(`date`) BETWEEN  %s AND %s ";
            $params [] = $filter['start_date'];
            $params [] = $filter['end_date'];
        }

        if (isset($filter['archived_log'])) {
            $sql .= "AND `archived` = %d ";
            $params [] = $filter['archived_log'];
        }

        $sql .= "GROUP BY referrer";
        if (!$return_count && !empty($filter['itemsPerPage']) && !empty($filter['page'])) {
            $sql .= " ORDER BY conversions DESC";
            $sql .= " LIMIT %d, %d ";
            $params [] = $filter['itemsPerPage'] * ($filter['page'] - 1);
            $params [] = $filter['itemsPerPage'];
        }

        if ($return_count) {
            $sql = "SELECT COUNT(*) AS count FROM (" . $sql . " ) as rows ";
        }

        if ($return_count == true) {
            return $this->wpdb->get_row($this->prepare($sql, $params))->count;
        } else {
            return $this->wpdb->get_results($this->prepare($sql, $params));
        }
    }

    /**
     * saves or creates a test
     *
     * @param array|stdClass $model the test to be saved
     * @return bool|int
     */
    public function save_test($model)
    {
        if (!is_array($model)) {
            $model = (array)$model;
        }

        $_columns = array(
            'id', 'test_type', 'main_group_id', 'date_added', 'date_started', 'date_completed', 'title', 'notes',
            'auto_win_enabled', 'auto_win_min_conversions', 'auto_win_min_duration', 'auto_win_chance_original', 'status'
        );

        foreach ($model as $key => $data) {
            if (!in_array($key, $_columns)) {
                unset($model[$key]);
            }
        }

        if (!empty($model['id'])) {
            $update_rows = $this->wpdb->update(tve_leads_table_name('split_test'), $model, array('id' => $model['id']));
            return $update_rows !== false;
        }
        $this->wpdb->insert(tve_leads_table_name('split_test'), $model);
        $id = $this->wpdb->insert_id;
        return $id;
    }

    /**
     * Get test model based on filter
     * @param $filter
     * @return mixed
     */
    public function tve_leads_get_test($filter)
    {
        $sql = "SELECT * FROM " . tve_leads_table_name('split_test') . " WHERE 1 ";

        if (!empty($filter['ID'])) {
            $sql .= "AND `id` = %d ";
            $params [] = $filter['ID'];
        }

        if (!empty($filter['test_type'])) {
            $sql .= "AND `test_type` = %d ";
            $params [] = $filter['test_type'];
        }

        if (!empty($filter['main_group_id']) && $filter['main_group_id'] > 0) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['status'])) {
            $sql .= "AND `status` = %s ";
            $params [] = $filter['status'];
        }

        $sql .= " LIMIT 1";

        return $this->wpdb->get_row($this->prepare($sql, $params));
    }

    public function save_test_item($model)
    {
        if (!empty($model['id'])) {
            $toUpdate = array(
                'id' => $model['id'],
                'test_id' => isset($model['test_id']) ? $model['test_id'] : '',
                'main_group_id' => isset($model['main_group_id']) ? $model['main_group_id'] : '',
                'form_type_id' => isset($model['form_type_id']) ? $model['form_type_id'] : '',
                'variation_key' => isset($model['variation_key']) ? $model['variation_key'] : '',
                'is_control' => isset($model['is_control']) ? $model['is_control'] : '',
                'is_winner' => isset($model['is_winner']) ? $model['is_winner'] : 0,
                'impressions' => isset($model['impressions']) ? $model['impressions'] : 0,
                'conversions' => isset($model['conversions']) ? $model['conversions'] : 0,
            );
            $rows = $this->wpdb->update(tve_leads_table_name('split_test_items'), $toUpdate, array('id' => $toUpdate['id']));
            return $rows !== false;
        }
        $this->wpdb->insert(tve_leads_table_name('split_test_items'), $model);
        $id = $this->wpdb->insert_id;
        return $id;
    }

    public function get_test_items($filters)
    {
        $sql = "SELECT * FROM " . tve_leads_table_name('split_test_items') . " WHERE 1";

        $params = array();

        if (!empty($filters['form_type_id'])) {
            $sql .= " AND form_type_id = '%d' ";
            $params[] = $filters['form_type_id'];
        }

        if (!empty($filters['test_id'])) {
            $sql .= " AND `test_id` = %d ";
            $params [] = $filters['test_id'];
        }

        if (!empty($filters['main_group_id'])) {
            $sql .= " AND main_group_id = '%d' ";
            $params[] = $filters['main_group_id'];
        }

        //make sure that the control is the first one.
        $sql .= " ORDER BY `is_control` DESC, id ASC";

        //TODO: implement more filters if applied

        return $this->wpdb->get_results($this->prepare($sql, $params));
    }

    public function tve_leads_get_tests($filters)
    {
        $sql = "SELECT * FROM " . tve_leads_table_name('split_test') . " WHERE 1";

        $params = array();

        if (!empty($filters['test_type'])) {
            $sql .= " AND test_type = '%d'";
            $params[] = $filters['test_type'];
        }

        if (!empty($filters['main_group_id'])) {
            $sql .= " AND main_group_id = '%d'";
            $params[] = $filters['main_group_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = '%s'";
            $params[] = $filters['status'];
        }

        return $this->wpdb->get_results($this->prepare($sql, $params));

    }

    /**
     * Get top tracking links for the Lead Source Report
     * @param $filter
     * @return mixed
     */
    public function tve_leads_get_tracking_links($filter, $return_count = false)
    {
        $sql = "SELECT COUNT(DISTINCT id) AS conversions, utm_source AS source, utm_campaign AS name, utm_medium AS medium
             FROM " . tve_leads_table_name('event_log') . " WHERE (utm_source!='' OR utm_campaign!='' OR utm_medium!='') ";

        if (!empty($filter['event_type'])) {
            $sql .= "AND `event_type` = %d ";
            $params [] = $filter['event_type'];
        }

        if (!empty($filter['main_group_id']) && $filter['main_group_id'] > 0) {
            $sql .= "AND `main_group_id` = %d ";
            $params [] = $filter['main_group_id'];
        }

        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $filter['end_date'] .= ' 23:59:59';
            $sql .= "AND DATE(`date`) BETWEEN  %s AND %s ";
            $params [] = $filter['start_date'];
            $params [] = $filter['end_date'];
        }

        if (isset($filter['archived_log'])) {
            $sql .= "AND `archived` = %d ";
            $params [] = $filter['archived_log'];
        }

        $sql .= "GROUP BY utm_campaign, utm_medium, utm_source";
        if (!$return_count && !empty($filter['itemsPerPage']) && !empty($filter['page'])) {
            $sql .= " ORDER BY conversions DESC";
            $sql .= " LIMIT %d, %d ";
            $params [] = $filter['itemsPerPage'] * ($filter['page'] - 1);
            $params [] = $filter['itemsPerPage'];
        }

        if ($return_count) {
            $sql = "SELECT COUNT(*) AS count FROM (" . $sql . " ) as rows ";
        }

        if ($return_count == true) {
            return $this->wpdb->get_row($this->prepare($sql, $params))->count;
        } else {
            return $this->wpdb->get_results($this->prepare($sql, $params));
        }
    }

    /**
     * check if the identified variation is included in a running test
     *
     * @param int $form_type_or_shortcode_id
     * @param int $variation_key
     * @return array
     */
    public function check_if_test_exists($form_type_or_shortcode_id, $variation_key)
    {
        $sql = "SELECT COUNT(ti.id) FROM {split_test_items} AS ti
            INNER JOIN {split_test} AS t ON t.id = ti.test_id
            WHERE `form_type_id` = %d AND variation_key = %d AND t.status = %s";

        $params = array(
            $form_type_or_shortcode_id,
            $variation_key,
            TVE_LEADS_TEST_STATUS_RUNNING
        );

        return $this->wpdb->get_var($this->prepare($sql, $params));
    }

    /**
     * get a form variation by key (primary id)
     * this also handles un serialization of any data that looks serialized
     *
     * @param int $key
     * @return mixed
     */
    public function get_form_variation($key)
    {
        $sql = "SELECT * FROM {form_variations} WHERE `key` = %d";

        $variation = $this->wpdb->get_row($this->prepare($sql, array($key)), ARRAY_A);

        if (empty($variation)) {
            return null;
        }

        $variation = $this->_unserialize_fields($variation, array('trigger_config', 'tcb_fields'));

        /* assign each field from the tcb_fields in the main variation array, so they can be accessed directly */
        foreach ($variation['tcb_fields'] as $k => $v) {
            $variation[$k] = $v;
        }

        return $variation;
    }

    /**
     * @param array $filters should contain at least post_parent
     * @param bool $return_count if true, returns the count of the variations matching the filters
     *
     * @return array the list of form variations matching the filters
     */
    public function get_form_variations($filters = array(), $return_count = false)
    {
        $select = $return_count ? 'COUNT( `key` )' : '*';
        $sql = "SELECT {$select} FROM {form_variations} WHERE 1";
        $params = array();

        if (!empty($filters['post_parent'])) {
            $sql .= " AND `post_parent` = %d";
            $params [] = $filters['post_parent'];
        }

        if (!empty($filters['post_status'])) {
            if (!is_array($filters['post_status'])) {
                $filters['post_status'] = array($filters['post_status']);
            }
            $sql .= " AND ( ";
            foreach ($filters['post_status'] as $post_status) {
                $sql .= isset($first) ? " OR " : "";
                $sql .= "`post_status` = %s";
                $params [] = $post_status;
                $first = true;
            }
            $sql .= " )";
        }

        if (!empty($filters['parent_id'])) {
            $sql .= " AND `parent_id` = %d";
            $params []= $filters['parent_id'];
        } else {
            $sql .= " AND `parent_id` = 0";
        }

        if (!empty($filters['order'])) {
            list($col, $dir) = explode(' ', $filters['order']);
            if (strpos($col, '.')) {
                list($table, $col) = explode('.', $col);
                $table = $table ? "`" . str_replace('`', '', '{' . $table . '}') . "`" : '`{form_variations}`';
            } else {
                $table = '{form_variations}';
            }
            $col = "`" . str_replace('`', '', $col) . "`";
            $sql .= " ORDER BY {$table}.{$col} {$dir}";
        }

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (!empty($filters['offset']) ? intval($filters['offset']) . ',' : '');
            $sql .= intval($filters['limit']);
        }

        if ($return_count) {
            return $this->wpdb->get_var($this->prepare($sql, $params));
        }

        $results = $this->wpdb->get_results($this->prepare($sql, $params), ARRAY_A);
        if (empty($results)) {
            return array();
        }

        foreach ($results as & $item) {
            $item = $this->_unserialize_fields($item, array('trigger_config', 'tcb_fields'));
            /* assign each field from the tcb_fields in the main variation array, so they can be accessed directly */
            foreach ($item['tcb_fields'] as $k => $v) {
                $item[$k] = $v;
            }
        }

        return $results;
    }

    /**
     * serialize everything that's needed and save a form variation
     *
     * @param array $data the variation model data
     *
     * @return array the inserted variation
     */
    public function save_form_variation($data)
    {
        $columns = array(
            'key',
            'date_added',
            'date_modified',
            'post_parent',
            'post_status',
            'post_title',
            'content',
            'trigger',
            'trigger_config',
            'display_frequency',
            'position',
            'display_animation',
            'tcb_fields',
            'form_state',
            'parent_id',
            'state_order',
        );

        if (is_array($data['trigger_config'])) {
            $data['trigger_config'] = serialize($data['trigger_config']);
        }
        if (is_array($data['tcb_fields'])) {
            $data['tcb_fields'] = serialize($data['tcb_fields']);
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $columns)) {
                unset($data[$key]);
            }
        }
        $data['content'] = wp_unslash($data['content']);
        if (empty($data['key'])) {
            unset($data['key']);
            $data['date_added'] = $data['date_modified'] = date('Y-m-d H:i:s');
            $this->wpdb->insert(tve_leads_table_name('form_variations'), $data);
            $data['key'] = $this->wpdb->insert_id;
        } else {
            $data['date_modified'] = date('Y-m-d H:i:s');
            $this->wpdb->update(tve_leads_table_name('form_variations'), $data, array('key' => $data['key']));
        }

        return tve_leads_get_form_variation(null, $data['key']);
    }

    /**
     * mass update a field in a table
     *
     * @param string $table the table name
     * @param string $field the field name needed to be updated
     * @param mixed $field_value the new field value
     * @param array $keys what IDs to update
     * @param string $key_field the name of the ID field
     * @return int
     */
    public function mass_update_field($table, $field, $field_value, $keys = array(), $key_field = 'id')
    {
        $table = '{' . $table . '}';
        $field = '`' . $field . '`';
        $sql = "UPDATE {$table} SET {$field} = %s WHERE 1";
        $params [] = $field_value;

        $or = '';
        foreach ($keys as $key) {
            $or .= isset($first) ? " OR " : "";
            $or .= "`{$key_field}` = %s";
            $params [] = $key;
            $first = true;
        }
        $sql .= $or ? " AND ({$or})" : "";

        return $this->wpdb->query($this->prepare($sql, $params));
    }

    /**
     * archive event logs based on a filter
     *
     * @param array $filter
     *
     * @return int|false number of affected entries or false for error or invalid parameters
     */
    public function archive_logs($filter = array())
    {
        /* prevent accidental updates */
        if (empty($filter)) {
            return false;
        }

        $sql = "UPDATE {event_log} SET `archived` = 1 WHERE 1 AND ";
        $params = array();

        if (!empty($filter['variation_key'])) {
            $sql .= " `variation_key` = %d";
            $params [] = $filter['variation_key'];
        }

        if (!empty($filter['form_type_id'])) {
            $sql .= " `form_type_id` = %d";
            $params [] = $filter['form_type_id'];
        }

        if (!empty($filter['main_group_id'])) {
            $sql .= " `main_group_id` = %d";
            $params [] = $filter['main_group_id'];
        }

        if (empty($params)) {
            return false;
        }

        return $this->wpdb->query($this->prepare($sql, $params));
    }

    /**
     * Delete display settings based on $args
     * @param $args
     * @return false|int number of rows affected
     */
    public function delete_display_settings($args)
    {
        return $this->wpdb->delete(tve_leads_table_name('group_options'), $args);
    }

    /**
     * Check if a group has display settings
     * @param $group_id
     * @return mixed
     */
    public function has_display_settings($group_id)
    {
        return $this->wpdb->get_row($this->prepare("SELECT id FROM {group_options} WHERE `group` = %d", array($group_id)));
    }

    /**
     * Delete logs based on $args
     * @param $args
     * @return false|int number of rows affected
     */
    public function delete_logs($args)
    {
        return $this->wpdb->delete(tve_leads_table_name('event_log'), $args);
    }

    /**
     * Delete tests base on $args
     * @param array $args used in where clause
     * @param $filters array
     * @return false|int number of rows affected
     */
    public function delete_tests($args, $filters = array())
    {
        $defaults = array(
            'delete_items' => false
        );

        $filters = array_merge($defaults, $filters);

        if (!empty($filters['delete_items'])) {
            $this->delete_test_items($args);
        }

        return $this->wpdb->delete(tve_leads_table_name('split_test'), $args);
    }

    /**
     * Delete test items based on $args
     * @param $args
     * @return false|int number of rows affected
     */
    public function delete_test_items($args)
    {
        return $this->wpdb->delete(tve_leads_table_name('split_test_items'), $args);
    }

    /**
     * @param $filters
     *
     * @return int the count matching the filters
     */
    public function count_form_variations($filters)
    {
        unset($filters['order'], $filters['limit']);

        return $this->get_form_variations($filters, true);
    }

    /**
     *
     * increment each state order for variations having the same parent ID and state_order >= $new_order
     *
     * @param int $parent_id
     * @param int $new_order
     */
    public function variation_increment_state_order($parent_id, $new_order)
    {
        $sql = "UPDATE {form_variations} SET state_order = state_order + 1 WHERE parent_id = %d AND state_order >= %d";
        $params = array(
            $parent_id,
            $new_order
        );

        $this->wpdb->query($this->prepare($sql, $params));
    }

    /**
     * completely delete a form variation
     *
     * @param int $variation_key
     *
     * @return bool
     */
    public function delete_form_variation($variation_key)
    {
        $sql = "DELETE FROM {form_variations} WHERE `key` = %d";

        return $this->wpdb->query($this->prepare($sql, array($variation_key)));
    }

    /**
     * get the highest state_order from a set of variation states (children of $parent_id)
     *
     * @param int $parent_id
     * @return null|string
     */
    public function variation_get_max_state_order($parent_id)
    {
        $sql = "SELECT MAX( state_order ) FROM {form_variations} WHERE parent_id = %d";

        return $this->wpdb->get_var($this->prepare($sql, array($parent_id)));
    }

    /**
     * find the already_subscribed state for a variation
     *
     * @param int $parent_id
     *
     * @return array|null
     */
    public function get_variation_already_subscribed_state($parent_id)
    {
        $sql = "SELECT * FROM {form_variations} WHERE parent_id = %d AND `form_state` = %s";
        $state = $this->wpdb->get_row($this->prepare($sql, array($parent_id, 'already_subscribed')), ARRAY_A);

        if (empty($state)) {
            return null;
        }

        $state = $this->_unserialize_fields($state, array('trigger_config', 'tcb_fields'));

        /* assign each field from the tcb_fields in the main variation array, so they can be accessed directly */
        foreach ($state['tcb_fields'] as $k => $v) {
            $state[$k] = $v;
        }

        return $state;
    }

    /**
     * completely delete all child states for a variation
     *
     * @param int $variation_key
     * @param array $where extra where conditions
     *
     * @return bool
     */
    public function variation_delete_states($variation_key, $where = array())
    {
        $sql = "DELETE FROM {form_variations} WHERE `parent_id` = %d";
        $params = array($variation_key);

        foreach ($where as $field => $v) {
            $sql .= " AND `{$field}` = %s";
            $params []= $v;
        }

        return $this->wpdb->query($this->prepare($sql, $params));
    }

    /**
     * count event logs that are recorded as "non-unique impressions"
     */
    public function count_non_unique_impressions()
    {
        return $this->count_events(array(
            'event_type' => TVE_LEADS_IMPRESSION
        ));
    }

}

$tvedb = new Thrive_Leads_DB();