<?php

if (!function_exists('jm_get_job_custom_fields')) :
    function jm_get_job_custom_fields($include_disabled_fields = false, $suppress_filters = false)
    {
        $custom_fields = noo_get_custom_fields('noo_job_custom_field', 'noo_job_field_');

        $default_fields = jm_get_job_default_fields();

        $custom_fields = noo_merge_custom_fields($default_fields, $custom_fields, $include_disabled_fields);

        return $suppress_filters ? $custom_fields : apply_filters('jm_job_custom_fields', $custom_fields);
    }
endif;

if (!function_exists('jm_get_job_search_custom_fields')) :
    function jm_get_job_search_custom_fields()
    {
        $custom_fields = jm_get_job_custom_fields();
        $date_field = array(
            'name' => 'date',
            'type' => 'datepicker',
            'label' => __('Publishing Date', 'noo'),
            'is_default' => true,
        );
        $unit_type = jm_get_location_setting('unit_type','km');
        $filter_distance = array(
            'name' => 'filter_distance',
            'type' => 'select_distance',
            'label' => __('By Distance','noo'),
            'value' => array(
                '1'   => 'Radius 1'.$unit_type,
                '5'   => 'Radius 5'.$unit_type,
                '10'  => 'Radius 10'.$unit_type,
                '50'  => 'Radius 50'.$unit_type,
                '100' => 'Radius 100'.$unit_type,
            ),

        );
        $custom_fields[] = $date_field;
        $custom_fields[] = $filter_distance;
        $not_searchable = noo_not_searchable_custom_fields_type();
        foreach ($custom_fields as $key => $field) {
            if (in_array($field['type'], $not_searchable)) {
                unset($custom_fields[$key]);
            }
        }
        return apply_filters('jm_job_search_custom_fields', $custom_fields);
    }
endif;

if (!function_exists('jm_get_job_filter_custom_fields')):
    function jm_get_job_filter_custom_fields(){
        $custom_fields = jm_get_job_custom_fields();
        $filterable = jm_filterable_custom_fields();
        foreach ($custom_fields as $key=> $field){
            if(!in_array($field['type'],$filterable)){
                unset($custom_fields[$key]);
            }
        }
        $posted_date = array(
            'name'  => 'posted_date_filter',
            'type'  => 'radio',
            'label' => esc_html__('Date Posted','noo'),
            'value' => array(
                'hourly'    => esc_html__('Last Hour','noo'),
                'daily'     => esc_html__('Daily','noo'),
                'weekly'    => esc_html__('Weekly','noo'),
                'fortnight' => esc_html__('Fortnight','noo'),
                'monthly'   => esc_html__('Monthly','noo'),
            ),
        );
        $date_field = array(
            'name' => 'date',
            'type' => 'datepicker',
            'label' => esc_html__('Publishing Date', 'noo'),
            'is_default' => true,
        );
        $unit_type = jm_get_location_setting('unit_type','km');
        $filter_distance = array(
            'name' => 'filter_distance',
            'type' => 'select_distance',
            'label' => esc_html__('By Distance', 'noo'),
            'value' => array(
                '1'   => esc_html__('Radius 1', 'noo') .$unit_type,
                '5'   => esc_html__('Radius 5', 'noo') .$unit_type,
                '10'  => esc_html__('Radius 10', 'noo') .$unit_type,
                '20'  => esc_html__('Radius 20', 'noo') .$unit_type,
                '30'  => esc_html__('Radius 30', 'noo') .$unit_type,
                '50'  => esc_html__('Radius 50', 'noo') .$unit_type,
                '100' => esc_html__('Radius 100', 'noo') .$unit_type,
            ),

        );
        $custom_fields[] = $posted_date;
        $custom_fields[] = $date_field;
        $custom_fields[] = $filter_distance;

        return apply_filters('jm_job_filter_custom_fields', $custom_fields);
    }
endif;

if (!function_exists('jm_job_custom_fields_prefix')) :
    function jm_job_custom_fields_prefix()
    {
        return apply_filters('jm_job_custom_fields_prefix', '_noo_job_field_');
    }
