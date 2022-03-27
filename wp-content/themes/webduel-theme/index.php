<?php 
get_header();
do_action('webduel_hero_section');  
?>
<div class="body-container index-page">
    <div class="row-container">
        <?php 
            while(have_posts()){
                the_post(); 
                if(!is_cart() ){ 
                    ?>
                     <h1 class="large-font-size regular center-align"><?php the_title();?></h1>
                    <?php 
                }
                ?>  
                    <div>
                        <?php the_content();?>
                    </div>

                <?php
            }
        ?>
    </div>
</div>
    

<?php 
    get_footer();
?> 

