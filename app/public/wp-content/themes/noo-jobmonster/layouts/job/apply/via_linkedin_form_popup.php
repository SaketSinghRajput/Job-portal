<?php $user_linked = $_SESSION["member_linkedin"];
$display_name = $user_linked['first_name'].' '.$user_linked['last_name'];
global $post;
$job_id = empty($job_id) ? $post->ID : $job_id;
$job_title = get_the_title($job_id);
$company_id = jm_get_job_company($job_id);
$company_name = !empty($company_id) ? get_the_title($company_id) : '';
?>
<div id="applyJobviaLinkedInModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="applyJobviaLinkedInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="applyJobviaLinkedInModalLabel"><?php esc_html_e('Apply for job via LinkedIn','noo')?></h4>
            </div>
            <div class="modal-body">
                <form id="apply_job_via_linkedin_form" class="form-horizontal jform-validate" method="post" enctype="multipart/form-data">
                    <div style="display: none">
                        <input type="hidden" name="action" value="apply_job_via_linkedin">
                        <input type="hidden" name ="in-profile-data" id="in-profile-data">
                        <input type="hidden" name="in-profile-email" id="in-profile-email" value="<?php echo $user_linked['email']; ?>"/>

                        <input type="hidden" name="in-profile-name" id="in-profile-name" value="<?php echo $display_name; ?>">
                        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id)?>">
                        <?php wp_nonce_field('noo-apply-job-via-linkedin', '_wpnonce')?>
                    </div>
                    <div class="form-group text-center noo-ajax-result" style="display: none"></div>
                    <div class="apply-via-linkedin-profile">
                        <input type="hidden" name="_attachment" class="in-profile-url" value="<?php echo $user_linked['linkedin_url'] ; ?>">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="in-profile-picture">
                                        <img src="<?php echo $user_linked['picture_url'] ?>" alt="<?php echo esc_html__('Avatar','noo') ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="in-profile-overview">
                                        <div class="in-profile-name"><?php echo esc_html($display_name) ; ?></div>
                                        <div class="in-profile-headline"><?php echo esc_html($user_linked['headline']); ?></div>
                                        <div class="in-profile-location"><?php echo esc_html($user_linked['location']['name']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <dl>
                                <?php if(!empty($user_linked['positions']['positions_current'] )): ?>
                                <dt class="in-profile-positions"><?php esc_html_e('Current','noo')?></dt>
                                <dd class="in-profile-positions"><ul>
                                        <li><?php echo esc_html($user_linked['positions']['positions_current'].'-'.$user_linked['positions']['current_company']) ?></li>
                                    </ul></dd>
                                <?php endif; ?>
                              <!--  <?php /*if($user_linked->threePastPositions->_total > 0): */?>
                                <dt class="in-profile-past"><?php /*esc_html_e('Past','noo')*/?></dt>
                                <dd class="in-profile-past"><ul></ul></dd>
                                <?php /*endif; */?>
                                <?php /*if($user_linked->education->_total > 0): */?>
                                <dt class="in-profile-educations"><?php /*esc_html_e('Education','noo')*/?></dt>
                                <dd class="in-profile-educations"><ul></ul></dd>
                                --><?php /*endif; */?>
                                <dt class="in-profile-email"><?php esc_html_e('Email','noo')?></dt>
                                <dd class="in-profile-email in-profile-email-value"><?php echo $user_linked['email']?></dd>
                                <?php
                                $cover_letter_field = jm_get_setting('noo_job_linkedin','cover_letter_field');
                                if($cover_letter_field!='hidden'){
                                    ?>
                                    <dt><?php esc_html_e('Cover letter','noo')?><?php if ( $cover_letter_field === 'optional' ) _e( '(optional)', 'noo' ); ?></dt>
                                    <dd class="apply-via-linkedin-cover-letter <?php if ( $cover_letter_field === 'required' ) echo 'required-field'; ?>">
                                        <textarea id="apply-via-linkedin-cover-letter" class="form-control <?php if ( $cover_letter_field === 'required' ) echo 'jform-validate' ?>" <?php if ( $cover_letter_field === 'required' ) echo 'required="required"'; ?> rows="5" name="linkedin-cover-letter"><?php echo sprintf( __("I am very interested in the %s position at %s. I believe my skills and work experience make me an ideal candidate for this role. I look forward to speaking to you about this position soon. Thank you for your consideration.\nBest regards \n", 'noo' ) , $job_title, $company_name ); ?></textarea>
                                    </dd>
                                <?php } ?>
                            </dl>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary"><?php _e('Send application','noo')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
