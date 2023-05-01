<?php

if( !function_exists('jm_is_resume_posting_page') ) :
	function jm_is_resume_posting_page( $page_id = '' ){
		$page_id = empty( $page_id ) ? get_the_ID() : $page_id;
		if( empty( $page_id ) ) return false;

		$page_temp = get_page_template_slug( $page_id );

		return 'page-post-resume.php' === $page_temp;
	}
endif;

if( !function_exists('jm_get_resume_posting_remain') ) :
	function jm_get_resume_posting_remain( $user_id = '' ) {
		if(empty($user_id)){
			$user_id = get_current_user_id();
		}

		$package = jm_get_resume_posting_info( $user_id );
		$resume_limit = empty( $package ) || !is_array( $package ) || !isset( $package['resume_limit'] ) ? 0 : $package['resume_limit'];
		$resume_added = jm_get_resume_posting_added( $user_id );

		return absint($resume_limit) - absint($resume_added);
	}
endif;
if( !function_exists('jm_get_feature_resume_added') ) :
    function jm_get_feature_resume_added( $user_id = null ){
        if( $user_id === null ) {
            $user_id = get_current_user_id();
        }

        if( empty( $user_id ) ) {
            return 0;
        }

        return absint(get_user_meta( $user_id, '_resume_featured', true ));
    }
endif;
if( !function_exists('jm_get_feature_resume_remain') ) :
    function jm_get_feature_resume_remain( $user_id = null ){
        if( $user_id === null ) {
            $user_id = get_current_user_id();
        }

        if( empty( $user_id ) ) {
            return 0;
        }

        $current_feature_count =  jm_get_feature_resume_added($user_id);
        $package = jm_get_resume_posting_info($user_id);
        if( empty( $package ) || !isset( $package['resume_featured'] ) ) {
            return 0;
        }

        return max( absint( $package['resume_featured'] ) - absint($current_feature_count), 0 ) ;
    }
endif;
if( ! function_exists('jm_can_set_feature_resume')):
    function jm_can_set_feature_resume($user_id = null){
        return jm_get_feature_resume_remain($user_id) > 0;
    }
endif;
if( !function_exists('jm_get_resume_posting_added') ) :
	function jm_get_resume_posting_added( $user_id = '' ) {
		if(empty($user_id)){
			$user_id = get_current_user_id();
		}

		$resume_added = get_user_meta($user_id,'_resume_added',true);

		return empty( $resume_added ) ? 0 : absint( $resume_added );
	}
endif;

if( !function_exists('jm_get_resume_posting_info') ) :
	function jm_get_resume_posting_info($user_id=''){
		if(empty($user_id)){
			$user_id = get_current_user_id();
		}

		if( jm_is_woo_resume_posting() ) {
			// delete_user_meta($user_id, '_resume_package'); // This code is for debuging
			$posting_info = get_user_meta($user_id, '_resume_package', true);
		} else {
			$posting_info = array(
				'resume_limit'    => absint(jm_get_resume_setting( 'resume_posting_limit',5)),
                'resume_refresh'   =>  absint(jm_get_resume_setting( 'max_resume_refresh',5)),
                'resume_featured'   => absint(jm_get_resume_setting('resume_feature_limit',5)),
			);
		}

		return apply_filters( 'jm_resume_posting_info', $posting_info, $user_id );
	}
endif;

if( !function_exists('jm_increase_resume_posting_count') ) :
	function jm_increase_resume_posting_count($user_id='') {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
		if( empty( $user_id ) ) return false;

		$_count = jm_get_resume_posting_added( $user_id );
		update_user_meta($user_id, '_resume_added', $_count + 1 );
	}
endif;

if( !function_exists('jm_decrease_resume_posting_count') ) :
	function jm_decrease_resume_posting_count($user_id='') {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
		if( empty( $user_id ) ) return false;

		$_count = jm_get_resume_posting_added( $user_id );
		update_user_meta($user_id, '_resume_added', max( 0, $_count - 1 ) );
	}
endif;

if( !function_exists('jm_can_post_resume') ) :
	function jm_can_post_resume($user_id = ''){
		if(empty($user_id)){
			$user_id = get_current_user_id();
		}
		if( !Noo_Member::is_candidate( $user_id ) ) return false;

		if( jm_is_woo_resume_posting() ) {
			// Resume posting with a package selected
			if( jm_is_resume_posting_page() && isset( $_REQUEST['package_id'] ) ) {
				return true;
			}

			// Check the number of resume added.
			return jm_get_resume_posting_remain( $user_id ) > 0;
		}

		return true;		
	}
endif;

if( !function_exists('jm_can_edit_resume') ) :
	function jm_can_edit_resume($resume_id = 0, $user_id = 0) {
		if( empty( $resume_id ) ) return jm_can_post_resume( $user_id );

		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id;
		if( empty($user_id) ) return false;

		return ( $user_id == get_post_field( 'post_author', $resume_id ) );
	}
endif;


if ( ! function_exists( 'jm_resume_expired_set_schedule' ) ) :
	function jm_resume_expired_set_schedule() {
		if ( get_option( 'noo_resume_cron_resumes' ) == '1' && ( wp_get_schedule( 'noo_resume_check_expired_resumes' ) !== false ) ) {
			return;
		}
		wp_clear_scheduled_hook( 'noo_resume_check_expired_resumes' );
		wp_schedule_event( time(), 'hourly', 'noo_resume_check_expired_resumes' );

		delete_option( 'noo_resume_cron_resumes' );
		update_option( 'noo_resume_cron_resumes', '1' );
	}

	add_action( 'admin_init', 'jm_resume_expired_set_schedule' );
endif;


if ( ! function_exists( 'jm_resume_expired_cron_action' ) ) :
	function jm_resume_expired_cron_action() {
		global $wpdb;

		$post_resume_role = jm_get_setting( 'jm_action_control', 'post_resume' );
		if ($post_resume_role == 'package') {

			// Change status to expired
			$resume_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = '_expires'
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'noo_resume'
				", current_time( 'timestamp' ) ) );
			if ( $resume_ids ) {
				foreach ( $resume_ids as $resume_id ) {
					$resume_data                  = array();
					$resume_data[ 'ID' ]          = $resume_id;
					$resume_data[ 'post_status' ] = 'pending';
					wp_update_post( $resume_data ); 
					update_post_meta( $resume_id, '_viewable', 'no' );
				}
			}
		}
	}
	add_action( 'noo_resume_check_expired_resumes', 'jm_resume_expired_cron_action' );
endif;

if ( ! function_exists( 'jm_set_resume_expired' ) ) :
	function jm_set_resume_expired( $resume_id = '' ) {
		if ( empty( $resume_id ) ) {
			return false;
		}

		$_ex         = noo_get_post_meta( $resume_id, '_expires' );
		$post_resume_role = jm_get_setting( 'jm_action_control', 'post_resume' );
		$candidate_id = get_post_field( 'post_author', $resume_id );
		$package = jm_get_resume_posting_info( $candidate_id );
		if ( empty( $_ex ) && is_array($package) && $post_resume_role == 'package' ) {
			if(isset($package['expired'])){
				$_expires              = absint($package['expired']);
			}else{
				$_expires = 0; // Unlimited
			}
			
			update_post_meta( $resume_id, '_expires', $_expires );
		}
	}
endif;
