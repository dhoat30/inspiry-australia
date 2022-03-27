<?php
// design board short code
function design_board_modal()
{
    if (is_user_logged_in()) {

?>
        <div class="design-board-selection-modal">
            <div class="title">
                Save To Board
            </div>
            <div class="board-list-container">
                <div class="subtitle">
                    All Boards
                </div>
                <ul class="board-list">
                    <?php

                    $boardLoop = new WP_Query(array(
                        'post_type' => 'boards',
                        'post_parent' => 0,
                        'author' => get_current_user_id()
                    ));
                    while ($boardLoop->have_posts()) {
                        $boardLoop->the_post();
                        global $product;
                    ?>
                        <li class="list-item">
                            <div class="content">
                                <div class="board-title"><?php echo get_the_title(); ?> </div>
                            </div>
                            <button class="save-btn" data-boardID='<?php echo get_the_id(); ?>' data-postStatus='<?php echo get_post_status(); ?>'>
                                Save
                            </button>
                        </li>
                    <?php
                        wp_reset_postdata();
                    }
                    ?>
                </ul>
            </div>
            <div class="footer-container">
                <div class="create-board-container">
                    <button><i class="fa-solid fa-plus"></i></button>
                    <div class="subtitle">
                        Create Board
                    </div>
                </div>
                <button class="cancel">Cancel</button>
            </div>
        </div>
        <div class="create-board-modal">
            <div class="title">
                Create Board
            </div>
            <form id="create-board-form">
                <label for="name">Name</label>
                <input type="text" placeholder='Like "Bedroom Furniture" or "Design Ideas"' id="board-name" required>
                <div class="checkbox-container">
                    <input type="checkbox" id="board-checkbox" >
                    <p>Keep this board secret.</p>
                </div>
                <button class="primary-button">Create</button>
                <div class="error"></div>
            </form>
        </div>
    <?php
    } else {
    ?>
        <div class="design-board-selection-modal">
            <div class="title">
                Sign in to use design boards
            </div>
            <?php
            $currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            ?>
            <a class="primary-button" href="<?php echo get_site_url(); ?>/sign-in?redirect-link=<?php echo $currentLink ?>" class="text-decoration-none dark-grey regular" data-root-url='<?php echo get_home_url() ?>/account-profile'>
                Sign in
            </a>
            <i class="fa-regular fa-xmark"></i>
        </div>
<?php
    }
}

// design board button shortcode
add_action('wp_footer', 'design_board_modal', 30);
