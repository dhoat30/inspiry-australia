<?php 
get_header(); 

  while(have_posts()){
    the_post(); 
    ?>
    <div class="body-contaienr">
      <div class="row-container">
          <a href="<?php echo get_home_url()."/profile" ?>" class="rm-txt-dec">Back to profile</a>
        <p><?php the_content();?></p>
      </div>
    </div>
    <?php
}

get_footer();
?>