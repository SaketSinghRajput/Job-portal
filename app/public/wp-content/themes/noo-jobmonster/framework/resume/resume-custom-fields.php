<?php

if (!function_exists('jm_get_resume_custom_fields')) :
    function jm_get_resume_custom_fields($include_disabled_fields = false, $suppress_filters = false)
    {
        $custom_fields = noo_get_custom_fields('noo_resume_custom_field', 'noo_resume_field_');

        $default_fields = jm_get_resume_default_fields();

        $custom_fields = noo_merge_custom_fields($default_fields, $custom_fields, $include_disabled_fields);

        return $suppress_filters ? $custom_fields : apply_filters('jm_resume_custom_fields', $custom_fields);
    }
endif;

if (!function_exists('jm_get_resume_search_custom_fields')) :
    function jm_get_resume_search_custom_fields()
    {
        $custom_fields = jm_get_resume_custom_fields();
        $candidate_field = array(
            'name' => 'candidate',
            'type' => 'text',
            'label' => __('Candidate', 'noo'),
            'value' => __('Name or Email', 'noo'),
            'is_default' => true,
        );
        $unit_type = jm_get_location_setting('unit_type','kilometer');
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
        $custom_fields = array_merge(array('candidate' => $candidate_field), $custom_fields);
        $custom_fields[] = $filter_distance;
        $not_searchable = noo_not_searchable_custom_fields_type();
        foreach ($custom_fields as $key => $field) {
            if (!empty($field['type'])) {
                if (in_array($field['type'], $not_searchable)) {
                    unset($custom_fields[$key]);
                }
            }
        }

        return apply_filters('jm_resume_search_custom_fields', $custom_fields);
    }
endif;
if (!function_exists('jm_get_filter_resume_custom_fields')):
    function jm_get_filter_resume_custom_fields(){
        $custom_fields = jm_get_resume_custom_fields();
        $filterable = jm_filterable_custom_fields();
        foreach ($custom_fields as $key=> $field){
            if(!in_array($field['type'],$filterable)){
                unset($custom_fields[$key]);
            }
        }
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
        $custom_fields[] = $date_field;
        $custom_fields[] = $filter_distance;
        return apply_filters('get_filter_resume_custom_fields', $custom_fields);
    }
endif;
if (!function_exists('jm_resume_filter_field')):
    function jm_resume_filter_field($field_val='',$list_query_post=array()){

        if(empty($field_val) || $field_val == 'no'){
            return '' ;
        }
        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_resume_search_custom_fields();
        $field_prefix = jm_resume_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);

        foreach ($fields as $field) {
            if (sanitize_title($field['name']) == str_replace($field_prefix, '', $field_id)) {
                if($field['type'] == 'radio' || $field['type'] == 'select' || $field['type'] == 'single_location' ){
                    $field['type'] = 'filter_radio';
                }elseif ($field['type'] == 'datepicker' || $field['type'] =='select_distance'){
                    jm_resume_render_filter_field($field,$list_query_post);
                    continue;
                }else{
                    $field['type'] = 'filter_checkbox';
                }
                jm_resume_render_filter_field($field,$list_query_post);
                break;
            }
        }

        return '';
    }
endif;
if (!function_exists('jm_resume_render_filter_field')) :
    function jm_resume_render_filter_field($field = array(),$list_post_query=array())
    {
        wp_enqueue_script('vendor-quicksearch');
        $field_id = jm_resume_custom_fields_name($field['name'], $field);

        $field['required'] = ''; // no need for required fields in search form


        $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
        $value = !is_array($value) ? trim($value) : $value;
        $params = apply_filters('jm_resume_render_search_field_params', compact('field', 'field_id', 'value'));
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
                noo_render_field($field, $field_id, $value, 'filter_resume',array(),'',$list_post_query); ?>
            </div>
        </div>
        <?php
    }
endif;
if(!function_exists('jm_get_resume_filter_custom_fields')):
    function jm_get_resume_filter_custom_fields(){
        $custom_fields = jm_get_resume_custom_fields();
        $not_filter = noo_not_searchable_custom_fields_type();
        foreach ($custom_fields as $key=>$field){
            if(!empty($field['type'])){
                if(in_array($field['type'],$not_filter) ||($field['name'] == '_job_location')|| ($field['name'] == '_job_category')){
                    unset($custom_fields[$key]);
                }
            }
        }
        return apply_filters('jm_resume_filter_custom_fields',$custom_fields);
    }
