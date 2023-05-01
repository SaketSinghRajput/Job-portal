<?php

if ( ! function_exists( 'jm_job_expired_set_schedule' ) ) :
	function jm_job_expired_set_schedule() {
		if ( get_option( 'noo_job_cron_jobs' ) == '1' && ( wp_get_schedule( 'noo_job_check_expired_jobs' ) !== false ) ) {
			return;
		}
		wp_clear_scheduled_hook( 'noo_job_check_expired_jobs' );
		wp_schedule_event( time(), 'hourly', 'noo_job_check_expired_jobs' );

		delete_option( 'noo_job_cron_jobs' );
		update_option( 'noo_job_cron_jobs', '1' );
	}

	add_action( 'admin_init', 'jm_job_expired_set_schedule' );
endif;

if ( ! function_exists( 'jm_job_expired_cron_action' ) ) :
	function jm_job_expired_cron_action() {
		global $wpdb;

		// Change status to expired
		$job_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status IN ( 'publish', 'inactive' )
			AND posts.post_type = 'noo_job'
			", current_time( 'timestamp' ) ) );

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				$job_data                  = array();
				$job_data[ 'ID' ]          = $job_id;
				$job_data[ 'post_status' ] = 'expired';
				wp_update_post( $job_data );
			}
		}
	}

	add_action( 'noo_job_check_expired_jobs', 'jm_job_expired_cron_action' );
endif;

if ( ! function_exists( 'jm_set_job_expired' ) ) :
	function jm_set_job_expired( $job_id = '' ) {
		if ( empty( $job_id ) ) {
			return false;
		}

		$_ex         = noo_get_post_meta( $job_id, '_expires' );
		$employer_id = get_post_field( 'post_author', $job_id );
		if ( empty( $_ex ) && $package = jm_get_job_posting_info( $employer_id ) ) {
			$_expires = strtotime( '+' . absint( @$package[ 'job_duration' ] ) . ' day' );
			update_post_meta( $job_id, '_expires', $_expires );
			$closing = noo_get_post_meta( $job_id, '_closing' );
			if ( empty( $closing ) ) {
				$closing = $_expires;
				update_post_meta( $job_id, '_closing', $_expires );
			}
		}
	}
endif;

if ( ! function_exists( 'noo_check_before_job_expired' ) ) :

	function noo_check_before_job_expired() {
		global $wpdb;

		$day_option    = jm_get_job_setting( 'forewarning_job_expired', 3 );
		$day_timestamp = strtotime( '+' . $day_option . ' day', current_time( 'timestamp' ) );

		if ( $day_option > 0 ) {

			$job_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status IN ( 'publish', 'inactive' )
			AND posts.post_type = 'noo_job'
			", $day_timestamp ) );

			if ( $job_ids ) {
				foreach ( $job_ids as $job_id ) {

					$forewarning = get_post_meta( $job_id, '_noo_forewarning_expired', true );

					if ( ! $forewarning ) {
						// Send mail notice.
						noo_email_forewarning_expired( $job_id );
						update_post_meta( $job_id, '_noo_forewarning_expired', 1 );
					}
				}
			}
		}
	}

	add_action( 'noo_job_check_expired_jobs', 'noo_check_before_job_expired' );

endif;

if ( ! function_exists( 'noo_email_forewarning_expired' ) ) :

	function noo_email_forewarning_expired( $job_id ) {

		$day_option    = jm_get_job_setting( 'forewarning_job_expired', 3 );

		$job       = get_post( $job_id );
		$employer_id = $job->post_author;

		$employer = get_user_by( 'id', $employer_id );

		$to = $employer->user_email;

		if ( is_multisite() ) {
			$blogname = $GLOBALS['current_site']->site_name;
		} else {
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}


		$array_replace = array(
			'[job_title]'      => $job->post_title,
			'[job_url]'        => get_permalink( $job ),
			'[job_company]'    => $employer->display_name,
			'[job_manage_url]' => Noo_Member::get_endpoint_url( 'manage-job' ),
			'[expire_date]' => $day_option,
			'[site_name]'      => $blogname,
			'[site_url]'       => esc_url( home_url( '' ) ),
		);

		$subject = jm_et_get_setting( 'employer_job_before_expired_subject' );
		$subject       = str_replace( array_keys( $array_replace ), $array_replace, $subject );

		$message = jm_et_get_setting( 'employer_job_before_expired_content' );
		$message = str_replace( array_keys( $array_replace ), $array_replace, $message );

		$subject = jm_et_custom_field('job',$job_id, $subject);
		$message = jm_et_custom_field('job',$job_id, $message);

		noo_mail( $to, $subject, $message, '', 'noo_forewarning_expired' );
	}

endif;