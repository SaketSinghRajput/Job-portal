<?php
/**
 * style-3.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
wp_enqueue_script( 'vendor-carousel' );
$column = ( $list_column == '3' ? 'col-md-4' : 'col-md-3' );
?>
<div<?php echo( $id . $class . $custom_style ); ?>>

    <div class="noo-resume-category-wrap <?php echo esc_attr( $style ); ?>">
        <div class="noo-resume-category row is-flex" id="<?php echo $id_resume_cat = uniqid( 'resume-cat-' ) ?>">
			<?php
			if ( $list_job_category == 'all' or $list_job_category == '' ) {
				$categories    = get_terms( 'job_category', array(
					'orderby'    => 'NAME',
					'order'      => 'ASC',
					'hide_empty' => false,
				) );
				foreach ( $categories as $key => $cat ) :
					$cate_name = $cat->name;
					$job_count = noo_get_total_resume_category( $cat->term_id );
					$cate_link = get_term_link( $cat );
					?>
                    <div class="category-item <?php echo esc_attr( $column ) ?>">
                        <a href="<?php echo esc_url_raw(add_query_arg( '_job_category', $cat->term_id, get_post_type_archive_link('noo_resume') )) ?>">
                            <span class="title">
                                <?php echo esc_html( $cate_name ); ?>
                                <span class="job-count">(<?php echo absint( $job_count ); ?> )</span>
                            </span>
                        </a>
                    </div>
				<?php endforeach;
			} else {
				$list_cat          = explode( ',', $list_job_category );
				foreach ( $list_cat as $key => $cat ) :
					$cate = get_term_by( 'id', absint( $cat ), 'job_category' );
					if ( ! empty( $cate ) ):
						$cate_name = $cate->name;
						$job_count = noo_get_total_resume_category( $cate->term_id );
						$cate_link = get_term_link( $cate );
						$icon_markers   = get_term_meta( $cate->term_id, 'icon_type', true );
						if ( empty( $icon_markers ) ) {
							$icon_markers = 'fa-home';
						}
						?>
                        <div class="category-item <?php echo esc_attr( $column ) ?>">
                            <a href="<?php echo esc_url_raw(add_query_arg( '_job_category', $cat->term_id, get_post_type_archive_link('noo_resume') ))?>">
                                <span class="title">
                                    <?php echo esc_html( $cate_name ); ?>
                                    <span class="job-count">(<?php echo absint( $job_count ); ?> )</span>
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
			echo '<div class="view-more"><a href="' . esc_url( $url_more[ 'url' ] ) . '">' . esc_html( $url_more[ 'title' ] ) . '</a></div>';
		}
		?>
    </div>
</div>