endif;
if (!function_exists('jm_get_resume_custom_fields_option')) :
    function jm_get_resume_custom_fields_option($key = '', $default = null)
    {
        $custom_fields = jm_get_setting('noo_resume_custom_field', array());

        if (!$custom_fields || !is_array($custom_fields)) {
            return $default;
        }

        if (isset($custom_fields['__options__']) && isset($custom_fields['__options__'][$key])) {

            return $custom_fields['__options__'][$key];
        }

        return $default;
    }
endif;

if (!function_exists('jm_rcf_settings_tabs')) :
    function jm_rcf_settings_tabs($tabs = array())
    {
        $temp1 = array_slice($tabs, 0, 1);
        $temp2 = array_slice($tabs, 1);

        $resume_cf_tab = array('resume' => __('Resume', 'noo'));

        return array_merge($temp1, $resume_cf_tab, $temp2);
    }

    // add to page Custom field (cf) tab.
    add_filter('jm_cf_settings_tabs_array', 'jm_rcf_settings_tabs');
endif;

if (!function_exists('jm_get_resume_socials')) :
    function jm_get_resume_socials()
    {
        $socials = jm_get_resume_custom_fields_option('socials', 'website,facebook,twitter,linkedin,instagram,googleplus');
        $socials = !is_array($socials) ? explode(',', $socials) : $socials;

        return apply_filters('jm_get_resume_socials', $socials);
    }
endif;
if(!function_exists('jm_get_filter_resume')):
    function jm_get_filter_resume(){
     $resume_filter = jm_get_resume_custom_fields_option('filter_resume');
     $resume_filter = !is_array($resume_filter) ? explode(',',$resume_filter) : $resume_filter ;
     return apply_filters('jm_get_filter_resume',$resume_filter);
    }
