<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 19.09.2014
 * Time: 10:15
 */

if (!class_exists('TCB_Landing_Page')) {

    class TCB_Landing_Page
    {
        const HOOK_HEAD = 'tcb_landing_head';
        const HOOK_BODY_OPEN = 'tcb_landing_body_open';
        const HOOK_FOOTER = 'tcb_landing_footer';
        const HOOK_BODY_CLOSE = 'tcb_landing_body_close';

        /**
         * landing page id
         * @var int
         */
        protected $id;

        /**
         * holds the configuration array for the landing page
         * @var array
         */
        protected $config = array();

        /**
         * holds the tve_globals meta configuration values
         * @var array
         */
        protected $globals = array();

        /**
         * currently used landing page template
         * @var string
         */
        protected $template = '';

        /**
         * javascripts for the head and footer section, if any
         *
         * @var array
         */
        protected $globalScripts = array();

        /**
         * sent all necessary parameters to avoid extra calls to get_post_meta
         *
         * @param int $landing_page_id
         * @param string $landing_page_template
         */
        public function __construct($landing_page_id, $landing_page_template)
        {
            $this->id = $landing_page_id;
            $this->globals = tve_get_post_meta($landing_page_id, 'tve_globals');
            $this->config = tve_get_landing_page_config($landing_page_template);
            $this->template = $landing_page_template;
            $this->globalScripts = get_post_meta(get_the_ID(), 'tve_global_scripts', true);

            if (empty($this->globals)) {
                $this->globals = array();
            }
        }

        /**
         * outputs the HEAD section specific to the landing page
         * finally, it calls the tcb_landing_head hook to allow injecting other stuff in the head
         */
        public function head()
        {
            /* I think the favicon should be added using the wp_head hook and not like this */
            if (function_exists('thrive_get_options_for_post')) {
                $options = thrive_get_options_for_post();
                if (!empty($options['favicon'])) : ?>
                    <link rel="shortcut icon" href="<?php echo $options['favicon']; ?>"/>
                <?php endif;
            }

            if(function_exists('thrive_include_meta_post_tags')) {
                thrive_include_meta_post_tags();
            }

            $this->fonts();

            if (!empty($this->globalScripts['head'])) {
                echo $this->globalScripts['head'];
            }

            empty($this->config['do_not_strip_css']) ?
                $this->stripHeadCss() : wp_head();

            /* finally, call the tcb_landing_head hook */
            apply_filters(self::HOOK_HEAD, $this->id);
        }

        /**
         * outputs <link>s for each font used by the page
         * fonts come from the configuration array
         *
         * @return TCB_Landing_Page allows chained calls
         */
        protected function fonts()
        {
            if (empty($this->config['fonts'])) {
                return $this;
            }
            foreach ($this->config['fonts'] as $font) {
                echo sprintf('<link href="%s" rel="stylesheet" type="text/css" />', $font);
            }

            return $this;
        }

        /**
         * this calls the WP wp_head() function, it will remove every <style>..</style> from the head
         */
        protected function stripHeadCss()
        {
            /* capture the output and strip out some of the <style></style> nodes */
            ob_start();
            wp_head();
            $contents = ob_get_clean();
            /* keywords to search for within the CSS rules */
            $tcb_rules_keywords = array(
                '.ttfm', 'data-tve-custom-colour', '.tve_more_tag', '.thrive-adminbar-icon', '#wpadminbar', 'html { margin-top: 32px !important; }'
            );
            /* keywords to search for within CSS style node - classes and ids for the <style> element */
            $tcb_style_classes = array('tve_user_custom_style', 'tve_custom_style');

            if (preg_match_all('#<style(.*?)>(.+?)</style>#ms', $contents, $m)) {
                foreach ($m[2] as $index => $css_rules) {
                    $css_node = $m[1][$index];
                    $remove_it = true;
                    foreach ($tcb_rules_keywords as $tcb_keyword) {
                        if (strpos($css_rules, $tcb_keyword) !== false) {
                            $remove_it = false;
                            break;
                        }
                    }
                    if ($remove_it) {
                        foreach ($tcb_style_classes as $style_class) {
                            if (strpos($css_node, $style_class) !== false) {
                                $remove_it = false;
                                break;
                            }
                        }
                    }
                    if ($remove_it) {
                        $contents = str_replace($m[0][$index], '', $contents);
                    }
                }
            }
            echo $contents;
        }

        /**
         * get all the css data needed for this landing page that's been previously saved from the editor
         * example: body background, content background (if content is outside tve_editor) etc
         *
         * @return array
         */
        public function getCssData()
        {
            $config = $this->globals;
            $lp_data = array(
                'custom_color' => !empty($config['lp_bg']) ? ' data-tve-custom-colour="' . $config['lp_bg'] . '"' : '',
                'class' => !empty($config['lp_bgcls']) ? ' ' . $config['lp_bgcls'] : '',
                'css' => '',
                'main_area' => array(
                    'css' => ''
                )
            );
            if (!empty($config['lp_bg']) && $config['lp_bg'] == '#ffffff') {
                $lp_data['custom_color'] = '';
                $lp_data['css'] .= 'background-color:#ffffff;';
            }
            if (!empty($config['lp_bgp'])) {
                $lp_data['css'] .= "background-image:url('{$config['lp_bgp']}');background-repeat:repeat;background-size:auto;";
            } elseif (!empty($config['lp_bgi'])) {
                $lp_data['css'] .= "background-image:url('{$config['lp_bgi']}');background-repeat:no-repeat;background-size:cover;background-position:center center;";
            }
            if (!empty($config['lp_bga'])) {
                $lp_data['css'] .= "background-attachment:{$config['lp_bga']};";
                if ($config['lp_bga'] == 'fixed') {
                    $lp_data['class'] .= ($lp_data['class'] ? ' ' : '') . 'tve-lp-fixed';
                }
            }
            if (!empty($config['lp_cmw']) && !empty($config['lp_cmw_apply_to'])) { // landing page - content max width
                if ($config['lp_cmw_apply_to'] == 'tve_post_lp') {
                    $lp_data['main_area']['css'] .= "max-width: {$config['lp_cmw']}px;";
                }
            }

            $lp_data['class'] .= !empty($lp_data['class']) ? ' tve_lp' : 'tve_lp';

            return $lp_data;
        }

        /**
         * called right after <body> open tag
         */
        public function afterBodyOpen()
        {
            echo !empty($this->globalScripts['body']) ? $this->globalScripts['body'] : '';
            apply_filters(self::HOOK_BODY_OPEN, $this->id);
        }

        /**
         * called before the WP get_footer hook
         */
        public function footer()
        {
            apply_filters(self::HOOK_FOOTER, $this->id);
        }

        /**
         * called right before the <body> end tag
         */
        public function beforeBodyEnd()
        {
            apply_filters(self::HOOK_BODY_CLOSE, $this->id);
            echo !empty($this->globalScripts['footer']) ? $this->globalScripts['footer'] : '';
        }

        /* general usability functions - implemented like this - more developer friendly */

        /**
         * whether or not this landing page should have lightbox associated
         */
        public function needsLightbox()
        {
            return !empty($this->config['has_lightbox']);
        }

        /**
         * check if the associated lightbox exists and, if not, create it
         */
        public function checkLightbox()
        {
            if (!$this->needsLightbox()) {
                return;
            }

            if (!empty($this->globals['lightbox_id'])) {
                $lightbox = get_post($this->globals['lightbox_id']);
                if ($lightbox && ($lightbox->post_status === 'trash' || $lightbox->post_type != 'tcb_lightbox')) {
                    $lightbox = array();
                }
            }

            if (empty($lightbox)) {

                $this->globals['lightbox_id'] = $this->newLightbox();

                tve_update_post_meta($this->id, 'tve_globals', $this->globals);
            }

            /* check if the id of the lightbox from the content is different than the id of the generated lightbox */
            $post_content = tve_get_post_meta($this->id, 'tve_updated_post');
            if (strpos($post_content, "&quot;l_id&quot;:&quot;{$this->globals['lightbox_id']}&quot;") === false) {
                $post_content = preg_replace('#&quot;l_id&quot;:(null|&quot;(.*?)&quot;)#', '&quot;l_id&quot;:&quot;' . $this->globals['lightbox_id'] . '&quot;', $post_content);
                tve_update_post_meta($this->id, 'tve_updated_post', $post_content);
                tve_update_post_meta($this->id, 'tve_save_post', $post_content);
            }

        }

        /**
         * generate new lightbox specific for this landing page
         */
        public function newLightbox()
        {
            $landing_page = get_post($this->id);

            $tcb_content = $this->lightboxDefaultContent();

            $lightbox_globals = array(
                'l_cmw' => isset($this->config['lightbox']['max_width']) ? $this->config['lightbox']['max_width'] : '600px',
                'l_cmh' => isset($this->config['lightbox']['max_height']) ? $this->config['lightbox']['max_height'] : '600px',
            );

            return tve_create_lightbox('Lightbox - ' . $landing_page->post_title . ' (' . $this->config['name'] . ')', $tcb_content, $lightbox_globals, array('tve_lp_lightbox' => $this->template));
        }

        /**
         * fetch default lightbox content from one of the files inside landing-page/lightbox/ folder
         */
        public function lightboxDefaultContent()
        {
            ob_start();
            if (file_exists(dirname(dirname(__FILE__)) . '/lightboxes/' . $this->template . '.php')) {
                include dirname(dirname(__FILE__)) . '/lightboxes/' . $this->template . '.php';
            }
            return ob_get_clean();
        }

    }
}