<?php
global $post;

$max_job_suggest = jm_get_job_setting('max_job_suggest', 5);
$current_user = wp_get_current_user();
$cats = noo_get_resume_tax('job_category');
$locations = noo_get_resume_tax('job_location');

$args = array(
    'post_type' => 'noo_job',
    'posts_per_page' => $max_job_suggest,
    'post_status' => array('publish'),
    'tax_query' => array(
        array(
            'taxonomy' => 'job_category',
            'field' => 'term_id',
            'terms' => $cats,
        ),
        array(
            'taxonomy' => 'job_location',
            'field' => 'term_id',
            'terms' => $locations,
        ),
    ),
);
$r = new WP_Query($args);

?>
<div class="member-manage">
    <?php if ($r->post_count) : ?>
        <h3><?php echo sprintf(_n("You have %s job suggest", "You have %s job suggest", $r->post_count, 'noo'), $r->post_count); ?></h3>
    <?php else : ?>
        <h3><?php echo __("No job suggest found", 'noo') ?></h3>
    <?php endif; ?>
    <div class="noo-dashboard-table">
        <table class="table noo-datatable" id="noo-table-shortlist">
            <thead>
            <tr>
                <th><?php _e('Company', 'noo') ?></th>
                <th><?php _e('Job Title', 'noo') ?></th>
                <th class="hidden-xs"><?php _e('Category', 'noo'); ?></th>
                <th class="hidden-xs"><?php _e('Location', 'noo'); ?></th>
                <th class="hidden-xs hidden-sm"><?php _e('Expiry Date', 'noo') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ($r->have_posts()) : ?>
                <?php while ($r->have_posts()): $r->the_post();
                    global $post;
                    ?>
                    <tr>
                        <?php
                        $company_id = jm_get_job_company($post);
                        $company_name = noo_get_the_company_name($company_id);
                        $company_url = get_the_permalink($company_id);
                        ?>
                        <td class="company-logo">
                            <a href="<?php echo $company_url; ?>">
                                <?php echo Noo_Company::get_company_logo($company_id, array(50, 50)); ?>
                            </a>
                        </td>
                        <td class="job-title">
                            <a href="<?php echo get_permalink() ?>">
                                <?php echo get_the_title() ?>
                            </a>
                            <p>
                                <span style="font-size: 12px;">
                                    <a href="<?php echo $company_url ?>">
                                        <?php echo sprintf(esc_html__('Post by: %s', 'noo'), $company_name); ?>
                                    </a>
                                </span>
                            </p>
                        </td>
                        <td class="job-category">
                            <span class="table-icon"><i class="fa fa-bars"></i></span>
                            <em><?php echo get_the_term_list(get_the_ID(), 'job_category', '', ', ') ?></em>
                        </td>
                        <td class="job-location">
                            <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                            <em><?php echo get_the_term_list(get_the_ID(), 'job_location', '', ', ') ?></em>
                        </td>
                        <td class="job-date">
                            <?php
                            $closing = noo_get_post_meta($post->ID, '_closing');
                            $closing = !is_numeric($closing) ? strtotime($closing) : $closing;
                            $closing = !empty($closing) ? date_i18n(get_option('date_format'), $closing) : '';
                            if (!empty($closing)) :
                                ?>
                                <span><i class="fa fa-calendar-alt"></i>&nbsp;<em><?php echo $closing; ?></em></span>
                            <?php else : ?>
                                <span class="text-center"><?php echo __('Equal to expired date', 'noo'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

