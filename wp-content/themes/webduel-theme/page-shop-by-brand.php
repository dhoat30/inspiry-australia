<?php 
get_header(); 
?>
 <!--second section --> 
 <section class="shop-by-brand">
    <section class="row-container second-section">
        <h1 class="center-align section-font-size margin-elements"><?php the_title();?></h1>

        <div class="flex">
            <?php 

                        $shopByBrand = array(
                            'post_type' => 'shop_by_brand',
                            'posts_per_page' => -1,
                            'post_status' => 'publish',
                            
                                'orderby' => 'date', 
                                'order' => 'ASC'
                        );
                        $brand = new WP_Query( $shopByBrand );

                        while($brand->have_posts()){ 
                            $brand->the_post(); 

            ?>      
                <div class="cards">
                    <div>   
                            <a class="rm-txt-dec" href="<?php echo get_field('add_a_link');?>">
                                <div class="medium-font-size center-align text-padding" style="text-transform: uppercase;"><?php the_title(); ?></div>
                                <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="<?php the_title();?>">                      
                                
                             </a>
                    </div>
                </div>
            
                <?php 

                }
                wp_reset_postdata();
                ?>
        
        </div>
    </section>
</section>
<?php get_footer(); ?>