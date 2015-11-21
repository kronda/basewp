<?php
$tt_img_resize_status = _thrive_get_image_resize_optimization_status();
$tt_img_resize_type = _thrive_get_image_resize_type();
?>

<table class="form-table">
    <tbody>
    <tr>
        <th scope="row">
            <?php _e("Image Resize", 'thrive'); ?>
            <span class="tooltips"
                  title="<?php _e("This process will run through your entire image library and resize all the thumbnails to the optimal sizes for this theme. With optimized thumbnail sizes, less data will be loaded on your pages, which speeds up your site. In most cases, the 'Scale and Crop' option is recommended. Choose 'Scale' if you want to ensure that no thumbnails are cropped on your site.", 'thrive'); ?>"></span>
        </th>
        <td>
            <div class="img-optimization">
                <div class="img-optimization-controls clearfix">
                    <div class="left">
                        <label>
                            <input class="tt-img-resize-type" type="radio" value="<?php echo TT_IMG_RESIZE_TYPE_SCALE; ?>"
                                   name="tt-img-resize-type"
                                   <?php if ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_STARTED): ?>disabled<?php endif; ?>
                                   <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_SCALE): ?>checked<?php endif; ?>/>
                            <?php _e("Scale", 'thrive'); ?>
                            <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_SCALE): ?>
                                (<?php _e("your current option", 'thrive'); ?>)
                            <?php endif; ?>
                        </label>
                    </div>
                    <div class="left">
                        <label>
                            <input class="tt-img-resize-type" type="radio"
                                   value="<?php echo TT_IMG_RESIZE_TYPE_SCALE_AND_CROP; ?>"
                                   name="tt-img-resize-type"
                                   <?php if ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_STARTED): ?>disabled<?php endif; ?>
                                   <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_SCALE_AND_CROP): ?>checked<?php endif; ?>/>
                            <?php _e("Scale and Crop", 'thrive'); ?>
                            <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_SCALE_AND_CROP): ?>
                                (<?php _e("your current option", 'thrive'); ?>)
                            <?php endif; ?>
                        </label>
                    </div>
                    <div class="left">
                        <label>
                            <input class="tt-img-resize-type" type="radio" value="<?php echo TT_IMG_RESIZE_TYPE_DEFAULT; ?>"
                                   name="tt-img-resize-type"
                                   <?php if ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_STARTED): ?>disabled<?php endif; ?>
                                   <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_DEFAULT): ?>checked<?php endif; ?>/>
                            <?php _e("Use default wordpress sizes", 'thrive'); ?>
                            <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_DEFAULT): ?>
                                (<?php _e("your current option", 'thrive'); ?>)
                            <?php endif; ?>
                        </label>
                    </div>
                    <div class="clear"></div>
                    <br/><br/>

                    <div class="optimization-container">
                        <span class="optimization-status" id="tt-optimization-status-msg">

                          <?php if ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_FINISHED): ?>
                              <?php _e("Your images are optimized for the current theme.", 'thrive'); ?>
                          <?php elseif ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_STARTED): ?>
                              <?php _e("Click to resume the image optimization process", 'thrive'); ?>
                          <?php else: ?>
                              <?php if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_DEFAULT): ?>
                                  <?php _e("Click to use the default images", 'thrive'); ?>
                              <?php else: ?>
                                  <?php _e("Click to start the image optimization process", 'thrive'); ?>
                              <?php endif; ?>
                          <?php endif; ?>

                        </span>

                        <button id="tt-btn-resize-images" class="btn-round btn-play"></button>
                        <button id="tt-btn-resize-images-cancel" class="btn-round btn-cencel"
                                <?php if ($tt_img_resize_status == TT_IMG_RESIZE_STATUS_STARTED): ?>style="display: block;"
                                <?php else: ?>style="display: none;"<?php endif; ?>></button>
                    </div>
                    <br/><br/>
                </div>


                <div class="img-optimization-progress">
                    <ul id="tt-list-optimized-images">

                    </ul>
                    <ul style="display: none;">
                        <li id="tt-clone-li-filename" style="display: none;">
                            <span class="img-icon"><span class="com-icon"></span></span>
                            <span class="img-name"></span>
                            <span class="img-status btn-round"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </td>
    </tr>
    </tbody>


</table>