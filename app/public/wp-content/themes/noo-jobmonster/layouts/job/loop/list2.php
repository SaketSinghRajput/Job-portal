<?php if ($wp_query->have_posts()): ?>

    <div class="posts-loop-content row noo-job-list2">
        <?php ?>
        <?php do_action('job_list_before', $loop_args, $wp_query); ?>

        <?php while ($wp_query->have_posts()) : $wp_query->the_post();

            global $post;

            $company_id = jm_get_job_company($post);

            $company_name = !empty($company_id) ? get_the_title($company_id) : '';
            $company_link = !empty($company_id) ? get_the_permalink($company_id) : '';
            $company_logo = !empty($company_id) ? Noo_Company::get_company_logo($company_id, 'company-logo-square') : '';

            $date_pub = get_the_time('U', get_the_ID());
            $pub_ago = noo_relative_time($date_pub);

            $list_job_meta['show_company'] = false;

            $is_feature = ('yes' == noo_get_post_meta(get_the_ID(), '_featured', '')) ? 'featured-job' : '';

            ?>
            <?php do_action('job_list_single_before', $loop_args, $wp_query); ?>

                <div class="noo-job-item noo_job <?php echo esc_attr($is_feature); ?>">
                    <div class="job-info">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="item-excerpt">
                         	<?php echo get_the_excerpt(); ?>
                         </div>
                    </div>

                    <?php if (noo_get_option('noo_jobs_show_quick_view', 1)) : ?>

                        <div class="show-quick-view">
                            <a title="<?php _e('Quick view', 'noo'); ?>" href="#" class="btn-quick-view btn-quick-view-popup" data-id="<?php the_ID(); ?>" data-security="<?php echo wp_create_nonce('job-quick-action'); ?>"></a>
                        </div>

                    <?php endif; ?>
                </div>

            <?php do_action('job_list_single_after', $loop_args, $wp_query); ?>

        <?php endwhile; ?>
        <?php do_action('job_list_after', $loop_args, $wp_query); ?>
    </div>


<?php else: ?>
    <div class="jobs posts-loop">
        <?php
        if ($no_content == 'text' || empty($no_content)) {
            noo_get_layout('no-content');
        } elseif ($no_content != 'none') {
            echo '<h3>' . $no_content . '</h3>';
        }
        ?>
    </div>
<?php endif; ?>