endif;

if (!function_exists('jm_job_custom_fields_name')) :
    function jm_job_custom_fields_name($field_name = '', $field = array())
    {
        if (empty($field_name)) {
            return '';
        }

        $cf_name = jm_job_custom_fields_prefix() . sanitize_title($field_name);

        if (!empty($field) && isset($field['is_default'])) {
            $cf_name = $field['name'];
        }

        return apply_filters('jm_job_custom_fields_name', $cf_name, $field_name, $field);
    }
endif;

if (!function_exists('jm_get_job_field')) :
    function jm_get_job_field($field_name = '')
    {

        $custom_fields = jm_get_job_custom_fields(false, true);
        if (isset($custom_fields[$field_name])) {
            return $custom_fields[$field_name];
        }

        foreach ($custom_fields as $field) {
            if ($field_name == $field['name']) {
                return $field;
            }
        }

        return array();
    }
endif;

if (!function_exists('jm_get_job_custom_fields_option')) :
    function jm_get_job_custom_fields_option($key = '', $default = null)
    {
        $custom_fields = jm_get_setting('noo_job_custom_field', array());

        if (!$custom_fields || !is_array($custom_fields)) {
            return $default;
        }

        if (isset($custom_fields['__options__']) && isset($custom_fields['__options__'][$key])) {

            return $custom_fields['__options__'][$key];
        }

        return $default;
    }
endif;

if (!function_exists('jm_job_cf_settings_tabs')) :
    function jm_job_cf_settings_tabs($tabs = array())
    {
        $temp1 = array_slice($tabs, 0, 1);
        $temp2 = array_slice($tabs, 1);

        $job_cf_tab = array('job' => __('Job', 'noo'));

        return array_merge($temp1, $job_cf_tab, $temp2);
    }

    // Add to Custom Field (cf) tab.
    add_filter('jm_cf_settings_tabs_array', 'jm_job_cf_settings_tabs', 5);
endif;


if (!function_exists('jm_job_custom_fields_setting')) :
    function jm_job_custom_fields_setting()
    {
        wp_enqueue_style('noo-custom-fields');
        wp_enqueue_script('noo-custom-fields');

        noo_custom_fields_setting(
            'noo_job_custom_field',
            'noo_job_field_',
            jm_get_job_custom_fields(true)
        );

        $field_display = jm_get_job_custom_fields_option('display_position', 'after');
        ?>
        <table class="form-table" cellspacing="0">
            <tbody>
            <tr>
                <th>
                    <?php _e('Show Custom Fields:', 'noo') ?>
                </th>
                <td>
                    <select class="regular-text" name="noo_job_custom_field[__options__][display_position]">
                        <option <?php selected($field_display, 'before') ?>
                                value="before"><?php _e('Before Description', 'noo') ?></option>
                        <option <?php selected($field_display, 'after') ?>
                                value="after"><?php _e('After Description', 'noo') ?></option>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <?php do_action('jm_job_custom_fields_setting_options');
    }

    add_action('jm_cf_setting_job', 'jm_job_custom_fields_setting');
endif;

if (!function_exists('jm_job_render_form_field')) :
    function jm_job_render_form_field($field = array(), $job_id = 0)
    {
        $field_id = jm_job_custom_fields_name($field['name'], $field);

        $value = !empty($job_id) ? noo_get_post_meta($job_id, $field_id, '') : '';
        $value = isset($_REQUEST[$field_id]) ? $_REQUEST[$field_id] : $value;
        $value = !is_array($value) ? trim($value) : $value;

        $params = apply_filters('jm_job_render_form_field_params', compact('field', 'field_id', 'value'), $job_id);
        extract($params);
        $object = array('ID' => $job_id, 'type' => 'post');
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;

        ?>
        <div class="form-group row col-md-12 <?php noo_custom_field_class($field, $object); ?>" <?php echo ($field['name'] == 'job_location' ? 'data-placeholder="' . sprintf(esc_html__('Select %s', 'noo'), $label) . '"' : ''); ?>>
            <label for="<?php echo esc_attr($field_id) ?>" class="col-sm-3 control-label"><?php echo esc_html($label) ?></label>
            <div class="col-sm-9">
                <?php noo_render_field($field, $field_id, $value, '', $object); ?>
            </div>
        </div>
        <?php
    }
