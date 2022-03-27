<?php 
/* Template Name: Places * Template Post Type: post*/ /*The template for displaying full width single posts. */
get_header(); 

?>
<div class="single-blog">
<?php 
    while(have_posts()){
        the_post(); 
        ?> 
        <h1 class="center-align large-font-size margin-row regular"><?php the_title();?></h1>
        <div><?php echo the_content();?></div>
        <?php 
    }
?>
</div>
<?php 

get_footer(); 
?>