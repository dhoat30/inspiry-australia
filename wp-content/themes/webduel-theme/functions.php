<?php 
/**
 * Inspiry functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package inspiry

 */

//  routes
require get_theme_file_path('/inc/api-routes/auth-route.php');
require get_theme_file_path('/inc/api-routes/boards-route.php');
require get_theme_file_path('/inc/api-routes/cart-routes.php');
require get_theme_file_path('/inc/api-routes/email-routes.php');
require get_theme_file_path('/inc/api-routes/mailchimp-route.php');
require get_theme_file_path('/inc/api-routes/pins-routes.php');
require get_theme_file_path('/inc/api-routes/search-routes.php');
require get_theme_file_path('/inc/api-routes/social-login-routes.php');
require get_theme_file_path('/inc/api-routes/trade-routes.php');
require get_theme_file_path('/inc/api-routes/windcave-routes.php');
require get_theme_file_path('/inc/api-routes/woocommerce-route.php');
require get_theme_file_path('/inc/api-routes/order-routes.php');

// auth
require get_theme_file_path('/inc/auth/ajax-login.php');
require get_theme_file_path('/inc/auth/create-user.php');

// customer service
require get_theme_file_path('/inc/customer-service/contact-form.php'); 
require get_theme_file_path('/inc/customer-service/feedback-form.php');


// general
require get_theme_file_path('/inc/cart-modal.php');
require get_theme_file_path('/inc/custom-post-type.php');
require get_theme_file_path('/inc/enquiry-modal.php');
require get_theme_file_path('/inc/nav-registeration.php');
require get_theme_file_path('/inc/rest-acf.php');
require get_theme_file_path('/inc/users.php');
// windcave 


// woocommerce 
require get_theme_file_path('/inc/woocommerce/image-size.php');
require get_theme_file_path('/inc/woocommerce/single-product/image-gallery.php');
require get_theme_file_path('/inc/woocommerce/single-product/product-summary.php');
require get_theme_file_path('/inc/woocommerce/single-product/related-products.php');
require get_theme_file_path('/inc/woocommerce/single-product/ajax-operations.php');
require get_theme_file_path('/inc/woocommerce/single-product/single-product-shortcode.php');
require get_theme_file_path('/inc/woocommerce/single-product/sample-button.php');

require get_theme_file_path('/inc/woocommerce/single-product/product-summary-accordion.php');
require get_theme_file_path('/inc/woocommerce/product-archive/product-archive.php');
require get_theme_file_path('/inc/woocommerce/product-archive/archive-product.php');

require get_theme_file_path('/inc/woocommerce/cart/cart.php');
require get_theme_file_path('/inc/woocommerce/cart/cart-ajax.php');

require get_theme_file_path('/inc/woocommerce/checkout/checkout.php');

require get_theme_file_path('/inc/woocommerce/misc/misc.php');

// google analytics ecommerce data 
require get_theme_file_path('/inc/woocommerce/product-archive/google-analytics-impressions.php');
require get_theme_file_path('/inc/woocommerce/single-product/google-analytics-detail.php');
require get_theme_file_path('/inc/woocommerce/cart/google-analytics-cart.php');

// shortcodes
require get_theme_file_path('/inc/short-codes/social-share.php');
require get_theme_file_path('/inc/short-codes/related-products-shortcode.php');
require get_theme_file_path('/inc/short-codes/archive-page-shortcode.php');
require get_theme_file_path('/inc/short-codes/general-shortcodes.php');

// design board 
require get_theme_file_path('/inc/design-board-modal/design-board-modal.php');

 //enqueue scripts
 function inspiry_scripts(){ 
   wp_enqueue_script('main', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
   wp_enqueue_style('webduel_main_styles', get_theme_file_uri('/build/style-index.css'));
   wp_enqueue_style('webduel_extra_styles', get_theme_file_uri('/build/index.css'));

   wp_enqueue_script("jQuery");
   
    wp_localize_script("main", "inspiryData", array(
      "root_url" => get_site_url(),
      "nonce" => wp_create_nonce("wp_rest"),
      'loadingmessage' => __('Sending user info, please wait...'),
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ));
}
add_action( "wp_enqueue_scripts", "inspiry_scripts" ); 


/**
 * Track product views. Always.
 */
function wc_track_product_view_always() {
  if ( ! is_singular( 'product' ) /* xnagyg: remove this condition to run: || ! is_active_widget( false, false, 'woocommerce_recently_viewed_products', true )*/ ) {
      return;
  }

  global $post;

  if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
      $viewed_products = array();
  } else {
      $viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
  }

  // Unset if already in viewed products list.
  $keys = array_flip( $viewed_products );

  if ( isset( $keys[ $post->ID ] ) ) {
      unset( $viewed_products[ $keys[ $post->ID ] ] );
  }

  $viewed_products[] = $post->ID;

  if ( count( $viewed_products ) > 15 ) {
      array_shift( $viewed_products );
  }

  // Store for session only.
  wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}

remove_action('template_redirect', 'wc_track_product_view', 20);
add_action( 'template_redirect', 'wc_track_product_view_always', 20 );