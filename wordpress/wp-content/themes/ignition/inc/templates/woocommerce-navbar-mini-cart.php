<!-- Cart Dropdown -->
<?php if (class_exists('WooCommerce')): ?>

    <?php
    function wordSlicer($word)
    {
        // Count
        $charsCount = strlen($word);
        // if more
        if ($charsCount > 18) {
            $word = substr($word, 0, 18) . "...";
        }
        return $word;
    }
    ?>

    <div class="mini-cart-contents">
        <?php if (sizeof(WC()->cart->get_cart()) > 0) : ?>
            <?php $items = WC()->cart->get_cart(); ?>
            <?php $maxProductsToShow = 5; ?>
            <?php $count = 0 ?>
            <ul class="cart-dropdown">
                <li>
                    <a class="cart-contents-btn" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'thrive'); ?>">
                        <?php echo sprintf('%d - ' . _n('item', 'items', WC()->cart->cart_contents_count, 'thrive'), WC()->cart->cart_contents_count); ?>
                    </a>
                    <ul>
                        <?php foreach ($items as $item => $values): ?>
                            <?php $_product = $values['data']->post; ?>
                            <?php $wc_product = apply_filters( 'woocommerce_cart_item_product', $values['data'], $values, $item ); ?>
                            <?php if ($count < $maxProductsToShow): ?>
                                <li>
                    <span class="item">
                        <span class="item-left">
                            <?php if (has_post_thumbnail($_product->ID)): ?>
                                <?php echo get_the_post_thumbnail($_product->ID, 'thumbnail'); ?>
                            <?php else: ?>
                                <img src="<?php echo WC()->plugin_url() . '/assets/images/placeholder.png'; ?>" alt="">
                            <?php endif; ?>
                            <span class="item-info">
                                <span class="product-name">
                                    <a href="<?php echo get_permalink($_product->ID); ?>">
                                        <?php echo wordSlicer($_product->post_title); ?>
                                    </a>
                                </span>
                                <span class="quantity-amount"><?php echo $values['quantity']; ?> x
                                    <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $wc_product ), $values, $item ); ?>
                                </span>
                            </span>
                        </span>
                        <span class="item-right">
                                <?php
                                echo apply_filters('woocommerce_cart_item_remove_link',
                                    sprintf('<a href="%s" class="remove" title="%s"></a>',
                                        esc_url(WC()->cart->get_remove_url($item)),
                                        __('Remove this item', 'thrive')), $item);
                                ?>
                        </span>
                    </span>
                                    <?php $count++ ?>
                                </li>
                            <?php endif ?>
                        <?php endforeach; ?>
                        <li class="divider"></li>
                        <li>
                            <a class="view-more" href="<?php echo WC()->cart->get_cart_url(); ?>">
                                <?php
                                if (WC()->cart->cart_contents_count <= $maxProductsToShow) {
                                    _e("View Cart", "thrive");
                                } else {
                                    echo __("View All", "thrive") . " " . WC()->cart->cart_contents_count . " " . __("Items", "thrive");
                                }
                                ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php else: ?>
            <a class="cart-contents-btn" href="<?php echo WC()->cart->get_cart_url(); ?>"><?php _e('Cart empty', 'thrive'); ?></a>
        <?php endif ?>
    </div>
<?php endif ?>