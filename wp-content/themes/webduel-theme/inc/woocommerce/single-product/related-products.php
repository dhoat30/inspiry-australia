<?php 
/**
 * Remove related products output
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// add related products
add_action('woocommerce_after_single_product', function(){ 
    echo do_shortcode('[related_product_loop_short_code]');
}, 80);

// add recently viewed products 
add_action('woocommerce_after_single_product', function(){
    echo do_shortcode('[recently_viewed_products]');
 }, 90);

// add categories buttons 
add_action('woocommerce_after_single_product', function(){ 
    global $product; 
    $childCategoryID = "null"; 
    $categories = get_the_terms( $product->get_id(), 'product_cat' ); 
    if ( $categories  ){
        // loop through each cat
        foreach($categories as $category) {
          // get the children (if any) of the current cat
          $children = get_categories( array ('taxonomy' => 'product_cat', 'parent' => $category->term_id ));
          if ( count($children) == 0 && $category->parent > 0 ) {
              // if no children, then echo the category name.
              $childCategoryID = $category->term_id; 
          }
        }
    }
    $buttonDataArray = get_field('related_categories', 'product_cat_'.$childCategoryID); 
    if($buttonDataArray){ 
        echo'
        <section class="related-categories-section  margin-row row-container">
            <h3 class="title">Related Categories</h3>
            <div class="flex">'; 
            foreach($buttonDataArray as $button){ 
               
                echo '
                <a href="'.$button['category_link'].'">
                '.$button['category_name'].'
                </a>
                ';
            }
            echo '
            </div>
        </section>
        '; 
    }
   
}, 100);

