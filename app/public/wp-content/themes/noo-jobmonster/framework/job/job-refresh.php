<?php

function noo_get_user_job_refresh_count( $user_id = null ) {
	if ( $user_id === null ) {
		$user_id = get_current_user_id();
	}

	if ( empty( $user_id ) ) {
		return 0;
	}

	return absint( get_user_meta( $user_id, '_job_refresh', true ) );
}

function noo_get_job_refresh_remain( $user_id = null ) {
	if ( $user_id === null ) {
		$user_id = get_current_user_id();
	}

	if ( empty( $user_id ) ) {
		return 0;
	}

	$current_count = noo_get_user_job_refresh_count( $user_id );

	$package = jm_get_job_posting_info( $user_id );

	if ( empty( $package ) || ! isset( $package['job_refresh'] ) ) {
		return 0;
	}

	return max( absint( $package['job_refresh'] ) - absint( $current_count ), 0 );
}

function noo_can_refresh_job($user_id = null){
	return noo_get_job_refresh_remain($user_id) > 0;
}

function noo_job_refresh( $job_id, $user_id = null ) {
    $time = current_time( 'mysql' );
    $args = array(
        'ID'                => $job_id,
        'post_date'         => $time,
        'post_date_gmt'     => get_gmt_from_date( $time ),
        'post_modified'     => $time,
        'post_modified_gmt' => get_gmt_from_date( $time )
    );

    $user_id = !empty($user_id) ? $user_id : get_current_user_id();
    $current = get_user_meta($user_id, '_job_refresh', true);

    update_user_meta($user_id, '_job_refresh', $current + 1);

    return wp_update_post( $args );
}

function noo_ajax_refresh_job() {

	$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;

	if ( empty( $job_id ) ) {
		$response = array(
			'status'  => 'error',
			'message' => esc_html__( 'Job not found.', 'noo' )
		);
	} else {
		if(noo_can_refresh_job()){
			$updated  = noo_job_refresh( $job_id );
			$rm = noo_get_job_refresh_remain();
			if(strlen((string)$rm) >= 7){
				// check job refresh unlimit -1 <-> 99999999 refresh times
				$response = array(
					'status'  => 'success',
					'remain'  =>  sprintf(esc_html__('Unlimited refresh time.', 'noo'), $rm),
					'message' => esc_html__( 'Job refresh success.', 'noo' )
				);
			}else{
				$response = array(
					'status'  => 'success',
					'remain'  =>  sprintf(esc_html__('Remain %s refresh time.', 'noo'), $rm),
					'message' => esc_html__( 'Job refresh success.', 'noo' )
				);
			}
		} else{
			$response = array(
				'status'  => 'error',
				'message' => esc_html__( 'You has exceeded the number of refreshes.', 'noo' )
			);
		}
	}
	wp_send_json( $response );
}

add_action( 'wp_ajax_noo_ajax_refresh_job', 'noo_ajax_refresh_job' );