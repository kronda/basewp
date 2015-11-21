<?php
/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 9/25/2015
 * Time: 10:24 AM
 */

require_once dirname(__FILE__) . "/Thrive_Font_Import_Manager_View.php";
require_once dirname(__FILE__) . "/Thrive_Font_Import_Manager_Data.php";

if (class_exists('Thrive_Font_Import_Manager')) {
    return;
}

class Thrive_Font_Import_Manager
{
    const OPTION_NAME = 'thrive_custom_font_pack';

    protected static $instance;

    protected $view;

    protected $url;

    /**
     * @var $domain string Translation domain
     */
    protected $domain;

    protected $messages = array();

    protected function __construct($url, $domain)
    {
        $this->view = new Thrive_Font_Import_Manager_View(dirname(dirname(__FILE__)) . "/views");
        $this->domain = $domain;
        $this->url = trim($url, "/");
    }

    public function mainPage()
    {
        if (!empty($_POST['attachment_id'])) {
            $this->handlePost();
        }

        $font_pack = get_option(self::OPTION_NAME, array());

        if (!empty($font_pack['css_file'])) {
            wp_enqueue_style('thrive_custom_fonts_manager', $font_pack['css_file']);
        }

        $this->enqueue();

        $data['font_pack'] = $font_pack;
        $data['messages'] = $this->messages;
        $data['domain'] = $this->domain;
        $data['enqueue_url'] = $this->getEnqueueUrl();
        $data['logo_url'] = $this->getLogoUrl();

        $this->view->render('main', $data);
    }

    protected function handlePost()
    {
        $handler = new Thrive_Font_Import_Manager_Data();

        if (!empty($_POST['attachment_id']) && $_POST['attachment_id'] != -1) {
            $maybe_zip_file = get_attached_file($_POST['attachment_id']);
            $maybe_zip_url = wp_get_attachment_url($_POST['attachment_id']);

            try {
                $new_font_pack = $handler->processZip($maybe_zip_file, $maybe_zip_url);
                $new_font_pack['attachment_id'] = $_POST['attachment_id'];

                update_option(self::OPTION_NAME, $new_font_pack);

                $this->messages['success'][] = __("Font pack saved !", $this->domain);

            } catch (Exception $e) {
                $this->messages['error'][] = $e->getMessage();
            }
        } else {

            try {
                $font_pack = get_option(self::OPTION_NAME, array());
                if (!empty($font_pack['attachment_id']) && is_file($font_pack['zip_path'])) {
                    $handler->deleteDir($font_pack['folder']);
                    delete_option(self::OPTION_NAME);
                }
                $this->messages['success'][] = __("Font pack removed", $this->domain);
            } catch (Exception $e) {
                $this->messages['error'][] = $e;
            }
        }
    }

    public function enqueue()
    {
        if ($this->domain === 'thrive') {
            wp_enqueue_style('thrive-theme-options', $this->getEnqueueUrl() . '/inc/css/theme-options.css', false, '2013-07-03');
            wp_enqueue_style('thrive-admin-colors', $this->getEnqueueUrl() . '/inc/css/thrive_admin_colours.css');
            wp_enqueue_style('thrive-pure-css');
        } else {
            wp_enqueue_style('thrive-theme-options', $this->getEnqueueUrl() . '/admin/css/theme-options.css', false, '2013-07-03');
            wp_enqueue_style('thrive-admin-colors', $this->getEnqueueUrl() . '/admin/css/thrive_admin_colours.css');
            wp_enqueue_style('thrive-pure-css', $this->getEnqueueUrl() . '/admin/css/pure-min.css');
        }

        wp_enqueue_style('thrive-font-import-manager-css', $this->url . '/font-import-manager/views/css/manager.css');

        wp_enqueue_media();
        wp_enqueue_script('thrive-font-import-manager-js', $this->url . '/font-import-manager/views/js/manager.js', array('jquery', 'media-upload', 'thickbox'));
    }

    public static function getInstance($url = null, $domain = 'thrive')
    {
        if (empty(self::$instance)) {
            self::$instance = new Thrive_Font_Import_Manager($url, $domain);
        }

        return self::$instance;
    }

    public static function getImportedFonts()
    {
        $font_pack = get_option(self::OPTION_NAME, array());
        if (empty($font_pack)) {
            return array();
        }

        $fonts = array();
        foreach ($font_pack['font_families'] as $name) {
            $fonts[] = array(
                'family' => $name,
                'variants' => array(
                    'regular',
                ),
                'subsets' => array(
                    'latin'
                )
            );
        }

        return $fonts;
    }

    public static function getCssFile()
    {
        $font_pack = get_option(self::OPTION_NAME, array());
        if (empty($font_pack)) {
            return null;
        }

        return $font_pack['css_file'];
    }

    /**
     * @param $font string font-family
     * @return bool
     */
    public static function isImportedFont($font)
    {
        $font_pack = get_option(self::OPTION_NAME, array());
        if (empty($font_pack)) {
            return false;
        }

        return in_array($font, $font_pack['font_families']);
    }

    protected function getEnqueueUrl()
    {
        return $this->domain === 'thrive' ? get_template_directory_uri() : tve_editor_url();
    }

    protected function getLogoUrl()
    {
        if($this->domain === 'thrive') {
            return $this->getEnqueueUrl() . '/inc/images/TT-logo-small.png';
        } else {
            return $this->getEnqueueUrl() . '/editor/css/images/tcb-logo-large.png';
        }
    }
}
