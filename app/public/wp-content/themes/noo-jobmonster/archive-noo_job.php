<?php 
get_header(); 

do_action('noo_job_archive_before_content');
?>
<div class="container-wrap">
    <div class="main-content container-boxed max offset">
        <div class="row">
            <div class="<?php noo_main_class(); ?>" role="main">
                <?php
                
                do_action('noo_job_archive_content');
                
                if ( noo_get_option( 'noo_jobs_featured', false ) && is_post_type_archive( 'noo_job' ) && ! is_search() ) {
                    echo do_shortcode( '[noo_jobs show=featured posts_per_page=' . noo_get_option( 'noo_jobs_featured_num', 4 ) . ' title="' . __( 'Featured Jobs', 'noo' ) . '" no_content="none" show_pagination="yes" choice_paginate="nextajax" css_class=" featured-jobs"]' );
                }
                
                jm_job_loop( array(
                    'paginate'      => noo_get_option( 'noo_jobs_list_pagination_style', '' ),
                    'title'         => '',
                    'display_style' => noo_job_list_display_type()
                ) );
                ?>
            </div> <!-- /.main -->
            <?php get_sidebar(); ?>
        </div><!--/.row-->
    </div><!--/.container-boxed-->
</div><!--/.container-wrap-->

<?php 
do_action('noo_job_archive_after_content');

get_footer(); 
?>
