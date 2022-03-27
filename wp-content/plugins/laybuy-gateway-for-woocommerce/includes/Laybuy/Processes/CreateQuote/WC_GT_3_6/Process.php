<?php

class Laybuy_Processes_CreateQuote_WC_GT_3_6_Process extends Laybuy_Processes_CreateQuote_CreateQuoteAbstract
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
        # Get posted data.
        $checkout = $this->getCheckout();

        if (!$checkout) {
            throw new InvalidArgumentException(
                'Parameter checkout is not set. Please call setCheckout before executing the process'
            );
        }

        $data = $checkout->get_posted_data();

        if ($data['payment_method'] != self::PAYMENT_METHOD_LAYBUY) {
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

        $cart = WC()->cart;

        $cart_hash = $cart->get_cart_hash();
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

        $chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
        $shipping_packages = WC()->shipping()->get_packages();

        $customer_id = apply_filters( 'woocommerce_checkout_customer_id', get_current_user_id() );
        $order_vat_exempt = ( $cart->get_customer()->get_is_vat_exempt() ? 'yes' : 'no' );
        $currency = get_woocommerce_currency();

        $prices_include_tax = ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' || get_woocommerce_currency() === WC_Laybuy_Helper::CURRENCY_CODE_GB);

        $customer_ip_address = WC_Geolocation::get_ip_address();
        $customer_user_agent = wc_get_user_agent();
        $customer_note = ( isset( $data['order_comments'] ) ? $data['order_comments'] : '' );
        $payment_method = ( isset( $available_gateways[ $data['payment_method'] ] ) ? $available_gateways[ $data['payment_method'] ] : $data['payment_method'] );
        $shipping_total = $cart->get_shipping_total();

        $discount_total = $cart->get_discount_total();
        $discount_tax = $cart->get_discount_tax();
        $cart_tax = $cart->get_cart_contents_tax() + $cart->get_fee_tax();
        $shipping_tax = $cart->get_shipping_tax();
        $total = $cart->get_total( 'edit' );

        $nonce = wp_create_nonce( "laybuy_quote_{$quote_id}_create" );
        update_post_meta( $quote_id, '_laybuy_quote_nonce', $nonce );

        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $defaultPhoneKey = 'billing_phone';
        $billingPhone = null;

        if (isset($data[$defaultPhoneKey])) {
            $billingPhone = $data[$defaultPhoneKey];
        }

        if (!$billingPhone && isset($data[$settings['laybuy_billing_phone_field']])) {
            $billingPhone = $data[$settings['laybuy_billing_phone_field']];
        }

        $apiData = array(
            'amount'    => $total,
            'returnUrl' => $this->_buildReturnUrl($quote_id, $nonce),
            'merchantReference' => $this->_makeUniqueReference($quote_id),
            'customer' => array(
                'firstName' => $data['billing_first_name'],
                'lastName' => $data['billing_last_name'],
                'email' => $data['billing_email'],
                'phone' => $billingPhone
            ),
            'billingAddress' => array(
                "address1" => $data['billing_address_1'],
                "city" => $data['billing_city'],
                "postcode" => $data['billing_postcode'],
                "country" => $data['billing_country'],
            ),
            'items' => array()
        );

        $apiData['tax'] = floatval($cart_tax + $shipping_tax + $discount_tax);

        // items total
        $items_total = $total;

        // shipping
        if ($shipping_total > 0) {
            $apiData['items'][] = array(
                'id' => 'shipping_fee_for_order#' . $quote_id,
                'description' => 'Shipping fee for this order',
                'quantity' => '1',
                'price' =>  $shipping_total
            );
            $items_total -= $shipping_total;
        }

        // tax
        if ($cart_tax) {
            $apiData['items'][] = array(
                'id' => 'total_tax_amount_for_order#' . $quote_id,
                'description' => 'Tax amount for this order',
                'quantity' => '1',
                'price' => $cart_tax + $shipping_tax + $discount_tax
            );
            $items_total -= ($cart_tax + $shipping_tax + $discount_tax);
        }

        $apiData['items'][] = [
            'id'          => 'item_for_order___#' . $quote_id,
            'description' => 'Purchase from ' . get_bloginfo('name'),
            'quantity'    => 1,
            'price'       => $items_total
        ];

        WC_Laybuy_Logger::log("Sending a request to create a Laybuy Order");
        WC_Laybuy_Logger::log(print_r($apiData, true));

        $gateway = $this->getProcessManager()->getApiGateway();
        // Send the order data to Laybuy to get a token.
        $response = $gateway->createOrder($apiData);

        if ($response === false || $response->result === Laybuy_ApiGateway::PAYMENT_STATUS_ERROR) {

            $this->getProcessManager()->cancelQuote($quote_id, 'failed');

            # Log the error and return a truthy integer (otherwise WooCommerce will not bypass the standard order creation process).
            WC_Laybuy_Logger::error("Laybuy_ApiGateway::createOrder() returned -2 (Laybuy did not provide a token for this order.)");
            WC_Laybuy_Logger::error("API Payload: " . print_r($data, true));
            WC_Laybuy_Logger::error("API Response: " . var_export($response, true));

            return -2;
        }

        # Process Get Laybuy Token End

        WC_Laybuy_Logger::log("Laybuy Order Created");
        WC_Laybuy_Logger::log("Response Data: " . print_r($response, true));

        # Add the meta data to the Afterpay_Quote post record.

        add_post_meta( $quote_id, 'status', 'pending' );

        add_post_meta( $quote_id, 'token', $response->token );

        add_post_meta( $quote_id, 'posted', $this->_encode($data) );
        add_post_meta( $quote_id, '$_POST', $this->_encode($_POST) );
        add_post_meta( $quote_id, 'cart', $this->_encode($cart) );

        add_post_meta( $quote_id, 'cart_hash', $this->_encode($cart_hash) );

        add_post_meta( $quote_id, 'chosen_shipping_methods', $this->_encode($chosen_shipping_methods) );
        add_post_meta( $quote_id, 'shipping_packages', $this->_encode($shipping_packages) );

        add_post_meta( $quote_id, 'customer_id', $this->_encode($customer_id) );
        add_post_meta( $quote_id, 'order_vat_exempt', $this->_encode($order_vat_exempt) );
        add_post_meta( $quote_id, 'currency', $this->_encode($currency) );
        add_post_meta( $quote_id, 'prices_include_tax', $this->_encode($prices_include_tax) );
        add_post_meta( $quote_id, 'customer_ip_address', $this->_encode($customer_ip_address) );
        add_post_meta( $quote_id, 'customer_user_agent', $this->_encode($customer_user_agent) );
        add_post_meta( $quote_id, 'customer_note', $this->_encode($customer_note) );
        add_post_meta( $quote_id, 'payment_method', $this->_encode($payment_method) );
        add_post_meta( $quote_id, 'shipping_total', $this->_encode($shipping_total) );
        add_post_meta( $quote_id, 'discount_total', $this->_encode($discount_total) );
        add_post_meta( $quote_id, 'discount_tax', $this->_encode($discount_tax) );
        add_post_meta( $quote_id, 'cart_tax', $this->_encode($cart_tax) );
        add_post_meta( $quote_id, 'shipping_tax', $this->_encode($shipping_tax) );
        add_post_meta( $quote_id, 'total', $this->_encode($total) );

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