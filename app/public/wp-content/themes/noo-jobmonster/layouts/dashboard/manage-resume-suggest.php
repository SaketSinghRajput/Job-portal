<?php
$ids = noo_get_resume_suggest_id();
$max_resume_suggest=jm_get_resume_setting('max_resume_suggest',5);
$archive_link = get_post_type_archive_link('noo_resume');

?>
<div class="member-manage">
    <?php if (empty($ids)) : ?>
        <h3><?php echo __("No resume suggest  found", 'noo') ?></h3>
    <?php else : 
        $arr_query = array(
            'post_type' => 'noo_resume',
            'posts_per_page' => $max_resume_suggest,
            'post_status' => 'publish',
            'post__in' => $ids,
        );
        $r = new WP_Query($arr_query);
        if($r->post_count){?>
            <h3><?php echo sprintf(_n("You have %s resume suggest", "You have %s resume suggest", $r->post_count, 'noo'), $r->post_count); ?></h3>
            <div class="noo-dashboard-table">
                <table class="table noo-datatable" id="noo-table-resume">
                    <div class="noo-dashboard-table">
                        <thead>
                        <tr>
                            <th><?php _e('Candidate', 'noo') ?></th>
                            <th><?php _e('Resume Title', 'noo') ?></th>
                            <th class="hidden-xs  hidden-sm"><?php _e('Category', 'noo') ?></th>
                            <th class="hidden-xs hidden-sm"><?php _e('Location', 'noo') ?></th>
                            <th class="hidden-xs hidden-sm"><?php _e('Date Modified', 'noo') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($r->have_posts()) : ?>
                            <?php while ($r->have_posts()): $r->the_post();
                                global $post;
                                $job_category = noo_get_post_meta($post->ID, '_job_category', '');
                                $job_location = noo_get_post_meta($post->ID, '_job_location', '');

                                $status = $status_class = $post->post_status;
                                $statuses = jm_get_resume_status();
                                $status_text = '';
                                if (isset($statuses[$status])) {
                                    $status_text = $statuses[$status];
                                } else {
                                    $status_text = __('Inactive', 'noo');
                                    $status_class = 'inactive';
                                }
                                $candidate_avatar = '';
                                $candidate_name = '';
                                $candidate_avatar = noo_get_avatar($post->post_author, 40);
                                $candidate = get_user_by('id', $post->post_author);
                                $candidate_name = !empty($candidate) ? $candidate->display_name : '';
                                $candidate_link = esc_url(apply_filters('noo_resume_candidate_link', get_the_permalink(), $post->ID, $post->post_author));
                                ?>
                                <tr>
                                    <td class="item-candidate">

                                         <span>
                                            <a href="<?php $candidate_link; ?>">
                                                <?php echo  $candidate_avatar;  ?>
                                        </span>
                                    </td>
                                    <td class="resume-title">
                                            <a href="<?php echo get_permalink( ) ?>">
                                                <?php echo get_the_title() ?>
                                            </a>
                                            <p>
                                                <span style="font-size: 12px;">
                                                    <a href="<?php echo $candidate_link; ?>">
                                                         <?php echo sprintf(esc_html__('Post by: %s', 'noo'), $candidate_name); ?>
                                                    </a>
                                                </span>
                                            </p>
                                    </td>
                                    <td class="hidden-xs category-col"><em>
                                            <?php
                                            $job_categories = array();
                                            if (!empty($job_category)) :
                                            $job_category = noo_json_decode($job_category);
                                            ?>
                                            <span class="table-icon"><i class="fa fa-bars"></i></span>
                                            <?php
                                            $links=array();
                                            foreach ($job_category as $cat):
                                                $category_link = esc_url(add_query_arg(array('resume_category' => $cat), $archive_link));
                                                $term = get_term_by('id', $cat, 'job_category');
                                                $cat_name =(!empty($term)) ? $term->name : '';
                                                $links[]='&nbsp;<a class="resume-category"  href="' . $category_link . '" >' . $cat_name . '</a>';
                                                ?>
                                            <?php endforeach; ?>
                                            <?php
                                            echo '<em>'.join(',',$links).'</em>';
                                            ?>

                                        <?php endif; ?>
                                    </td>
                                    <td class="hidden-xs hidden-sm location-col">
                                        <?php
                                        $job_locations = array();
                                        if (!empty($job_location)) :
                                            $job_location = noo_json_decode($job_location);
                                            ?>
                                            <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                                            <?php
                                            $links=array();
                                            foreach ($job_location as $loc):
                                                $location_link = esc_url(add_query_arg(array('_job_location' => $loc), $archive_link));
                                                $term = get_term_by('id', $loc, 'job_location');
                                                $loc_name =(!empty($term))? $term->name : '';
                                                $links[]='&nbsp;<a class="resume-location"  href="' . $location_link . '" >' . $loc_name . '</a>';

                                                ?>

                                            <?php endforeach; ?>
                                            <?php
                                            echo '<em>'.join(',',$links).'</em>';
                                            ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="hidden-xs hidden-sm date-col">
                                        <span><i class="fa fa-calendar-alt"></i>&nbsp;<em><?php the_modified_date(); ?></em></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php wp_reset_query(); ?>
                        <?php endif; ?>
                        </tbody>
                </table>
            </div>
        <?php }else{?>
            <h3><?php echo __("No resume suggest  found", 'noo') ?></h3>
        <?php }?>
        
    <?php endif; ?>    
</div>
