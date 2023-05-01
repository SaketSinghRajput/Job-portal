<?php get_header(); ?>
<div class="container-wrap">
	<div class="container main-content">
		<div id="error-404">
			<div class="entry">
				<h1 class="error-title">404</h1>
				<h3 class="error-subtitle"><?php echo esc_html__( 'Page Not Found', 'noo' ); ?></h3>
				<div class="error-text"><?php echo esc_html__( 'The page you were looking for does not exist. It might have been removed had its address changed or become temporarily unavailable.', 'noo' ); ?></div>
				<div class="error-action">
					<a class="btn btn-primary" href="<?php echo esc_url(home_url('/'))?>" title="<?php echo esc_attr__('Home Page', 'noo');?>"><?php esc_html_e('Return to HomePage', 'noo');?></a>
				</div>
			</div>
		</div>
	</div><!--/container-->
</div>
<?php get_footer(); ?>