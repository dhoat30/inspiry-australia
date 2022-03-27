<?php 
get_header(); 
?>
<section class="about-us">
    <section class="hero-section">
       <div class="hero-bg" style='background: url("<?php echo get_the_post_thumbnail_url();?>")'>
          <h1 class=" playfair-fonts "><?php echo get_the_content();?></h1>
       </div>
    </section>


    <?php 
                
           if(have_rows('image_title_content_layout')){ 
            while ( have_rows('image_title_content_layout') ){
                the_row();
                if( get_row_layout() == 'image_title_content' ){ 
                  $image = get_sub_field('image');
                  $imgUrl; 
                  if($image['sizes']['medium_large']){
                      $imgUrl = $image['sizes']['medium_large'];
                  }
                  else{
                      $imgUrl = $image['url'];
                  }
                    ?>
                    <section class="row-container  first-section">
                            <div>
                                <picture>
                                    <source media="(min-width:1366px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                    <source media="(min-width:600px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                    <img loading="lazy" src="<?php echo esc_url($imgUrl);?>"
                                    alt="<?php echo get_sub_field('title');?>" width="100%">
                                </picture>
                            </div>
                            <div class="content">
                                <div class="first">
                                   <h2 class="italic thin"><?php echo get_sub_field('title'); ?></h2>
                                   <?php   echo get_sub_field('content');?>
                                </div>
                            </div>

                        </section>
                    <?php 
                }
            }
           }
    ?>
<!-- second section -->
<?php 
                
                if(have_rows('Image_content_layout')){ 
                 while ( have_rows('Image_content_layout') ){
                     the_row();
                     if( get_row_layout() == 'image_content' ){ 
                       $image = get_sub_field('image');
                       $imgUrl; 
                       if($image['sizes']['medium_large']){
                           $imgUrl = $image['sizes']['medium_large'];
                       }
                       else{
                           $imgUrl = $image['url'];
                       }
                         ?>
                         <section class="row-container flex-row second-section reverse-row">
                                <div>
                                     <picture>
                                         <source media="(min-width:1366px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                         <source media="(min-width:600px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                         <img loading="lazy" src="<?php echo esc_url($imgUrl);?>"
                                         alt="Inspiry Diverse Range and Culture" width="100%">
                                     </picture>
                                 </div>
                                 <div class="content">
                                     <div class="first">
                                        <h2 class="italic thin"><?php echo get_sub_field('title'); ?></h2>
                                        <?php   echo get_sub_field('content');?>
                                     </div>
                                 </div>
                                
                             </section>
                         <?php 
                     }
                 }
                }
         ?>

         <!-- third section -->
<?php 
                
                if(have_rows('Image_content_layout')){ 
                 while ( have_rows('Image_content_layout') ){
                     the_row();
                     if( get_row_layout() == 'furniture_content' ){ 
                       $image = get_sub_field('image');
                       $imgUrl; 
                       if($image['sizes']['medium_large']){
                           $imgUrl = $image['sizes']['medium_large'];
                       }
                       else{
                           $imgUrl = $image['url'];
                       }
                         ?>
                         <section class="row-container flex-row third-section" >
                                <div>
                                     <picture>
                                         <source media="(min-width:1366px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                         <source media="(min-width:600px)" srcset="<?php  echo esc_url($imgUrl); ?>">
                                         <img loading="lazy" src="<?php echo esc_url($imgUrl);?>"
                                         alt="Furniture Range" width="100%">
                                     </picture>
                                 </div>
                                 <div class="content">
                                     <div class="first">
                                        <h2 class="italic thin"><?php echo get_sub_field('title'); ?></h2>
                                        <?php   echo get_sub_field('content');?>
                                     </div>
                                 </div>
                                
                             </section>
                         <?php 
                     }
                 }
                }
         ?>

    <div class="slogan">
                <h4 class="playfair-fonts center-align light-grey"><?php echo get_field('slogan'); ?></h4>

    </div>
</section>

<?php get_footer(); ?>