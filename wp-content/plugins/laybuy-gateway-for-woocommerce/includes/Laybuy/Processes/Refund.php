<?php

class Laybuy_Processes_Refund extends Laybuy_Processes_AbstractProcess
{
    public $orderId;
    public $amount;
    public $reason;

    public function setOrderId($orderId)
    {
        $this->orderId = (int) $orderId;
        return $this;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function execute()
    {
        $order_id = $this->getOrderId();
        $amount   = $this->getAmount();
        $reason   = $this->getReason();

        WC_Laybuy_Logger::log("Refunding WooCommerce Order #{$order_id} for \${$amount}...");

        if (function_exists('wc_get_order')) {
            $order = wc_get_order( $order_id );
        } else {
            $order = new WC_Order( $order_id );
        }

        $laybuy_token = get_post_meta($order_id, '_laybuy_token', TRUE);
        $laybuy_order_id = get_post_meta( $order_id, '_laybuy_order_id', true );

        if (!$laybuy_token) {

            WC_Laybuy_Logger::error("Refund order {$order_id} failed:\n | Token not found");

            $ret['error'] = 'Token not found error';
            return $ret;
        }

        $response = $this->getProcessManager()->getApiGateway()->refund([
                'orderId' => $laybuy_order_id,
                'token' => $laybuy_token,
                'amount' => $amount,
                'refundReference' => $this->_makeUniqueReference($order_id),
            ]
        );

        if( is_wp_error( $response ) ) {

            WC_Laybuy_Logger::error("Failed to refund the order {$order_id}. " . print_r($response, true));

            $order->update_status( 'failed', __( $response->get_error_message(), 'woocommerce_laybuy' ) );
            wc_add_notice( __( 'Failed to refund the order, please try again, Error message: ', 'woocommerce_laybuy' ) . $response->get_error_message(), 'error' );

            return $response;
        }

        if ('ERROR' === $response->result) {

            $order->update_status( 'failed', __( $response->error, 'woocommerce_laybuy' ) );
            $order->add_order_note( __( "Failed to send refund of \${$amount} to Laybuy.", 'woo_laybuy' ) );

            wc_add_notice( __( "Failed to refund order {$order_id}", 'woocommerce_laybuy' ) . $response->error, 'error' );

            return false;
        }

        WC_Laybuy_Logger::log("Successfully refunded {$amount} for order {$order_id}. " . print_r($response, true));

        $order->add_order_note( __( "Refunded \${$amount} via Laybuy (Refund ID: {$response->refundId}). Reason: {$reason}", 'woo_laybuy' ) );
        update_post_meta( $order_id, '_laybuy_refund_id', $response->refundId );

        return true;
    }
}