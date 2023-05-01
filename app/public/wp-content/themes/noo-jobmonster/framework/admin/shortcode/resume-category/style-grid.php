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
?>
<div<?php echo( $id . $class . $custom_style ); ?>>

    <?php noo_the_heading_title( $title, $sub_title ); ?>

    <div class="noo-resume-category-wrap <?php echo esc_attr( $style ); ?>">
        <div class="noo-resume-category row is-flex">
            <?php
            if ( $list_job_category == 'all' or $list_job_category == '' ) {
                $categories    = get_terms( 'job_category', array(
                    'orderby'    => 'NAME',
                    'order'      => 'ASC',
                    'hide_empty' => false,
                ) );
                foreach ( $categories as $key => $cat ) :
                    $cate_name = $cat->name;
                    $cate_link = get_term_link( $cat );
	                $icon_markers   = get_term_meta( $cat->term_id, 'icon_type', true );
	                if ( empty( $icon_markers ) ) {
		                $icon_markers = 'fa-home';
	                }
	                ?>
                    <div class="category-item col-lg-<?php echo( 12 / $list_column ) ?> col-md-<?php echo( 12 / $list_column ) ?> col-sm-<?php echo( 12 / $list_column ) ?> col-xs-12">
                        <a href="<?php echo esc_url_raw(add_query_arg( '_job_category', $cat->term_id, get_post_type_archive_link('noo_resume') )); ?>">
                            <span class="icon">
                                <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                            </span>
                            <span class="title">
                                <?php echo esc_html( $cate_name ); ?>
                            </span>
                            <span class="job-count">
                                (<?php echo sprintf( _n( '%s Available worker', '%s Available Workers', noo_get_total_resume_category( $cat->term_id ), 'noo' ), noo_get_total_resume_category( $cat->term_id ) ); ?> )
                            </span>
                        </a>
                    </div>
                <?php endforeach;
            } else {
                $list_cat          = explode( ',', $list_job_category );
                foreach ( $list_cat as $key => $cat ) :
                    $cate = get_term_by( 'id', absint( $cat ), 'job_category' );
                    if (!is_wp_error($cate) &&  ! empty( $cate ) ):
                        $cate_name = $cate->name;
                        $cate_link = get_term_link( $cate );
	                    $icon_markers   = get_term_meta( $cate->term_id, 'icon_type', true );
	                    if ( empty( $icon_markers ) ) {
		                    $icon_markers = 'fa-home';
	                    }
                        ?>
                        <div class="category-item col-lg-<?php echo( 12 / $list_column ) ?> col-md-<?php echo( 12 / $list_column ) ?> col-sm-<?php echo( 12 / $list_column ) ?> col-xs-12">
                            <a href="<?php echo esc_url_raw(add_query_arg( '_job_category', $cat->term_id, get_post_type_archive_link('noo_resume') ));?>">
                                <span class="icon">
                                    <i class="fa <?php echo esc_attr( $icon_markers ) ?>"></i>
                                </span>
                                <span class="title">
                                    <?php echo esc_html( $cate_name ); ?>
                                </span>
                                <span class="job-count">
                                    (<?php echo sprintf( _n( '%s Open Vacancies', '%s Open Vacancies', noo_get_total_resume_category( $cate->term_id ), 'noo' ), noo_get_total_resume_category( $cate->term_id ) ); ?> )
                                </span>
                            </a>
                        </div>
                    <?php
                    endif;
                endforeach;
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