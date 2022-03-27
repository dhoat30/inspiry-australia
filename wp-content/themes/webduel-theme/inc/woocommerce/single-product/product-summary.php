<?php

// remove sku code
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// remove sidebar 
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/**
 * Remove product page tabs
 */
add_filter( 'woocommerce_product_tabs', 'my_remove_all_product_tabs', 98 );
 
function my_remove_all_product_tabs( $tabs ) {
  unset( $tabs['description'] );        // Remove the description tab
  unset( $tabs['reviews'] );       // Remove the reviews tab
  unset( $tabs['additional_information'] );    // Remove the additional information tab
  return $tabs;
}

// price ------------------------------------------------------------------------------------------
// Trim zeros in price decimals
add_filter( 'woocommerce_price_trim_zeros', '__return_true' ); 

// remove short description on single product page
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

// add free delivery tag ----------------------------------------------
add_action('woocommerce_single_product_summary', function(){ 
    echo '<div class="price-flex">';
}, 5); 
add_action('woocommerce_single_product_summary', function(){ 
    global $product; 
    if($product->get_shipping_class()=== "free-shipping"){ 
        echo '<div class="free-shipping">
        <i class="fa-solid fa-cube"></i>
        Free Delivery
        </div>'; 
    }
    echo '
    </div>
    ';

}, 10); 


// add deal information on single product page --------------------------------------
add_action('woocommerce_single_product_summary', 'webduelModal', 15); 

function webduelModal(){ 
   if(is_product()){ 
        global $product; 
        // get current deal for the single product 
        $currentDeal = $product->get_attribute( 'pa_current-deal' );
    
        // get modal Categories
        $modalCategories = get_terms( array( 
            'taxonomy' => 'modal-categories',
            'parent'   => 0
        ) );
        // get category slug for custom post type
        $categorySlug = ''; 
        foreach ($modalCategories as $item ) {
            // process element here
            if($item->name === $currentDeal){
                $categorySlug = $item->slug; 
            }
        }
        
        singleProductQuery($categorySlug); 
   }
   
}   

// modal query 
function singleProductQuery($categorySlug){ 
       //  query modal with category slug
       $the_query = new WP_Query( array(
        'post_type' => 'modal',
        'tax_query' => array(
            array (
                'taxonomy' => 'modal-categories',
                'field' => 'slug',
                'terms' => $categorySlug
            )
        ),
    ) );
    while($the_query->have_posts()){ 
        $the_query->the_post(); 
        echo '<div class="deal-section">
               <div class="content" >
                    <div class=" title">'.get_the_title().'</div>
                    <div class="subtitle">'.get_the_content().'</div>
                </div>
            </div>
        '; 
    }
    wp_reset_postdata();
}


// availability section _--------------------------------------------------------------

add_action('woocommerce_single_product_summary', function (){ 
    global $product;    
    // Available on backorder
    $countryOfOrigin = $product->get_attribute( 'pa_country-of-origin' );
    $deliveryETA = $product->get_attribute( 'pa_availability' );
    if($countryOfOrigin){ 
        $countryOfOrigin = 'Country Of Origin: '.$countryOfOrigin; 
    }
    echo '
    <div class="availability">
        <h3 class="title">'; 
        // if($product->get_availability()['class'] === 'in-stock'){ 
        //    echo ' 
        //     <i class="fa-solid fa-circle-check" style="color: var(--green); "></i>
        //     <span  style="color: var(--green); ">
        //     IN STOCK
        //     </span>
        //     '; 

        // }
        // else{ 
        //     echo ' 
        //     <i class="fa-solid fa-circle-check" style="color: var(--orange); "></i>
        //     <span  style="color: var(--orange); ">
        //     Pre Order
        //     </span>
        //     '; 
        // }
        echo '
        <h3>
        <div class="content">
            <h4 class="subtitle">
                '.$countryOfOrigin.'
            </h4>
            <h5 class="delivery-info">
                Availability: '.$deliveryETA.' 
            </h5>
        </div>
    </div>'; 
}, 60) ; 

// social share -----------------------------------------------------------
add_action('woocommerce_single_product_summary', function(){
    echo '
    <div class="social-share-container">
        <p>Share:</p>
    ';  
    echo do_shortcode('[webduelSocialShare]');
    echo '</div>';
}, 80); 

// add variation availability data before add to cart button ---------------------------------
function iconic_output_engraving_field() {
	
    global $product;
    echo $product->get_stock_quantity();

    // $product->is_type( $type ) checks the product type, string/array $type ( 'simple', 'grouped', 'variable', 'external' ), returns boolean
    
    if ( $product->is_type( 'variable' ) ) {
        $dataArray = array(); 
        foreach($product->get_available_variations() as $key){
            $variation = wc_get_product( $key['variation_id'] );
            $stock = $variation->get_availability();
            
            array_push($dataArray, array(
                "variation_id" => $key['variation_id'], 
                "availability"=>$stock['class']
             )); 
        }
        
        ?>
        <div class="variation-availability-data" 
        data-variation_availability='<?php   print_r(json_encode($dataArray)); ?>'>
        </div>
        <?php
       
    }
}

add_action( 'woocommerce_before_add_to_cart_button', 'iconic_output_engraving_field', 10 );

