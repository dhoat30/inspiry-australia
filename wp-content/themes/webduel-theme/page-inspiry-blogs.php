<?php get_header(); 
?>
<section class="inspiry-blogs">
    <!--first section --> 
    <section class="row-container first-section">
        <div class="sidebar row-container">
                <div class="poppins-font medium-font-sizeium">Category</div>
                <?php echo do_shortcode('[facetwp facet="blogs"]');?>
                <button onclick="FWP.reset()" class="facet-reset-btn">Reset</button>
        </div>
        <div class="flex">
        <?php echo do_shortcode('[facetwp template="blogs"]');?>

        <?php echo do_shortcode('[facetwp facet="pager_"]'); ?>
        </div>
    </section>

    <!--second section --> 
    <section class="row-container second-section">
        
        <div class="flex">
            <?php 

                        $argsBlog = array(
                            'post_type' => 'blogs',
                            'posts_per_page' => 2,
                            'post_status' => 'publish',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'blog-category',
                                    'field'    => 'slug',
                                    'terms'    => array('interior-inspiration'),
                                )
                                ), 
                                'orderby' => 'date', 
                                'order' => 'ASC'
                        );
                        $Blog = new WP_Query( $argsBlog );

                        while($Blog->have_posts()){ 
                            $Blog->the_post(); 

            ?>      
                <div class="cards">
                    <div>
                            <img src="<?php echo get_the_post_thumbnail_url(null,"large"); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                            <div class="medium-font-size center-align text-padding"><?php the_title(); ?></div>
                            <div class="paragraph-font-size poppins-font center-align text-padding"><?php 
                             echo wp_trim_words(get_the_content(), 15) ;?> </div>
                             <a class="button btn-dk-green rm-txt-dec" href="<?php the_permalink();?>">READ THE POST</a>
                    </div>
                </div>
            
                <?php 

                }
                wp_reset_postdata();
                ?>
        
        </div>
    </section>

    <!--third section --> 
    <section class="row-container third-section">
        <div class="section-font-size">The Latest </div>
        <div class="flex">
            <?php 

                        $argsBlog = array(
                            'post_type' => 'blogs',
                            'posts_per_page' => 4,
                            'post_status' => 'publish',
                                'orderby' => 'date', 
                                'order' => 'ASC'
                        );
                        $Blog = new WP_Query( $argsBlog );

                        while($Blog->have_posts()){ 
                            $Blog->the_post(); 

            ?>      
                <div class="cards">
                    <div>   
                            
                            <a class="rm-txt-dec" href="<?php the_permalink();?>">  
                                <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                                <div class="paragraph-font-size"><?php the_title(); ?></div>
                            </a>
                    </div>
                </div>
            
                <?php 

                }
                wp_reset_postdata();
                ?>
        
        </div>
    </section>

    <!--fourth section --> 
    <section class="fourth-section">
        <div class="flex-container">
             <div class="nav-buttons">
                 <button class="border-none button-border">Wallpaper</button>
                 <button class="border-none">Furniture</button>
                 <button class="border-none">Homeware</button>
             </div>   
            <div class="medium-font-size">The Latest </div>
            <div class="flex wallpaper --visible-flex">
            
                <?php 

                            $argsProduct = array(
                                'post_type' => 'product',
                                'posts_per_page' => 5,
                                'post_status' => 'publish',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'slug',
                                        'terms'    => array('blog-wallpaper'),
                                    )
                                    ), 
                                    'orderby' => 'date', 
                                    'order' => 'ASC'
                            );
                            $productQuery = new WP_Query( $argsProduct );

                            while($productQuery->have_posts()){ 
                                $productQuery->the_post(); 

                ?>      
                    <div class="cards">
                        <div>   
                                
                                <a class="rm-txt-dec" href="<?php echo get_the_permalink();?>">  
                                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                                    <div class="paragraph-font-size center-align"><?php the_title(); ?></div>
                                    <a class="button btn-dk-green rm-txt-dec" href="<?php echo get_field('link');?>">SHOP NOW</a>

                                </a>
                        </div>
                    </div>
                
                    <?php 

                    }
                    wp_reset_postdata();
                    ?>
            
            </div>
            <div class="flex furniture">
            
                <?php 

                            $argsBlog = array(
                                'post_type' => 'product',
                                'posts_per_page' => 5,
                                'post_status' => 'publish',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'slug',
                                        'terms'    => array('blog-furniture'),
                                    )
                                    ), 
                                    'orderby' => 'date', 
                                    'order' => 'ASC'
                            );
                            $Blog = new WP_Query( $argsBlog );

                            while($Blog->have_posts()){ 
                                $Blog->the_post(); 

                ?>      
                    <div class="cards">
                        <div>   
                                
                                <a class="rm-txt-dec" href="<?php echo get_field('link');?>">  
                                    <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                                    <div class="paragraph-font-size center-align"><?php the_title(); ?></div>
                                    <a class="button btn-dk-green rm-txt-dec" href="<?php echo get_field('link');?>">SHOP NOW</a>

                                </a>
                        </div>
                    </div>
                
                    <?php 

                    }
                    wp_reset_postdata();
                    ?>
            
            </div>
            <div class="flex homeware">
            
                <?php 

                            $argsBlog = array(
                                'post_type' => 'product',
                                'posts_per_page' => 5,
                                'post_status' => 'publish',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'slug',
                                        'terms'    => array('blog-homeware'),
                                    )
                                    ), 
                                    'orderby' => 'date', 
                                    'order' => 'ASC'
                            );
                            $Blog = new WP_Query( $argsBlog );

                            while($Blog->have_posts()){ 
                                $Blog->the_post(); 

                ?>      
                    <div class="cards">
                        <div>   
                                
                                <a class="rm-txt-dec" href="<?php echo get_field('link');?>">  
                                    <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                                    <div class="paragraph-font-size center-align"><?php the_title(); ?></div>
                                    <a class="button btn-dk-green rm-txt-dec" href="<?php echo get_field('link');?>">SHOP NOW</a>

                                </a>
                        </div>
                    </div>
                
                    <?php 

                    }
                    wp_reset_postdata();
                    ?>
            
            </div>
        </div>
        
    </section>
    
    <!--social meda section --> 

    <section class="row-container social-section">
        <div class="paragraph-font-size center-align">Inspiry On Instagram</div>
            <script src="https://assets.juicer.io/embed.js" type="text/javascript"></script>
        <link href="https://assets.juicer.io/embed.css" media="all" rel="stylesheet" type="text/css" />
        <ul data-per="6" class="juicer-feed" data-feed-id="inspirynz"><h1 class="referral"><a href="https://www.juicer.io"></a></h1></ul>
    </section>



     <!--fifth section --> 
     <section class="fifth-section">
        <div class="paragraph-font-size center-align">More Inspiry </div>
        <div class="flex">
            <?php 

                        $argsBlog = array(
                            'post_type' => 'blogs',
                            'posts_per_page' => 3,
                            'post_status' => 'publish',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'blog-category',
                                    'field'    => 'slug',
                                    'terms'    => array('design-inspiration'),
                                )
                                ),
                                'orderby' => 'date', 
                                'order' => 'ASC'
                        );
                        $Blog = new WP_Query( $argsBlog );

                        while($Blog->have_posts()){ 
                            $Blog->the_post(); 

            ?>      
                <div class="cards">
                    <div>   
                            
                            <a class="rm-txt-dec" href="<?php the_permalink();?>">  
                                <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="<?php echo get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', TRUE);?>">                      
                                <div class="paragraph-font-size"><?php the_title(); ?></div>
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

<?php 
    get_footer(); 
?>