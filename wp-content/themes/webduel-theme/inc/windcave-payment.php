<?php

//     if(class_exists('WC_Payment_Gateway')){
//         class Inspiry_Payment_Gateway extends WC_Payment_Gateway {

//             // constructor function 
//             public function __construct()
//             {   
//                 $this->seamlessHpp = ''; 
//                 $this->id = "inspiry_payment"; 
//                 $this->icon = apply_filters( 'woocommerce_inspiry_icon', "https://inspiry.co.nz/wp-content/uploads/2021/08/windcave-icons.png" );
//                 $this->has_fields = false; 
//                 $this->method_title = __('Windcave Payment', 'inspiry-pay-woo'); 
//                 $this->method_description =  __('Pay with your Credit or Debit Card via Windcave.', 'inspiry-pay-woo'); 

//                 $this->title = $this->get_option( 'title' );
//                 $this->description = $this->get_option( 'description' );
//                 $this->instructions = $this->get_option( 'instructions', $this->description );

             

//                 $this->init_form_fields();
//                 $this->init_settings();

//                 add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
//                 add_action( 'woocommerce_thank_you_' . $this->id, array( $this, 'thank_you_page' ) );
//             }

//             public function init_form_fields() { 
//                 $this->form_fields = apply_filters('woo_inspiry_pay_fields', array(
//                     'enabled' => array(
//                         'title' => __( 'Enable/Disable', 'inspiry-pay-woo'),
//                         'type' => 'checkbox',
//                         'label' => __( 'Enable or Disable Inspiry Payments', 'inspiry-pay-woo'),
//                         'default' => 'no'
//                     ),
//                     'title' => array(
//                         'title' => __( 'Inspiry Payments Gateway', 'inspiry-pay-woo'),
//                         'type' => 'text',
//                         'default' => __( 'Inspiry Payments Gateway', 'inspiry-pay-woo'),
//                         'desc_tip' => true,
//                         'description' => __( 'Add a new title for the Inspiry Payments Gateway that customers will see when they are in the checkout page.', 'inspiry-pay-woo')
//                     ),
//                     'description' => array(
//                         'title' => __( 'Inspiry Payments Gateway Description', 'inspiry-pay-woo'),
//                         'type' => 'textarea',
//                         'default' => __( 'Please remit your payment to the shop to allow for the delivery to be made', 'inspiry-pay-woo'),
//                         'desc_tip' => true,
//                         'description' => __( 'Add a new title for the Inspiry Payments Gateway that customers will see when they are in the checkout page.', 'inspiry-pay-woo')
//                     ),
//                     'instructions' => array(
//                         'title' => __( 'Instructions', 'inspiry-pay-woo'),
//                         'type' => 'textarea',
//                         'default' => __( 'Default instructions', 'inspiry-pay-woo'),
//                         'desc_tip' => true,
//                         'description' => __( 'Instructions that will be added to the thank you page and odrer email', 'inspiry-pay-woo')
//                     ),
//                 ));
               
//             }
                
//                 // process payments 
//                 function process_payment( $order_id ) {
//                     global $woocommerce;
//                     $order = new WC_Order( $order_id );
                
//                     // mark as completed
//                     $order->payment_complete();    

//                     // Remove cart
//                     $woocommerce->cart->empty_cart();
                
//                     // Return thankyou redirect
//                     return array(
//                         'result' => 'success',
//                         'redirect' => $this->get_return_url( $order )
//                     );
//                 }

                
//                 public function thank_you_page(){
//                     if( $this->instructions ){
//                         echo wpautop( $this->instructions );
//                     }
//                 }
          

//         }

//     }

// add_filter('woocommerce_payment_gateways', 'add_to_inspiry_payment_gateway'); 

// function add_to_inspiry_payment_gateway($gateways) {
//     $gateways[]= 'Inspiry_Payment_Gateway';
//     return $gateways; 
// }
