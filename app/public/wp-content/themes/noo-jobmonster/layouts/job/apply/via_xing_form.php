<?php
global $post;
$job_id = $post->ID;
$job_title = get_the_title($job_id);
$company_id = jm_get_job_company($job_id);
$company_name = !empty($company_id) ? get_the_title($company_id) : '';
$xing_consumer_key = jm_get_3rd_api_setting( 'xing_consumer_key', '' );
if ( empty( $xing_consumer_key ) ) {
	return;
}
?>
<a id="xing-login" class="btn btn-default xing-login" href="#"data-toggle="tooltip"
   title="<?php echo esc_attr__( 'Apply via Xing', 'noo' ); ?>">
    <script type="xing/login">
	        {
	            "consumer_key": "<?php echo jm_get_3rd_api_setting('xing_consumer_key', ''); ?>",
	            "onAuthLogin" : "onXingapply",
	            "color"       : "grey",
	            "size"        : "xlarge"
	        }
    </script>
</a>
<div id="applyXingModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="applyXingModal"
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
                    <div class="xing-profile" style="display: none">
                        <input type="hidden" name="action" value="apply_job_via_xing">
                        <input type="hidden" name="in-profile-data" id="in-profile-data" />
                        <input type="hidden" name="in-profile-email" id="in-profile-email" />
                        <input type="hidden" name="in-profile-name" id="in-profile-name" >
                        <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id)?>">
                        <?php wp_nonce_field('noo-apply-job-via-xing', '_wpnonce')?>
                    </div>
                    <div class="form-group text-center noo-ajax-result" style="display: none"></div>
                    <div class="apply-via-xing-profile">
                        <input type="hidden" name="_attachment" class="in-profile-url">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="in-profile-picture">

                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="in-profile-overview">
                                        <div class="in-profile-name"></div>
                                        <div class="in-profile-email"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <dl>
                                <dt class="in-profile-positions"><?php esc_html_e('Current Position','noo')?></dt>
                                <dd class="in-profile-positions"></dd>
                                <dt class="in-profile-company"><?php esc_html_e('Company','noo') ?></dt>
                                <dd class="in-profile-company"></dd>
                                <dt class="in-profile-location"><?php esc_html_e('Location','noo') ?></dt>
                                <dd class="in-profile-location"></dd>
                            </dl>

                        </div>
                    </div>
                    <div class="form-group required-field">
                        <label for="application_message" class="control-label"><?php _e( 'Message', 'noo' ) ?></label>
                        <textarea id="apply-via-xing-cover-letter" class="form-control jform-validate" required="required" rows="5" name="xing-cover-letter"><?php echo sprintf( __("I am very interested in the %s position at %s. I believe my skills and work experience make me an ideal candidate for this role. I look forward to speaking to you about this position soon. Thank you for your consideration.\nBest regards \n", 'noo' ) , $job_title, $company_name ); ?></textarea>
                    </div>

                    <input type="hidden" value="" name="xing_candidate_id" id="xing_candidate_id">

					<?php do_action( 'after_apply_job_form' ); ?>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary"><?php _e( 'Send application', 'noo' ) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function onXingapply(response) {
        var _in_profile = jQuery('.apply-via-xing-profile');
        var xing_profile = jQuery('.xing-profile');

        _in_profile.find('.in-profile-name').html( response.user.display_name);
        _in_profile.find('.in-profile-email').html( response.user.active_email);
        _in_profile.find('input.in-profile-url').val( response.user.permalink);
        xing_profile.find('input#in-profile-name').val(response.user.display_name);
        xing_profile.find('input#in-profile-email').val(response.user.active_email);
        _in_profile.find('dd.in-profile-location').html(response.user.business_address.city);
        _in_profile.find('dd.in-profile-company').html(response.user.professional_experience.primary_company.name);
        _in_profile.find('dd.in-profile-positions').html(response.user.professional_experience.primary_company.title);
        jQuery('input#in-profile-data').val( JSON.stringify( response, null, '' ) );

        if ( response.user.photo_urls.maxi_thumb) {
            // reset img
            jQuery('.in-profile-picture').html('');
            jQuery('<img/>').attr('src', response.user.photo_urls.maxi_thumb ).attr('alt', response.user.display_name ).appendTo('.in-profile-picture');
            // _in_profile.find('img').attr('src', data.pictureUrl );
            // _in_profile.find('img').attr('alt', data.formattedName );
        } else {
            _in_profile.find('.in-profile-picture').parent().hide();
        }
        jQuery('#applyXingModal').modal('show');
        xing.logout();
    }
</script>