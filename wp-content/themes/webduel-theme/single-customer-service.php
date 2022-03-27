<?php 
get_header(); 
?>
<div class="customer-service-page">
    <div class="container">
        <div class="sidebar-mobile-menu">
            <button class="secondary-button">
                <span>Customer Service Menu</span>
                <i class="fal fa-angle-down"></i>
            </button>
        </div>
        <div class="sidebar">
            <h3 class="sidebar-title">Customer Service</h3>
                <?php
                wp_nav_menu(
                        array(
                            'theme_location' => 'customer-service-sidebar', 
                            'container_id' => 'customer-service-sidebar'
                        ));
                ?>   
        </div>
        <div class="main-container">
            <?php 
                while(have_posts()){
                    the_post(); 
                    ?>
                        <h1 class="title"><?php the_title();?></h1>
                        <div class="content"> 
                            <?php the_content();?>
                        </div>

                    <?php
                }
            ?>
        </div>
    </div>
</div>

<?php 
    get_footer();
?> 