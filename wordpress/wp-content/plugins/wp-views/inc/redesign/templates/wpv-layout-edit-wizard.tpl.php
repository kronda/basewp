<div class="wpv-dialog wpv-dialog-layout-wizard js-wpv-dialog-layout-wizard js-wvp-wizard-loc" data-loc-error="<?php _e('Can\'t insert content in to shortcode') ?>" data-loc-error2="<?php _e('Error occured') ?>" data-loc-insert="<?php _e('Insert') ?>" data-loc-next="<?php _e('Next') ?>">
    <div class="wpv-dialog-header">
        <h2><?php _e('Insert a layout','wpv-views'); ?></h2>
        <i class="icon-remove js-dialog-close"></i>
    </div>

    <ul class="wpv-dialog-nav js-layout-wizard-nav">
        <li class="wpv-dialog-nav-tab">
            <a href="#js-layout-wizard-layout-style" class="active"><?php _e('Layout style','wpv-views') ?></a>
        </li>
        <li class="wpv-dialog-nav-tab">
            <a href="#js-layout-wizard-fields" class="js-tab-not-visited"><?php _e('Choose fields','wpv-views') ?></a>
        </li>
        <li class="wpv-dialog-nav-tab">
            <a href="#js-layout-wizard-insert" class="js-tab-not-visited"><?php _e('Insert to the view','wpv-views') ?></a>
        </li>
    </ul>

    <div class="wpv-dialog-content">

        <div class="wpv-dialog-content-tabs">

            <div class="wpv-dialog-content-tab js-layout-wizard-tab" id="js-layout-wizard-layout-style" style="position:relative">
                <h2><?php _e('Select the style of the layout to insert','wpv-views'); ?></h2>
                <ul class="layout-wizard-layout-style">
                    <li>
                        <input type="radio" name="layout-wizard-style" id="layout-wizard-style-unformatted" class="js-wpv-layout-wizard-style" value="unformatted" />
                        <label for="layout-wizard-style-unformatted">
                            <i class="icon-code"></i>
                         <?php _e('Unformatted','wpv-views'); ?>
                        </label>
                    </li>
                    <li>
                        <input type="radio" name="layout-wizard-style" id="layout-wizard-style-bootstrap-grid" class="js-wpv-layout-wizard-style" value="bootstrap-grid" />
                        <label for="layout-wizard-style-bootstrap-grid">
                            <i class="icon-th-large"></i>
                            <?php _e('Bootstrap grid','wpv-views'); ?>
                        </label>
						<p class="tip js-wpv-bootstrap-disabled">
							<?php _e( 'You need to set the Bootstrap version used in your theme.', 'wpv-views' ); ?>
							<?php echo sprintf( __("<a href='%s' target='_blank'>Go to the Settings page &raquo;</a>", 'wpv-views'), admin_url( 'admin.php?page=views-settings') ); ?>
						</p>
                    </li>
                    <li>
                        <input type="radio" name="layout-wizard-style" id="layout-wizard-style-grid" class="js-wpv-layout-wizard-style" value="table" />
                        <label for="layout-wizard-style-grid">
                            <i class="icon-th"></i>
                            <?php _e('Table-based grid','wpv-views'); ?>
                        </label>
                    </li>
                    <li>
                        <input type="radio" name="layout-wizard-style" id="layout-wizard-style-table" class="js-wpv-layout-wizard-style" value="table_of_fields" />
                        <label for="layout-wizard-style-table">
                            <i class="icon-table"></i>
                            <?php _e('Table','wpv-views'); ?>
                        </label>
                    </li>
                    <li>
                        <input type="radio" name="layout-wizard-style" id="layout-wizard-style-ul" class="js-wpv-layout-wizard-style" value="un_ordered_list" />
                        <label for="layout-wizard-style-ul">
                            <i class="icon-list-ul"></i>
                            <?php _e('Unordered list','wpv-views'); ?>
                        </label>
                    </li>
                    <li>
                         <input type="radio" name="layout-wizard-style" id="layout-wizard-style-ol" class="js-wpv-layout-wizard-style" value="ordered_list" />
                         <label for="layout-wizard-style-ol">
                            <i class="icon-list-ol"></i>
                            <?php _e('Ordered list','wpv-views'); ?>
                        </label>
                    </li>
                </ul>
                <div class="layout-wizard-layout-style-options" style="position:absolute;top:40px;right:0;width:285px;padding:0 0 0 20px;border-left:solid 1px #ededed;">
					<div class="js-layout-wizard-bootstrap-grid-box hidden">
						<h4><?php _e('Bootstrap grid options','wpv-views'); ?></h4>
                        <?php /* Commented, because we will not use fixed width rows for now.
                        <span style="float: right;" class="js-layout-wizard-bootstrap-grid-style">
                        <?php _e('Grid style','wpv-views'); ?>:
                        <select name="bootstrap_grid_style">
                            <option value="fixed">Fixed</option>
                            <option value="fluid">Fluid</option>
                        </select><br>
                        </span>
                        <span style="float: right;" class="js-layout-wizard-bootstrap-grid-col-width">
                        <?php _e('Columns width','wpv-views'); ?>:
                        <select name="bootstrap_grid_cols_width">
                            <?php
                                for($i = 1; $i < 13; $i++) {
                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                }
                            ?>
                        </select>
                        </span>
						 */
						 ?>
                        <p class="js-layout-wizard-bootstrap-grid-num-columns">
	                        <?php _e('Number of columns','wpv-views'); ?>:
	                        <select name="bootstrap_grid_cols">
	                        </select>
                        </p>
                        <p class="js-layout-wizard-bootstrap-grid-container">
	                        <input type="checkbox" name="bootstrap_grid_container" value="1" />
	                        <?php _e('Add container','wpv-views'); ?><br>
                        </p>
                        <p class="js-layout-wizard-bootstrap-grid-individual">
	                        <input type="radio" name="bootstrap_grid_individual" id="bootstrap_grid_individual_yes" value="" />
	                        <label for="bootstrap_grid_individual_yes"><?php _e('Compact HTML structure','wpv-views'); ?></label><br>
	                        <input type="radio" name="bootstrap_grid_individual" id="bootstrap_grid_individual_no" value="1" />
	                        <label for="bootstrap_grid_individual_no"><?php _e('Detailed HTML structure','wpv-views'); ?></label><br>
                        </p>
					</div>
					<div class="js-layout-wizard-num-columns hidden">
                        <h4><?php _e('Table-based grid options','wpv-views'); ?></h4>
                        <p>
							<?php _e('Number of columns','wpv-views'); ?>:
							<select name="table_cols">
								<?php
									for($i = 2; $i < 13; $i++) {
                                    echo '<option value="'.$i.'">'.$i.'</option>';
									}
								?>
							</select>
						</p>
					</div>
					<div class="js-layout-wizard-include-fields-names hidden">
						<h4><?php _e('Table options','wpv-views'); ?></h4>
						<p>
							<input id="include_field_names" type="checkbox" name="include_field_names" />
							<?php _e('Include field names in table headings','wpv-views'); ?>
						</p>
					</div>
                </div>
                <p>
					<?php echo '<a class="wpv-help-link" href="http://wp-types.com/documentation/user-guides/view-layouts-101/" target="_blank">' . __('Learn about different layouts', 'wpv-views') . ' &raquo;</a>';?>
                </p>
            </div>

            <div class="wpv-dialog-content-tab js-layout-wizard-tab" id="js-layout-wizard-fields">
                <h2><?php _e('Select the fields to include in the layout','wpv-views'); ?></h2>
                <ul class="layout-wizard-layout-fields">

                </ul>

                <p>
                    <button class="button button-secondary js-layout-wizard-add-field">
                        <i class="icon-plus"></i> <?php _e('Add field','wpv-views') ?>
                    </button>
                </p>
            </div>

            <div class="wpv-dialog-content-tab js-layout-wizard-tab" id="js-layout-wizard-insert">
                <h2><?php _e('Where do you want to insert this layout?','wpv-views'); ?></h2>
                <ul>
                    <li>
                        <input type="radio" name="layout-wizard-insert" id="layout-wizard-insert-cursor" value="insert_cursor" />
                        <label for="layout-wizard-insert-cursor"><?php _e('In the current cursor position','wpv-views'); ?></label>
                    </li>
                    <li>
                        <input type="radio" name="layout-wizard-insert" id="layout-wizard-insert-replace" value="insert_replace" />
                        <label for="layout-wizard-insert-replace"><?php _e('Replace existing layout','wpv-views'); ?></label>
                    </li>
                </ul>
            </div>

            <?php wp_nonce_field('layout_wizard_nonce', 'layout_wizard_nonce'); ?>
        </div>

    </div>

    <div class="wpv-dialog-footer">
        <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
        <button class="button js-dialog-prev"><?php _e('Previous','wpv-views') ?></button>
        <button class="button button-primary js-insert-layout" data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_extra_nonce' ); ?>" disabled><?php _e('Next','wpv-views') ?></button>
    </div>

</div>