<?php
$keyword = isset($_GET['s']) ? esc_html($_GET['s']) : '';
$get_tax=get_queried_object();
$fields = jm_get_job_alert_custom_fields();
?>
<div id="modalJobAlertForm" class="modal fade modalJobAlertForm" tabindex="-1"
     role="dialog"
     aria-labelledby="modalJobAlertForm"
     aria-hidden="true">
    <div class="modal-dialog noo-form-job-alert-dialog">

        <div class="modal-content noo-form-job-alert-wrap">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"><?php echo esc_html__('New Job Alert', 'noo'); ?></h4>
            </div>
            <div class="modal-body">
                <h4><?php echo esc_html__('Never miss a chance!', 'noo'); ?></h4>
                <p><?php echo esc_html__('Let us know your job expectations, so we can find you jobs better!', 'noo'); ?></p>
                <form class="noo-job-alert-form">

                    <div class="row">
                            <div class="col-sm-6">
                                <?php if (is_user_logged_in()): ?>
                                    <div class="form-group required-field">
                                        <label for="job_alert_name"><?php echo esc_html__('Alert Name', 'noo'); ?></label>
                                        <input required type="text" class="form-control" id="job_alert_name"
                                               name="job_alert_name"
                                               placeholder="<?php echo esc_html__('Your alert name', 'noo'); ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="form-group required-field">
                                        <label for="job_alert_email"><?php echo esc_html__('Email', 'noo'); ?></label>
                                        <input required type="email" class="form-control" id="job_alert_email"
                                               name="job_alert_email"
                                               placeholder="<?php echo esc_html__('Enter your email', 'noo'); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>
                           <div class="col-sm-6">
                               <div class="form-group">
                                   <label for="job_alert_keywords"><?php echo esc_html__('Keywords', 'noo'); ?></label>
                                   <input type="text" class="form-control" id="job_alert_keywords"
                                          name="job_alert_keywords"
                                          value="<?php echo esc_attr($keyword); ?>"
                                          placeholder="<?php echo esc_html__('Enter keywords to match jobs', 'noo'); ?>">
                               </div>
                           </div>
                         <div class="col-sm-6">
                             <div class="form-group"
                                  data-placeholder="<?php echo esc_html__('Select frequency', 'noo'); ?>">
                                 <label for="job_alert_frequency"><?php _e('Email Frequency', 'noo') ?></label>
                                 <?php
                                 $frequency_arr = Noo_Job_Alert::get_frequency();
                                 ?>
                                 <select class="form-control" name="job_alert_frequency" id="job_alert_frequency">
                                     <?php foreach ($frequency_arr as $key => $label): ?>
                                         <option value="<?php echo esc_attr($key) ?>"><?php echo $label ?></option>
                                     <?php endforeach; ?>
                                 </select>
                             </div>
                         </div>
                            <?php for ($po = 1; $po <=8 ; $po++):
                                $field= jm_get_job_alert_setting('job_alert'.$po.'',5);?>
                                <div class="col-sm-6">
                              <?php jm_job_advanced_search_field($field,'','job_alert');  ?>
                                  </div>
                          <?php  endfor; ?>


                    </div>

                    <input type="hidden" name="action" value="noo_job_alert_popup">
                    <input type="hidden" name="security" value="<?php echo wp_create_nonce('noo-job-alert-form') ?>"/>

                    <div class="form-group">
                        <p class="help-block noo-job-alert-notice"></p>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="submit"
                        class="btn btn-primary noo-btn-save-job-alert"><?php echo esc_html__('Save', 'noo'); ?></button>
            </div>

        </div>
    </div>
</div>
