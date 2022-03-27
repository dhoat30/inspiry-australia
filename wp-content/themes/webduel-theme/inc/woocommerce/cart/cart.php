<?php

// add banner on cart page 
add_action('webduel_hero_section', function () {
    if (is_cart()) {
        $imageLarge = get_the_post_thumbnail_url(get_the_id(), 'large');
        echo '<img src="' . $imageLarge . '"/>';
    }
}, 10);

// add page title
add_action('woocommerce_before_cart', function () {
?>

<?php
}, 20);
// desktop cart tabel 
add_action('woocommerce_before_cart', function () {
?>
    <div class="cart-flex-container">
        <div class="product-summary desktop-cart-table">
            <h1>My Cart</h1>
            <table class="cart-items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Details</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Remove</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $product = $cart_item['data'];

                        $delivery = wc_get_product_terms($cart_item['product_id'], 'pa_availability')[0]->name;
                        $availability = "";
                        $product_id = '';
                        $colourAttribute = '';
                        $sizeAttribute = '';
                        if ($cart_item['data']->post_type === 'product_variation') {
                            $product_id =  $cart_item['variation_id'];
                            $variation = wc_get_product($product_id);
                            $availability = $variation->get_availability();
                            $colourAttribute = $product->get_attributes()['pa_colour'];
                            $sizeAttribute = $product->get_attributes()['pa_sizes'];
                        } else {
                            $product_id = $cart_item['product_id'];
                            $availability = $product->get_availability();
                        }
                        $quantity = $cart_item['quantity'];
                        $regularPrice = $product->regular_price;
                        $subtotal = WC()->cart->get_product_subtotal($product, $cart_item['quantity']);
                        $link = $product->get_permalink($cart_item);
                    ?>
                        <tr class="<?php echo $cart_item_key ?>">
                            <td class="image-column">
                                <a href="<?php echo $link ?>">
                                    <div class="img-container">
                                        <?php
                                        if (wc_get_product($cart_item["free_sample"])) {
                                            $originalProduct = wc_get_product($cart_item["free_sample"]);
                                            $imageID = $originalProduct->image_id;
                                            $name = $originalProduct->get_name();

                                        ?>
                                            <img src="<?php echo wp_get_attachment_image_url($imageID, 'woocommerce_thumbnail'); ?>" alt="<?php echo $name; ?>" />

                                        <?php
                                        } else {
                                        ?>
                                            <img src="<?php echo get_the_post_thumbnail_url($product_id, array(500, 500)); ?>" alt="<?php echo $product->name ?>" />

                                        <?php
                                        }
                                        ?>
                                    </div>
                                </a>

                            </td>
                            <td class="product-info-column">
                                <!-- check if the product is sample -->
                                <?php
                                if (wc_get_product($cart_item["free_sample"])) {
                                    $originalProduct = wc_get_product($cart_item["free_sample"]);
                                    $permalink = get_the_permalink($originalProduct->get_id());
                                    $name = $originalProduct->get_name();
                                ?>
                                    <a href="<?php echo $permalink; ?>" class="product-title">
                                        Free Sample(<?php echo $name; ?>)
                                    </a>
                                <?php
                                } else {
                                ?>
                                    <a href="<?php echo $link ?>" class="product-title">
                                        <?php echo $product->name ?>
                                    </a>
                                <?php
                                }
                                ?>


                                <!-- variation attributes  -->
                                <div class="variation-attributes">
                                    <?php
                                    if ($colourAttribute) {
                                    ?>
                                        <div class="item">
                                            Color: <span><?php echo $colourAttribute; ?> </span>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if ($sizeAttribute) {
                                    ?>
                                        <div class="item">
                                            Size: <span><?php echo $sizeAttribute; ?> </span>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <!-- uncomment once all the product are uploaded -->
                                <!-- <div class="availability-container">

                                    <div class="availability">
                                        <i class="fa-solid fa-cube"></i>
                                        Availability:
                                        <span>
                                            <?php
                                            // if ($availability['class'] === 'in-stock') {
                                            //     echo "In stock";
                                            // } else {
                                            //     echo "Pre order";
                                            // }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="arrives">
                                        Arrives: <span> <?php //echo $delivery;
                                                        ?></span>
                                    </div>
                                </div> -->
                            </td>
                            <td class="quantity-column">
                                <!-- check if the product is sample  -->
                                <?php
                                if (wc_get_product($cart_item["free_sample"])) {
                                    $originalProduct = wc_get_product($cart_item["free_sample"]);
                                    $imageID = $originalProduct->image_id;
                                    $name = $originalProduct->get_name();
                                ?>
                                    <div class="quantity-container">
                                        <input class="minus" type="button" value="–" control-id="ControlID-1">
                                        <input type="number" name="quantity" id="cart-quantity" value="<?php echo $quantity; ?>" max="1" min="1" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>" />
                                        <input class="plus" type="button" value="+" control-id="ControlID-3">
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="quantity-container">
                                        <input class="minus" type="button" value="–" control-id="ControlID-1">
                                        <input type="number" name="quantity" id="cart-quantity" value="<?php echo $quantity; ?>" max="25" min="1" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>" />
                                        <input class="plus" type="button" value="+" control-id="ControlID-3">
                                    </div>
                                <?php
                                }
                                ?>


                            </td>
                            <td class="price-column">
                                <?php
                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                ?>
                            </td>
                            <td class="remove-column remove-product">
                                <i class="fa-solid fa-trash" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>"></i>
                            </td>
                            <td class="item-subtotal-column">
                                <div class="subtotal"><?php echo $subtotal; ?></div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
}, 30);


// mobile table 
add_action('woocommerce_before_cart', function () {
    ?>

        <div class="product-summary mobile-cart-table">
            <h1>My Cart</h1>
            <table class="cart-items-table">

                <tbody>
                    <?php

                    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                        $product = $cart_item['data'];

                        $delivery = wc_get_product_terms($cart_item['product_id'], 'pa_availability')[0]->name;
                        $availability = "";
                        $product_id = '';
                        $colourAttribute = '';
                        $sizeAttribute = '';
                        if ($cart_item['data']->post_type === 'product_variation') {
                            $product_id =  $cart_item['variation_id'];
                            $variation = wc_get_product($product_id);
                            $availability = $variation->get_availability();
                            $colourAttribute = $product->get_attributes()['pa_colour'];
                            $sizeAttribute = $product->get_attributes()['pa_sizes'];
                        } else {
                            $product_id = $cart_item['product_id'];
                            $availability = $product->get_availability();
                        }
                        $quantity = $cart_item['quantity'];
                        $regularPrice = $product->regular_price;
                        $subtotal = WC()->cart->get_product_subtotal($product, $cart_item['quantity']);
                        $link = $product->get_permalink($cart_item);
                    ?>
                        <tr class="<?php echo $cart_item_key ?>">
                            <td class="image-column">
                                <a href="<?php echo $link ?>">
                                    <div class="img-container">
                                        <img src="<?php echo get_the_post_thumbnail_url($product_id, array(500, 500)); ?>" alt="<?php echo $product->name ?>" />
                                    </div>
                                </a>
                                <div class="product-info-column">
                                    <a href="<?php echo $link ?>" class="product-title">
                                        <?php echo $product->name ?>
                                    </a>
                                    <!-- variation attributes  -->
                                    <div class="variation-attributes">
                                        <?php
                                        if ($colourAttribute) {
                                        ?>
                                            <div class="item">
                                                Color: <span><?php echo $colourAttribute; ?> </span>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if ($sizeAttribute) {
                                        ?>
                                            <div class="item">
                                                Size: <span><?php echo $sizeAttribute; ?> </span>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="price-column">
                                            <?php
                                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            ?>
                                        </div>
                                    </div>
                                    <!-- <div class="availability-container">
                                            
                                            <div class="availability">
                                                <i class="fa-solid fa-cube"></i>
                                                Availability: 
                                                <span>
                                                    <?php
                                                    // if( $availability['class']=== 'in-stock'){ 
                                                    //     echo "In stock"; 
                                                    // }
                                                    // else{ 
                                                    // echo "Pre order";
                                                    // }

                                                    ?>
                                                </span>
                                            </div>
                                            <div class="arrives">
                                                Arrives: <span> <?php //echo $delivery;
                                                                ?></span>
                                            </div>
                                        </div> -->
                                </div>

                            </td>

                            <td class="quantity-column">
                                <div class="quantity-container">
                                    <input class="minus" type="button" value="–" control-id="ControlID-1">
                                    <input type="number" name="quantity" id="cart-quantity" value="<?php echo $quantity; ?>" max="25" min="1" data-cart_item_key="<?php echo $cart_item_key; ?>" />
                                    <input class="plus" type="button" value="+" control-id="ControlID-3">
                                </div>
                                <div class="remove-column">
                                    <i class="fa-solid fa-trash" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>"></i>
                                </div>

                            </td>



                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
}, 30);

add_action('woocommerce_before_cart', function () {
    $cart = WC()->cart;

    ?>
        <div class="total-summary" id="total-summary">
            <h2>Order Summary</h2>
            <div>
                <ul class="flex-box subtotal-row">
                    <li class="title">Subtotal: </li>
                    <li class="amount">$<span><?php echo WC()->cart->subtotal; ?> </span></li>
                </ul>
                <?php if (WC()->cart->get_coupon_discount_amount(WC()->cart->get_applied_coupons()[0], false)) {
                ?>
                    <ul class="flex-box coupon-row">
                        <li class="title">Coupon: <?php print_r(WC()->cart->get_applied_coupons()[0]); ?> </li>
                        <li class="amount">-$<span><?php echo WC()->cart->get_coupon_discount_amount(WC()->cart->get_applied_coupons()[0], false); ?> <button>[Remove]</button></span></li>
                    </ul>
                <?php
                } ?>

                <ul class="flex-box shipping-row">
                    <li class="title">Shipping: </li>
                    <li class="amount">$<span><?php $shippingTotal = WC()->cart->get_shipping_total() + WC()->cart->get_shipping_taxes()[1];
                                                echo $shippingTotal; ?>
                        </span>
                    </li>
                </ul>
                <ul class="flex-box tax-row">
                    <li class="title">Est. GST: </li>
                    <li class="amount">$<span><?php echo WC()->cart->get_taxes_total(); ?></span> </li>
                </ul>
                <ul class="flex-box total-row">
                    <li class="title">Total: </li>
                    <li class="amount"><?php echo WC()->cart->get_total(); ?> </li>
                </ul>
            </div>

            <!-- show apply coupon if the coupon is not added yet -->
            <?php if (!WC()->cart->get_coupon_discount_amount(WC()->cart->get_applied_coupons()[0], false)) {
            ?>
                <div class="coupon-code-input-container" )>
                    <input type="text" name="coupon" id="coupon" placeholder="Coupon Code" />
                    <button class="primary-button">Apply</button>
                </div>
            <?php
            } ?>
            <!-- checkout button -->
            <a href="<?php echo wc_get_checkout_url(); ?>" class="primary-button cart-checkout-btn">CHECKOUT NOW</a>
        </div>
        <!-- closing the div of above hook -->
    </div>
<?php
}, 30);

// add regular price 
add_filter('woocommerce_cart_item_price', 'bbloomer_change_cart_table_price_display', 30, 3);

function bbloomer_change_cart_table_price_display($price, $values, $cart_item_key)
{
    $slashed_price = $values['data']->get_price_html();
    $is_on_sale = $values['data']->is_on_sale();
    if ($is_on_sale) {
        $price = $slashed_price;
    }
    return $price;
}
