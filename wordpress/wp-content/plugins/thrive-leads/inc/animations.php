<?php
/**
 * Structure for all the available animations
 */

class TVE_Leads_Animation_Abstract
{
    const ANIM_INSTANT = 'instant';
    const ANIM_ZOOM_IN = 'zoom_in';
    const ANIM_ZOOM_OUT = 'zoom_out';
    const ANIM_ROTATIONAL = 'rotational';
    const ANIM_SLIDE_IN_TOP = 'slide_top';
    const ANIM_SLIDE_IN_BOT = 'slide_bot';
    const ANIM_SLIDE_IN_LEFT = 'slide_left';
    const ANIM_SLIDE_IN_RIGHT = 'slide_right';

    public static $AVAILABLE = array(
        self::ANIM_INSTANT,
        self::ANIM_ZOOM_IN,
        self::ANIM_ZOOM_OUT,
        self::ANIM_ROTATIONAL,
        self::ANIM_SLIDE_IN_TOP,
        self::ANIM_SLIDE_IN_BOT,
        self::ANIM_SLIDE_IN_LEFT,
        self::ANIM_SLIDE_IN_RIGHT,
    );

    /**
     * @var string title to be displayed
     */
    protected $title = '';

    /**
     * @var string internal animation key
     */
    protected $key = '';

    /**
     * base dir path for the plugin
     *
     * @var string
     */
    protected $base_dir = '';

    /**
     * @param $type
     * @param $config array
     * @return TVE_Leads_Animation_Abstract
     */
    public static function factory($type)
    {
        $parts = explode('_', $type);

        $class = 'TVE_Leads_Animation';
        foreach ($parts as $part) {
            $class .= '_' . ucfirst($part);
        }

        if (!class_exists($class)) {
            return null;
        }

        return new $class($type);
    }

    /**
     * merge the received config with the defaults
     *
     */
    public function __construct($key)
    {
        $this->key = $key;
        $this->base_dir = plugin_dir_path(dirname(__FILE__));
    }

    /**
     * get the title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * prepare data to be used in JS
     *
     * @return array
     */
    public function to_array()
    {
        return array(
            'title' => $this->get_title(),
            'key' => $this->key,
            'config' => $this->config
        );
    }

    /**
     * output javascript required for the animation, if the case applies
     *
     * renders directly JS code, without returning it
     *
     * @param $data - this should usually be the variation
     */
    public function output_js($data)
    {
        if (is_file($this->base_dir . 'js/animations/' . $this->key . '.js.php')) {
            include $this->base_dir . 'js/animations/' . $this->key . '.js.php';
        }
    }

    /**
     * parse a CSS selector, making sure it's compliant
     *
     * @param $raw
     */
    protected function parse_selector($raw, $prefix = '.')
    {
        $selector = '';
        $raw = str_replace(array('#', '.'), '', $raw);

        $parts = explode(',', $raw);
        foreach ($parts as $part) {
            $selector .= ($selector ? ',' : '') . $prefix . $part;
        }

        return trim($selector, ', ');
    }

    /**
     * get the human-friendly animation name (and also include the configuration settings)
     *
     * @return string
     */
    public function get_display_name()
    {
        return $this->get_title();
    }
}

/**
 * Instant Animation - No Animation
 *
 * Class TVE_Leads_Animation_Instant
 */
class TVE_Leads_Animation_Instant extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Instant';
}

/**
 * Make the form zoom in at display
 *
 * Class TVE_Leads_Animation_Zoom_In
 */
class TVE_Leads_Animation_Zoom_In extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Zoom In';
}

/**
 * Make the form zoom out at display
 *
 * Class TVE_Leads_Animation_Zoom_Out
 */
class TVE_Leads_Animation_Zoom_Out extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Zoom Out';
}

/**
 * Rotate the form at display
 *
 * Class TVE_Leads_Animation_Rotational
 */
class TVE_Leads_Animation_Rotational extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Rotational';

}

/**
 * The form slides in from the top
 *
 * Class TVE_Leads_Animation_Slide_Top
 */
class TVE_Leads_Animation_Slide_Top extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Slide in from Top';

}

/**
 * The form slides in from the Bottom
 *
 * Class TVE_Leads_Animation_Slide_Bot
 */
class TVE_Leads_Animation_Slide_Bot extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Slide in from Bottom';
}

/**
 * Form slides in from lateral
 *
 * Class TVE_Leads_Animation_Slide_Left
 */
class TVE_Leads_Animation_Slide_Left extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Slide in from Left';
}

/**
 * Form slides in from right
 *
 * Class TVE_Leads_Animation_Slide_Right
 */
class TVE_Leads_Animation_Slide_Right extends TVE_Leads_Animation_Abstract
{
    protected $title = 'Slide in from Right';
}