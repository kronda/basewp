<div class="wpv-dialog wpv-dialog-layout-wizard js-wpv-dialog-layout-wizard">
    <div class="wpv-dialog-header">
        <h2><?php _e( 'Loop Wizard','wpv-views' ); ?></h2>
        <i class="icon-remove js-dialog-close"></i>
    </div>

    <ul class="wpv-dialog-nav js-layout-wizard-nav">
        <li class="wpv-dialog-nav-tab">
            <a href="#js-layout-wizard-layout-style" class="active"><?php _e( 'Loop output style', 'wpv-views' ); ?></a>
        </li>
        <li class="wpv-dialog-nav-tab">
            <a href="#js-layout-wizard-fields" class="js-tab-not-visited"><?php _e('Choose fields','wpv-views') ?></a>
        </li>
    </ul>

    <div class="wpv-dialog-content">

		<p class="toolset-alert toolset-alert-info js-wpv-layout-wizard-overwrite-notice" style="display:none">
			<?php
			_e( 'The View loop will be overwritten by this wizard.', 'wpv-views' );
			echo WPV_MESSAGE_SPACE_CHAR;
			_e( 'If you want to add fields and keep your HTML edits, use the <strong>Fields and Views</strong> button.', 'wpv-views' );
			?>
		</p>
		
		<div class="js-wpv-message-container"></div>
		
        <div class="wpv-dialog-content-tabs">
			
            <div class="wpv-dialog-content-tab js-layout-wizard-tab" id="js-layout-wizard-layout-style" style="position:relative">
                <h2><?php _e('How do you want the View to display?','wpv-views'); ?></h2>
                <ul class="wpv-layout-wizard-layout-style js-wpv-layout-wizard-layout-style">
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
						<p class="tip js-wpv-bootstrap-message"></p>
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
						<p class="tip js-wpv-layout-wizard-layout-style-options-table_of_fields js-layout-wizard-include-fields-names hidden">
							<input id="include_field_names" type="checkbox" name="include_field_names" />
							<label for="include_field_names"><?php _e('Make the table sortable by columns','wpv-views'); ?></label>
						</p>
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
                <div class="wpv-layout-wizard-layout-style-options js-wpv-layout-wizard-layout-style-options">
					<div class="js-wpv-layout-wizard-layout-style-options-bootstrap-grid js-layout-wizard-bootstrap-grid-box hidden">
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
	                        <select class="js-wpv-layout-wizard-bootstrap-grid-cols" name="bootstrap_grid_cols">
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="6">6</option>
								<option value="12">12</option>
	                        </select>
                        </p>
                        <p class="js-layout-wizard-bootstrap-grid-container">
	                        <input type="checkbox" name="bootstrap_grid_container" id="bootstrap_grid_container" value="1" />
	                        <label for="bootstrap_grid_container"><?php _e('Add container','wpv-views'); ?><br></label>
                        </p>
                        <p class="js-layout-wizard-bootstrap-grid-row-class">
	                        <input type="checkbox" name="bootstrap_grid_row_class" id="bootstrap_grid_row_class" value="1" />
	                        <label for="bootstrap_grid_row_class"><?php _e('Add .row class','wpv-views'); ?><br></label>
                        </p>
                        <p class="js-layout-wizard-bootstrap-grid-individual">
	                        <input type="radio" name="bootstrap_grid_individual" id="bootstrap_grid_individual_yes" value="" />
	                        <label for="bootstrap_grid_individual_yes"><?php _e('Compact HTML structure','wpv-views'); ?></label><br>
	                        <input type="radio" name="bootstrap_grid_individual" id="bootstrap_grid_individual_no" value="1" />
	                        <label for="bootstrap_grid_individual_no"><?php _e('Detailed HTML structure','wpv-views'); ?></label><br>
                        </p>
					</div>
					<div class="js-wpv-layout-wizard-layout-style-options-table js-layout-wizard-num-columns hidden">
                        <h4><?php _e('Table-based grid options','wpv-views'); ?></h4>
                        <p>
							<?php _e('Number of columns','wpv-views'); ?>:
							<select class="js-wpv-layout-wizard-table-cols" name="table_cols">
								<?php
									for ( $i = 2; $i < 13; $i++ ) {
										echo '<option value="' . $i . '">' . $i . '</option>';
									}
								?>
							</select>
						</p>
					</div>
                </div>
				
				<div style="margin: 10px 0 0;padding: 10px 0 0; border-top: solid 1px #ededed;text-align: right;clear: both;">
					<?php echo '<a class="wpv-help-link" href="http://wp-types.com/documentation/user-guides/view-layouts-101/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-wizard&utm_term=Learn about different layouts" target="_blank">' . __('Learn about different layouts', 'wpv-views') . ' &raquo;</a>';?>
				</div>
            </div>

            <div class="wpv-dialog-content-tab js-layout-wizard-tab" id="js-layout-wizard-fields">
                <h2><?php _e('Select the fields to include in the loop','wpv-views'); ?></h2>
                <div class="wpv-layout-wizard-layout-fields js-wpv-layout-wizard-layout-fields">

                </div>
				
				<p style="text-align: right;padding-right:5px;">
					<button class="button button-secondary js-layout-wizard-add-field">
						<i class="icon-plus"></i> <?php _e('Add a field','wpv-views') ?>
					</button>
				</p>
				
				<div style="margin: 10px 0 0;padding: 10px 0 0; border-top: solid 1px #ededed;">
					<input type="checkbox" value="1" id="js-wpv-use-view-loop-ct" />
					<label for="js-wpv-use-view-loop-ct"><?php _e('Use a Content Template to group the fields in this View loop','wpv-views'); ?></label>
					<span class="wpv-helper-text" style="margin-left: 25px;">
						<?php _e( 'Wrap all the fields into a Content Template, so you can edit the content of the loop easily', 'wpv-views' ); ?>
					</span>
				</div>
            </div>
            <?php wp_nonce_field('layout_wizard_nonce', 'layout_wizard_nonce'); ?>
        </div>

    </div>

    <div class="wpv-dialog-footer js-wpv-layout-wizard-dialog-footer">
		<span class="wpv-layout-wizard-layout-fields-feedback js-wpv-layout-wizard-layout-fields-feedback" style="display:none;line-height:26px;color:#757575;margin-right:20px;"><?php _e( 'Loading existing fields', 'wpv-views' ); ?></span>
        <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
        <button class="button js-dialog-prev"><?php _e('Previous','wpv-views') ?></button>
        <button class="button button-primary js-insert-layout" data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_extra_nonce' ); ?>" disabled><?php _e('Next','wpv-views') ?></button>
    </div>

</div>
