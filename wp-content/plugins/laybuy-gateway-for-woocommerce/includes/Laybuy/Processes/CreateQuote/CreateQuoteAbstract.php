<?php

abstract class Laybuy_Processes_CreateQuote_CreateQuoteAbstract extends Laybuy_Processes_AbstractProcess
{
    abstract public function setCheckout($checkout);
    abstract public function getCheckout();

    /**
     * Checking if the Cart Item Details is a Custom Field or normal / default WooCommerce Product Line Item structure.
     *
     * @since	2.0.4
     * @param	string $key 	The Key to be checked for Custom Field processing.
     * @return	bool			Whether or not the given key is a Custom Field (not WooCommerce Default structure).
     */
    protected function _isProductDetailCustom($key) {
        # Array of default values to exclude from the Custom Fields Handling
        $default_keys = 	array(

            'key',
            'variation_id',
            'variation',
            'quantity',
            'data_hash',
            'line_tax_data',
            'line_subtotal',
            'line_subtotal_tax',
            'line_total',
            'line_tax',
            'data'
        );

        return !in_array($key, $default_keys);
    }
}