endif;
if (!function_exists('jm_resume_custom_fields_setting')) :
    function jm_resume_custom_fields_setting()
    {
        wp_enqueue_style('noo-custom-fields');
        wp_enqueue_script('noo-custom-fields');

        noo_custom_fields_setting(
            'noo_resume_custom_field',
            'noo_resume_field_',
            jm_get_resume_custom_fields(true)
        );

        do_action('jm_resume_custom_fields_setting_options');

        wp_enqueue_style('noo-custom-fields');
        wp_enqueue_script('noo-custom-fields');

        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }
        wp_enqueue_style('vendor-chosen-css');
        wp_enqueue_script('vendor-chosen-js');

        $all_socials = noo_get_social_fields();
        $selected_arr = jm_get_resume_socials();
        $selected_fields_filter = jm_get_filter_resume();
        $fields_filter = jm_get_resume_filter_custom_fields();

        ?>
        <h3><?php echo __('Social Fields', 'noo') ?></h3>
        <table class="form-table" cellspacing="0">
            <tbody>
            <tr>
                <th>
                    <?php _e('Select Social Networks', 'noo') ?>
                </th>
                <td>
                    <?php if ($all_socials): ?>
                        <select class="social_list_field" name="noo_resume_custom_field[__options__][socials]"
                                multiple="multiple" style="width: 500px;max-width: 100%;">
                            <?php if ($selected_arr): ?>
                                <?php foreach ((array)$selected_arr as $index => $key): ?>
                                    <?php if (isset($all_socials[$key])) : ?>
                                        <option value="<?php echo esc_attr($key) ?>"
                                                selected><?php echo esc_html($all_socials[$key]['label']); ?></option>
                                        <?php unset($all_socials[$key]); ?>
                                    <?php else : unset($selected_arr[$index]); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php foreach ($all_socials as $key => $social): ?>
                                <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($social['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input name="noo_resume_custom_field[__options__][socials]" type="hidden"
                               value="<?php echo implode(',', $selected_arr); ?>"/>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                $("select.social_list_field").chosen({
                                    placeholder_text_multiple: "<?php echo __('Select social networks', 'noo'); ?>"
                                }).change(function (e, params) {
                                    var $this = $(this);
                                    var values = $(this).siblings('input').val();
                                    values = values !== "" ? values.split(',') : [];

                                    if (typeof params.deselected !== "undefined") {
                                        values = $.grep(values, function (value) {
                                            return value != params.deselected;
                                        });
                                    } else if (typeof params.selected !== "undefined") {
                                        values.push(params.selected);
                                    }

                                    $(this).siblings('input').val(values.join());
                                });
                            });
                        </script>
                        <style type="text/css">
                            .chosen-container input[type="text"] {
                                height: auto !important;
                            }
                        </style>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php _e('Select Fields To Filter Resume Management','noo') ?>
                </th>
                <td>
                        <select class="resume_filter_list_field" name="noo_resume_custom_field[__options__][filter_resume]"
                                multiple="multiple" style="width: 500px;max-width: 100%;">
                            <?php if ($selected_fields_filter): ?>
                                <?php foreach ((array)$selected_fields_filter as $index => $key): ?>
                                    <?php if (isset(  $fields_filter[$key])) : ?>
                                        <option value="<?php echo esc_attr($key) ?>"
                                                selected><?php echo esc_html( $fields_filter[$key]['label']); ?></option>
                                        <?php unset( $fields_filter[$key]); ?>
                                    <?php else : unset( $selected_fields_filter[$index]); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php foreach ($fields_filter as $key => $value): ?>
                                <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input name="noo_resume_custom_field[__options__][filter_resume]" type="hidden"
                               value="<?php echo implode(',',  $selected_fields_filter); ?>"/>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                $("select.resume_filter_list_field").chosen({
                                    placeholder_text_multiple: "<?php echo __('Select Fields To Filter Resume Management', 'noo'); ?>"
                                }).change(function (e, params) {
                                    var $this = $(this);
                                    var values = $(this).siblings('input').val();
                                    values = values !== "" ? values.split(',') : [];

                                    if (typeof params.deselected !== "undefined") {
                                        values = $.grep(values, function (value) {
                                            return value != params.deselected;
                                        });
                                    } else if (typeof params.selected !== "undefined") {
                                        values.push(params.selected);
                                    }

                                    $(this).siblings('input').val(values.join());
                                });
                            });
                        </script>
                        <style type="text/css">
                            .chosen-container input[type="text"] {
                                height: auto !important;
                            }
                        </style>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    add_action('jm_cf_setting_resume', 'jm_resume_custom_fields_setting');
endif;

if (!function_exists('jm_resume_render_form_field')) :
    function jm_resume_render_form_field($field = array(), $resume_id = 0)
    {
        $field_id = jm_resume_custom_fields_name($field['name'], $field);
        $value = !empty($resume_id) ? noo_get_post_meta($resume_id, $field_id, '') : '';
        $value = !is_array($value) ? trim($value) : $value;
        
        $params = apply_filters('jm_resume_render_form_field_params', compact('field', 'field_id', 'value'), $resume_id);
        extract($params);
        $object = array('ID' => $resume_id, 'type' => 'post');
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
        ?>
        <div class="form-group row <?php noo_custom_field_class($field, $object); ?>"
             data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo esc_attr($field_id) ?>"
                   class="col-sm-5 control-label"><?php echo esc_html($label); ?></label>
            <div class="col-sm-7">
                <?php noo_render_field($field, $field_id, $value, '', $object); ?>
            </div>
        </div>
        <?php
    }
endif;

if (!function_exists('jm_resume_render_search_field')) :
    function jm_resume_render_search_field($field = array(), $disable_multiple_select = false)
    {
        $field_id = jm_resume_custom_fields_name($field['name'], $field);
        $value = '';
        $params = apply_filters('jm_resume_render_search_field_params', compact('field', 'field_id', 'value'));
        extract($params);

        $field['required'] = ''; // no need for required fields in search form

        if ($disable_multiple_select) {
            $field['disable_multiple'] = true;
        }

        $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
        $value = !is_array($value) ? trim($value) : $value;
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;
        ?>
        <div class="form-group" data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo 'search-' . esc_attr($field_id) ?>"
                   class="control-label"><?php echo esc_html($label); ?></label>
            <div class="advance-search-form-control">
                <?php noo_render_field($field, $field_id, $value, 'search'); ?>
            </div>
        </div>
        <?php
    }
