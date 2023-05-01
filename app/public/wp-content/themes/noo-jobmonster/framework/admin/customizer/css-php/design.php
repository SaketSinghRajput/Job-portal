<?php
// Variables
$default_link_color = '#2e2e2e'; // noo_default_text_color();

$noo_site_link_color = $default_link_color;
$noo_site_link_hover_color = noo_get_option( 'noo_site_link_color',  noo_default_primary_color() );

$noo_site_link_color_lighten_10 = lighten( $noo_site_link_hover_color, '10%' );
$noo_site_link_color_darken_5   = darken( $noo_site_link_hover_color, '5%' );
$noo_site_link_color_darken_10   = darken( $noo_site_link_hover_color, '10%' );
$noo_site_link_color_darken_15   = darken( $noo_site_link_hover_color, '15%' );

$default_font_color = noo_default_text_color();
$default_headings_color = noo_default_headings_color();

$noo_typo_use_custom_fonts_color = noo_get_option( 'noo_typo_use_custom_fonts_color', false );
$noo_typo_body_font_color = $noo_typo_use_custom_fonts_color ? noo_get_option( 'noo_typo_body_font_color', $default_font_color ) : $default_font_color;
$noo_typo_headings_font_color = $noo_typo_use_custom_fonts_color ? noo_get_option( 'noo_typo_headings_font_color', $default_headings_color ) : $default_headings_color; 

$noo_header_custom_nav_font = noo_get_option( 'noo_header_custom_nav_font', false );
$noo_header_nav_link_color = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_link_color', $noo_site_link_color ) : $noo_site_link_color;
$noo_header_nav_link_hover_color = $noo_header_custom_nav_font ? noo_get_option( 'noo_header_nav_link_hover_color', $noo_site_link_hover_color ) : $noo_site_link_hover_color;

?>

body {
	color: <?php echo esc_html($noo_typo_body_font_color); ?>;
}

h1, h2, h3, h4, h5, h6,
.h1, .h2, .h3, .h4, .h5, .h6,
h1 a, h2 a, h3 a, h4 a, h5 a, h6 a,
.h1 a, .h2 a, .h3 a, .h4 a, .h5 a, .h6 a {
	color: <?php echo esc_html($noo_typo_headings_font_color); ?>;
}

