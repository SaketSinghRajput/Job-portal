<?php
if( !function_exists( 'jm_get_resume_setting' ) ) :
	function jm_get_resume_setting($id = null ,$default = null){
		return jm_get_setting('noo_resume_general', $id, $default);
	}
endif;
if(!function_exists('jm_get_resume_alert_setting')):
    function jm_get_resume_alert_setting($id=null,$default =null){
        return jm_get_setting('noo_resume_alert',$id,$default);
    }
endif;
if( !function_exists( 'jm_resume_enabled' ) ) :
	function jm_resume_enabled(){
		return (bool) jm_get_resume_setting('enable_resume', 1);
	}
endif;

if( !function_exists( 'jm_get_resume_status' ) ) :
	function jm_get_resume_status() {
		return apply_filters('noo_resume_status', array(
			'draft'           => _x( 'Draft', 'Job status', 'noo' ),
			// 'preview'         => _x( 'Preview', 'Job status', 'noo' ),
			'pending'         => _x( 'Pending Approval', 'Job status', 'noo' ),
			'pending_payment' => _x( 'Pending Payment', 'Job status', 'noo' ),
			'publish'         => _x( 'Published', 'Job status', 'noo' ),
		));
	}
endif;

if( !function_exists( 'jm_get_allowed_file_types' ) ) :
	function jm_get_allowed_file_types( $is_display = false ) {
		$allowed_file_types = jm_get_resume_setting('extensions_upload_resume', 'doc,docx,pdf');
		$allowed_file_types = !empty( $allowed_file_types ) ? explode(',', $allowed_file_types ) : array();
		$allowed_exts = array();
		foreach ($allowed_file_types as $type) {
			$type = trim($type);
			if( empty( $type ) ) continue;

			if( $type[0] == '.' && !$is_display ) {
				$type = substr( $type, 1 );
			} elseif( $is_display ) {
				$type = '.' . $type;
			}

			$allowed_exts[] = $type;
		}

		return apply_filters( 'jm_allowed_upload_file_types', $allowed_exts );
	}
endif;

if ( ! function_exists( 'noo_resume_time_ago' ) ) :

    function noo_resume_time_ago( $resume_id = '' ) {
		if ( empty( $resume_id ) ) {
			return false;
		}
	    return sprintf( esc_html__( '% ago', 'noo' ), human_time_diff(get_post_time('U', false, $resume_id), current_time('timestamp')) );
    }

endif;

if ( ! function_exists( 'noo_shortlist_status' ) ) :

	function noo_shortlist_status( $resume_id, $user_id ) {

		if ( empty( $user_id ) || empty( $resume_id ) ) {
			return esc_html__( 'ShortList', 'noo' );
		}

		$list_resume_shortlist = get_user_meta( $user_id, 'list_resume_shortlist', true );

		if ( empty( $list_resume_shortlist ) || !is_array( $list_resume_shortlist ) ) {
			$list_resume_shortlist = array();
		}

		if ( in_array( $resume_id, $list_resume_shortlist ) ) {
			return esc_html__( 'ShortListed', 'noo' );
		}

		return esc_html__( 'ShortList', 'noo' );
	}

endif;

if ( ! function_exists( 'noo_shortlist_icon' ) ) :

	function noo_shortlist_icon( $resume_id, $user_id ) {

		if ( empty( $user_id ) || empty( $resume_id ) ) {
			return '<i class="far fa-heart" aria-hidden="true"></i>';
		}

		$list_resume_shortlist = get_user_meta( $user_id, 'list_resume_shortlist', true );

		if ( empty( $list_resume_shortlist ) || !is_array( $list_resume_shortlist ) ) {
			$list_resume_shortlist = array();
		}

		if ( in_array( $resume_id, $list_resume_shortlist ) ) {
			return '<i class="fa fa-heart" aria-hidden="true"></i>';
		}

		return '<i class="far fa-heart" aria-hidden="true"></i>';
	}

endif;

