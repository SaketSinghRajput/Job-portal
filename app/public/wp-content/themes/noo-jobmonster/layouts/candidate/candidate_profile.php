<?php 
$current_user = wp_get_current_user();
?>
<div class="candidate-profile-form row">
	<div class="col-sm-6">
		<div class="form-group row">
			<label for="profile_image" class="col-sm-4 control-label"><?php _e('Profile Image','noo')?></label>
			<div class="col-sm-8">
				<?php
					$profile_image = ($current_user->ID ? get_user_meta( $current_user->ID, 'profile_image', true) : '');
					$attrs = apply_filters('noo_candidate_require_profile_image', false) ? 'required aria-required="true"' : '';
					noo_image_upload_form_field( 'profile_image', $profile_image, false, __('Recommend size: 160x160px', 'noo'), $attrs);
				?>
			</div>
		</div>
		<?php 
			$fields = jm_get_candidate_custom_fields();
			if( !empty( $fields ) ) {
				foreach ($fields as $field) {
					jm_candidate_render_form_field( $field, $current_user->ID );
				}
			}
		?>
		<?php $socials = jm_get_candidate_socials();
			if(!empty($socials)) {
				foreach ($socials as $social) {
					jm_candidate_render_social_field( $social, $current_user->ID );
				}
			}
		?>
	</div>

	<div class="col-sm-6">
		<div class="form-group">
		    <label for="description" class="control-label"><?php _e('Introduce Yourself','noo')?> <small><?php _e('(Optional)','noo')?></small></label>
			<?php
			$user_content = $current_user->description ? $current_user->description : '';
			noo_wp_editor($user_content, 'candidate_form_description_field', 'description', true);
			?>
		</div>		
	</div>
</div>