endif;

if (!function_exists('jm_resume_advanced_search_field')) :
    function jm_resume_advanced_search_field($field_val = '', $disable_multiple_select = false)
    {
        if (empty($field_val) || $field_val == 'no') {
            return '';
        }

        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';
        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_resume_search_custom_fields();


        $field_prefix = jm_resume_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);
        foreach ($fields as $field) {
                $muliple_select = strpos($field['type'], 'multi') !== false;
            if (sanitize_title($field['name']) == $field_id) {

                $tax_fields = array('_job_location', '_job_category');
                if (in_array($field['name'], $tax_fields)) {
                    if ($muliple_select) {
                        $field['type'] = 'resume_tax_multiple_select';
                    } else {
                        $field['type'] = 'resume_tax_select';
                    }
                }
                jm_resume_render_search_field($field, $disable_multiple_select);
                break;
            }
        }

        return '';
    }
endif;

if(!function_exists('jm_resume_alert_advanced_field')):
    function jm_resume_alert_advanced_field($field_val='',$disable_multiple_select=false,$resume_alert_id=''){
        if (empty($field_val) || $field_val == 'no') {
            return '';
        }
        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_resume_search_custom_fields();

        $field_prefix = jm_resume_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);

        foreach ($fields as $field) {
            $muliple_select = strpos($field['type'], 'multi') !== false;
            if (sanitize_title($field['name']) == str_replace($field_prefix, '', $field_id)) {

                jm_resume_edit_render_field($field, $disable_multiple_select,$resume_alert_id);
                break;
            }
        }

        return '';
    }
endif;

