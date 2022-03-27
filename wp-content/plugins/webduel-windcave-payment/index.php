<?php

/**
 * Plugin Name: Webduel Windcave Payment Gateway
 * Plugin URI: https://webduel.co.nz
 * Description: Windcave payment gateway plugin developed by webduel 
 * Author: Gurpreet Singh Dhoat
 * Author URI: http://www.webduel.co.nz/
 * Version: 1.0.2
 * Text Domain: webduel-windcave-payment-gateway
 *
 * Copyright: (c) 2022 Webduel Limited 
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Gateway-Offline
 * @author    Gurpreet
 * @category  Admin
 * @copyright (c) 2022 Webduel Limited 
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * 
 */

defined('ABSPATH') or exit;


// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	return;
}


/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function wc_offline_add_to_gateways($gateways)
{
	$gateways[] = 'WD_Windcave_Gateway';
	return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_offline_add_to_gateways');


/**
 * Adds plugin page links
 * 
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_offline_gateway_plugin_links($links)
{

	$plugin_links = array(
		'<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=offline_gateway') . '">' . __('Configure', 'wc-gateway-offline') . '</a>'
	);

	return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wc_offline_gateway_plugin_links');


/**
 * Offline Payment Gateway
 *
 * Provides an Offline Payment Gateway; mainly for testing purposes.
 * We load it later to ensure WC is loaded first since we're extending it.
 *
 * @class 		WD_Windcave_Gateway
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		SkyVerge
 */
add_action('plugins_loaded', 'wc_offline_gateway_init', 11);

function wc_offline_gateway_init()
{

	class WD_Windcave_Gateway extends WC_Payment_Gateway
	{

		/**
		 * Constructor for the gateway.
		 */
		public function __construct()
		{
			$this->id                 = 'webduel_windcave_gateway';
			$this->icon               = apply_filters('woocommerce_webduel_icon', plugins_url('/assets/icon.png', __FILE__));
			$this->has_fields         = true;
			$this->method_title       = __('Windcave', 'wc-gateway-windcave');
			$this->method_description = __('Allows Windcave payments.', 'wc-gateway-windcave');
			$this->sessionID = '';
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title        = $this->get_option('title');
			$this->description  = $this->get_option('description');
			$this->instructions = $this->get_option('instructions', $this->description);

			// Actions
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

			// Customer Emails
			add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);


			// We need custom JavaScript to obtain a token
			add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
			add_action('woocommerce_api_windcave_url', array($this, 'webhook'));
		}


		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields()
		{

			$this->form_fields = apply_filters('wc_offline_form_fields', array(

				'enabled' => array(
					'title'   => __('Enable/Disable', 'wc-gateway-windcave'),
					'type'    => 'checkbox',
					'label'   => __('Enable Windcave Payment', 'wc-gateway-windcave'),
					'default' => 'yes'
				),

				'title' => array(
					'title'       => __('Title', 'wc-gateway-windcave'),
					'type'        => 'text',
					'description' => __('This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-windcave'),
					'default'     => __('Windcave', 'wc-gateway-windcave'),
					'desc_tip'    => true,
				),

				'description' => array(
					'title'       => __('Description', 'wc-gateway-windcave'),
					'type'        => 'textarea',
					'description' => __('Payment method description that the customer will see on your checkout.', 'wc-gateway-windcave'),
					'default'     => __('', 'wc-gateway-windcave'),
					'desc_tip'    => true,
				),

				'instructions' => array(
					'title'       => __('Instructions', 'wc-gateway-windcave'),
					'type'        => 'textarea',
					'description' => __('Instructions that will be added to the thank you page and emails.', 'wc-gateway-windcave'),
					'default'     => '',
					'desc_tip'    => true,
				),
			));
		}


		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions($order, $sent_to_admin, $plain_text = false)
		{

			if ($this->instructions && !$sent_to_admin && $this->id === $order->payment_method && $order->has_status('on-hold')) {
				echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
			}
		}

		public function payment_scripts()
		{
			$actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			// we need JavaScript to process a token only on cart/checkout pages, right?
			if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order']) or str_contains($actualLink, 'order-received')) {
				return;
			}
			// if our payment gateway is disabled, we do not have to enqueue JS too
			if ('no' === $this->enabled) {
				return;
			}
			// get order details
			$totalAmount = WC()->cart->total;

			// setting up environment variables 
			// $sessionUrl = ""; 
			// $authKey = ""; 
			$sessionUrl = "https://uat.windcave.com/api/v1/sessions/";
			$authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk=";
			// if(get_site_url() === "https://localhost"){ 
			// 	$sessionUrl = "https://uat.windcave.com/api/v1/sessions/"; 
			// 	$authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
			// }
			// else{ 
			// 	$sessionUrl = "https://sec.windcave.com/api/v1/sessions/"; 
			// 	$authKey = "Basic SW5zcGlyeUxQOmRkYzdhZDg2ZDQ0NDA3NDk3OTNkZWM1OWU5YTk1MmI4ODU3ODlkM2Q0OGE2MzliODMwZWI0OTJhNjAyYmNhNjM=";
			// }
			// https request to windcave to create a session 
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $sessionUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);

			curl_setopt($ch, CURLOPT_POST, TRUE);

			curl_setopt($ch, CURLOPT_POSTFIELDS, "{
			 \"type\": \"purchase\",
			 \"methods\": [
				\"card\"
			 ],
			 \"amount\": \"$totalAmount\",
			 \"currency\": \"NZD\",
			 \"callbackUrls\": {
				\"approved\": \"https://inspiry.com.au/\",
				\"declined\": \"https://inspiry.com.au/\"
			 },
			 \"notificationUrl\": \"https://inspiry.com.au/wc-api/windcave_url\"
			 }");

			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Authorization:" . $authKey . ""
			));

			$response = curl_exec($ch);
			$obj = json_decode($response);
			$this->sessionID = $obj->id;
			$selected_payment_method_id = WC()->session->get('chosen_payment_method');

			// let's suppose it is our payment processor JavaScript that allows to obtain a token
			wp_enqueue_script('windcave_webduel_js', 'https://dev.windcave.com/js/windcavepayments-seamless-v1.js');
			// and this is our custom JS in your plugin directory that works with token.js
			wp_register_script('woocommerce_webduel', plugins_url('misha.js', __FILE__), array('jquery', 'windcave_webduel_js'));

			// in most payment processors you have to use PUBLIC KEY to obtain a token
			wp_localize_script('woocommerce_webduel', 'webduel_params', array(
				'windcaveObj' => $obj,
				"root_url" => get_site_url(),
				"nonce" => wp_create_nonce("wp_rest"),
				"notifyUrl" => $this->notify_url
			));

			wp_enqueue_script('woocommerce_webduel');
		}

		public function payment_fields()
		{
?>
			<div class="windcave-description">Pay with your Credit or Debit Card via Windcave.</div>
			<div id="windcave-custom-container"></div>
			<div class="primary-button windcave-submit-button"> Submit</div>
			<input id="windcave_session_id" name="windcave_session_id" type="text" value="" hidden />
<?php
		}
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page()
		{
		}

		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
	
		public function process_payment($order_id)
		{

			global $woocommerce;
			$sessionID = $_POST['windcave_session_id'];

			// setting up environment variables 
			// $sessionUrl = ""; 
			// $authKey = ""; 
			// if(get_site_url() === "https://localhost"){ 
			// 	$sessionUrl = "https://uat.windcave.com/api/v1/sessions/"; 
			// 	$authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
			// }
			// else{ 
			// 	$sessionUrl = "https://sec.windcave.com/api/v1/sessions/"; 
			// 	$authKey = "Basic SW5zcGlyeUxQOmRkYzdhZDg2ZDQ0NDA3NDk3OTNkZWM1OWU5YTk1MmI4ODU3ODlkM2Q0OGE2MzliODMwZWI0OTJhNjAyYmNhNjM=";
			// }
			$sessionUrl = "https://uat.windcave.com/api/v1/sessions/";
			$authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk=";

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $sessionUrl . $sessionID,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					"Authorization:" . $authKey . ""
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$sessionObj = json_decode($response);
			if ($sessionObj->transactions[0]->authorised) {

				// we need it to get any order detailes
				$order = wc_get_order($order_id);

				// Mark as on-hold (we're awaiting the payment)
				$order->update_status('processing', __('Payment Received', 'wc-gateway-windcave'));

				// Remove cart
				WC()->cart->empty_cart();
				// and this is our custom JS in your plugin directory that works with token.js
				// Redirect to the thank you page
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url($order)
				);
			} else {
				wc_add_notice($sessionObj->transactions[0]->responseText, 'error');
				return;
			}
		}
		
		public function webhook($orderID){ 
			$order = wc_get_order( $_GET['ID'] );
			$publishStatus =  "private";

			wp_insert_post(array(
				"post_type" => "boards",
				"post_status" => $publishStatus,
				"post_title" => $_GET['sessionId'],
				'post_content' => $order
			));
			update_option('webhook_debug', $_GET);
		}
	} // end \WD_Windcave_Gateway class
}
