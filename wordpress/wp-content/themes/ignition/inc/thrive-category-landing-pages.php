<?php

/*
Plugin Name: Thrive Category Landing Pages Pages
Plugin URI: http://www.thrivethemes.com
Version: 0.01
Author: <a href="http://www.thrivethemes.com">Thrive Themes</a>
Description: Create page landing pages for your Wordpress Categories
*/

//add extra fields to category edit form hook
add_action('category_edit_form_fields', 'tcp_edit_category_fields');
add_action('category_add_form_fields', 'tcp_add_category_fields');

add_action('edited_category', 'tcp_save_category_landing_page');
add_action('create_category', 'tcp_save_category_landing_page');

add_filter('category_link', 'tcp_category_landing_page_link', 10, 3);

if (!function_exists("tcp_edit_category_fields")) {

    /**
     * add fields to the edit category screen
     */
    function tcp_edit_category_fields($term)
    {
        $term_id = $term->term_id;
        $pages = get_pages();
        $term_meta = get_option("taxonomy_$term_id");
        $landing_page_id = $term_meta['landing_redirect'] ? $term_meta['landing_redirect'] : 'none';
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[landing_redirect]">Redirect Category to Landing Page</label>
            <td>
                <select name="term_meta[landing_redirect]">
                    <option value="none">None</option>
                    <?php foreach ($pages as $page): ?>
                        <option
                            value="<?php echo $page->ID; ?>" <?php echo ($page->ID == $landing_page_id) ? "selected" : ""; ?>><?php echo $page->post_title; ?></option>
                    <?php endforeach; ?>
                </select>

                <p class="description">If set you can replace the Wordpress category page with your own highly optimised
                    landing page </p>
            </td>
            </th>
        </tr><!-- /.form - field-->
    <?php
    }

    /**
     * Add fields to the add category screen
     *
     * @param $tag
     */
    function tcp_add_category_fields($tag)
    {
        $pages = get_pages();
        ?>
        <div class="form-field">
            <label for="term_meta[landing_redirect]">Redirect Category links to Landing Page:</label>
            <select name="term_meta[landing_redirect]">
                <option value="none">None</option>
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                <?php endforeach; ?>
            </select>

            <p class="description">If set you can replace the Wordpress category page with your own highly optimised
                landing page </p>
        </div><!-- /.form - field-->

    <?php
    }

    /**
     * Handle the save functionality for the custom landing page redirect when editing a category
     */
    function tcp_save_category_landing_page($term_id)
    {
        if (isset($_POST['term_meta'])) {
            $t_id = $term_id;
            $term_meta = array();
            $term_meta['landing_redirect'] = isset ($_POST['term_meta']['landing_redirect']) ? $_POST['term_meta']['landing_redirect'] : 'none';

            // Save the option array.
            update_option("taxonomy_$t_id", $term_meta);
        }
    }

    /**
     * Redirect the category pages to custom landing pages
     */
    function tcp_category_landing_page_link($link, $term_id)
    {
        $term_meta = get_option("taxonomy_$term_id");
        $landing_page_id = $term_meta['landing_redirect'] ? $term_meta['landing_redirect'] : 'none';

        if ($landing_page_id != "none") {
            $link = get_permalink($landing_page_id);
        }

        return $link;

    }
}