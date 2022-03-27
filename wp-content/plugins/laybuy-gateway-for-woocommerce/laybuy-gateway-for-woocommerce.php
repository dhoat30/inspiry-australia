<?php
/*
Plugin Name: Laybuy Gateway for WooCommerce
Description: Provide Laybuy as a payment option for WooCommerce orders.
Author: Laybuy
Author URI: https://www.laybuy.com/
Version: 5.3.5
Text Domain: laybuy-gateway-for-woocommerce
License: GPL2
Tested up to: 5.8
WC requires at least: 3.0.0
WC tested up to: 5.0.0
Woocommerce Laybuy is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Woocommerce Laybuy is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Woocommerce Laybuy. If not, see https://www.laybuy.com/.
*/

if (!defined('ABSPATH')) {
    exit;
}

function woocommerce_laybuy_missing_wc_notice() {
    echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'LayBuy requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-gateway-stripe' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

add_action( 'plugins_loaded', 'woocommerce_gateway_laybuy_init' );

function woocommerce_gateway_laybuy_init() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'woocommerce_laybuy_missing_wc_notice' );
        return;
    }
}

define( 'WC_LAYBUY_VERSION', '5.3.5' );
define('WC_LAYBUY_PLUGIN_URL', plugin_dir_url(__FILE__));


class WC_Laybuy
{
    protected static $instance;
    public static $version = WC_LAYBUY_VERSION;

