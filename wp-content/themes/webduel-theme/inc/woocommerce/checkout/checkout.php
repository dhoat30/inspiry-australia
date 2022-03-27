<?php 
// remove coupon --------------------
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
    remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

// add custom order review area 
add_action('woocommerce_checkout_before_order_review', 'webduel_order_review', 10);
function webduel_order_review(){ 
    ?>
    <div class="summary-payment">
        <div class="total-summary" id="total-summary">
            <div class="title-container">
                <h2>Order Summary</h2>
                <a href="<?php echo wc_get_cart_url();?>">Edit cart</a>
            </div>
            <div>
                <?php 
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $product = $cart_item['data'];
                
                        $delivery = wc_get_product_terms( $cart_item['product_id'], 'pa_delivery' )[0]->name;
                        $availability = ""; 
                        $product_id = ''; 
                        $colourAttribute = ''; 
                        $sizeAttribute = '';  
                                if($cart_item['data']->post_type ==='product_variation'){ 
                                    $product_id =  $cart_item['variation_id']; 
                                    $variation = wc_get_product( $product_id );
                                    $availability = $variation->get_availability();
                                    $colourAttribute = $product->get_attributes()['pa_colour'];
                                    $sizeAttribute = $product->get_attributes()['pa_sizes'];
                                    
                                }
                                else{ 
                                    $product_id = $cart_item['product_id'];
                                    $availability = $product->get_availability();
                                }
                        $quantity = $cart_item['quantity'];
                        $regularPrice = $product->regular_price; 
                        $subtotal = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
                        $link = $product->get_permalink( $cart_item );
                        ?>
                        <ul class="flex-box product-row">
                            <li class="title"><?php echo $product->name; ?> X <?php echo $quantity; ?> </li>
                            <li class="amount">$<span><?php echo round($product->get_price() * $quantity, 2) ;?> </span></li>
                        </ul>
                        <?php
                    }
                ?>
                    <ul class="flex-box subtotal-row">
                        <li class="title">Subtotal: </li>
                        <li class="amount">$<span><?php echo WC()->cart->subtotal;?> </span></li>
                    </ul>
                    <?php if(WC()->cart->get_coupon_discount_amount( WC()->cart->get_applied_coupons()[0], false )){
                    ?>
                        <ul class="flex-box coupon-row">
                            <li class="title">Coupon: <?php print_r( WC()->cart->get_applied_coupons()[0]);?> </li>
                            <li class="amount">-$<span><?php echo WC()->cart->get_coupon_discount_amount( WC()->cart->get_applied_coupons()[0], false );?> <button>[Remove]</button></span></li>
                        </ul>
                    <?php 
                    }?>
                
                    <ul class="flex-box shipping-row">
                        <li class="title">Shipping: </li>
                        <li class="amount">$<span><?php $shippingTotal = WC()->cart->get_shipping_total() + WC()->cart->get_shipping_taxes()[1]; 
                            echo $shippingTotal;?> 
                            </span>
                        </li>
                    </ul>
                    <ul class="flex-box tax-row">
                        <li class="title">Est. GST: </li>
                        <li class="amount">$<span><?php echo WC()->cart->get_taxes_total();?></span> </li>
                    </ul>
                    <ul class="flex-box total-row">
                        <li class="title">Total: </li>
                        <li class="amount"><?php echo WC()->cart->get_total();?> </li>
                    </ul>
            </div>
        </div>
        <button class="primary-button" id="pay-button" disabled><i class="fa-regular fa-lock-keyhole"></i>Pay Securely Now</button>
    <?php 
} 

// add payment option
add_action('woocommerce_checkout_before_order_review', 'woocommerce_checkout_payment', 20);

add_action('woocommerce_checkout_before_order_review', function(){
    ?>
    </div>
    <?php 
}, 30);


/* WooCommerce: The Code Below Removes Checkout Fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {

unset($fields['billing']['billing_company']);
unset($fields['billing']['billing_country']);

unset($fields['shipping']['shipping_company']);
unset($fields['shipping']['shipping_country']);
return $fields;
}

// add windcave iframe 
add_action('woocommerce_after_checkout_form', function(){ 
    ?>

    <div class="payment-gateway-container"  data-carttotal="<?php echo WC()->cart->total;?>">
        <div class="foreground-loader">
            <i class="fa-duotone fa-loader fa-spin"></i>
         </div>
        <img src="https://inspiry.co.nz/wp-content/uploads/2021/08/windcave-logo.png" width="95%">
        <div id="payment-iframe-container"> 
           
        </div>
        <div class="button-container" >
            <button class="windcave-submit-button" >Submit</button> 
            <div class="cancel-payment" >Cancel Payment</div> 
        </div> 
    </div> 

  <?php 
}, 20); 