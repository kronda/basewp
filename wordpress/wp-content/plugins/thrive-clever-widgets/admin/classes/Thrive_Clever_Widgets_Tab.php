<?php

/**
 * Class Tab
 * Basic implementation of a tab
 */
abstract class Thrive_Clever_Widgets_Tab implements Thrive_Clever_Widgets_Tab_Interface
{
    /**
     * @var array of items from the wordpress database
     */
    protected $items = array();

    /**
     * @var array of Thrive_Clever_Widgets_Option build based on the items
     */
    public $options = array();

    /**
     * @var array "Select All, Select None, etc"
     */
    public $actions = array();

    /**
     * @var array
     */
    public $filters = array();

    /**
     * @var string widget identifier
     */
    protected $widget;

    /**
     * @var Thrive_Clever_Widgets_Widget_Options saved by user
     */
    protected $savedOptions;

    /**
     * @var string tab identifier
     */
    public $identifier;

    /**
     * @var string tab label
     */
    public $label;

    /**
     * @var string hanger identifier
     */
    protected $hanger;

    public function __construct()
    {
        $this->actions = $this->getActions();
    }

    /**
     * @return array of Thrive_Clever_Widgets_Option models
     */
    final public function getOptions()
    {
        if (empty($this->options)) {
            $this->matchItems();
        }
        return $this->options;
    }

    /**
     * Get the items from database once
     * If the $items is not initialized initItems is called which is an abstract method
     * and has to be implemented for each tab
     * @return array
     */
    protected function getItems()
    {
        if (empty($this->items)) {
            $this->initItems();
        }

        return $this->items;
    }

    /**
     * Read the options from the database once
     * Init Thrive_Clever_Widgets_Option with json strings
     * @return Thrive_Clever_Widgets_Widget_Options
     */
    protected function getSavedOptions()
    {
        if ($this->savedOptions) {
            return $this->savedOptions;
        }

        $widgetOptions = new Thrive_Clever_Widgets_Widget_Options($this->getWidget());
        $widgetOptions->initOptions();
        $this->savedOptions = $widgetOptions;

        return $widgetOptions;
    }

    public function setSavedOptions(Thrive_Clever_Widgets_Widget_Options $savedOptions)
    {
        $this->savedOptions = $savedOptions;
        return $this;
    }

    /**
     * Overwrite this method to set a specific list of actions
     * @return array of Thrive_Clever_Widgets_Action
     */
    public function getActions()
    {
        return array(
            new Thrive_Clever_Widgets_Action('selectAll', '', __('Select All', 'thrive-cw')),
            new Thrive_Clever_Widgets_Action('selectNone', '', __('Select None', 'thrive-cw'))
        );
    }

    /**
     * Overwrite this method for specific list of filters
     * @return array empty
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set the items outside the box
     * @param array $items
     * @return $this
     */
    public function setItems(Array $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Can be called outside the box
     * @return $this
     */
    public function initFilters()
    {
        $this->filters = $this->getFilters();
        return $this;
    }

    /**
     * @param string $widget
     * @return $this
     */
    public function setWidget($widget)
    {
        $this->widget = $widget;
        return $this;
    }

    /**
     * @return string
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @param mixed $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $label
     * @return $this;
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $hanger
     * @return $this
     */
    public function setHanger($hanger)
    {
        $this->hanger = $hanger;
        return $this;
    }

    /**
     * @return string
     */
    public function getHanger()
    {
        return $this->hanger;
    }

    /**
     * Callable from outside the box
     * Init the items and options in one call
     * @return $this
     */
    public function initOptions()
    {
        $this->initItems();
        $this->matchItems();
        return $this;
    }

    protected function getSavedOptionForTab($tabIndex, $id)
    {
        $savedOptions = $this->getSavedOptions();

        $optionArr = $savedOptions->getTabSavedOptions($tabIndex, $this->hanger);
        $option = new Thrive_Clever_Widgets_Option();

        if (empty($optionArr)) {
            return $option;
        }

        $hanger = $this->hanger;
        $options = json_decode(stripcslashes($savedOptions->$hanger));
        if (!$options) {
            return new Thrive_Clever_Widgets_Option();
        }

        $option->setId($id);
        $option->setLabel(isset($this->items[$id]) ? $this->items[$id] : '');
        $option->setIsChecked(in_array($id, $optionArr));

        return $option;
    }

    /**
     * Specific tab has to implement this function which transforms
     * items(pages, posts, post types) into Thrive_Clever_Widgets_Option models
     * @return void
     */
    abstract protected function matchItems();

    /**
     * Has to get the Thrive_Clever_Widgets_Option from json string based on the $item
     * @param $item
     * @return Thrive_Clever_Widgets_Option
     */
    abstract protected function getSavedOption($item);

    /**
     * Read items from the database and initiate them
     * @return $this
     */
    abstract protected function initItems();

}
