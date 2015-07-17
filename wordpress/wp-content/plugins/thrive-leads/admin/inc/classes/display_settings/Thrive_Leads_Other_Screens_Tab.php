<?php

/**
 * Class OtherScreensTab
 */
class Thrive_Leads_Other_Screens_Tab extends Thrive_Leads_Tab implements Thrive_Leads_Tab_Interface
{
    /**
     * Predefined screens
     * @var array
     */
    protected $items = array(
        'front_page' => 'Front Page',
        'all_post' => 'All Posts',
        'all_page' => 'All Pages',
        'blog_index' => 'Blog Index',
        '404_error_page' => '404 Error Page',
        'search_page' => 'Search page'
    );

    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $options = $this->getSavedOptions();

        $optionArr = $options->getTabSavedOptions(0, $this->hanger);

        foreach ($this->getItems() as $id => $label) {
            $option = new Thrive_Leads_Option();
            $option->setLabel($label);
            $option->setId($id);
            $option->setIsChecked(in_array($id, $optionArr));
            $this->options[] = $option;
        }
    }

    /**
     * @param string $item
     * @return Option|Thrive_Leads_Option
     */
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
        return $this;
    }

    /**
     * @param $screen string
     * @return bool
     */
    public function displayWidget($screen)
    {
        $this->hanger = 'show_group_options';
        $showOption = $this->getSavedOption($screen);
        $display = $showOption->isChecked;

        if ($display === true) {
            $this->hanger = 'hide_group_options';
            $display = !$this->getSavedOption($screen)->isChecked;
        }

        return $display;

    }

    public function isScreenAllowed($screen)
    {
        $this->hanger = 'show_group_options';
        return $this->getSavedOption($screen)->isChecked;
    }

    public function isScreenDenied($screen)
    {
        $this->hanger = 'hide_group_options';
        return $this->getSavedOption($screen)->isChecked;
    }

    public function allTypesAllowed($post_type = 'post')
    {
        $this->hanger = 'show_group_options';
        return $this->getSavedOption('all_' . $post_type)->isChecked;
    }

    public function allTypesDenied($post_type = 'post')
    {
        $this->hanger = 'hide_group_options';
        return $this->getSavedOption('all_' . $post_type)->isChecked;
    }
}
