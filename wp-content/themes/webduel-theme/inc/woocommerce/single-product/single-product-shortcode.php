<?php 
// design board short code
function design_board_button(){ 
    $designBoardButton = '<div class="wishlist-designer-board-container">
        <div class="design-board-save-btn-container" data-user="'.is_user_logged_in().'" data-id='.get_the_id().' data-name="'.get_the_title().'">         
            <i class="fa-regular fa-heart"></i>
        </div>'.
        do_shortcode('[yith_wcwl_add_to_wishlist]')
        .'
    </div>
    '; 
    return $designBoardButton; 
}

// design board button shortcode
add_shortcode('design_board_button_code', 'design_board_button'); 