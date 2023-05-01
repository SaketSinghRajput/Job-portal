<?php 
$num = ( noo_get_option( 'noo_footer_widgets', '3' ) == '' ) ? '3' : noo_get_option( 'noo_footer_widgets', '3' ); 
$style = '';
if($noo_footer_bg_image = noo_get_option('noo_footer_bg_image')){
	$style = ' style="background: url('.$noo_footer_bg_image.') scroll 0 0 no-repeat; background-size:cover"';
}
if ( $num != 0 ) : 
?>
	<div class="colophon wigetized hidden-print"<?php echo $style; ?>>
		<div class="container-boxed max">
			<div class="row">
				<?php

				$i = 0; while ( $i < $num ) : $i ++;
				switch ( $num ) {
					case 4 : $class = 'col-md-3 col-sm-6';  break;
					case 3 : 
						$class = 'col-sm-4';
					break;
					case 2 : $class = 'col-md-6 col-sm-12';  break;
					case 1 : $class = 'col-md-12'; break;
				}
				echo '<div class="' . $class . '">';
				dynamic_sidebar( 'noo-footer-' . $i );
				echo '</div>';
				endwhile;

				?>
			</div> <!-- /.row -->
		</div> <!-- /.container-boxed -->
	</div> <!-- /.colophon.wigetized -->

<?php endif; ?>