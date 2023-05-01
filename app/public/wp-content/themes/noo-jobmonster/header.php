<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php wp_head(); ?>
<!--[if lt IE 9]>
<script src="<?php echo NOO_FRAMEWORK_URI . '/vendor/respond.min.js'; ?>"></script>
<![endif]-->
</head>
<body <?php body_class(); ?>>
<?php wp_body_open() ?>
<div class="<?php noo_site_class( 'site' ); ?>" <?php noo_job_schema(); ?> >
	<?php 
	
	$rev_slider_pos = home_slider_position();
	
	if($rev_slider_pos == 'above') {
		noo_get_layout( 'slider-revolution');
	}
	
	?>
	
	<?php if( ! (int) noo_get_post_meta( get_the_ID(), '_noo_wp_page_hide_header', false )) :?>
	<header class="noo-header <?php noo_header_class(); ?>" id="noo-header" <?php noo_header_schema(); ?>>
		<?php
		if(noo_get_option('noo_header_top_bar', 0)){
			noo_get_layout('topbar');
		}
		?>
		<?php noo_get_layout('navbar'); ?>
	</header>
	<?php endif; ?>
	
	<?php
	if($rev_slider_pos == 'below') {
		noo_get_layout( 'slider-revolution');
	}
	
	noo_get_layout('heading'); ?>