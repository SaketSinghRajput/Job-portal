<?php 
$resume_id = isset($_GET['resume_id']) ? absint($_GET['resume_id']) : 0;
$query = new WP_Query(array(
	'post__in' => array($resume_id),
	'post_type'=>'noo_resume',
	'post_status'=>'draft',
	// 'post_status'=>array('publish', 'pending'),
));

$back_location_query=array(
	'action'=>'resume_detail',
	'resume_id'=>$resume_id
);

?>
<div class="jpanel jpanel-resume-preview">
	<div class="jpanel-body">
		<div class="resume-preview">
			<div class="resume-form-detail">
				<?php 
					if($query->post_count){
						?>
                        <div class="row">
                            <?php jm_resume_detail($query); ?>
                        </div>
                        <?php do_action('noo_jm_resume_preview')?>
						<div class="form-actions job-preview-actions text-center clearfix">
							<?php $submit_agreement = jm_get_resume_setting( 'submit_agreement', null);
							$submit_agreement = (!empty( $submit_agreement ) && !is_null($submit_agreement)) ? $submit_agreement : sprintf(__('The employer can find your resume and contact you via email or regarding your resume. Preview all information thoroughly before submitting your resume for approval.','noo'), get_bloginfo('name') );
							if( !empty( $submit_agreement ) ) :
							?>
							<div class="resume-preview-notice">
								<div class="checkbox">
									<div class="form-control-flat"><label class="checkbox"><input name="agreement" type="checkbox" class="jform-validate" required title="<?php esc_attr_e('You must agree with these terms.','noo')?>"><i></i> <?php echo apply_filters('noo_post_resume_preview_notice', $submit_agreement)?></label></div>
								</div>
							</div>
							<?php endif; ?>
							
							<a href="<?php echo esc_url(add_query_arg($back_location_query))?>" class="btn btn-primary"><?php _e('Back','noo')?></a>&nbsp;&nbsp;&nbsp;
					 		<button type="submit" class="btn btn-primary"><?php _e('Submit','noo')?></button>
					 	</div>
						<?php
					}else{
						echo '<h2 class="text-center" style="min-height:200px">'.__('Resume not found !','noo').'</h2>';
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php wp_reset_query();?>