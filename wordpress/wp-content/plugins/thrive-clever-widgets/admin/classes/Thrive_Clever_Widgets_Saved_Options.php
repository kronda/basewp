<?php

class Thrive_Clever_Widgets_Saved_Options
{
    private $table_name = 'saved_widgets_options';
    private $db;
    private $name;
    private $description;
    public $show_widget_options;
    public $hide_widget_options;

    public function __construct($name = '', $show_widget_options = '', $hide_widget_options = '', $description = '')
    {
        /**
         * @var $wpdb wpdb
         */
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = Thrive_Clever_Widgets_Database_Manager::tableName($this->table_name);
        $this->name = $name;
        $this->description = $description;
        $this->show_widget_options = $show_widget_options;
        $this->hide_widget_options = $hide_widget_options;
    }

    protected function _processPreSave($jsonOptions)
    {
        $options = @json_decode(stripcslashes($jsonOptions), true);

        if (empty($options) || empty($options['tabs'])) {
            return json_encode(array('identifier' => $jsonOptions['identifier']));
        }

        foreach ($options['tabs'] as $index => $tab) {
            $saved_options = array();
            foreach ($tab['options'] as $i => $item) {
                if (!empty($item['isChecked']) || $item['type'] == 'direct_url') {
                    $saved_options []= $item['id'];
                }
            }
            unset($options['tabs'][$index]['actions']);
            unset($options['tabs'][$index]['filters']);
            unset($options['tabs'][$index]['label']);
            unset($options['tabs'][$index]['isActive']);
            $options['tabs'][$index]['options'] = $saved_options;
        }

        return json_encode($options);
    }

    public function save()
    {
        $this->delete();
        $this->db->suppress_errors();

        $show_options = $this->_processPreSave($this->show_widget_options);
        $hide_options = $this->_processPreSave($this->hide_widget_options);

        return $this->db->insert($this->table_name, array(
            'name' => $this->name,
            'description' => $this->description,
            'show_widget_options' => $show_options,
            'hide_widget_options' => $hide_options
        )) !== false ? true : $this->db->last_error;
    }

    public function delete()
    {
        $this->db->delete($this->table_name, array('name' => $this->name));
    }

    /**
     * Read options from database
     * @return $this
     */
    public function initOptions()
    {
        $sql = "SELECT * FROM {$this->table_name} WHERE name = '{$this->name}'";
        $row = $this->db->get_row($sql);
        if ($row) {
            $this->show_widget_options = $row->show_widget_options;
            $this->hide_widget_options = $row->hide_widget_options;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getShowWidgetOptions()
    {
        return $this->show_widget_options;
    }

    /**
     * @return string
     */
    public function getHideWidgetOptions()
    {
        return $this->hide_widget_options;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table_name} ORDER BY name";
        $results = $this->db->get_results($sql);
        return $results;
    }
}
