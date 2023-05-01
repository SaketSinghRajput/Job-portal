<?php
$heading_img  = noo_get_option( 'noo_job_heading_image', '' );
$heading_text = noo_get_option( 'noo_job_heading_text', '' );

$hide_empty_tax = jm_get_job_setting( 'hide_empty_tax','');

$show_search_location = noo_get_option( 'noo_show_job_location_search',1);
$show_search_category = noo_get_option( 'noo_show_job_category_search',1);
$show_search_type = noo_get_option( 'noo_show_job_type_search','');
$show_address           = noo_get_option( 'noo_show_job_address','');

$disable_multiple     = noo_get_option( 'noo_job_search_field_type',0);
$noo_enable_parallax = noo_get_option( 'noo_enable_parallax', 1 );
$heading_image = get_page_heading_image();
$page_layout  = get_page_layout();
$mode_type = (isset($_GET['mode'])) ? $_GET['mode'] : '';
list( $heading ) = get_page_heading();
?>
<header class="noo-page-heading noo-job-heading" style="background-image: url('<?php echo esc_url( $heading_img ); ?>'); <?php echo ( ! $noo_enable_parallax ) ? 'background: url(' . esc_url( $heading_image ) . ') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;' : 'background: rgba(67, 67, 67, 0.55);'; ?> ">
    <div class="container-boxed max">
        <?php if ( noo_get_option( 'noo_show_job_page_title', 1) ) : ?>
        	<h1 class="page-title"><?php echo esc_html($heading);?></h1>
        <?php endif;?>
        <?php if ( noo_get_option( 'noo_jobs_show_search', 1 ) ) : ?>
            <div class="noo-heading-search">
                <form id="noo-heading-search-form" method="get" action="<?php echo esc_url( apply_filters('noo_heading_search_form_action_url', get_post_type_archive_link( 'noo_job' )) ); ?>">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="noo-form-label">
                                <?php echo esc_html__( 'Search Job Now:', 'noo' ); ?>
                            </label>
                            <input type="text" name="s" class="noo-form-control" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_html__( 'Enter keywords...', 'noo' ); ?>">
                        </div>
                        <?php if($show_search_location):?>
                            <div class="col-sm-3">
                                <?php jm_job_advanced_search_field( 'job_location', $disable_multiple );?>
                            </div>
                        <?php endif;?>
                        <?php if($show_search_category):?>
                            <div class="col-sm-3">
                                <?php jm_job_advanced_search_field( 'job_category', $disable_multiple );?>                                  
                            </div>
                        <?php endif;?>
                        <?php if($show_search_type):?>
                        	<div class="col-sm-3">
                                <?php jm_job_advanced_search_field( 'job_type', $disable_multiple );?>                                  
                            </div>
                        <?php endif;?>
                        <?php do_action('noo_heading_job_search'); ?>
                        <?php if($show_address):
                            $map_type = jm_get_location_setting('map_type','');
                            $address = isset($_GET['_full_address']) ? $_GET['_full_address'] :'';
                            ?>
                            <div class="col-sm-3 mb15">
                                <div class="form-group">
                                    <?php if ($map_type == 'google'):
                                       wp_enqueue_script('location-picker');           
                                    ?>
                                    <div class="noo-location-picker-field-wrap">
                                        <label for="noo-mb-location-address" class="control-label">
                                            <?php esc_html_e('Location', 'noo'); ?>
                                        </label>
                                        <input id="noo-mb-location-address" class="noo-mb-location-address noo-form-control" type="text" name="_full_address" value="<?php esc_attr_e($address);?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'jobica') ?>" />
                                        <input type="hidden" class="noo-mb-lat" name="_full_address_lat">
                                        <input type="hidden" class="noo-mb-lon" name="_full_address_lon">
                                        <div class="noo-mb-job-location" id="_full_address" data-lat data-lon style="height: 300px;">
                                        </div>
                                    </div>
                                    <?php elseif ($map_type == 'bing'): 
                                        wp_enqueue_script('bing-map');?>
                                        <div class="noo-location-picker-field-wrap">
                                            <div class="heading_map_type">
                                                <label for="noo-mb-location-address" class="control-label">
                                                    <?php esc_html_e('Location', 'noo'); ?>
                                                </label>
                                                <input id="noo-mb-location-address-heading" class="noo-mb-location-address noo-form-control" type="text" name="_full_address" value="<?php echo esc_html($address); ?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'jobica') ?>"/>
                                            </div>
                                        <input type="hidden" class="noo-mb-lat" name="_full_address_lat" id="noo-mb-lat">
                                        <input type="hidden" class="noo-mb-lon" name="_full_address_lon" id="noo-mb-lon">
                                        <div class="noo-mb-job" data-id="_full_address">
                                            <div id="_full_address"></div>
                                        </div>
                                    </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif;?>
                        <div class="col-sm-2">
                            <label>&nbsp;</label>
                            <button style="display: block;" type="submit" class="btn btn-primary noo-btn-search"><?php echo esc_html__( 'Search', 'noo' ); ?></button>
                        </div>
                    </div>
                    <!-- <input type="hidden" name="post_type" value="noo_job"> -->
                </form>
                <?php if ( ! empty( $heading_text ) ): ?>
                    <div class="noo-search-html">
                        <?php echo $heading_text; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div><!-- /.container-boxed -->
    <?php if ( ! empty( $heading_image ) ) : ?>
        <?php if ( $noo_enable_parallax ) : ?>
            <div class="parallax" data-parallax="1" data-parallax_no_mobile="1" data-velocity="0.1" style="background-image: url(<?php echo esc_url( $heading_image ); ?>); background-position: 50% 0; background-repeat: no-repeat;"></div>
        <?php endif; ?>
    <?php endif; ?>
</header>