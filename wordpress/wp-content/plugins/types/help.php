<?php
/*
 * Plugin contextual help
 *
 *
 */

/**
 * Returns contextual help.
 *
 * @param string $page
 * @param $contextual_help
 *
 * @return string
 */
function wpcf_admin_help($page, $contextual_help)
{
    $help = '';
    switch ($page) {
        // Post Fields (list)
        case 'custom_fields':
            $help.= ''
                .__("Types plugin organizes post fields in groups. Once you create a group, you can add the fields to it and control to what content it belongs.", 'wpcf')
                .PHP_EOL
                .PHP_EOL
                .sprintf(
                    __('You can read more about Post Fields in this tutorial: %s.', 'wpcf'),
                    '<a href="http://wp-types.com/user-guides/using-custom-fields/" target="_blank">http://wp-types.com/user-guides/using-custom-fields/ &raquo;</a>'
                )
                .PHP_EOL
                .PHP_EOL
                .__("On this page you can see your current post field groups, as well as information about which post types and taxonomies they are attached to, and whether they are active or not.", 'wpcf')
                .PHP_EOL
                .PHP_EOL
                .sprintf('<h3>%s</h3>', __('You have the following options:', 'wpcf'))
                .'<dl>'
                .'<dt>'.__('Add New', 'wpcf').'</dt>'
                .'<dd>'.__('Use this to add a new post fields group which can be attached to a post type', 'wpcf').'</dd>'
                .'<dt>'.__('Edit', 'wpcf').'</dt>'
                .'<dd>'.__('Click to edit the post field group', 'wpcf').'</dd>'
                .'<dt>'.__('Activate', 'wpcf').'</dt>'
                .'<dd>'.__('Click to activate a post field group', 'wpcf').'</dd>'
                .'<dt>'.__('Deactivate', 'wpcf').'</dt>'
                .'<dd>'.__('Click to deactivate a post field group (this can be re-activated at a later date)', 'wpcf').'</dd>'
                .'<dt>'.__('Delete', 'wpcf').'</dt>'
                .'<dd>'.__('Click to delete a post field group.', 'wpcf')
                .' '
                .sprintf('<strong>%s</strong>', __('Warning: This cannot be undone.', 'wpcf'))
                .'</dd>'
                .'</dl>'
                ;
            break;

        case 'need-more-help':

			// Post fields
            $help .= sprintf('<h4>%s</h4>', __('Post Fields', 'wpcf'));
            $help .= '<ul>';
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/using-custom-fields/#1?utm_source=typesplugin&utm_medium=help&utm_term=adding-fields&utm_campaign=types">%s &raquo;</a></li>',
                __('Adding post fields to content', 'wpcf')
            );
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/displaying-wordpress-custom-fields/?utm_source=typesplugin&utm_medium=help&utm_term=displaying-fields&utm_campaign=types">%s &raquo;</a></li>',
                __('Displaying post fields on front-end', 'wpcf')
            );
            $help .= '</ul>';

			// User fields
            $help .= sprintf('<h4>%s</h4>', __('User Fields', 'wpcf'));
            $help .= '<ul>';
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/user-fields/?utm_source=typesplugin&utm_medium=help&utm_term=adding-user-fields&utm_campaign=types">%s &raquo;</a></li>',
                __('Adding user fields to user profiles', 'wpcf')
            );
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/displaying-wordpress-user-fields/?utm_source=typesplugin&utm_medium=help&utm_term=displaying-user-fields&utm_campaign=types">%s &raquo;</a></li>',
                __('Displaying user fields on front-end', 'wpcf')
            );
            $help .= '</ul>';

	        // Term fields
	        $help .= sprintf(
		        '<h4>%s</h4>
				<ul>
					<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/term-fields/?utm_source=typesplugin&utm_medium=help&utm_term=adding-term-fields&utm_campaign=types">%s &raquo;</a></li>
					<li><a target="_blank" href="https://wp-types.com/documentation/user-guides/displaying-wordpress-term-fields/?utm_source=typesplugin&utm_medium=help&utm_term=displaying-term-fields&utm_campaign=types">%s &raquo;</a></li>
				</ul>',
		        __( 'Term Fields', 'wpcf' ),
		        __( 'Adding term fields to taxonomies', 'wpcf' ),
		        __( 'Displaying term fields on front-end', 'wpcf' )
	        );

            $help .= sprintf('<h4>%s</h4>', __('Post Types and Taxonomy', 'wpcf'));
            $help .= '<ul>';
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/create-a-custom-post-type/?utm_source=typesplugin&utm_medium=help&utm_term=custom-post-types&utm_campaign=types">%s &raquo;</a></li>',
                __('Creating and using post types', 'wpcf')
            );
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/create-custom-taxonomies/?utm_source=typesplugin&utm_medium=help&utm_term=custom-taxonomy&utm_campaign=types">%s &raquo;</a></li>',
                __('Arranging content with Taxonomy', 'wpcf')
            );
            $help .= sprintf(
                '<li><a target="_blank" href="http://wp-types.com/documentation/user-guides/creating-post-type-relationships/?utm_source=typesplugin&utm_medium=help&utm_term=post-relationship&utm_campaign=types">%s &raquo;</a></li>',
                __('Creating parent / child relationships', 'wpcf')
            );
            $help .= '</ul>';



            break;

        case 'custom_taxonomies_list':
            $help .= ''
                . __('This is the your Taxonomies list. It provides you with an overview of your data.', 'wpcf')
                .PHP_EOL
                .PHP_EOL
                .sprintf(
                    __('You can read more about Post Types and Taxonomies in this tutorial. %s', 'wpcf'),
                    '<a href="https://wp-types.com/user-guides/create-a-custom-post-type/" target="_blank">https://wp-types.com/user-guides/create-a-custom-post-type/ &raquo;</a>'
                )
                .PHP_EOL
                .PHP_EOL
                .sprintf('<h3>%s</h3>', __('On this page you have the following options:', 'wpcf'))
                .'<dl>'
                .'<dt>'.__('Add New', 'wpcf')
                .'<dd>'.__('Use to create a new Taxonomy', 'wpcf')
                .'<dt>'.__('Edit', 'wpcf')
                .'<dd>'.__('Click to edit the settings of a Taxonomy', 'wpcf').'</dd>'
                .'<dt>'.__('Deactivate', 'wpcf')
                .'<dd>'.__('Click to deactivate a Taxonomy (this can be reactivated at a later date)', 'wpcf').'</dd>'
                .'<dt>'.__('Duplicate', 'wpcf')
                .'<dd>'.__('Click to duplicate a Taxonomy', 'wpcf').'</dd>'
                .'<dt>'.__('Delete', 'wpcf')
                .'<dd>'.__('Click to delete a Taxonomy.', 'wpcf')
                .' '
                .sprintf('<strong>%s</strong>', __('Warning: This cannot be undone.', 'wpcf'))
                .'</dd>'
                .'</dl>';
            break;

        case 'post_types_list':
            $help .= ''
                . __('This is the main admin page for built-in Post Types and your Post Types. It provides you with an overview of your data.', 'wpcf')
               .PHP_EOL
               .PHP_EOL
               .__('Post Types are built-in and user-defined content types.', 'wpcf')
               .PHP_EOL
               .PHP_EOL
               .sprintf(
                    __('You can read more about Post Types and Taxonomies in this tutorial. %s', 'wpcf'),
                    '<a href="https://wp-types.com/user-guides/create-a-custom-post-type/" target="_blank">https://wp-types.com/user-guides/create-a-custom-post-type/ &raquo;</a>'
                )
                .PHP_EOL
                .PHP_EOL
                .sprintf('<h3>%s</h3>', __('On this page you have the following options:', 'wpcf'))
                .'<dl>'
                .'<dt>'.__('Add New', 'wpcf').'</dt>'
                .'<dd>'.__('Use to create a new Post Type', 'wpcf').'</dd>'
                .'<dt>'.__('Edit', 'wpcf').'</dt>'
                .'<dd>'.__('Click to edit the settings of a Post Type', 'wpcf').'</dd>'
                .'<dt>'.__('Deactivate', 'wpcf').'</dt>'
                .'<dd>'.__('Click to deactivate a Post Type (this can be reactivated at a later date)', 'wpcf').'</dd>'
                .'<dt>'.__('Duplicate', 'wpcf')
                .'<dd>'.__('Click to duplicate a Post Type', 'wpcf').'</dd>'
                .'<dt>'.__('Delete', 'wpcf').'</dt>'
                .'<dd>'.__('Click to delete a Post Type.', 'wpcf')
                .' '
                .sprintf('<strong>%s</strong>', __('Warning: This cannot be undone.', 'wpcf'))
                .'</dd>'
                .'</dl>'
                ;
            break;

        // Import/Export page
        case 'import_export':
            $help .= 
                __('Use this page to import and export post types, taxonomies and post fields to and from Types.', 'wpcf')
                    . PHP_EOL
                    . PHP_EOL
                    . __('On this page you have the following options:', 'wpcf')

                    . '<h3>' . __('Import Types data file', 'wpcf') . '</h3>'
                    . '<dl>'
                    .'<dt>' .__('Step 1:', 'wpcf') .'</dt>'
                    .'<dd>' .__('Choose and upload an XML file.', 'wpcf') .'</dd>'
                    .'<dt>' .__('Step 2:', 'wpcf') .'</dt>'
                    .'<dd>' . __('Select which post types, taxonomies and post fields should be imported.', 'wpcf') .'</dd>'
                    .'</dl>'

                    .'<h3>' . __('Import Types data text input', 'wpcf') . '</h3>'
                    . '<dl>'
                    .'<dt>' .__('Step 1:', 'wpcf') .'</dt>'
                    .'<dd>' .__('Paste XML content directly into the text area.', 'wpcf') .'</dd>'
                    .'<dt>' .__('Step 2:', 'wpcf') .'</dt>'
                    .'<dd>' .__('Select which post types, taxonomies and post fields should be imported.', 'wpcf') .'</dd>'
                    .'</dl>'

                    .'<h3>' . __('Export', 'wpcf') . '</h3>'
                    .__('Click Export to export data from Types as an XML file.', 'wpcf')
            ;
            break;

        // Add/Edit group form page
        case 'edit_group':
            $help .= ''
                .__('This is the edit page for your Post Field Groups.', 'wpcf')
                 .PHP_EOL
                 .PHP_EOL
                .__('On this page you can create and edit your groups. To create a group, do the following:', 'wpcf')
                .'<ol style="list-style-type:decimal;"><li style="list-style-type:decimal;">'
                .__('Add a Title.', 'wpcf')
                .'</li><li style="list-style-type:decimal;">'
                .__('Choose where to display your group. You can attach this to both default WordPress post types and Post Types (you can also associate Taxonomy terms with Post Field Groups).', 'wpcf')
                .'</li><li style="list-style-type:decimal;">'
                .__('To add a field, click on "Add New Field" and choose the field you desire. This will be added to your Post Field Group.', 'wpcf')
                .'</li><li style="list-style-type:decimal;">'
                .__('Add information about your Post Field.', 'wpcf')
                .'</li></ol>'
                .'<h3>' .__('Tips', 'wpcf') .'</h3>'
                .'<ul><li>'
                .__('To ensure a user fills out a field, check Required in Validation section.', 'wpcf')
                .'</li><li>'
                .__('Once you have created a field, it will be saved for future use under "Choose from previously created fields" of "Add New Field" dialog.', 'wpcf')
                .'</li><li>'
                .__('You can drag and drop the order of your post fields.', 'wpcf')
                .'</li></ul>';
            break;

            // Add/Edit custom type form page
        case 'edit_type':
            $help .= ''
               .__('Use this page to create a WordPress post type. If you’d like to learn more about Post Types you can read our detailed guide: <a href="https://wp-types.com/user-guides/create-a-custom-post-type/" target="_blank">https://wp-types.com/user-guides/create-a-custom-post-type/</a> or check out our tutorial on creating them with Types: <a href="http://wp-types.com/user-guides/create-a-custom-post-type/" target="_blank">http://wp-types.com/user-guides/create-a-custom-post-type/ &raquo;</a>', 'wpcf')
               .PHP_EOL
               .PHP_EOL
               .'<dt>'.__('Name and Description', 'wpcf').'</dt>'
               .'<dd>'.__('Add a singular and plural name for your post type. You should also add a slug. This will be created from the post type name if none is added.', 'wpcf').'</dd>'
               .'<dt>'.__('Visibility', 'wpcf').'</dt>'
               .'<dd>'.__('Determine whether your post type will be visible on the admin menu to your users.', 'wpcf').'</dd>'
               .'<dd>'.__('You can also adjust the menu position. The default position is 20, which means your post type will appear under “Pages”. You can find more information about menu positioning in the WordPress Codex. <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Parameters" target="_blank">http://codex.wordpress.org/Function_Reference/register_post_type#Parameters</a>', 'wpcf').'</dd>'
               .'<dd>'.__('The default post type icon is the pushpin icon that appears beside WordPress posts. You can change this by adding your own icon of 16px x 16px.', 'wpcf').'</dd>'
               .'<dt>'.__('Select Taxonomies', 'wpcf').'</dt>'
               .'<dd>'.__('Choose which taxonomies are to be associated with this post type.', 'wpcf').'</dd>'
               .'<dt>'.__('Labels', 'wpcf').'</dt>'
               .'<dd>'.__('Labels are the text that is attached to your post type name. Examples of them in use are “Add New Post” (where “Add New” is the label”) and “Edit Post” (where “Edit” is the label). In normal circumstances the defaults will suffice.', 'wpcf').'</dd>'
               .'<dt>'.__('Custom Post Properites', 'wpcf').'</dt>'
               .'<dd>'.__('Choose which sections to display on your “Add New” page.', 'wpcf').'</dd>'
               .'<dt>'.__('Advanced Settings', 'wpcf').'</dt>'
               .'<dd>'.__('Advanced settings give you even more control over your post type. You can read in detail what all of these settings do on our tutorial.', 'wpcf').'</dd>'
                .'</dl>'
                ;
            break;

        // Add/Edit Taxonomy form page
        case 'edit_tax':
            $help .= ''
                .__('You can use Taxonomies to categorize your content. Read more about what they are on our website: <a href="https://wp-types.com/user-guides/create-a-custom-post-type/" target="_blank">https://wp-types.com/user-guides/create-a-custom-post-type/ &raquo;</a> or you can read our guide about how to set them up: <a href="http://wp-types.com/user-guides/create-custom-taxonomies/" target="_blank">http://wp-types.com/user-guides/create-custom-taxonomies/</a>', 'wpcf')
                .'<dl>'
                .'<dt>'.__('Name and Description', 'wpcf') .'</dt>'
                .'<dd>'.__('Add a singular and plural name for your Taxonomy. You should also add a slug. This will be created from the Taxonomy name if none is added.', 'wpcf').'</dd>'
                .'<dt>'.__('Visibility', 'wpcf') .'</dt>'
                .'<dd>'.__('Determine whether your Taxonomy will be visible on the admin menu to your users.', 'wpcf').'</dd>'
                .'<dt>'.__('Select Post Types', 'wpcf') .'</dt>'
                .'<dd>'.__('Choose which Post Types this Taxonomy should be associated with.', 'wpcf').'</dd>'
                .'<dt>'.__('Labels', 'wpcf') .'</dt>'
                .'<dd>'.__('Labels are the text that is attached to your Taxonomy name. Examples of them in use are “Add New Taxonomy” (where “Add New” is the label”) and “Edit Taxonomy” (where “Edit” is the label). In normal circumstances the defaults will suffice.', 'wpcf').'</dd>'
                .'<dt>'.__('Options', 'wpcf') .'</dt>'
                .'<dd>'.__('Advanced settings give you even more control over your Taxonomy. You can read in detail what all of these settings do on our tutorial.', 'wpcf').'</dd>'
                .'</dl>'
                ;
            break;

        case 'user_fields_list':
            $help .= ''
                .__("Types plugin organizes User Fields in groups. Once you create a group, you can add the fields to it and control to what content it belongs.", 'wpcf')
                .PHP_EOL
                .PHP_EOL
                .__("On this page you can see your current User Fields groups, as well as information about which user role they are attached to, and whether they are active or not.", 'wpcf')
                . sprintf('<h3>%s</h3>', __('You have the following options:', 'wpcf'))
                .'<dl>'
                .'<dt>'.__('Add New', 'wpcf').'</dt>'
                .'<dd>'.__('Use this to add a new User Field Group', 'wpcf').'</dd>'
                .'<dt>'.__('Edit', 'wpcf').'</dt>'
                .'<dd>'.__('Click to edit the User Field Group', 'wpcf').'</dd>'
                .'<dt>'.__('Activate', 'wpcf').'</dt>'
                .'<dd>'.__('Click to activate a User Field Group', 'wpcf').'</dd>'
                .'<dt>'.__('Deactivate', 'wpcf').'</dt>'
                .'<dd>'.__('Click to deactivate a User Field Group (this can be re-activated at a later date)', 'wpcf').'</dd>'
                .'<dt>'.__('Delete', 'wpcf').'</dt>'
                .'<dd>'.__('Click to delete a User Field Group.', 'wpcf')
                .' '
                .sprintf('<strong>%s</strong>', __('Warning: This cannot be undone.', 'wpcf'))
                .'</dd>'
                .'</dl>'
                ;
            break;

        case 'user_fields_edit':
            $help .= ''
                .__('This is the edit page for your User Field Groups.', 'wpcf')
                .PHP_EOL
                .PHP_EOL
                . __('On this page you can create and edit your groups. To create a group, do the following:', 'wpcf')
                .'<ol><li>'
                . __('Add a Title', 'wpcf')
                .'</li><li>'
                . __('Choose where to display your group. You can attach this to both default WordPress user roles and custom roles.', 'wpcf')
                .'</li><li>'
                . __('To add a field, click on "Add New Field" and choose the field you desire. This will be added to your User Field Group.', 'wpcf')
                .'</li><li>'
                . __('Add information about your User Field.', 'wpcf')
                .'</li></ol>'
                .'<h3>' . __('Tips', 'wpcf') .'</h3>'
                .'<ul><li>'
                . __('To ensure a user fills out a field, check Required in Validation section.', 'wpcf')
                .'</li><li>'
                . __('Once you have created a field, it will be saved for future use under "Choose from previously created fields" of "Add New Field" dialog.', 'wpcf')
                .'</li><li>'
                . __('You can drag and drop the order of your user fields.', 'wpcf')
                .'</li></ul>';
            break;


	    case 'edit_termmeta':
		    $help .= ''
			    .__('This is the edit page for your Term Field Groups.', 'wpcf')
			    .PHP_EOL
			    .PHP_EOL
			    . __('On this page you can create and edit your groups. To create a group, do the following:', 'wpcf')
			    .'<ol><li>'
			    . __('Add a Title.', 'wpcf')
			    .'</li><li>'
			    . __('Choose where to display your group. You can attach this to any taxonomy.', 'wpcf')
			    .'</li><li>'
			    . __('To add a field, click on "Add New Field" and choose the field you desire. This will be added to your Term Field Group.', 'wpcf')
			    .'</li><li>'
			    . __('Add information about your Term Field.', 'wpcf')
			    .'</li></ol>'
			    .'<h3>' . __('Tips', 'wpcf') .'</h3>'
			    .'<ul><li>'
			    . __('To ensure a user fills out a field, check Required in Validation section.', 'wpcf')
			    .'</li><li>'
			    . __('Once you have created a field, it will be saved for future use under "Choose from previously created fields" of "Add New Field" dialog.', 'wpcf')
			    .'</li><li>'
			    . __('You can drag and drop the order of your term fields.', 'wpcf')
			    .'</li></ul>';
		    break;


	    case 'term_fields_list':
            $help .= ''
                     .__("Types plugin organizes Term Fields in groups. Once you create a group, you can add the fields to it and control to what content it belongs.", 'wpcf')
                     .PHP_EOL
                     .PHP_EOL
                     .__("On this page you can see your current Term Field groups, as well as information about which taxonomies they are attached to, and whether they are active or not.", 'wpcf')
                     . sprintf('<h3>%s</h3>', __('You have the following options:', 'wpcf'))
                     .'<dl>'
                     .'<dt>'.__('Add New', 'wpcf').'</dt>'
                     .'<dd>'.__('Use this to add a new Term Field Group', 'wpcf').'</dd>'
                     .'<dt>'.__('Edit', 'wpcf').'</dt>'
                     .'<dd>'.__('Click to edit the Term Field Group', 'wpcf').'</dd>'
                     .'<dt>'.__('Activate', 'wpcf').'</dt>'
                     .'<dd>'.__('Click to activate a Term Field Group', 'wpcf').'</dd>'
                     .'<dt>'.__('Deactivate', 'wpcf').'</dt>'
                     .'<dd>'.__('Click to deactivate a Term Field Group (this can be re-activated at a later date)', 'wpcf').'</dd>'
                     .'<dt>'.__('Delete', 'wpcf').'</dt>'
                     .'<dd>'.__('Click to delete a Term Field Group.', 'wpcf')
                     .' '
                     .sprintf('<strong>%s</strong>', __('Warning: This cannot be undone.', 'wpcf'))
                     .'</dd>'
                     .'</dl>'
            ;
            break;

    }

    return wpautop($help);
}

