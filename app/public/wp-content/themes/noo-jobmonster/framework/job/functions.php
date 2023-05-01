<?php
if ( ! function_exists( 'jm_get_job_setting' ) ) :
	function jm_get_job_setting( $id = null, $default = null ) {
		return jm_get_setting( 'noo_job_general', $id, $default );
	}
endif;

if(!function_exists('jm_job_single_apply_text')){
	function jm_job_single_apply_text($is_external_url = false){
		if($is_external_url){
			$text = __('Apply for this job','noo');
		}else{
			$text = __('Apply for this job','noo');
		}
		return apply_filters('jm_job_single_apply_text',$text, $is_external_url);
	}
}

if(! function_exists('jm_get_job_alert_setting')):
    function jm_get_job_alert_setting($id = null, $default = null){
        return jm_get_setting('noo_job_alert',$id,$default);
    }
endif;

if ( ! function_exists( 'jm_get_application_setting' ) ) :
	function jm_get_application_setting( $id = null, $default = null ) {
		return jm_get_setting( 'noo_job_linkedin', $id, $default );
	}
endif;

if ( ! function_exists( 'jm_get_email_setting' ) ) :
	function jm_get_email_setting( $id = null, $default = null ) {
		return jm_get_setting( 'noo_email', $id, $default );
	}
endif;

if ( ! function_exists( 'jm_get_employer_company' ) ) :
	function jm_get_employer_company( $employer_id = '' ) {
		if ( empty( $employer_id ) ) {
			$employer_id = get_current_user_id();
		}

		return get_user_meta( $employer_id, 'employer_company', true );
	}
endif;
if ( ! function_exists( 'jm_get_button_text' ) ) :
	function jm_get_button_text( $type = 'job' ) {
		if ( $type == 'job' ) {
			$button_text = __('Post a Job', 'noo');
		}elseif($type == 'resume'){
			$button_text = __('Post a Resume', 'noo');
		}
		return apply_filters('jm_get_button_text', $button_text, $type);
	}
endif;

if ( ! function_exists( 'jm_get_job_company' ) ) :
	function jm_get_job_company( $job = '' ) {
		$job_id = 0;
		if ( is_object( $job ) ) {
			$job_id = $job->ID;
		} elseif ( is_numeric( $job ) ) {
			$job_id = $job;
		}

		if ( empty( $job_id ) ) {
			$job_id = get_the_ID();
		}

		if ( 'noo_job' != get_post_type( $job_id ) ) {
			return 0;
		}

		$company_id = noo_get_post_meta( $job_id, '_company_id', '' );
		if ( empty( $company_id ) ) {
			$company_id = jm_get_employer_company( get_post_field( 'post_author', $job_id ) );
		}

		return $company_id;
	}
endif;

if ( ! function_exists( 'jm_get_job_type' ) ) :
	function jm_get_job_type( $job = null ,$allow_multiple=true) {
		global $noo_job_type;

		if ( is_int( $job ) ) {
			$job = get_post( $job );
		}

		if ( empty( $job->post_type ) || ! is_object( $job ) || $job->post_type !== 'noo_job' ) {
			return;
		}

		if ( empty( $noo_job_type ) ) {
			$noo_job_type = array();
		}

		if ( ! isset( $noo_job_type[ $job->ID ] ) ) {
			$types = get_the_terms( $job->ID, 'job_type' );
			$type  = false;

			if ( ! is_wp_error( $types ) && ! empty( $types ) ) {
			    if($allow_multiple){
                    foreach ($types as $type) {
                        $type->color = jm_get_job_type_color($type->term_id);
                        $noo_job_type[$job->ID][] = $type;
                    }
                }else{
                        $type=current($types);
                        $type->color = jm_get_job_type_color( $type->term_id );
                        $noo_job_type[ $job->ID ][] = $type;
                }
			} else {
				return;
			}
		}

		return apply_filters( 'noo_get_job_type', $noo_job_type[ $job->ID ], $job );
	}
endif;

if ( ! function_exists( 'jm_get_job_status' ) ) :
	function jm_get_job_status() {
		return apply_filters( 'noo_job_status', array(
			'publish'         => _x( 'Active', 'Job status', 'noo' ),
			'inactive'        => _x( 'Inactive', 'Job status', 'noo' ),
			'pending'         => _x( 'Pending Approval', 'Job status', 'noo' ),
			'pending_payment' => _x( 'Pending Payment', 'Job status', 'noo' ),
			'expired'         => _x( 'Expired', 'Job status', 'noo' ),
			'draft'           => _x( 'Draft', 'Job status', 'noo' ),
			// 'preview'         => _x( 'Preview', 'Job status', 'noo' ),
		) );
	}
