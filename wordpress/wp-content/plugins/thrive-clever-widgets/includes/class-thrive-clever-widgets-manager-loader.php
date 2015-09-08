<?php

/**
 * Class Thrive_Clever_Widgets_Manager_Loader
 * Initiate all the actions/filters that are needed at some point
 */
class Thrive_Clever_Widgets_Manager_Loader
{
    protected $actions;
    protected $filters;

    public function __construct()
    {
        $this->filters = array();
        $this->actions = array();
    }

    public function add_action($hook, $component, $callback)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback);
    }

    public function add_filter($hook, $component, $callback)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback);
    }

    public function get_action($hook)
    {
        foreach ($this->actions as $key => $action) {
            if ($action['hook'] === $hook) {
                return $action;
            }
        }
        return null;
    }

    public function run()
    {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']));
        }
        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']));
        }
    }

    private function add($hooks, $hook, $component, $callback)
    {
        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback
        );
        return $hooks;
    }
}
