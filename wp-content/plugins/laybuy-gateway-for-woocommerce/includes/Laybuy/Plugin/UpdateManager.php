<?php

class Laybuy_Plugin_UpdateManager
{
    public $pluginVersion;
    public $pluginDbVersion;

    public function setPluginVersion($pluginVersion)
    {
        $this->pluginVersion = $pluginVersion;
        return $this;
    }

    public function setPluginDbVersion($pluginDbVersion)
    {
        $this->pluginDbVersion = $pluginDbVersion;
        return $this;
    }

    public function update()
    {
        // update to 5.0.6
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.0.6', '<')) {
            $this->update_5_0_6();
        }

        //  update to 5.0.11
        if (!$this->pluginDbVersion || version_compare($this->pluginDbVersion, '5.0.11', '<')) {
            $this->update_5_0_11();
        }

        //  update to 5.0.13
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.0.13', '<')) {
            $this->update_5_0_13();
        }

        //  update to 5.1.0
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.1.0', '<')) {
            $this->update_5_1_0();
        }

        //  update to 5.1.5
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.1.5', '<')) {
            $this->update_5_1_5();
        }

        //  update to 5.1.11
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.1.11', '<')) {
            $this->update_5_1_11();
        }

        //  update to 5.2.4
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.2.4', '<')) {
            $this->update_5_2_4();
        }

        //  update to 5.2.5
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.2.5', '<')) {
            $this->update_5_2_5();
        }

        //  update to 5.2.6
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.2.6', '<')) {
            $this->update_5_2_6();
        }

        //  update to 5.2.8
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.2.8', '<')) {
            $this->update_5_2_8();
        }

        //  update to 5.2.9
        if ($this->pluginDbVersion && version_compare($this->pluginDbVersion, '5.2.9', '<')) {
            $this->update_5_2_9();
        }

        $this->fixCurrentPluginVersion();
    }

    public function update_5_0_11()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        if (empty($settings['product_pages_info_text'])) {
            $settings['product_pages_info_text'] = 'or 6 weekly interest-free payments from <strong>[AMOUNT]</strong> with <a href="#" id="laybuy-what-is-modal">[laybuy_logo] <span style="font-size: 12px"><u>what\'s this?</u></span></a>';
        }

        if (empty($settings['category_pages_info_text'])) {
            $settings['category_pages_info_text'] = 'or 6 weekly interest-free payments from <strong>[AMOUNT]</strong> with <span id="laybuy-what-is-modal" class="laybuy-cat-page" style="width:50px"> [laybuy_logo]</span>';
        }

        if (empty($settings['cart_page_info_text'])) {
            $settings['cart_page_info_text'] = '<tr><td colspan="2" style="font-size: 14px">or 6 weekly interest-free payments from <strong>[AMOUNT]</strong> with [laybuy_logo link=https://www.laybuy.com]<span id="laybuy-what-is-modal"><u style="font-size: 11px">what\'s this?</u></span></td></tr>';
        }

        if (empty($settings['checkout_page_info_text'])) {
            $settings['checkout_page_info_text'] = '<div class="laybuy-checkout-content "><p class="title"> Pay it in 6 weekly, interest-free payments from <strong>[AMOUNT]</strong></p><div class="laybuy-checkout-img"><img class="left-column" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_pay.jpg"><img src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_schedule.jpg"><img class="left-column second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_complete.jpg"><img class="second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_done.jpg"></div></div><div style="clear:both" />';
        }

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_0_6()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['sandbox_global_merchant_id']    = '';
        $settings['sandbox_global_api_key']        = '';
        $settings['production_global_merchant_id'] = '';
        $settings['production_global_api_key']     = '';
        $settings['global'] = 'yes';
        $settings['currency'] = [];

        $currencies = ['NZD', 'AUD', 'GBP', 'USD'];
        $activeCurrencies = [];
        $apiProductionKeys = [];
        $apiSandboxKeys = [];

        foreach ($currencies as $currency) {

            if (
                !empty($settings["production_{$currency}_api_key"]) ||
                !empty($settings["sandbox_{$currency}_api_key"])
            ) {
                $activeCurrencies[] = $currency;
            }

            if (!empty($settings["production_{$currency}_api_key"])) {
                $apiProductionKeys[$settings["production_{$currency}_merchant_id"]] = $settings["production_{$currency}_api_key"];
            }

            if (!empty($settings["sandbox_{$currency}_api_key"])) {
                $apiSandboxKeys[$settings["sandbox_{$currency}_merchant_id"]] = $settings["sandbox_{$currency}_api_key"];
            }
        }

        if (count(array_unique($apiProductionKeys)) == 1) {
            $settings['production_global_merchant_id'] = key($apiProductionKeys);
            $settings['production_global_api_key']     = $apiProductionKeys[key($apiProductionKeys)];
        }

        if (count(array_unique($apiSandboxKeys)) == 1) {
            $settings['sandbox_global_merchant_id'] = key($apiSandboxKeys);
            $settings['sandbox_global_api_key'] = $apiSandboxKeys[key($apiSandboxKeys)];
        }

        if (
            count(array_unique($apiProductionKeys)) > 1 ||
            count(array_unique($apiSandboxKeys)) > 1
        ) {
            $settings['global'] = 'no';
        }

        $settings['currency'] = $activeCurrencies;

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_0_13()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );
        $settings['cart_page_info_text'] = '<tr><td colspan="2" style="font-size: 14px">or 6 weekly interest-free payments from <strong>[AMOUNT]</strong> with [laybuy_logo link=https://www.laybuy.com]<span id="laybuy-what-is-modal"><u style="font-size: 11px">what\'s this?</u></span></td></tr>';
        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_1_0()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );
        $settings['laybuy_compatibility_mode'] = 'no';
        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_1_5()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['NZD_pay_limit_min'] = 0.06;
        $settings['NZD_pay_limit_max'] = 1500;

        $settings['AUD_pay_limit_min'] = 0.06;
        $settings['AUD_pay_limit_max'] = 1200;

        $settings['GBP_pay_limit_min'] = 0.06;
        $settings['GBP_pay_limit_max'] = 720;

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_1_11()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['product_price_breakdown_hook_priority'] = 11;

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_2_4()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['laybuy_geolocate_ip'] = 'no';
        $settings['laybuy_logo_theme'] = 'white';

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_2_5()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['sandbox_USD_merchant_id']    = '';
        $settings['sandbox_USD_api_key']        = '';
        $settings['production_USD_merchant_id'] = '';
        $settings['production_USD_api_key']     = '';

        $settings['USD_pay_limit_min'] = 0.06;
        $settings['USD_pay_limit_max'] = 1200;

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_2_6()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );
        $product_types = [];
        foreach (wc_get_product_types() as $value => $label) {
            $product_types[] = $value;
        }

        $settings['product_types'] = $product_types;
        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_2_8()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['laybuy_price_breakdown_out_of_stock'] = 'no';

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function update_5_2_9()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

        $settings['laybuy_billing_phone_field'] = 'billing_phone';

        update_option('woocommerce_laybuy_settings', $settings);
    }

    public function fixCurrentPluginVersion()
    {
        if (version_compare($this->pluginDbVersion, $this->pluginVersion, '<')) {
            update_option('wc_laybuy_version', $this->pluginVersion);
        }
    }
}