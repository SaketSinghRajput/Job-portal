<?php
$current_user = wp_get_current_user();

$status_filter = isset($_REQUEST['status']) ? esc_attr($_REQUEST['status']) : '';

$all_statuses = jm_get_job_status();

$job_need_approve = jm_get_job_setting('job_approve', '') == 'yes';
if (!$job_need_approve) {
    unset($all_statuses['pending']);
}
if (!jm_is_woo_job_posting()) {
    unset($all_statuses['pending_payment']);
}
$r = jm_user_job_query($current_user->ID, true, (!empty($status_filter) ? array($status_filter) : array()), -1);

$package_data = jm_get_job_posting_info();
$remain_featured_job = jm_get_feature_job_remain();
$can_set_featured_job = jm_can_set_feature_job();

$remain_refresh_job = noo_get_job_refresh_remain();

$show_clone_button = apply_filters('noo_member_manage_job_show_clone_button', true);

$bulk_actions = (array) apply_filters('noo_member_manage_job_bulk_actions', array(
    'publish' => __('Publish', 'noo'),
    'unpublish' => __('Unpublish', 'noo'),
    'delete' => __('Delete', 'noo')
));

$user_email = $current_user->user_email;

do_action('noo_member_manage_job_before');

?>
    <div class="member-manage">
        <?php ?>
        <?php if ($r->found_posts) : ?>
            <h3><?php echo sprintf(_n("We found %s job", "We found %s jobs", $r->found_posts, 'noo'), $r->found_posts); ?></h3>
        <?php else : ?>
            <h3><?php echo __("No jobs found", 'noo') ?></h3>
        <?php endif; ?>
        <em><strong><?php _e('Note:', 'noo') ?></strong> <?php _e('Expired listings will be removed from public view.', 'noo') ?>
        </em><br/>
        <?php if ($remain_featured_job > 0) : 
            if(strlen((string)$remain_featured_job) >= 7) $remain_featured_job = __('Unlimited','noo'); // check featured job unlimit -1 <-> 99999999 featured job times
            ?>
            <em><?php echo sprintf(_n('You can set %s more job to be featured. Featured jobs cannot be reverted.', 'You can set %s jobs to be featured. Featured jobs cannot be reverted.', $remain_featured_job, 'noo'), $remain_featured_job); ?></em>
        <?php endif; ?>
        <?php if ($remain_refresh_job > 0) : 
            if(strlen((string)$remain_refresh_job) >= 7) $remain_refresh_job = esc_html__('Unlimited','noo'); // check refresh job unlimit -1 <-> 99999999 refresh times
            ?>
            <em><?php echo sprintf(__('You have %s times to refresh for all the job.', 'noo'), $remain_refresh_job); ?></em>
        <?php endif; ?>
        <form method="get">
            <div class="member-manage-toolbar top-toolbar clearfix">
                <div class="bulk-actions pull-left clearfix">
                    <strong><?php _e('Action:', 'noo') ?></strong>
                    <div class="form-control-flat">
                        <select name="action">
                            <option selected="selected" value="-1"><?php _e('-Bulk Actions-', 'noo') ?></option>
                            <?php foreach ($bulk_actions as $action => $label): ?>
                                <option value="<?php echo esc_attr($action) ?>"><?php echo esc_html($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa fa-caret-down"></i>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php _e('Go', 'noo') ?></button>
                </div>
                <div class="bulk-actions pull-right clearfix">
                    <strong><?php _e('Filter:', 'noo') ?></strong>
                    <div class="form-control-flat" style="width: 200px;">
                        <select name="status" id="job_status">
                            <option value=""><?php _e('All Status', 'noo') ?></option>
                            <?php 
                            $filter_status = apply_filters('noo_member_manage_job_filter_status', $all_statuses);
                            foreach ($filter_status as $key => $status): ?>
                                <option value="<?php echo esc_attr($key) ?>" <?php selected($status_filter, $key) ?> ><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa fa-caret-down"></i>
                    </div>
                </div>
            </div>
            <div style="display: none">
                <?php noo_form_nonce('job-manage-action') ?>
            </div>
            <div class="noo-dashboard-table">
                <table class="table noo-datatable" id="noo-table-job">
                    <thead>
                    <tr>
                        <th class="check-column">
                            <label class="noo-checkbox"><input type="checkbox"/><span class="noo-checkbox-label">&nbsp;</span></label>
                        </th>
                        <th ><?php _e('Job Title', 'noo') ?></th>
                        <th class="hidden-xs"><?php _e('Views', 'noo') ?></th>
                        <th class="hidden-xs"><?php _e('Featured?', 'noo'); ?></th>
                        <th class="hidden-xs hidden-sm"><?php _e('Location', 'noo') ?></th>
                        <th class="hidden-xs hidden-sm"><?php _e('Closing Date', 'noo') ?></th>
                        <th class="hidden-xs  hidden-sm text-center"><?php _e('Apps', 'noo') ?></th>
                        <th class="hidden-xs text-center hidden-sm"><?php _e('Status', 'noo') ?></th>
                        <th data-priority="1" class=""><?php _e('Action', 'noo') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($r->have_posts()) : ?>
                        <?php while ($r->have_posts()): $r->the_post();
                            global $post;
                            $status = $status_class = jm_correct_job_status($post->ID, $post->post_status);
                            $statuses = jm_get_job_status();
                            $status_text = '';
                            if (isset($statuses[$status])) {
                                $status_text = $statuses[$status];
                            } else {
                                $status_text = __('Inactive', 'noo');
                                $status_class = 'inactive';
                            }
                            ?>
                            <tr>
                                <td class="check-column">
                                    <label class="noo-checkbox"><input type="checkbox" name="ids[]" value="<?php the_ID() ?>"><span class="noo-checkbox-label">&nbsp;</span></label>
                                </td>
                                <td>
                                    <?php if (in_array($status, array('inactive','pending','pending_payment','draft','expired'))) :
                                        $url = add_query_arg('job_id', get_the_ID(), get_the_permalink());
                                        ?>
                                        <a rel="nofollow" href="<?php echo esc_url($url); ?>">
                                            <strong><?php the_title() ?></strong>
                                        </a>
                                    <?php else : ?>
                                        <a rel="nofollow" href="<?php the_permalink() ?>"><strong><?php the_title() ?></strong></a>
                                        <?php $notify_email = get_post_meta(get_the_ID(), '_application_email', true);
                                        if (!empty($notify_email) && $notify_email != $user_email) : ?>
                                            <br/>
                                            <em class="hidden-xs"><?php echo sprintf(__('Notify email: %s', 'noo'), $notify_email); ?></em>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-xs text-center">
                                    <p><?php echo noo_get_post_views($post->ID); ?></p>
                                </td>
                                <td class="hidden-xs text-center">
                                    <?php
                                    $featured = noo_get_post_meta($post->ID, '_featured');
                                    if (empty($featured)) {
                                        // Update old data
                                        update_post_meta($post->ID, '_featured', 'no');
                                    }
                                    if ('yes' === $featured) :
                                        echo '<span class="noo-job-feature" data-toggle="tooltip" title="' . esc_attr__('Featured', 'noo') . '"><i class="fas fa-star"></i></span>';
                                    elseif ($can_set_featured_job) :
                                        ?>
                                        <a href="<?php echo wp_nonce_url(add_query_arg(array(
                                            'action' => 'featured',
                                            'job_id' => get_the_ID()
                                        )), 'job-manage-action') ?>">
                                            <span class="noo-job-feature not-featured" data-toggle="tooltip" title="<?php _e('Set Featured', 'noo'); ?>">
                                            	<i class="far fa-star"></i>
                                            </span>
                                        </a>
                                    <?php else : ?>
                                        <span class="noo-job-feature not-featured" title="<?php _e('Set Featured', 'noo'); ?>">
                                        	<i class="far fa-star"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-xs hidden-sm">
                                    <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                                    <em><?php echo get_the_term_list(get_the_ID(), 'job_location', '', ', ') ?></em>
                                </td>
                                <td class="job-manage-expires hidden-xs hidden-sm">
                                    <?php
                                    $closing = noo_get_post_meta($post->ID, '_closing');
                                    //Equal to expired date
                                    $use_expired_date = false;
                                    if(empty($closing)){
                                    	$use_expired_date = true;
                                    	$closing = noo_get_post_meta($post->ID, '_expires');
                                    }
                                    $closing = !is_numeric($closing) ? strtotime($closing) : $closing;
                                    $closing = !empty($closing) ? date_i18n(get_option('date_format'), $closing) : '';
                                    if (!empty($closing)) :
                                        ?>
                                        <span><i class="fa fa-calendar-alt"></i>&nbsp;<em><?php echo $closing; ?></em></span>
                                        <?php if($use_expired_date):?>
                                         <br />
                                         <small class="text-center"><?php echo __('Equal to expired date', 'noo'); ?></small>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="text-center"><?php echo __('Equal to expired date', 'noo'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="job-manage-app text-center hidden-xs hidden-sm">
										<span>
										<?php
                                        $applications = get_posts(array(
                                            'post_type' => 'noo_application',
                                            'posts_per_page' => -1,
                                            'post_parent' => $post->ID,
                                            'post_status' => array('publish', 'pending', 'rejected'),
                                            'suppress_filters' => false
                                        ));
                                        if (absint(count($applications)) > 0):
                                            $apply_job_url = add_query_arg(array(
                                                'action' => '-1',
                                                'job' => get_the_ID(),
                                            ), Noo_Member::get_endpoint_url('manage-application'));
                                            ?>
                                            <a href="<?php echo $apply_job_url; ?>"><?php echo absint(count($applications)); ?></a>
                                        <?php
                                        else:
                                            echo absint(count($applications));
                                        endif; ?>
										</span>
                                </td>
                                <td class="text-center hidden-xs hidden-sm" data-filter="<?php echo $status; ?>">
										<span class="jm-status jm-status-<?php echo esc_attr($status_class) ?>">
										<?php echo esc_html($status_text) ?>
										</span>
                                </td>
                                <td class="member-manage-actions">
                                    <?php
                                    $package = jm_get_job_posting_info( get_current_user_id() );
                                    $job_refresh = isset($package['job_refresh']) && !empty($package['job_refresh']) ? $package['job_refresh'] : 0;
                                    $remain_refresh_text = sprintf(esc_html__('Remain %s refresh time.', 'noo'), $remain_refresh_job);
                                    // check job refresh unlimit -1 <-> 99999999 refresh times
                                    if(strlen((string)$job_refresh) >= 7) $remain_refresh_text = esc_html__('Unlimited refresh time.','noo');
                                    ?>
                                    <?php if($job_refresh > 0): ?>
                                        <span class="btn-refresh-job" data-id="<?php echo get_the_ID(); ?>"
                                              data-refresh="<?php echo esc_attr($remain_refresh_text); ?>"
                                              data-toggle="tooltip"
                                              title="<?php echo esc_attr__($remain_refresh_text); ?>">
                                            <i class="fas fa-sync-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php if($show_clone_button):?>
                                    <span class="btn-clone-job" data-id="<?php echo get_the_ID(); ?>" data-toggle="tooltip" title="<?php esc_attr_e('Clone Job', 'noo') ?>">
                                          <i class="fa fa-clone"></i>
                                     </span>
                                     <?php endif; ?>
                                    <?php if (Noo_Member::can_change_job_state($post->ID, get_current_user_id())): ?>
                                        <?php if ($status == 'publish'): ?>
                                            <a href="<?php echo wp_nonce_url(add_query_arg(array(
                                                'action' => 'unpublish',
                                                'job_id' => get_the_ID()
                                            )), 'job-manage-action'); ?>" class="member-manage-action"
                                               data-toggle="tooltip"
                                               title="<?php esc_attr_e('Unpublish Job', 'noo') ?>"><i
                                                        class="fa fa-toggle-on"></i></a>
                                        <?php else: ?>
                                            <a href="<?php echo wp_nonce_url(add_query_arg(array(
                                                'action' => 'publish',
                                                'job_id' => get_the_ID()
                                            )), 'job-manage-action'); ?>" class="member-manage-action"
                                               data-toggle="tooltip"
                                               title="<?php esc_attr_e('Publish Job', 'noo') ?>"><i
                                                        class="fa fa-toggle-off"></i></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (Noo_Member::can_edit_job($post->ID, get_current_user_id())): ?>
                                        <a href="<?php echo Noo_Member::get_edit_job_url(get_the_ID()) ?>"
                                           class="member-manage-action" data-toggle="tooltip"
                                           title="<?php esc_attr_e('Edit Job', 'noo') ?>"><i class="fas fa-pencil-alt"></i></a>
                                    <?php endif; ?>
                                    <?php if($status == 'draft' || $status == 'expired'): ?>
                                        <a onclick="return confirm('<?php _e('Are you sure?', 'noo'); ?>')"
                                           href="<?php echo wp_nonce_url(add_query_arg(array(
                                               'action' => 're_post',
                                               'job_id' => get_the_ID()
                                           )), 'job-manage-action'); ?>" class="member-manage-action action-delete"
                                           data-toggle="tooltip" title="<?php esc_attr_e('Reposting', 'noo') ?>"><i class="fas fa-redo-alt"></i></a>
                                    <?php endif; ?>
                                    <?php if ($status == 'expired') : ?>
                                        <a href="#" class="member-manage-action" data-toggle="tooltip"
                                           title="<?php esc_attr_e('Expired Job', 'noo') ?>"><i class="far fa-clock"></i></a>
                                    <?php endif; ?>
                                    <a onclick="return confirm('<?php _e('Are you sure?', 'noo'); ?>')"
                                       href="<?php echo wp_nonce_url(add_query_arg(array(
                                           'action' => 'delete',
                                           'job_id' => get_the_ID()
                                       )), 'job-manage-action'); ?>" class="member-manage-action action-delete"
                                       data-toggle="tooltip" title="<?php esc_attr_e('Delete Job', 'noo') ?>"><i class="far fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr >
                            <td colspan="9" ><a href="<?php echo Noo_Member::get_post_job_url(); ?>" class="btn btn-primary"><?php echo jm_get_button_text(); ?></a></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
<?php
do_action('noo_member_manage_job_after');
wp_reset_query();