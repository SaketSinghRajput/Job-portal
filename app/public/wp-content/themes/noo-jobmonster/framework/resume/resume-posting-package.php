<?php

if( !function_exists('jm_is_woo_resume_posting') ) :
	function jm_is_woo_resume_posting(){
		$resume_package_actions = array( 
			jm_get_action_control( 'post_resume' ),
			jm_get_action_control( 'view_job' ),
			jm_get_action_control( 'apply_job' ),
		);
		return in_array( 'package', $resume_package_actions );
	}
endif;

if( !function_exists('jm_check_package_post_resume')):
    function jm_check_package_post_resume(){
        $resume_post_action = jm_get_action_control('post_resume');
        if($resume_post_action == 'package'){
            return true;
        }else{
            return false;
        }
    }
endif;
