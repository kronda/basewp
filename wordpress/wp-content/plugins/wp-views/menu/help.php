<div class="wrap toolset-views">

    <div class="wpv-views-help-page">
        <div id="icon-views" class="icon32"></div>
        <h2><?php _e('Views Help', 'wpv-views') ?></h2>

       <div class="header">
            <h3><?php _e('Building Websites with Views','wpv-views'); ?></h3>
            <p><?php _e('Views plugin lets you design single pages, display content from the database and customize standard listing pages.','wpv-views'); ?></p>
            <p><?php _e('Here are the things that you can create with Views plugin:','wpv-views'); ?></p>
       </div>

        <div class="types-of-views">
            <ul>

                <li>

                    <div class="img-wrap">
                        <img src="<?php echo (WPV_URL . '/res/img/help-page-views-normal.jpg'); ?>">
                    </div>
                    <h4><?php _e('Views','wpv-views'); ?></h4>
                    <p class="desc">
                        <?php _e('A View loads content from the database and displays it anyway you choose. Use Views to create content lists, sliders, parametric searches and more.
','wpv-views'); ?>
                    </p>
                    <p>
                        <a class="button-primary" href="<?php echo admin_url('admin.php?page=views'); ?>"><?php _e('Create a new View','wpv-views'); ?></a>
                    </p>

                </li>

                <li>
                	<div class="img-wrap">
                		<img src="<?php echo (WPV_URL . '/res/img/help-page-ct.jpg'); ?>">
                	</div>
                	<h4><?php _e('Content Templates','wpv-views'); ?></h4>
                    <p class="desc">
                    	<?php _e('Content Templates let you design single pages using fields, taxonomy and HTML. With Content Templates, you can design the output for posts, pages and custom post types.','wpv-views'); ?>
                    </p>
                    <p>
                    	<a class="button-primary" href="<?php echo admin_url('admin.php?page=view-templates'); ?>"><?php _e('Create a new Content Template') ?></a>
                    </p>
                </li>

                <li>
                	<div class="img-wrap">
                		<img src="<?php echo (WPV_URL . '/res/img/help-page-views-archive.jpg'); ?>">
                	</div>
                	<h4><?php _e('WordPress Archives','wpv-views'); ?></h4>
                    <p class="desc">
                    	<?php _e('WordPress Archives let you customize standard listing pages. You will be able to customize the blog, custom post archives, taxonomy pages and other standard listing pages.','wpv-views'); ?>
                    </p>
                    <p>
                    	<a class="button-primary" href="<?php echo admin_url('admin.php?page=view-archives'); ?>"><?php _e('Create a new WordPress Archive') ?></a>
                    </p>
                </li>

            </ul>
        </div>

       <div class="footer">
            <h3><?php _e('Learning Views','wpv-views'); ?></h3>
            <ul>
                <li>
                    <a href="http://wp-types.com/documentation/user-guides/getting-started-with-views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=help-page-link-1&utm_term=Views Training" target="_blank"><?php _e('Views Training','wpv-views') ?></a> &ndash; <?php _e('an interactive course that takes you through the basics and advanced features of Views.','wpv-views') ?>
                    <p><?php _e('Learn by building a real site, using sample data and step-by-step tutorials that we’ve prepared for you.','wpv-views') ?></p>
                </li>
                <li>
                    <a href="http://wp-types.com/documentation/user-guides/?utm_source=viewsplugin&utm_campaign=views&utm_medium=help-page-link-2&utm_term=Views Documentation#Views" target="_blank"><?php _e('Views Documentation','wpv-views') ?></a> &ndash; <?php _e('a complete reference library for everything in Views.','wpv-views') ?>
                </li>
                <li>
                    <a href="http://wp-types.com/forums/forum/support-2/?utm_source=viewsplugin&utm_campaign=views&utm_medium=help-page-link-3&utm_term=Support forum" target="_blank"><?php _e('Support forum','wpv-views') ?></a> &ndash; <?php _e('need any technical help? Our support staff are waiting for you in our forum.','wpv-views') ?>
                </li>
            </ul>
       </div>

    </div>

<h3 style="margin-top:3em;"><?php _e('Debug information', 'wpv-views'); ?></h3>
<p><?php
printf(
    __( 'For retrieving debug information if asked by a support person, use the <a href="%s">debug information</a> page.', 'wpv-views' ),
    admin_url('admin.php?page=views-debug-information')
);
?></p>
</div>
