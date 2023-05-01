<?php

function noo_member_dashboard_ajax_application_datatable(){
	$start = $_REQUEST['start'];
	$length = $_REQUEST['length'];
	$filter_job = isset( $_REQUEST[ 'filter_job' ] ) ? absint( $_REQUEST[ 'filter_job' ] ) : 0;
	$filter_status = isset( $_REQUEST[ 'filter_status' ] ) ? $_REQUEST[ 'filter_status' ] : '';
	$paged = ( $start / $length ) + 1;
	$per_page = $length;
	$order = isset($_REQUEST['order']) ? reset($_REQUEST['order']) : false;
	$job_ids = isset( $_REQUEST['job_ids'] ) ? explode(',', $_REQUEST['job_ids']) : array();
	$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : array();
	$args = array(
		'paged'    		  		=> $paged,
		'post_type'       		=> 'noo_application',
		'posts_per_page'  		=> $per_page,
		'post_parent__in' 		=> $job_ids,
		'ignore_sticky_posts'	=> true,
		'post_status'     		=> apply_filters('noo_member_manage_application_status', array(
			'publish',
			'pending',
			'rejected'
		))
	);
	
	if(is_array($order) && isset($order['column'])){
		$column = 'ID';
		$dir = $order['dir'] ? $order['dir'] : 'DESC';
		if($order['column'] == '6'){
			$column = 'date';
		}
		$orderby = $column;
		$args[ 'order' ] = $dir;
	}
	
	if(is_array($search) && isset($search['value'])){
		$args['s'] = $search['value'];
	}
	
	if(!empty($filter_status)){
		$args[ 'post_status' ] = array( $filter_status );
	}
	if ( ! empty( $filter_job ) && in_array( $filter_job, $job_ids ) ) {
		$args[ 'post_parent__in' ] = array( $filter_job );
	}
	$statuses     = Noo_Application::get_application_status();
	$r            = new WP_Query( $args );
	$data = array();
	if ( $r->have_posts() ) {
		while ( $r->have_posts() ): $r->the_post();
			global $post;
			
			$mesage_excerpt  = ! empty( $post->post_content ) ? wp_trim_words( $post->post_content, 10 ) : '';
			$mesage_excerpt  = ! empty( $mesage_excerpt ) ? $mesage_excerpt . __( '...', 'noo' ) : '';
			$candidate_email = noo_get_post_meta( $post->ID, '_candidate_email' );
			$phone_number    = noo_get_post_meta($post->ID,'phone_number');
			
			//Candidate
			$candidate_link = apply_filters( 'noo_application_candidate_link', '', $post->ID, $candidate_email );
			$candidate = '<span class="candidate-name">';
			if ( ! empty( $candidate_link ) ) {
				$candidate .= '<a href="' . $candidate_link . '">';
			}
			$candidate .= '<strong>'.get_the_title().'</strong>';
			if ( ! empty( $candidate_link ) ) {
				$candidate .= '</a>';
			}
			$candidate .= '<br/>';
            if(!empty($candidate_email)){
            	$candidate .= '<em>'.$candidate_email.'</em>';
            }
            $candidate .= '<br/>';
            if ( !empty($phone_number) ){
            	$candidate .= '<em class="phone-number">'.sprintf(__('Phone: %s','noo'),$phone_number).'</em>';
            }
            $candidate .= '</span>';
            
            //Job
            $parent_job = get_post( $post->post_parent );
            if ( $parent_job && $parent_job->post_type === 'noo_job' ) {
            	$job = '<a href="' . get_permalink( $parent_job->ID ) . '">' . $parent_job->post_title . '</a>';
            } elseif ( $job_applied_for = noo_get_post_meta( $post->ID, '_job_applied_for', true ) ) {
            	$job = esc_html( $job_applied_for );
            } else {
            	$job = '<span class="na">&ndash;</span>';
            }
            
            //Message
            $readmore_link = '<a href="#" data-application-id="' . esc_attr( $post->ID ) . '" class="member-manage-action view-employer-message" data-mode="1"></i><em class="text-primary">' . __( 'View Details', 'noo' ) . '</em></a>';
            $readmore_link = apply_filters( 'noo-manage-application-message-link', $readmore_link, $post->ID );
            ob_start();
            echo $readmore_link;
            do_action( 'after_read_more_link', $post->ID );
            $message = ob_get_clean();
            
            //Attachment
            $attachment_html = '';
            $fb_id  = get_post_meta( $post->ID, 'fb_candidate_id', true );
            $fb_url = ! empty( $fb_id ) ? 'https://facebook.com/' . $fb_id : '';
            if ( ! empty( $fb_url ) ){
            	$attachment_html .= '<a class="application-attachment"
										   data-application-id="<?php echo $post->ID; ?>"
										   href="<?php echo esc_url( $fb_url ); ?>" data-toggle="tooltip"
										   title="'.esc_attr__( 'Facebook profile url', 'noo' ).'"
										   target="_blank"><i class="fa fa-facebook"></i></a>';
            }
            $attachment = jm_correct_application_attachment( $post->ID );
            if ( ! empty( $attachment ) ) {
            	if ( is_string( $attachment ) && strpos( $attachment, 'linkedin' ) ) {
            		$attachment_html .= '<a class="application-attachment"
											  data-application-id="'.$post->ID.'"
											   href="'.esc_url( $attachment ).'" data-toggle="tooltip"
											   title="'.esc_attr__( 'LinkedIn profile', 'noo' ).'"
											   target="_blank"><i class="fa fa-linkedin"></i></a>';
            	}elseif(is_string( $attachment ) && strpos( $attachment, 'xing' )) {
            		$attachment_html .= '<a class="application-attachment"
                                               data-application-id="'.$post->ID.'"
                                                href="'.esc_url( $attachment ).'" data-toggle="tooltip"
                                               title="'.esc_attr__( 'Xing profile', 'noo' ).'"
                                               target="_blank"><i class="fa fa-xing" aria-hidden="true"></i></a>';
            	}else{
            		$attachment = ! is_array( $attachment ) ? array( $attachment ) : $attachment;
            		foreach ( $attachment as $atm ){
						$file_name = basename( $atm );
						$attachment_html .= '<a class="application-attachment"
												   data-application-id="'.$post->ID.'"  rel="nofollow"
												   href="'.esc_url( $atm ).'" data-toggle="tooltip"
												   title="'.esc_attr( $file_name ).'"><i class="fas fa-cloud-download-alt"></i></a>';						
					}
            	}
            	$attachment_html .= '<br/>';
            }
            
            $resume = noo_get_post_meta( $post->ID, '_resume', '' );
            if ( ! empty( $resume ) ){
            	$resume_link = add_query_arg( 'application_id', $post->ID, get_permalink( $resume ) );
            	$attachment_html .='<a class="application-resume" data-application-id="'.$post->ID.'" href="'.esc_url( $resume_link ).'" data-toggle="tooltip" title="'.esc_attr__( 'Resume', 'noo' ).'"><i class="far fa-file-alt"></i></a>';
			}
			if ( empty( $attachment ) && empty( $resume ) && empty( $fb_url ) ) {
				$attachment_html .= '<span class="na">&ndash;</span>';
			}
			
			//Status
			$status       = $post->post_status;
			$status_class = $status;
			if ( isset( $statuses[ $status ] ) ) {
				$status = $statuses[ $status ];
			} else {
				$status       = __( 'Inactive', 'noo' );
				$status_class = 'inactive';
			}
			
			//Actions
			$action_html = '';
			if ( apply_filters('noo_manage_application_can_reject_approve', false)  ||  $post->post_status == 'pending' ){
				$approve_link = '<a href="#" class="member-manage-action approve-reject-action" data-hander="approve" data-application-id="' . $post->ID . '" data-toggle="tooltip" title="' . esc_attr__( 'Approve Application', 'noo' ) . '"><i class="far fa-check-square"></i></a>';
				$reject_link  = '<a href="#" class="member-manage-action approve-reject-action" data-hander="reject"  data-application-id="' . $post->ID . '" data-toggle="tooltip" title="' . esc_attr__( 'Reject Application', 'noo' ) . '"><i class="fas fa-ban"></i></a>';
			
				$action_html  .= apply_filters( 'noo-manage-application-approve-link', $approve_link, $post->ID );
				$action_html  .= apply_filters( 'noo-manage-application-reject-link', $reject_link, $post->ID );
			}elseif(apply_filters('noo_manage_application_show_reject_approve_no_link', true)){
				$action_html .='
				<a class="member-manage-action action-no-link" title='.esc_attr__( 'Approve Application', 'noo' ).'">
					<i class="far fa-check-square"></i>
				</a>
				<a class="member-manage-action action-no-link" title="'.esc_attr__( 'Reject Application', 'noo' ).'">
					<i class="fas fa-ban"></i>
				</a>';
			}
			
			$email_link = '<a href="mailto:' . $candidate_email . '" class="member-manage-action" data-toggle="tooltip" title="' . esc_attr__( 'Email Candidate', 'noo' ) . '"><i class="far fa-envelope"></i></a>';
			
			$action_html .= apply_filters( 'noo-manage-application-email-link', $email_link, $post->ID );
			ob_start();
			do_action( 'noo-manage-application-action', $post->ID );
			?>
			<a onclick="return confirm('<?php _e( 'Are you sure?', 'noo' ); ?>')" href="<?php echo wp_nonce_url( add_query_arg( array( 'action'=> 'delete','application_id' => $post->ID) ), 'application-manage-action' ); ?>" class="member-manage-action action-delete" data-toggle="tooltip" title="<?php esc_attr_e( 'Delete Application', 'noo' ) ?>"><i class="far fa-trash-alt"></i></a>
			<?php 
			$action_html .= ob_get_clean();
			
			
			$data[] = array(
				'id' => '<label class="noo-checkbox"><input type="checkbox" name="ids[]" value="'.$post->ID.'"><span class="noo-checkbox-label">&nbsp;</span></label>',
				'candidate' => $candidate,
				'avatar' => '<span class="candidate-avatar">'.noo_get_avatar( $candidate_email, 40 ).'</span>',
				'job_id' => $parent_job->ID,
				'job' => $job,
				'message' => $message,
				'attachment' => $attachment_html,
				'date' => '<span><i class="fa fa-calendar"></i> <em>'.date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ).'</em></span>',
				'date_sort' => date_i18n( 'Y/m/d', strtotime( $post->post_date ) ),
				'status' => '<span class="jm-status jm-status-'.esc_attr( $status_class ).'">'.esc_html( $status ).'</span>',
				'status_class' => $status_class,
				'action' => $action_html
			);
		endwhile;
	}
	
	wp_reset_postdata();
	
	wp_send_json(array(
		'draw' => $_REQUEST['draw'],
		'recordsTotal' => intval($r->found_posts),
		'recordsFiltered' => intval($r->found_posts),
		'data' => $data,
	));
}
add_action('wp_ajax_dashboard_ajax_application_datatable', 'noo_member_dashboard_ajax_application_datatable');