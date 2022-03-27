<?php

class Laybuy_Processes_CreateQuote_WC_LT_3_6_Process extends Laybuy_Processes_CreateQuote_CreateQuoteAbstract
{
    public $checkout;

    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;
    }

    public function getCheckout()
    {
        return $this->checkout;
    }

    public function execute()
    {
        $checkout = $this->getCheckout();

        if (!$checkout) {
            throw new InvalidArgumentException(
                'Parameter checkout is not set. Please call setCheckout before executing the process'
            );
        }

        $posted = method_exists($checkout, 'get_posted_data') ? $checkout->get_posted_data() : $checkout->posted;

        if ($posted['payment_method'] !== self::PAYMENT_METHOD_LAYBUY) {
            return;
        }

        $quote_id = wp_insert_post( array(
            'post_content' => 'Thank you for your order. Now redirecting to Laybuy to complete your payment...',
            'post_title' => 'Laybuy Order',
            'post_status' => 'publish',
            'post_type' => 'laybuy_quote'
        ), true );


        if (is_wp_error($quote_id)) {
            $errors_str = implode($quote_id->get_error_messages(), ' ');
            WC_Laybuy_Logger::error("WC_Payment_Gateway_Laybuy::create_order_quote() returned -1 (Could not create laybuy_quote post. WordPress threw error(s): {$errors_str})");
            return -1;
        }

        WC_Laybuy_Logger::log("New WP Quote created with ID:{$quote_id} and permalink:\"" . get_permalink( $quote_id ) . "\"");

        $cart     = WC()->cart;
        $session  = WC()->session;

        // Billing
        $billing = array();
        $billing_encoded = array();

        if ( $checkout->checkout_fields['billing'] ) {
            foreach ( array_keys( $checkout->checkout_fields['billing'] ) as $field ) {
                $field_name = str_replace( 'billing_', '', $field );
                $billing[$field_name] = $checkout->get_posted_address_data( $field_name );
                $billing_encoded[$field_name] = $this->_encode($billing[ $field_name ]);
            }
        }

        if (empty($billing['phone']) || strlen(preg_replace('/[^0-9+]/i', '', $billing['phone'])) <= 6) {
            $billing['phone'] = "00 000 000";
        }

        $nonce = wp_create_nonce( "laybuy_quote_{$quote_id}_create" );
        update_post_meta( $quote_id, '_laybuy_quote_nonce', $nonce );

        $data = array(
            'amount'    => $cart->total,
            'returnUrl' => $this->_buildReturnUrl($quote_id, $nonce),
            'merchantReference' => $this->_makeUniqueReference($quote_id),
            'customer' => array(
                'firstName' => $billing['first_name'],
                'lastName' => $billing['last_name'],
                'email' => $billing['email'],
                'phone' => $billing['phone']
            ),
            'billingAddress' => array(
                "address1" => $billing['address_1'],
                "city" => $billing['city'],
                "postcode" => $billing['postcode'],
                "country" => $billing['country'],
            ),
            'items' => array()
        );

        if ('yes' === get_option('woocommerce_prices_include_tax') || get_woocommerce_currency() === WC_Laybuy_Helper::CURRENCY_CODE_GB) {
            $data['tax'] = number_format($cart->tax_total + $cart->shipping_tax_total, 2, '.', '');
        }


        // Shipping total
        $shipping_total = $cart->shipping_total;

        if ($cart->shipping_tax_total > 0) {
            $shipping_total += $cart->shipping_tax_total;
        }

        // items total
        $items_total = $cart->total;

        // shipping
        if ($shipping_total > 0) {
            $data['items'][] = array(
                'id' => 'shipping_fee_for_order#' . $quote_id,
                'description' => 'Shipping fee for this order',
                'quantity' => '1',
                'price' =>  $shipping_total
            );
            $items_total -= $shipping_total;
        }

        // tax
        if ($cart->tax_total && 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
            $data['items'][] = array(
                'id' => 'total_tax_amount_for_order#' . $quote_id,
                'description' => 'Tax amount for this order',
                'quantity' => '1',
                'price' => $cart->tax_total
            );
            $items_total -= $cart->tax_total;
        }

        $data['items'][] = [
            'id'          => 'item_for_order___#' . $quote_id,
            'description' => 'Purchase from ' . get_bloginfo('name'),
            'quantity'    => 1,
            'price'       => $items_total
        ];

        // Store data to build a WC_Order object later.
        $cart_items = array();

        // see WC_Checkout::create_order_line_items
        foreach ($cart->get_cart() as $cart_item_key => $values) {
            $product = $values['data'];

            if ( WC_Laybuy_Helper::is_wc_gt( '3.0' )) {
                $cart_items[$cart_item_key] = array(
                    'props' => array(
                        'quantity'     => $values['quantity'],
                        'variation'    => $this->_encode($values['variation']),
                        'subtotal'     => $values['line_subtotal'],
                        'total'        => $values['line_total'],
                        'subtotal_tax' => $values['line_subtotal_tax'],
                        'total_tax'    => $values['line_tax'],
                        'taxes'        => $values['line_tax_data']
                    )
                );
                if ($product) {
                    $cart_items[$cart_item_key]['id'] = $product->get_id();

                    $cart_items[$cart_item_key]['props'] = array_merge($cart_items[$cart_item_key]['props'], array(
                        'name'         => $this->_encode($product->get_name()),
                        'tax_class'    => $this->_encode($product->get_tax_class()),
                        'product_id'   => $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id(),
                        'variation_id' => $product->is_type( 'variation' ) ? $product->get_id() : 0
                    ));
                }
                
                // custom fields
                foreach( $values as $field => $field_value ) {
                    if ( $this->_isProductDetailCustom($field) ) {
                        $cart_items[$cart_item_key][$field] = $this->_encode($field_value);
                    }
                }

            } else {
                $cart_items[$cart_item_key] = array(
                    'class' => $this->_encode(get_class($product)),
                    'id' => $product->id,
                    'quantity' => $values['quantity'],
                    'variation' => $this->_encode($values['variation']),
                    'totals' => array(
                        'subtotal' => $values['line_subtotal'],
                        'subtotal_tax' => $values['line_subtotal_tax'],
                        'total' => $values['line_total'],
                        'tax' => $values['line_tax'],
                        'tax_data' => $values['line_tax_data'] # Since WooCommerce 2.2
                    )
                );
            }
        }

        // Fees
        $cart_fees = array();

        foreach ( $cart->get_fees() as $fee_key => $fee ) {
            $cart_fees[$fee_key] = $this->_encode($fee);
        }

        // Discounts
        if ($cart->has_discount()) {
            // The total is stored in $cart->get_total_discount(), but we should also be able to get a list.
            $data['discounts'] = array();
            foreach ($cart->coupon_discount_amounts as $code => $amount) {
                $data['discounts'][] = array(
                    'displayName' => $code,
                    'amount' => array(
                        'amount' => number_format($amount, 2, '.', ''),
                        'currency' => get_woocommerce_currency()
                    )
                );
            }
        }

        // Coupons
        $cart_coupons = array();
        foreach ($cart->get_coupons() as $code => $coupon) {
            $cart_coupons[$code] = array(
                'discount_amount' => $cart->get_coupon_discount_amount($code),
                'discount_tax_amount' => $cart->get_coupon_discount_tax_amount($code)
            );
        }

        // Taxes
        $cart_taxes = array();
        foreach (array_keys($cart->taxes + $cart->shipping_taxes) as $tax_rate_id) {
            if ($tax_rate_id && $tax_rate_id !== apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' )) {
                $cart_taxes[$tax_rate_id] = array(
                    'tax_amount' => $cart->get_tax_amount($tax_rate_id),
                    'shipping_tax_amount' => $cart->get_shipping_tax_amount($tax_rate_id)
                );
            }
        }

        // Shipping costs.
        if ($shipping_total > 0) {
            $data['shippingAmount'] = array(
                'amount' => number_format($shipping_total, 2, '.', ''),
                'currency' => get_woocommerce_currency()
            );
        }

        // Shipping address.
        $shipping = array();
        $shipping_encoded = array();

        if ( $checkout->checkout_fields['shipping'] ) {
            foreach ( array_keys( $checkout->checkout_fields['shipping'] ) as $field ) {
                $field_name = str_replace( 'shipping_', '', $field );
                $shipping[ $field_name ] = $checkout->get_posted_address_data( $field_name, 'shipping' );
                $shipping_encoded[ $field_name ] = $this->_encode($shipping[ $field_name ]);
            }
        }

        // shipping methods
        $chosen_shipping_methods = $session->get( 'chosen_shipping_methods' );

        // Shipping packages.
        $shipping_packages = array();

        foreach (WC()->shipping->get_packages() as $package_key => $package) {
            if (isset($package['rates'][$checkout->shipping_methods[$package_key]])) {
                $shipping_rate = $package['rates'][$checkout->shipping_methods[$package_key]];
                $package_metadata = $shipping_rate->get_meta_data();

                $shipping_packages[$package_key] = array();
                if (version_compare( WC_VERSION, '3.0.0', '>=' )) {
                    $shipping_packages[$package_key]['package'] = $this->_encode($package);
                } else {
                    $shipping_packages[$package_key]['id'] = $shipping_rate->id;
                    $shipping_packages[$package_key]['label'] = $this->_encode($shipping_rate->label);
                    $shipping_packages[$package_key]['cost'] = $shipping_rate->cost;
                    $shipping_packages[$package_key]['taxes'] = $shipping_rate->taxes;
                    $shipping_packages[$package_key]['method_id'] = $shipping_rate->method_id;
                }
                $shipping_packages[$package_key]['package_metadata'] = $this->_encode($package_metadata);
            }
        }

        WC_Laybuy_Logger::log("Sending a request to create a Laybuy Order");
        WC_Laybuy_Logger::log(print_r($data, true));


        # Process Get Laybuy Token Start

        // Send the order data to Laybuy to get a token.
        $response = $this->getProcessManager()->getApiGateway()->createOrder($data);

        if ($response === false || $response->result === Laybuy_ApiGateway::PAYMENT_STATUS_ERROR) {

            $this->getProcessManager()->cancelQuote($quote_id, 'failed');

            # Log the error and return a truthy integer (otherwise WooCommerce will not bypass the standard order creation process).
            WC_Laybuy_Logger::error("Laybuy_ApiGateway::createOrder() returned -2 (Laybuy did not provide a token for this order.)");
            WC_Laybuy_Logger::error("API Payload: " . print_r($data, true));
            WC_Laybuy_Logger::error("API Response: " . var_export($response, true));

            return -2;
        }

        WC_Laybuy_Logger::log("Laybuy Order Created\nResponse Data: ". print_r($response, true));

        # Post Create

        // Add the meta data to the Laybuy_Quote post record.
        add_post_meta( $quote_id, 'status', 'pending' );
        add_post_meta( $quote_id, 'token', $response->token );
        add_post_meta( $quote_id, 'customer_id', apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() ) ); # WC_Checkout::$customer_id is private. See WC_Checkout::process_checkout() for how it populates this property.
        add_post_meta( $quote_id, 'cart_hash', md5( json_encode( wc_clean( $cart->get_cart_for_session() ) ) . $cart->total ) );
        add_post_meta( $quote_id, 'cart_shipping_total', $cart->shipping_total );
        add_post_meta( $quote_id, 'cart_shipping_tax_total', $cart->shipping_tax_total );
        add_post_meta( $quote_id, 'cart_discount_total', $cart->get_cart_discount_total() );
        add_post_meta( $quote_id, 'cart_discount_tax_total', $cart->get_cart_discount_tax_total() );
        add_post_meta( $quote_id, 'cart_tax_total', $cart->tax_total );
        add_post_meta( $quote_id, 'cart_total', $cart->total );
        add_post_meta( $quote_id, 'cart_items', json_encode($cart_items) );
        add_post_meta( $quote_id, 'cart_fees', json_encode($cart_fees) );
        add_post_meta( $quote_id, 'cart_coupons', json_encode($cart_coupons) );
        add_post_meta( $quote_id, 'cart_taxes', json_encode($cart_taxes) );
        add_post_meta( $quote_id, 'cart_needs_shipping', (bool)$cart->needs_shipping() );

        add_post_meta( $quote_id, 'shipping_packages', json_encode($shipping_packages) );
        add_post_meta( $quote_id, 'billing_address', json_encode($billing_encoded) );
        add_post_meta( $quote_id, 'shipping_address', json_encode($shipping_encoded) );
        add_post_meta( $quote_id, 'api_data', json_encode($data) );

        add_post_meta( $quote_id, 'currency', $this->_encode(get_woocommerce_currency()));
        add_post_meta( $quote_id, 'total', $this->_encode($cart->total) );

        if ( WC_Laybuy_Helper::is_wc_gt( '3.0' ) ) {
            add_post_meta( $quote_id, 'chosen_shipping_methods', json_encode($chosen_shipping_methods) );
        }

        //  Store the Checkout Posted Data within a Post Meta to run the woocommerce_checkout_order_processed hooks
        add_post_meta( $quote_id, 'posted', json_encode($posted) );
        add_post_meta( $quote_id, '$_POST', $this->_encode($_POST) );

        $result = array(
            'result'	=> 'success',
            'redirect'	=> $response->paymentUrl
        );

        if ( is_ajax() ) {
            wp_send_json( $result );
        } else {
            wp_redirect( $result['redirect'] );
            exit;
        }
    }
}