<?php

$blog_name		= get_bloginfo( 'name' );
$blog_desc		= get_bloginfo( 'description' );
$image_logo		= '';
$mobile_logo	= '';
$page_logo		= '';
$logo_sticky = '';


$header_style 	= noo_get_option('noo_header_nav_style','header1');

if ( noo_get_option( 'noo_header_use_image_logo', false )  ) {
	if ( noo_get_image_option( 'noo_header_logo_image', '' ) !=  '' ) {
		$image_logo = noo_get_image_option( 'noo_header_logo_image', '' );
		
	}

	if('' !=  ($noo_header_sticky_logo_image = noo_get_image_option( 'noo_header_sticky_logo_image', '' )) ){
		$logo_sticky  = $noo_header_sticky_logo_image;
	}else{
		$logo_sticky = $image_logo;
	}

	if( is_page()) {
		$page_logo = noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo');
		$image_logo = !empty( $page_logo ) ? wp_get_attachment_url( $page_logo ) : $image_logo;
		if (noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo_sticky')) {
			$logo_sticky = noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo_sticky');
			$logo_sticky = !empty($logo_sticky) ? wp_get_attachment_url($logo_sticky) : ''; 
		}
	}
	
} else {
	if( is_page()) {

		if('' !=  ($noo_header_sticky_logo_image = noo_get_image_option( 'noo_header_sticky_logo_image', '' )) ){
			$logo_sticky  = $noo_header_sticky_logo_image;
		}else{
			$logo_sticky = $image_logo;
		}


		$page_logo = noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo');
		$image_logo = !empty( $page_logo ) ? wp_get_attachment_url( $page_logo ) : $image_logo;
		if (noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo_sticky')) {
			$logo_sticky = noo_get_post_meta(get_the_ID(),'_noo_wp_page_menu_logo_sticky');
			$logo_sticky = !empty($logo_sticky) ? wp_get_attachment_url($logo_sticky) : ''; 
		} 
	}
}

$mobile_logo = noo_get_image_option( 'noo_header_logo_mobile_image', $image_logo );

if(is_page()){
	$header_page = noo_get_post_meta(get_the_ID(),'_noo_wp_page_header_style');
	if( !empty($header_page) && $header_page != 'header'){
		$header_style = $header_page;
	}
}
//fixed for 404 page
if(is_404()){
	$header_style = 'header1';
}
?>
<div class="navbar-wrapper">
	<div class="navbar navbar-default <?php echo noo_navbar_class(); ?>" role="navigation">
		<div class="container-boxed max">
			<div class="navbar-header">
				<?php if ( is_front_page() ) : echo '<h1 class="sr-only">' . $blog_name . '</h1>'; endif; ?>
				<a class="navbar-toggle collapsed" data-toggle="collapse" data-target=".noo-navbar-collapse">
					<span class="sr-only"><?php echo __( 'Navigation', 'noo' ); ?></span>
					<i class="fa fa-bars"></i>
				</a>
				<?php if( apply_filters('noo_header_mobile_nav_user_menu',false) || noo_get_option('noo_header_nav_user_menu', true) ) : ?>
					<a class="navbar-toggle member-navbar-toggle collapsed" data-toggle="collapse" data-target=".noo-user-navbar-collapse">
						<i class="fa fa-user"></i>
					</a>
				<?php endif; ?>
				<a href="<?php echo apply_filters('noo_navbar_brand_url', home_url( '/' )); ?>" class="navbar-brand" title="<?php echo esc_attr($blog_desc); ?>">

				<?php echo ( $image_logo == '' ) ? '<span class="noo-blog-name">'.$blog_name .'</span>': '<img class="noo-logo-img noo-logo-normal" src="' . esc_url($image_logo) . '" alt="' . esc_attr($blog_desc) . '">'; ?>
				<?php echo ( $mobile_logo == '' ) ? '' : '<img class="noo-logo-mobile-img noo-logo-normal" src="' . esc_url($mobile_logo) . '" alt="' . esc_attr($blog_desc) . '">'; ?>
				<?php echo( $logo_sticky == '' &&  $image_logo == '')? '<span class="noo-blog-name-sticky">'.$blog_name .'</span>'  : '<img class="noo-sticky-logo-img" src="' . esc_url($logo_sticky) . '" alt="' . esc_attr($blog_desc) . '">'; ?>

				</a>
		
			</div> <!-- / .nav-header -->
			<?php if( noo_get_option('noo_header_nav_user_menu', true) ) : ?>
				<nav class="collapse navbar-collapse noo-user-navbar-collapse">
					<ul class="navbar-nav sf-menu">
						<?php noo_get_layout('user-menu-collapsed'); ?>
					</ul>
				</nav>
			<?php endif; ?>
			<nav class="collapse navbar-collapse noo-navbar-collapse">
	        <?php
				if ( has_nav_menu( 'primary' ) ) :
					wp_nav_menu( array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav sf-menu'
						) );
				else :
					echo '<ul class="navbar-nav nav"><li><a href="' . home_url( '/' ) . 'wp-admin/nav-menus.php">' . __( 'No menu assigned!', 'noo' ) . '</a></li></ul>';
				endif;
			?>
			</nav> <!-- /.navbar-collapse -->
		</div> <!-- /.container-fluid -->
	</div> <!-- / .navbar -->
</div>
