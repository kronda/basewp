<?php

/**
 * Class Thrive_Clever_Widgets_Taxonomy_Archives_Tab
 */
class Thrive_Clever_Widgets_Taxonomy_Archives_Tab extends Thrive_Clever_Widgets_Tab implements Thrive_Clever_Widgets_Tab_Interface
{
    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $optionArr = $this->getSavedOptions()->getTabSavedOptions(6, $this->hanger);

        foreach ($this->getItems() as $id => $taxonomy) {
            $option = new Thrive_Clever_Widgets_Option();
            $option->setLabel($taxonomy->label);
            $option->setId($id);
            $option->setIsChecked(in_array($id, $optionArr));
            $this->options[] = $option;
        }
    }

    protected function getSavedOption($item)
    {
        return $this->getSavedOptionForTab(6, $item);
    }

    /**
     * @return $this
     */
    protected function initItems()
    {
        $this->setItems(get_taxonomies(array(
            'public' => true
        ), 'objects'));

        return $this;
    }

    /**
     * @param $taxonomy
     * @return bool
     */
    public function displayWidget($taxonomy = null)
    {
        if (!$taxonomy) {
            return false;
        }

        $this->hanger = 'show_widget_options';
        $showOption = $this->getSavedOption($taxonomy->taxonomy);
        $display = $showOption->isChecked;

        if ($display === true) {
            $this->hanger = 'hide_widget_options';
            $display = !$this->getSavedOption($taxonomy->taxonomy)->isChecked;
        }

        return $display;

    }

    public function isTaxonomyAllowed($taxonomy = null)
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption($taxonomy->taxonomy)->isChecked;
    }

    public function isTaxonomyDenied($taxonomy = null)
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption($taxonomy->taxonomy)->isChecked;
    }
}
