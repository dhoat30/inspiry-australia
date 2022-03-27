<?php
// add to cart  Ajax -------------------------------------------------------------
add_action('wp_ajax_woocommerce_ajax_update_cart', 'woocommerce_ajax_update_cart');
add_action('wp_ajax_nopriv_woocommerce_ajax_update_cart', 'woocommerce_ajax_update_cart');
        
function woocommerce_ajax_update_cart() {

            $cartItemKey = sanitize_text_field($_POST['cartItemKey']);
            $qty = sanitize_text_field($_POST['qty']);
            global $woocommerce;

            $woocommerce->cart->set_quantity( $cartItemKey, $qty, false );
            $woocommerce->cart->calculate_totals();
            $productPrice = ''; 
            $regularPrice = ''; 
            $salePrice = ''; 
            $productSubtotal = '';
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                if($cart_item_key === $cartItemKey){ 
                    $product = $cart_item['data'];
                    $regularPrice = $product->regular_price; 
                    $productPrice = $product->price; 
                    $salePrice = $product->get_sale_price();
                    $productSubtotal = WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] );
                }
            } 
            $dataArray = array(
                'code'=>  200, 
                'subtotal'=> WC()->cart->subtotal, 
                'total'=> WC()->cart->get_total(), 
                'tax' => WC()->cart->get_taxes_total(), 
                'shipping'=> WC()->cart->get_shipping_total() + WC()->cart->get_shipping_taxes()[1], 
                'productPrice'=> $productPrice, 
                'regularPrice' => $regularPrice, 
                'salePrice'=> $salePrice, 
                'productSubtotal'=> $productSubtotal
            );
            echo wp_send_json($dataArray);

           wp_die();
}

// add coupon  Ajax -------------------------------------------------------------
add_action('wp_ajax_woocommerce_ajax_add_coupon', 'woocommerce_ajax_add_coupon');
add_action('wp_ajax_nopriv_woocommerce_ajax_add_coupon', 'woocommerce_ajax_add_coupon');
        
function woocommerce_ajax_add_coupon() {

            $couponCode = $_POST['couponCode'];
            global $woocommerce;
            
            if($couponCode === 'remove'){ 
                WC()->cart->remove_coupons();
                    $dataArray = array(
                        'code'=>  202, 
                        'couponCode'=> $couponCode, 
                        'subtotal'=> WC()->cart->subtotal, 
                        'total'=> WC()->cart->get_total(), 
                        'tax' => WC()->cart->get_taxes_total(), 
                        'shipping'=> WC()->cart->get_shipping_total() + WC()->cart->get_shipping_taxes()[1]
                    );
                     echo wp_send_json($dataArray);
            }
            else{ 
                WC()->cart->remove_coupons();
                $ret = WC()->cart->add_discount( $couponCode ); 
                if($ret){ 
                    $dataArray = array(
                        'code'=>  200, 
                        'couponCode'=> $couponCode, 
                        'subtotal'=> WC()->cart->subtotal, 
                    'total'=> WC()->cart->get_total(), 
                    'tax' => WC()->cart->get_taxes_total(), 
                    'shipping'=> WC()->cart->get_shipping_total() + WC()->cart->get_shipping_taxes()[1]
                    );
                     echo wp_send_json($dataArray);
                }
            }
           
           wp_die();
}