<?php
$heading_text = noo_get_option( 'noo_resume_heading_text' );

$heading_img = noo_get_option( 'noo_resume_heading_image', '' );
$show_search_location = noo_get_option( 'noo_show_resume_location_search',1);
$show_search_category = noo_get_option( 'noo_show_resume_category_search',1);
$disable_multiple     = noo_get_option( 'noo_resume_search_field_type',0);
$noo_enable_parallax = noo_get_option( 'noo_enable_parallax', 1 );
$heading_image = get_page_heading_image();
$page_layout  = get_page_layout();
?>
<header class="noo-page-heading noo-resume-heading"   style="background-image: url('<?php echo esc_url( $heading_img ); ?>'); <?php echo ( ! $noo_enable_parallax ) ? 'background: url(' . esc_url( $heading_image ) . ') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;' : 'background: rgba(67, 67, 67, 0.55);'; ?> ">
    <div class="container-boxed max">

        <?php if ( $resume_heading_title = noo_get_option( 'noo_resume_heading_title', '' ) ) : 
            ?>
            <h1 class="page-title"><?php echo esc_html($resume_heading_title);?></h1>
        <?php endif;?>
        <?php if ( noo_get_option( 'resume_search_form', 1 ) ) : ?>
            <div class="noo-heading-search">
                <form id="noo-heading-search-form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'noo_resume' ) ); ?>">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="noo-form-label">
                                <?php echo esc_html__( 'Search Resume Now:', 'noo' ); ?>
                            </label>
                            <input type="text" name="s" class="noo-form-control" value="<?php echo get_search_query(); ?>"  placeholder="<?php echo esc_html__( 'Enter keywords...', 'noo' ); ?>">
                        </div>
                        <?php if($show_search_location):?>
                            <div class="col-sm-3">
                              <?php jm_resume_advanced_search_field( '_job_location', $disable_multiple );?>
                            </div>
                        <?php endif;?>
                        <?php if($show_search_category):?>
                            <div class="col-sm-3">
                               <?php jm_resume_advanced_search_field( '_job_category', $disable_multiple );?>
                                </div>
                        <?php endif;?>
                        <?php do_action('noo_heading_resume_search'); ?>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <button style="display: block;" type="submit" class="btn btn-primary noo-btn-search"><?php echo esc_html__( 'Search', 'noo' ); ?></button>
                        </div>
                    </div>
                    <!-- <input type="hidden" class="form-control" name="post_type" value="noo_resume"/> -->
                </form>

                <?php if ( ! empty( $heading_text ) ): ?>
                    <div class="noo-search-html">
                        <?php echo $heading_text; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else:?>
            <h1 class="page-title"><?php echo esc_html($heading);?></h1>
        <?php endif; ?>
    </div><!-- /.container-boxed -->
    <?php if ( ! empty( $heading_image ) ) : ?>
        <?php if ( $noo_enable_parallax ) : ?>
            <div class="parallax" data-parallax="1" data-parallax_no_mobile="1" data-velocity="0.1" style="background-image: url(<?php echo esc_url( $heading_image ); ?>); background-position: 50% 0; background-repeat: no-repeat;"></div>
        <?php endif; ?>
    <?php endif; ?>
</header>