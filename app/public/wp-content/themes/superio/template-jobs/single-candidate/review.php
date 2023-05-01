<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$rating = get_comment_meta( $comment->comment_ID, '_rating', true );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="the-comment ">
		<div class="avatar">
			<?php echo get_avatar( $comment->user_id, '80', '' ); ?>
		</div>
		<div class="comment-box">
			<div class="flex-top clearfix">
				<div class="meta comment-author">
					<div class="info-meta">
						<strong>
							<?php comment_author(); ?>
						</strong>
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<div class="date"><em><?php esc_html_e( 'Your comment is awaiting approval', 'superio' ); ?></em></div>
						<?php else : ?>
							<div class="date">
								<i class="flaticon-event"></i><?php echo get_comment_date( get_option('date_format', 'd M, Y') ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="star-rating clear ali-right" title="<?php echo sprintf( esc_attr__( 'Rated %d out of 5', 'superio' ), $rating ) ?>">
					<span class="review-avg"><?php echo number_format((float)$rating, 1, '.', ''); ?></span>
					<?php echo WP_Job_Board_Pro_Review::print_review($rating); ?>
				</div>
			</div>
			<div itemprop="description" class="comment-text">
				<?php comment_text(); ?>
			</div>
		</div>
	</div>