function wpcf_admin_help_add_tabs($call, $hook, $contextual_help = '')
{

    set_current_screen( $hook );
    $screen = get_current_screen();
    if ( is_null( $screen ) ) {
        return;
    }

    $title =  __( 'Types', 'wpcf' );

    switch($call) {

    case 'edit_type':
        $title =  __( 'Post Type', 'wpcf' );
        break;

    case 'post_types_list':
            $title =  __( 'Post Types', 'wpcf' );
            break;

    case 'custom_taxonomies_list':
        $title =  __( 'Taxonomies', 'wpcf' );
        break;

    case 'edit_tax':
        $title =  __( 'Taxonomy', 'wpcf' );
        break;

    case 'custom_fields':
        $title = __('Post Fields', 'wpcf');
        break;

    case 'edit_group':
        $title = __('Post Field Group', 'wpcf');
        break;

    case 'user_fields_list':
        $title = __('User Field Groups', 'wpcf');
        break;

    case 'user_fields_edit':
        $title = __('User Field Group', 'wpcf');
        break;

    case 'edit_termmeta':
        $title = __( 'Term Field Group', 'wpcf' );
        break;

    case 'term_fields_list':
        $title = __( 'Term Field Groups', 'wpcf' );
        break;

    case 'import_export':
        $title = __('Import/Export', 'wpcf');
        break;

    }

    $args = array(
        'title' => $title,
        'id' => 'wpcf',
        'content' => wpcf_admin_help( $call, $contextual_help),
        'callback' => false,
    );
    $screen->add_help_tab( $args );

    /**
     * Need Help section for a bit advertising
     */
    $args = array(
        'title' => __( 'Need More Help?', 'wpcf' ),
        'id' => 'custom_fields_group-need-help',
        'content' => wpcf_admin_help( 'need-more-help', $contextual_help ),
        'callback' => false,
    );
    $screen->add_help_tab( $args );

}
