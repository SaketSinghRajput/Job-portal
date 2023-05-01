<?php
// List;
wp_enqueue_style('noo-rating');
wp_enqueue_script('noo-rating');
wp_enqueue_script('noo-lightgallery');
wp_enqueue_style('noo-lightgallery');

global $wp_query;
$total = $wp_query->found_posts;
$paged = get_query_var('paged', 1);
$current = !empty($paged) ? $paged : 1;
$per_page = $wp_query->query_vars['posts_per_page'];

$display_type = 'list';


$settings_fields = get_theme_mod('noo_resume_list_fields', 'title,_job_location,_job_category');
$settings_fields = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;
$display_fields = array();
foreach ($settings_fields as $index => $resume_field) {
    if ($resume_field == 'title') {
        $field = array('name' => 'title', 'label' => __('Resume Title', 'noo'));
    } else {
        $field = jm_get_resume_field($resume_field);
    }
    if (!empty($field)) {
        $display_fields[] = $field;
    }
}


$params = $_REQUEST;

unset($params['action']);
unset($params['live-search-nonce']);
unset($params['_wp_http_referer']);
unset($params['_wpnonce']);
$main_url = get_post_type_archive_link('noo_resume');
$current_url = add_query_arg($params, $main_url);
$id_resume = uniqid('resume-id-');
$enable_block_company = jm_get_action_control('enable_block_company');

?>

