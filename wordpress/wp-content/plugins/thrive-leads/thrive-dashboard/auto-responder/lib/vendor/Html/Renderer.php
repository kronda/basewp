<?php

/**
 * maybe we can use this to generate the actual form fields ?
 *
 * Class Thrive_Api_Html_Renderer
 */
class Thrive_Api_Html_Renderer
{
    protected $show_display_options = false;
    protected $show_order = false;

    /**
     * render all available API fields
     */
    public function getApiFields()
    {
        $fields = array(
            'name' => array(
                'display' => 1,
                'type' => 'text',
                'name' => 'name',
                'label' => __('Name', 'thrive-visual-editor'),
                'required' => true,
            ),
            'email' => array(
                'display' => 1,
                'type' => 'text',
                'name' => 'email',
                'label' => __('Email', 'thrive-visual-editor'),
                'validation' => 'email',
                'required' => true,
            ),
            'phone' => array(
                'display' => 1,
                'type' => 'text',
                'name' => 'phone',
                'label' => __('Phone Number', 'thrive-visual-editor'),
                'validation' => 'phone',
            )
        );

        /**
         * allow adding / removing fields from the form
         */
        $fields = apply_filters('tcb_lead_generation_api_fields', $fields);

        return $fields;
    }

    /**
     * generate the setup table for the fields included in API-connected forms
     *
     * @param array $params provide a way to filter the output
     * @param array $order elements order - numeric array containing field names as values
     *
     * @return string the generated table containing all input data
     */
    public function apiFieldsTable($params = array(), $order = array())
    {
        $this->show_display_options = empty($params['show_display_options']) ? false : true;
        $this->show_order = empty($params['show_order']) ? false : true;

        $fields = $this->getOrderedFields($order);

        return $this->fieldsTable($fields);
    }

    /**
     * get the ordered list of fields
     * @param array $order elements order - numeric array containing field names as values
     * @return array
     */
    public function getOrderedFields($order)
    {
        $fields = $this->getApiFields();

        if (!empty($order)) {
            $ordered = array();
            foreach ($order as $field) {
                if (!isset($fields[$field])) {
                    continue;
                }
                $ordered[$field] = $fields[$field];
                unset($fields[$field]);
            }
            /**
             * take on any other fields
             */
            foreach ($fields as $k => $f) {
                $ordered[$k] = $f;
            }
            $fields = $ordered;
        }

        return $fields;
    }

    /**
     * render the table containing all fields (inside the editor view / panel)
     *
     * @param array $elements
     *
     * @return string the table containing the setup fields
     */
    public function fieldsTable($elements = array())
    {
        $html = '<table class="tcb-editor-table">';
        $html .= $this->_tableHead() . '<tbody>';
        $input_index = 1;
        foreach ($elements as $element) {
            switch ($element['type']) {
                case 'text':
                    $html .= $this->_textRow($element, $input_index);
                    break;
                case 'radio':
                    $html .= $this->_radioRow($element, $input_index);
                    break;
                case 'checkbox':
                    $html .= $this->_checkboxRow($element, $input_index);
                    break;
                case 'select':
                    $html .= $this->_selectRow($element, $input_index);
                    break;
                case 'textarea':
                    $html .= $this->_textareaRow($element, $input_index);
                    break;
            }
            $input_index++;
        }

        $html .= '</tbody></table>';

        return $html;
    }

    /**
     * render the table head
     * @return string
     */
    protected function _tableHead()
    {
        ob_start();
        ?><thead><tr>
        <?php if ($this->show_order) : ?><td width="5%">&nbsp;</td><?php endif ?>
            <td style="width: 10%; text-align: center"><?php $this->show_display_options ? _e('Display', 'thrive-visual-editor') : _e('Field Number', 'thrive-visual-editor'); ?></td>
            <td style="width: 20%;"><?php _e("Field Properties", "thrive-visual-editor"); ?></td>
            <td style="width: 25%;"><?php _e("Field Label / Description", "thrive-visual-editor"); ?></td>
            <td style="width: 10%;"><?php _e("Validation", "thrive-visual-editor") ?></td>
            <td style="width: 10%; text-align: center;"><?php _e("Required Field", "thrive-visual-editor"); ?></td>
            <td><?php _e('Show Icon', "thrive-visual-editor") ?></td>
        </tr></thead><?php

        $head = ob_get_contents();
        ob_end_clean();

        return $head;
    }

