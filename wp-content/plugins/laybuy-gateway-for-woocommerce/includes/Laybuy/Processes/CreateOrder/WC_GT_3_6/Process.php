<?php

class Laybuy_Processes_CreateOrder_WC_GT_3_6_Process extends Laybuy_Processes_CreateOrder_CreateOrderAbstract
{
    public $quoteId;

    public function setQuoteId($quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function getQuoteId()
    {
        return $this->quoteId;
    }

    public function execute()
    {
        $quote_id = $this->getQuoteId();

        WC_Laybuy_Logger::log("Creating an WC order from a quote {$quote_id}.");
        
        # Get persisted data

        $checkout = WC()->checkout;
        $data = $this->_decode(get_post_meta( $quote_id, 'posted', true ));
        $_POST = $this->_decode(get_post_meta( $quote_id, '$_POST', true ));
        $cart = $this->_decode(get_post_meta( $quote_id, 'cart', true ));

        $cart_hash = $this->_decode(get_post_meta( $quote_id, 'cart_hash', true ));

        $chosen_shipping_methods = $this->_decode(get_post_meta( $quote_id, 'chosen_shipping_methods', true ));
        $shipping_packages = $this->_decode(get_post_meta( $quote_id, 'shipping_packages', true ));

        $customer_id = $this->_decode(get_post_meta( $quote_id, 'customer_id', true ));
        $order_vat_exempt = $this->_decode(get_post_meta( $quote_id, 'order_vat_exempt', true ));
        $currency = $this->_decode(get_post_meta( $quote_id, 'currency', true ));
        $prices_include_tax = $this->_decode(get_post_meta( $quote_id, 'prices_include_tax', true ));
        $customer_ip_address = $this->_decode(get_post_meta( $quote_id, 'customer_ip_address', true ));
        $customer_user_agent = $this->_decode(get_post_meta( $quote_id, 'customer_user_agent', true ));
        $customer_note = $this->_decode(get_post_meta( $quote_id, 'customer_note', true ));
        $payment_method = $this->_decode(get_post_meta( $quote_id, 'payment_method', true ));
        $shipping_total = $this->_decode(get_post_meta( $quote_id, 'shipping_total', true ));
        $discount_total = $this->_decode(get_post_meta( $quote_id, 'discount_total', true ));
        $discount_tax = $this->_decode(get_post_meta( $quote_id, 'discount_tax', true ));
        $cart_tax = $this->_decode(get_post_meta( $quote_id, 'cart_tax', true ));
        $shipping_tax = $this->_decode(get_post_meta( $quote_id, 'shipping_tax', true ));
        $total = $this->_decode(get_post_meta( $quote_id, 'total', true ));

        try {

            wp_delete_post( $quote_id, true );

            /**
             * @see WC_Checkout::create_order
             */

            $order = new WC_Order();

            $fields_prefix = array(
                'shipping' => true,
                'billing'  => true,
            );

            $shipping_fields = array(
                'shipping_method' => true,
                'shipping_total'  => true,
                'shipping_tax'    => true,
            );

            foreach ( $data as $key => $value ) {
                if ( is_callable( array( $order, "set_{$key}" ) ) ) {
                    $order->{"set_{$key}"}( $value );
                } elseif ( isset( $fields_prefix[ current( explode( '_', $key ) ) ] ) ) {
                    if ( ! isset( $shipping_fields[ $key ] ) ) {
                        $order->update_meta_data( '_' . $key, $value );
                    }
                }
            }

            $order->set_created_via( 'checkout' );
            $order->set_cart_hash( $cart_hash );
            $order->set_customer_id( $customer_id );
            $order->add_meta_data( 'is_vat_exempt', $order_vat_exempt );
            $order->set_currency( $currency );
            $order->set_prices_include_tax( $prices_include_tax );
            $order->set_customer_ip_address( $customer_ip_address );
            $order->set_customer_user_agent( $customer_user_agent );
            $order->set_customer_note( $customer_note );
            $order->set_payment_method( $payment_method );
            $order->set_shipping_total( $shipping_total );
            $order->set_discount_total( $discount_total );
            $order->set_discount_tax( $discount_tax );
            $order->set_cart_tax( $cart_tax );
            $order->set_shipping_tax( $shipping_tax );
            $order->set_total( $total );

            $checkout->create_order_line_items( $order, $cart );
            $checkout->create_order_fee_lines( $order, $cart );
            $checkout->create_order_shipping_lines( $order, $chosen_shipping_methods, $shipping_packages );
            $checkout->create_order_tax_lines( $order, $cart );
            $checkout->create_order_coupon_lines( $order, $cart );

            # Store the Post ID in the superglobal array so we can use it in
            # self::filter_woocommerce_new_order_data, which is attached to the
            # "woocommerce_new_order_data" hook and allows us
            # to inject data into the `wp_insert_post` call.

            $GLOBALS['laybuy_quote_id'] = $quote_id;

            do_action( 'woocommerce_checkout_create_order', $order, $data );

            $order_id = $order->save();

            do_action( 'woocommerce_checkout_update_order_meta', $order_id, $data );

            # Clear globals after use, if not already cleared.

            if (isset($GLOBALS['laybuy_quote_id'])) {
                unset($GLOBALS['laybuy_quote_id']);
            }

            WC_Laybuy_Logger::log("Order created successfully from a quote {$quote_id}");

            return $order;
        } catch ( Exception $e ) {

            WC_Laybuy_Logger::log("Error while WC creating an order from a quote {$quote_id} " . $e->getMessage());

            return new WP_Error( 'checkout-error', $e->getMessage() );
        }
    }
}