<?php
if (empty($_POST['action'])) {
    echo '<div class="resumes posts-loop resume-' . $paginate . '" data-paginate="' . $paginate . '">';
}
?>
    <div class="noo-resumes-slider" id="<?php echo($id_resume); ?>">

        <?php if ($wp_query->have_posts()): ?>

            <ul class=" noo-resume-<?php echo esc_attr($display_type) ?>">

                <?php while ($wp_query->have_posts()): $wp_query->the_post();
                    global $post; ?>

                    <?php
                    if($enable_block_company=='enable' && Noo_Member::get_user_role(get_current_user_id())!=='administrator'){
                        $block_company_meta = get_post_meta($post->ID,'_block_company',true);
                        $block_company = noo_json_decode($block_company_meta);
                        $employer_id = get_current_user_id();
                        if(Noo_Member::is_employer($employer_id)){
                            $company_id = jm_get_employer_company($employer_id);
                            $company_id = (!empty($company_id)) ? $company_id : '0' ;
                            if(in_array($company_id,$block_company) && $company_id!='0'){
                                continue;
                            }
                        }
                    }

                    $total_review = noo_get_total_review($post->ID);
                    $total_review_point = noo_get_total_point_review_resume($post->ID);
                    $candidate_avatar = '';
                    $candidate_name = '';
                    if (!empty($post->post_author)) :
                        $candidate_avatar = noo_get_avatar($post->post_author, 70);
                        $candidate = get_user_by('id', $post->post_author);
                        $candidate_name = !empty($candidate) ? $candidate->display_name : '';
                        $candidate_link = esc_url(apply_filters('noo_resume_candidate_link', get_the_permalink(), $post->ID, $post->post_author));
                        $data_marker      = 'data-marker="'.esc_attr(json_encode(jm_get_marker_resume_data($post->ID))).'"';
                        ?>
                        <li class="noo-resume-item  col-md-12 <?php echo ('yes' == noo_get_post_meta($post->ID, '_featured', '')) ? 'featured-resume' : '' ?>">
                            <a class="resume-details-link" href="<?php the_permalink(); ?>"></a>
                            <div class="noo-resume-info">
                                <div class="item-featured">
                                    <a href="<?php echo $candidate_link; ?>">
                                        <?php echo $candidate_avatar; ?>
                                    </a>
                                </div>

                                <div class="item-content">
                                    <h5 class="item-author">
                                        <a href="<?php echo $candidate_link; ?>"
                                           title="<?php echo esc_html($candidate_name); ?>">
                                            <?php echo esc_html($candidate_name); ?>
                                        </a>
                                    </h5>
                                    <h4 class="item-title">
                                        <a href="<?php the_permalink() ?>" title="<?php echo get_the_title(); ?>">
                                            <?php echo get_the_title(); ?>
                                        </a>
                                    </h4>
                                    <div class="item-meta">
                                        <?php foreach ($display_fields as $index => $field) : ?>
                                            <?php if (!isset($field['name']) || empty($field['name'])) {
                                                continue;
                                            } ?>
                                            <?php if ($field['name'] !== 'title' && $field['name'] !== '_portfolio') : ?>
                                                <?php $value = jm_get_resume_field_value($post->ID, $field); ?>
                                                <?php if (empty($value)) continue; ?>
                                                <span class="<?php echo esc_attr($field['name']) ?>">
                                                        <?php

                                                        if (!empty($value)) {
                                                            $html = array();
                                                            $value = noo_convert_custom_field_value($field, $value);
                                                            //                                                    if ( $index <= 1 || count( $display_fields ) <= 1 )
                                                            if (count($display_fields) <= 1) {
                                                                if (is_array($value)) {
                                                                    $value = implode(', ', $value);
                                                                }
                                                                $html[] = $value;
                                                            } else {
                                                                $icon = isset($field['icon']) ? $field['icon'] : '';
                                                                $icon_class = str_replace("|", " ", $icon);

                                                                $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
                                                                $html[] = '<span class="resume-' . $field['name'] . '" style="display: inline-block;">';
                                                                $html[] = '<i class="' . $icon_class . '">';
                                                                $html[] = '</i>';
                                                                $html[] = '<em>';
                                                                $html[] = is_array($value) ? implode(', ', $value) : $value;
                                                                $html[] = '</em></span>';
                                                            }
                                                            echo implode("\n", $html);
                                                        }
                                                        ?>
                                                    </span>
                                            <?php endif; ?>
                                            <?php if($field['name']== '_portfolio'): ?>
                                                <?php
                                                $portfolio_arr = noo_get_post_meta($post->ID, "_portfolio", '');
                                                if(!empty($portfolio_arr)) :
                                                    if ( !is_array( $portfolio_arr ) ) {
                                                        $portfolio_arr = explode(',', $portfolio_arr);
                                                    }
                                                    ?>
                                                    <div class="resume-timeline row">
                                                        <div class="col-md-12 col-sm-12">
                                                            <div id="portfolio" class="portfolio row is-flex">
                                                                <?php
                                                                foreach ( $portfolio_arr as $image_id ) :
                                                                    if ( empty( $image_id ) )
                                                                        continue;

                                                                    $image = wp_get_attachment_image_src( $image_id, 'portfolio-image');
                                                                    $image_full = wp_get_attachment_image_src( $image_id, 'full');

                                                                    echo '<a class="col-md-4 col-sm-4 col-xs-6" href="' . $image_full[0] . '"><img src="' . esc_url( $image[0] ) . '" alt="*" /></a>';

                                                                endforeach;
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach;
                                        reset($display_fields); ?>
                                    </div>
                                </div>
                                <?php $can_shortlist_candidate = noo_can_shortlist_candidate() ?>
                                <?php if ($can_shortlist_candidate): ?>
                                    <?php if ('list' == $display_type) : ?>
                                        <div class="show-view-more">
                                            <a class="btn btn-primary noo-shortlist" href="#"
                                               data-resume-id="<?php echo esc_attr($post->ID) ?>"
                                               data-user-id="<?php echo get_current_user_id() ?>" data-type="text">
                                                <?php echo noo_shortlist_status($post->ID, get_current_user_id()) ?>
                                            </a>
                                            <div class="time-post">
                                                <?php echo sprintf(__("%s ago", 'noo'), human_time_diff(get_the_time('U'), current_time('timestamp'))); ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <a class="noo-shortlist" href="#"
                                           data-resume-id="<?php echo esc_attr($post->ID) ?>"
                                           data-user-id="<?php echo get_current_user_id() ?>" data-type="icon">
                                            <?php echo noo_shortlist_icon($post->ID, get_current_user_id()) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>
                <?php endwhile; ?>

            </ul>
            <script>
                jQuery(document).ready(function() {
                    lightGallery(document.getElementById('portfolio'), {
                        thumbnail:true
                    });
                });
            </script>
        <?php else: ?>
            <div class="resume posts-loop ">
                <?php
                if ($no_content == 'text' || empty($no_content)) {
                    noo_get_layout('no-content');
                } elseif ($no_content != 'none') {
                    echo '<h3>' . $no_content . '</h3>';
                }
                ?>
            </div>
        <?php endif; ?>

    </div>
    <?php if ($paginate == 'resume_nextajax') : ?>

        <?php if (1 < $wp_query->max_num_pages) :

            $paged = isset($_POST['page']) ? absint($_POST['page']) : 1; ?>
            <div class="pagination list-center"
                 data-job-category="<?php echo esc_attr($job_category); ?>"
                 data-job-location="<?php echo esc_attr($job_location); ?>"
                 data-orderby="<?php echo esc_attr($orderby); ?>"
                 data-order="<?php echo esc_attr($order); ?>"
                 data-posts-per-page="<?php echo absint($posts_per_page) ?>"
                 data-current-page="<?php echo absint($paged) ?>"
                 data-style="list"
                 data-max-page="<?php echo absint($wp_query->max_num_pages) ?>">
                <a href="#" class="prev page-numbers disabled">
                    <i class="fa fa-long-arrow-left"></i>
                </a>

                <a href="#" class="next page-numbers">
                    <i class="fa fa-long-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php
if (empty($_POST['action'])) {
    echo '</div>';
}
?>