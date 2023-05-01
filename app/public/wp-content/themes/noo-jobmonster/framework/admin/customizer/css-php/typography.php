<?php
// Variables
$noo_typo_use_custom_fonts = noo_get_option( 'noo_typo_use_custom_fonts', false );
$noo_typo_headings_uppercase = noo_get_option( 'noo_typo_headings_uppercase', false );
$noo_typo_body_font_size = noo_get_option( 'noo_typo_body_font_size', noo_default_font_size() );

// Font size computed
$font_size_base       = $noo_typo_body_font_size;
$font_size_large      = ceil( $font_size_base * 1.285 );
$font_size_small      = ceil(($font_size_base * 0.8));
$line_height_computed = floor(($font_size_base * 1.7));

if( $noo_typo_use_custom_fonts ) :
	$noo_typo_headings_font = noo_get_option( 'noo_typo_headings_font', noo_default_font_family() );
	$noo_typo_headings_font_style = noo_get_option( 'noo_typo_headings_font_style', 'bold' );
	$noo_typo_headings_font_weight = noo_get_option( 'noo_typo_headings_font_weight', 'bold' );
	$noo_typo_headings_uppercase = noo_get_option( 'noo_typo_headings_uppercase', false );

	$noo_typo_body_font = noo_get_option( 'noo_typo_body_font', noo_default_font_family() );
	$noo_typo_body_font_style = noo_get_option( 'noo_typo_body_font_style', 'normal' );
	$noo_typo_body_font_weight = noo_get_option( 'noo_typo_body_font_weight', noo_default_font_weight() );

?>

/* Body style */
/* ===================== */
body {
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
	font-size: <?php echo esc_html( $noo_typo_body_font_size ) . 'px'; ?>;
	font-style: <?php echo esc_html( $noo_typo_body_font_style ); ?>;
	font-weight: <?php echo esc_html( $noo_typo_body_font_weight ); ?>;
}

/* Headings */
/* ====================== */
h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6 {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
	font-style: <?php echo esc_html( $noo_typo_headings_font_style ); ?>;
	font-weight: <?php echo esc_html( $noo_typo_headings_font_weight ); ?>;	
	<?php if ( !empty( $noo_typo_headings_uppercase ) ) : ?>
		text-transform: uppercase;
	<?php else : ?>
		text-transform: none;
	<?php endif; ?>
}
.jsteps .jstep-label, .btn, .content-footer .content-tags a, .widget_tag_cloud .tagcloud a, .widget_product_tag_cloud .tagcloud a, .widget_newsletterwidget .newsletter-submit {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}
/* Scaffolding */
/* ====================== */
select {
	font-size: <?php echo esc_html($font_size_base) . 'px'; ?>;
}

/* Bootstrap */
.btn,
.dropdown-menu,
.input-group-addon,
.popover-title
output,
.form-control {
	font-size: <?php echo esc_html($font_size_base) . 'px'; ?>;
}
legend,
.close {
	font-size: <?php echo floor($font_size_base * 1.5) . 'px'; ?>;
}
.lead {
	font-size: <?php echo floor($font_size_base * 1.15) . 'px'; ?>;
}
@media (min-width: 768px) {
	.lead {
		font-size: <?php echo floor($font_size_base * 1.5) . 'px'; ?>;
	}
}
pre {
	padding: <?php echo (($line_height_computed - 1) / 2) . 'px'; ?>;
	margin: 0 0 <?php echo ($line_height_computed / 2) . 'px'; ?>;
	font-size: <?php echo ($font_size_base - 1) . 'px'; ?>;
}
.panel-title {
	font-size: <?php echo ceil($font_size_base * 1.125) . 'px'; ?>;
}

@media screen and (min-width: 768px) {
	.jumbotron h1, .h1 {
		font-size: <?php echo ceil($font_size_base * 4.5) . 'px'; ?>;
	}
}

.badge,
.btn-sm,
.btn-xs,
.dropdown-header,
.input-sm,
.input-group-addon.input-sm,
.pagination-sm,
.tooltip {
	font-size: <?php echo esc_html($font_size_small) . 'px'; ?>;
}

.btn-lg,
.input-lg,
.input-group-addon.input-lg,
 pagination-lg {
	font-size: <?php echo esc_html($font_size_large) . 'px'; ?>;
}

