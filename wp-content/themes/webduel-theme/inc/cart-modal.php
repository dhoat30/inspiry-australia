<?php 

add_action('cart_modal', 'modal_html'); 

function modal_html(){
    $argsModal = array(
        'post_type' => 'modal',
        'posts_per_page'=> 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'modal-categories',
                'field'    => 'slug',
                'terms'    => array( 'cart-page')
            )
            )

    );
    $modal = new WP_Query( $argsModal );

    while($modal->have_posts()){
        $modal->the_post();
                    
              echo   '<section class="modal-section" data-overlay="true" > 
             
                <i class="fal fa-times"></i>
                <div class="flex"> 
                            
                            <div>
                        <img src="'; 
                        echo get_the_post_thumbnail_url(null,"medium_large"); 
                        echo '"/>'; 
                    echo '</div>
                    <div class="content">
                        <div class="section-font-size  center-align">'; 
                    echo get_the_title();
                    echo ' </div>
                        <div class="center-align medium-font-size poppins-font">'; 
               echo get_the_content(); 
               echo ' </div>
                    </div>
                        
                    
                </div> 
                </section> ';
        } 
        wp_reset_postdata();
}