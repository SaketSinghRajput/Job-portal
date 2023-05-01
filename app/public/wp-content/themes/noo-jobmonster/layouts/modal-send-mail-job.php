<?php

//$job_id = get_the_ID();
//$job_title = get_the_title(  $job_id );
//$job_link  = get_permalink(  $job_id );
//
//$content = $job_title . "\n" . $job_link;

?>
<div id="modalSendEmailJob" class="modal fade" tabindex="-1"
     role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content noo-form-email-job-wrap">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">
                    <?php _e('Send to a friend', 'noo'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <form class="job-send-email" method="POST">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                        for="friend_name"><?php echo esc_html__('Your full name', 'noo'); ?></label>
                                <input type="text" class="form-control" id="friend_name" name="friend_name"
                                       placeholder="<?php echo esc_html__('Enter your full name', 'noo'); ?>">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label
                                        for="friend_email"><?php echo esc_html__('Your friend email', 'noo'); ?></label>
                                <input type="email" class="form-control" id="friend_email" name="friend_email"
                                       placeholder="<?php echo esc_html__('Enter email address.', 'noo'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email_content"><?php echo esc_html__('Content', 'noo'); ?></label>
                        <textarea class="form-control" name="email_content" id="noo_form_email_content"
                                  rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <p class="help-block noo-job-mail-notice"></p>
                    </div>
                    <div class="row">
                        <?php do_action('noo_ajax_job_send_email'); ?>
                    </div>
                    <input type="hidden" name="job_id" id="noo_form_job_id" value="0"/>
                    <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce('noo-email-send-job')?>"/>
                    <input type="hidden" name="action" value="noo_ajax_job_send_email"/>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit"
                        class="btn btn-primary noo-btn noo-btn-send-job-email"><?php echo esc_html__('Send', 'noo'); ?></button>

            </div>

        </div>
    </div>
</div>