if(!function_exists('jm_resume_edit_render_field')):
    function jm_resume_edit_render_field($field = array(), $disable_multiple_select = false,$resume_alert_id=''){
        $field_id = jm_resume_custom_fields_name($field['name'], $field);


        $field['required'] = ''; // no need for required fields in search form

        if ($disable_multiple_select) {
            $field['disable_multiple'] = true;
        }
        $value = noo_get_post_meta($resume_alert_id,$field_id, '');
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


if (!function_exists('jm_resume_custom_fields_prefix')) :
    function jm_resume_custom_fields_prefix()
    {
        return apply_filters('jm_resume_custom_fields_prefix', '_noo_resume_field_');
    }
endif;

if (!function_exists('jm_resume_custom_fields_name')) :
    function jm_resume_custom_fields_name($field_name = '', $field = array())
    {
        if (empty($field_name)) {
            return '';
        }

        $cf_name = jm_resume_custom_fields_prefix() . sanitize_title($field_name);

        if (!empty($field) && isset($field['is_default'])) {
            $cf_name = $field['name'];
        }

        return apply_filters('jm_resume_custom_fields_name', $cf_name, $field_name, $field);
    }
endif;

if (!function_exists('jm_get_resume_field')) :
    function jm_get_resume_field($field_name = '')
    {
        $custom_fields = jm_get_resume_custom_fields();
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
if(!function_exists('jm_get_all_resume_field_for_job_package')):
    function jm_get_all_resume_field_for_job_package($field_name = ''){
        $custom_fields = jm_get_resume_custom_fields(false,true);
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
if (!function_exists('jm_get_resume_field_value')) :
    function jm_get_resume_field_value($resume_id, $field = array())
    {
        $field['type'] = isset($field['type']) ? $field['type'] : 'text';
        $id = jm_resume_custom_fields_name($field['name'], $field);
        $archive_link = get_post_type_archive_link('noo_resume');
        
        $value = $resume_id ? noo_get_post_meta($resume_id, $id, '') : '';
        if ($id == '_job_category') {
            if (!empty($value)) {
                $value = jm_resume_get_tax_value($resume_id, $id);
                $links = array();
                foreach ($value as $cat) {
                    if(!is_numeric($cat)){
                        continue;
                    }
                    $category_link = esc_url(add_query_arg(array('resume_category' => $cat), $archive_link));
                    $term = get_term_by('id', $cat, 'job_category');
                    $cat_name =  $term ? $term->name : '';
                    $links[] = '&nbsp;<a class="resume-category"  href="' . $category_link . '" >' . $cat_name . '</a>';
                }
                $value = join(',', $links);
            }
        } elseif ($id == '_job_location') {
            if (!empty($value)) {
                $value = jm_resume_get_tax_value($resume_id, $id);
                $links = array();
                foreach ($value as $loc) {
                    if(!is_numeric($loc)){
                        continue;
                    }
                    $location_link = esc_url(add_query_arg(array('_job_location' => $loc), $archive_link));
                    $term = get_term_by('id', $loc, 'job_location');
                    $loc_name = $term ? $term->name : '';
                    $links[] = '&nbsp;<a class="resume-category"  href="' . $location_link . '" >' . $loc_name . '</a>';
                }
                $value = join(',', $links);
            }
        } else {
            $value = !is_array($value) ? trim($value) : $value;
            // if( !empty( $value ) ) {
            // 	$value = noo_convert_custom_field_value( $field, $value );
            // 	if( is_array( $value ) ) {
            // 		$value = implode(', ', $value);
            // 	}
            // }
        }

        return $value;
    }
endif;


if (!function_exists('jm_resume_advanced_search_tax_field')) :
    function jm_resume_advanced_search_tax_field($field, $field_id, $value, $form_type, $object)
    {

        $custom_multiple_select = strpos($field['type'], 'multi') !== false;

        $allow_multiple_select = isset($field['disable_multiple']) ? !$field['disable_multiple'] : $custom_multiple_select;

        $label = $field['label'];
        $tax = str_replace('_job_', 'job_', $field['name']);

        $field_name = ! $allow_multiple_select ? $field_id : $field_id . '[]';
        $selected = isset($_GET[$field_id]) ? $_GET[$field_id] : '';

        if ($field['name'] == '_job_category' && empty($selected)) {
            $selected = isset($_GET['resume_category']) ? intval($_GET['resume_category']) : '';
        }

        $field_args = array(
            'hide_empty' => 0,
            'echo' => 1,
            'selected' => $selected,
            'hierarchical' => 1,
            'name' => $field_name,
            'id' => 'noo-field-' . $tax,
            'class' => 'form-control noo-select form-control-chosen',
            'depth' => 0,
            'taxonomy' => $tax,
            'value_field' => 'term_id',
            'orderby' => 'name',
            'multiple' => $allow_multiple_select,
            'walker' => new Noo_Walker_TaxonomyDropdown(),
        );
        if (!$allow_multiple_select) {
            $field_args['show_option_none'] =  $label;
            $field_args['option_none_value'] = '';
        }

        wp_dropdown_categories($field_args);
    }

    add_action('noo_render_field_resume_tax_select', 'jm_resume_advanced_search_tax_field', 10, 5);
    add_action('noo_render_field_resume_tax_multiple_select', 'jm_resume_advanced_search_tax_field', 10, 5);
endif;
if( !function_exists( 'jm_resume_render_social_field') ) :
    function jm_resume_render_social_field( $social = '', $resume_id = 0 ) {
        $all_socials = noo_get_social_fields();
        if( empty( $social ) || !isset( $all_socials[$social] ) ) return;

        $field = $all_socials[$social];
        $field['name'] = $social;
        $field['type'] = 'text';
        $field['value'] = $social == 'email' ? 'email@' . $_SERVER['HTTP_HOST'] : 'http://';
        $field_id = $field['name'];

        $value = !empty( $resume_id ) ? get_post_meta( $resume_id, $field_id, true ) : '';
        $value = !is_array($value) ? trim($value) : $value;

        $params = apply_filters( 'jm_resume_render_social_field_params', compact( 'field', 'field_id', 'value' ), $resume_id );
        extract($params);
        $object = array( 'ID' => $resume_id, 'type' => 'user' );

        $field_id = esc_attr($field_id);
        ?>
        <div class="form-group row <?php noo_custom_field_class( $field, $object ); ?>">
            <label for="<?php echo $field_id; ?>" class="col-sm-5 control-label"><?php echo esc_html( $field['label'] ); ?></label>
            <div class="col-sm-7">
                <?php noo_render_field( $field, $field_id, $value, '', $object ); ?>
            </div>
        </div>
        <?php
    }
endif;
