<?php

class Laybuy_ApiGateway
{
    const PRODUCTION_API_ENDPOINT = 'https://api.laybuy.com/';
    const SANDBOX_API_ENDPOINT = 'https://sandbox-api.laybuy.com/';

    const PAYMENT_STATUS_SUCCESS   = 'SUCCESS';
    const PAYMENT_STATUS_ERROR     = 'ERROR';
    const PAYMENT_STATUS_DECLINED  = 'DECLINED';
    const PAYMENT_STATUS_CANCELLED = 'CANCELLED';


    protected $api_endpoint;
    protected $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;

        if (!isset($settings['environment'])) {
            return;
        }

        if ($settings['environment'] == 'sandbox') {
            $this->api_endpoint = self::SANDBOX_API_ENDPOINT;
        } else {
            $this->api_endpoint = self::PRODUCTION_API_ENDPOINT;
        }
    }

    public function createOrder($data)
    {
        return $this->post_to_api($this->api_endpoint . 'order/create', $data);
    }

    public function confirmOrder($data)
    {
        return $this->post_to_api($this->api_endpoint . 'order/confirm', $data);
    }

    public function refund($data)
    {
        return $this->post_to_api($this->api_endpoint . 'order/refund', $data);
    }

    /**
     * Get the Merchant ID from our user settings.
     *
     * @since	2.0.0
     * @return	string
     */
    public function get_merchant_id() {

        if ($this->isGlobal()) {
            return $this->settings["{$this->settings['environment']}_global_merchant_id"];
        }

        $currency = get_woocommerce_currency();

        if (in_array($currency, $this->settings['currency'])) {
            return $this->settings["{$this->settings['environment']}_{$currency}_merchant_id"];
        }

        return false;
    }

    /**
     * Get the Secret Key from our user settings.
     *
     * @since	2.0.0
     * @return	string
     */
    public function get_api_key() {

        if ($this->isGlobal()) {
            return $this->settings["{$this->settings['environment']}_global_api_key"];
        }

        $currency = get_woocommerce_currency();

        if (in_array($currency, $this->settings['currency'])) {
            return $this->settings["{$this->settings['environment']}_{$currency}_api_key"];
        }

        return false;
    }

    /**
     * POST to an API endpoint and load the response.
     */
    public function post_to_api($url, $data) {

        WC_Laybuy_Logger::log("POST {$url}");

        $response = wp_remote_post( $url, array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => $this->build_authorization_header(),
                'User-Agent' => $this->build_user_agent_header(),
                'Content-Type' => 'application/json',
                'Accepts' => 'application/json'
            ),
            'body' => json_encode($data)
        ) );

        if (!is_wp_error( $response )) {
            $body = json_decode(wp_remote_retrieve_body( $response ));

            if (!is_null($body)) {
                return $body;
            }

            WC_Laybuy_Logger::log("Failed to parse Laybuy response: " . var_export( $response, true));
        } else {
            # Unable to establish a secure connection with the Laybuy API endpoint.
            # Likely a TLS or network error.
            # Log the error details.
            foreach ($response->errors as $code => $messages_arr) {
                $messages_str = implode("\n", $messages_arr);
                WC_Laybuy_Logger::error("API NETWORK ERROR! Code: \"{$code}\"; Message(s):\n" . $messages_str);
            }

            # Get CloudFlare Header for the error
            $cf_ray = wp_remote_retrieve_header($response, "cf-ray");

            if (!empty($cf_ray)) {
                WC_Laybuy_Logger::error("Error CF-Ray: " .  $cf_ray);
            }
            else {
                WC_Laybuy_Logger::error("No CF-Ray Detected");
            }

            # Return the WP_Error object.
            return $response;
        }

        return false;
    }


    public function build_authorization_header() {
        return 'Basic ' . base64_encode($this->get_merchant_id() . ':' . $this->get_api_key());
    }

    /**
     * Build the Laybuy User-Agent header for use with the APIs.
     */
    private function build_user_agent_header() {
        global $wp_version;

        $plugin_version = WC_Laybuy::$version;
        $php_version = PHP_VERSION;
        $woocommerce_version = WC()->version;
        $merchant_id = $this->get_merchant_id();

        $extra_detail_1 = '';
        $extra_detail_2 = '';

        $matches = array();
        if (array_key_exists('SERVER_SOFTWARE', $_SERVER) && preg_match('/^[a-zA-Z0-9]+\/\d+(\.\d+)*/', $_SERVER['SERVER_SOFTWARE'], $matches)) {
            $s = $matches[0];
            $extra_detail_1 .= "; {$s}";
        }

        if (array_key_exists('REQUEST_SCHEME', $_SERVER) && array_key_exists('HTTP_HOST', $_SERVER)) {
            $s = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
            $extra_detail_2 .= " {$s}";
        }

        return "Laybuy Gateway for WooCommerce/{$plugin_version} (PHP/{$php_version}; WordPress/{$wp_version}; WooCommerce/{$woocommerce_version}; Merchant/{$merchant_id}{$extra_detail_1}){$extra_detail_2}";
    }

    protected function isGlobal()
    {
        return $this->settings['global'] == 'yes';
    }
}