<?php



if ( ! function_exists( 'noo_ajax_job_send_email' ) ) :

	function noo_ajax_job_send_email() {

		check_ajax_referer( 'noo-email-send-job', 'wp_nonce', esc_html__( 'Security Breach! Please contact admin!', 'noo' ) );
		$job_id  = isset( $_POST[ 'job_id' ] ) ? intval($_POST[ 'job_id' ]) : 0;
		$email   = isset( $_POST[ 'friend_email' ] ) ? sanitize_email($_POST[ 'friend_email' ]) : '';
		$name    = isset( $_POST[ 'friend_name' ] ) ? esc_html($_POST[ 'friend_name' ]) : '';
		$content = isset( $_POST[ 'email_content' ] ) ? esc_textarea( $_POST[ 'email_content' ] ) : '';

		if ( empty( $job_id ) ) {

			$result = array(
				'success' => false,
				'message' => esc_html__( 'There\'s an unknown error. Please retry or contact Administrator.', 'noo' ),
			);

			wp_send_json( $result );

			die;
		}

		if ( empty( $name ) ) {

			$result = array(
				'success' => false,
				'message' => esc_html__( 'Please enter your name.', 'noo' ),
			);

			wp_send_json( $result );

			die;
		}
		if (isset($_POST['g-recaptcha-response']) && !noo_recaptcha_verification()) {

            $result['status'] = 'error';
            $result['message'] =  esc_html__('Please complete the Recaptcha challenge.', 'noo') ;

            wp_send_json( $result );

            die;
        }

		if ( empty( $email ) or ! is_email( $email ) ) {

			$result = array(
				'success' => false,
				'message' => esc_html__( 'Please enter a valid email.', 'noo' ),
			);

			wp_send_json( $result );

			die;
        }else {

			// Get Job info

			$job = get_post( $job_id );

			if ( ! empty( $job ) ) {

				$site_name = get_bloginfo( 'name' );

				$job_link  = get_permalink( $job );
				$job_title = get_the_title( $job );

				$email_title = sprintf( esc_html__( '%s sent a job for you: %s', 'noo' ), esc_html( $name ), $job_title );

				$email_content = $content . '<br/><br/>';
				$email_content .= sprintf( esc_html__( 'Email send from: %s - %s', 'noo' ), $site_name, get_home_url() ) . '<br/>';

				$send = noo_mail( $email, $email_title, $email_content );

				if ( $send ) {
					$result = array(
						'success' => true,
						'message' => esc_html__( 'Email sent to success.', 'noo' ),
					);
				} else {
					$result = array(
						'success' => false,
						'message' => esc_html__( 'Email send error.', 'noo' ),
					);
				}
			} else {
				$result = array(
					'success' => false,
					'message' => esc_html__( 'There\'s an unknown error. Please retry or contact Administrator.', 'noo' ),
				);
			}

			wp_send_json( $result );

			die;
		}
	}

	add_action( 'wp_ajax_noo_ajax_job_send_email', 'noo_ajax_job_send_email' );
	add_action( 'wp_ajax_nopriv_noo_ajax_job_send_email', 'noo_ajax_job_send_email' );

endif;