<?php get_header(); ?>
<div class="container-boxed max offset main-content">
	<div class="row">
		<div class="<?php noo_main_class(); ?>" role="main">
			<?php 
			while ( have_posts() ) : the_post(); 
			
				noo_get_layout( 'post/post', get_post_format());
				
				if(apply_filters('noo_show_post_nav', true)){
					noo_post_nav();
				}
				
				wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'noo' ), 'after' => '</div>' ) );
				
				if ( comments_open() ) :
				 	comments_template( '', true ); 
				 endif; 
				 
			endwhile; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>