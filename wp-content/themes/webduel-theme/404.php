<?php


get_header(); ?>

<div id="primary" class="content-area row-container not-found">
	<div id="content" class="site-content" role="main">

		<header class="page-header">
			<h1 class="large-font-size center-align regular margin-row"><?php _e( 'Not Found', 'twentythirteen' ); ?></h1>
		</header>

		<div class="page-wrapper">
			<div class="page-content">
				<h2 class="poppins-font center-align medium-font-sizeium regular">
					<?php _e( 'This is somewhat embarrassing, isn’t it?', 'twentythirteen' ); ?></h2>
				<p class="poppins-font center-align paragraph-font-size regular">
					<?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'twentythirteen' ); ?>
				</p>

				<?php get_search_form(); ?>
			</div><!-- .page-content -->
		</div><!-- .page-wrapper -->

	</div><!-- #content -->

	<div class="fabric-calculator-container">
		<div>

		</div>
	</div>
</div><!-- #primary -->

<?php get_footer(); ?>