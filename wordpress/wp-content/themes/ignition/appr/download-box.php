<?php
$downloadLinksJson = get_post_meta(get_the_ID(), '_thrive_meta_appr_download_links', true);
$downloadLinksArray = json_decode($downloadLinksJson, true);
$downloadLinksHeading = thrive_get_theme_options("appr_download_heading");
?>
<?php if (is_array($downloadLinksArray) && count($downloadLinksArray) > 0): ?>

    <?php if (!empty($downloadLinksHeading)): ?>
        <h3><?php echo $downloadLinksHeading; ?></h3>
    <?php endif; ?>

    <div class="apc">
        <?php foreach ($downloadLinksArray as $link): ?>
            <a href="<?php echo $link['link_url']; ?>" <?php if ($link['new_tab'] == 1): ?>target="_blank"<?php endif; ?> class="apl clearfix">
                <div class="api">
                    <span class="awe">
                        <?php
                        switch ($link['icon']):
                            case 'document':
                                echo "&#xf15c;";
                                break;
                            case 'audio':
                                echo "&#xf025;";
                                break;
                            case 'video':
                                echo "&#xf04b;";
                                break;
                            case 'link':
                                echo "&#xf08e;";
                                break;
                            default:
                                echo "&#xf019;";
                        endswitch;
                        ?>
                    </span>
                </div>
                <p><?php echo $link['link_text']; ?></p>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>