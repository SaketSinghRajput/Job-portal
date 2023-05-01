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
        <div class="noo-job-category noo-grid-col <?php echo esc_attr( $mobile_class . ' ' .$tablet_class.' '.$desktop_class) ;?>" id="<?php echo $id_job_cat = uniqid( 'job-loc-' ) ?> " >
			<?php
			$i = 0;
			if ( $list_job_location == 'all' or $list_job_location == '' ) {
				$locations    = get_terms( 'job_location', array(
					'orderby'    => 'NAME',
					'order'      => 'ASC',
					'hide_empty' => ('true' == $hide_empty) ? false : true,
				) );
			} else {
                $locations     = explode( ',', $list_job_location );
			}
            foreach ( $locations as $key => $loc ) :
                if ( $i >= $limit_location )
                    break;
                $loc_name = $loc->name;
                $job_count = $loc->count;
                $loc_link = get_term_link( $loc );
                ?>
                <div class="category-item noo-grid-item">
                    <a href="<?php echo esc_url( $loc_link ); ?>">
                            <span class="title">
                                <?php echo esc_html( $loc_name ); ?>
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