<h3>Your Custom Icons</h3>
<p>These icons are available for use on your site:</p>
<div class="icomoon-admin-icons">
    <?php foreach ($this->icons as $class) : ?>
        <span class="icomoon-icon" title="<?php echo $class ?>">
            <span class="<?php echo $class ?>"></span>
        </span>
    <?php endforeach ?>
</div>