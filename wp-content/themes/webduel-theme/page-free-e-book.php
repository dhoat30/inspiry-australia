<?php 
get_header(); 
?>
 <!--second section --> 
 <section class="free-e-book">
    <h1 class="center-align section-font-size"><?php the_title();?></h1>
    <div class="poppins-font medium-font-sizeium center-align">HOW TO CHANNEL YOUR INNER INTERIOR DESIGNER</div>
    <section class="row-container hero-section">
        <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2020/11/DRAFT-2.jpg" alt="<?php the_title();?>">
    </section>

    <section class="second-section row-container">
        <div>
             <div class="medium-font-sizeium center-align">Find out what defines different popular styles such as mid century modern or scandinavian</div>

        </div>
        <div>
            <img src="<?php echo get_site_url();?>/wp-content/uploads/2020/11/Cole-Son-Topiary-1.jpg" alt="Cole Son Topiary">
        </div>
    </section>

    <section class="form row-container">
        <div>
            <div class="center-align section-font-size"> Enjoy the read!</div>
            <div class="poppins-font paragraph-font-size center-align"> Inspiry will send you a download link for the Ebook.</div>
        </div>
        <div class="form-box">
            <?php echo do_shortcode('[mc4wp_form id="47317"]'); ?>
        </div>
    </section>

    <section class="row-container last-section">
        <div class="section-font-size">
        The world of interior design is vast and ever expanding.
        </div>
        <br>
        <div class="paragraph-font-size poppins-font">
        It’s easy to get lost in all the latest market trends and diverse assortments of design styles. With the rise of the internet and social media over the last decade, the lines of design style have been blurred. It can be difficult to define the direction you want to explore.
    <br><br>
What works, and what doesn’t, has always been a question that we leave to the interior designer. But now, we are entering a new era where anyone can be their own interior designer. 
<br><br>
With the right knowledge and tools, you can create the interior design space of your dreams. All you need is the right outlook and a clear vision for what you want to achieve. With a little bit of inspiration, and a few innovative and creative ideas, there’s no limit on what you can accomplish.
        </div>
    </section>
</section>
<?php get_footer(); ?>