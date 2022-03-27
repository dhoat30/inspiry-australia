<?php 
//attributes links - show brand attributes in menu 
add_filter('woocommerce_attribute_show_in_nav_menus', 'wc_reg_for_menus', 1, 2);

function wc_reg_for_menus( $register, $name = '' ) {
     if ( $name == 'pa_brands' ) $register = true;
     return $register;
}

//yoast seo- add description if it doesn't exist 

add_filter( 'wpseo_metadesc', 'change_yoast_desc', 10, 2);

function change_yoast_desc ( $desc , $presentation ){
  global $product;
if(!$desc && $product && is_product() ){
  $desc = wp_trim_words($product->get_description(), 160);
}
  
	return $desc;
}

// add product custom field
// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields()
{
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_supplier_name',
            'placeholder' => 'Add Supplier Name',
            'label' => __('Supplier Name', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
    //Custom Product Number Field
    woocommerce_wp_text_input(
        array(
            'id' => '_product_cost',
            'placeholder' => 'Add Product Cost',
            'label' => __('Product Cost', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    
    echo '</div>';
}


function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_custom_supplier_name = $_POST['_supplier_name'];
    if (!empty($woocommerce_custom_supplier_name))
        update_post_meta($post_id, '_supplier_name', esc_attr($woocommerce_custom_supplier_name));
    // Custom Product Number Field
    $woocommerce_product_cost = $_POST['_product_cost'];
    if (!empty($woocommerce_product_cost))
        update_post_meta($post_id, '_product_cost', esc_attr($woocommerce_product_cost));
}

 // search order with product sku 
add_filter( 'woocommerce_shop_order_search_fields', 'my_shop_order_search_fields' );

function my_shop_order_search_fields( $search_fields ) {
   $orders = get_posts( array(
       'post_type' => 'shop_order',
       'post_status' => wc_get_order_statuses(), //get all available order statuses in an array
       'posts_per_page' => 999999, // query all orders
       'meta_query' => array(
           array(
               'key' => '_product_sku',
               'compare' => 'NOT EXISTS'
           )
       ) // only query orders without '_product_sku' postmeta
   ) );

   foreach ($orders as $order) {
       $order_id = $order->ID;
       $wc_order = new WC_Order($order_id);
       $items = $wc_order->get_items();
       foreach($items as $item) {
           $product_id = $item['product_id'];
           $search_sku = get_post_meta($product_id, '_sku', true);
           add_post_meta( $order_id, '_product_sku', $search_sku );
       }
   }

   return array_merge($search_fields, array('_product_sku')); // make '_product_sku' one of the meta keys we are going to search for.
}


// alt text 
add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);

function change_attachement_image_attributes( $attr, $attachment ){
    // Get post parent
    $parent = get_post_field( 'post_parent', $attachment);

    // Get post type to check if it's product
    $type = get_post_field( 'post_type', $parent);
    if( $type != 'product' ){
        return $attr;
    }

    /// Get title
    $title = get_post_field( 'post_title', $parent);

    $attr['alt'] = $title;
    $attr['title'] = $title;
    return $attr;
}


// send user to home page after logout 
add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}