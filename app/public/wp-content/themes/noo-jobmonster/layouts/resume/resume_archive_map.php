<?php
$map_align      = ( noo_get_option( 'noo_resume_map_align','' ) == 'right' ) ? 'map-right' : 'map-left' ;
$noo_job_align  = ( $map_align =='map-right' ) ? 'map-left' : 'map-right';
$sidebar_align  = noo_get_option('noo_resume_map_sidebar_align','pull-right');
$display_type   = noo_job_list_display_type();
$page_layout    = get_page_layout();
$map_type       = jm_get_location_setting( 'map_type','' );
$lat_default    = jm_get_location_setting( 'latitude','' );
$lon_default    = jm_get_location_setting( 'longitude','' );
$zoom_default   = jm_get_location_setting( 'zoom','' );
$pagination   	= noo_get_option( 'noo_resumes_list_pagination_style', '' );
jm_resume_enqueue_map_script( $wp_query );
?>

    <div class="container-wrap">
        <div class="main-content container-full-width max offset">
            <div class="main-content-wrap">
                <div class="map-info <?php echo esc_attr( $map_align )  ?>" >
                    <div class="noo-resume-map ">
                        <div class="resume-map">
                            <div class="gmap-loading"><?php esc_html_e( 'Loading Maps', 'noo' ); ?>
                                <div class="gmap-loader">
                                    <div class="rect1"></div>
                                    <div class="rect2"></div>
                                    <div class="rect3"></div>
                                    <div class="rect4"></div>
                                    <div class="rect5"></div>
                                </div>
                            </div>
                            <?php if ($map_type == 'google'): ?>
                                <?php wp_enqueue_script( 'location-picker' ); ?>
                                <div id="gmap" data-map_style="dark"
                                     data-latitude="<?php echo esc_attr( $lat_default ); ?>"
                                     data-longitude="<?php echo esc_attr( $lon_default ); ?>"
                                     data-zoom="<?php echo esc_attr( $zoom_default ); ?>"
                                     data-fit_bounds="yes" style="height: 800px;">
                                </div>
                                <div class="container-map-location-search">
                                    <i class="fa fa-search"></i>
                                    <input type="text" class="form-control" id="map-location-search"
                                           placeholder="<?php echo esc_attr__( 'Search for a location...', 'noo' ); ?>" autocomplete="off">
                                </div>
                            <?php else:?>
                                <div id="bmap"
                                     data-latitude="<?php echo esc_attr($lat_default); ?>"
                                     data-longitude="<?php echo esc_attr($lon_default); ?>"
                                     data-zoom="<?php echo esc_attr($zoom_default); ?>"
                                     data-id="bmap"
                                     class="resume_bmap" style="height: 800px">
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="content-info <?php echo esc_attr( $noo_job_align );?>">
                    <div class="<?php noo_main_class(); ?> <?php echo esc_attr($sidebar_align); ?>" role="main">
                        <?php
                        $can_view_resume_list = jm_can_view_resumes_list();
                        if ( $can_view_resume_list ) {
                            jm_resume_loop(array(
                                'paginate'        => $pagination,
                            ));
                        } else {
                            list( $title, $link ) = jm_cannot_view_list_resume();
                            ?>
                            <article class="resume">
                                <h3><?php echo ($title); ?></h3>
                                <?php
                                if ( !empty( $link ) ) {
                                    echo (string)$link;
                                }
                                ?>
                            </article>
                            <?php
                        }
                        ?>
                    </div> <!-- /.main -->
                    <?php get_sidebar(); ?>
                </div>
            </div><!--/.row-->
        </div><!--/.container-boxed-->
    </div><!--/.container-wrap-->

<?php wp_footer(); ?>