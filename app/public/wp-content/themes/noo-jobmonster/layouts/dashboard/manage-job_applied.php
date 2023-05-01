<?php
$user_id = get_current_user_id();
$viewed_messages = get_user_meta( $user_id, '_check_view_applied', true );
$viewed_messages = empty( $viewed_messages ) || ! is_array( $viewed_messages ) ? array() : $viewed_messages;

$user_email = wp_get_current_user()->user_email;
$all_statuses  = Noo_Application::get_application_status();

$args = array(
	'post_type'      => 'noo_application',
	'posts_per_page' => - 1,
	'post_status'    => array_keys(Noo_Application::get_application_status()),
	'meta_query'     => array(
		'relation' => 'OR',
		array(
			'key'   => '_candidate_email',
			'value' => $user_email,
		),
		array(
			'key'   => '_candidate_user_id',
			'value' => $user_id,
		),
	),
);

$r = new WP_Query( $args );
do_action( 'noo_member_manage_application_before' );
$title_text = $r->found_posts ? sprintf( _n( "You've applied for %s job", "You've applied for %s jobs", $r->found_posts, 'noo' ), $r->found_posts ) : __( "You haven't applied for any jobs", 'noo' );
$withdraw_status = apply_filters('noo_canidate_allow_withdraw_application_status', array('pending'));
?>
	<div class="member-manage">
		<h3><?php echo $title_text; ?></h3>
		<form method="post">
			<div class="member-manage-toolbar top-toolbar clearfix">
				<div class="bulk-actions pull-right clearfix">
					<strong><?php _e( 'Filter:', 'noo' ) ?></strong>
					<div class="form-control-flat" style="width: 200px;">
						<select name="status" id="job_applied_status">
							<option value=""><?php _e( 'All Status', 'noo' ) ?></option>
							<?php foreach ( $all_statuses as $key => $status ): ?>
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
			<div class="noo-dashboard-table">
				<table class="table noo-datatable" id="noo-table-job-applied">
					<thead>
					<tr>
						<th><?php _e( 'Applied job', 'noo' ) ?></th>
						<th class="hidden-xs hidden-sm"><?php _e( 'Applied Date', 'noo' ) ?></th>
                        <th class="hidden-xs"> <?php _e('Company','noo') ?></th>
						<th><?php _e( 'Employer\'s message', 'noo' ) ?></th>
						<th><?php _e( 'Attachment', 'noo' ) ?></th>
						<th class="text-center" data-orderable="false"><?php _e( 'Status', 'noo' ) ?></th>
						<th class="hidden-xs text-center"><?php _e( 'Action', 'noo' ) ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( $r->have_posts() ): ?>
						<?php
						while ( $r->have_posts() ): $r->the_post();
							global $post;
							$job = get_post( $post->post_parent );
							// don't display if there's no job.
							if ( empty( $job ) && $post->post_status != 'inactive' ) {
								$post->post_status = 'inactive';
								wp_update_post( array( 'ID' => $post->ID, 'post_status' => $post->post_status ) );
							}
							$company_id             = jm_get_job_company( $job );
                            $company_name           = noo_get_the_company_name($company_id) ;
                            $company_link           = get_the_permalink($company_id);
							$company_logo           = Noo_Company::get_company_logo( $company_id, '60' );
							$employer_message_title = noo_get_post_meta( $post->ID, '_employer_message_title', '' );
							$employer_message_body  = noo_get_post_meta( $post->ID, '_employer_message_body', '' );
							$mesage_excerpt         = empty( $employer_message_title ) ? wp_trim_words( $employer_message_body, 10 ) : $employer_message_title;
							$mesage_excerpt         = ! empty( $mesage_excerpt ) ? $mesage_excerpt . __( '...', 'noo' ) : '';
							$status                 = $post->post_status;
							$status_class           = $status;
							$statuses               = Noo_Application::get_application_status();
							if ( isset( $statuses[ $status ] ) ) {
								$status = $statuses[ $status ];
							} else {
								$status       = __( 'Inactive', 'noo' );
								$status_class = 'inactive';
							}
							?>
							<tr>
								<td>
									<div class="loop-item-wrap">
										<?php
										if ( $job && $job->post_type === 'noo_job' ) :
											?>
											<div class="loop-item-content">
												<strong><a href="<?php echo get_permalink( $job->ID ); ?>"><?php echo esc_html( $job->post_title ); ?></a></strong>
											</div>
											<?php
										else :
											echo( '<span class="na">&ndash;</span>' );
										endif;
										?>
									</div>
								</td>
								<td class="hidden-xs hidden-sm" data-sort="<?php echo date_i18n( 'Y/m/d', strtotime( $post->post_date ) )?>">
									<span><i class="fa fa-calendar-alt"></i> <em><?php echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ?></em></span>
								</td>
                                <td class="hidden-xs company ">
                                    <span>
                                        <strong><?php echo $company_name?></strong>
                                    </span>
                                </td>
								<td>
									<?php if ( $post->post_status == 'rejected' || $post->post_status == 'publish' ) : ?>
										<?php
										$tag           = ! in_array( $post->ID, $viewed_messages ) ? 'strong' : 'span';
										$readmore_link = '<a href="#" data-application-id="' . esc_attr( $post->ID ) . '" class="member-manage-action view-employer-message"><em class="text-primary">' . __( 'Continue reading', 'noo' ) . '</em></a>';
										$readmore_link = apply_filters( 'noo-manage-job-applied-message-link', $readmore_link, $post->ID );

										if ( ! in_array( $post->ID, $viewed_messages ) ) :
											?>
											<strong class="hidden-xs hidden-sm">
												<?php echo esc_html( $mesage_excerpt ); ?>
											</strong>&nbsp;<?php echo $readmore_link; ?>
										<?php else : ?>
											<span class="hidden-xs hidden-sm">
												<?php echo esc_html( $mesage_excerpt ); ?>
											</span>&nbsp;<?php echo $readmore_link; ?>
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td>
									<?php
									$attachment = jm_correct_application_attachment( $post->ID );
									if ( ! empty( $attachment ) ) :
										if ( is_string( $attachment ) && strpos( $attachment, 'linkedin' ) ) : ?>
											<a class="application-attachment"
											   data-application-id="<?php echo $post->ID; ?>"
											   href="<?php echo esc_url( $attachment ); ?>" data-toggle="tooltip"
											   title="<?php echo esc_attr__( 'LinkedIn profile', 'noo' ); ?>"
											   target="_blank"><i class="fa fa-linkedin"></i></a>
										<?php else :
											$attachment = ! is_array( $attachment ) ? array( $attachment ) : $attachment;
											foreach ( $attachment as $atm ) : ?>
												<?php $file_name = basename( $atm ); ?>
												<a class="application-attachment"
												   data-application-id="<?php echo $post->ID; ?>" rel="nofollow"
												   href="<?php echo esc_url( $atm ); ?>" data-toggle="tooltip"
												   title="<?php echo esc_attr( $file_name ) ?>"><i class="fa fa-cloud-download"></i></a>
											<?php endforeach;
										endif;
										echo '<br/>';
									endif;

									$resume = noo_get_post_meta( $post->ID, '_resume', '' );
									if ( ! empty( $resume ) ) :
										$resume_link = add_query_arg( 'application_id', $post->ID, get_permalink( $resume ) );
										?>
										<a class="application-resume" data-application-id="<?php echo $post->ID; ?>"
										   href="<?php echo esc_url( $resume_link ); ?>" data-toggle="tooltip"
										   title="<?php echo esc_attr__( 'Resume', 'noo' ); ?>"><i class="far fa-file-alt"></i></a>
									<?php endif;

									if ( empty( $attachment ) && empty( $resume ) ) {
										echo( '<span class="na">&ndash;</span>' );
									}
									?>
								</td>
								<td class="text-center" data-filter="<?php echo esc_attr($status_class); ?>">
									<span class="jm-status jm-status-<?php echo sanitize_html_class( $status_class ) ?>">
									<?php echo esc_html( $status ) ?>
									</span>
								</td>
								<td class="member-manage-actions hidden-xs text-center">
									<?php 
									do_action( 'noo-manage-job-applied-action', get_the_ID() );
									
									if ( in_array($post->post_status, $withdraw_status) ) : ?>
										<a href="<?php echo wp_nonce_url( add_query_arg( array(
											'action'         => 'withdraw',
											'application_id' => get_the_ID(),
                                            'resume_id'      => $resume,
										) ), 'job-applied-manage-action' ); ?>"
										   class="member-manage-action action-delete" data-toggle="tooltip"
										   title="<?php esc_attr_e( 'Withdraw', 'noo' ) ?>"><i class="fa fa-history"></i></a>
									<?php elseif ( $post->post_status == 'inactive' ) : ?>
										<a onclick="return confirm('<?php _e( 'Are you sure?', 'noo' ); ?>')"
										   href="<?php echo wp_nonce_url( add_query_arg( array(
											   'action'         => 'delete',
											   'application_id' => get_the_ID(),
                                               'resume_id'      => $resume,
										   ) ), 'job-applied-manage-action' ); ?>"
										   class="member-manage-action action-delete" data-toggle="tooltip"
										   title="<?php esc_attr_e( 'Delete Application', 'noo' ) ?>"><i class="far fa-trash-alt"></i></a>
									<?php endif; ?>
								</td>
							</tr>
						<?php endwhile; ?>
					<?php else: ?>
						<tr>
							<td colspan="7" class="text-center"><h3><?php _e( 'No Applications', 'noo' ) ?></h3></td>
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