if ( ! function_exists( 'noo_shortlist' ) ) :

    function noo_shortlist() {

	    $response = array();
	    if ( !empty( $_POST['resume_id'] ) && !empty( $_POST['user_id'] ) && !empty( $_POST['type'] ) ) {
		    $resume_id = absint( $_POST['resume_id'] );
		    $user_id = absint( $_POST['user_id'] );
		    $type = sanitize_text_field( $_POST['type'] );

		    $list_shortlist = get_post_meta( $resume_id, 'list_user_shortlist', true );
		    if ( empty( $list_shortlist ) || !is_array( $list_shortlist ) ) {
			    $list_shortlist = array();
		    }

		    $list_resume_shortlist = get_user_meta( $user_id, 'list_resume_shortlist', true );
		    if ( empty( $list_resume_shortlist ) || !is_array( $list_resume_shortlist ) ) {
			    $list_resume_shortlist = array();
		    }

		    if ( in_array( $user_id, $list_shortlist ) ) {
			    $item = array_search($user_id, $list_shortlist);
			    unset($list_shortlist[$item]);

			    update_post_meta( $resume_id, 'list_user_shortlist', $list_shortlist );

			    $item_company = array_search($resume_id, $list_resume_shortlist);
			    unset($list_resume_shortlist[$item_company]);

			    update_user_meta( $user_id, 'list_resume_shortlist', $list_resume_shortlist );

			    $response['status']  = 'success';
			    $response['message'] = esc_html__( 'You are add shortlist success', 'noo' );
			    $response['label'] = ($type == 'text' ? esc_html__( 'ShortList', 'noo' ) : '<i class="far fa-heart" aria-hidden="true"></i>' );
		    } else {
			    $list_shortlist = array_merge( $list_shortlist, array( $user_id ) );
			    $list_shortlist = array_unique( $list_shortlist );

			    update_post_meta( $resume_id, 'list_user_shortlist', $list_shortlist );

			    $list_resume_shortlist = array_merge( $list_resume_shortlist, array( $resume_id ) );
			    $list_resume_shortlist = array_unique( $list_resume_shortlist );

			    update_user_meta( $user_id, 'list_resume_shortlist', $list_resume_shortlist );
			    $response['status']  = 'success';
			    $response['message'] = esc_html__( 'You are add shortlist success', 'noo' );
			    $response['label'] = ( $type == 'text' ? esc_html__( 'ShortListed', 'noo' ) : '<i class="fa fa-heart" aria-hidden="true"></i>' );
		    }
	    } else {
		    $response['status'] = 'error';
		    $response['message'] = esc_html__( 'Please login to add resumes shortlist.', 'noo' );
	    }

	    wp_send_json( $response );

    }

    add_action( 'wp_ajax_noo_shortlist', 'noo_shortlist' );
    add_action( 'wp_ajax_nopriv_noo_shortlist', 'noo_shortlist' );

endif;

if ( ! function_exists( 'noo_resume_contact' ) ) :

    function noo_resume_contact() {

	    $response = array();
	    if(isset($_POST[ 'email_rehot' ]) && !empty($_POST[ 'email_rehot' ])){
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = '<span class="error-response">' . esc_html__('You can not perform this action.', 'noo') . '</span>';
			wp_send_json( $response );
			die;
		}
        if (isset($_POST['name']) && empty( $_POST['name'])) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please enter your name.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
        if (isset($_POST['mail']) && empty( $_POST['mail'])) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please enter your email.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        } 
        if (isset($_POST['phone']) && empty( $_POST['phone'])) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please enter your phone.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
        if (isset($_POST['message']) && empty( $_POST['message'])) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please enter your message.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
        if (isset($_POST['g-recaptcha-response']) && !noo_recaptcha_verification()) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please complete the Recaptcha challenge.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
	    if ( !empty( $_POST['candidate_id'] )) {
		    $name         = sanitize_text_field( $_POST['name'] );
		    $mail         = sanitize_text_field( $_POST['mail'] );
		    $phone        = sanitize_text_field( $_POST['phone'] );
		    $message_content      = sanitize_text_field( $_POST['message'] );
		    $candidate_id = sanitize_text_field( $_POST['candidate_id'] );

		    $candidate = ! empty( $candidate_id ) ? get_userdata( $candidate_id ) : false;
		    if ( $candidate ) {
			    $email = $candidate ? $candidate->user_email : '';
			    $subject = sprintf( __( 'New message from %s', 'noo' ), get_bloginfo( 'name' ) );
			    $message = '';
			    $message .= sprintf( __( 'You get a contact from %s', 'noo' ), $name ) . '<br/><br/>';
			    $message .= sprintf( __( 'From email %s', 'noo' ), $mail ) . '<br/><br/>';
			    $message .= sprintf( __( 'From phone %s', 'noo' ), $phone ) . '<br/><br/>';
			    $message .= sprintf( __( 'Message :', 'noo' ) ) . '<br/><br/>';
			    $message .= $message_content . '<br/><br/>';
			    $result = noo_mail( $email, $subject, $message, '', 'noo_send_contact_resume' );
			    if ( $result ) {
				    $response['status'] = 'success';
				    $response['message'] = esc_html__( 'Send contact success', 'noo' );
				    wp_send_json( $response );
			    }
			    $response['status'] = 'error';
			    $response['message'] = esc_html__( 'Send contact error', 'noo' );
			    wp_send_json( $response );
		    } else {
			    $response['status'] = 'error';
			    $response['message'] = esc_html__( 'Candidate not exits', 'noo' );
	        }
	    } else {
		    $response['status'] = 'error';
		    $response['message'] = esc_html__( 'Do not support this action', 'noo' );
	    }

	    wp_send_json( $response );

    }

    add_action( 'wp_ajax_resume_contact', 'noo_resume_contact' );
    add_action( 'wp_ajax_nopriv_resume_contact', 'noo_resume_contact' );

endif;
if(!function_exists('noo_resume_list_customRSS')):
    function noo_resume_list_customRSS(){
        noo_get_layout('resume/resume_feed');
    }
endif;

add_action('init','resume_customRSS');
function resume_customRSS(){
    add_feed('resume_feed','noo_resume_list_customRSS');
}