endif;

if (!function_exists('jm_job_render_search_field')) :
    function jm_job_render_search_field($field = array(), $disable_multiple_select = false,$object = '')
    {
        $field_id = jm_job_custom_fields_name($field['name'], $field);

        $field['required'] = ''; // no need for required fields in search form

        if ($disable_multiple_select) {
            $field['disable_multiple'] = true;
        }

        $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
        $value = !is_array($value) ? trim($value) : $value;
        $params = apply_filters('jm_job_render_search_field_params', compact('field', 'field_id', 'value'));
        extract($params);
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;
        ?>
        <div class="form-group" data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo 'search-' . esc_attr($field_id) ?>" class="control-label">
                <?php echo esc_html($label); ?>
            </label>
            <div class="advance-search-form-control">
                <?php
                $object = ($object == 'job_alert' ) ? 'job_alert' : 'search';
                noo_render_field($field, $field_id, $value, $object); ?>
            </div>
        </div>
        <?php
    }
endif;
if(!function_exists('jm_job_edit_render_field')):
    function jm_job_edit_render_field($field = array(), $disable_multiple_select = false,$job_alert_id=''){
        $field_id = jm_job_custom_fields_name($field['name'], $field);


        $field['required'] = ''; // no need for required fields in search form

        if ($disable_multiple_select) {
            $field['disable_multiple'] = true;
        }
        $value = noo_get_post_meta($job_alert_id,$field_id, '');
        $value = !is_array($value) ? trim($value) : $value;

        $params = apply_filters('jm_job_render_search_field_params', compact('field', 'field_id', 'value'));
        extract($params);

        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
        ?>
        <div class="form-group" data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo  esc_attr($field_id) ?>" class="control-label col-sm-3">
                <?php echo esc_html($label); ?>
            </label>
            <div class="col-sm-9">
                <div class="advance-search-form-control">
                    <?php noo_render_field($field, $field_id, $value, 'search'); ?>
                </div>
            </div>

        </div>
        <?php
    }
endif;

if (!function_exists('jm_job_advanced_search_field')) :
    function jm_job_advanced_search_field($field_val = '', $disable_multiple_select = false,$object = '')
    {
        if (empty($field_val) || $field_val == 'no') {
            return '';
        }
        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_job_search_custom_fields();
        $field_prefix = jm_job_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);

        foreach ($fields as $field) {
            $muliple_select = strpos($field['type'], 'multi') !== false;
            if (sanitize_title($field['name']) == str_replace($field_prefix, '', $field_id)) {
                $tax_fields = jm_get_job_taxonomies();
                if (in_array($field['name'], $tax_fields)) {
                    if ($muliple_select) {
                        $field['type'] = 'job_tax_multiple_select';
                    } else {
                        $field['type'] = 'job_tax_select';
                    }

                }
                jm_job_render_search_field($field, $disable_multiple_select,$object);
                break;
            }
        }

        return '';
    }
endif;
if (!function_exists('jm_job_render_filter_field')) :
    function jm_job_render_filter_field($field = array(),$list_post_query=array())
    {
        wp_enqueue_script('vendor-quicksearch');
        $field_id = jm_job_custom_fields_name($field['name'], $field);

        $field['required'] = ''; // no need for required fields in search form


        $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
        $value = !is_array($value) ? trim($value) : $value;
        $params = apply_filters('jm_job_render_search_field_params', compact('field', 'field_id', 'value'));
        extract($params);
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;
        ?>
        <div class="form-group" data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo 'search-' . esc_attr($field_id) ?>" class="control-label show-expand">
                <?php echo esc_html($label); ?>
                <i class="fa fa-angle-up expand-field"></i>
            </label>
            <div class="advance-filter-form-control <?php echo $field['type']; ?>">
                <?php
                noo_render_field($field, $field_id, $value, 'search',array(),'',$list_post_query); ?>
            </div>
        </div>
        <?php
    }
