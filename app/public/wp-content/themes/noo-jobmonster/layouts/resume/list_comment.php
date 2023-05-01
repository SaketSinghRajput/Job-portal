<?php
/**
 * list-comment.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

wp_enqueue_style('noo-rating');
wp_enqueue_script('noo-rating');

$user_id =get_current_user_id();
$resume_id = get_the_ID();
$total_review = noo_get_total_review($resume_id);
$total_review_point = noo_get_total_point_review_resume($resume_id);
?>
<div class="noo-resume-review col-md-8 ">
    <h3 class="noo-title">
        <span><?php echo sprintf(esc_html__('%s %s'), $total_review, ($total_review > 1 ? esc_html__('Reviews', 'noo') : esc_html__('Review', 'noo'))) ?></span>
    </h3>
    <h3 class="noo-sub-title">
        <span class="label-review"><?php echo esc_html__('Rate This Resume', 'noo') ?></span>
        <span class="total-review">
            <?php noo_box_rating($total_review_point, true) ?>
            <span style="font-size: 13px;">
            <?php if ($total_review > 0): ?>
                ( <?php echo sprintf(esc_html__('%s average based on %s %s', 'noo'), $total_review_point, $total_review, ($total_review > 1 ? esc_html__('Reviews', 'noo') : esc_html__('Review', 'noo'))) ?>
                )
            <?php
            else:
                echo esc_html__('( No reviews yet )', 'noo');
            endif;
            ?>
                </span>
        </span>
    </h3>
    <ol class="noo-list-comment">
        <?php
        $comments = get_comments(array(
            'post_id' => get_the_ID(),
            'number' => '15'
        ));

        foreach ($comments as $comment) :

            $review_id = $comment->comment_ID;

            $user_rating1 = get_comment_meta($comment->comment_ID, 'user_rating1', true);
            $user_rating2 = get_comment_meta($comment->comment_ID, 'user_rating2', true);
            $user_rating3 = get_comment_meta($comment->comment_ID, 'user_rating3', true);
            $total_rating = (float)(($user_rating1 + $user_rating2 + $user_rating3 ) / 3);

            $subject = get_comment_meta($comment->comment_ID, 'subject', true);

            ?>
            <li class="comment-item">
                <div class="comment-head">
                    <?php if (!empty($subject)) : ?>
                        <h4 class="subject">
                            <?php echo esc_html($subject); ?>
                        </h4>

                    <?php endif; ?>
                    <time datetime="<?php echo get_comment_date('d-m-Y', $comment->comment_ID); ?>">
                        <?php echo get_comment_date('M d, Y', $comment->comment_ID); ?>
                    </time>
                </div>

                <div class="comment-info">
                    <span class="er-rate-count"><?php noo_box_rating($total_rating, true) ?></span>
                    <span class="noo-box-reviewed">
                        <span class="reviewed-box-icon"><i class="fa fa-caret-down"></i></span>
                        <div class="noo-review-voted">
                             <div class="line-vote"><span><?php echo esc_html__('Education', 'noo'); ?></span><?php noo_box_rating($user_rating1, true) ?></div>
                             <div class="line-vote"><span><?php echo esc_html__('Work experience', 'noo'); ?></span><?php noo_box_rating($user_rating2, true) ?></div>
                             <div class="line-vote"><span><?php echo esc_html__('Summary of skills', 'noo'); ?></span><?php noo_box_rating($user_rating3, true) ?></div>
                        </div>
                     </span>
                    <span class="user-name"><?php echo esc_html__('By ', 'noo') ?><?php echo esc_html__($comment->comment_author) ?></span>
                </div>
                <p class="comment-content">
                    <?php echo esc_html($comment->comment_content) ?>
                </p>
            </li>
        <?php
        endforeach;
        ?>
    </ol>

	<?php 
	$can_post_review = noo_can_post_resume_review($resume_id);
	if(isset($_POST['post_review']) && ($_POST['post_review']=='disable')){
	    $can_post_review = false;
	}
	if($can_post_review):
		if (apply_filters('noo_resume_review_need_registration', true) && get_option( 'comment_registration' ) && ! is_user_logged_in() ) :
			echo sprintf(
				'<p class="must-log-in">%s</p>',
				sprintf(
					/* translators: %s: Login URL. */
					__( 'You must be <a href="%s">logged in</a> to post a review.','noo'),
					/** This filter is documented in wp-includes/link-template.php */
					wp_login_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ), get_the_ID() ) )
					)
				);
		else:
	?>
    <form id="myform" name="myform" class="noo-form-resume-comment row">
        <?php
        $current_user = wp_get_current_user();
        $name = !empty($current_user) ? $current_user->display_name : '';
        ?>
        <input type="hidden" name="user_name" value="<?php echo esc_attr($current_user->display_name) ?>"/>
        <input type="hidden" name="user_email" value="<?php echo esc_attr($current_user->user_email) ?>"/>
        <div class="noo-comment-item col-md-3">
            <input type="text" name="subject" placeholder="<?php echo esc_html__('Subject', 'noo') ?>"/>
            <input class="hide" type="text" name="email_rehot" autocomplete="off"/>
        </div>
        <div class="noo-comment-item col-md-9">
            <div class="row">
                <div class="container-rating col-sm-6">
                    <span class="label-rating"><?php echo esc_html__('Education', 'noo') ?></span>
                    <?php noo_box_rating(5, false, true, 'ed'); ?>
                </div>
                <div class="container-rating col-sm-6">
                    <span><?php echo esc_html__('Work experience', 'noo') ?></span>
                    <?php noo_box_rating(5, false, true, 'wx') ?>
                </div>
            </div>
            <div class="row">
                <div class="container-rating col-sm-6">
                    <span><?php echo esc_html__('Summary of skills', 'noo') ?></span>
                    <?php noo_box_rating(5, false, true, 'sok') ?>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <textarea name="message" placeholder="<?php echo esc_html__('Your review *', 'noo') ?>"></textarea>
        </div>
        <?php do_action('noo_resumes_review');?>
        <div class="col-md-12" id="button" value="Reset Form">
            <?php
            $url = '';
            if( isset($_GET['application_id'] ) && !empty($_GET['application_id']) ){
                $url =  add_query_arg( 'application_id', $_GET['application_id'], get_permalink( ) );
            }
            ?>
            <button class="noo-review-submit btn btn-primary" data-url="<?php echo esc_url($url); ?>">
                <?php echo esc_html__('Post a Review', 'noo') ?>
            </button>
            <p class="notice"></p>
        </div>
        <input type="hidden" name="action" value="noo_resume_review"/>
        <input type="hidden" name="resume_id" value="<?php the_ID() ?>"/>
    </form>
    <?php 
   	 endif;
    
    endif; 
    ?>
</div>
