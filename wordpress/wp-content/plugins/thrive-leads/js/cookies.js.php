<?php
/**
 * set the individual cookies from javascript - the ones that we cannot set server-side - as in the shortcodes case
 */
?>
<script type="text/javascript">
    var _now = new Date(), sExpires;
    _now.setTime(_now.getTime() + (365 * 24 * 3600 * 1000));
    sExpires = _now.toUTCString();
    <?php foreach ($GLOBALS['tve_leads_set_cookies'] as $key => $value) : ?>
        document.cookie = encodeURIComponent(<?php echo json_encode($key) ?>) + '=' + encodeURIComponent(<?php echo json_encode($value) ?>) +
            '; expires=' + sExpires + '; path=/';
    <?php endforeach ?>
</script>