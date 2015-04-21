<?php

/**
 * Contains the class for handling the administration interface
 *
 * @package    Code_Snippets
 * @subpackage Administration
 */

/**
 * This class handles the admin interface for Code Snippets
 *
 * Don't directly access the methods in this class or attempt to
 * re-initialize it. Instead, use the instance in $code_snippets->admin
 *
 * @since      1.7.1
 * @package    Code_Snippets
 * @subpackage Administration
 */
class Code_Snippets_Admin {

	/**
	 * The full URLs to the admin pages
	 *
	 * @var    string
	 * @since  1.7.1
	 * @access public
	 */
	public $manage_url, $single_url, $import_url = '';

	/**
	 * The hooks for the admin pages
	 * Used primarily for enqueueing scripts and styles
	 *
	 * @var    string
	 * @since  1.7.1
	 * @access public
	 */
	public $manage_page, $single_page, $import_page = '';

	/**
	 * Initializes the variables and
	 * loads everything needed for the class
	 *
	 * @since 1.7.1
	 */
	function __construct() {
		global $code_snippets;

		$this->manage_slug = apply_filters( 'code_snippets/admin/manage_slug', 'snippets' );
		$this->single_slug = apply_filters( 'code_snippets/admin/single_slug', 'snippet' );

		$this->manage_url  = self_admin_url( 'admin.php?page=' . $this->manage_slug );
		$this->single_url  = self_admin_url( 'admin.php?page=' . $this->single_slug );

		$this->setup_hooks();
	}

