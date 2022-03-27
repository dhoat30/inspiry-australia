<?php 
get_header(); 
  ?> 
  <section class="inspiry-specials">
      <!-- hero section -->
    <div class="hero-section" style='background: url("<?php echo get_the_post_thumbnail_url(null,"full"); ?>")'>
        <div class="content"> 
            <h1 class="large-font-size regular">INSPIRY <?php echo get_the_title();?></h1>
        </div>
    </div>

    <!-- sale cards -->
        <?php
        $rows = get_field('special_cards', get_the_id());
        if( $rows ) {
           ?> 
           <ul class="special-cards row-container">
            <?php 
                // for each loop for repeater content 
                foreach( $rows as $row ) {
                    $image = $row['image'];
                    $link = $row['link'];          
                ?>
                <li class="card">
                    <a  href="<?php echo $link;?>" target="_blank">
                                        <img loading="lazy" src="<?php echo esc_url($image['url']);?>"
                                        alt="<?php echo get_the_title();?>" width="100%">
                    </a>
                </li>
                   
                <?php 
                }
            ?>
           </ul>
           <?php 
        }
        ?>

    </section>
<?php
get_footer();
?>