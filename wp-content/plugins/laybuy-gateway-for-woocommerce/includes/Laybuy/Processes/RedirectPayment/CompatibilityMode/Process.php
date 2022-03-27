<?php

class Laybuy_Processes_RedirectPayment_CompatibilityMode_Process extends Laybuy_Processes_AbstractProcess
{
    public $orderId;
    public $status;
    public $token;

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function execute()
    {
        $status   = $this->getStatus();
        $order_id = $this->getOrderId();

        if (function_exists('wc_get_order') ) {
            $order = wc_get_order( $order_id );
        } else {
            $order = new WC_Order( $order_id );
        }

        switch ($status) {

            case Laybuy_ApiGateway::PAYMENT_STATUS_SUCCESS:

                if (get_post_meta( $order_id, '_laybuy_order_nonce', true ) !==  $_GET['_wpnonce']) {
                    wp_redirect( wc_get_checkout_url() );
                    exit;
                }

                WC_Laybuy_Logger::log("Confirming the payment for an order {$order_id}");

                // confirm payment
                $response = $this->getProcessManager()
                    ->getApiGateway()
                    ->confirmOrder([
                        'token' => $this->getToken(),
                        'currency' => $order->get_currency(),
                        'amount' => $order->get_total()
                    ]);

                // returns a WP error object
                if (is_wp_error($response) || !$response) {

                    WC_Laybuy_Logger::log("Failed while confirming the payment for a order {$order_id}");
                    WC_Laybuy_Logger::log("Response data: " . print_r($response, true));

                    $this->cancelOrder($order, 'failed');

                    # Store an error notice and redirect the customer back to the checkout.
                    wc_add_notice( __( 'Your payment token has expired. Please try again.', 'woo_laybuy' ), 'error' );
                    if (wp_redirect( wc_get_checkout_url() )) {
                        exit;
                    }
                }

                if ($response->result !== Laybuy_ApiGateway::PAYMENT_STATUS_SUCCESS) {

                    WC_Laybuy_Logger::log("Failed while confirming the payment for a order {$order_id}");
                    WC_Laybuy_Logger::log("Response data: " . print_r($response, true));

                    $this->cancelOrder($order, 'failed');

                    wc_add_notice( __( $response->error, 'woo_laybuy' ), 'error' );
                    wp_redirect( wc_get_checkout_url() );
                    exit;
                }

                WC_Laybuy_Logger::log("Payment confirmed. Order ID {$order_id}");
                WC_Laybuy_Logger::log("Response: " . print_r($response, true));

                update_post_meta($order->get_id(), '_laybuy_order_id', $response->orderId );
                update_post_meta($order->get_id(), '_laybuy_token', $this->getToken() );

                // save to the DB
                $order->save();

                $order->add_order_note( __( "Payment approved. Laybuy Order ID: {$response->orderId}", 'woo_laybuy') );

                // complete the order and reduce stock if required
                $order->payment_complete($response->orderId);

                wc_empty_cart();
                $redirect = $this->getProcessManager()->getWcGateway()->get_return_url($order);

                wp_redirect( html_entity_decode( $redirect ) );
                exit;

                break;

            case Laybuy_ApiGateway::PAYMENT_STATUS_DECLINED:

                WC_Laybuy_Logger::log("Payment declined. Order ID {$order_id}");

                wc_add_notice( __( 'Your payment was declined.', 'woo_laybuy' ), 'error' );
                if (wp_redirect( wc_get_checkout_url() )) {
                    exit;
                }

                break;
            case Laybuy_ApiGateway::PAYMENT_STATUS_CANCELLED:

                WC_Laybuy_Logger::log("Payment canceled. Order ID {$order_id}");

                $this->cancelOrder($order);

                if (wp_redirect( wc_get_checkout_url() )) {
                    exit;
                }

                break;
            case Laybuy_ApiGateway::PAYMENT_STATUS_ERROR:

                WC_Laybuy_Logger::log("Payment error.");

                wc_add_notice(__('Your payment could not be processed. Please try again.', 'woo_laybuy'), 'error');
                if (wp_redirect( wc_get_checkout_url() )) {
                    exit;
                }
                break;
            default:
                if (wp_redirect( wc_get_checkout_url() )) {
                    exit;
                }
                break;
        }
    }

    public function cancelOrder($order, $status = 'cancelled')
    {
        if (method_exists($order, 'get_cancel_order_url_raw')) {
            if (wp_redirect( $order->get_cancel_order_url_raw() )) {
                exit;
            }
        } else {
            $order->update_status( $status );
            if (wp_redirect( WC()->cart->get_cart_url() )) {
                exit;
            }
        }
    }
}