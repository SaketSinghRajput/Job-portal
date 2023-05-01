<?php
$company_id = jm_get_employer_company();
$company = (!empty($company_id) ? get_post($company_id) : '');
$user_id = get_current_user_id();
$display_name = get_the_author_meta('display_name',$user_id);
if(empty($display_name)){
    $display_name = get_the_author_meta('user_login',$user_id);
}
$company_name = (!empty($company_id) ? $company->post_title : '');
$company_name = (!empty($company_name)) ? $company_name : $display_name;
$content = !empty($company_id) ? $company->post_content : '';
?>
<div class="company-profile-form">
    <div class="form-group row required-field">
        <label for="company_name" class="col-sm-3 control-label"><?php _e('Company Name', 'noo') ?></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" autofocus id="company_name" required value="<?php echo $company_name; ?>" name="company_name" placeholder="<?php echo esc_attr__('Enter your company name', 'noo') ?>">
        </div>
    </div>
    <?php if(apply_filters('noo_company_form_description_field', true)):?>
    <div class="form-group row">
        <label for="company_desc" class="col-sm-3 control-label"><?php _e('Company Description', 'noo') ?></label>
        <div class="col-sm-9">
            <?php
            noo_wp_editor($content,'company_form_description_field','company_desc', true);
            ?>
        </div>
    </div>
    <?php endif;?>
    <?php
    $fields = jm_get_company_custom_fields();
    if (!empty($fields)) {
        foreach ($fields as $field) :

            $allow_multiple_select = strpos($field['type'], 'multi') !== false;

            if ($field['name'] == '_job_category') {
                $label=(!empty($field['plural'])) ? $field['plural'] : $field['label'];
                $name = '_job_category';
                if ($allow_multiple_select) {
                    $name = '_job_category[]';
                }

                $selected = array();
                if ($company_id) {
                    $selected = get_post_meta($company_id, '_job_category', true);
                }

                $required = $field['required'] ? 'required-field' : '';

                $job_category_args = array(
                    'hide_empty' => 0,
                    'echo' => 1,
                    'selected' => $selected,
                    'hierarchical' => 1,
                    'name' => $name,
                    'id' => 'noo-field-job_category',
                    'class' => 'form-control noo-select form-control-chosen',
                    'depth' => 0,
                    'taxonomy' => 'job_category',
                    'value_field' => 'term_id',
                    'orderby' => 'name',
                    'multiple' => $allow_multiple_select,
                    'walker' => new Noo_Walker_TaxonomyDropdown(),

                ); ?>

                <div class="form-group row <?php noo_custom_field_class($field); ?>" data-placeholder="<?php echo esc_html__((sprintf( __( 'Select %s', 'noo'),$field['label']) )); ?>">
                    <label for="<?php echo esc_attr($field['label']) ?>" class="col-sm-3 control-label"><?php echo(isset($field['label_translated']) ? $field['label_translated'] : $label) ?></label>
                    <div class="col-sm-9">
                        <?php wp_dropdown_categories($job_category_args); ?>
                    </div>
                </div>

                <?php
            } else {
                jm_company_render_form_field($field, $company_id);
            }
        endforeach;

    }
   
    $socials = jm_get_company_socials();
    if (!empty($socials)) {
        foreach ($socials as $social) {
            jm_company_render_social_field($social, $company_id);
        }
    }
    ?>
</div>