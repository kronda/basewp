<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 7/8/2015
 * Time: 4:45 PM
 */

/**
 * Class Thrive_OptIn
 * Singleton for OptIn functions
 */
class Thrive_OptIn
{
    protected static $instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Thrive_OptIn();
        }

        return self::$instance;
    }

    /**
     * Renders the html for desired input
     *
     * @param $name string name of the input. Send as individual parameter for backwards compatibility
     * @param $params array of input's properties
     * @param $css_class mixin
     *
     * @return string the html for input(text,checkbox)
     */
    public function getInputHtml($name, $params, $css_class = null)
    {
        if (!is_array($params)) {
            $params = array(
                'label' => $params,
                'name' => $name,
                'type' => 'text',
            );
        }

        $field = array(
            'name' => '',
            'label' => '',
            'type' => 'text',
        );

        $field = array_merge($field, $params);

        if(!is_array($css_class)) {
            $css_class = array($css_class);
        }
        $css_class = implode(" ", $css_class);

        $html = '';

        switch ($field['type']) {
            case 'text':
            case 'email':
                $field_type = 'text'; //hardcoded because we don't have styles for inputs with email type
                $html .= '<input class="' . $css_class . '" type="' . $field_type . '" placeholder="' . $field['label'] . '" name="' . _thrive_get_optin_name_attr_fixed($field['name']) . '" />';
                break;
            case 'checkbox':
                /** @var $rand int used for label in "for" attribute and also for input for "id" attribute;
                 * for the case when the same input is rendered more that 1 time;
                 * e.g. in focus area and in widget
                 */
                $rand = rand(1, 9999);
                $html .= '<div class="op-r">';
                $html .= '<input class="' . $css_class . '" id="' . $field['name'] . $rand . '" type="' . $field['type'] . '" name="' . _thrive_get_optin_name_attr_fixed($field['name']) . '" />';
                $html .= '<label for="' . $field['name'] . $rand . '">' . $field['label'] . '</label>';
                $html .= "</div>";
                break;
        }

        return $html; 
    }

}