    /**
     * render a row for a text input
     *
     * @param array $element the element data
     * @param int $input_index
     *
     * @return string
     */
    protected function _textRow($element, $input_index)
    {
        $field_name = $field = $element['name'];
        $field = $this->encodeAttrName($field);
        ob_start();
        ?><tr class="tcb-row-hover">
        <?php if ($this->show_order) : ?><td class="tcb-text-center"><span class="tve_icm tve-ic-move tve-drag-handle"></span></td><?php endif ?>
            <td style="text-align: center">
                <?php if ($this->show_display_options) : ?>
                    <?php if ($field != 'email') : ?>
                        <input class="tve-lg-display-elem" data-elem-field="display" type="checkbox" id="<?php echo 'elem_display_' . $field ?>"<?php echo !empty($element['display']) ? ' checked="checked"' : '' ?> />
                    <?php else : ?>
                        -
                    <?php endif ?>
                <?php else :
                    echo $input_index;
                endif ?></td>
            <td>
                <?php echo isset($element['label']) ? $element['label'] : ucfirst($field_name) ?>
                <input type="hidden" class="lg_elem_field" value="<?php echo $field ?>" />
            </td>
            <td><input type="text" data-elem-field="label" value="<?php echo empty($element['label']) ? '' : $element['label'] ?>" class='thrive_txt_field_label' id='txt_label_<?php echo $field; ?>'/></td>
            <td style="text-align: center"><?php $this->_validationOptions($field, $element) ?></td>
            <td style="text-align: center"><input data-elem-field="required" type="checkbox"<?php echo !empty($element['required']) ? ' checked="checked"' : '' ?> id="required_<?php echo $field ?>"/></td>
            <td>
                <input data-elem-field="show_icon" type="checkbox" id="icon_<?php echo $field ?>"/>
                <button class="tve_lg_icon_picker tve_click" data-ctrl="function:auto_responder.open_icon_picker"
                data-field="<?php echo $field; ?>"><?php _e('Choose icon', "thrive-visual-editor") ?></button>
            </td></tr><?php

        $row = ob_get_contents();
        ob_end_clean();

        return $row;
    }

    /**
     * render a row for a select (dropdown) element
     *
     * @param array $element element data
     * @param int $input_index
     *
     * @return string
     */
    protected function _selectRow($element, $input_index)
    {
        $field_name = $field = $element['name'];
        $field = $this->encodeAttrName($field);
        ob_start();
        ?><tr class="tcb-row-hover">
        <?php if ($this->show_order) : ?><td class="tcb-text-center"><span class="tve_icm tve-ic-move tve-drag-handle"></span></td><?php endif ?>
        <td style="text-align: center"><?php echo $input_index; ?></td>
        <td>
            <?php echo isset($element['label']) ? $element['label'] : ucfirst($field_name) ?>
            <input type="hidden" class="lg_elem_field" value="<?php echo $field ?>" />
        </td>
        <td><input type="text" data-elem-field="label" value="<?php echo empty($element['default_value']) ? '' : $element['default_value'] ?>" class='thrive_txt_field_label' id='txt_label_<?php echo $field; ?>'/></td>
        <td style="text-align: center">&nbsp;</td>
        <td style="text-align: center"><input data-elem-field="required" type="checkbox"<?php echo !empty($element['required']) ? ' checked="checked"' : '' ?> id="required_<?php echo $field ?>"/></td>
        <td>
            <input data-elem-field="show_icon" type="checkbox" id="icon_<?php echo $field ?>"/>
            <button class="tve_lg_icon_picker tve_click" data-ctrl="function:auto_responder.open_icon_picker"
                    data-field="<?php echo $field; ?>"><?php _e('Choose icon', "thrive-visual-editor") ?></button>
        </td>
        </tr><?php

        $row = ob_get_contents();
        ob_end_clean();

        return $row;
    }

