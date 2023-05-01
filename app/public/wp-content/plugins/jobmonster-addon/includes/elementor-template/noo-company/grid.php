<?php
global $noo_company_setting;
$filter_type = $noo_company_setting['alphabet_filter_type'] == '' ? 1 : $noo_company_setting['alphabet_filter_type'];
if ($filter_type === '1') {
    $letter_range = range(__('A', 'noo'), __('Z', 'noo'));
    $letter_range = apply_filters('noo_company_title_letter_range', $letter_range);
    $letter_range = array_unique($letter_range);
} else {
    $custom_letters = $noo_company_setting['custom_letters'];
    $custom_letters = preg_split('/\r\n|[\r\n]/', $custom_letters);
    $letter_range = apply_filters('noo_company_title_letter_range', $custom_letters);
    $letter_range = array_unique($letter_range);
}
$current_key = (isset($_GET['key'])) ? $_GET['key'] : '';
$main_url = get_post_type_archive_link('noo_company');
?>
<?php if ($show_filter): ?>
    <div class="company-letters">
        <?php
        if (!empty($archive) && $archive == 'yes') {
            $link = get_post_type_archive_link('noo_company');
        } else {
            $link = get_page_link();
        }

        ?>
        <a href="<?php echo $link; ?>"
           class="<?php echo ($current_key == '') ? 'selected' : ''; ?>"><?php _e('All', 'noo'); ?></a>
        <?php foreach ($letter_range as $letter) {
            $letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
            $class = ($current_key == $letter) ? 'selected' : '';
            echo '<a href="' . $link . '?key=' . $letter . '" class="' . $class . '">' . $letter . '</a>';
        } ?>

    </div>
<?php endif; ?>
<div class="company-list">
    <div class="row">
        <?php
        if (!isset($_GET['s'])) {
            $query = $wp_query;
        } else {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $post_per_page = noo_get_option('noo_companies_style_count');
            $args = array(
                'post_type' => 'noo_company',
                'post_status' => 'publish',
                's' => esc_html($_GET['s']),
                'paged' => $paged,
                'posts_per_page' => $post_per_page,
            );
            $args = jm_company_query_from_request($args, $_GET);
            $query = new WP_Query($args);
        }
        if ($query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                global $post; ?>
                <?php
                $company_name = $post->post_title;
                $count = noo_company_job_count($post->ID);

                $ft = ('yes' == noo_get_post_meta($post->ID, '_company_featured', '')) ? 'featured-company' : '';
                $total_review = noo_get_total_review($post->ID);
                ?>
                <div class="col-sm-4 company-list-item">
                    <div class="company-item company-inner <?php echo esc_attr($ft); ?>">
                        <div class="company-item-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo Noo_Company::get_company_logo($post->ID, array(150, 150)); ?>
                            </a>
                            <a class="btn btn-primary btn-company" href="<?php the_permalink(); ?>">
                                <?php echo __('View More', 'noo'); ?>
                            </a>
                        </div>
                        <div class="company-item-meta">
                            <a href="<?php the_permalink(); ?>">
                                <?php echo esc_html($company_name); ?>
                            </a>
                            <?php if (Noo_Company::review_is_enable()): ?>
                                <div class="total-review">
                                    <?php noo_box_rating(noo_get_total_point_review($post->ID), true) ?>
                                    <span><?php echo sprintf(esc_html__('(%s %s)', 'noo'), $total_review, ($total_review > 1 ? esc_html__('reviews', 'noo') : esc_html__('review', 'noo'))) ?></span>
                                </div>
                            <?php endif; ?>
                            <p>
                                <i class="fa fa-briefcase"></i><span
                                        class="job-count"><?php echo $count > 0 ? sprintf(_n('%s Job', '%s Jobs', $count, 'noo'), $count) : __('No Jobs', 'noo'); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile;
        } else {
            ?>
            <h3 class="text-center"><?php _e('Nothing Found', 'noo'); ?></h3>
            <?php
        } ?>
    </div>
    <?php
    if (1 < $query->max_num_pages) {
        noo_pagination(array(), $query);
    }
    ?>
</div>