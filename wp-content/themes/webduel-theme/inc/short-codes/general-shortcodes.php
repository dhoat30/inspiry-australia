<?php
// add free shipping if it exist for the given product 
function signInModal()
{
?>
    <div class="sign-in-modal">
        <ul class="list">
            <li class="list-item">
                <?php
                $currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if (is_user_logged_in()) {
                ?>
                    <a href="<?php echo get_site_url(); ?>/home/members/edit-profile" class="anchor">
                        <i class="fa-light fa-user"></i>
                        <span>My Account </span>
                    </a>
                <?php
                } else {
                ?>
                    <a href="<?php echo get_site_url(); ?>/sign-in?redirect-link=<?php echo $currentLink ?>" class="anchor">
                        <i class="fa-light fa-user"></i>
                        <span>Sign In / Create Account </span>
                    </a>
                <?php } ?>
            </li>
            <li class="list-item">
                <a href="<?php echo get_home_url() . '/home/track-order' ?>" class="anchor">
                    <i class="fa-light fa-cube"></i>
                    <span>Track Orders </span>
                </a>
            </li>
            <li class="list-item">
                <a href="<?php echo get_home_url() ?>/home/members/design-boards" class="anchor">
                    <i class="fa-light fa-heart"></i>
                    <span>Create / Manage Design Boards </span>
                </a>
            </li>
            <li class="list-item">
                <a href="<?php echo get_home_url() ?>/wishlist" class="anchor">
                    <i class="fa-light fa-bookmark"></i>
                    <span>View Saved Items </span>
                </a>
            </li>
            <?php 
            if (is_user_logged_in()) {
                ?>
                     <li class="list-item">
                <a href="<?php echo get_home_url() ?>/wp-login.php?action=logout" class="anchor">
                <i class="fa-light fa-arrow-right-from-bracket"></i>
                    <span>Sign Out </span>
                </a>
            </li>
                <?php
                }?>
           
        </ul>
    </div>
<?php
}

add_shortcode('sign-in-modal', 'signInModal');

// add free shipping if it exist for the given product 
function designBoardExplainerModal()
{
?>
    <div class="design-board-header-modal">
        <?php
        if (!is_user_logged_in()) {
        ?>
            <div class="title">
                Something catch your eye?
            </div>

            <ul class="list">
                <li class="item"><span>1. Save</span> it to Favourites</li>
                <li class="item"><span>2. Create</span> a Design Board</li>
                <li class="item"><span>3. See</span> it all together</li>
            </ul>
            <div class="image-container">
                <?php
                $currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $argsDesignBoards = array(
                    'post_type' => 'page',
                    'pagename' => 'contact',
                    'posts_per_page' => 1
                );
                $queryDesignBoards = new WP_Query($argsDesignBoards);
                while ($queryDesignBoards->have_posts()) {
                    $queryDesignBoards->the_post();
                    $image = get_field('design_board_screenshot')['url'];
                ?>
                    <img src="<?php echo $image ?>" alt="Design Boards" />
                <?php
                }
                wp_reset_postdata();
                ?>
            </div>
            <p class="anchor">
                <a href="<?php echo get_site_url(); ?>/sign-in?redirect-link=<?php echo $currentLink ?>">Sign in</a> to see items you may have added using another computer or device.
            </p>
            <a href="<?php echo get_site_url(); ?>/sign-in?redirect-link=<?php echo $currentLink ?>" class="secondary-button">
                SIGN IN
            </a>
        <?php
        } else {
        ?>
            <div class="image-container">
                <?php
                $currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $argsDesignBoards = array(
                    'post_type' => 'page',
                    'pagename' => 'contact',
                    'posts_per_page' => 1
                );
                $queryDesignBoards = new WP_Query($argsDesignBoards);
                while ($queryDesignBoards->have_posts()) {
                    $queryDesignBoards->the_post();
                    $image = get_field('design_board_screenshot')['url'];
                ?>
                    <img src="<?php echo $image ?>" alt="Design Boards" />
                <?php
                }
                wp_reset_postdata();
                ?>
            </div>
            <a class="secondary-button" href="<?php echo get_site_url(); ?>/home/members/design-boards">View Design Boards</a>
        <?php
        }
        ?>

    </div>
<?php
}

add_shortcode('design-board-header-modal', 'designBoardExplainerModal');
