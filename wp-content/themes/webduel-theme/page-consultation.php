<?php 
get_header(); 
  ?>
  <section class="consultation-page">

  
<?php 
    while(have_posts()){ 
        the_post(); 
        ?>

        <div class="flex">
            <div class="form">
                <div class="column-font-size"><?php the_title();?></div>
                <?php the_content();?>
            </div>

            <div class="right-bar">
                <div class="stamp hero-content">
                    <div>
                        <i class="fal fa-home-alt center-align"></i>
                        <div class="section-font-size center-align">INSPIRY</div>
                        <div class="medium-font-size center-align">Interior Design Services</div>
                    </div>



                    <div class="services">

                    
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
                                <div class="column-font-size center-align"><?php the_title(); ?></div>
                                <div class="underline underline-dg"></div>
                                <div class="paragraph center-align"><?php the_content();?></div>
                            </div>
                        </div>
                        <?php 
                         }
                        ?>
                    </div>
                    
                </div>
            </div>
        </div>

        <?php
    }
?>
</section>

<?php
get_footer();
?>