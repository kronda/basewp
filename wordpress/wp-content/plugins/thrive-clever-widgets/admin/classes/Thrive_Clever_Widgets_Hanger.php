<?php

class Thrive_Clever_Widgets_Hanger
{
    public $identifier;
    public $tabs = array();
    protected $widget;

    public function __construct($identifier, $widget)
    {
        $this->identifier = $identifier;
        $this->widget = $widget;
    }

    public function initTabs(Array $identifiers)
    {
        foreach ($identifiers as $identifier => $label) {
            /**
             * @var $tab Tab
             */
            $tab = Thrive_Clever_Widgets_Tab_Factory::build($identifier);
            $tab->setWidget($this->widget)
                ->setIdentifier($identifier)
                ->setLabel($label)
                ->setHanger($this->identifier)
                ->initOptions()
                ->initFilters();

            $this->tabs[] = $tab;
        }
    }

}