endif;
if (!function_exists('jm_job_filter_field')):
    function jm_job_filter_field($field_val='',$list_query_post=array()){

        if(empty($field_val) || $field_val == 'no'){
            return '' ;
        }
        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_job_filter_custom_fields();
        $field_prefix = jm_job_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);
        foreach ($fields as $field) {
            if (sanitize_title($field['name']) == str_replace($field_prefix, '', $field_id)) {
                if($field['type'] == 'radio' || $field['type'] == 'select' ){
                    $field['type'] = 'filter_radio';
                }elseif($field['type'] == 'price_range'){
                    $field['type'] = 'filter_slide';
                }elseif ($field['type'] == 'datepicker' || $field['type'] =='select_distance'){
                    jm_job_render_filter_field($field,$list_query_post);
                    continue;
                }elseif ($field['name'] =='job_category' || $field['name']=='job_location' || $field['name'] =='job_tag'){
                    $field['type'] = 'filter_checkbox';
                }else{
                    $field['type'] = 'filter_checkbox';
                }
                jm_job_render_filter_field($field,$list_query_post);
                break;
            }
        }

        return '';
    }
endif;
if(!function_exists('jm_job_alert_advanced_field')):
    function jm_job_alert_advanced_field($field_val='',$disable_multiple_select=false,$job_alert_id=''){
        if (empty($field_val) || $field_val == 'no') {
            return '';
        }
        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_job_search_custom_fields();

        $field_prefix = jm_job_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);

        foreach ($fields as $field) {
            $muliple_select = strpos($field['type'], 'multi') !== false;
            if (sanitize_title($field['name']) == str_replace($field_prefix, '', $field_id)) {

                $tax_fields = jm_get_job_taxonomies();
                if (in_array($field['name'], $tax_fields)) {
                    if ($muliple_select) {
                        $field['type'] = 'job_tax_multiple_select';
                    } else {
                        $field['type'] = 'job_tax_select';
                    }

                }
                jm_job_edit_render_field($field, $disable_multiple_select,$job_alert_id);
                break;
            }
        }

        return '';
    }
endif;


if (!function_exists('jm_job_save_custom_fields')) :
    function jm_job_save_custom_fields($post_id = 0, $args = array())
    {
        if (empty($post_id)) {
            return;
        }

        // Update custom fields
        $fields = jm_get_job_custom_fields();
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (isset($field['is_tax']) && $field['is_tax']) {
                    continue;
                }
                $id = jm_job_custom_fields_name($field['name'], $field);
                if (isset($field['type']) && $field['type'] == 'location_picker') {
                    update_post_meta($post_id, $id . '_lat', $args[$id . '_lat']);
                    update_post_meta($post_id, $id . '_lon', $args[$id . '_lon']);
                }
                if (isset($args[$id])) {
                    noo_save_field($post_id, $id, $args[$id], $field);
                }else{
                    delete_post_meta($post_id,$id);
                }
            }
        }
    }
endif;

