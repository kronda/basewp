<article id="comments">
    <div class="awr">
        <div class="cmb" style="margin-left: 0px;" id="thrive_container_list_comments">            
            <?php wp_list_comments(array('callback' => 'thrive_comments')); ?>      
        </div>
        <div class="no_comm">
            <h4 class="ctr">
                <?php _e("Comments are closed", 'thrive'); ?>
            </h4>
        </div>            
    </div>
</article>