/* WordPress Element */
/* ====================== */
.content-link,
.content-cite,
.comment-form-author input,
.comment-form-email input,
.comment-form-url input,
.comment-form-comment textarea,
.pagination .page-numbers,
.entry-tags span,
.widget.widget_recent_entries li a,
.default_list_products .woocommerce ul.products.grid li.product figcaption h3.product_title,
.default_list_products .woocommerce ul.products li.product figure figcaption .product_title,
.woocommerce div.product .wpn_buttons,
.woocommerce div.product .product-navigation .next-product a > span,
.woocommerce div.product .product-navigation .next-product a .next-product-info .next-desc .amount,
.woocommerce div.product .product-navigation .prev-product a > span,
.woocommerce div.product div.summary .variations_form label,
.woocommerce div.product div.summary .product_meta > span,
.woocommerce .list_products_toolbar .products-toolbar span,
.woocommerce ul.products li.product .price,
.woocommerce ul.products.list li.product h3.product_title,
.woocommerce div.product span.price,
.woocommerce div.product p.price,
.woocommerce div.product .woocommerce-tabs .nav-tabs > li > a,
.woocommerce .quantity .plus,
.woocommerce .quantity .minus,
.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta strong,
.woocommerce table.shop_attributes th,
.woocommerce table.cart .product-price,
.woocommerce table.cart .product-subtotal,
.woocommerce .checkout #order_review td.product-total,
.woocommerce .checkout #order_review .cart-subtotal td,
.woocommerce .checkout #order_review .order-total td,
.woocommerce .view_order .wrap_order_details table tr .amount,
.woocommerce .checkout_complete ul.order_details.general li.total strong,
.woocommerce table.my_account_orders tr td.order-total .amount,
.woocommerce .widget_price_filter .price_slider_amount {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}
<?php else : ?>
/* Body style */
/* ===================== */
body {
	font-size: <?php echo esc_html( $noo_typo_body_font_size ) . 'px'; ?>;
}

/* Headings */
/* ====================== */
h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6 {
	<?php if ( !empty( $noo_typo_headings_uppercase ) ) : ?>
		text-transform: uppercase;
	<?php else : ?>
		text-transform: none;
	<?php endif; ?>
}
<?php endif; ?>
.noo-counter-icon .noo-counter-item{
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}

/* Fix Heading Font */

.noo-job-category-wrap.style-grid .noo-job-category .category-item, {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}

h2.loop-item-title a{
	/* font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif !important; */
}

/*==== Fix Font Body ====  */
/* resume-home */
.noo-job-search-wrapper .job-advanced-search .job-advanced-search-form,
.advance-search-form-control .chosen-container-multi .chosen-choices li.search-field input[type="text"],
.form-group .chosen-container-single,
ul.noo-resume-list li .show-view-more .time-post,
.noo-step-icon .noo-step-icon-title,
.member-heading .member-heading-nav ul,
.member-manage,
.table > tbody > tr > td, .table > tbody > tr > th, 
.table > tfoot > tr > td, .table > tfoot > tr > th, 
.table > thead > tr > td, .table > thead > tr > th{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}
/* Recruitment Home */
.noo-job-category-wrap.style-grid .noo-job-category .category-item a .job-count,
.colophon.wigetized p,
.widget ul li a,
.noo-job-list-row .job-date-ago{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}

/* search map home */
.noo-job-map .job-advanced-search .job-advanced-search-form{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}
/* home v4*/
.noo-counter-icon .noo-counter-item,
.noo-list-job-category .noo-list-job-category-content ul{
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}

.noo-job-category-wrap.style-slider .noo-job-category .category-item a .job-count,
.noo-list-job-category .noo-list-job-category-content.style-1 ul li a .job-count {
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}
/*job-categories-home*/
.noo-job-category-wrap.style-list .view-more a,
.noo-job-category-wrap.style-list a .title,
.noo-heading-sc .noo-subtitle-sc{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}
.control-label,
.noo-job-category-wrap.style-list a .job-count{
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}
/*==jobs=== */

.noo-company-sc.style-grid .company-item .company-meta .company-name {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}

.company-cf-title,
.job-social .noo-social-title,
.noo-company-contact .noo-company-contact-title,
.form-control,
.entry-tags a,
.advance-search-form-control .dropdown-toggle,
.noo-heading-search .noo-form-control,
.form-control, .widget_newsletterwidget .newsletter-email, .form-group .chosen-container-single, .form-group .chosen-container-multi .chosen-choices, .wpcf7-form-control:not(.wpcf7-submit)
{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}

.noo-resume-archive-before , .noo-job-archive-before,
.control-label, {
	font-family: "<?php echo esc_html( $noo_typo_headings_font ); ?>", sans-serif;
}
/*===companies===*/
.noo-company.noo-company-style2 .company-cf-title,
.noo-company.noo-company-style2 .noo-company-heading,
.total-review,
.company-letters a,
.companies-overview.masonry-container li.masonry-item ul > li a,
.companies-overview.masonry-container li.masonry-item > .company-letter{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}
/* resume*/
.dataTables_info,
.noo-datatable td strong,
.noo-datatable td em a,
.noo-datatable td.job-manage-app ,
.noo-datatable td .text-center,
.dataTables_length label,
.dataTables_filter label,
.form-control-flat select,
.member-manage-toolbar .bulk-actions strong,
.member-manage em,
.resume-style-2 .resume-general ul li .noo-label,
.resume-style-2 .timeline-event a,
.resume-style-2 .skill .pregress-bar .progress_title ,
.resume .skill .pregress-bar .progress_title,
.resume .resume-content .resume-desc .resume-timeline .timeline-container .timeline-wrapper .timeline-series .timeline-event a{
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}

.noo-heading-search .noo-form-control,
.multiselect-native-select .dropdown-toggle, .advance-search-form-control .dropdown-toggle,
.form-control {
	font-family: "<?php echo esc_html( $noo_typo_body_font ); ?>", sans-serif;
}