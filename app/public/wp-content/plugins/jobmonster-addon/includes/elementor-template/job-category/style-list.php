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
?>
<div<?php echo( $class ); ?>>

    <div class="noo-job-category-wrap <?php echo esc_attr( $style ); ?>">
        <div class="noo-job-category noo-grid-col <?php echo esc_attr( $mobile_class . ' ' .$tablet_class.' '.$desktop_class) ;?>" id="<?php echo $id_job_cat = uniqid( 'job-cat-' ) ?> " >
			<?php
			$i = 0;
			if ( $list_job_category == 'all' or $list_job_category == '' ) {
				$categories    = get_terms( 'job_category', array(
					'orderby'    => 'NAME',
					'order'      => 'ASC',
					'hide_empty' => ('true' == $hide_empty) ? false : true,
				) );
			} else {
                $categories     = explode( ',', $list_job_category );
			}
            foreach ( $categories as $key => $cat ) :
                if ( $i >= $limit_category )
                    break;
                $cate_name = $cat->name;
                $job_count = $cat->count;
                $cate_link = get_term_link( $cat );
                $cate_link = apply_filters('noo_job_widget_shortcode_category_link', $cate_link, $cat);
                ?>
                <div class="category-item noo-grid-item">
                    <a href="<?php echo esc_url( $cate_link ); ?>">
                        <span class="title">
                            <?php echo esc_html( $cate_name ); ?>
                            <?php if ( 'true' == $show_job_count ) : ?>
                                <span class="job-count">(<?php echo absint( $job_count ); ?>)</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </div>
                <?php $i++; endforeach;

			?>
        </div>
    </div>
</div>