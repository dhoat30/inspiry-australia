<?php

class Laybuy_Processes_CreateOrder_CompatibilityMode_Process extends Laybuy_Processes_AbstractProcess
{
    public $orderId;

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function execute()
    {
        # Get posted data.
        $order_id = $this->getOrderId();

        if (!$order_id) {
            throw new InvalidArgumentException(
                'Parameter order_id is not set. Please call setCheckout before executing the process'
            );
        }

        $order = new WC_Order( $order_id );
        $cart = WC()->cart;

        $nonce = wp_create_nonce( "laybuy_{$order_id}_create" );
        update_post_meta( $order_id, '_laybuy_order_nonce', $nonce );

        $apiData = array(
            'amount'    => $cart->get_total( 'edit' ),
            'returnUrl' => $this->_buildReturnUrl($order_id, $nonce, ['compatibility' => 1]),
            'merchantReference' => $this->_makeUniqueReference($order_id),
            'customer' => array(
                'firstName' => $order->get_billing_first_name(),
                'lastName' => $order->get_billing_last_name(),
                'email' => $order->get_billing_email(),
                'phone' => $order->get_billing_phone()
            ),
            'billingAddress' => array(
                "address1" => $order->get_billing_address_1(),
                "city" => $order->get_billing_city(),
                "postcode" => $order->get_billing_postcode(),
                "country" => $order->get_billing_country(),
            ),
            'items' => array()
        );

        $discount_tax = $cart->get_discount_tax();
        $cart_tax = $cart->get_cart_contents_tax() + $cart->get_fee_tax();
        $shipping_tax = $cart->get_shipping_tax();
        $total = $cart->get_total( 'edit' );
        $shipping_total = $cart->get_shipping_total();

        $apiData['tax'] = floatval($cart_tax + $shipping_tax + $discount_tax);

        // items total
        $items_total = $total;

        // shipping
        if ($shipping_total > 0) {
            $apiData['items'][] = array(
                'id' => 'shipping_fee_for_order#' . $order_id,
                'description' => 'Shipping fee for this order',
                'quantity' => '1',
                'price' =>  $shipping_total
            );
            $items_total -= $shipping_total;
        }

        // tax
        if ($cart_tax) {
            $apiData['items'][] = array(
                'id' => 'total_tax_amount_for_order#' . $order_id,
                'description' => 'Tax amount for this order',
                'quantity' => '1',
                'price' => $cart_tax + $shipping_tax + $discount_tax
            );
            $items_total -= ($cart_tax + $shipping_tax + $discount_tax);
        }

        $apiData['items'][] = [
            'id'          => 'item_for_order___#' . $order_id,
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

            # Log the error and return a truthy integer (otherwise WooCommerce will not bypass the standard order creation process).
            WC_Laybuy_Logger::error("Laybuy_ApiGateway::createOrder() returned -2 (Laybuy did not provide a token for this order.)");
            WC_Laybuy_Logger::error("API Payload: " . print_r($apiData, true));
            WC_Laybuy_Logger::error("API Response: " . var_export($response, true));

            return -2;
        }

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