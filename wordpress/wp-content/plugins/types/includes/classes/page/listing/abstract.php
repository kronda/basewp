<?php

/**
 * Represents an abstract listing page.
 */
abstract class WPCF_Page_Listing_Abstract extends WPCF_Page_Abstract {

	protected function __construct() {
		add_filter( 'wpcf_admin_menu_get_subpages', array( $this, 'add_submenu' ) );
	}


	/**
	 * Add submenu for the page.
	 *
	 * Hooked into the wpcf_admin_menu_get_subpages filter.
	 *
	 * @param array $submenus
	 * @return array
	 */
	public abstract function add_submenu( $submenus );


	/**
	 * @return string Page slug.
	 */
	protected abstract function get_page_name();


	/**
	 * Render the page.
	 *
	 * @return void
	 */
	public abstract function page_handler();


	/**
	 * Temporary hack. Insert the submenu item directly after the "User Fields" item.
	 *
	 * See the wpcf_admin_menu_get_subpages filter for description of parameters.
	 *
	 * @param array[] $all_submenus
	 * @param array $submenu_to_add
	 *
	 * @return array
	 */
	protected function add_submenu_at_the_end( $all_submenus, $submenu_to_add ) {

		$usermeta_index = array_search( 'wpcf-um', array_keys( $all_submenus ) );
		$temp = array_slice( $all_submenus, 0, $usermeta_index + 1 );
		$temp[ $this->get_page_name() ] = $submenu_to_add;
		$all_submenus = array_merge( $temp, array_slice( $all_submenus, $usermeta_index + 1, count( $all_submenus ) ) );

		return $all_submenus;
	}

}