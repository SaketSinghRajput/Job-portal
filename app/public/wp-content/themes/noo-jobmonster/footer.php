
<?php if( ! (int) noo_get_post_meta( get_the_ID(), '_noo_wp_page_hide_footer', false )) :?>
	
	<?php noo_get_layout( 'footer', 'widgetized' ); ?>
	
	<?php $noo_bottom_bar_content = noo_get_option( 'noo_bottom_bar_content', '&copy; 2016 JobMonster. Designed with <i class="fa fa-heart text-primary"></i> by NooTheme' ); ?>
	<?php if ( !empty( $noo_bottom_bar_content ) ) : ?>
		<footer class="colophon site-info">
			<div class="container-full">
				<div class="footer-more">
					<div class="container-boxed">
						<div class="row">
							<div class="col-md-12">
							<?php if ( $noo_bottom_bar_content != '' ) : ?>
								<div class="noo-bottom-bar-content">
									<?php echo wp_kses_post($noo_bottom_bar_content); ?>
								</div>
							<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div> <!-- /.container-boxed -->
		</footer> <!-- /.colophon.site-info -->
	<?php endif; ?>
	
<?php endif; ?>
</div> <!-- /#top.site -->
<?php wp_footer(); ?>
</body>
</html>
