<?php

/**
 * Class Thrive_Clever_Widgets_Other_Screens_Tab
 */
class Thrive_Clever_Widgets_Other_Screens_Tab extends Thrive_Clever_Widgets_Tab implements Thrive_Clever_Widgets_Tab_Interface
{

    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $options = $this->getSavedOptions();

        $optionArr = $options->getTabSavedOptions(0, $this->hanger);

        foreach ($this->getItems() as $id => $label) {
            $option = new Thrive_Clever_Widgets_Option();
            $option->setLabel($label);
            $option->setId($id);
            $option->setIsChecked(in_array($id, $optionArr));
            $this->options[] = $option;
        }
    }

    protected function getSavedOption($item)
    {
        return $this->getSavedOptionForTab(0, $item);
    }

    /**
     * All the $items are hardcoded in class property
     * @return $this
     */
    protected function initItems()
    {
        $this->items = array(
            'front_page' => __('Front Page', 'thrive-cw'),
            'all_post' => __('All Posts', 'thrive-cw'),
            'all_page' => __('All Pages', 'thrive-cw'),
            'blog_index' => __('Blog Index', 'thrive-cw'),
            '404_error_page' => __('404 Error Page', 'thrive-cw'),
            'search_page' => __('Search page', 'thrive-cw')
        );
        return $this;
    }

    /**
     * @param $screen string
     * @return bool
     */
    public function displayWidget($screen)
    {
        $this->hanger = 'show_widget_options';
        $showOption = $this->getSavedOption($screen);
        $display = $showOption->isChecked;

        if ($display === true) {
            $this->hanger = 'hide_widget_options';
            $display = !$this->getSavedOption($screen)->isChecked;
        }

        return $display;

    }

    public function isScreenAllowed($screen)
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption($screen)->isChecked;
    }

    public function isScreenDenied($screen)
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption($screen)->isChecked;
    }

    public function allTypesAllowed($post_type = 'post')
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption('all_' . $post_type)->isChecked;
    }

    public function allTypesDenied($post_type = 'post')
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption('all_' . $post_type)->isChecked;
    }
}
