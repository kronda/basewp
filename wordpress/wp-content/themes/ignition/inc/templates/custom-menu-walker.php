<?php

class thrive_custom_menu_walker extends Walker_Nav_Menu {

    //start of the sub menu wrap
    function start_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '<ul class="sub-menu">';
    }

    //end of the sub menu wrap
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '</ul>';
    }

    //add the description to the menu item output
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $wp_query;

        if (!is_object($args)) {
            $args = (object)$args;
        }

        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        if ($depth == 0 && $item->highlight_menu == 'on') {
            $class_names .= ' h-cta ';
        }
        if ($depth == 0 && $item->extended_activate == 'on') {
            $class_names .= ' has-extended col-no-' . $item->extended_columns;
            if ($item->extended_heading == 'on') {
                $class_names .= ' has-heading ';
            }
        } else if ($depth == 2 && $item->extended_text_chk == 'on') {
            $class_names .= ' has-free ';
        }
        $class_names = ' class="' . esc_attr($class_names) . '"';
        $output .= $indent . '<li ' . ' id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';


        $attributes = ($depth == 1) ? ' class="colch" ' : '';
        $attributes .=!empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        if ($item->extended_disable_link != 'on') {
            $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        }

        $item_output = $args->before;
        if ($depth == 2 && $item->extended_text_chk == 'on') {
            $item_output .= strip_tags($item->extended_free_text, '<p><br><strong><em><img><a>');
        } else {
            $item_output .= '<a ' . $attributes . '>';

            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;

            $item_output .= '</a>';
        }
        $item_output .= $args->after;


        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }


}

function thrive_function_admin_custom_menu_walker($walker, $menu_id) {
    return 'thrive_admin_custom_menu_walker';
}

class thrive_admin_custom_menu_walker extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker_Nav_Menu::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    function start_lvl(&$output, $depth = 0, $args = array()) {

    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker_Nav_Menu::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    function end_lvl(&$output, $depth = 0, $args = array()) {

    }

