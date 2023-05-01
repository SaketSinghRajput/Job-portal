<?php

function noo_get_user_resume_refresh_count( $user_id = null ) {
    if ( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    if ( empty( $user_id ) ) {
        return 0;
    }

    return absint( get_user_meta( $user_id, '_resume_refresh', true ) );
}

function noo_get_resume_refresh_remain( $user_id = null ) {
    if ( $user_id === null ) {
        $user_id = get_current_user_id();
    }

    if ( empty( $user_id ) ) {
        return 0;
    }

    $current_count = noo_get_user_resume_refresh_count( $user_id );
    $package = jm_get_resume_posting_info( $user_id );

    if ( empty( $package ) || ! isset( $package['resume_refresh'] ) ) {
        return 0;
    }

    return max( absint( $package['resume_refresh'] ) - absint( $current_count ), 0 );
}

function noo_can_refresh_resume($user_id = null){
    return noo_get_resume_refresh_remain($user_id) > 0;
}

function noo_resume_refresh( $resume_id, $user_id = null ) {
    $time = current_time( 'mysql' );
    $args = array(
        'ID'                => $resume_id,
        'post_date'         => $time,
        'post_date_gmt'     => get_gmt_from_date( $time ),
        'post_modified'     => $time,
        'post_modified_gmt' => get_gmt_from_date( $time )
    );

    $user_id = !empty($user_id) ? $user_id : get_current_user_id();
    $current = get_user_meta($user_id, '_resume_refresh', true);

    update_user_meta($user_id, '_resume_refresh', $current + 1);

    return wp_update_post( $args );
}

function noo_ajax_refresh_resume() {

    $resume_id = isset( $_POST['resume_id'] ) ? intval( $_POST['resume_id'] ) : 0;

    if ( empty( $resume_id ) ) {
        $response = array(
            'status'  => 'error',
            'message' => esc_html__( 'Resume not found.', 'noo' )
        );
    } else {
        if(noo_can_refresh_resume()){
            $updated  = noo_resume_refresh( $resume_id );
            $rm = noo_get_resume_refresh_remain();
            $response = array(
                'status'  => 'success',
                'remain'  =>  sprintf(esc_html__('Remain %s refresh time.', 'noo'), $rm),
                'message' => esc_html__( 'Resume refresh success.', 'noo' )
            );
        } else{
            $response = array(
                'status'  => 'error',
                'message' => esc_html__( 'You has exceeded the number of refreshes.', 'noo' )
            );
        }
    }
    wp_send_json( $response );
}

add_action( 'wp_ajax_noo_ajax_refresh_resume', 'noo_ajax_refresh_resume' );