endif;

if ( ! function_exists( 'jm_job_default_data' ) ) :
	function jm_job_default_data( $post_ID = 0, $post = null, $update = false ) {

		if ( ! $update && ! empty( $post_ID ) && $post->post_type == 'noo_job' ) {
			$featured = noo_get_post_meta( $post_ID, '_featured' );
			if ( empty( $featured ) ) {
				update_post_meta( $post_ID, '_featured', 'no' );
			}
		}
	}

	add_filter( 'wp_insert_post', 'jm_job_default_data', 10, 3 );
endif;

function jm_update_slug_edit_job( $data ) {

	if ( is_admin() ) {
		return $data;
	}
	if ( ! in_array( $data[ 'post_type' ], array( 'noo_job', 'noo_resume', 'noo_company' ) ) ) {
		return $data;
	}
	if ( ! in_array( $data[ 'post_status' ], array( 'draft', 'pending', 'auto-draft' ) ) ) {
		unset( $data[ 'post_name' ] );
	}

	return $data;
}

//add_filter( 'wp_insert_post_data', 'jm_update_slug_edit_job', 99 );

function jm_ajax_quick_view_job() {

	if ( ! check_ajax_referer( 'job-quick-action', 'security', false ) ) {
		$result = array(
			'success' => false,
			'message' => '<span class="error-response">' . __( 'Your session has expired.', 'noo' ) . '</span>',
		);

		wp_send_json( $result );

		return;
	}

	if ( ! isset( $_POST[ 'job_id' ] ) || empty( $_POST[ 'job_id' ] ) ) {
		$result = array(
			'success' => false,
			'message' => '<span class="error-response">' . __( 'There\'s an unknown error. Please retry or contact Administrator.', 'noo' ) . '</span>',
		);

		wp_send_json( $result );

		return;
	} else {
		$html = '';

		ob_start();

		$job_id = $_POST[ 'job_id' ];

		include( locate_template( "layouts/job/job-quickview.php" ) );

		$html = ob_get_clean();

		$result = array(
			'success' => true,
			'html'    => $html,
		);

		wp_send_json( $result );
	}
}

add_action( 'wp_ajax_noo_quick_view_job', 'jm_ajax_quick_view_job' );
add_action( 'wp_ajax_nopriv_noo_quick_view_job', 'jm_ajax_quick_view_job' );

function noo_modal_job_send_mail() {
	noo_get_layout( 'modal-send-mail-job' );
}

add_action( 'wp_footer', 'noo_modal_job_send_mail' );

add_action('init','job_customRSS');
function job_customRSS(){
    add_feed('job_feed','noo_job_list_customRSS');
}

add_action( 'wp_ajax_show_more_company', 'show_more_company' );
add_action( 'wp_ajax_nopriv_show_more_company', 'show_more_company' );

function show_more_company() {
	global $wpdb;
	check_ajax_referer( 'show_more_company', 'security' );
	
	$filter = $_POST['filter'] ? $_POST['filter'] : '';
	
	if( !isset( $_POST['start'] ) || $_POST['start'] == '' || !is_numeric($_POST['start']) ) {
		$result = array(
			'success' => false,
			'html'    => '',
		);
	} else
	{
		$start = $_POST['start'];
		$limit_show = ( !isset( $_POST['limit'] ) || $_POST['limit'] == '' ) ? 5 : $_POST['limit'];
		$query = "SELECT p.ID, p.post_title FROM {$wpdb->prefix}posts as p WHERE p.post_type = 'noo_company' AND p.post_status = 'publish'";
		if(!empty($filter)){
			$query .= " AND p.post_title <> '' AND p.post_title LIKE '{$filter}%'";
		}
		$query .= " ORDER BY p.post_title ASC LIMIT $start, $limit_show";
		$rows = $wpdb->get_results($query);
		$html = '';
		if( !empty( $rows ) )
		{
			ob_start();
			foreach ($rows as $post)
			{
				$company_name = $post->post_title;
				if (empty($company_name)){
					continue;
				}
				$count = noo_company_job_count($post->ID);

				echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . noo_get_the_company_name($company_name) . ' (' . $count . ')</a></li>';

			}
			$html = ob_get_clean();
		}
		$result = array(
			'success' => true,
			'html'    => $html,
			'start'   => $start + $limit_show,
		);
	}
	wp_send_json( $result );
}
