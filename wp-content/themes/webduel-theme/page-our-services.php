<?php 
get_header(); 
  ?>
    <section class="service-page">
        <div class="hero-section"  style='background: url("<?php echo get_site_url(); ?>/wp-content/uploads/2020/11/AdobeStock_171006496.jpg") no-repeat center top/cover;'>
            <div class="hero-overlay"></div>
        </div>    
        <div class="stamp hero-content">
            <i class="fal fa-home-alt"></i>
            <div class="section-font-size">INSPIRY</div>
            <div class="medium-font-size">Interior Design Services</div>
            <a class="rm-txt-dec button btn-dk-green" href="<?php echo get_site_url();?>/consultation">MAKE AN APPOINTMENT</a>
        </div>
    </section>

    <div class="services-section row-section margin-row">
        <div class="heading-line-through">
            <div class="underline-dg"></div>

            <div class="large-font-size center-align">Our Services</div>
        </div>
        
        <div class="flex">

        <?php 

            $argsLoving = array(
                'post_type' => 'loving',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => array( 'our-services'),
                    )
                    ), 
                    'orderby' => 'date', 
                    'order' => 'ASC'
            );
            $loving = new WP_Query( $argsLoving );

            while($loving->have_posts()){ 
                $loving->the_post(); 

                ?>      
                        <div class="cards">
                            <div>
                                <img src="<?php echo get_the_post_thumbnail_url(null,"full"); ?>" alt="Khroma">                      
                                <div class="column-font-size"><?php the_title(); ?></div>
                                <div class="paragraph center-align"><?php the_content();?></div>
                            </div>
                        </div>
                   
                <?php 

            }
            wp_reset_postdata();
            ?>
            

          
                                
        </div>                                
    </div>

    <section class="service-page">
        <div class="hero-section"  style='background: url("<?php echo get_site_url(); ?>/wp-content/uploads/2020/11/HELP.jpg") no-repeat center top/cover;'>
            <div class="hero-overlay"></div>
        </div>    
        <div class="content">
            
            <div class="section-font-size white center-align">Free half an hour consultation </div>
         
            <a class="rm-txt-dec button btn-dk-green" href="<?php echo get_site_url();?>/consultation">Book Now</a>
        </div>
    </section>

    <section class="testimonials">
        <div class="flex">
            <div>
                <div class="hero-section"  style='background: url("<?php echo get_site_url(); ?>/wp-content/uploads/2020/11/IMG_8586_large.jpg") no-repeat center top/cover;'>
                    <div class="hero-overlay"></div>
                </div>

                <div class="content">
                    <div class="white center-align large-font-size">Testimonials</div>
                </div>
            </div>
            <div class="quote">
                <i class="fas fa-quote-left"></i>
                <div class='background beige-color-bc'>
                    <div class="column-font-size regular">Corrine has a great sense of style, taste & knew what I wanted. She helped me so much and inspired me with lights, drapes, furniture and painting. She took all the pressure away and I love everything.</div>
                    <span class="poppins-font medium medium-font-size">Mary Jaques</span>
                    <div class="poppins-font medium-font-size regular">Bay Of Plenty</div>
                </div>
                
            </div>

            
        </div>
    </section>
  

<?php
get_footer();
?>