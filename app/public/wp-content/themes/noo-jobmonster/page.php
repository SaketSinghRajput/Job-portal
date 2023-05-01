<?php get_header(); ?>

<div class="container-wrap">
	<div class="main-content container-boxed max offset">
		<div class="row">
			<div class="<?php noo_main_class(); ?> <?php noo_page_class(); ?>" role="main">
				<?php 
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						the_content();
					endwhile; 
				endif; 
				?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>