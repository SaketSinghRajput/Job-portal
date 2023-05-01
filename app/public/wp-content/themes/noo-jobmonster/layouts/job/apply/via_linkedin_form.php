<?php
global $post;
$job_id = empty($job_id) ? $post->ID : $job_id;
$job_title = get_the_title($job_id);
$company_id = jm_get_job_company($job_id);
$company_name = !empty($company_id) ? get_the_title($company_id) : '';
$url= Noo_Apply_Linkedin::get_apply_url($job_id);
?>
<a class="via-linkedin btn btn-default" href="<?php echo esc_url($url)?>"><?php _e('Apply via LinkedIn','noo');?></a>