<?php

/**
 * Class Thrive_Clever_Widgets_Posts_Tab
 */
class Thrive_Clever_Widgets_Posts_Tab extends Thrive_Clever_Widgets_Tab implements Thrive_Clever_Widgets_Tab_Interface
{
    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $optionArr = $this->getSavedOptions()->getTabSavedOptions(2, $this->hanger);

        foreach ($this->getItems() as $post) {
            $option = new Thrive_Clever_Widgets_Option();
            $option->setLabel($post->post_title);
            $option->setId($post->ID);
            $option->setIsChecked(in_array($post->ID, $optionArr));
            $this->options[] = $option;
        }
    }

    /**
     * @param $item WP_Post
     * @return Thrive_Clever_Widgets_Option
     */
    protected function getSavedOption($item)
    {
        return $this->getSavedOptionForTab(2, $item->ID);
    }

    /**
     * @return $this
     */
    protected function initItems()
    {
        $this->setItems(get_posts(array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        )));

        return $this;
    }

    /**
     * @param $post WP_Post
     * @return bool
     */
    public function displayWidget(WP_Post $post)
    {
        $this->hanger = 'show_widget_options';
        $showOption = $this->getSavedOption($post);
        $display = $showOption->isChecked;

        if ($display === true) {
            $this->hanger = 'hide_widget_options';
            $display = !$this->getSavedOption($post)->isChecked;
        }

        return $display;

    }

    public function isPostDenied($post)
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption($post)->isChecked;
    }

    public function isPostAllowed($post)
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption($post)->isChecked;
    }

}
