<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 22.01.2015
 * Time: 11:50
 */
class Thrive_Clever_Widgets_Others_Tab extends Thrive_Clever_Widgets_Tab
{
    /** @var  Thrive_Clever_Widgets_Visitors_Status_Tab */
    protected $visitor_status;

    /** @var  Thrive_Clever_Widgets_Direct_Urls_Tab */
    protected $direct_urls;

    public function __construct()
    {
        $this->visitor_status = new Thrive_Clever_Widgets_Visitors_Status_Tab();
        $this->direct_urls = new Thrive_Clever_Widgets_Direct_Urls_Tab();

        $this->direct_urls->setExclusions($this->visitor_status->getItems());

        parent::__construct();
    }

    /**
     * Specific tab has to implement this function which transforms
     * items(pages, posts, post types) into Thrive_Clever_Widgets_Option models
     * @return array
     */
    protected function matchItems()
    {
        $this->visitor_status->matchItems();
        $this->direct_urls->matchItems();

        $this->options = array_merge(
            $this->visitor_status->options,
            $this->direct_urls->options
        );

        return $this;
    }

    /**
     * Has to get the Thrive_Clever_Widgets_Option from json string based on the $item
     * @param $item
     * @return Thrive_Clever_Widgets_Option
     */
    protected function getSavedOption($item)
    {
        return new Thrive_Clever_Widgets_Option();
    }

    /**
     * Read items from the database and initiate them
     * @return $this
     */
    protected function initItems()
    {
        $this->visitor_status->getItems();
        $this->direct_urls->getItems();
        return $this;
    }

    public function setHanger($hanger)
    {
        $this->hanger = $hanger;
        $this->visitor_status->setHanger($hanger);
        $this->direct_urls->setHanger($hanger);

        return $this;
    }

    /**
     * @param string $widget
     * @return $this
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
        $this->visitor_status->setWidget($widget);
        $this->direct_urls->setWidget($widget);

        return $this;
    }
}
