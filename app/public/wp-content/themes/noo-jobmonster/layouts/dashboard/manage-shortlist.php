<?php
$archive_link = get_post_type_archive_link('noo_resume');
$can_shortlist_candidate = noo_can_shortlist_candidate();
if( !$can_shortlist_candidate ):?>
    <h3><?php echo __('The featured has disabled for the user. Please contact Administrator to enable this featured.', 'noo') ?></h3>
<?php else: ?>
    <?php
    $current_user = wp_get_current_user();
    $list_resume_shortlist = get_user_meta( $current_user->ID, 'list_resume_shortlist', true );

    if ( empty( $list_resume_shortlist ) || !is_array( $list_resume_shortlist ) ) {
        $list_resume_shortlist = array();
    }

    $args = array(
        'paged' => -1,
        'post_type' => 'noo_resume',
        'post__in' => array_merge($list_resume_shortlist, array(0)),
        'post_status' => 'publish'
    );

    $r = new WP_Query($args);
    ?>
    <div class="member-manage">
        <?php if( $r->found_posts ) : ?>
            <h3><?php echo sprintf( _n( "You've received %s shortlist", "You've received %s shortlist", $r->found_posts, 'noo'), $r->found_posts ); ?></h3>
        <?php else : ?>
            <h3><?php echo __("No shortlist found",'noo')?></h3>
        <?php endif; ?>
        <div class="noo-dashboard-table">
            <table class="table noo-datatable" id="noo-table-shortlist">
                <thead>
                <tr>
                    <th><?php _e('Candidate','noo')?></th>
                    <th><?php _e('Resume Title','noo')?></th>
                    <th class="hidden-xs"><?php _e('Category', 'noo'); ?></th>
                    <th class="hidden-xs"><?php _e('Location', 'noo'); ?></th>
                    <th class="hidden-xs hidden-sm"><?php _e('Date Modified','noo')?></th>
                    <th class="hidden-xs"><?php _e('Action','noo')?></th>
                </tr>
                </thead>
                <tbody>
                <?php if($r->have_posts()) : ?>
                    <?php while ($r->have_posts()): $r->the_post(); global $post; ?>
                        <?php
                        $job_category = noo_get_post_meta($post->ID, '_job_category', '');
                        $job_location = noo_get_post_meta($post->ID, '_job_location', '');
                        $mesage_excerpt = !empty($post->post_content) ? wp_trim_words( $post->post_content, 10 ) : '';
                        $mesage_excerpt = !empty($mesage_excerpt) ? $mesage_excerpt . __('...', 'noo') : '';
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
                            <td class="hidden-xs item-category-col"><em>
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
                            <td class="hidden-xs item-location">
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
                            <td class="hidden-xs hidden-sm item-date">
                                <span><i class="fa fa-calendar-alt"></i>&nbsp;<em><?php the_modified_date(); ?></em></span>
                            </td>
                            <td class="hidden-xs item-actions">
                                <a class="noo-shortlist" href="#" data-resume-id="<?php echo $post->ID ?>" data-user-id="<?php echo get_current_user_id(); ?>" data-type="text">
                                    <i class="far fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile;?>
                    <?php wp_reset_query(); ?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>