    private function __construct() {

        add_action('parse_request', array($this, 'laybuy_plugin_version'));
        add_filter( 'page_template', array($this, 'laybuy_page_template') );

        if (!defined('LAYBUY_MAIN_PATH')) {
            define('LAYBUY_MAIN_PATH', plugin_dir_path(__FILE__));
        }

        require_once dirname( __FILE__ ) . '/includes/constants.php';
        require_once dirname( __FILE__ ) . '/includes/wc-functions.php';
        require_once dirname( __FILE__ ) . '/includes/class-wc-laybuy-helper.php';
        require_once dirname( __FILE__ ) . '/includes/class-wc-laybuy-logger.php';
        require_once dirname( __FILE__ ) . '/includes/class-wc-gateway-laybuy.php';

        require_once dirname( __FILE__ ) . '/includes/Laybuy/ApiGateway.php';
        require_once dirname( __FILE__ ) . '/includes/Laybuy/ProcessManager.php';
        require_once dirname( __FILE__ ) . '/includes/Laybuy/SupportRequest.php';

        $gateway = WC_Payment_Gateway_Laybuy::getInstance();
        $settings = get_option( 'woocommerce_laybuy_settings', true );

        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateways' ), 10, 1 );
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'filter_action_links'), 10, 1 );
        add_filter( 'woocommerce_available_payment_gateways', array($gateway, 'check_cart_within_limits'), 99, 1 );

        if ($gateway->is_enabled()) {
            add_filter( 'woocommerce_currency', [$gateway, 'change_woocommerce_currency']);
        }

        add_action( 'wp_enqueue_scripts', array($this, 'init_website_assets'), 10, 0 );
        add_action( 'admin_enqueue_scripts', array($this, 'init_admin_assets'), 10, 0 );
        add_action( 'woocommerce_update_options_payment_gateways_laybuy', array( $gateway, 'process_admin_options' ) );
        add_action( 'woocommerce_settings_saved', array($this, 'wc_settings_saved'));
        add_action( 'woocommerce_review_order_before_payment', array($gateway, 'checkout_add_laybuy_paragraph'));

        add_shortcode( 'laybuy_iframe_src', array($gateway, 'shortcode_laybuy_iframe_src') );
        // add_shortcode( 'laybuy_paragraph', array($gateway, 'shortcode_laybuy_paragraph') );

        if (!$gateway->is_available()) {
            return;
        }

        // filters
        add_filter( 'woocommerce_create_order', array($gateway, 'create_order_quote'), 10, 2 );
        add_filter( 'woocommerce_new_order_data', array($gateway, 'filter_woocommerce_new_order_data'), 10, 1 );
        add_filter( 'checkout_modify_description', array($gateway, 'checkout_modify_description'), 10, 2 );

        if (WC_Laybuy_Helper::is_wc_gt('3.7')) {
            add_filter('woocommerce_get_price_html', array($gateway, 'filter_woocommerce_get_price_html'), 199, 2);
        } else {
            add_action( 'woocommerce_after_shop_loop_item_title', array($gateway, 'print_info_for_listed_products'), 199, 0 );
        }

        // actions
        add_action( 'woocommerce_cart_totals_after_order_total', array($gateway, 'render_schedule_on_cart_page'), 10, 0 );
        add_action( 'template_redirect', array( $gateway, 'process_redirect_order' ) );

        add_action(
            $settings['price_breakdown_option_product_page_position'],
            [$gateway, 'product_price_breakdown'],
            $gateway->getProductPriceBreakdownHookPriority()
        );

        // shortcodes
        add_shortcode( 'laybuy_logo', array($gateway, 'shortcode_laybuy_logo') );
        add_action( 'wp_ajax_send_laybuy_support_request', array(new Laybuy_SupportRequest(), 'send'));
    }

    /**
     * Initialise the class and return an instance.
     *
     * Note:	Hooked onto the "plugins_loaded" Action.
     *
     * @since	2.0.0
     * @uses	self::load_classes()
     * @return	WC_Laybuy
     */
    public static function init()
    {
        if (!defined('WC_VERSION')) {
            return;
        }

        if (version_compare( WC_VERSION, '3.9', '>=' )) {
            if (!file_exists(WC()->plugin_path() .'/vendor/autoload.php')) {
                return;
            }
        }

        require_once dirname( __FILE__ ) . '/includes/Laybuy/Plugin/UpdateManager.php';

        $laybuyCurrentPluginVersion = get_option('wc_laybuy_version');

        if ($laybuyCurrentPluginVersion) {
            $pluginUpdateManager = new Laybuy_Plugin_UpdateManager();
            $pluginUpdateManager->setPluginVersion(WC_LAYBUY_VERSION)
                ->setPluginDbVersion($laybuyCurrentPluginVersion)
                ->update();
        }

        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function laybuy_page_template($page_template) {

        if ( is_page( 'laybuy' ) ) {
            $page_template = dirname( __FILE__ ) . '/templates/laybuy-page-template.php';
        }
        return $page_template;
    }

    /**
     * Add the gateways to WooCommerce.
     *
     * @since 1.0.0
     * @version 4.0.0
     */
    public function add_gateways($methods) {

        if (isset($_GET['debug'])) {
            var_dump('add laybuy gateway');
        }
        $methods[] = 'WC_Payment_Gateway_LayBuy';

        return $methods;
    }

    /**
     * Note: Hooked onto the "plugin_action_links_laybuy-gateway-for-woocommerce/laybuy-gateway-for-woocommerce.php" Action.
     *
     * @since	2.0.0
     * @see		self::__construct()		For hook attachment.
     * @param	array	$links
     * @return	array
     */
    public function filter_action_links($links)
    {
        $additional_links = array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=laybuy' ) . '">' . __( 'Settings', 'woo_laybuy' ) . '</a>',
        );
        return array_merge($additional_links, $links);
    }

    /**
     * Note: Hooked onto the "admin_enqueue_scripts" Action.
     *
     * @since	2.0.0
     * @see		self::__construct()		For hook attachment.
     */
    public function init_admin_assets()
    {
        wp_enqueue_script( 'laybuy_admin_js', plugins_url( 'assets/js/laybuy-admin.js', __FILE__ ), array(), WC_LAYBUY_VERSION );
        wp_enqueue_style('laybuy_admin_css',  plugins_url( 'assets/css/laybuy_admin.css', __FILE__ ), array(), WC_LAYBUY_VERSION );
    }

    public function init_website_assets() {
        wp_enqueue_script( 'laybuy_js', plugins_url( 'assets/js/laybuy.js', __FILE__ ), array(), WC_LAYBUY_VERSION );
        wp_enqueue_style( 'laybuy_css', plugins_url( 'assets/css/laybuy.css', __FILE__ ), array(), WC_LAYBUY_VERSION );
    }

    public function laybuy_plugin_version() {
        if($_SERVER["REQUEST_URI"] == '/laybuy_version') {
            echo "Laybuy version: " . self::$version;
            exit();
        }
    }

    public function wc_settings_saved()
    {
        $settings = (array) get_option( 'woocommerce_laybuy_settings', true );
        $laybuyPage = get_page_by_path( 'laybuy' );

        if ($settings['laybuy_page_enabled'] == 'yes') {
            if (!$laybuyPage)
                wp_insert_post( array(
                    'post_title'    => 'How Laybuy works',
                    'post_content'  => '',
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'post_type'     => 'page',
                    'post_name'     => 'laybuy'
                ));

        } else if ($settings['laybuy_page_enabled'] == 'no') {
            if ($laybuyPage)
                wp_delete_post(intval($laybuyPage->ID), true);
        }
    }
}

add_action( 'plugins_loaded', array('WC_Laybuy', 'init'), 10, 0 );