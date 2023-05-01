<?php

global $post;
$job_id = $post->ID;

$facebook_app_id = jm_get_3rd_api_setting( 'facebook_app_id', '' );
if ( empty( $facebook_app_id ) ) {
	return;
}
?>
<a id="apply_via_facebook" class="btn btn-default" href="#"><?php _e( 'Apply via Facebook', 'noo' ); ?></a>
<div id="applyFacebookModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="applyFacebookModal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="applyFacebookModal"><?php esc_html_e( 'Apply for job', 'noo' ) ?></h4>
            </div>
            <div class="modal-body">
                <form id="apply_job_form" class="form-horizontal jform-validate" method="post"
                      enctype="multipart/form-data">
                    <div style="display: none">
                        <input type="hidden" name="action" value="apply_job">
                        <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">
						<?php wp_nonce_field( 'noo-apply-job' ) ?>
                    </div>
                    <div class="form-group text-center noo-ajax-result" style="display: none"></div>
                    <div class="form-group required-field">
                        <label for="candidate_name" class="control-label"><?php _e( 'Name', 'noo' ) ?></label>
                        <input type="text" class="form-control jform-validate" id="fb_candidate_name"
                               value="" name="candidate_name" autofocus
                               required placeholder="<?php echo esc_attr__( 'Name', 'noo' ) ?>">
                    </div>
                    <div class="form-group required-field">
                        <label for="candidate_email" class="control-label"><?php _e( 'Email', 'noo' ) ?></label>
                        <input type="email" class="form-control jform-validate jform-validate-email"
                               id="fb_candidate_email" value=""
                               name="candidate_email" required
                               placeholder="<?php echo esc_attr__( 'Email', 'noo' ) ?>">
                    </div>

                    <div class="form-group required-field">
                        <label for="application_message" class="control-label"><?php _e( 'Message', 'noo' ) ?></label>
                        <textarea class="form-control jform-validate" required="" aria-required="true"
                                  id="application_message" name="application_message"
                                  placeholder="<?php echo esc_html__( 'Your cover letter/message sent to the employer', 'noo' ) ?>"
                                  rows="8"></textarea>
                    </div>

                    <input type="hidden" value="" name="fb_candidate_id" id="fb_candidate_id">

					 <?php do_action( 'after_apply_job_form' ); ?>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary"><?php _e( 'Send application', 'noo' ) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>