	/**
	 * Register action and filter hooks
	 *
	 * @since  1.7.1
	 * @access private
	 * @return void
	 */
	function setup_hooks() {
		global $code_snippets;

		/* add the administration menus */
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ), 5 );
		add_action( 'network_admin_menu', array( $this, 'add_admin_menus' ), 5 );

		/* register the importer */
		add_action( 'admin_init', array( $this, 'load_importer' ) );
		add_action( 'network_admin_menu', array( $this, 'add_import_admin_menu' ) );

		/* add helpful links to the Plugins menu */
		add_filter( 'plugin_action_links_' . $code_snippets->basename, array( $this, 'settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), 10, 2 );

		/* Add a custom icon to Snippets menu pages */
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_icon_style' ) );

		/* Add the description editor to the Snippets > Add New page */
		add_action( 'code_snippets/admin/single', array( $this, 'description_editor_box' ), 5 );

		/* Handle saving the user's screen option preferences */
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );

		/* Allow super admins to control site admins access to snippet admin menus */
		add_filter( 'mu_menu_items', array( $this, 'mu_menu_items') );

		/* Add the survey notice on the manage snippets page */
		add_action( 'code_snippets/admin/manage', array( $this, 'survey_message' ) );

		/* Remove incompatible Debug Bar Console CodeMirror version */
		$this->remove_debug_bar_codemirror();
	}

	/**
	 * Remove the old CodeMirror version used by the Debug Bar Console
	 * plugin that is messing up the snippet editor
	 * @since 1.9
	 */
	function remove_debug_bar_codemirror() {
		global $pagenow;

		/* Try to discern if we are on the single snippet page as best as we can at this early time */
		is_admin() && 'admin.php' === $pagenow && isset( $_GET['page' ] ) && 'snippet' === $_GET['page']

		/* Remove the action and stop all Debug Bar Console scripts */
		&& remove_action( 'debug_bar_enqueue_scripts', 'debug_bar_console_scripts' );
	}

	/**
	 * Check if we are on the pre-3.8 interface
	 *
	 * @return boolean
	 * @since  1.9.1
	 */
	function is_legacy_interface() {
		return !defined( 'MP6' ) && version_compare( $GLOBALS['wp_version'], '3.8-alpha', '<' );
	}

	/**
	 * Handles saving the user's snippets per page preference
	 *
	 * @param  unknown $status
	 * @param  string  $option
	 * @param  unknown $value
	 * @return unknown
	 */
	function set_screen_option( $status, $option, $value ) {
		if ( 'snippets_per_page' === $option )
			return $value;
	}

	/**
	 * Allow super admins to control site admin access to
	 * snippet admin menus
	 *
	 * Adds a checkbox to the *Settings > Network Settings*
	 * network admin menu
	 *
	 * @since  1.7.1
	 * @access private
	 *
	 * @param  array $menu_items The current mu menu items
	 * @return array             The modified mu menu items
	 */
	function mu_menu_items( $menu_items ) {
		$menu_items['snippets'] = __( 'Snippets', 'code-snippets' );
		return $menu_items;
	}

	/**
	 * Load the Code Snippets importer
	 *
	 * Add both an importer to the Tools menu
	 * and an Import Snippets page to the network admin menu
	 *
	 * @since  1.6
	 * @access private
	 * @return void
	 */
	function load_importer() {
		global $code_snippets;

		/* Only register the importer if the current user can manage snippets */
		if ( defined( 'WP_LOAD_IMPORTERS' ) && current_user_can( $code_snippets->get_cap() ) ) {

			/* Load Importer API */
			require_once ABSPATH . 'wp-admin/includes/import.php';

			if ( ! class_exists( 'WP_Importer' ) ) {
				$class_wp_importer = ABSPATH .  'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $class_wp_importer ) ) {
					require_once $class_wp_importer;
				}
			}

			/* Register the Code Snippets importer with WordPress */
			register_importer(
				'code-snippets',
				__( 'Code Snippets', 'code-snippets' ),
				__( 'Import snippets from a Code Snippets export file', 'code-snippets' ),
				array( $this, 'display_import_menu' )
			);
		}

		$this->import_url  = self_admin_url( 'admin.php?import=code-snippets' );
		add_action( 'load-importer-code-snippets', array( $this, 'load_import_menu' ) );
	}

	/**
	 * Load contextual help tabs for an admin screen.
	 *
	 * @since  1.8
	 * @access public
	 * @param  string $slug The file handle (filename with no path or extension) to load
	 * @return void
	 */
	public function load_help_tabs( $slug ) {
		global $code_snippets;
		include $code_snippets->plugin_dir . "admin/help/{$slug}.php";
	}

	/**
	 * Load an admin view template
	 *
	 * @since  1.8
	 * @access public
	 * @param  string $slug The file handle (filename with no path or extension) to load
	 * @return void
	 */
	public function get_view( $slug ) {
		global $code_snippets;
		require $code_snippets->plugin_dir . "admin/views/{$slug}.php";
	}

	/**
	 * Display the admin status and error messages
	 *
	 * @since  1.8
	 * @access public
	 * @param  string $slug The file handle (filename with no path or extension) to load
	 * @return void
	 */
	public function get_messages( $slug ) {
		global $code_snippets;
		require $code_snippets->plugin_dir . "admin/messages/{$slug}.php";
	}

	/**
	 * Check if the current user can manage snippets.
	 * If not, display an error message
	 *
	 * @since  1.9.1.1
	 * @access public
	 * @return void
	 */
	public function check_perms() {
		global $code_snippets;

		if ( ! current_user_can( $code_snippets->get_cap() ) ) {
			wp_die( __( 'You are not access this page.', 'code-snippets' ) );
		}
	}

	/**
	 * Add the dashboard admin menu and subpages
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @uses   add_menu_page()    To register a top-level menu
	 * @uses   add_submenu_page() To register a submenu page
	 * @uses   apply_filters()    To retrieve the current menu slug
	 * @uses   plugins_url()      To retrieve the URL to a resource
	 * @return void
	 */
	function add_admin_menus() {
		global $code_snippets;

		/* Provide a raster icon for the legacy interface */
		if ( $this->is_legacy_interface() ) {
			$menu_icon = apply_filters( 'code_snippets/admin/menu_icon_url',
				plugins_url( 'assets/images/menu-icon.png', $code_snippets->file )
			);
		} else {
			$menu_icon = 'div';
		}

		/* Add the top-level menu and associated subpage */
		$this->manage_page = add_menu_page(
			__( 'Snippets', 'code-snippets' ),
			__( 'Snippets', 'code-snippets' ),
			$code_snippets->get_cap(),
			$this->manage_slug,
			array( $this, 'display_manage_menu' ),
			$menu_icon,
			is_network_admin() ? 21 : 67
		);

		add_submenu_page(
			$this->manage_slug,
			__( 'Snippets', 'code-snippets' ),
			__( 'Manage', 'code-snippets' ),
			$code_snippets->get_cap(),
			$this->manage_slug,
			array( $this, 'display_manage_menu')
		);

		/* Add the Edit/Add New Snippet page */
		$editing = ( isset( $_REQUEST['page'], $_REQUEST['edit'] ) && $this->single_slug === $_REQUEST['page'] );

		$this->single_page = add_submenu_page(
			$this->manage_slug,
			$editing ? __( 'Edit Snippet', 'code-snippets' ) : __( 'Add New Snippet', 'code-snippets' ),
			$editing ? __( 'Edit', 'code-snippets' ) : __( 'Add New', 'code-snippets' ),
			$code_snippets->get_cap(),
			$this->single_slug,
			array( $this, 'display_single_menu' )
		);

		add_action( "load-$this->manage_page", array( $this, 'load_manage_menu' ) );
		add_action( "load-$this->single_page", array( $this, 'load_single_menu' ) );
	}

	/**
	 * Add an Import Snippets page to the network admin menu.
	 * We need to do this as there is no Tools menu in the network
	 * admin, and so we cannot register an importer
	 *
	 * @since  1.6
	 * @access private
	 * @uses   add_submenu_page() To register the menu page
	 * @uses   apply_filters()    To retrieve the current menu slug
	 * @uses   add_action()       To enqueue scripts and styles
	 * @return void
	 */
	function add_import_admin_menu() {
		global $code_snippets;

		$this->import_page = add_submenu_page(
			$this->manage_slug,
			__( 'Import Snippets', 'code-snippets' ),
			__( 'Import', 'code-snippets' ),
			$code_snippets->get_cap(),
			'import-code-snippets',
			array( $this, 'display_import_menu' )
		);

		$this->import_url = self_admin_url( 'admin.php?page=import-code-snippets' );
		add_action( "load-$this->import_page", array( $this, 'load_import_menu' ) );
	}

	/**
	 * Enqueue the icon stylesheet
	 *
	 * @since  1.0
	 * @access private
	 * @uses   wp_enqueue_style() To add the stylesheet to the queue
	 * @uses   get_user_option()  To check if MP6 mode is active
	 * @uses   plugins_url        To retrieve a URL to assets
	 * @return void
	 */
	function load_admin_icon_style() {
		global $code_snippets;

		$stylesheet = $this->is_legacy_interface() ? 'screen-icon' : 'menu-icon';

		wp_enqueue_style(
			'icon-snippets',
			plugins_url( "assets/css/{$stylesheet}.css", $code_snippets->file ),
			false,
			$code_snippets->version
		);
	}

	/**
	 * Initializes the list table class and loads the help tabs
	 * for the Manage Snippets page
	 *
	 * @since  1.0
	 * @access private
	 * @return void
	 */
	function load_manage_menu() {
		global $code_snippets;

		/* Make sure the user has permission to be here */
		$this->check_perms();

		/* Create the snippet tables if they don't exist */
		$code_snippets->maybe_create_tables( true, true );

		/* Load the screen help tabs */
		$this->load_help_tabs( 'manage' );

		/* Initialize the snippet table class */
		$code_snippets->get_include( 'class-list-table' );
		$code_snippets->list_table = new Code_Snippets_List_Table();
		$code_snippets->list_table->prepare_items();
	}

	/**
	 * Loads the help tabs for the Edit Snippets page
	 *
	 * @since  1.0
	 * @access private
	 * @return void
	 *
	 * @uses   $wpdb       To save the posted snippet to the database
	 * @uses   wp_redirect To pass the results to the page
	 */
	function load_single_menu() {
		global $code_snippets;
		$screen = get_current_screen();

		/* Make sure the user has permission to be here */
		$this->check_perms();

		/* Create the snippet tables if they don't exist */
		$code_snippets->maybe_create_tables( true, true );

		/* Load the screen help tabs */
		$this->load_help_tabs( 'single' );

		/* Enqueue the code editor and other scripts and styles */
		add_filter( 'admin_enqueue_scripts', array( $this, 'single_menu_enqueue_scripts' ) );

		/* Make sure the nonce validates before we do any snippet ops */
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'save_snippet' ) ) {
			return;
		}

		/* Save the snippet if one has been submitted */
		if ( isset( $_POST['save_snippet'] ) || isset( $_POST['save_snippet_activate'] ) || isset( $_POST['save_snippet_deactivate'] ) ) {

			/* Activate or deactivate the snippet before saving if we clicked the button */
			if ( isset( $_POST['save_snippet_activate'] ) ) {
				$_POST['snippet_active'] = 1;
			} elseif ( isset( $_POST['save_snippet_deactivate'] ) ) {
				$_POST['snippet_active'] = 0;
			}

			/* Save the snippet to the database */
			$result = $code_snippets->save_snippet( stripslashes_deep( $_POST ) );

			/* Strip old status query vars from URL */
			$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'added', 'updated', 'activated', 'deactivated', 'invalid' ) );

			/* Build the status message and redirect */

			if ( $result && isset( $_POST['save_snippet_activate'] ) ) {
				/* Snippet was activated addition to saving*/
				$_SERVER['REQUEST_URI'] = add_query_arg( 'activated', true );
			}
			elseif ( $result && isset( $_POST['save_snippet_deactivate'] ) ) {
				/* Snippet was deactivated addition to saving*/
				$_SERVER['REQUEST_URI'] = add_query_arg( 'deactivated', true );
			}

			if ( ! $result || $result < 1 ) {
				/* An error occurred */
				wp_redirect( add_query_arg( 'invalid', true ) );
			}
			elseif ( isset( $_POST['snippet_id'] ) ) {
				/* Existing snippet was updated */
				wp_redirect( add_query_arg(	array( 'edit' => $result, 'updated' => true ) ) );
			}
			else {
				/* New snippet was added */
				wp_redirect( add_query_arg( array( 'edit' => $result, 'added' => true ) ) );
			}
		}

		/* Delete the snippet if the button was clicked */
		elseif ( isset( $_POST['snippet_id'], $_POST['delete_snippet'] ) ) {
			$code_snippets->delete_snippet( $_POST['snippet_id'] );
			wp_redirect( add_query_arg( 'delete', true, $this->manage_url ) );
		}

		/* Export the snippet if the button was clicked */
		elseif ( isset( $_POST['snippet_id'], $_POST['export_snippet'] ) ) {
			$code_snippets->export( $_POST['snippet_id'] );
		}
	}

	/**
	 * Registers and loads the code editor's scripts
	 *
	 * @since  1.7
	 * @access private
	 *
	 * @uses   wp_register_script()
	 * @uses   wp_register_style()
	 * @uses   wp_enqueue_script() To add the scripts to the queue
	 * @uses   wp_enqueue_style()  To add the stylesheets to the queue
	 *
	 * @param  string $hook        The current page hook, to be compared with the single snippet page hook
	 * @return void
	 */
	function single_menu_enqueue_scripts( $hook ) {
		global $code_snippets;

		/* If we're not on the right admin page, bail early */
		if ( $hook !== $this->single_page )
			return;

		/* Remove other CodeMirror styles */
		wp_deregister_style( 'codemirror' );
		wp_deregister_style( 'wpeditor' );

		/* CodeMirror */

		$codemirror_version = '3.20.0';
		$codemirror_url     = plugins_url( 'vendor/codemirror/', $code_snippets->file );

		wp_enqueue_style(
			'code-snippets-codemirror',
			$codemirror_url . 'lib/codemirror.css',
			false,
			$codemirror_version
		);

		wp_enqueue_script(
			'code-snippets-codemirror',
			$codemirror_url . 'lib/codemirror.js',
			false,
			$codemirror_version
		);

		/* CodeMirror Modes */

		wp_enqueue_script(
			'code-snippets-codemirror-mode-clike',
			$codemirror_url . 'mode/clike/clike.js',
			array( 'code-snippets-codemirror' ),
			$codemirror_version
		);

		wp_enqueue_script(
			'code-snippets-codemirror-mode-php',
			$codemirror_url . 'mode/php/php.js',
			array( 'code-snippets-codemirror', 'code-snippets-codemirror-mode-clike' ),
			$codemirror_version
		);


		/* CodeMirror Addons */

		wp_enqueue_script(
			'code-snippets-codemirror-addon-searchcursor',
			$codemirror_url . 'addon/search/searchcursor.js',
			array( 'code-snippets-codemirror' ),
			$codemirror_version
		);

		wp_enqueue_script(
			'code-snippets-codemirror-addon-search',
			$codemirror_url . 'addon/search/search.js',
			array( 'code-snippets-codemirror', 'code-snippets-codemirror-addon-searchcursor' ),
			$codemirror_version
		);

		wp_enqueue_script(
			'code-snippets-codemirror-addon-matchbrackets',
			$codemirror_url . 'addon/edit/matchbrackets.js',
			array( 'code-snippets-codemirror' ),
			$codemirror_version
		);

		/* Plugin Assets */

		wp_enqueue_style(
			'code-snippets-admin-single',
			plugins_url( 'assets/css/admin-single.css', $code_snippets->file ),
			false,
			$code_snippets->version
		);

		wp_enqueue_script(
			'code-snippets-admin-single',
			plugins_url( 'assets/js/admin-single.js', $code_snippets->file ),
			array( 'code-snippets-codemirror' ),
			$code_snippets->version,
			true // Load in footer
		);
	}

	/**
	 * Processes import files and loads the help tabs for the Import Snippets page
	 *
	 * @since  1.3
	 *
	 * @uses   $code_snippets->import() To process the import file
	 * @uses   wp_redirect()            To pass the import results to the page
	 * @uses   add_query_arg()          To append the results to the current URI
	 * @uses   $this->load_help_tabs()  To load the screen contextual help tabs
	 *
	 * @param  string $file             A filesystem path to the import file
	 * @return void
	 */
	function load_import_menu() {
		global $code_snippets;

		/* Make sure the user has permission to be here */
		$this->check_perms();

		/* Create the snippet tables if they don't exist */
		$code_snippets->maybe_create_tables( true, true );

		/* Process import files */

		if ( isset( $_FILES['code_snippets_import_file']['tmp_name'] ) ) {

			/* Import the snippets. The result is the number of snippets that were imported */
			$result = $code_snippets->import( $_FILES['code_snippets_import_file']['tmp_name'] );

			/* Send the amount of imported snippets to the page */
			if ( false === $result ) {
				wp_redirect( add_query_arg( 'error', true ) );
			} else {
				wp_redirect( add_query_arg( 'imported', $result ) );
			}
		}

		/* Load the screen help tabs */
		$this->load_help_tabs( 'import' );
	}

	/**
	 * Displays the manage snippets page
	 *
	 * @since  1.0
	 * @access private
	 * @uses   $this->get_view() To load an admin view template
	 * @return void
	 */
	function display_manage_menu() {
		$this->get_view( 'manage' );
	}

	/**
	 * Displays the single snippet page
	 *
	 * @since  1.0
	 * @access private
	 * @uses   $this->get_view() To load an admin view template
	 * @return void
	 */
	function display_single_menu() {
		$this->get_view( 'single' );
	}

	/**
	 * Displays the import snippets page
	 *
	 * @since  1.3
	 * @access private
	 * @uses   $this->get_view() To load an admin view template
	 * @return void
	 */
	function display_import_menu() {
		$this->get_view( 'import' );
	}

	/**
	 * Add a description editor to the single snippet page
	 *
	 * @since  1.7
	 * @access private
	 * @param  object $snippet The snippet being used for this page
	 * @return void
	 */
	function description_editor_box( $snippet ) {

		?>

		<label for="snippet_description">
			<h3><div><?php _e( 'Description', 'code-snippets' ); ?></div></h3>
		</label>

		<?php

		remove_editor_styles(); // stop custom theme styling interfering with the editor

		wp_editor(
			$snippet->description,
			'description',
			apply_filters( 'code_snippets/admin/description_editor_settings', array(
				'textarea_name' => 'snippet_description',
				'textarea_rows' => 10,
				'teeny' => true,
				'media_buttons' => false,
			) )
		);
	}

	/**
	 * Adds a link pointing to the Manage Snippets page
	 *
	 * @since  1.0
	 * @access private
	 * @param  array $links The existing plugin action links
	 * @return array        The modified plugin action links
	 */
	function settings_link( $links ) {
		array_unshift( $links, sprintf(
			'<a href="%1$s" title="%2$s">%3$s</a>',
			$this->manage_url,
			__( 'Manage your existing snippets', 'code-snippets' ),
			__( 'Manage', 'code-snippets' )
		) );
		return $links;
	}

	/**
	 * Adds extra links related to the plugin
	 *
	 * @since  1.2
	 * @access private
	 * @param  array  $links The existing plugin info links
	 * @param  string $file  The plugin the links are for
	 * @return array         The modified plugin info links
	 */
	function plugin_meta( $links, $file ) {
		global $code_snippets;

		/* We only want to affect the Code Snippets plugin listing */
		if ( $file !== $code_snippets->basename )
			return $links;

		$format = '<a href="%1$s" title="%2$s">%3$s</a>';

		/* array_merge appends the links to the end */
		return array_merge( $links, array(
			sprintf( $format,
				'http://wordpress.org/plugins/code-snippets/',
				__( 'Visit the WordPress.org plugin page', 'code-snippets' ),
				__( 'About', 'code-snippets' )
			),
			sprintf( $format,
				'http://wordpress.org/support/plugin/code-snippets/',
				__( 'Visit the support forums', 'code-snippets' ),
				__( 'Support', 'code-snippets' )
			),
			sprintf( $format,
				'http://code-snippets.bungeshea.com/donate/',
				__("Support this plugin's development", 'code-snippets' ),
				__( 'Donate', 'code-snippets' )
			)
		) );
	}

	/**
	 * Print a notice inviting people to participate in the Code Snippets Survey
	 *
	 * @since  1.9
	 * @return void
	 */
	function survey_message() {
		global $current_user;

		$key = 'ignore_code_snippets_survey_message';

		/* Bail now if the user has dismissed the message */
		if ( get_user_meta( $current_user->ID, $key ) ) {
			return;
		}
		elseif ( isset( $_GET[ $key ], $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $key ) ) {
			add_user_meta( $current_user->ID, $key, true, true );
			return;
		}

		?>

		<br />

		<div class="updated"><p>

		<?php _e( "<strong>Have feedback on Code Snippets?</strong> Please take the time to answer a short survey on how you use this plugin and what you'd like to see changed or added in the future.", 'code-snippets' ); ?>

		<a href="http://code-snippets.bungeshea.com/survey/" class="button secondary" target="_blank" style="margin: auto .5em;">
			<?php _e( 'Take the survey now', 'code-snippets' ); ?>
		</a>

		<a href="<?php echo wp_nonce_url( add_query_arg( $key, true ), $key ); ?>">Dismiss</a>

		</p></div>

		<?php
	}

} // end of class
