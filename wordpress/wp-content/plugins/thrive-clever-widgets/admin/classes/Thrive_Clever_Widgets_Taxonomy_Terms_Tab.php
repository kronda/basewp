<?php

/**
 * Class Thrive_Clever_Widgets_Taxonomy_Terms_Tab
 */
class Thrive_Clever_Widgets_Taxonomy_Terms_Tab extends Thrive_Clever_Widgets_Tab implements Thrive_Clever_Widgets_Tab_Interface
{
    public function __construct()
    {

    }

    protected function matchItems()
    {
        if (!$this->getItems()) {
            return array();
        }

        $optionArr = $this->getSavedOptions()->getTabSavedOptions(1, $this->hanger);

        foreach ($this->getItems() as $key => $term) {
            $option = new Thrive_Clever_Widgets_Option();
            $option->setLabel($term->name);
            $option->setId($term->term_id);
            $option->setType($term->taxonomy);
            $option->setIsChecked(in_array($term->term_id, $optionArr));
            $this->options[] = $option;
        }
    }

    protected function getSavedOption($item)
    {
        return $this->getSavedOptionForTab(1, $item->term_id);
    }

    /**
     * @return $this
     */
    protected function initItems()
    {
        $taxonomies = get_taxonomies(array('public' => true));
        $terms = get_terms($taxonomies);
        $this->setItems($terms);

        return $this;
    }

    /**
     * For this case the filters are the taxonomies
     * @return array of Thrive_Clever_Widgets_Filter elements
     */
    public function getFilters()
    {
        if (!empty($this->filters)) {
            return $this->filters;
        }

        $taxonomies = array();
        $filters = array();
        foreach ($this->getItems() as $item) {
            if (in_array($item->taxonomy, $taxonomies)) {
                continue;
            }
            $taxonomies[] = $item->taxonomy;
            $taxonomy = $this->getTaxonomy($item->taxonomy);
            $filter = new Thrive_Clever_Widgets_Filter('taxonomyFilter', $taxonomy->name, $taxonomy->label);
            $filters[] = $filter;
        }
        return $filters;
    }

    /**
     * @param $taxonomyName
     * @return bool|object
     */
    public function getTaxonomy($taxonomyName)
    {
        return get_taxonomy($taxonomyName);
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
        $showOption = $this->getSavedOption($taxonomy);
        $display = $showOption->isChecked;

        if ($display === true) {
            $this->hanger = 'hide_widget_options';
            $display = !$this->getSavedOption($taxonomy)->isChecked;
        }

        return $display;

    }

    public function isTaxonomyAllowed($taxonomy = null)
    {
        $this->hanger = 'show_widget_options';
        return $this->getSavedOption($taxonomy)->isChecked;
    }

    public function isTaxonomyDenied($taxonomy = null)
    {
        $this->hanger = 'hide_widget_options';
        return $this->getSavedOption($taxonomy)->isChecked;
    }

    public function isPostAllowed($post)
    {
        //get all taxonomy terms for all taxonomies the $post has
        $taxonomies = get_taxonomies(array('public' => true));
        $post_terms = array();
        foreach($taxonomies as $taxonomy) {
            foreach(wp_get_post_terms($post->ID, $taxonomy) as $term) {
                $post_terms[] = $term;
            }
        }

        //check if any of the posts taxonomy terms is checked
        $this->hanger = 'show_widget_options';
        foreach($post_terms as $post_term) {
            if($this->getSavedOption($post_term)->isChecked) {
                return true;
            }
        }

        return false;
    }

    public function isPostDenied($post)
    {
        //get all taxonomy terms for all taxonomies the $post has
        $taxonomies = get_taxonomies(array('public' => true));
        $post_terms = array();
        foreach($taxonomies as $taxonomy) {
            foreach(wp_get_post_terms($post->ID, $taxonomy) as $term) {
                $post_terms[] = $term;
            }
        }

        //check if any of the posts taxonomy terms is checked
        $this->hanger = 'hide_widget_options';
        foreach($post_terms as $post_term) {
            if($this->getSavedOption($post_term)->isChecked) {
                return true;
            }
        }

        return false;
    }

}
