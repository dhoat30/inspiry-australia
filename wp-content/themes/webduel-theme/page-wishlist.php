<?php
get_header(); 
?>

    <section class="wishlist-section">
        <div class="wishlist-container">
            <h1 class="regular">My Saved Items</h1>
        <?php
            the_content();
        ?>
        </div>
    </section>

<?php get_footer(); ?>