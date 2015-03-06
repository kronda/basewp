<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Template_Collector' ) ) :
/**
 * Holds all of the registered templates and provides an interface for those templates.
 *
 * @since 1.0.0.
 */
class TTFMP_Template_Collector {
	/**
	 * The collection of registered templates.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The registered templates.
	 */
	var $templates = array();

	/**
	 * Collects the names of templates for simple lookup.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The list of template names.
	 */
	var $template_names = array();

	/**
	 * The one instance of TTFMP_Template_Collector.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Template_Collector
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Template_Collector instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Template_Collector
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add a template to the collector.
	 *
	 * @since  1.0.0.
	 *
	 * @param  TTTFMP_Template    $template    The template as a TTTFMP_Template object.
	 * @return void
	 */
	public function add_template( $template ) {
		if ( isset( $template->name ) ) {
			$this->templates[ $template->name ] = $template;
		}
	}

	/**
	 * Get the templates.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    An array of all of the registered templates.
	 */
	public function get_templates() {
		return $this->templates;
	}

	/**
	 * Get an individual template by name.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string                  $name    The registered template name.
	 * @return TTTFMP_Template|bool             The template if successful; False if template is not found.
	 */
	public function get_template( $name ) {
		$templates = $this->get_templates();
		if ( isset( $templates[ $name ] ) ) {
			return $templates[ $name ];
		} else {
			return false;
		}
	}

	/**
	 * Get a list of the template names.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The names of the templates.
	 */
	public function get_template_names() {
		return array_keys( $this->get_templates() );
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_template_collector' ) ) :
/**
 * Instantiate or return the one TTFMP_Template_Collector instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Template_Collector
 */
function ttfmp_get_template_collector() {
	return TTFMP_Template_Collector::instance();
}
endif;

if ( ! function_exists( 'ttfmp_register_template' ) ) :
/**
 * Add a template to the collector.
 *
 * @since  1.0.0.
 *
 * @param  TTTFMP_Template    $template    The template as a TTTFMP_Template object.
 * @return void
 */
function ttfmp_register_template( $template ) {
	ttfmp_get_template_collector()->add_template( $template );
}
endif;