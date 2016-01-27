<?php
/**
 *
 * The Redirect List Entry Template.
 *
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */

global $EPS_Redirects_Plugin;
$query_args = array( 'page' => $EPS_Redirects_Plugin->config('page_slug'), 'delete_redirect' => esc_attr( $redirect->id ) );


?>
<tr class="redirect-entry <?php  echo esc_attr( $redirect->status ); ?> id-<?php echo esc_attr( $redirect->id ); ?>" data-id="<?php echo esc_attr( $redirect->id ); ?>" data-status="<?php echo esc_attr( $redirect->status ); ?>">
    <td>
        <p class="eps-grey-text eps-text-center eps-small"><?php echo $redirect->id; ?></p>
    </td>
    <td>
        <a target="_blank" class="eps-url" href="<?php bloginfo('url'); ?>/<?php echo esc_attr($dfrom); ?>" title="<?php bloginfo('url'); ?>/<?php echo esc_attr($dfrom); ?>">
            <span class="eps-url-root eps-url-startcap"><?php echo ($redirect->status == 'inactive' ) ? 'OFF': esc_attr($redirect->status); ?></span><span class="eps-url-root"><?php bloginfo('url'); ?>/</span><span class="eps-url-fragment eps-url-endcap"><?php echo esc_attr($dfrom); ?></span>
        </a>
    </td>
    <td>
        <?php echo eps_get_destination( $redirect ); ?>
    </td>
    <td class="redirect-hits"><strong><?php echo esc_attr( $redirect->count ); ?></strong></td>
    <td class="redirect-actions">
        <a class="button eps-redirect-edit" href="#eps-redirect-edit" data-id="<?php echo esc_attr( $redirect->id ); ?>">Edit</a>
        <a class="button eps-redirect-remove" href="<?php echo add_query_arg( $query_args, admin_url( '/options-general.php' ) ); ?>" data-id="<?php echo esc_attr( $redirect->id ); ?>">&times;</a>
    </td>
</tr>