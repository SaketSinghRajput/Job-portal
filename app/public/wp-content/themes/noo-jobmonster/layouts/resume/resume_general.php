<?php 
$resume_id = isset($_GET['resume_id']) ?absint($_GET['resume_id']) : 0;
$resume = $resume_id ? get_post($resume_id) : '';
?>
<?php do_action('noo_post_resume_general_before'); ?>
<div class="resume-form">
	<div class="resume-form-general row">
		<div class="col-sm-6">
			<div class="form-group row required-field">
				<label for="title" class="col-sm-5 control-label"><?php _e('Resume Title','noo')?></label>
				<div class="col-sm-7">
			    	<input type="text" value="<?php echo ($resume ? $resume->post_title : '')?>" class="form-control jform-validate" id="title"  name="title" autofocus required>
			    </div>
			</div>
			<?php 
			$fields = jm_get_resume_custom_fields();

			if( !empty( $fields ) ) {
				foreach ($fields as $field) {
                    $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
                    $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;
                    $allow_multiple_select = strpos($field['type'], 'multi') !== false;
                    if ($field['name']=='_job_category'){
                        $name = '_job_category';
                        if ($allow_multiple_select) {
                            $name = '_job_category[]';
                        }

                        $selected = array();
                        if ($resume_id) {
                            $selected = get_post_meta($resume_id,'_job_category',true );
                            $selected = noo_json_decode($selected);
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
                            'required' => $field['required'],
                            'orderby' => 'name',
                            'multiple' => $allow_multiple_select,
                            'walker' => new Noo_Walker_TaxonomyDropdown(),

                    ); ?>

                        <div class="form-group row <?php noo_custom_field_class( $field); ?>" data-placeholder="<?php echo sprintf(esc_html__('Select %s', 'noo'), $field['label']); ?>">
                            <label for="<?php echo esc_attr($field['label'])?>" class="col-sm-5 control-label"><?php echo esc_html($label);  ?></label>
                            <div class="col-sm-7">
                                <?php  wp_dropdown_categories( $job_category_args );?>
                            </div>
                        </div>

                        <?php
                    } elseif($field['name']=='_job_location'){
                        $allow_user_input = strpos($field['type'], 'input') !== false;
                        $name = '_job_location';
                        if ($allow_multiple_select) {
                            $name = '_job_location[]';
                        }

                        $selected = array();
                        if ($resume_id) {
                            $selected = get_post_meta($resume_id, '_job_location', true);
                            $selected = noo_json_decode($selected);
                        }
                        $location_args = array(
                            'hide_empty'      => 0,
                            'echo'            => 1,
                            'selected'        => $selected,
                            'hierarchical'    => 1,
                            'name'            => $name,
                            'id'              => 'noo-field-job_location',
                            'class'           => 'form-control noo-select form-control-chosen',
                            'depth'           => 0,
                            'taxonomy'        => 'job_location',
                            'value_field'     => 'term_id',
                            'required' => $field['required'],
                            'orderby' => 'name',
                            'multiple' => $allow_multiple_select,
                            'walker' => new Noo_Walker_TaxonomyDropdown(),
                        );?>
                        <div class="form-group row <?php noo_custom_field_class($field); ?>"  data-placeholder="<?php echo sprintf(esc_html__('Select %s', 'noo'), $field['label']); ?>">
                            <label for="noo-field-job_location" class="col-sm-5 control-label"><?php echo esc_html($label); ?></label>
                            <div class="col-sm-7 job_location_field">
                                <?php  wp_dropdown_categories( $location_args );
                                if ( $allow_user_input) {
                                    jm_job_add_new_location();
                                }
                                ?>

                            </div>
                        </div>
                        <?php
                    }
                    else{
                        jm_resume_render_form_field( $field, $resume_id );

                    }

				}
			}
			?>
            <?php
            $enable_socials =noo_get_option('noo_resume_social','1');
            $socials = jm_get_resume_socials();
            if(!empty($socials) && $enable_socials) {
                foreach ($socials as $social) {
                    jm_resume_render_social_field( $social, $resume_id);
                }
            }
            ?>
		</div>
		<?php if( jm_get_resume_setting('enable_upload_resume', '1') ) : ?>
			<div class="col-sm-6">
                <div class="form-group">
    				<label for="file_cv" class="control-label"><?php _e('Upload your Attachment','noo')?></label>
    				<div class="form-control-flat">
    					<div class="upload-to-cv clearfix">
    				    	<?php noo_file_upload_form_field( 'file_cv', jm_get_allowed_attach_file_types(), noo_get_post_meta( $resume_id, '_noo_file_cv' ), '' ) ?>
    					</div>
    				</div>
                </div>
			</div>
		<?php endif; ?>
		<div class="col-sm-6">
			<div class="form-group">
			    <label for="desc" class="control-label"><?php _e('Professional Summary','noo')?></label>
				<?php
				$default_resume_content = jm_get_resume_setting( 'default_resume_content', '' );
				$resume_content = $resume ? $resume->post_content : $default_resume_content;
				noo_wp_editor($resume_content, 'resume_form_description_field', 'desc', true);
				?>
			</div>
		</div>
	</div>
</div>
<?php do_action('noo_post_resume_general_after'); ?>