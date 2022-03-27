<?php

class Laybuy_Processes_CreateOrder_WC_LT_3_6_Process extends Laybuy_Processes_CreateOrder_CreateOrderAbstract
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
        try {

            $quote_id = $this->getQuoteId();

            WC_Laybuy_Logger::log("Creating an WC order from a quote {$quote_id}. Transaction start");

            // Start transaction if available
            wc_transaction_query( 'start' );

            # Retrieve the order data from the Laybuy_Quote item.

            $_POST = $this->_decode(get_post_meta( $quote_id, '$_POST', true ));

            $customer_id = get_post_meta( $quote_id, 'customer_id', true );
            $cart_hash = get_post_meta( $quote_id, 'cart_hash', true );
            $cart_shipping_total = (float)get_post_meta( $quote_id, 'cart_shipping_total', true );
            $cart_shipping_tax_total = (float)get_post_meta( $quote_id, 'cart_shipping_tax_total', true );
            $cart_discount_total = (float)get_post_meta( $quote_id, 'cart_discount_total', true );
            $cart_discount_tax_total = (float)get_post_meta( $quote_id, 'cart_discount_tax_total', true );
            $cart_tax_total = (float)get_post_meta( $quote_id, 'cart_tax_total', true );
            $cart_total = (float)get_post_meta( $quote_id, 'cart_total', true );
            $cart_items = json_decode(get_post_meta( $quote_id, 'cart_items', true ), true);
            $cart_fees = json_decode(get_post_meta( $quote_id, 'cart_fees', true ), false);
            $cart_coupons = json_decode(get_post_meta( $quote_id, 'cart_coupons', true ), true);
            $cart_taxes = json_decode(get_post_meta( $quote_id, 'cart_taxes', true ), true);

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {
                $chosen_shipping_methods = json_decode(get_post_meta( $quote_id, 'chosen_shipping_methods', true ), true);
            }
            $shipping_packages = json_decode(get_post_meta( $quote_id, 'shipping_packages', true ), true);
            $billing_address = json_decode(get_post_meta( $quote_id, 'billing_address', true ), true);
            $shipping_address = json_decode(get_post_meta( $quote_id, 'shipping_address', true ), true);
            $posted = json_decode(get_post_meta( $quote_id, 'posted', true ), true);

            // Force-delete the Laybuy_Quote item. This will make its ID available to be used as the WC_Order ID.

            wp_delete_post( $quote_id, true );

            //  Create the WC_Order item.
            $order_data = array(
                'status'        => apply_filters( 'woocommerce_default_order_status', 'pending' ),
                'customer_id'   => $customer_id,
                'customer_note' => isset( $posted['order_comments'] ) ? $posted['order_comments'] : '',
                'cart_hash'     => $cart_hash,
                'created_via'   => 'checkout',
            );

            $GLOBALS['laybuy_quote_id'] = $quote_id;
            $order = wc_create_order( $order_data );

            if (isset($GLOBALS['laybuy_quote_id'])) {
                unset($GLOBALS['laybuy_quote_id']);
            }

            if ( is_wp_error( $order ) ) {
                throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 520 ) );
            } elseif ( false === $order ) {
                throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 521 ) );
            } else {
                # avoid older WooCommerce error
                if (method_exists($order, "get_id")) {
                    $order_id = $order->get_id();
                }
                else {
                    $order_id = $order->ID;
                }
                do_action( 'woocommerce_new_order', $order_id );
            }

            $this->create_order_line_items($order, $cart_items);
            $this->create_order_fee_lines($order, $cart_fees);
            $this->create_order_shipping_lines($order, $shipping_packages, $chosen_shipping_methods);
            $this->create_order_tax_lines($order, $cart_taxes);
            $this->create_order_coupon_lines($order, $cart_coupons);

            /**
             * @since 2.0.3
             * Decode the shipping & billing address fields.
             */
            foreach($billing_address as $key => $billing_data) {
                $billing_address[$key] = $this->_decode($billing_data);
            }
            foreach($shipping_address as $key => $shipping_data) {
                $shipping_address[$key] = $this->_decode($shipping_data);
            }

            $order->set_address( $billing_address, 'billing' );
            $order->set_address( $shipping_address, 'shipping' );
            $order->set_payment_method( $this->getProcessManager()->getWcGateway() );

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {
                $order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
                $order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
                $order->set_customer_user_agent( wc_get_user_agent() );
                $order->set_shipping_total( $cart_shipping_total );
                $order->set_discount_total( $cart_discount_total );
                $order->set_discount_tax( $cart_discount_tax_total );
                $order->set_cart_tax( $cart_tax_total );
                $order->set_shipping_tax( $cart_shipping_tax_total );
            } else {
                $order->set_total( $cart_shipping_total, 'shipping' );
                $order->set_total( $cart_discount_total, 'cart_discount' );
                $order->set_total( $cart_discount_tax_total, 'cart_discount_tax' );
                $order->set_total( $cart_tax_total, 'tax' );
                $order->set_total( $cart_shipping_tax_total, 'shipping_tax' );
            }

            $order->set_total( $cart_total );

            do_action( 'woocommerce_checkout_update_order_meta', $order_id, $posted );

            WC_Laybuy_Logger::log("Committing transaction");

            // If we got here, the order was created without problems!
            wc_transaction_query( 'commit' );

            WC_Laybuy_Logger::log("Order created successfully from a quote {$quote_id}");
        } catch ( Exception $e ) {

            // There was an error adding order data!
            wc_transaction_query( 'rollback' );

            WC_Laybuy_Logger::log("Error while WC creating an order from a quote {$quote_id} " . $e->getMessage());

            return new WP_Error( 'checkout-error', $e->getMessage() );
        }

        return $order;
    }

    public function create_order_line_items($order, $cart_items) {

        WC_Laybuy_Logger::log("create_order_line_items - Start");
        WC_Laybuy_Logger::log("cart_items: " . print_r($cart_items, true));

        // Store the line items to the new/resumed order
        foreach ( $cart_items as $cart_item_key => $cart_item ) {
            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {
                $values = array(
                    'data' => wc_get_product($cart_item['id']),
                    'quantity' => $cart_item['props']['quantity'],
                    'variation' => $this->_decode($cart_item['props']['variation']),
                    'line_subtotal' => $cart_item['props']['subtotal'],
                    'line_total' => $cart_item['props']['total'],
                    'line_subtotal_tax' => $cart_item['props']['subtotal_tax'],
                    'line_tax' => $cart_item['props']['total_tax'],
                    'line_tax_data' => $cart_item['props']['taxes']
                );

                # Also reinsert any custom line item fields
                # that may have been attached by third-party plugins.

                foreach( $cart_item as $cart_item_key => $cart_item_value ) {
                    if( !in_array($cart_item_key, array('id', 'props')) ) {
                        $values[$cart_item_key] = $this->_decode($cart_item_value);
                    }
                }

                $item                       = apply_filters( 'woocommerce_checkout_create_order_line_item_object', new WC_Order_Item_Product(), $cart_item_key, $values, $order );
                $item->legacy_values        = $values; // @deprecated For legacy actions.
                $item->legacy_cart_item_key = $cart_item_key; // @deprecated For legacy actions.
                $item->set_props( array(
                    'quantity'     => $cart_item['props']['quantity'],
                    'variation'    => $this->_decode($cart_item['props']['variation']),
                    'subtotal'     => $cart_item['props']['subtotal'],
                    'total'        => $cart_item['props']['total'],
                    'subtotal_tax' => $cart_item['props']['subtotal_tax'],
                    'total_tax'    => $cart_item['props']['total_tax'],
                    'taxes'        => $cart_item['props']['taxes'],
                    'name'         => $this->_decode($cart_item['props']['name']),
                    'tax_class'    => $this->_decode($cart_item['props']['tax_class']),
                    'product_id'   => $cart_item['props']['product_id'],
                    'variation_id' => $cart_item['props']['variation_id']
                ) );
                $item->set_backorder_meta();

                do_action( 'woocommerce_checkout_create_order_line_item', $item, $cart_item_key, $values, $order );

                // Add item to order and save.
                $order->add_item( $item );
            } else {
                $product = new $cart_item['class']( $cart_item['id'] );
                unset( $cart_item['class'] );
                unset( $cart_item['id'] );
                $cart_item['data'] = $product;

                $item_id = $order->add_product(
                    $product,
                    $cart_item['quantity'],
                    array(
                        'variation' => $this->_decode($cart_item['variation']),
                        'totals'    => $cart_item['totals']
                    )
                );

                if ( ! $item_id ) {
                    throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 525 ) );
                }

                // Allow plugins to add order item meta
                do_action( 'woocommerce_add_order_item_meta', $item_id, $cart_item, $cart_item_key );
            }
        }

        WC_Laybuy_Logger::log("create_order_line_items - End");
    }

    /*
     * Add coupon lines to the order.
     */
    public function create_order_coupon_lines($order, $cart_coupons) {

        WC_Laybuy_Logger::log("create_order_coupon_lines - Start");
        WC_Laybuy_Logger::log("cart_coupons: " . print_r($cart_coupons, true));

        // Store coupons
        foreach ( $cart_coupons as $code => $coupon_data ) {

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {

                $item = new WC_Order_Item_Coupon();
                $item->set_props(
                    array(
                        'code'         => $code,
                        'discount'     => $coupon_data['discount_amount'],
                        'discount_tax' => $coupon_data['discount_tax_amount'],
                    )
                );

                // Avoid storing used_by - it's not needed and can get large.
                if (isset($coupon_data['used_by'])) {
                    unset( $coupon_data['used_by'] );
                }

                $item->add_meta_data( 'coupon_data', $coupon_data );

                $coupon = new WC_Coupon( $code );
                /**
                 * Action hook to adjust item before save.
                 *
                 * @since 3.0.0
                 */
                do_action( 'woocommerce_checkout_create_order_coupon_item', $item, $code, $coupon, $order );

                // Add item to order and save.
                $order->add_item( $item );

            } else {
                if ( ! $order->add_coupon( $code, $coupon_data['discount_amount'], $coupon_data['discount_tax_amount'] ) ) {
                    throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 529 ) );
                }
            }
        }
    }

    public function create_order_tax_lines($order, $cart_taxes) {

        WC_Laybuy_Logger::log("create_order_tax_lines - Start");
        WC_Laybuy_Logger::log("cart_taxes: " . print_r($cart_taxes, true));

        // Store tax rows
        foreach ( $cart_taxes as $tax_rate_id => $cart_tax ) {

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {
                if ( $tax_rate_id && apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $tax_rate_id ) {
                    $item = new WC_Order_Item_Tax();
                    $item->set_props(
                        array(
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $cart_tax['tax_amount'],
                            'shipping_tax_total' => $cart_tax['shipping_tax_amount'],
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                        )
                    );

                    /**
                     * Action hook to adjust item before save.
                     *
                     * @since 3.0.0
                     */
                    do_action( 'woocommerce_checkout_create_order_tax_item', $item, $tax_rate_id, $order );

                    // Add item to order and save.
                    $order->add_item( $item );
                } else {
                    if ( ! $order->add_tax( $tax_rate_id, $cart_tax['tax_amount'], $cart_tax['shipping_tax_amount'] ) ) {
                        throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 528 ) );
                    }
                }
            }
        }

        WC_Laybuy_Logger::log("create_order_tax_lines - End");
    }

    public function create_order_shipping_lines($order, $shipping_packages, $chosen_shipping_methods) {

        WC_Laybuy_Logger::log("create_order_shipping_lines - Start");
        WC_Laybuy_Logger::log("shipping_packages: " . print_r($shipping_packages, true));

        /**
         * Store shipping for all packages
         * see WC_Checkout::create_order_shipping_lines
         */

        foreach ( $shipping_packages as $package_key => $package_data ) {

            $package_metadata = $this->_decode( $package_data['package_metadata'] );

            if (version_compare( WC_VERSION, '3.0.0', '>=' )) {
                $package = $this->_decode( $package_data['package'] );

                if ( isset( $chosen_shipping_methods[ $package_key ], $package['rates'][ $chosen_shipping_methods[ $package_key ] ] ) ) {
                    $shipping_rate = $package['rates'][ $chosen_shipping_methods[ $package_key ] ];
                    $item = new WC_Order_Item_Shipping;
                    $item->legacy_package_key = $package_key; // @deprecated For legacy actions.
                    $item->set_props( array(
                        'method_title' => $shipping_rate->label,
                        'method_id'    => $shipping_rate->method_id,
                        'instance_id'  => $shipping_rate->instance_id,
                        'total'        => wc_format_decimal( $shipping_rate->cost ),
                        'taxes'        => array(
                            'total' => $shipping_rate->taxes,
                        ),
                    ) );

                    foreach ( $package_metadata as $key => $value ) {
                        $item->add_meta_data( $key, $value, true );
                    }

                    /**
                     * Action hook to adjust item before save.
                     * @since WooCommerce 3.0.0
                     */
                    do_action( 'woocommerce_checkout_create_order_shipping_item', $item, $package_key, $package, $order );

                    // Add item to order and save.
                    $order->add_item( $item );
                }
            } else {
                $package = new WC_Shipping_Rate( $package_data['id'], $this->_decode($package_data['label']), $package_data['cost'], $package_data['taxes'], $package_data['method_id'] );

                foreach ($package_metadata as $key => $value) {
                    $package->add_meta_data($key, $value);
                }

                $item_id = $order->add_shipping( $package );

                if ( ! $item_id ) {
                    throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 527 ) );
                }

                // Allows plugins to add order item meta to shipping
                do_action( 'woocommerce_add_shipping_order_item', $order->get_id(), $item_id, $package_key );
            }
        }

        WC_Laybuy_Logger::log("create_order_shipping_lines - End");
    }

    public function create_order_fee_lines($order, $cart_fees) {

        WC_Laybuy_Logger::log("create_order_fee_lines - Start");
        WC_Laybuy_Logger::log("cart_fees: " . print_r($cart_fees, true));

        // Store fees
        foreach ( $cart_fees as $fee_key => $fee ) {

            $tax_data = array();

            if (is_string($fee)) { // decode custom fee object
                $fee = $this->_decode($fee);
            }

            foreach ($fee->tax_data as $key_str => $amount) {
                $tax_data[(int)$key_str] = $this->_decode($amount);
            }

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {

                $item                 = new WC_Order_Item_Fee();
                $item->legacy_fee     = $fee; // @deprecated For legacy actions.
                $item->legacy_fee_key = $fee_key; // @deprecated For legacy actions.
                $item->set_props(
                    array(
                        'name'      => $fee->name,
                        'tax_class' => $fee->taxable ? $fee->tax_class : 0,
                        'amount'    => $fee->amount,
                        'total'     => $fee->total,
                        'total_tax' => $fee->tax,
                        'taxes'     => array(
                            'total' => $tax_data,
                        ),
                    )
                );

                /**
                 * Action hook to adjust item before save.
                 *
                 * @since 3.0.0
                 */
                do_action( 'woocommerce_checkout_create_order_fee_item', $item, $fee_key, $fee, $order );

                // Add item to order and save.
                $order->add_item( $item );


            } else {

                $fee->tax_data = $tax_data;
                $item_id = $order->add_fee( $fee );

                if ( ! $item_id ) {
                    throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'woocommerce' ), 526 ) );
                }
            }

            // Allow plugins to add order item meta to fees
            do_action( 'woocommerce_add_order_fee_meta', $order->get_id(), $item_id, $fee, $fee_key );
        }

        WC_Laybuy_Logger::log("create_order_fee_lines - End");
    }
}