    /**
     * Start the element output.
     *
     * @see Walker_Nav_Menu::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     * @param int    $id     Not used.
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {

        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr($item->ID);
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ('taxonomy' == $item->type) {
            $original_title = get_term_field('name', $item->object_id, $item->object, 'raw');
            if (is_wp_error($original_title))
                $original_title = false;
        } elseif ('post_type' == $item->type) {
            $original_object = get_post($item->object_id);
            $original_title = get_the_title($original_object->ID);
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr($item->object),
            'menu-item-edit-' . ( ( isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if (!empty($item->_invalid)) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf(__('%s (Invalid)', 'thrive'), $item->title);
        } elseif (isset($item->post_status) && 'draft' == $item->post_status) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf(__('%s (Pending)', 'thrive'), $item->title);
        }

        $title = (!isset($item->label) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if (0 == $depth)
            $submenu_text = 'style="display: none;"';
        ?>
        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                <span class="item-title"><span class="menu-item-title"><?php echo esc_html($title); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e('sub item', 'thrive'); ?></span></span>
                <span class="item-controls">
                    <span class="item-type"><?php echo esc_html($item->type_label); ?></span>
                    <span class="item-order hide-if-js">
                        <a href="<?php
                        echo wp_nonce_url(
                                add_query_arg(
                                        array(
                            'action' => 'move-up-menu-item',
                            'menu-item' => $item_id,
                                        ), remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ), 'move-menu_item'
                        );
                        ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up', 'thrive'); ?>">&#8593;</abbr></a>
                        |
                        <a href="<?php
                        echo wp_nonce_url(
                                add_query_arg(
                                        array(
                            'action' => 'move-down-menu-item',
                            'menu-item' => $item_id,
                                        ), remove_query_arg($removed_args, admin_url('nav-menus.php'))
                                ), 'move-menu_item'
                        );
                        ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down', 'thrive'); ?>">&#8595;</abbr></a>
                    </span>
                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item', 'thrive'); ?>" href="<?php
                    echo ( isset($_GET['edit-menu-item']) && $item_id == $_GET['edit-menu-item'] ) ? admin_url('nav-menus.php') : add_query_arg('edit-menu-item', $item_id, remove_query_arg($removed_args, admin_url('nav-menus.php#menu-item-settings-' . $item_id)));
                    ?>"><?php _e('Edit Menu Item', 'thrive'); ?></a>
                </span>
                </dt>
            </dl>

            <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
                <?php if ('custom' == $item->type) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                            <?php _e('URL', 'thrive'); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->url); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-thin">
                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                        <?php _e('Navigation Label', 'thrive'); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->title); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                        <?php _e('Title Attribute', 'thrive'); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->post_excerpt); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked($item->target, '_blank'); ?> />
                        <?php _e('Open link in a new window/tab', 'thrive'); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                        <?php _e('CSS Classes (optional)', 'thrive'); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr(implode(' ', $item->classes)); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                        <?php _e('Link Relationship (XFN)', 'thrive'); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->xfn); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                        <?php _e('Description', 'thrive'); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html($item->description); // textarea_escaped                         ?></textarea>
                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'thrive'); ?></span>
                    </label>
                </p>

                <!-- Custom fields for the menu -->
                <?php $theme_options = get_option('thrive_theme_options'); ?>
                <?php if ((isset($theme_options['extended_menu']) && $theme_options['extended_menu'] == 'on') || (!isset($theme_options['extended_menu']))) : ?>
                    <p class="description description-wide depth-0-extended menu-item-extended-activate">
                        <label for="menu-item-extended-activate-<?php echo $item_id; ?>">
                            <input type="checkbox" id="menu-item-extended-activate-<?php echo $item_id; ?>" value="on" name="menu-item-extended-activate[<?php echo $item_id; ?>]" <?php checked($item->extended_activate, 'on'); ?>/>
                            <?php _e('Activate Extended Menu', 'thrive'); ?>
                        </label>
                    </p>
                <?php endif; ?>

                <p class="description description-wide depth-0-extended highlight-menu-item">
                    <label for="menu-item-highlight-menu-<?php echo $item_id; ?>">
                        <input type="checkbox" id="menu-item-highlight-menu-<?php echo $item_id; ?>" value="on" name="menu-item-highlight-menu-item[<?php echo $item_id; ?>]" <?php checked($item->highlight_menu, 'on'); ?>/>
                        <?php _e('Highlight Menu Item', 'thrive'); ?>
                    </label>

                    <a href="#"><?php _e("What's this?", "thrive"); ?></a>
                </p>
                <p class="highlight-menu-item-info">
                    <span><?php _e("If selected, this particular menu item will be highlighted making a stronger call to action than other items in this menu.","thrive"); ?></span><br /><br />
                    <span><?php _e("This is commonly used to make one menu item stand out from the rest and is recommended for conversion goals such as 'Sign up' or 'Try for free'. ","thrive"); ?></span><br /><br />
                    <span><?php _e("You can only have one highlighted menu item for each menu and you can change the colour in the '<a href='".admin_url( 'customize.php')."'>theme customizer</a>'","thrive"); ?></span><br /><br />
                </p>

                <?php if ((isset($theme_options['extended_menu']) && $theme_options['extended_menu'] == 'on') || (!isset($theme_options['extended_menu']))) : ?>
                    <p class="description description-wide depth-0-extended menu-item-extended-heading">
                        <label for="menu-item-extended-heading-<?php echo $item_id; ?>">
                            <input type="checkbox" id="menu-item-extended-heading-<?php echo $item_id; ?>" value="on" name="menu-item-extended-heading[<?php echo $item_id; ?>]" <?php checked($item->extended_heading, 'on'); ?>/>
                            <?php _e('Use Column Heading', 'thrive'); ?>
                        </label>
                    </p>

                    <p class="description description-thin depth-0-extended menu-item-extended-columns">
                        <label for="edit-menu-item-extended-columns-<?php echo $item_id; ?>">
                            <?php _e('Columns', 'thrive'); ?>
                            <select style="width:100px" id="edit-menu-item-extended-columns-<?php echo $item_id; ?>" name="menu-item-extended-columns[<?php echo $item_id; ?>]">
                                <?php for ($i = 1; $i < 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == $item->extended_columns ? "selected" : ""; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </label>
                    </p>

                    <p class="description description-wide depth-1-extended">
                        <label for="menu-item-extended-disable-link-<?php echo $item_id; ?>">
                            <input type="checkbox" id="menu-item-extended-disable-link-<?php echo $item_id; ?>" value="on" name="menu-item-extended-disable-link[<?php echo $item_id; ?>]" <?php checked($item->extended_disable_link, 'on'); ?>/>
                            <?php _e('Disable Link', 'thrive'); ?>
                        </label>
                    </p>


                    <p class="description description-wide depth-2-extended">
                        <label for="menu-item-extended-text-chk-<?php echo $item_id; ?>">
                            <input class="menu-item-extended-text-chk" type="checkbox" id="menu-item-extended-text-chk-<?php echo $item_id; ?>" value="on" name="menu-item-extended-text-chk[<?php echo $item_id; ?>]" <?php checked($item->extended_text_chk, 'on'); ?>/>
                            <?php _e('Free Text Item', 'thrive'); ?>
                        </label>
                    </p>

                    <p style="display: none;" class="description description-wide depth-2-extended extended-free-text">
                        <label for="menu-item-extended-free-text-<?php echo $item_id; ?>">
                            <?php _e('Text Field (This field is HTML enabled for the following tags: p, a, strong, em, img and br).', 'thrive'); ?>
                            <br/>
                            <textarea style="width: 100%;" id="menu-item-extended-free-text-<?php echo $item_id; ?>" rows="3" name="menu-item-extended-free-text[<?php echo $item_id; ?>]"><?php echo esc_html($item->extended_free_text); // textarea_escaped                         ?></textarea>
                        </label>
                    </p>
                <?php endif; ?>

                <?php do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args ); ?>

                <!-- End custom fields for the menu -->

                <p class="field-move hide-if-no-js description description-wide">
                    <label>
                        <span><?php _e('Move', 'thrive'); ?></span>
                        <a href="#" data-dir="up" class="menus-move menus-move-up"><?php _e('Up one'); ?></a>
                        <a href="#" data-dir="down" class="menus-move menus-move-down"><?php _e('Down one'); ?></a>
                        <a href="#" data-dir="left" class="menus-move menus-move-left"></a>
                        <a href="#" data-dir="right" class="menus-move menus-move-right"></a>
                        <a href="#" data-dir="top" class="menus-move menus-move-top"><?php _e('To the top'); ?></a>
                    </label>
                </p>

                <div class="menu-item-actions description-wide submitbox">
                    <?php if ('custom' != $item->type && $original_title !== false) : ?>
                        <p class="link-to-original">
                            <?php printf(__('Original: %s', 'thrive'), '<a href="' . esc_attr($item->url) . '">' . esc_html($original_title) . '</a>'); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                    echo wp_nonce_url(
                            add_query_arg(
                                    array(
                        'action' => 'delete-menu-item',
                        'menu-item' => $item_id,
                                    ), admin_url('nav-menus.php')
                            ), 'delete-menu_item_' . $item_id
                    );
                    ?>"><?php _e('Remove', 'thrive'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), admin_url('nav-menus.php')));
                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', 'thrive'); ?></a>
                </div>

                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object_id); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_item_parent); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_order); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->type); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>

            <style>

                .depth-0-extended, .depth-1-extended, .depth-2-extended{
                    display: none;
                }

                .menu-item-depth-0>div>p.depth-0-extended, .menu-item-depth-1>div>p.depth-1-extended, .menu-item-depth-2>div>p.depth-2-extended {
                    display: block;
                }

                .highlight-menu-item a {
                    float:right;
                }

                .highlight-menu-item-info {
                    display: none;
                }

            </style>
            <?php
            $output .= ob_get_clean();
        }

    }

// Walker_Nav_Menu_Edit


