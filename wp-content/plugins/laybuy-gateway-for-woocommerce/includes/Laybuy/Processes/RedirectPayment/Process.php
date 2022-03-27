<?php

class Laybuy_Processes_RedirectPayment_Process extends Laybuy_Processes_AbstractProcess
{
    public $quoteId;
    public $status;
    public $token;

    public function setQuoteId($quoteId)
    {
        $this->quoteId = $quoteId;
        return $this;
    }

    public function getQuoteId()
    {
        return $this->quoteId;
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
        $status = $this->getStatus();
        $quote_id = $this->getQuoteId();

        switch ($status) {

            case Laybuy_ApiGateway::PAYMENT_STATUS_SUCCESS:

                if (get_post_meta( $quote_id, '_laybuy_quote_nonce', true ) !==  $_GET['_wpnonce']) {
                    wp_redirect( wc_get_checkout_url() );
                    exit;
                }

                WC_Laybuy_Logger::log("Confirming the payment for a quote {$quote_id}");

                $currency = $this->_decode(get_post_meta( $quote_id, 'currency', true ));
                $amount   = floatval($this->_decode(get_post_meta( $quote_id, 'total', true )));

                // confirm payment
                $response = $this->getProcessManager()
                    ->getApiGateway()
                    ->confirmOrder([
                        'token' => $this->getToken(),
                        'currency' => $currency,
                        'amount' => $amount
                    ]);

                // returns a WP error object
                if (is_wp_error($response) || !$response) {

                    WC_Laybuy_Logger::log("Failed while confirming the payment for a quote {$quote_id}");
                    WC_Laybuy_Logger::log("Request data: " . print_r(['token' => $this->getToken(), 'currency' => $currency, 'amount' => $amount], true));
                    WC_Laybuy_Logger::log("Response data: " . print_r($response, true));

                    $this->getProcessManager()->cancelQuote($quote_id, 'failed');

                    # Store an error notice and redirect the customer back to the checkout.
                    wc_add_notice( __( 'Your payment token has expired. Please try again.', 'woo_laybuy' ), 'error' );
                    if (wp_redirect( wc_get_checkout_url() )) {
                        exit;
                    }
                }

                if ($response->result !== Laybuy_ApiGateway::PAYMENT_STATUS_SUCCESS) {

                    WC_Laybuy_Logger::log("Failed while confirming the payment for a quote {$quote_id}");
                    WC_Laybuy_Logger::log("Request data: " . print_r(['token' => $this->getToken(), 'currency' => $currency, 'amount' => $amount], true));
                    WC_Laybuy_Logger::log("Response data: " . print_r($response, true));

                    $this->getProcessManager()->cancelQuote($quote_id, 'failed');

                    wc_add_notice( __( $response->error, 'woo_laybuy' ), 'error' );
                    wp_redirect( wc_get_checkout_url() );
                    exit;
                }

                WC_Laybuy_Logger::log("Payment confirmed. Quote {$quote_id}");
                WC_Laybuy_Logger::log("Response: " . print_r($response, true));

                $posted = $this->_decode(get_post_meta( $quote_id, 'posted', true ));
                $order = $this->getProcessManager()->createOrderFromQuote($quote_id);

                if( is_wp_error($order) ) {

                    wc_add_notice( 'Invalid order.', 'error' );

                    WC_Laybuy_Logger::error("Could not create order for quote {$quote_id}");

                    wp_redirect( wc_get_checkout_url() );
                    exit;
                }

                do_action( 'woocommerce_checkout_order_processed', $order->get_id(), $posted, $order );

                update_post_meta($order->get_id(), '_laybuy_order_id', $response->orderId );
                update_post_meta($order->get_id(), '_laybuy_token', $_GET['token'] );

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

                WC_Laybuy_Logger::log("Payment declined. Quote {$quote_id}");

                wc_add_notice( __( 'Your payment was declined.', 'woo_laybuy' ), 'error' );
                if (wp_redirect( wc_get_checkout_url() )) {
                    exit;
                }

                break;
            case Laybuy_ApiGateway::PAYMENT_STATUS_CANCELLED:

                $this->getProcessManager()->cancelQuote($_GET['quote_id']);

                WC_Laybuy_Logger::log("Payment canceled. Quote {$quote_id}");

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
}