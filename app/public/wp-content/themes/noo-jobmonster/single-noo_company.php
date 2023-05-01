<?php get_header(); ?>
<div class="container-boxed max offset main-content">
	<div class="row">
		<?php
		noo_get_layout('company/company', Noo_Company::get_layout());
		?>
	</div> <!-- /.row -->
</div> <!-- /.container-boxed.max.offset -->
<?php get_footer(); ?>