if (!function_exists('jm_job_display_custom_fields')) :
    function jm_job_display_custom_fields()
    {
        $fields = jm_get_job_custom_fields();
        // Check for style 4, style 3
        $job_overview = get_theme_mod('noo_jobs_list_fields');
        $job_overview = !is_array($job_overview) ? explode(',', $job_overview) : $job_overview;
        
        $layout = noo_get_option('noo_job_detail_layout', 'style-1');
        $layout = isset($_GET['layout']) ? sanitize_text_field($_GET['layout']) : $layout;

        $current_user_role = Noo_Member::get_user_role(get_current_user_id());
        if (!empty($fields)) {
            $html = array();
            $field_skipped = 0;
            $user_per = noo_get_user_permission();
            foreach ($fields as $field) {
                // if( isset( $field['is_tax'] ) )
                // 	continue;
                if ($field['name'] == '_closing') // reserve the _closing field
                {
                    continue;
                }
                if ($field['name'] == '_cover_image') // reserve the _closing field
                {
                    continue;
                }
                if ($field['name'] == '_postalcode') // reserve the _postalcode field
                {
                    continue;
                }
                if(in_array($field['name'], $job_overview) && ( 'style-4' == $layout || 'style-3' == $layout)){
                    continue;
                }

                $id = jm_job_custom_fields_name($field['name'], $field);
                if (isset($field['is_tax'])) {
                    $value = jm_job_get_tax_value();
                    $value = implode(',', $value);
                } else {
                    $value = noo_get_post_meta(get_the_ID(), $id, '');
                }

                $icon = isset($field['icon']) ? $field['icon'] : '';
                $icon_class = str_replace("|", " ", $icon);
                $current_user_id = get_current_user_id();
                /* $current_user_permission = 'candidate';*/
                $permission = isset($field['permission']) ? $field['permission'] : '';

                $is_can_view = false;


                if (empty($permission) or 'public' == $permission or $user_per == 'candidate_with_package' or $user_per=='true' or 'administrator' == $current_user_role ) {
                    $is_can_view = true;
                } elseif ($permission == $user_per) {
                    $is_can_view = true;
                }

                if ($is_can_view == false) {
                    $field_skipped++;
                    continue;
                }

                if ($field['name'] == '_full_address') {

                    $is_using_company_address = noo_get_post_meta(get_the_ID(), '_use_company_address', '');

                    if ($is_using_company_address) {
                        $company_id = jm_get_job_company(get_the_ID());
                        $value = noo_get_post_meta($company_id, '_full_address', '');
                    } else {
                        $value = noo_get_post_meta(get_the_ID(), '_full_address', '');
                    }
                    if ($value != '') {
                        $html[] = '<li class="job-cf">' . noo_display_field($field, $id, $value, array(
                                'label_tag' => 'strong',
                                'label_class' => '',
                                'value_tag' => 'span'
                            ), false) . '</li>';
                    }

                } else {

                    if ($value != '') {
                        $html[] = '<li class="job-cf">' . noo_display_field($field, $id, $value, array(
                                'label_tag' => 'strong',
                                'label_class' => '',
                                'value_tag' => 'span'
                            ), false) . '</li>';
                    }

                    if('_salary' == $field['name']){
                        $is_schema = noo_get_option('noo_job_schema',false);
                        if($is_schema){
                            $html[] = '<span class="hidden" itemprop="baseSalary" itemscope itemtype="http://schema.org/MonetaryAmount">';
                            if(function_exists('get_woocommerce_currency')){
                                $currency = get_woocommerce_currency();
                            }else{
                                $currency = apply_filters('noo_jobmonster_salary_currency','USD');

                            }
                            $html[] = '<span itemprop="currency">'.$currency.'</span>';
                            $html[] = '<span itemprop="value" itemscope itemtype="http://schema.org/QuantitativeValue">';
                            $html[] = '<span itemprop="value">'.$value.'</span>';
                            $html[] = '<span itemprop="unitText">'.esc_html__('Month','noo').'</span>';
                            $html[] = '</span></span>';
                        }
                    }
                }


            }

            if (!empty($html) && count($html) > 0) : ?>
                <div class="job-custom-fields">
                    <h3><?php echo esc_html__('More Information', 'noo') ?></h3>
                    <div class="video-gallery-fields">
                        <ul>
                            <?php echo implode("\n", $html); ?>
                        </ul>
                    </div>
                </div>
            <?php endif;
            if ($field_skipped >= 1):?>

                <?php 
                $package_page_id = Noo_Resume_Package::get_setting( 'resume_package_page_id' );
                $link = get_permalink($package_page_id);
                $link = '<a href="' . esc_url($link) . '" class="upgrade">' . __('Upgrade', 'noo') . '</a>';
                ?>
                <?php if ($current_user_role == 'candidate') { ?>
                     <div class="noo-message noo-message-error">
                        <?php echo sprintf(__('Please %s the package to view more fields.', 'noo'), $link) ?>
                    </div>
                <?php } else { ?>
                    <div class="noo-message noo-message-error">
                        <?php echo sprintf(__('Please login with Candidate account to view more fields.', 'noo'), $link) ?>
                    </div>    
                <?php } ?>

            <?php endif;
        }
    }

    $field_pos = jm_get_job_custom_fields_option('display_position', 'after');
    add_action('jm_job_detail_content_' . $field_pos, 'jm_job_display_custom_fields', 5);

