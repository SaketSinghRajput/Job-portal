<?php
$logo_company = Noo_Company::get_company_logo( $company_id, 'thumbnail-logo' );
$all_socials = noo_get_social_fields();
wp_enqueue_style('noo-rating');
wp_enqueue_script('noo-rating');

$slogan = noo_get_post_meta($company_id, '_slogan');
$noo_single_jobs_layout = noo_get_option('noo_single_jobs_layout', 'right_company');
$is_schema = noo_get_option( 'noo_job_schema', false );
$schema_company = $schema_url = $schema_name = $schema_logo = '';
if($is_schema){
    $schema_company = ' itemscope itemtype="http://schema.org/Organization"';
    $schema_url = ' itemprop="url"';
    $schema_name = ' itemprop="name"';
    $schema_logo = ' itemprop="logo"';
}
?>
    <div id="company-desc" class="company-desc" <?php echo ($schema_company);?>>

        <div class="company-header">
            <div class="company-featured"><a href="<?php echo get_permalink($company_id); ?>" <?php echo ($schema_url);?>><span <?php echo ($schema_logo);?>><?php echo $logo_company; ?></span></a></div>
            <div class="company-info-style2">
                <h3 class="company-title" <?php echo ($schema_name);?>>
                    <a href="<?php echo get_permalink($company_id); ?>">
                        <?php echo noo_get_the_company_name($company_id); ?>
                    </a>
                </h3>
                <?php if (!empty($slogan)) : ?>
                    <div class="slogan">
                        <?php echo ($slogan); ?>
                    </div>
                <?php endif; ?>
                <?php
                // Job's social info
                $socials = jm_get_company_socials();
                $html = array();
                if(is_array($socials)  && !empty($socials)){
                    foreach ($socials as $social) {
                    	if (!isset($all_socials[$social])) {
                    		continue;
                    	}
                        $data = $all_socials[$social];
                        $value = get_post_meta($company_id, "_{$social}", true);
                        if (!empty($value)) {
                            $url = $social == 'email_address' ? 'mailto:' . $value : esc_url($value);
                            if($data['icon'] == 'fa-link'){
                                $html[] = '<a title="' . sprintf($data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
                            }else{
                                $html[] = '<a title="' . sprintf($data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
                            }
                        }
                    }
                    if (!empty($html) && count($html) > 0) : ?>
                        <div class="job-social clearfix">
                            <?php echo implode("\n", $html); ?>
                        </div>
                    <?php endif; ?>
                <?php }?>
                <?php if (Noo_Company::review_is_enable()): ?>
                    <span class="total-review">
					    <?php noo_box_rating(noo_get_total_point_review($company_id), true) ?>
                        <span><?php echo '(' . noo_get_total_review($company_id) . ')' ?></span>
					</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="company-info">
            <?php

            $settings_fields    = get_theme_mod('noo_company_list_fields');

            $content_meta       = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;
            // Custom Fields
            $fields = jm_get_company_custom_fields();
            $html = array();
            foreach ($fields as $field) {

                if ($field['name'] == '_logo' || $field['name'] == '_cover_image' || $field['name'] == '_portfolio') {
                    continue;
                }
                
                $id = jm_company_custom_fields_name($field['name'], $field);
                $value = noo_get_post_meta($company_id, $id, '');
                if(($field['type']=='multi_tax_location') || ($field['type']=='multi_tax_location_input')){
                    $field['type'] = 'multi_company_location';
                }
                if ($field['name'] == '_job_category') {

                    $archive_link = get_post_type_archive_link( 'noo_company' );

                    $field['type'] = 'text';
                    $field['is_tax'] = false;
                    $meta = noo_get_post_meta($company_id, '_job_category',array());
                    if( ! empty($meta)){
                        $meta   = noo_json_decode($meta);
                        $links  = array();
                        foreach ( $meta as $cat_id) {
                            $term = get_term_by('id', $cat_id, 'job_category');
                            if (!empty($term)){
                                $cat_name = $term->name;
                                $cat_url = esc_url( add_query_arg( array( 'company_category' => $term->term_id ), $archive_link ) );
                                $links[] = '<a href="' . $cat_url . '">' . $cat_name . '</a>';
                            }
                        }
                        $value = join(", ", $links);
                    }

                }
                if (!empty($value) && in_array($field['name'], $content_meta)) {
                    $html[] = '<li>' . noo_display_field($field, $id, $value, array('label_tag' => 'strong', 'label_class' => 'company-cf', 'value_tag' => 'span'), false) . '</li>';
                }
            }
            if (!empty($html) && count($html) > 0) : ?>
                <div class="company-custom-fields">
                    <strong class="company-cf-title"><?php _e('Company Information', 'noo'); ?></strong>
                    <ul>
                        <?php if(in_array('total_job', $content_meta)):?>
                            <li class="total-job">
                                <strong><?php _e('Total Jobs ', 'noo') ?></strong>
                                <span><?php echo sprintf(esc_html__('%s Jobs', 'noo'), $total_job) ?></span>
                            </li>
                        <?php endif;?>
                        <?php echo implode("\n", $html); ?>
                        <?php
                        if (is_singular('noo_company')) {
                            jm_display_full_address_field($company_id);
                        }
                        ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php

// hidden is job submit page.
if (!is_page_template('page-post-job.php') && noo_get_option('noo_single_company_contact_form')):
    noo_get_layout('company/contact-form');
endif;
?>