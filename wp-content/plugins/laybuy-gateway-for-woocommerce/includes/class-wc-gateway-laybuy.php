<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Payment_Gateway_Laybuy extends WC_Payment_Gateway
{
    const GATEWAY_NAME = 'laybuy';
    const PAYMENTS_COUNT = 6;

    const COOKIE_GEOLOCATION_COUNTRY = 'laybuy_geo_country';

    const LOGO_THEME_WHITE = 'white';
    const LOGO_THEME_DARK  = 'dark';

    protected $supported_currencies;

    protected $pay_over_time_limit_max = 1200;
    protected $pay_over_time_limit_min = 0.06;

    protected $pay_limits_max = [
        'NZD' => 1500,
        'AUD' => 1200,
        'GBP' => 720,
        'USD' => 1200,
    ];

    protected $compatibility_mode = false;

    protected $assets = [];

    protected $apiGateway;

    protected static $instance;

    const MODE_BASIC = 'basic';
    const MODE_PLUS  = 'plus';

    protected $mode = self::MODE_BASIC;

    public function __construct() {

        $this->id = self::GATEWAY_NAME;
        $this->icon = apply_filters( 'woocommerce_laybuy_gateway_icon', 'https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_logo_small.svg' );
        $this->has_fields   = false;
        $this->method_title = 'Laybuy';
        $this->method_description = __( 'Use Laybuy as a credit card processor for WooCommerce.', 'woo_laybuy' );

        $this->supported_currencies = array(
            'AUD', 'NZD', 'GBP', 'USD'
        );

        if (defined('WC_LAYBUY_PLUS')) {
            $this->mode = self::MODE_PLUS;
        }

        // Get setting values.
        $this->title         = $this->get_option( 'title' );
        $this->description   = 'Pay by Laybuy';
        $this->enabled       = $this->get_option( 'enabled' ) === 'yes';

        $this->assets = include 'assets.php';

        $currency =	get_woocommerce_currency();

        if (!empty($this->assets[strtolower($currency)])) {
            $this->assets =	$this->assets[strtolower($currency)];
        } else {
            $this->assets = $this->assets['aud'];
        }

        $this->supports	= array('products', 'refunds');

        $this->init_form_fields();
        $this->init_settings();

        if ($this->enabled === 'no') {
            return;
        }

        $currency = get_woocommerce_currency();
        if (in_array($currency, $this->supported_currencies)) {
            if ($this->is_plus()) {

                $this->pay_over_time_limit_min = floatval($this->settings["${currency}_pay_limit_min"]);
                $this->pay_over_time_limit_max = floatval($this->settings["${currency}_pay_limit_max"]);

                $this->check_pay_over_time_limits();

            } else {
                $this->pay_over_time_limit_max = $this->pay_limits_max[$currency];
            }
        }

        if (!is_admin()) {
            $this->compatibility_mode = $this->settings['laybuy_compatibility_mode'] == 'yes';
        }

        $this->apiGateway = new Laybuy_ApiGateway($this->settings);

        if (array_key_exists('debug', $this->settings)) {
            WC_Laybuy_Logger::$enabled = $this->settings['debug'] === 'yes';
        }
    }

    /**
     * Instantiate the class if no instance exists. Return the instance.
     *
     * @since	2.0.0
     * @return	WC_Payment_Gateway_Laybuy
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = require( dirname( __FILE__ ) . '/laybuy-settings.php' );
    }

    /**
     * Is currency enabled for laybuy
     * @return	bool
     */
    public function is_currency_enabled($currency) {
        return in_array($currency, (array) @$this->settings['currency']);
    }

    public function payment_fields() {
        if ($description = $this->get_description()) {
            $description = apply_filters('checkout_modify_description', $description, $this->get_order_total());
            echo wpautop(wptexturize($description));
        }
    }

    /**
     * Process a refund if supported.
     *
     * Note:	This overrides the method defined in the parent class.
     *
     * @since	1.0.0
     * @see		WC_Payment_Gateway::process_refund()		For the method that this overrides.
     * @param	int			$order_id
     * @param	float		$amount							Optional. The amount to refund. This cannot exceed the total.
     * @param	string		$reason							Optional. The reason for the refund. Defaults to an empty string.
     * @return	bool
     */
    public function process_refund($order_id, $amount = null, $reason = '') {

        $order_id = (int) $order_id;

        if (function_exists('wc_get_order')) {
            $order = wc_get_order( $order_id );
        } else {
            $order = new WC_Order( $order_id );
        }

        if (method_exists($this, 'can_refund_order') && !$this->can_refund_order($order)) {
            WC_Laybuy_Logger::error('Refund Failed - No Transaction ID.');
            return false;
        }

        $processManager = Laybuy_ProcessManager::getInstance();
        $processManager->setApiGateway($this->apiGateway)
            ->setWcGateway($this);

        return $processManager->refund($order_id, $amount, $reason);
    }

    public function generate_wysiwyg_html($key, $data) {
        $html = '';

        $id = str_replace('-', '', $key);
        $class = array_key_exists('class', $data) ? $data['class'] : '';
        $css = array_key_exists('css', $data) ? ('<style>' . $data['css'] . '</style>') : '';
        $name = "{$this->plugin_id}{$this->id}_{$key}";
        $title = array_key_exists('title', $data) ? $data['title'] : '';
        $value = array_key_exists($key, $this->settings) ? esc_attr( $this->settings[$key] ) : '';
        $description = array_key_exists('description', $data) ? $data['description'] : '';

        ob_start();

        include dirname( __FILE__ ) . '/admin/wysiwyg.php';

        $html = ob_get_clean();

        return $html;
    }

    /**
     * Process the HTML from one of the rich text editors and output the converted string.
     */
    private function process_and_print_laybuy_paragraph($output_filter, $product = null) {

        if (is_admin()) {
            return;
        }

        if (is_null($product)) {
            $product = $this->get_product_from_the_post();
        }

        if (!$this->is_product_supported($product, true)) {
            # Don't display anything on the product page if the product is not supported when purchased on its own.
            return;
        }

        if (!$this->is_currency_supported()) {
            # Don't display anything on the product page if the website currency is not within supported currencies.
            return;
        }

        if (!$product->is_in_stock() && $this->settings['laybuy_price_breakdown_out_of_stock'] == 'no') {
            return;
        }

        $of_or_from = 'of';
        $price = $this->getProductPrice($product);
        $price = floatval($price);

        if (!$price) {
            return '';
        }

        if (!$this->is_product_within_limits($product, true) && $output_filter == 'laybuy_html_on_product_thumbnails') {

            if ($this->is_plus() && $price < $this->pay_over_time_limit_min) {
                $html = $this->get_plus_limits_html('product_thumbnails_');
            } else {
                $html = $this->assets['category_page_pay_over_limit'];
            }

            $html = str_replace(array(
                '[PAY_TODAY]',
                '[AMOUNT]',
                '[MIN_PRICE]',
                '[MAX_PRICE]',
            ), array(
                $this->display_price_html( $price - $this->pay_over_time_limit_max ),
                $this->display_price_html( $this->pay_over_time_limit_max / 5 ),
                wc_price( $this->pay_over_time_limit_min, ['decimals' => 0] ),
                wc_price( $this->pay_over_time_limit_max, ['decimals' => 0] ),
            ), $html);

        } else {

            $amount = $this->display_price_html( round($price / self::PAYMENTS_COUNT, 2) );

            $html = str_replace(array(
                '[OF_OR_FROM]',
                '[AMOUNT]'
            ), array(
                $of_or_from,
                $amount
            ), $this->settings['category_pages_info_text']);
        }

        # Execute shortcodes on the string after running internal replacements,
        # but before applying filters and rendering.
        $html = do_shortcode( "<p class=\"laybuy-payment-info\">{$html}</p>" );

        # Add the Modal Window to the page
        # Website Admin have no access to the Modal Window codes for data integrity reasons
        //$html = $this->apply_modal_window($html);

        # Allow other plugins to maniplulate or replace the HTML echoed by this funtion.
        echo apply_filters( $output_filter, $html, $product, $price );
    }

    /**
     * Print a paragraph of Laybuy info onto each product item in the shop loop if enabled and the product is valid.
     *
     * Note:	Hooked onto the "woocommerce_after_shop_loop_item_title" Action.
     */
    public function print_info_for_listed_products($product = null) {

        if (!$this->isAllowCurrencyForLaybuy()) {
            return;
        }

        if( is_single()) {

            if (!isset($this->settings['show_info_on_product_pages'])
                || $this->settings['show_info_on_product_pages'] != 'yes'
                || empty($this->settings['category_pages_info_text'])
                || !empty($product) && (!$product->is_in_stock() && $this->settings['laybuy_price_breakdown_out_of_stock'] == 'no') ) {
                # Don't display anything on Single product page unless the Display on Individual Page is enabled
                # The Variant selected is in stock
                return;
            }
        }
        else { #Category Pages

            if (!isset($this->settings['show_info_on_category_pages'])
                || $this->settings['show_info_on_category_pages'] != 'yes'
                || empty($this->settings['category_pages_info_text'])) {
                # Don't display anything on product items within the shop loop unless
                # the "Payment info on product listing pages" box is ticked
                # and there is a message to display.
                return;
            }

            $this->process_and_print_laybuy_paragraph(
                'laybuy_html_on_product_thumbnails',
                $product
            );
        }
    }

    /**
     * Render Laybuy elements (logo and payment schedule) on Cart page.
     *
     * This is dependant on all of the following criteria being met:
     *		- The Laybuy Payment Gateway is enabled.
     *		- The cart total is valid and within the merchant payment limits.
     *		- The "Payment Info on Cart Page" box is ticked and there is a message to display.
     *		- All of the items in the cart are considered eligible to be purchased with Laybuy.
     *
     * Note:	Hooked onto the "woocommerce_cart_totals_after_order_total" Action.
     */
    public function render_schedule_on_cart_page() {

        $total = WC()->cart->total;

        if (!array_key_exists('enabled', $this->settings) || $this->settings['enabled'] != 'yes') {
            return;
        } else {

            if ($total <= 0 ) {
                return;
            }
        }
        if(!$this->isAllowCurrencyForLaybuy()) {
            return;
        }

        if (
            !isset($this->settings['show_info_on_cart_page']) ||
            $this->settings['show_info_on_cart_page'] != 'yes' ||
            empty($this->settings['cart_page_info_text'])
        ) {
            return;
        }

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            if (!$this->is_product_supported($product)) {
                return;
            }
        }


        if ($this->is_plus() && $total < $this->pay_over_time_limit_min) {

            $html = '<tr><td colspan="2">' . $this->get_plus_limits_html('cart_') . '<td/><tr/>';
            $html = str_replace(array(
                '[MIN_PRICE]',
                '[MAX_PRICE]',
            ), array(
                wc_price( $this->pay_over_time_limit_min, ['decimals' => 0] ),
                wc_price( $this->pay_over_time_limit_max, ['decimals' => 0] ),
            ), $html);

        } else if ( $this->pay_over_time_limit_max && $total > $this->pay_over_time_limit_max) {

            $fallback_asset = $this->assets['cart_pay_over_limit_asset'];
            $html = '<tr><td colspan="2">' . $fallback_asset . '<td/><tr/>';

            $html = str_replace(array(
                '[PAY_TODAY]',
                '[AMOUNT]'
            ), array(
                $this->display_price_html( $total - max($this->pay_over_time_limit_min, $this->pay_over_time_limit_max) ),
                $this->display_price_html( max($this->pay_over_time_limit_min, $this->pay_over_time_limit_max) / 5 )
            ), $html);
        } else {
            $schedule = $this->calculation(WC()->cart->total);
            $amount = $this->display_price_html($schedule['minimum_today']);

            $html = str_replace(array(
                '[AMOUNT]',
            ), array(
                $amount,
            ), $this->settings['cart_page_info_text']);
        }

        # Execute shortcodes on the string before applying filters and rendering it.
        $html = do_shortcode( $html );

        # Add the Modal Window to the page
        # Website Admin have no access to the Modal Window codes for data integrity reasons
        // $html = $this->apply_modal_window($html);

        # Allow other plugins to maniplulate or replace the HTML echoed by this funtion.
        echo apply_filters( 'laybuy_html_on_cart_page', $html );
    }


    /**
     * Convert the global $post object to a WC_Product instance.
     */
    private function get_product_from_the_post() {
        global $post;

        if (function_exists('wc_get_product')) {
            $product = wc_get_product( $post->ID );
        } else {
            $product = new WC_Product( $post->ID );
        }

        return $product;
    }

    /**
     * Is the given product supported by the Laybuy gateway?
     */
    private function is_product_supported($product, $alone = false) {

        if (!isset($this->settings['enabled']) || $this->settings['enabled'] != 'yes') {
            return false;
        }

        $productTypes = (array) $this->settings['product_types'];

        if (false !== array_search('variable', $productTypes)) {
            $productTypes[] = 'variation';
        }

        if (!empty($productTypes) && !in_array($product->get_type(), $productTypes)) {
            return false;
        }

        # Allow other plugins to exclude Laybuy from products that would otherwise be supported.
        return (bool)apply_filters( 'laybuy_is_product_supported', true, $product, $alone );
    }

    private function is_product_within_limits($product, $alone = false) {

        if ($this->is_plus()) {
            return $this->is_product_within_limits_plus($product);
        }

        $price = $this->getProductPrice($product);

        if ( $price < $this->pay_over_time_limit_min || $price > $this->pay_over_time_limit_max ) {
            return false;
        }

        if ( $alone && $price < $this->pay_over_time_limit_min) {
            # If the product is viewed as being on its own and priced lower that the merchant's minimum, it will be considered as not supported.
            return false;
        }

        return true;
    }

    private function is_product_within_limits_plus($product)
    {
        $price = $this->getProductPrice($product);

        if ($this->pay_over_time_limit_min && $this->pay_over_time_limit_max) {
            return $price >= $this->pay_over_time_limit_min && $price <= $this->pay_over_time_limit_max;
        }

        if (!$this->pay_over_time_limit_min && $this->pay_over_time_limit_max) {
            return $price <= $this->pay_over_time_limit_max;
        }

        if ($this->pay_over_time_limit_min && !$this->pay_over_time_limit_max) {
            return $price >= $this->pay_over_time_limit_min;
        }

        return true;
    }

    /**
     * Is the the website currency supported by the Laybuy gateway?
     */
    private function is_currency_supported() {
        $store_currency = strtoupper(get_woocommerce_currency());
        return in_array($store_currency, $this->supported_currencies);
    }

    private function display_price_html($price) {
        if (function_exists('wc_price')) {
            return wc_price($price);
        } elseif (function_exists('woocommerce_price')) {
            return woocommerce_price($price);
        }
    }

    /**
     * Provide a shortcode for rendering the standard Laybuy logo on individual product pages.
     *
     * E.g.:
     * 	- [laybuy_product_logo]
     *
     * @return	string
     */
    public function shortcode_laybuy_logo($atts) {

        $atts = shortcode_atts( array(
            'width' => '80px',
            'link' => '',
            'style' => 'float:none; display:inline-block; vertical-align:middle; height:1.2em; top: -2px; position: relative;'
        ), $atts );

        $logoUrl = 'https://integration-assets.laybuy.com/woocommerce_laybuy_icons/';

        if ($this->settings['laybuy_logo_theme'] === self::LOGO_THEME_WHITE) {
            $logoUrl .= 'laybuy_logo_small.svg';
        } else {
            $logoUrl .= 'laybuy_logo_small_white.svg';
        }

        if (!empty($atts['link'])) {
            return <<<LOGO
<a href = "{$atts['link']}" target = "_blank" class="laybuy-logo-link" ><img style = "{$atts['style']}" src="{$logoUrl}" alt = "Laybuy" /></a >
LOGO;
        } else {

            return <<<LOGO
<img src="{$logoUrl}" alt = "Laybuy" />
LOGO;
        }
    }

    public function shortcode_laybuy_iframe_src() {

        $currency = get_woocommerce_currency();

        switch ($currency) {
            case 'AUD' :
                $code = 'au';
                break;
            case 'GBP' :
                $code = 'gb';
                break;
            case 'USD' :
                $code = 'us';
                break;
            default :
                $code = 'nz';
                break;
        }
        return 'https://integration-assets.laybuy.com/laybuy-cms-page/dist/index_' . $code . '.html';
    }

    public function product_price_breakdown() {

        global $product;

        if (
            !is_product() ||
            (!$product->is_in_stock() && $this->settings['laybuy_price_breakdown_out_of_stock'] == 'no') ||
            !$this->is_product_supported($product, true)
        ) {
            return;
        }

        $settings = get_option( 'woocommerce_laybuy_settings', true );

        $font_size  = (strlen($settings['laybuy_fontsize_in_breakdowns']) == 4) ? $settings['laybuy_fontsize_in_breakdowns'] : '12px';
        $logo_width = '80px';

        if ($font_size === '14px') {
            $logo_width = '90px';
        }
        elseif ($font_size === '16px') {
            $logo_width = '100px';
        }
        elseif ($font_size === '18px') {
            $logo_width = '110px';
        }
        elseif ($font_size === '20px') {
            $logo_width = '120px';
        }

        $price = floatval($this->getProductPrice($product));

        if (!$this->isAllowCurrencyForLaybuy() || empty($price)) {
            return;
        }

        $payment_breakdown = $this->calculation($price);
        $weekly_payment = wc_price($payment_breakdown['weekly_payment']);

        if (!$price) {
            return '';
        }

        if ($this->is_plus() && $price < $this->pay_over_time_limit_min) {

            $html = str_replace(array(
                '[MIN_PRICE]',
                '[MAX_PRICE]',
            ), array(
                wc_price( $this->pay_over_time_limit_min, ['decimals' => 0]),
                wc_price( $this->pay_over_time_limit_max, ['decimals' => 0])
            ), $this->get_plus_limits_html());

            echo '<p class="laybuy-inline-widget">' . do_shortcode($html) . '</p>';

            return;
        }

        if (!$this->is_product_within_limits($product)) {

            $html = $this->assets['product_page_pay_over_limit'];

            $html = str_replace(array(
                '[PAY_TODAY]',
                '[AMOUNT]'
            ), array(
                $this->display_price_html( $price - $this->pay_over_time_limit_max ),
                $this->display_price_html( $this->pay_over_time_limit_max / 5 )
            ), $html);

            $html_breakdown = '<p class="laybuy-inline-widget">' . do_shortcode($html) . '</p>';
            echo $html_breakdown;

            return;
        }


        $html_breakdown = '<p class="laybuy-inline-widget">';
        $html_breakdown .= str_replace(array(
            '[AMOUNT]',
            '[LOGO_WIDTH]'
        ), array(
            $weekly_payment,
            $logo_width
        ), do_shortcode( $this->settings['product_pages_info_text'] ));

        $html_breakdown .= '</p>';

        echo $html_breakdown;
    }

    public function isAllowCurrencyForLaybuy() {
        return in_array(get_woocommerce_currency(), $this->settings['currency']);
    }

    public function checkout_modify_description( $description, $total ) {

        $settings = get_option( 'woocommerce_laybuy_settings', true );

        if ($total < $this->pay_over_time_limit_min) {
            return '';
        }

        $payment_breakdown = $this->calculation( $total );
        $currencyCode = $settings['laybuy_currency_prefix_in_breakdowns'] === 'yes';

        $price_prefix = '';

        if ($currencyCode) {
            $price_prefix = get_woocommerce_currency() . ' ';
        }
        if(!$this->isAllowCurrencyForLaybuy()) {
            return;
        }

        $weekly_payment = $price_prefix . wc_price( $payment_breakdown['weekly_payment'] );

        if ($this->pay_over_time_limit_max && $total > $this->pay_over_time_limit_max) {

            if (isset($settings['laybuy_wide_layout_setting']) && $settings['laybuy_wide_layout_setting'] == "yes") {
                $html = '<div class="laybuy-checkout-content "><p class="title">Pay <strong>[PAY_TODAY]</strong> today & 5 weekly interest-free payments of <strong>[AMOUNT]</strong></p><div class="laybuy-checkout-img"><img style="width:25% !important" class="left-column" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_pay.jpg" /><img style="width:25% !important" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_schedule.jpg" /><img style="width:25% !important" class="left-column second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_complete.jpg" /><img style="width:25% !important" class="second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_done.jpg" /></div>
</div><div style="clear: both;"></div>';
            } else {
                $html = $this->assets['checkout_page_pay_over_limit'];
            }

            $html = str_replace(array(
                '[PAY_TODAY]',
                '[AMOUNT]'
            ), array(
                $this->display_price_html( $total - $this->pay_over_time_limit_max ),
                $this->display_price_html( $this->pay_over_time_limit_max / 5 )
            ), $html);

        } else {
            if (isset($settings['laybuy_wide_layout_setting']) && $settings['laybuy_wide_layout_setting'] == "yes") {
                $html = '<div class="laybuy-checkout-content "><p class="title">Pay it in 6 weekly, interest-free payments from <strong>[AMOUNT]</strong></p><div class="laybuy-checkout-img"><img style="width:25% !important" class="left-column" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_pay.jpg" /><img style="width:25% !important" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_schedule.jpg" /><img style="width:25% !important" class="left-column second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_complete.jpg" /><img style="width:25% !important" class="second-row" src="https://integration-assets.laybuy.com/woocommerce_laybuy_icons/laybuy_done.jpg" /></div>
</div><div style="clear: both;"></div>';
            } else {
                $html = do_shortcode($this->settings['checkout_page_info_text']);
            }
        }

        $html = nl2br($html);

        if (!$html) {
            return '';
        }

        $html_breakdown = '<p class="laybuy-checkout-widget">';
        $html_breakdown .= str_replace(array(
            '[AMOUNT]'
        ), array(
            $weekly_payment
        ), $html);

        $html_breakdown .= '</p>';

        return $html_breakdown;
    }

    public function calculation($dividend, $divisor = self::PAYMENTS_COUNT) {

        // multiplying it to 100 makes is having a better precision.
        // stripe use this
        $dividend = $dividend * 100;

        if( 0 < ($dividend % $divisor) ) {
            // get weeklys
            $weekly_payment = intval( $dividend / $divisor );

            // get minimum payment
            $minimum_today = intval( $dividend - ($weekly_payment * 5) );

            $calculation = array(
                'minimum_today' => $minimum_today * 0.01,
                'weekly_payment' => $weekly_payment * 0.01
            );
        } else {
            $even_weekly_payment = $dividend / $divisor;

            $calculation = array(
                'minimum_today' => $even_weekly_payment * 0.01,
                'weekly_payment' => $even_weekly_payment * 0.01
            );

        }

        return $calculation;
    }

    /**
     * Processses the orders that are redirected.
     *
     * @since 4.0.0
     * @version 4.0.0
     */
    public function process_redirect_order() {

        if (
            !isset($_GET['gateway_id']) ||
            constant('WC_GATEWAY_LAYBUY_ID') !== $_GET['gateway_id'] ||
            !isset($_GET['post_type']) ||
            !isset($_GET['quote_id'])
        ) {
            return;
        }

        $quote_id = wc_clean( $_GET['quote_id'] );
        $status   = strtoupper($_GET['status']);
        $token    = $_GET['token'];

        $processManager = Laybuy_ProcessManager::getInstance();
        $processManager->setApiGateway($this->apiGateway)
            ->setWcGateway($this)
            ->setCompatibilityMode($this->compatibility_mode);

        $processManager->processRedirectPayment($quote_id, $status, $token);
    }

    /**
     * Create order - Part 1 of 2.
     *
     * Override WooCommerce's create_order function and make our own order-quote object. We will manually
     * convert this into a proper WC_Order object later, if the checkout completes successfully. Part of the data
     * collected here is submitted to the Laybuy API to generate a token, the rest is persisted to the
     * database to build the WC_Order object. Based on WooCommerce 2.6.8.
     *
     * Note:	This needs to follow the WC_Checkout::create_order() method very closely. In order to properly
     * 			create the WC_Order object later, we need to make sure we're storing all of the data that will be
     * 			needed later. If it fails, it needs to return an integer that evaluates to true in order to bypass the
     * 			standard WC_Order creation process.
     *
     * Note:	Hooked onto the "woocommerce_create_order" Filter.
     *
     */
    public function create_order_quote($null, $checkout) {

        if ($this->compatibility_mode) {
            return;
        }

        $processManager = Laybuy_ProcessManager::getInstance();
        $processManager->setApiGateway($this->apiGateway)
            ->setWcGateway($this)
            ->createQuote($checkout);
    }

    public function process_payment($order_id)
    {
        return Laybuy_ProcessManager::getInstance()
            ->setApiGateway($this->apiGateway)
            ->setWcGateway($this)
            ->createOrder($order_id);
    }

    /**
     * If calling wc_create_order() for an Laybuy Quote, tell wp_insert_post() to reuse the ID of the quote.
     */
    public function filter_woocommerce_new_order_data( $order_data ) {
        if (array_key_exists('laybuy_quote_id', $GLOBALS) && is_numeric($GLOBALS['laybuy_quote_id']) && $GLOBALS['laybuy_quote_id'] > 0) {
            $order_data['import_id'] = (int) $GLOBALS['laybuy_quote_id'];
            unset($GLOBALS['laybuy_quote_id']);
        }
        return $order_data;
    }

    /**
     * Checks to see if all criteria is met before showing payment method.
     *
     * @return bool
     */
    public function is_available() {

        return true; // debug

        $is_available = parent::is_available();

        if (!$is_available) {
            return false;
        }

        $currency = get_woocommerce_currency();

        if ($this->settings['laybuy_geolocate_ip'] === 'no') {
            return $this->is_currency_enabled($currency);
        }

        $country = $this->get_country();

        if ($country) {

            $map = ['GBP' => 'GB', 'AUD' => 'AU', 'NZD' => 'NZ', 'USD' => 'US'];

            $countryList = [];

            foreach (array_values($this->settings['currency']) as $cur) {
                if (isset($map[$cur])) {
                    $countryList[] = $map[$cur];
                }
            }

            if (count($countryList) > 0 && !in_array($country, $countryList)) {
                return false;
            }

            return true;
        }

        return $this->is_currency_enabled($currency);
    }

    public function filter_woocommerce_get_price_html($price, $product) {

        if (is_object($product) && $product instanceof WC_Product) {
            ob_start();
            $this->print_info_for_listed_products($product);
            $html = ob_get_clean();

            return $price . $html;
        }
        return $price;
    }

    public function getProductPrice($product)
    {
        if ($product->is_type('composite')) {
            return $product->get_composite_price_including_tax( 'min', true );
        }

        if ($product->is_type('bundle')) {
            return $product->get_bundle_price_including_tax( 'min', true );
        }

        if ($product->is_type('grouped')) {

            $children = $product->get_children();
            $price = 0;
            foreach ($children as $key => $value) {
                $childProduct = wc_get_product( $value );
                if ($price !== 0) {
                    $price = min($price, $this->getProductPrice($childProduct));
                } else {
                    $price = $this->getProductPrice($childProduct);
                }
            }
            return $price;
        }

        if ($product->is_type( 'variable' )) {
            return $product->get_variation_price( 'min', false );
        }

        if (function_exists('wc_get_price_including_tax')) {
            return wc_get_price_including_tax( $product );
        } elseif (method_exists($product, 'get_price_including_tax')) {
            return $product->get_price_including_tax();
        } else {
            return $product->get_price();
        }
    }

    public function get_plus_limits_html($namespace = '')
    {
        if ($this->pay_over_time_limit_min && !$this->pay_over_time_limit_max) {
            return $this->assets["${namespace}available_on_orders_over"];
        }

        if (!$this->pay_over_time_limit_min && $this->pay_over_time_limit_max) {
            return $this->assets["${namespace}available_on_orders_under"];
        }

        return $this->assets["${namespace}available_on_orders_between"];
    }

    public function is_plus()
    {
        return self::MODE_PLUS === $this->mode;
    }

    public function check_pay_over_time_limits()
    {
        $currency = get_woocommerce_currency();

        if (
            in_array($currency, $this->supported_currencies) &&
            !$this->pay_over_time_limit_min &&
            !$this->pay_over_time_limit_max
        ) {

            $settings = (array) get_option( 'woocommerce_laybuy_settings', true );

            $settings["{$currency}_pay_limit_min"] = 0.06;
            $settings["{$currency}_pay_limit_max"] = $this->pay_limits_max[$currency];

            update_option('woocommerce_laybuy_settings', $settings);
        }
    }

    public function check_cart_within_limits($gateways)
    {
        if (is_admin()) {
            return $gateways;
        }

        if (WC()->cart->total < $this->pay_over_time_limit_min) {
            if (isset($_GET['debug'])) {
                var_dump('UNSET');exit;
            }
            unset($gateways[$this->id]);

        }

        return $gateways;
    }

    public function checkout_add_laybuy_paragraph()
    {
        if (!$this->is_plus()) {
            return;
        }

        if (WC()->cart->total < $this->pay_over_time_limit_min) {

            $html = str_replace(array(
                '[MIN_PRICE]',
                '[MAX_PRICE]',
            ), array(
                wc_price( $this->pay_over_time_limit_min, ['decimals' => 0]),
                wc_price( $this->pay_over_time_limit_max, ['decimals' => 0]),
            ), $this->get_plus_limits_html());

            echo '<div class="laybuy-checkout-widget">' . do_shortcode($html) . '</div>';
        }
    }

    public function getProductPriceBreakdownHookPriority()
    {
        return $this->settings['product_price_breakdown_hook_priority'];
    }

    public function change_woocommerce_currency( $currency )
    {
        if ($this->settings['laybuy_geolocate_ip'] === 'no') {
            return $currency;
        }

        $country = $this->get_country();

        if (empty($country)) {
            return $currency;
        }

        $map = ['GB' => 'GBP', 'AU' => 'AUD', 'NZ' => 'NZD', 'US' => 'USD'];

        if (!isset($map[$country]) || empty($this->settings['currency'])) {
            return $currency;
        }

        $countryList = array_values($this->settings['currency']);

        if (in_array($map[$country], $countryList)) {
            $currency = $map[$country];
        }

        return $currency;
    }

    public function is_enabled() {
        return $this->enabled == 'yes';
    }

    private function get_country()
    {
        $country = null;

        if (isset($_COOKIE[self::COOKIE_GEOLOCATION_COUNTRY])) {
            $country = $_COOKIE[self::COOKIE_GEOLOCATION_COUNTRY];
        } else {

            $geolocation = apply_filters('laybuy_geolocation', []);

            if (empty($geolocation)) {
                $geolocation = WC_Geolocation::geolocate_ip();
            }

            if (!empty($geolocation['country'])) {
                $country = $geolocation['country'];
                setcookie( self::COOKIE_GEOLOCATION_COUNTRY, $country, time()+3600*24*30);
            }
        }

        return $country;
    }

    public function generate_support_request_btn_html( $key, $data )
    {
        $field_key = $this->get_field_key( $key );

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                    <button id="laybuy_send_support_request" class="button-cancel woocommerce-save-button" type="button" value="<?php esc_attr_e( 'Send Support Report', 'woocommerce' ); ?>"><?php esc_html_e( 'Send Support Request', 'woocommerce' ); ?></button>
                    <?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
                </fieldset>
            </td>
        </tr>
        <?php

        return ob_get_clean();
    }
}