endif;
if (!function_exists('jm_job_advanced_search_by_distance')):
    function jm_job_advanced_search_by_distance($field,$field_id,$value,$form_type,$object){
        $value = isset($_GET['filter_distance']) ? $_GET['filter_distance'] : '';
        $input_id           = $form_type == 'search' ? 'search-' . $field_id : $field_id;

        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];

        $field_value = $field['value'];
        $placeholder =  sprintf( __( "Select %s", 'noo' ), $label );
        $is_chosen = false;
        $is_chosen = apply_filters( 'noo_select_field_is_chosen', $is_chosen, $field, $field_id );

        $rtl_class    = is_rtl() && $is_chosen ? ' chosen-rtl' : '';
        $chosen_class = $is_chosen ? ' form-control-chosen ignore-valid' : '';
        $chosen_class .= isset( $field['required'] ) && $field['required'] ? ' jform-chosen-validate' : '';
        $attrs = isset( $field['required'] ) && $field['required'] ? ' class="form-control' . $rtl_class . $chosen_class . '" required aria-required="true"' : ' class="form-control ' . $rtl_class . $chosen_class . '"';
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
        ?>
            <?php $current_lat = isset($_GET['current_lat']) ? $_GET['current_lat'] : '' ;
                  $current_lon = isset($_GET['current_lon']) ? $_GET['current_lon'] : '' ;
                  $address = isset($_GET['address'])? $_GET['address'] : '';
                  $map_type = jm_get_location_setting('map_type','');
                  $unit_type = jm_get_location_setting('unit_type','km');
            ?>
        <?php if($map_type=='google'): ?>
            <?php wp_enqueue_script( 'location-picker' );
                  wp_enqueue_script('jquery-ui-slider');
            ?>
            <div class="noo-location-picker-field-wrap">
                <div class="map_type">
                    <input id="noo-mb-location-address-filter" name="address" class="noo-mb-location-address-filter form-control"  type="text" value="<?php echo esc_attr($address); ?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'noo') ?>"  />
                    <i class="fa fa-map-marker-alt geocode-location"></i>
                </div>
                <input type="hidden" name="current_unit" value="<?php echo esc_attr($unit_type); ?>">
                <input type="hidden" class="noo-mb-lat-filter" id="noo-mb-lat-filter" name="current_lat" value="<?php echo esc_attr($current_lat); ?>">
                <input type="hidden" class="noo-mb-lon-filter" id="noo-mb-lon-filter" name="current_lon" value="<?php echo esc_attr($current_lon); ?>">
                <div class="noo-mb-job-location-filter" id="<?php echo esc_attr( $input_id ); ?>">
                </div>
            </div>
        <?php elseif ($map_type=='bing'):
            wp_enqueue_script('bing-map');
            wp_enqueue_script('jquery-ui-slider');
            ?>
            <?php $uniqID = uniqid(); ?>
            <div class="noo-location-picker-field-wrap ">
                <div class="map_type">
                    <input id="noo-mb-location-address" name="address" class="noo-mb-location-address form-control" type="text" value="<?php echo esc_attr($address); ?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'noo') ?>" />
                    <i class="fa fa-map-marker-alt geocode-location"></i>
                </div>
                <input type="hidden" name="current_unit" value="<?php echo esc_attr($unit_type); ?>">
                <input type="hidden" class="noo-mb-lat" name="current_lat" value="<?php echo esc_attr($current_lat); ?>" id="noo-mb-lat">
                <input type="hidden" class="noo-mb-lon" name="current_lon" value="<?php echo esc_attr($current_lon); ?>" id="noo-mb-lon">
                <div class="noo-mb-job" data-id='_full_address<?php echo  esc_attr( $uniqID  ) ?>'>
                    <div id='_full_address<?php echo  esc_attr( $uniqID  ) ?>'></div>
                </div>
            </div>
            <br/>
        <?php endif; ?>
        <div class="form-group proximity_slider_wrapper">
            <input type="hidden" name="filter_distance" id="filter_distance" value="<?php echo esc_attr($value);?>">
            <div class="search-distance-label"><input type="text" class="text-distance" readonly></div>
            <div class="proximity_slider" style="display: none" data-value="<?php echo !empty($value) ? $value : apply_filters('noo_distance_slider_default_value', 30) ;  ?>" data-distance="<?php echo esc_attr($unit_type); ?>"></div>
        </div>
        <script type="text/javascript">
           jQuery("#noo-mb-location-address").on('change',function () {
               var $form = jQuery(this).parents('.form-group');
               $form.find('option:selected').removeAttr('selected');

               $form.find('select.form-control').multiselect('refresh');
               return false;
           });
           jQuery(window).on('keydown',function(event){
               if(event.keyCode === 13) {
                   event.preventDefault();
                   return false;
               }
           });
        </script>
        <?php
    }
    add_action('noo_render_field_select_distance', 'jm_job_advanced_search_by_distance', 10, 5);
