<?php

$job_filter = isset( $_REQUEST[ 'job' ] ) ? absint( $_REQUEST[ 'job' ] ) : 0;
$jobs_list  = jm_application_job_list();
$job_ids = array();
$job_filter_options = array();
foreach ($jobs_list as $job_item){
	$job_ids[] = $job_item->ID;
	$job_filter_options[] = '<option value="'.esc_attr($job_item->ID).'" '.selected( $job_filter, $job_item->ID, false).'>'.$job_item->post_title.'</option>';
}

$statuses = $all_statuses  = Noo_Application::get_application_status();
unset( $all_statuses[ 'inactive' ] );

$per_page = 10;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1 ;

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
if ( ! empty( $job_filter ) && in_array( $job_filter, $job_ids ) ) {
	$args[ 'post_parent__in' ] = array( $job_filter );
}

$r  = new WP_Query( $args );

$bulk_actions = (array) apply_filters( 'noo_member_manage_application_bulk_actions', array(
	'approve' => __( 'Approve', 'noo' ),
	'reject'  => __( 'Reject', 'noo' ),
	'delete'  => __( 'Delete', 'noo' ),
) );

do_action( 'noo_member_manage_application_before' );

?>
	<div class="member-manage">
		<?php if ( $r->found_posts > 0 ) : ?>
			<h3><?php echo sprintf( _n( "You've received %s application", "You've received %s applications", $r->found_posts, 'noo' ), $r->found_posts ); ?></h3>
		<?php else : ?>
			<h3><?php echo __( "You've received no application", 'noo' ); ?></h3>
		<?php endif; ?>
		<form method="get">
			<div class="member-manage-toolbar top-toolbar clearfix">
				<div class="bulk-actions pull-left clearfix">
					<strong><?php _e( 'Action:', 'noo' ) ?></strong>
					<div class="form-control-flat">
						<select name="action">
							<option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'noo' ) ?></option>
							<?php foreach ( $bulk_actions as $action => $label ): ?>
								<option value="<?php echo esc_attr( $action ) ?>"><?php echo esc_html( $label ) ?></option>
							<?php endforeach; ?>
						</select>
						<i class="fa fa-caret-down"></i>
					</div>
					<button class="btn btn-primary bulk-manage-application-action"><?php _e( 'Go', 'noo' ) ?></button>
				</div>
				<div class="bulk-actions pull-right clearfix">
					<strong><?php _e( 'Filter:', 'noo' ) ?></strong>
					<div class="form-control-flat" style="width: 200px;">
						<select name="job" id="application_job">
							<option value="0"><?php _e( 'All Jobs', 'noo' ) ?></option>
							<?php echo implode("\n", $job_filter_options); ?>
						</select>
						<i class="fa fa-caret-down"></i>
					</div>
					<div class="form-control-flat" style="width: 200px;">
						<select name="status" id="application_status">
							<option value=""><?php _e( 'All Status', 'noo' ) ?></option>
							<?php 
							$filter_status = apply_filters('noo_member_manage_application_filter_status', $all_statuses);
							foreach ( $filter_status as $key => $status ): 
							?>
								<option value="<?php echo esc_attr( $key ) ?>"><?php echo $status; ?></option>
							<?php endforeach; ?>
						</select>
						<i class="fa fa-caret-down"></i>
					</div>
				</div>
			</div>
			<div style="display: none">
				<?php noo_form_nonce( 'application-manage-action' ) ?>
			</div>
			<div class="col-sm-6">
			</div>
			<div class="col-sm-6">
			</div>
			<div class="noo-dashboard-table">
				<table class="table noo-datatable" id="noo-table-app">
					<thead>
					<tr>
						<th class="check-column">
							<label class="noo-checkbox"><input type="checkbox"/><span class="noo-checkbox-label">&nbsp;</span></label>
						</th>
						<th class="appl-col-candidate"><?php _e( 'Candidate', 'noo' ) ?></th>
                        <th class="appl-col-job"><?php _e( 'Applied job', 'noo' ) ?></th>
						<th class="appl-col-message"><?php _e( 'Message', 'noo' ) ?></th>
						<th class="appl-col-attachment"><?php _e( 'Attachment', 'noo' ) ?></th>
						<th class="appl-col-date"><?php _e( 'Applied Date', 'noo' ) ?></th>
						<th class="appl-col-status text-center"><?php _e( 'Status', 'noo' ) ?></th>
						<th class="appl-col-action text-center"><?php _e( 'Action', 'noo' ) ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( $r->have_posts() ) : ?>
						<?php
						while ( $r->have_posts() ): $r->the_post();
							global $post;

							$mesage_excerpt  = ! empty( $post->post_content ) ? wp_trim_words( $post->post_content, 10 ) : '';
							$mesage_excerpt  = ! empty( $mesage_excerpt ) ? $mesage_excerpt . __( '...', 'noo' ) : '';
							$candidate_email = noo_get_post_meta( $post->ID, '_candidate_email' );
							$phone_number    = noo_get_post_meta($post->ID,'phone_number');

							$avatar = noo_get_avatar( $candidate_email, 40 );

							?>
							<tr>
								<td class="check-column">
									<label class="noo-checkbox"><input type="checkbox" name="ids[]" value="<?php the_ID() ?>">
										<span class="noo-checkbox-label">&nbsp;</span>
									</label>
								</td>
								<td>
									<?php $candidate_link = apply_filters( 'noo_application_candidate_link', '', $post->ID, $candidate_email ); ?>
									<span class="candidate-name"><?php if ( ! empty( $candidate_link ) ) {
											echo '<a href="' . $candidate_link . '">';
										} ?>
										<strong><?php echo get_the_title(); ?></strong><?php if ( ! empty( $candidate_link ) ) {
											echo '</a>';
										} ?>
                                       	 <br>
                                        <?php if(!empty($candidate_email)){
                                            echo '<em>'.$candidate_email.'</em>';
                                        } ?>
                                        <br>
                                        <?php if ( !empty($phone_number) ) : ?>
                                        <em class="phone-number">
                                       	 <?php echo sprintf(__('Phone: %s','noo'),$phone_number); ?>
                                    	</em>
                                        <?php endif; ?>
                                    </span>
								</td>
								<?php $parent_job = get_post( $post->post_parent ); ?>
								<td data-filter="<?php echo $parent_job->ID; ?>">
									<?php

									if ( $parent_job && $parent_job->post_type === 'noo_job' ) {
										echo( '<a href="' . get_permalink( $parent_job->ID ) . '">' . $parent_job->post_title . '</a>' );
									} elseif ( $parent_job = noo_get_post_meta( $post->ID, '_job_applied_for', true ) ) {
										echo esc_html( $parent_job );
									} else {
										echo( '<span class="na">&ndash;</span>' );
									}
									?>
								</td>
								<td>
									<?php
									$readmore_link = '<a href="#" data-application-id="' . esc_attr( $post->ID ) . '" class="member-manage-action view-employer-message" data-mode="1"></i><em class="text-primary">' . __( 'View Details', 'noo' ) . '</em></a>';
									$readmore_link = apply_filters( 'noo-manage-application-message-link', $readmore_link, $post->ID );
									?>
									<?php echo $readmore_link; ?>
									<?php do_action( 'after_read_more_link', $post->ID ); ?>
								</td>
								<td>
									<?php
									$fb_id  = get_post_meta( $post->ID, 'fb_candidate_id', true );
									$fb_url = ! empty( $fb_id ) ? 'https://facebook.com/' . $fb_id : '';
									if ( ! empty( $fb_url ) ):
										?>
										<a class="application-attachment"
										   data-application-id="<?php echo $post->ID; ?>"
										   href="<?php echo esc_url( $fb_url ); ?>" data-toggle="tooltip"
										   title="<?php echo esc_attr__( 'Facebook profile url', 'noo' ); ?>"
										   target="_blank"><i class="fa fa-facebook"></i></a>
									<?php endif; ?>
									<?php
									$attachment = jm_correct_application_attachment( $post->ID );
									if ( ! empty( $attachment ) ) :
										if ( is_string( $attachment ) && strpos( $attachment, 'linkedin' ) ) : ?>
											<a class="application-attachment"
											   data-application-id="<?php echo $post->ID; ?>"
											   href="<?php echo esc_url( $attachment ); ?>" data-toggle="tooltip"
											   title="<?php echo esc_attr__( 'LinkedIn profile', 'noo' ); ?>"
											   target="_blank"><i class="fa fa-linkedin"></i></a>
                                        <?php elseif(is_string( $attachment ) && strpos( $attachment, 'xing' )) : ?>
                                            <a class="application-attachment"
                                               data-application-id="<?php echo $post->ID; ?>"
                                               href="<?php echo esc_url( $attachment ); ?>" data-toggle="tooltip"
                                               title="<?php echo esc_attr__( 'Xing profile', 'noo' ); ?>"
                                               target="_blank"><i class="fa fa-xing" aria-hidden="true"></i></a>
										<?php else :
											$attachment = ! is_array( $attachment ) ? array( $attachment ) : $attachment;
											foreach ( $attachment as $atm ) : ?>
												<?php $file_name = basename( $atm ); ?>
												<a class="application-attachment"
												   data-application-id="<?php echo $post->ID; ?>"
												   href="<?php echo esc_url( $atm ); ?>" data-toggle="tooltip"
												   rel="nofollow"
												   title="<?php echo esc_attr( $file_name ) ?>"><i class="fas fa-cloud-download-alt"></i></a>
											<?php endforeach;
										endif;
										echo '<br/>';
									endif;

									$resume = noo_get_post_meta( $post->ID, '_resume', '' );
									if ( ! empty( $resume ) ) :
										$resume_link = add_query_arg( 'application_id', $post->ID, get_permalink( $resume ) );
										?>
										<a class="application-resume" data-application-id="<?php echo $post->ID; ?>" href="<?php echo esc_url( $resume_link ); ?>" data-toggle="tooltip" title="<?php echo esc_attr__( 'Resume', 'noo' ); ?>"><i class="far fa-file-alt"></i></a>
									<?php endif;

									if ( empty( $attachment ) && empty( $resume ) && empty( $fb_url ) ) {
										echo( '<span class="na">&ndash;</span>' );
									}
									?>
								</td>
								<td data-sort="<?php echo date_i18n( 'Y/m/d', strtotime( $post->post_date ) )?>">
									<span><i class="fa fa-calendar"></i> <em><?php echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ?></em></span>
								</td>

								<?php
								$status       = $post->post_status;
								$status_class = $status;
								if ( isset( $statuses[ $status ] ) ) {
									$status = $statuses[ $status ];
								} else {
									$status       = __( 'Inactive', 'noo' );
									$status_class = 'inactive';
								}
								?>

								<td class="text-center" data-filter="<?php echo $status_class; ?>">

									<span class="jm-status jm-status-<?php echo esc_attr( $status_class ) ?>">
										<?php echo esc_html( $status ) ?>
									</span>
								</td>
								<td class="member-manage-actions text-center">
									<?php
									if ( apply_filters('noo_manage_application_can_reject_approve', false)  || $post->post_status == 'pending') :
										$approve_link = '<a href="#" class="member-manage-action approve-reject-action" data-hander="approve" data-application-id="' . $post->ID . '" data-toggle="tooltip" title="' . esc_attr__( 'Approve Application', 'noo' ) . '"><i class="far fa-check-square"></i></a>';
										$reject_link  = '<a href="#" class="member-manage-action approve-reject-action" data-hander="reject"  data-application-id="' . $post->ID . '" data-toggle="tooltip" title="' . esc_attr__( 'Reject Application', 'noo' ) . '"><i class="fas fa-ban"></i></a>';

										$approve_link = apply_filters( 'noo-manage-application-approve-link', $approve_link, $post->ID );
										$reject_link  = apply_filters( 'noo-manage-application-reject-link', $reject_link, $post->ID );

										echo $approve_link;
										echo $reject_link;
									elseif(apply_filters('noo_manage_application_show_reject_approve_no_link', true)): ?>
										<a class="member-manage-action action-no-link" title="<?php esc_attr_e( 'Approve Application', 'noo' ) ?>">
											<i class="far fa-check-square"></i>
										</a>
										<a class="member-manage-action action-no-link" title="<?php esc_attr_e( 'Reject Application', 'noo' ) ?>">
											<i class="fas fa-ban"></i>
										</a>
									<?php endif; ?>
									<?php
									$email_link = '<a href="mailto:' . $candidate_email . '" class="member-manage-action" data-toggle="tooltip" title="' . esc_attr__( 'Email Candidate', 'noo' ) . '"><i class="far fa-envelope"></i></a>';

									$email_link = apply_filters( 'noo-manage-application-email-link', $email_link, $post->ID );
									echo $email_link;
									?>
									
									<?php do_action( 'noo-manage-application-action', $post->ID ); ?>
									
									<a onclick="return confirm('<?php _e( 'Are you sure?', 'noo' ); ?>')"
									   href="<?php echo wp_nonce_url( add_query_arg( array(
										   'action'         => 'delete',
										   'application_id' => $post->ID,
									   ) ), 'application-manage-action' ); ?>"
									   class="member-manage-action action-delete" data-toggle="tooltip"
									   title="<?php esc_attr_e( 'Delete Application', 'noo' ) ?>"><i class="far fa-trash-alt"></i></a>
								</td>

							</tr>
						<?php endwhile; ?>
					<?php else: ?>
						<tr>
							<td colspan="8" class="text-center">
								<h3><?php _e( 'No Applications', 'noo' ) ?></h3>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</form>
	</div>
<?php
do_action( 'noo_member_manage_application_after' );
wp_reset_query();