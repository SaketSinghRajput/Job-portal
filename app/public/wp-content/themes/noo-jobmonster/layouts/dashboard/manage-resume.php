<?php
$archive_link = get_post_type_archive_link('noo_resume');
$args = array(
    'post_type' => 'noo_resume',
    'posts_per_page' => -1,
    'post_status' => array('publish', 'pending', 'pending_payment'),
    'author' => get_current_user_id(),
);

$r = new WP_Query($args);

do_action('noo_member_manage_resume_before');

$viewable_resume_enabled = jm_viewable_resume_enabled();
$remain_refresh_resume = noo_get_resume_refresh_remain();
$remain_featured_resume = jm_get_feature_resume_remain();
$can_set_featured_resume = jm_can_set_feature_resume();
$enable_resume_feature = jm_get_resume_setting('enable_feature_resume');

?>
    <div class="member-manage">
        <?php if ($r->have_posts()): ?>
            <h3><?php echo sprintf(_n("You've saved %s resume", "You've saved %s resumes", $r->found_posts, 'noo'), '<span class="text-primary">' . $r->found_posts . '</span>'); ?></h3>
            <?php if ($viewable_resume_enabled) :
                $max_viewable_resumes = absint(jm_get_resume_setting('max_viewable_resumes', 1));
                $viewable_resumes = absint(Noo_Resume::count_viewable_resumes(get_current_user_id()));
                $disable_set_viewable = $viewable_resumes >= $max_viewable_resumes;

                $note_text = '';
                if ($max_viewable_resumes < 1) {
                    $note_text = __('No resume is publicly viewable/searchable.', 'noo');
                } else {
                    $note_text = sprintf(_n('Only %d resume is publicly viewable/searchable.', 'Only %d resumes are publicly viewable/searchable.', $max_viewable_resumes, 'noo'), $max_viewable_resumes);
                }
                ?>
                <em><strong><?php _e('Note:', 'noo') ?></strong> <?php echo esc_html($note_text); ?></em><br/>
            <?php endif; ?>
            <?php if ($remain_featured_resume > 0) : ?>
                <em><?php echo sprintf(_n('You can set %d more resume to be featured. Featured Resumes cannot be reverted.', 'You can set %d resumes to be featured. Featured resume cannot be reverted.', $remain_featured_resume, 'noo'), $remain_featured_resume); ?></em>
            <?php endif; ?>
            <form method="post">
                <div style="display: none">
                    <?php noo_form_nonce('resume-manage-action') ?>
                </div>
                <div class="noo-dashboard-table">
                    <table class="table noo-datatable" id="noo-table-resume">
                        <thead>
                            <tr>
                                <th><?php _e('Resume Title', 'noo') ?></th>
                                <th class="hidden-xs"><?php _e('View', 'noo') ?></th>
                                <?php if ($viewable_resume_enabled) : ?>
                                    <th class="text-center"><?php _e('Viewable', 'noo'); ?></th>
                                <?php endif; ?>
                                <?php if($enable_resume_feature): ?>
                                <th class="hidden-xs"><?php _e('Featured?','noo') ?></th>
                                <?php endif; ?>
                                <th class="hidden-xs"><?php _e('Category', 'noo') ?></th>
                                <th class="hidden-xs hidden-sm"><?php _e('Location', 'noo') ?></th>
                                <th class="hidden-xs hidden-sm"><?php _e('Date Modified', 'noo') ?></th>
                                <th class="text-center"><?php _e('Action', 'noo') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($r->have_posts()): $r->the_post();
                            global $post;
                            $status = $status_class = $post->post_status;
                            $statuses = jm_get_resume_status();
                            $status_text = '';
                            if (isset($statuses[$status])) {
                                $status_text = $statuses[$status];
                            } else {
                                $status_text = __('Inactive', 'noo');
                                $status_class = 'inactive';
                            }
                            ?>
                            <tr>
                                <td class="title-col">
                                    <?php if ($status == 'publish') : ?>
                                        <a href="<?php the_permalink() ?>"><strong><?php the_title() ?></strong></a>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url(add_query_arg('resume_id', get_the_ID(), Noo_Member::get_endpoint_url('preview-resume'))); ?>"><strong><?php the_title() ?></strong></a>
                                        <p>
                                            <em class="jm-status-text-<?php echo $status_class; ?>"><?php echo $status_text; ?></em>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-xs text-center">
                                    <?php $views = noo_get_post_views($post->ID); ?>
                                    <p><?php echo $views; ?></p>
                                </td>
                                <?php if ($viewable_resume_enabled) : ?>
                                    <td class="text-center viewable-col">
                                        <?php
                                        $viewable = noo_get_post_meta($post->ID, '_viewable');

                                        if ('yes' === $viewable) {
                                            echo '<a href="' . wp_nonce_url(add_query_arg(array(
                                                    'action' => 'toggle_viewable',
                                                    'resume_id' => $post->ID,
                                                )), 'resume-manage-action') . '" class="noo-resume-viewable" data-toggle="tooltip" title="' . esc_attr__('Disable viewable', 'noo') . '"><i class="fa fa-eye text-primary"></i></a>';
                                        } else {
                                            echo($disable_set_viewable ? '<span class="noo-resume-viewable" not-viewable" data-toggle="tooltip" ><i class="fa fa-eye-slash"></i></span>' : '<a href="' . wp_nonce_url(add_query_arg(array(
                                                    'action' => 'toggle_viewable',
                                                    'resume_id' => $post->ID,
                                                )), 'resume-manage-action') . '" class="noo-resume-viewable not-viewable" data-toggle="tooltip" title="' . esc_attr__('Set Viewable', 'noo') . '"><i class="fa fa-eye-slash"></i></a>');
                                        }
                                        ?>
                                    </td>
                                <?php endif; ?>
                                <?php if($enable_resume_feature): ?>
                                <td class="hidden-xs ">
                                    <?php
                                    $featured = noo_get_post_meta($post->ID, '_featured');
                                    if (empty($featured)) {
                                        // Update old data
                                        update_post_meta($post->ID, '_featured', 'no');
                                    }
                                    if ('yes' === $featured) :
                                        echo '<span class="noo-job-feature" data-toggle="tooltip" title="' . esc_attr__('Featured', 'noo') . '"><i class="fa fa-star"></i></span>';
                                    elseif ($can_set_featured_resume) :
                                        ?>
                                        <a href="<?php echo wp_nonce_url(add_query_arg(array(
                                            'action' => 'featured',
                                            'resume_id' => $post->ID
                                        )), 'resume-manage-action') ?>">
                                            <span class="noo-job-feature not-featured" data-toggle="tooltip"
                                                  title="<?php _e('Set Featured', 'noo'); ?>"><i
                                                        class="fa fa-star"></i></span>
                                        </a>
                                    <?php else : ?>
                                        <span class="noo-job-feature not-featured"
                                              title="<?php _e('Set Featured', 'noo'); ?>"><i
                                                    class="fa fa-star"></i></span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td class="hidden-xs category-col"><?php
                                    $job_category = noo_get_post_meta($post->ID, '_job_category', '');
                                    $job_categories = array();
                                    if (!empty($job_category)) :
                                        $job_category = noo_json_decode($job_category);
                                        ?>
                                        <span class="table-icon"><i class="fa fa-bars"></i></span>
                                        <?php
                                        $links = array();
                                        foreach ($job_category as $cat):
                                            $category_link = esc_url(add_query_arg(array('resume_category' => $cat), $archive_link));
                                            $term = get_term_by('id', $cat, 'job_category');
                                            $cat_name =(!empty($term)) ? $term->name : '';
                                            $links[] = '&nbsp;<a class="resume-category"  href="' . $category_link . '" >' . $cat_name . '</a>';
                                            ?>
                                        <?php endforeach; ?>
                                        <?php
                                        echo '<em>' . join(',', $links) . '</em>';
                                        ?>

                                    <?php endif; ?>
                                </td>
                                <td class="hidden-xs hidden-sm location-col">
                                    <?php
                                    $job_location = noo_get_post_meta($post->ID, '_job_location', '');
                                    if (!empty($job_location)) :
                                        $job_location = noo_json_decode($job_location);
                                        ?>
                                        <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                                        <?php
                                        $links = array();
                                        foreach ($job_location as $loc):
                                            $location_link = esc_url(add_query_arg(array('_job_location' => $loc), $archive_link));
                                            $term = get_term_by('id', $loc, 'job_location');
                                            $loc_name =(!empty($term))? $term->name : '';
                                            $links[] = '&nbsp;<a class="resume-location"  href="' . $location_link . '" >' . $loc_name . '</a>';

                                            ?>

                                        <?php endforeach; ?>
                                        <?php
                                        echo '<em>' . join(',', $links) . '</em>';
                                        ?>
                                    <?php endif; ?>
                                </td>
                                <td class="hidden-xs hidden-sm date-col"><span><i
                                                class="fa fa-calendar-alt"></i>&nbsp;<em><?php the_modified_date(); ?></em></span>
                                </td>
                                <td class="member-manage-actions text-center">
                                    <?php if (jm_get_resume_setting('enable_refresh_resume')): ?>
                                        <span class="btn-refresh-resume" data-id="<?php echo get_the_ID(); ?>"
                                              data-toggle="tooltip"
                                              title="<?php echo sprintf(esc_html__('Remain %s refresh time.', 'noo'), $remain_refresh_resume); ?>">
                                            <i class="fas fa-sync-alt"></i>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (jm_get_resume_setting('enable_clone_resume')): ?>
                                        <span class="btn-clone-resume" data-id="<?php echo get_the_ID(); ?>"
                                              data-toggle="tooltip"
                                              title="<?php esc_attr_e('Clone Resume', 'noo') ?>">
                                            <i class="fa fa-clone"></i>
                                        </span>
                                    <?php endif; ?>
                                    <a href="<?php echo Noo_Member::get_edit_resume_url($post->ID) ?>"
                                       class="member-manage-action" data-toggle="tooltip"
                                       title="<?php esc_attr_e('Edit Resume', 'noo') ?>"><i class="fas fa-pencil-alt"></i></a>
                                    <?php if ((bool)jm_get_resume_setting('enable_print_resume', '1')) : ?>
                                        <a data-resume="<?php echo esc_attr($post->ID); ?>"
                                           data-total-review="<?php echo (noo_get_total_review($post->ID)) ?>"
                                           class=" btn-print-resume print-resume noo-icon" href="#"
                                           title="<?php echo esc_attr__('Print', 'noo'); ?>">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a onclick="return confirm('<?php _e('Are you sure?', 'noo'); ?>')"
                                       href="<?php echo wp_nonce_url(add_query_arg(array(
                                           'action' => 'delete',
                                           'resume_id' => $post->ID,
                                       )), 'resume-manage-action'); ?>" class="member-manage-action action-delete"
                                       data-toggle="tooltip" title="<?php esc_attr_e('Delete Resume', 'noo') ?>"><i class="far fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php else: ?>
            <h4><?php echo __("You have no resume, why don't you start posting one.", 'noo') ?></h4>
            <p>
                <a href="<?php echo Noo_Member::get_post_resume_url(); ?>"
                   class="btn btn-primary"><?php echo jm_get_button_text('resume'); ?></a>
            </p>
        <?php endif; ?>
    </div>
<?php
do_action('noo_member_manage_resume_after');
wp_reset_query();