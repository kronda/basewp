<?php

class Thrive_Clever_Widgets_Visitors_Status_Tab extends Thrive_Clever_Widgets_Tab
{
    protected $items;

    /**
     * Specific tab has to implement this function which transforms
     * items(pages, posts, post types) into Thrive_Clever_Widgets_Option models
     * @return void
     */
    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $optionArr = $this->getSavedOptions()->getTabSavedOptions(7, $this->hanger);

        foreach ($this->getItems() as $id => $label) {
            $option = new Thrive_Clever_Widgets_Option();
            $option->setLabel($label);
            $option->setId($id);
            $option->setIsChecked(in_array($id, $optionArr));
            $this->options[] = $option;
        }
    }

    /**
     * Has to get the Thrive_Clever_Widgets_Option from json string based on the $item
     * @param $item
     * @return Thrive_Clever_Widgets_Option
     */
    protected function getSavedOption($item)
    {
        return $this->getSavedOptionForTab(7, $item);
    }

    /**
     * Read items from the database and initiate them
     * @return $this
     */
    protected function initItems()
    {
        $this->items = array(
            'logged_in' => __('Logged in', 'thrive-cw'),
            'logged_out' => __('Logged out', 'thrive-cw'),
        );

        return $this;
    }

    public function isStatusAllowed($status)
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption($status)->isChecked;
    }

    public function isStatusDenied($status)
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption($status)->isChecked;
    }

}