endif;
if (!function_exists('jm_job_advanced_search_job_tax_field')) :
    function jm_job_advanced_search_job_tax_field($field, $field_id, $value, $form_type, $object)
    {
        $custom_multiple_select = strpos($field['type'], 'multi') !== false;

        $allow_multiple_select = isset($field['disable_multiple']) ? !$field['disable_multiple'] : $custom_multiple_select;

        $label = isset( $field[ 'label_translated' ] ) ? $field[ 'label_translated' ] : ( isset($field[ 'plural' ]) && !empty($field[ 'plural' ]) ? $field[ 'plural' ] : $field[ 'label' ]);
        $tax = $field['name'];

        $field_name = $allow_multiple_select ? $field_id . '[]' : $field_id;
        $get_tax = get_queried_object();
        $selected = isset($_GET[$field_id]) ? $_GET[$field_id] : '';

        if($get_tax){
            if(isset($get_tax->taxonomy) && isset($field['name']) && $field['name'] ==  $get_tax->taxonomy){
                $selected = $get_tax->slug;
            }
        }
        $hide_empty_tax = ($object == 'job_alert') ? 0 : jm_get_job_setting( 'hide_empty_tax','');

        $field_args = array(
            'hide_empty' => (int)$hide_empty_tax,
            'echo' => 1,
            'selected' => $selected,
            'hierarchical' => 1,
            'name' => $field_name,
            'id' => 'noo-field-' . $tax,
            'class' => 'form-control noo-select form-control-chosen',
            'depth' => 0,
            'taxonomy' => $tax,
            'value_field' => 'slug',
            'orderby' => 'name',
            'multiple' => $allow_multiple_select,
            'walker' => new Noo_Walker_TaxonomyDropdown(),
        );
        if (!$allow_multiple_select) {
            $field_args['show_option_none'] = $label;
            $field_args['option_none_value'] = '';
        }

        wp_dropdown_categories($field_args);
    }

    add_action('noo_render_field_job_tax_select', 'jm_job_advanced_search_job_tax_field', 10, 5);
    add_action('noo_render_field_job_tax_multiple_select', 'jm_job_advanced_search_job_tax_field', 10, 5);
endif;

add_filter( 'wp_dropdown_cats', 'noo_dropdown_cats_multiple', 10, 2 );

function noo_dropdown_cats_multiple( $output, $r ) {

    if( isset( $r['multiple'] ) && $r['multiple'] ) {

        $output = preg_replace( '/^<select/i', '<select multiple', $output );

//        $output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
        $selected=(is_array($r['selected']))? $r['selected']: explode(",",$r['selected']);
        $new_array=array_map('trim',$selected);
        if(is_array($new_array)){
            foreach ($new_array as $value){
                $output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
            }
        }

    }

    return $output;
}