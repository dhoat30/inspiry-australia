<?php 
get_header(); 
echo('this is an index page');
  while(have_posts()){
    the_post(); 
    ?>
    <div class="body-contaienr">
      <div class="row-container">
        <h1 class="center-align section-font-size playfair-fonts"><?php the_title();?></h1>
        <div><?php the_content();?></div>
      </div>
    </div>
    <?php
}

get_footer();
?>