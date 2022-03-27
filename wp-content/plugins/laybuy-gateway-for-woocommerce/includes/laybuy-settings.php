<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

# Process Region-based Assets
$assets					= 	include 'assets.php';
$currency				=	strtolower(get_woocommerce_currency());

if (!empty($assets[$currency])) {
    $region_assets = $assets[$currency];
} else {
    $region_assets = $assets['nzd'];
}

$product_page_asset  = $region_assets['product_page'];
$ctg_page_asset 	 = $region_assets['category_page'];
$cart_page_asset 	 = $region_assets['cart_page'];
$checkout_page_asset = $region_assets['checkout_page'];

$settings = array(
        'enabled' => array(
            'title'   => __( 'Enable/Disable', 'woo_laybuy' ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Laybuy', 'woo_laybuy' ),
            'default' => 'no'
        ),
        'title' => array(
            'title'       => __( 'Title', 'woo_laybuy' ),
            'type'        => 'text',
            'description' => __( 'This is the title for this payment method. The customer will see this during checkout.', 'woo_laybuy' ),
            'default'     => __( 'Laybuy', 'woo_laybuy' ),
            'desc_tip'    => true,
        ),
        'environment' => array(
            'title'       => __( 'Environment', 'woo_laybuy' ),
            'type'        => 'select',
            'description' => __( 'Select the sandbox environment for testing purposes only.', 'woo_laybuy' ),
            'default'     => 'production',
            'options'     => array(
                'sandbox' => __( 'Sandbox', 'woo_laybuy' ),
                'production' => __( 'Production', 'woo_laybuy' )
            ),
            'desc_tip'    => true,
        ),
        'currency' => array(
            'title'   => __( 'Default Currency' , 'woo_laybuy' ),
            'type'    => 'multiselect',
            'options'     => array(
                'NZD' => __( 'New Zealand Dollars', 'woo_laybuy' ),
                'AUD' => __( 'Australian Dollars', 'woo_laybuy' ),
                'GBP' => __( 'Great British Pounds', 'woo_laybuy' ),
                'USD' => __( 'United States Dollar', 'woo_laybuy' ),
            ),
            'label'   => __( 'Supported currencies', 'woo_laybuy' ),
            'class' => 'currency-select',
            'default'     => 'NZD',
        ),
        'global' => array(
            'title'   => __( 'Laybuy Global' , 'woo_laybuy' ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable', 'woo_laybuy' ),
            'default' => 'yes'
        ),
        // NZD
        "sandbox_NZD_merchant_id" => array(
            'title'       => __("NZD Merchant ID (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
            'label_class' => array('credentials-label'),
        ),
        "sandbox_NZD_api_key" => array(
            'title'       => __("NZD API Key (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_NZD_merchant_id" => array(
            'title'       => __("NZD Merchant ID (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_NZD_api_key" => array(
            'title'       => __("NZD API Key (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        // AUD
        "sandbox_AUD_merchant_id" => array(
            'title'       => __("AUD Merchant ID (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "sandbox_AUD_api_key" => array(
            'title'       => __("AUD API Key (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_AUD_merchant_id" => array(
            'title'       => __("AUD Merchant ID (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_AUD_api_key" => array(
            'title'       => __("AUD API Key (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        // GBP
        "sandbox_GBP_merchant_id" => array(
            'title'       => __("GBP Merchant ID (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "sandbox_GBP_api_key" => array(
            'title'       => __("GBP API Key (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_GBP_merchant_id" => array(
            'title'       => __("GBP Merchant ID (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_GBP_api_key" => array(
            'title'       => __("GBP API Key (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        // USD
        "sandbox_USD_merchant_id" => array(
            'title'       => __("USD Merchant ID (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "sandbox_USD_api_key" => array(
            'title'       => __("USD API Key (sandbox)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_USD_merchant_id" => array(
            'title'       => __("USD Merchant ID (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_USD_api_key" => array(
            'title'       => __("USD API Key (production)", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        // Global
        "sandbox_global_merchant_id" => array(
            'title'       => __("Global Merchant ID", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "sandbox_global_api_key" => array(
            'title'       => __("Global API Key", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_global_merchant_id" => array(
            'title'       => __("Global Merchant ID", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
        "production_global_api_key" => array(
            'title'       => __("Global API Key", 'woo_laybuy'),
            'type'        => 'text',
            'description' => __('This will be supplied by Laybuy.com', 'woo_laybuy'),
            'default'     => '',
            'desc_tip'    => TRUE,
        ),
);

if ($this->is_plus()) {

    $settings['NZD_pay_limit_min'] = array(
        'title'       => __("Limit Min [NZD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Min limit for the payments in NZD', 'woo_laybuy'),
        'default'     => '0.06',
        'desc_tip'    => TRUE,
    );
    $settings['NZD_pay_limit_max'] = array(
        'title'       => __("Limit Max [NZD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Max limit for the payments in NZD', 'woo_laybuy'),
        'default'     => '1500',
        'desc_tip'    => TRUE,
    );

    $settings['AUD_pay_limit_min'] = array(
        'title'       => __("Limit Min [AUD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Min limit for the payments in AUD', 'woo_laybuy'),
        'default'     => '0.06',
        'desc_tip'    => TRUE,
    );
    $settings['AUD_pay_limit_max'] = array(
        'title'       => __("Limit Max [AUD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Max limit for the payments in AUD', 'woo_laybuy'),
        'default'     => '1200',
        'desc_tip'    => TRUE,
    );

    $settings['GBP_pay_limit_min'] = array(
        'title'       => __("Limit Min [GBP]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Min limit for the payments in GBP', 'woo_laybuy'),
        'default'     => '0.06',
        'desc_tip'    => TRUE,
    );
    $settings['GBP_pay_limit_max'] = array(
        'title'       => __("Limit Max [GBP]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Max limit for the payments in GBP', 'woo_laybuy'),
        'default'     => '720',
        'desc_tip'    => TRUE,
    );

    $settings['USD_pay_limit_min'] = array(
        'title'       => __("Limit Min [USD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Min limit for the payments in USD', 'woo_laybuy'),
        'default'     => '0.06',
        'desc_tip'    => TRUE,
    );
    $settings['USD_pay_limit_max'] = array(
        'title'       => __("Limit Max [USD]", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Max limit for the payments in USD', 'woo_laybuy'),
        'default'     => '1200',
        'desc_tip'    => TRUE,
    );
}

$settings['debug'] =  array(
    'title'       => __('Debug', 'woo_laybuy'),
    'label'       => __('Enable verbose debug logging', 'woo_laybuy'),
    'type'        => 'checkbox',
    'description'		=>
        __( 'The Laybuy log is in the ', 'woo_laybuy' ) .
        '<code>wc-logs</code>' .
        __( ' folder, which is accessible from the ', 'woo_laybuy' ) .
        '<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' .
        __( 'WooCommerce System Status', 'woo_laybuy' ) .
        '</a>' .
        __( ' page.', 'woo_laybuy' ),
    'default'     => 'no',
);

$settings['presentational_customisation_title'] = array(
    'title'				=> __( 'Customisation', 'woo_laybuy' ),
    'type'				=> 'title',
    'description'		=> __( 'Please feel free to customise the presentation of the Laybuy elements below to suit the individual needs of your web store.</p><p><em>Note: Advanced customisations may require the assistance of your web development team.</em>', 'woo_laybuy' )
);

$product_types = [];
foreach (wc_get_product_types() as $value => $label) {
    $product_types[$value] = $label;
}

$settings['product_types'] = array(
    'title'	 => __( 'Displayed for Product Types', 'woo_laybuy' ),
    'type'    => 'multiselect',
    'description'	=> __( 'Select product types for which Laybuy widget is displayed', 'woo_laybuy' ),
    'options' => $product_types,
    'custom_attributes' => array(
        'size' => '4'
    ),
    'default'  => array_keys($product_types),
);

$settings += array(
    'show_info_on_category_pages' => array(
        'title'				=> __( 'Payment Info on Category Pages', 'woo_laybuy' ),
        'label'				=> __( 'Enable', 'woo_laybuy' ),
        'type'				=> 'checkbox',
        'description'		=> __( 'Enable to display Laybuy elements on category pages', 'woo_laybuy' ),
        'default'			=> 'yes'
    ),
    'category_pages_info_text' => array(
        'type'				=> 'wysiwyg',
        'default'			=> $ctg_page_asset,
        'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. Use [OF_OR_FROM] to insert "from" if the product\'s price is variable, or "of" if it is static.', 'woo_laybuy' )
    ),
    'show_info_on_product_pages' => array(
        'title'				=> __( 'Payment Info on Individual Product Pages', 'woo_laybuy' ),
        'label'				=> __( 'Enable', 'woo_laybuy' ),
        'type'				=> 'checkbox',
        'description'		=> __( 'Enable to display Laybuy elements on individual product pages', 'woo_laybuy' ),
        'default'			=> 'yes'
    ),
    'product_pages_info_text' => array(
        'type'				=> 'wysiwyg',
        'default'			=> $product_page_asset,
        'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. Use [OF_OR_FROM] to insert "from" if the product\'s price is variable, or "of" if it is static.', 'woo_laybuy' )
    ),

    'price_breakdown_option_product_page_position' => array(
        'title'       => __('Product Price breakdown Position', 'woo_laybuy'),
        'type'        => 'select',
        'description' => 'Select where on the Product page you would like the breakdown to display, see <a href="https://businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/" target="_blank">here</a> for a visual guide',
        'default'     => 'disable',
        'options'     => array(
            'woocommerce_single_product_summary'        => __('woocommerce_single_product_summary'    ),
            'woocommerce_before_add_to_cart_form'       => __('woocommerce_before_add_to_cart_form'   ),
            'woocommerce_before_variations_form'        => __('woocommerce_before_variations_form'    ),
            'woocommerce_before_add_to_cart_button'     => __('woocommerce_before_add_to_cart_button' ),
            'woocommerce_before_single_variation'       => __('woocommerce_before_single_variation'   ),
            'woocommerce_single_variation'              => __('woocommerce_single_variation'          ),
            'woocommerce_after_single_variation'        => __('woocommerce_after_single_variation'    ),
            'woocommerce_after_add_to_cart_button'      => __('woocommerce_after_add_to_cart_button'  ),
            'woocommerce_after_add_to_cart_form'        => __('woocommerce_after_add_to_cart_form'    ),
            'woocommerce_product_meta_start'            => __('woocommerce_product_meta_start'        ),
            'woocommerce_product_meta_end'              => __('woocommerce_product_meta_end'          ),
        ),
    ),

    'product_price_breakdown_hook_priority' => [
        'title'       => __("Product Price Breakdown Hook Priority", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Choose hook priority for the product price breakdown hook. Default is 11.', 'woo_laybuy'),
        'default'     => 11,
        'desc_tip'    => false,
    ],

    'checkout_page_info_text' => array(
        'title'				=> __( 'Payment Info on Checkout Page', 'woo_laybuy' ),
        'type'				=> 'wysiwyg',
        'default'			=> $checkout_page_asset,
        'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. In this case, the instalment amount will be calculated based on the grand total of the cart, including tax and shipping.', 'woo_laybuy' )
    ),
    'show_info_on_cart_page' => array(
        'title'				=> __( 'Payment Info on Cart Page', 'woo_laybuy' ),
        'label'				=> __( 'Enable', 'woo_laybuy' ),
        'type'				=> 'checkbox',
        'description'		=> __( 'Enable to display Laybuy elements on the cart page', 'woo_laybuy' ),
        'default'			=> 'yes'
    ),
    'cart_page_info_text' => array(
        'type'				=> 'textarea',
        'default'			=> $cart_page_asset,
        'description'		=> __( 'Use [AMOUNT] to insert the calculated instalment amount. In this case, the instalment amount will be calculated based on the grand total of the cart, including tax and shipping.', 'woo_laybuy' )
    ),
    'laybuy_fontsize_in_breakdowns' => array(
        'title'       => __('Font Size Breakdowns', 'woo_laybuy'),
        'type'        => 'select',
        'default'     => '12px',
        'description' => __('Select the Font Size in the breakdowns', 'woo_laybuy'),
        'options'     => array(
            '10px'  => __('10px', 'woo_laybuy'),
            '12px'  => __('12px', 'woo_laybuy'),
            '14px'  => __('14px', 'woo_laybuy'),
            '16px'  => __('16px', 'woo_laybuy'),
            '18px'  => __('18px', 'woo_laybuy'),
            '20px'  => __('20px', 'woo_laybuy'),
        )
    ),

    'laybuy_logo_theme' => array(
        'title'       => __('Laybuy Logo Theme', 'woo_laybuy'),
        'type'        => 'select',
        'default'     => 'White',
        'description' => __('Select the Laybuy logo theme in the breakdowns', 'woo_laybuy'),
        'options'     => array(
            'white'  => __('White', 'woo_laybuy'),
            'dark'  => __('Dark', 'woo_laybuy'),
        )
    ),

    'laybuy_currency_prefix_in_breakdowns' => array(
        'title'       => __('Show Currency code in Breakdowns', 'woo_laybuy'),
        'type'        => 'checkbox',
        'default'     => 'yes',
        'description' => __('Show the currency code (ie NZD) in the breakdowns', 'woo_laybuy'),
    ),
    'laybuy_page_enabled' => array(
        'title'       => __('Laybuy Page', 'woo_laybuy'),
        'label'		  => __( 'Enable', 'woo_laybuy' ),
        'type'        => 'checkbox',
        'default'     => 'yes',
        'description' => __('Enable /laybuy info page', 'woo_laybuy'),
    ),
    'laybuy_wide_layout_setting' => array(
        'title'       => __('Set the laybuy checkout to wide'),
        'type'        => 'checkbox',
        'default'     => 'no',
        'description' => __('Use this to display the Laybuy process in a single row, instead of two rows. Best suited for wide/single column checkout themes.', 'woo_laybuy'),
    ),
    'laybuy_compatibility_mode' => array(
        'title'       => __('Compatibility Mode'),
        'type'        => 'checkbox',
        'default'     => 'no',
        'description' => __('Use this mode only if experiencing challenges with the display of order data within the WooCommerce admin views for Laybuy orders.', 'woo_laybuy'),
    ),

    'laybuy_geolocate_ip' => array(
        'title'       => __('Geolocation IP'),
        'label'		  => __( 'Enable', 'woo_laybuy' ),
        'type'        => 'checkbox',
        'default'     => 'no',
        'description' => __('Enable geolocation to display Laybuy payment method only for supported countries (NZ, AU, GB).', 'woo_laybuy'),
    ),

    'laybuy_price_breakdown_out_of_stock' => array(
        'title'       => __('Price breakdown for "out of stock" products'),
        'label'		  => __( 'Enable', 'woo_laybuy' ),
        'type'        => 'checkbox',
        'default'     => 'no',
        'description' => __('Price breakdown snippet will appear on products that are "out of stock"', 'woo_laybuy'),
    ),

    'laybuy_billing_phone_field' => array(
        'title'       => __("Billing Phone Field Name", 'woo_laybuy'),
        'type'        => 'text',
        'description' => __('Override for custom checkout phone field name', 'woo_laybuy'),
        'default'     => 'billing_phone',
    ),

    'laybuy_advance_setting' => array(
        'title'       => __('Developer Mode (Advanced)', 'woo_laybuy'),
        'type'        => 'checkbox',
        'default'     => 'no',
        'description' => __('This is only for developers, be careful with this setting. You should not need to use this.', 'woo_laybuy'),
    ),
    'laybuy_send_support_request' => array(
        'title'       => __('', 'woo_laybuy'),
        'type'        => 'support_request_btn',
        'desc_tip'    => false,
        'description' => __('Submit email support request to Laybuy.', 'woo_laybuy'),
    ),
);

return apply_filters('woocommerce_laybuy_settings', $settings);