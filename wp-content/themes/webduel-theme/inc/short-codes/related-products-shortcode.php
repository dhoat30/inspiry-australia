<?php 
// related products logic 
function relatedProductLoopShortCode(){ 
    global $product; 
    $childCategorySlug = "null"; 
    $parentCategory=''; 
    $categories = get_the_terms( $product->get_id(), 'product_cat' ); 
    //  print_r($categories);
    if ( $categories  ){
        // loop through each cat
        foreach($categories as $category) {
            $parentCategory = $category->slug;
          // get the children (if any) of the current cat
          $children = get_categories( array ('taxonomy' => 'product_cat', 'parent' => $category->term_id ));
          if ( count($children) == 0 && $category->parent > 0 ) {
              // if no children, then echo the category name.
              $childCategorySlug = $category->slug; 
          }
        }
    }
    
    // echo $childCategorySlug; 
 
        $childCategoriesQuery = array(
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
        $postsExists = ''; 

        $posts = new WP_Query( $childCategoriesQuery );
        // check of the child category exists and have posts
        if($posts->have_posts()){ 
            $postsExists = new WP_Query( $childCategoriesQuery );
        }
        else{ //else query for parent category 
            $parentCategoriesQuery = array(
                'post_type' => 'product',
                'posts_per_page'=>12,
                    'orderby' => 'date', 
                    'order' => 'ASC',
                    'tax_query' => array(
                        'relation'=> 'OR', 
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => array($parentCategory),
                        )
                        ), 
            );
            $postsExists = new WP_Query( $parentCategoriesQuery );
        }
        if($postsExists->have_posts()){ 
            $relatedOwlCarouselClass = $postsExists->post_count>3 ? 'owl-carousel' : null; 

            ?>
            <section class="trending-section row-container">
                <h3 class="title">You may also like</h3>
                <div class="related-product-section <?php echo $relatedOwlCarouselClass; ?>">
                    <?php 
                    while($postsExists->have_posts()){ 
                        $postsExists->the_post(); 
                        $product = wc_get_product( get_the_ID() );
                        
                        ?>
                        <a class="card" href="<?php echo get_the_permalink()?>">
                            <div class="image-container">
                                <img 
                                loading="lazy" 
                                src="<?php  echo get_the_post_thumbnail_url(null,"woocommerce_thumbnail")?>" 
                                alt="<?php echo get_the_title()?>"/>
                            </div>
                            <h5 ><?php echo get_the_title()?></h5>
                            <h6 class="price"><?php echo $product->get_price_html()?></h6>
                        </a>
                        <?php 
                    }
                    ?>
                </div>
            </section>
            <?php 
             wp_reset_postdata();
        }
}

add_shortcode('related_product_loop_short_code', 'relatedProductLoopShortCode'); 

// recently viewed products 

add_shortcode( 'recently_viewed_products', 'bbloomer_recently_viewed_shortcode' );
 
function bbloomer_recently_viewed_shortcode() {
 
   $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array();
   $viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
 
   if ( empty( $viewed_products ) ) return;
   $owlCarouselClass = count($viewed_products)>3 ? 'owl-carousel' : null; 

    echo '
    <section class="recently-viewed-section row-container">
        <h3 class="title">Recently Viewed</h3>
        <div class="recently-viewed-products '.$owlCarouselClass.'">'; 
        foreach($viewed_products as $productID){ 
            $product = wc_get_product( $productID );
            
            echo  '
            <a class="card" href="'.get_the_permalink($productID).'">
                <div class="image-container">
                    <img 
                    loading="lazy" 
                    src="'. get_the_post_thumbnail_url($productID, "woocommerce_thumbnail").'" 
                    alt="'.get_the_title($productID).'"/>
                </div>
                <h5 >'.$product->get_name().'</h5>
                <h6 class="price">'.$product->get_price_html().'</h6>
             </a>';
        }
    echo '
        </div>
    </section>'; 
    
//    return $title . do_shortcode("[products ids='$product_ids']");
   
}