    /**
     * render a row for a radio input element
     *
     * @param array $element element data
     * @param int $input_index
     *
     * @return string
     */
    protected function _radioRow($element, $input_index)
    {
        $field_name = $field = $element['name'];
        $field = $this->encodeAttrName($field);
        ob_start();
        ?><tr class="tcb-row-hover">
        <?php if ($this->show_order) : ?><td class="tcb-text-center"><span class="tve_icm tve-ic-move tve-drag-handle"></span></td><?php endif ?>
        <td style="text-align: center"><?php echo $input_index; ?></td>
        <td>
            <?php echo isset($element['label']) ? $element['label'] : ucfirst($field_name) ?>
        </td>
        <td>
            <?php foreach ($element['options'] as $encoded_value => $radio_label) : ?>
                <input style="margin-bottom: 5px;" type="text" value="<?php echo $radio_label ?>"
                       class="thrive_txt_field_label"
                       id="txt_label_<?php echo $field . '_' . $encoded_value; ?>"/>
            <?php endforeach; ?>
        </td>
        <td style="text-align: center">&nbsp;</td>
        <td style="text-align: center"><input data-elem-field="required" type="checkbox"<?php echo !empty($element['required']) ? ' checked="checked"' : '' ?> id="required_<?php echo $field ?>"/></td>
        <td>&nbsp;</td>
        </tr><?php

        $row = ob_get_contents();
        ob_end_clean();

        return $row;
    }

    /**
     * render a row for a checkbox element
     *
     * @param array $element
     * @param int $input_index
     *
     * @return string
     */
    protected function _checkboxRow($element, $input_index)
    {
        $field_name = $field = $element['name'];
        $field = $this->encodeAttrName($field);
        ob_start();
        ?><tr class="tcb-row-hover">
        <?php if ($this->show_order) : ?><td class="tcb-text-center"><span class="tve_icm tve-ic-move tve-drag-handle"></span></td><?php endif ?>
        <td style="text-align: center"><?php echo $input_index; ?></td>
        <td>
            <?php echo isset($element['label']) ? $element['label'] : ucfirst($field_name) ?>
        </td>
        <td>
            <input data-elem-field="label" type="text" value="<?php echo $element['value'] ?>" class='thrive_txt_field_label'
                   id='txt_label_<?php echo $field; ?>'/>
        </td>
        <td style="text-align: center">&nbsp;</td>
        <td style="text-align: center"><input data-elem-field="required" type="checkbox"<?php echo !empty($element['required']) ? ' checked="checked"' : '' ?> id="required_<?php echo $field ?>"/></td>
        <td>&nbsp;</td>
        </tr><?php

        $row = ob_get_contents();
        ob_end_clean();

        return $row;
    }

    /**
     * render a row for a textarea element
     *
     * @param array $element element data
     * @param int $input_index
     *
     * @return string
     */
    protected function _textareaRow($element, $input_index)
    {
        return $this->_textRow($element, $input_index);
    }

    /**
     * output validation options
     *
     * @param string $field
     * @param array $element element data
     */
    protected function _validationOptions($field, $element)
    {
        $selected = !empty($element['validation']) ? $element['validation'] : '';
        ?><select id="validation_<?php echo $field ?>" class="tve_lg_validation_options" data-elem-field="validation">
            <option value="none"><?php _e("None", "thrive-visual-editor") ?></option>
            <option value="email"<?php echo $selected == 'email' ? ' selected="selected"' : '' ?>><?php _e('Email', 'thrive-visual-editor') ?></option>
            <option value="phone"<?php echo $selected == 'phone' ? ' selected="selected"' : '' ?>><?php _e('Phone number', 'thrive-visual-editor') ?></option>
        </select><?php
    }

    /**
     * replace all special (css meaning) characters with codes
     *
     * @param string $attr
     * @return string
     */
    public function encodeAttrName($attr)
    {
        $attr = str_replace("[", "_tbl_", $attr);
        $attr = str_replace("]", "_tbr_", $attr);
        $attr = str_replace("(", "_tbl2_", $attr);
        $attr = str_replace(")", "_tbr2_", $attr);
        $attr = str_replace(" ", "_tsp_", $attr);
        $attr = str_replace(".", "_tspnt_", $attr);
        $attr = str_replace("/", "_ts_", $attr);
        $attr = str_replace(",", "_tc_", $attr);
        $attr = str_replace(":", "_tcol_", $attr);

        return $attr;
    }

    /**
     * decode attribute name
     *
     * @param string $attr
     * @return string
     */
    public function decodeAttrName($attr)
    {
        $attr = str_replace("_tbl_", "[", $attr);
        $attr = str_replace("_tbr_", "]", $attr);
        $attr = str_replace("_tbl2_", "(", $attr);
        $attr = str_replace("_tbr2_", ")", $attr);
        $attr = str_replace("_tsp_", " ", $attr);
        $attr = str_replace("_tspnt_", ".", $attr);
        $attr = str_replace("_ts_", "/", $attr);
        $attr = str_replace("_tc_", ",", $attr);
        $attr = str_replace("_tcol_", ":", $attr);

        return $attr;
    }
}