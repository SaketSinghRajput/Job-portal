<?php
if( !function_exists( 'jm_job_alert_admin_init' ) ) :
    function jm_job_alert_admin_init(){
        register_setting('jm_job_alert_custom_field', 'jm_job_alert_custom_field');
    }

    add_filter('admin_init', 'jm_job_alert_admin_init' );
endif;
if (!function_exists('jm_get_job_alert_custom_fields')) :
    function jm_get_job_alert_custom_fields($include_disabled_fields = false, $suppress_filters = false)
    {
        $custom_fields = noo_get_custom_fields('jm_job_alert_custom_field', 'jm_job_alert_field_');

        $default_fields = jm_get_job_alert_default_fields();

        $custom_fields = noo_merge_custom_fields($default_fields, $custom_fields, $include_disabled_fields);

        return $suppress_filters ? $custom_fields : apply_filters('jm_job_alert_custom_fields', $custom_fields);
    }
endif;
if (!function_exists('jm_job_alert_custom_fields_prefix')) :
    function jm_job_alert_custom_fields_prefix()
    {
        return apply_filters('jm_job_alert_custom_fields_prefix', '_noo_job_alert_field_');
    }
endif;

if (!function_exists('jm_job_alert_custom_fields_name')) :
    function jm_job_alert_custom_fields_name($field_name = '', $field = array())
    {
        if (empty($field_name)) {
            return '';
        }

        $cf_name = jm_job_alert_custom_fields_prefix() . sanitize_title($field_name);

        if (!empty($field) && isset($field['is_default'])) {
            $cf_name = $field['name'];
        }

        return apply_filters('jm_job_alert_custom_fields_name', $cf_name, $field_name, $field);
    }
endif;

if (!function_exists('jm_job_alert_custom_fields_setting')) :
    function jm_job_alert_custom_fields_setting()
    {
        wp_enqueue_style('noo-custom-fields');
        wp_enqueue_script('noo-custom-fields');
        wp_enqueue_style( 'vendor-chosen-css' );
        wp_enqueue_script( 'vendor-chosen-js' );

        if(function_exists( 'wp_enqueue_media' )){
            wp_enqueue_media();
        }else{
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }
        noo_custom_fields_setting(
            'jm_job_alert_custom_field',
            'jm_job_alert_field_',
            jm_get_job_alert_custom_fields(true)
        );
    }
    add_action('jm_cf_setting_job_alert', 'jm_job_alert_custom_fields_setting');
endif;
if (!function_exists('jm_job_alert_save_custom_fields')) :
    function jm_job_alert_save_custom_fields($post_id = 0, $args = array())
    {
        if (empty($post_id)) {
            return;
        }

        // Update custom fields
        $fields = jm_get_job_alert_custom_fields();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (isset($field['is_tax']) && $field['is_tax']) {
                    continue;
                }
                $id = jm_job_alert_custom_fields_name($field['name'], $field);
                if (isset($field['type']) && $field['type'] == 'location_picker') {
                    update_post_meta($post_id, $id . '_lat', $args[$id . '_lat']);
                    update_post_meta($post_id, $id . '_lon', $args[$id . '_lon']);
                }
                if (isset($args[$id])) {
                    noo_save_field($post_id, $id, $args[$id], $field);
                }
            }
        }
    }
endif;
if(!function_exists(' jm_get_job_alert_default_fields')):
   function jm_get_job_alert_default_fields(){
       $default_fields = array(
           'job_category' => array(
               'name' => 'job_category',
               'label' => __('Job Category', 'noo'),
               'is_default' => true,
               'is_tax' => true,
               'type' => 'multiple_select',
               'allowed_type' => array(
                   'select'			=> __('Select', 'noo'),
                   'multiple_select'	=> __( 'Multiple Select', 'noo' ),
               ),
               'required' => true
           ),
           'job_type' => array(
               'name' => 'job_type',
               'label' => __('Job Type', 'noo'),
               'is_default' => true,
               'is_tax' => true,
               'type' => 'select',
               'allowed_type' => array(
                   'select'			=> __('Select', 'noo'),
                   'multiple_select'	=> __( 'Multiple Select', 'noo' ),
               ),
               'required' => true
           ),
           'job_location' => array(
               'name' => 'job_location',
               'label' => __('Job Location', 'noo'),
               'is_default' => true,
               'is_tax' => true,
               'type' => 'multi_location_input',
               'allowed_type' => array(
                   'multi_location'		=> __('Multiple Location', 'noo'),
                   'single_location'		=> __('Single Location', 'noo'),
               ),
               'required' => true
           ),
       );

       return apply_filters( 'jm_application_default_fields', $default_fields );
    }
endif;
if (!function_exists('jm_job_alert_render_form_field')) :
    function jm_job_alert_render_form_field($field = array(), $job_alert_id = 0)
    {
        $field_id = jm_job_alert_custom_fields_name($field['name'], $field);

        $value = !empty($job_alert_id) ? noo_get_post_meta($job_alert_id, $field_id, '') : '';
        $value = isset($_REQUEST[$field_id]) ? $_REQUEST[$field_id] : $value;
        $value = !is_array($value) ? trim($value) : $value;

        $params = apply_filters('jm_job_alert_render_form_field_params', compact('field', 'field_id', 'value'), $job_alert_id);
        extract($params);
        $object = array('ID' => $job_alert_id, 'type' => 'post');
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];

        ?>
        <div class="form-group <?php noo_custom_field_class($field, $object); ?>"
            <?php echo'data-placeholder="' . sprintf(esc_html__('Select %s', 'noo'), $label) . '"' ; ?>
        >
            <label for="<?php echo esc_attr($field_id) ?>"
                   class=" control-label"><?php echo($label) ?></label>
            <div class="">
                <?php noo_render_field($field, $field_id, $value, '', $object); ?>
            </div>
        </div>
        <?php
    }
endif;