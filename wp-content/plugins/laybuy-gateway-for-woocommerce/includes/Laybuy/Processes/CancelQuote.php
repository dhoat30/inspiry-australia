<?php

class Laybuy_Processes_CancelQuote extends Laybuy_Processes_AbstractProcess
{
    public $quoteId;
    public $status;

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

    public function execute()
    {
        global $wpdb;

        $quote_id = $this->getQuoteId();
        $status = $this->getStatus();

        WC_Laybuy_Logger::log("Cancelling WP Quote with ID:{$quote_id}");

        # Mark the quote as cancelled.
        update_post_meta( $quote_id, 'status', $status );

        # Don't use `wp_trash_post` or `wp_delete_post`
        # because we don't want any hooks to fire.

        WC_Laybuy_Logger::log("Running DB query: 'DELETE FROM `{$wpdb->postmeta}` WHERE `post_id` = $quote_id'");
        WC_Laybuy_Logger::log($wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->postmeta}` WHERE `post_id` = %d", $quote_id ) ) . " row(s) deleted from `{$wpdb->postmeta}` table.");

        WC_Laybuy_Logger::log("Running DB query: 'DELETE FROM `{$wpdb->posts}` WHERE `ID` = $quote_id LIMIT 1'");
        WC_Laybuy_Logger::log($wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->posts}` WHERE `ID` = %d LIMIT 1", $quote_id ) ) . " row(s) deleted from `{$wpdb->posts}` table.");

        return true;
    }
}