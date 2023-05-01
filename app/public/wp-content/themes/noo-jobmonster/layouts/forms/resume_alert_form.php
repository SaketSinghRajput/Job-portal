<?php
$resume_alert_id = isset($_GET['resume_alert_id']) ? absint($_GET['resume_alert_id']) : 0;
$resume_alert = $resume_alert_id ? get_post($resume_alert_id) : '';
?>
    <div class="resume_alert-form">
        <div class="resume_alert-form row">
            <div class="form-group required-field">
                <label for="title" class="col-sm-3 control-label"><?php _e('Alert Name', 'noo') ?></label>
                <div class="col-sm-9">
                    <input type="text" value="<?php echo esc_attr($resume_alert ? $resume_alert->post_title : '') ?>"
                           class="form-control jform-validate" id="title" name="title" autofocus required
                           placeholder="<?php _e('Your alert name', 'noo'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="keywords" class="col-sm-3 control-label"><?php _e('Keywords', 'noo') ?></label>
                <div class="col-sm-9">
                    <input type="text"
                           value="<?php echo esc_attr($resume_alert ? noo_get_post_meta($resume_alert_id, '_keyword') : '') ?>"
                           class="form-control" id="keywords" name="keywords"
                           placeholder="<?php _e('Enter keywords to match jobs', 'noo'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="frequency" class="col-sm-3 control-label"><?php _e('Email Frequency', 'noo') ?></label>
                <div class="col-sm-9">
                    <?php
                    $frequency = $resume_alert ? noo_get_post_meta($resume_alert_id, '_frequency', 'weekly') : 'weekly';
                    $frequency_arr = Noo_Job_Alert::get_frequency();
                    ?>
                    <select class="form-control" name="frequency" id="frequency">
                        <?php foreach ($frequency_arr as $key => $label): ?>
                            <option value="<?php echo esc_attr($key) ?>" <?php selected($frequency, $key); ?>><?php echo $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php for ($po = 1; $po <= 8; $po++):
                $field = jm_get_resume_alert_setting('resume_alert' . $po . '', 5); ?>
                <?php if ($field == '_job_location'): ?>
                <?php
                $locations = get_terms('job_location', array('hide_empty' => 0));
                if ($locations) {
                    $value = $resume_alert ? noo_get_post_meta($resume_alert_id, '_job_location') : '';
                    $value = noo_json_decode($value);
                }

                $placeholder = sprintf(__("All Job Locations", 'noo'));
                $job_locations_args = array(
                    'hide_empty' => 0,
                    'echo' => 1,
                    'selected' => $value,
                    'hierarchical' => 1,
                    'name' => 'job_location[]',
                    'id' => 'noo-field-job_category',
                    'class' => 'form-control noo-select form-control-chosen',
                    'depth' => 0,
                    'taxonomy' => 'job_location',
                    'value_field' => 'term_id',
                    'required' => true,
                    'orderby' => 'name',
                    'multiple' => true,
                    'walker' => new Noo_Walker_TaxonomyDropdown(),
                ); ?>
                <div class="form-group"
                     data-placeholder="<?php echo $placeholder; ?>">
                    <label for="job_location" class="col-sm-3 control-label"><?php _e('Job Location', 'noo') ?></label>
                    <div class="col-sm-9  <?php if (is_rtl()) echo ' chosen-rtl'; ?>">
                        <?php wp_dropdown_categories($job_locations_args); ?>
                    </div>
                </div>
            <?php elseif ($field == '_job_category'): ?>
                <?php
                $categories = get_terms('job_category', array('hide_empty' => 0));
                if ($categories) {
                    $value = $resume_alert ? noo_get_post_meta($resume_alert_id, '_job_category', '') : '';
                    $value = noo_json_decode($value);
                }
                $placeholder = sprintf(__("All Job Categories", 'noo'));
                $job_category_args = array(
                    'hide_empty' => 0,
                    'echo' => 1,
                    'selected' => $value,
                    'hierarchical' => 1,
                    'name' => 'job_category[]',
                    'id' => 'noo-field-job_category',
                    'class' => 'form-control noo-select form-control-chosen',
                    'depth' => 0,
                    'taxonomy' => 'job_category',
                    'value_field' => 'term_id',
                    'required' => true,
                    'orderby' => 'name',
                    'multiple' => true,
                    'walker' => new Noo_Walker_TaxonomyDropdown(),
                ); ?>
                <div class="form-group"
                     data-placeholder="<?php echo $placeholder; ?>">
                    <label for="job_category" class="col-sm-3 control-label"><?php _e('Job Category', 'noo') ?></label>
                    <div class="col-sm-9  <?php if (is_rtl()) echo ' chosen-rtl'; ?>">
                        <?php wp_dropdown_categories($job_category_args); ?>
                    </div>
                </div>
            <?php else:
                jm_resume_alert_advanced_field($field, '', $resume_alert_id);
                ?>
            <?php endif; ?>
            <?php endfor; ?>


        </div>
    </div>