h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
.h1 a:hover, .h2 a:hover, .h3 a:hover, .h4 a:hover, .h5 a:hover, .h6 a:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Global Link */
/* ====================== */
a:hover,
a:focus,
.text-primary,
a.text-primary:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.noo-job-search-wrapper .job-advanced-search .btn-search-submit,
.bg-primary,
.navbar-nav li.menu-item-post-btn > a,
.navbar-nav li.menu-item-post-btn > a:hover,
.navbar-nav li.menu-item-post-btn > a:focus,
.navbar-nav li.menu-item-post-btn > a:hover:hover {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.bg-primary-overlay {
  background: <?php echo fade($noo_site_link_hover_color, '90%'); ?>;
}

.read-more:hover, .read-more:focus, .read-more:active, .read-more.active, .open > .dropdown-toggle.read-more {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}

/* Navigation Color */
/* ====================== */

/* Default menu style */
.noo-menu li > a {
	color: <?php echo esc_html($noo_header_nav_link_color); ?>;
}
.noo-menu li > a:hover,
.noo-menu li > a:active,
.noo-menu li.current-menu-item > a {
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}

/* NavBar: Link */
.navbar-nav li > a,
.navbar-nav ul.sub-menu li > a {
	color: <?php echo esc_html($noo_header_nav_link_color); ?>;
}

body.page-menu-transparent .navbar:not(.navbar-fixed-top) .navbar-nav > li > a:hover,
.navbar-nav li > a:hover,
.navbar-nav li > a:focus,
.navbar-nav li:hover > a,
.navbar-nav li.sfHover > a,
.navbar-nav li.current-menu-item > a {
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}

/* Border color */
@media (min-width: 992px) {
	.navbar-default .navbar-nav.sf-menu > li > ul.sub-menu {
		border-top-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	}
	.navbar-default .navbar-nav.sf-menu > li > ul.sub-menu:before,
	.navbar-nav.sf-menu > li.align-center > ul.sub-menu:before,
	.navbar-nav.sf-menu > li.align-right > ul.sub-menu:before,
	.navbar-nav.sf-menu > li.align-left > ul.sub-menu:before,
	.navbar-nav.sf-menu > li.full-width.sfHover > a:before {
		border-bottom-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	}
}

/* Dropdown Color */
.navbar-nav ul.sub-menu li > a:hover,
.navbar-nav ul.sub-menu li > a:focus,
.navbar-nav ul.sub-menu li:hover > a,
.navbar-nav ul.sub-menu li.sfHover > a,
.navbar-nav ul.sub-menu li.current-menu-item > a {
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}


/* Button Color */
/* ====================== */
.read-more,
.read-more:hover {
	background-color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}


/* Other Text/Link Color */
/* ====================== */

.noo-page-heading .page-title .count,
.noo-page-heading .noo-company-heading .noo-company-info .noo-company-info .noo-company-name .count {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Loop */
.posts-loop.grid .event-info a:hover,
.posts-loop.grid.staffs .loop-item-title a {
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}
.posts-loop.grid.staffs .loop-item-title:before {
	background-color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}

.posts-loop.slider .loop-thumb-content .carousel-indicators li.active {
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Job + Resume */
.job-action a.bookmark-job i:hover,
.job-action a.bookmark-job.bookmarked,
.job-desc ul li:before,
.more-jobs ul li:before,
.company-custom-fields ul li:before,
.noo-ajax-result ajob,
.resume .title-general span,
.job-desc ul li i, .more-jobs ul li i,
.job-custom-fields ul li i,
.company-custom-fields ul li i,
.noo-counter-job .noo-counter-item i,
.resume-style-3 .resume-content .resume-desc .resume-about .candidate-field-icon i,
.page-heading-info .pull-right a,
.resume-style-4 .title-general span,
.resume-style-4 .resume-content .resume-desc .resume-general ul li span:first-child i,
.noo-resume-category-wrap.style-grid .noo-resume-category .category-item a .icon,
.candidate-info .candidate-field-icon i {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.link-alt {
	border-bottom-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Member */
.member-manage .table tbody tr:hover a:not(.btn-primary):hover,
.member-manage table tbody tr:hover a:not(.btn-primary):hover,
.noo-pricing-table .noo-pricing-column .pricing-content .pricing-info .readmore,
.noo-pricing-table .noo-pricing-column .pricing-content .pricing-info i,
.noo-page-heading .page-heading-info .pregress-bar {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.noo-pricing-table .noo-pricing-column .pricing-content .pricing-header .pricing-value .noo-price,
.jsteps li.completed .jstep-num a:before,
.jsteps li.active .jstep-num a:before,
.noo-pricing-table .noo-pricing-column .pricing-content .pricing-info .readmore:hover {
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.jpanel-title {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.featured_slider .page a.selected,
.jsteps li.completed .jstep-num a,
.jsteps li.active .jstep-num a,
.gmap-loading .gmap-loader > div {
	background-color: <?php echo esc_html($noo_site_link_color_darken_10); ?>;
}

<?php if( NOO_WOOCOMMERCE_EXIST ) : ?>
/* WooCommerce */
/* ====================== */
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions a:hover,
.woocommerce ul.products li.product figcaption .product_title a:hover {
	color: <?php echo esc_html($noo_header_nav_link_hover_color); ?>;
}

.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .button:hover,
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .shop-loop-quickview:hover,
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .yith-wcwl-add-to-wishlist .add_to_wishlist:hover,
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .yith-wcwl-add-to-wishlist .add_to_wishlist:hover:before,
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:hover,
.woocommerce ul.products li.product figure .product-wrap .shop-loop-actions .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:hover,
.woocommerce .widget_layered_nav ul li.chosen a:hover {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
<?php endif; ?>

/* WordPress Element */
/* ====================== */

/* Comment */
h2.comments-title span,
.comment-reply-link,
.comment-author a:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Post */
.content-meta > span > a:hover,
.hentry.format-quote a:hover,
.hentry.format-link a:hover,
.single .hentry.format-quote .content-title:hover,
.single .hentry.format-link .content-title:hover,
.single .hentry.format-quote a:hover,
.single .hentry.format-link a:hover,
.sticky h2.content-title:before {
	color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}

.content-thumb:before,
.entry-tags a:hover {
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.content-wrap .entry-tags a:hover{
	color:<?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Pagination */
.pagination .page-numbers:hover:not(.disabled),
.pagination .page-numbers.current:not(.disabled),
.post-navigation .prev-post,
.post-navigation .next-post,
.loadmore-loading span {
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Widget */
.wigetized .widget a:hover,
.wigetized .widget ul li a:hover,
.wigetized .widget ol li a:hover,
.wigetized .widget.widget_recent_entries li a:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* Shortcode */
/* ====================== */

.btn-primary,
.form-submit input[type="submit"],
.wpcf7-submit,
.widget_newsletterwidget .newsletter-submit,
.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.form-submit input[type="submit"]:hover,
.form-submit input[type="submit"]:focus,
.btn-primary.active,
.wpcf7-submit:hover,
.progress-bar {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
ul.noo-resume-list li .show-view-more .btn-primar{
	    color: #000 !important;
}

.btn-primary.pressable {
	-webkit-box-shadow: 0 4px 0 0 <?php echo esc_html($noo_site_link_color_darken_15); ?>,0 4px 9px rgba(0,0,0,0.75) !important;
	box-shadow: 0 4px 0 0  <?php echo esc_html($noo_site_link_color_darken_15); ?>,0 4px 9px rgba(0,0,0,0.75) !important;
}

.btn-link,
.btn.btn-white:hover,
.wpcf7-submit.btn-white:hover,
.widget_newsletterwidget .newsletter-submit.btn-white:hover,
.colophon.site-info .footer-more a:hover{
	color: <?php echo esc_html($noo_site_link_color); ?>;
}
.noo-btn-job-alert-form:hover{
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-tabs.vc_tta .vc_tta-tab > a:hover, .noo-tabs.vc_tta .vc_tta-tab > a:focus {
	color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}

.btn-link:hover,
.btn-link:focus {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.noo-social a {
	color: <?php echo esc_html($noo_typo_body_font_color); ?>;
}
.noo-social a:hover,
.login-form-links > span a,
.login-form-links > span .fa,
.form-control-flat > .radio i,
.form-control-flat > .checkbox i {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.form-control-flat .radio i:after {
	background: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.noo-step-icon .noo-step-icon-class:hover {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-step-icon .noo-step-icon-class:after {
	border-color: <?php echo esc_html($noo_site_link_color_lighten_10); ?>;
}
.noo-step-icon .noo-step-icon-item:after,
.noo-step-icon .noo-step-icon-item:before {
	background-color: <?php echo esc_html($noo_site_link_color_lighten_10); ?>;
}
.noo-recent-news.grid .loop-item-wrap:hover .loop-item-featured:before {
	background-color: <?php echo fade($noo_site_link_hover_color, '70%'); ?>;
}

/* FED */
#fep-content input[type="submit"],
#fep-content input[type="submit"]:hover,
#fep-content input[type="submit"]:focus {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.member-manage #fep-content a {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.member-manage #fep-content a:hover {
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-counter-icon .noo-counter-item .fa {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo_job.featured-job:before {
	border-left: solid 50px <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.company-list .company-item .company-item-meta .fa{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.company-list .company-item.featured-company:before{
	border-left: solid 50px <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.company-letters a.selected{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.wp-editor-wrap .button {
	background:  <?php echo esc_html($noo_site_link_hover_color); ?>!important;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>!important;
}
.btn-readmore {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.add-new-location a {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.woocommerce .woocommerce-MyAccount-navigation ul li.is-active a, .woocommerce .woocommerce-MyAccount-navigation ul li:hover a {
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}
.noo-topbar .noo-topbar-social ul li a:hover{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

/* fix primary Color */

.header-2 .navbar.navbar-fixed-top .navbar-nav > li > a {
  color: <?php echo esc_html($noo_header_nav_link_color); ?>; !important;
}
span.noo-blog-name{
	color:#fff;
}

.testimonial-title b,
.posts-loop-title h3 b,
.noo-job-category-wrap.style-grid .noo-job-category .category-item a .icon{
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}
.nav-item-member-profile.register-link a,
.nav-item-member-profile.register-link a:hover,
.noo-recent-news.slider .loop-item-wrap .loop-item-content .category-post,
.banner-icon:after,
.pagination-top .jobs-shortcode .pagination .page-numbers.current,
.noo-company-sc.style-grid .swiper-prev,
.noo-company-sc.style-grid .swiper-next,
.wpb_wrapper .owl-theme .owl-controls .owl-buttons div,
.wpb_wrapper .slider .owl-buttons div{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?> 
}
.owl-theme .owl-controls .owl-page.active span,
.owl-theme .owl-controls.clickable .owl-page:hover span,
.owl-theme .owl-controls .owl-buttons div,
.jobs.slider .owl-theme .owl-controls .owl-buttons div{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}
.noo-resume-archive-before .noo-display-type .noo-type-btn:hover,
.noo-resume-archive-before .noo-display-type .noo-type-btn.active{
	color: <?php echo esc_html($noo_site_link_hover_color); ?> ;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?> 
}
/* search-map-home */

.noo-job-map .job-advanced-search .btn-search-submit{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}
/*  RecruitExpert Home */
.prev.page-numbers.disabled:hover{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}
.header-2 .navbar .navbar-nav > li > a{
	color: <?php echo esc_html($noo_header_nav_link_color); ?>;
}

/* hiring-home */
h2.search-main-title b,
.noo-btn-bookmark.bookmarked,
.noo-job-category-wrap.style-slider .noo-job-category .category-item:hover .icon{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>
}
.vc_tta-style-outline .vc_tta-tabs-container ul li.vc_active a{
	color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
	border-bottom: 3px solid <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}

/*  candidate-home */
.noo-resume.style-2 .resume-pagination a i{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}
/* job */
.noo-job-grid .noo-job-item.featured-job:before{
	border-left: solid 50px <?php echo esc_html($noo_site_link_hover_color); ?>
}
.noo-job-archive-before .noo-display-type .noo-type-btn:hover,
.noo-job-archive-before .noo-display-type .noo-type-btn.active,
.mobile-job-filter:hover,.mobile-job-filter.active{
	color: <?php echo esc_html($noo_site_link_hover_color); ?> ;
	border-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}

/* Company Full infomation */

.noo-page-heading .noo-company-heading .noo-company-action .noo-follow-company{
	background-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}

/* Resume Lising/Grid/ Detail */
ul.noo-resume-grid li.featured-resume:before,
ul.noo-resume-list li.featured-resume:before{
	border-left: solid 50px <?php echo esc_html($noo_site_link_hover_color); ?>
}

.resume-style-2 .timeline-event > a:after,
.resume-style-2 .title-general i,
.resume-contact .noo-heading i,
.resume-style-3 .resume-content .resume-desc .resume-general ul li span:first-child i,
.resume .resume-content .resume-desc .resume-general ul li span:first-child i {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>
}
.noo-resume-info-heading .resume-info .noo-shortlist:hover{
	background-color:<?php echo esc_html($noo_site_link_hover_color); ?>;
	border-color:<?php echo esc_html($noo_site_link_hover_color); ?>
}
/* about */
.noo-quote.noo-quote-comma:before{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>
}
.noo-dashboard-table .pagination .paginate_button.active a,
.noo-dashboard-table .pagination .paginate_button a:hover:not(.disabled),
.jm-status.jm-status-pending{
	background-color:<?php echo esc_html($noo_site_link_hover_color); ?>;
	color:#fff;
}
.widget_noo_social_profile .social-icons a:hover i{
	color:<?php echo esc_html($noo_site_link_hover_color); ?>
}
/*===Blog===*/
.read-more, .read-more:hover{
    background-color: <?php echo esc_html($noo_site_link_hover_color); ?>
}

/* ========== Fix Font Color =============*/
.noo-resume-category-widget h4.widget-title,
.widget_noo_advanced_resume_search_widget h4.widget-title,
.noo-job-category-widget h4.widget-title,
.noo-job-type-widget h4.widget-title,
.noo-job-location-widget h4.widget-title,
.widget_noo_advanced_job_search_widget h4.widget-title,
h3.widget-title,
h5.item-author a,
h4.item-title a,
.box-info-entry h4 span,
.candidate-title h2,
.candidate-title h3{
	color:<?php echo esc_html($noo_typo_headings_font_color); ?> !important;
}

/* ==== Fix color font body -======*/
/* ==== Recruitment Home === */
.noo-job-category-wrap.style-grid .noo-job-category .category-item a .title,
.loop-item-wrap .item-info a em,
.noo-heading-sc .noo-subtitle-sc
{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}


.loop-item-wrap .item-info a:hover em{
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}

/*== resume-home == */

.noo-step-icon .noo-step-icon-title{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}

/*=== jobs-listing-home === */

.chosen-container.chosen-container-active.chosen-with-drop .chosen-single,
.advance-search-form-control .chosen-container-multi .chosen-choices li.search-field input[type="text"],
.chosen-container .chosen-results,
.job-tools .noo-btn-bookmark.bookmarked{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}

/* search-map-home */
.chosen-container .chosen-results,
.chosen-container-single .chosen-single span{
	color: <?php echo esc_html($noo_typo_body_font_color); ?> !important;
}

/* ===Recruitment=== */
.noo-counter-icon .noo-counter-item,
.noo-list-job-category-content ul li a{
		color: <?php echo esc_html($noo_typo_body_font_color); ?>
}
.noo-list-job-category-content ul li a:hover .job-count
{
	color: <?php echo esc_html($noo_typo_body_font_color); ?> !important;
}
.noo-list-job-category-content ul li a:hover{
	color: <?php echo esc_html($noo_site_link_hover_color); ?>
}

/* === job categories-home ===*/
.noo-title-sc b,
.elementor-heading-title b,
.noo-job-category-wrap.style-list a .job-count{
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}
.noo-job-category-wrap.style-list a{
	color: <?php echo esc_html($noo_typo_body_font_color); ?> 
}
.wpb_wrapper .vc_custom_heading b {
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}

/* Hiring-home */
.noo-job-grid2 .noo-job-item h3 a,
.noo-job-grid2 .noo-job-item h3 span{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>;
}
.noo-job-grid2 .noo-job-item h3 a:hover,
.company-info .job-company a:hover .company-name{
	color: <?php  echo esc_html($noo_site_link_hover_color);?> !important;
}
.company-info .job-company a .company-name{
	color: <?php echo esc_html($noo_typo_body_font_color); ?> !important;
}

.vc_tta-tabs-list .vc_tta-style-outline .vc_tta-tabs-container ul li.vc_active a{
	color: <?php  echo esc_html($noo_site_link_hover_color);?> !important;
	border-bottom: 3px solid <?php  echo esc_html($noo_site_link_hover_color);?> !important;
}

/*=== job====  */

.noo-job-grid .noo-job-item h3 a,
.noo-job-grid .noo-job-item .job-date-ago{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>;
}
.noo-job-grid .noo-job-item:hover h3 .job-date-ago,
.noo-job-grid .noo-job-item:hover .job-company .company-name{
	color:#fff !important;
}

.noo-job-grid .noo-job-item:hover .job-company a,
.noo-job-grid .noo-job-item:hover .job-meta .entry-date span{
	color:#fff !important;
}
.pagination .page-numbers:hover:not(.disabled), .pagination .page-numbers.current:not(.disabled){
	color:#fff;
}
.widget ul li a{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}
.widget ul li a:hover{
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}

.form-control{
	color: <?php echo esc_html($noo_typo_body_font_color); ?> 
}
/*=== senior-designer ====*/
.noo-company-contact-form a,
.entry-tags a{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}
.noo-company-contact-form a:hover{
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}

/*====== companies===== */

.noo-datatable td em a,
.form-control-flat select,
.resume-style-2 .resume-general ul li .noo-label,
.resume-style-2,
.resume-style-2 .resume-general ul li .cf-text-value,
.resume-style-2 .skill .pregress-bar .progress_title ,
.noo-quote p,
.resume-content ul li .noo-label,
.resume-description p,
.timeline-event a,
.email a,
.noo-list-comment .comment-item .comment-content,
.company-list .company-item .company-item-meta a,
.company-letters a,
.companies-overview.masonry-container li.masonry-item ul > li a{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}
.noo-datatable tr td strong{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}
.noo-datatable tr:hover td strong{
	color:#fff !important;
}

.timeline-event a:hover,
.email a:hover,
.company-letters a:hover,
.companies-overview.masonry-container li.masonry-item ul > li a:hover {
	color: <?php  echo esc_html($noo_site_link_hover_color);?>
}
/*.box-content p,*/
span.job-company a,

span.job-category a,
span.job-date .entry-date span,

.noo-step-icon-advanced .noo-step-icon-advanced-list ul .noo-step-icon-title,
.noo-job-list-column .loop-item-company,
.noo-faq .noo_faq_group .noo_faq_item .noo_faq_content,
.content-meta > span > a,
.noo-job-category-wrap.style-grid .noo-job-category .category-item a .job-count,
.noo-subtitle-sc,
.noo-company-sc.style-grid .company-item .company-meta .company-name,
.noo-company-sc.style-grid .company-item .company-meta p,
.noo-company-sc.style-slider .company-item .company-meta .company-name,
.list.loop-item-wrap .loop-item-content .content-meta, .style-1 .loop-item-wrap .loop-item-content .content-meta
{
	color: <?php echo esc_html($noo_typo_body_font_color); ?>
}

.multiselect-container.dropdown-menu li.active label, .multiselect-container.dropdown-menu li:hover label {
	color: <?php echo esc_html($noo_site_link_hover_color); ?>;
}

.noo-job-type-widget li a:hover {
    color: <?php echo esc_html($noo_site_link_hover_color); ?> !important;
}

a.upgrade {
  color: <?php echo esc_html($noo_site_link_hover_color); ?>
}