//add wallpaper calculator 
add_action('woocommerce_single_product_summary', function(){
    global $post, $product; 
    $category = wp_strip_all_tags($product->get_categories());
    $CategoryWallpaper = "Wallpaper";
    $categoryFabric = "Fabric"; 

    if($category === 'Wallpaper' ){
        echo '<div class="product-page-btn-container">
            <button class="sizing-calculator-button secondary-button"><i class="far fa-calculator" aria-hidden="true"></i> Wallpaper Calculator</button>       
        </div>'; 

     //add calculator body 
     calculator_body();
    }
    
   
}, 40); 


//wallpaper calculator 
 

function calculator_body(){
    global $product; 
    echo '<div class="body-container">
       

    <!--sizing calculator-->
    <div class="overlay-background">
        <div class="calculator-overlay">
        <i class="fal fa-times close"></i>

            <div id="calculator-container">
                <div class="popup-modal wallpaper-calculator-modal is-open">
          
                  <h1>Wallpaper Calculator</h1>
          
          
              <form name="wallpaper_calculator" id="wallpaper-calculator">
                <section>
                  <div>
                    <label for="calc-roll-width">Roll Width<em>*</em> </label>
                    <select name="calc-roll-width" id="calc-roll-width"><option value="37.2">37.2 cm</option><option value="42">42 cm</option><option value="45">45 cm</option><option value="48.5">48.5 cm</option><option value="53">53 cm</option><option value="52">52 cm</option><option value="64">64 cm</option><option value="68">68 cm</option><option value="68.5">68.5 cm</option><option value="70">70 cm</option><option value="90">90 cm</option><option value="95">95 cm</option><option value="100">100 cm</option><option value="140">140 cm</option></select>
                    <label for="calc-roll-height">Roll Length<em>*</em> </label>
                    <select name="calc-roll-height" id="calc-roll-height"><option value="2.65">2.65 cm</option><option value="2.79">2.79 cm</option><option value="3">3 cm</option><option value="5.6">5.6 cm</option><option value="6">6 cm</option><option value="8.5">8.5 cm</option><option value="8.37">8.37 cm</option><option value="9">9 cm</option><option value="10">10 cm</option><option value="10.05">10.05 cm</option><option value="12">12 cm</option><option value="24">24 cm</option></select>
                  </div>
                  <aside>
                    <label for="last-name">Wall width<em>*</em></label>
                    <div class="input-group">
                      <input type="text" name="calc-wall-width1" value="" id="calc-wall-width1" class="form-control" placeholder="Wall 1 width">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-width2" value="" id="calc-wall-width2" class="form-control" placeholder="Wall 2 width">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-width" value="" id="calc-wall-width3" class="form-control" placeholder="Wall 3 width">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-width4" value="" id="calc-wall-width4" class="form-control" placeholder="Wall 4 width">
                          <span class="input-group-addon">m</span>
                      </div>
                  </aside>
                  <aside>
                    <label for="last-name">Wall height<em>*</em></label>
                    <div class="input-group">
                      <input type="text" name="calc-wall-height1" value="" id="calc-wall-height1" class="form-control" placeholder="Wall 1 length">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-height2" value="" id="calc-wall-height2" class="form-control" placeholder="Wall 3 length">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-height3" value="" id="calc-wall-height3" class="form-control" placeholder="Wall 3 length">
                          <span class="input-group-addon">m</span>
                    </div>
                    <div class="input-group">
                      <input type="text" name="calc-wall-height4" value="" id="calc-wall-height4" class="form-control" placeholder="Wall 4 length">
                          <span class="input-group-addon">m</span>
                      </div>
                  </aside>
                </section>
                <section>
                  <label for="address">Repeat<em>(optional)</em></label>
                  <div class="input-group">
                    <input type="text" name="calc-pattern-repeat" value="" id="calc-pattern-repeat" class="form-control">
                    <span class="input-group-addon">cm</span>
                  </div>
                </section>
                <section class="buttons">
                  <button id="estimate-roll" class="primary-button">Calculate</button>
                </section>
                <section class="estimate-result margin-elements">
                      <h3>Result</h3>
                      <p>
                      
                              <span class="calc-round">0</span>&nbsp;
                              <span class="suffix-singular hidden" style="display: none;">roll</span>
                              <span class="suffix-plural">rolls</span>
                   
                      </p>
                </section>
            
                <section class="message margin-elements">
                  <p>Please check your measurements carefully. Inspiry is not responsible for overages or shortages based on this calculator.</p>
                </section>
            
              </form>
          
          
          
          
                </div>
              </div>
        </div>
      </div>
</div>';
}

// single product page wishlist container and add brand name before title 
add_action("woocommerce_single_product_summary", "single_product_page_title_start", 1); 

function single_product_page_title_start(){ 
 
    global $product; 
    // find the brand name of the product
    $brand = array_shift( wc_get_product_terms( $product->id, 'pa_brands', array( 'fields' => 'names' ) ) );

    echo  '<div class="single-product-before-title-container">';
        echo '<div class="brand-name">'; 
        echo $brand; 
        echo '</div>';
        echo '<div class="design-board-container">'. do_shortcode('[design_board_button_code]').'</div>';
    echo '</div>';
}
      