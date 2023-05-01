<?php

if ( ! function_exists( 'jm_is_woo_job_posting' ) ) :
	function jm_is_woo_job_posting() {
		$job_package_actions = array(
			jm_get_action_control( 'post_job' ),
			jm_get_action_control( 'view_resume' ),
//			jm_get_action_control( 'view_candidate_profile' ),
		);

		return in_array( 'package', $job_package_actions );
	}
endif;

if(! function_exists('jm_check_package_post_job')) :
    function jm_check_package_post_job(){
        $post_job_action = jm_get_action_control('post_job');
        if($post_job_action =='package'){
            return true;
        }else{
            return false;
        }
    }
endif;
if(! function_exists('jm_check_package_view_resume_detail')){
    function jm_check_package_view_resume_detail(){
        $view_resume_action = jm_get_action_control('view_resume');
        if($view_resume_action == 'package'){
            return true;
        }else{
            return false;
        }
    }
}
