<?php
/**
 * style-1.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
wp_enqueue_script( 'vendor-carousel' );
?>
<div<?php echo( $id . $class . $custom_style ); ?>>
    <div class="noo-job-category-wrap <?php echo esc_attr( $style ); ?>">

        <div class="noo-job-category" id="<?php echo $id_job_loc = uniqid( 'job-loc-' ) ?>">
            <?php
            $i = 0;
            if ( $list_job_location == 'all' or $list_job_location == '' ) {
                $locations    = get_terms( 'job_location', array(
                    'orderby'    => 'NAME',
                    'order'      => 'ASC',
                    'hide_empty' => ('true' == $hide_empty) ? false : true,
                ) );
                foreach ( $locations as $key => $loc ) :
                    if ( $i >= $limit_location )
                        break;
                    $loc_name = $loc->name;
                    $job_count = $loc->count;
                    $loc_link = get_term_link( $loc );
                    $icon_markers   = get_term_meta( $loc->term_id, 'icon_type', true );
                    if ( empty( $icon_markers ) ) {
                        $icon_markers = 'fa-home';
                    }
                    ?>
                    <div class="category-item">
                        <a href="<?php echo esc_url( $loc_link ); ?>">
                            <span class="icon">
                                <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                            </span>
                            <span class="title">
                                <?php echo esc_html( $loc_name ); ?>
                            </span>
                            <?php if ( 'true' == $show_job_count ) : ?>
                                <span class="job-count">
                                    (<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?>)
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php $i++; endforeach;
            } else {
                $list_loc         = explode( ',', $list_job_location );
                foreach ( $list_loc as $key => $loc ) :
                    $loca = get_term_by( 'id', absint( $loc ), 'job_location' );
                    if ( ! empty( $loca ) ):
                        if ( $i >= $limit_location )
                            break;
                        $loca_name = $loca->name;
                        $job_count = $loca->count;
                        $loca_link = get_term_link( $loca );
                        $icon_markers   = get_term_meta( $loca->term_id, 'icon_type', true );
                        if ( empty( $icon_markers ) ) {
                            $icon_markers = 'fa-home';
                        }
                        ?>
                        <div class="category-item">
                            <a href="<?php echo esc_url( $loca_link ); ?>">
                                <span class="icon">
                                    <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                                </span>
                                <span class="title">
                                    <?php echo esc_html( $loca_name ); ?>
                                </span>
                                <?php if ( 'true' == $show_job_count ) : ?>
                                    <span class="job-count">
                                        (<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?> )
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php
                    endif;
                    $i++; endforeach;
            }
            ?>
        </div>
        <?php
        $url_more = isset( $url_more ) ? vc_build_link( $url_more ) : '';
        if ( !empty( $url_more[ 'url' ] ) ) {
            echo '<div class="view-more"><a class="btn btn-primary" href="' . esc_url( $url_more[ 'url' ] ) . '">' . esc_html( $url_more[ 'title' ] ) . '</a></div>';
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#<?php echo esc_attr( $id_job_loc ) ?>").owlCarousel({
            loop: true,
            margin: 10,
            responsiveClass: true,
            navigation: true,
            navigationText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
            items: <?php echo esc_attr( $list_column ) ?>,
            pagination: false,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:5,
                    nav:true,
                    loop:false
                }
            }
        });
    });
</script>