<?php 

// related products logic 
function relatedProductLoopShortCode(){ 
    global $product; 
    $childCategorySlug = "null"; 
    $categories = get_the_terms( $product->get_id(), 'product_cat' ); 
    //  print_r($categories);
    if ( $categories && ! is_wp_error( $category ) ){
        // loop through each cat
        foreach($categories as $category) {
          // get the children (if any) of the current cat
          $children = get_categories( array ('taxonomy' => 'product_cat', 'parent' => $category->term_id ));
          if ( count($children) == 0 && $category->parent > 0 ) {
              // if no children, then echo the category name.
              $childCategorySlug = $category->slug; 
          }
        }
    }
    
    // echo $childCategorySlug; 
    $brand = array_shift( wc_get_product_terms( $product->id, 'pa_brands', array( 'fields' => 'names' ) ) );
    echo '<section class="trending-section  margin-row row-container">'; 
   echo  '<div class="flex flex-row related-product-section owl-carousel">'; 
        $argsLoving = array(
            'post_type' => 'product',
            'posts_per_page'=>12,
                'orderby' => 'date', 
                'order' => 'ASC',
                'tax_query' => array(
                    'relation'=> 'OR', 
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'slug',
                        'terms'    => array($childCategorySlug),
                    )
                    ), 
        );
        $loving = new WP_Query( $argsLoving );

        while($loving->have_posts()){ 
            $loving->the_post(); 
           

       echo  '<a class="cards rm-txt-dec" href="';
        echo get_the_permalink();
        echo '">'; 

        echo '<img loading="lazy" src="'; 
        echo  get_the_post_thumbnail_url(null,"woocommerce_thumbnail");
        echo '" alt="products">
        <div class="paragraph-font-size margin-top upper-case"  id="trending-now" >
        '; 
        
                       
         echo get_the_title();
         echo ' <i class="fal fa-angle-right"></i>
         </div>
         </a>'; 
        }
        wp_reset_postdata();
       
   echo "</div>"; 
   echo "</section>"; 
}

add_shortcode('related_product_loop_short_code', 'relatedProductLoopShortCode'); 


// add free shipping if it exist for the given product 
function addFreeShippingTag(){ 
    global $product;
    if($product->get_shipping_class()==="free-shipping"){ 
      return '<p class="product-loop-attribute">FREE SHIPPING</p>'; 
    }
}

add_shortcode('add_free_shipping_tag', 'addFreeShippingTag'); 


// add deal text if it exist for the given product on product loop page
function addDealText(){ 
    global $product;
    $dealText = $product->get_attribute( 'pa_current-deal' );
    if(!empty($dealText)){ 
      return '<p class="product-loop-deal">'.$dealText.'</p>'; 
    }
}

add_shortcode('add_deal_text', 'addDealText'); 

// filter button shortcode 
function addFilterButton(){ 
   echo '<div class="medium-font-size regular filter-title"><i class="far fa-filter"></i> FILTERS</div>';
}

add_shortcode('add_filter_button', 'addFilterButton'); 
