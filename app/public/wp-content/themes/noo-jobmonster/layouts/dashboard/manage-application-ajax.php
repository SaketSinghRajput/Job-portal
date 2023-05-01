<?php
$jobs_list  = jm_application_job_list();
$job_ids = array();
$job_filter_options = array();
foreach ($jobs_list as $job_item){
	$job_ids[] = $job_item->ID;	
	$job_filter_options[] = '<option value="'.esc_attr($job_item->ID).'">'.$job_item->post_title.'</option>';
}

$all_statuses  = Noo_Application::get_application_status();
unset( $all_statuses[ 'inactive' ] );

$bulk_actions = (array) apply_filters( 'noo_member_manage_application_bulk_actions', array(
	'approve' => __( 'Approve', 'noo' ),
	'reject'  => __( 'Reject', 'noo' ),
	'delete'  => __( 'Delete', 'noo' ),
) );

do_action( 'noo_member_manage_application_before' );
?>
	<div class="member-manage" data-title="">
		<h3 class="member-manage-application-title" data-title-text="<?php echo esc_attr__("You've received %s application",'noo') ?>" data-no-title-text="<?php echo esc_attr__("You've received no application",'noo') ?>"></h3>
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
				<table class="table noo-datatable" id="noo-table-app" data-use-ajax="yes" data-job-ids="<?php echo implode(',',$job_ids)?>">
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
						<tr>
							<td colspan="9" class="text-center">
								<div class="noo-dashboard-table-placeholder" style="height: 50px"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
	</div>
<?php
do_action( 'noo_member_manage_application_after' );
wp_reset_query();