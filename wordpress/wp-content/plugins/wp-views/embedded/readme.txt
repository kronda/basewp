The Embedded version lets you display Content Templates and Views in your site, without requiring any extra plugin.

= Instructions =

1. Install like any other plugin into WordPress

2. Export your configuration from your development site. 
Go to the Views->Import/Export menu and click on the 'Export' button. 
You will receive a ZIP file with the XML and PHP configuration files (both are required).
Unzip that file and place both settings.xml and the setting.php into the root directory of this plugin.

You're done!

= Changelog =

v. 1.8 aka R2-D2

	- Added a complete GUI for the Views Embedded plugin.
	
	- Added a new API function is_wpv_content_template_assigned.
	
	- Added a new API function is_wpv_wp_archive_assigned.
	
	- Improved the import workflow.
	
	- Improved the compatibility with WordPress 4.1 related to meta_query entries for custom fields and sorting by meta values.
	
	- Improved the compatibility with WordPress 4.2 related to term splitting.
	
	- Improved the compatibility with WordPress 4.2 related to cache objects for taxonomies.
	
	- Improved the compatibility with WordPress 4.2 related to accessing the global $wp_filter.
	
	- Changed the Google Maps script register handler for better third-party compatibility.
	
	- Fixed an issue about filtering by custom field values containing a plus sign.
	
	- Fixed an issue about filtering by Types checkboxes fields - extended support for complex queries in WordPress 4.1+.
	
	- Fixed an issue about multiselect items on a parametric search - avoid force selecting the first option by default.
	
	- Cleaned some deprecated code.

v. 1.7 aka Zaphod Beeblebrox

v. 1.6.2

	- First release as standalone plugin.