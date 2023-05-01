<?php
global $wp_query;
$google_ads = noo_get_option('noo_job_google_ads');
$google_position = noo_get_option('noo_job_google_ads_position', 'top');
if ($wp_query->have_posts()):
    if (empty($title)) {
        if (is_search() || $title_type == 'job_count') {
        	$title = sprintf(_n('We found %s available job for you', 'We found %s available jobs for you', $wp_query->found_posts, 'noo'), '<span class="text-primary">' . number_format_i18n( $wp_query->found_posts ) . '</span>');
        }
    }
    ?>
    <?php if (!$ajax_item || $ajax_item == null)://ajax item
	    $id_scroll = uniqid('scroll');
	    $attributes = 'id="' . $id_scroll . '" ' . 'class="jobs posts-loop ' . $class . '"' . (!empty($paginate) ? ' data-paginate="' . esc_attr($paginate) . '"' : '');
	    ?>
	    <div <?php echo $attributes; ?>>
	
	    <?php if (!is_post_type_archive('noo_job') or !is_tag(jm_get_job_taxonomies())): ?>
		    <?php if (!empty($title)): ?>
		        <div class="posts-loop-title<?php if (is_singular('noo_job')) echo ' single_jobs' ?>">
		            <h3><?php echo $title ?></h3>
		        </div>
		    <?php endif; ?>
	
		<?php endif; ?>
		<div class="posts-loop-content noo-job-list-row">
		    <div class="<?php echo esc_attr($paginate) ?>-wrap">
	
	<?php endif;//ajax item ?>
	
    <div class="result-filter-wraper mb30">
        <div class="value-filter-selected b-shadow">
            <div class="inner">
                <ul class="results-filter">
                    <?php  jm_url_job_filter_selected($_GET,$_SERVER['REQUEST_URI']); ?>
                </ul>
                <a class="filter-clear-all" href="<?php echo get_post_type_archive_link('noo_job') ?>"><?php esc_html_e('Clear All', 'noo'); ?></a>
            </div>
        </div>
    </div>
    <?php do_action('job_list_before', $loop_args, $wp_query); ?>
    <?php if (empty($class)): ?>
    <?php if (!empty($google_ads) && $google_position == 'top') {
        echo $google_ads;
    } ?>
<?php endif; ?>
    <?php 
    
    while ($wp_query->have_posts()) : $wp_query->the_post();
    global $post;
    
    $logo_company = '';
    $company_id = jm_get_job_company($post);
    $compary_title = get_the_title($company_id);
    if (!empty($company_id)) {
        if (noo_get_option('noo_jobs_show_company_logo', true)) {
            // use size "thumbnail" instead of "company-logo"
            $logo_company = Noo_Company::get_company_logo($company_id, 'company-logo-square', $compary_title);
        }
    }
    
    do_action('job_list_single_before', $loop_args, $wp_query);
    
    $data_marker = 'data-marker="'.esc_attr(json_encode(jm_get_marker_job_data($post->ID))).'"'; ?>
    
    <article <?php post_class($item_class); ?> data-url="<?php the_permalink(); ?>" <?php echo (string)($data_marker); ?>>
        <a class="job-details-link" href="<?php the_permalink(); ?>"></a>
        <div class="loop-item-wrap <?php echo $display_style; ?>">
            <?php if (!empty($logo_company)) : ?>
                <div class="item-featured">
                    <a href="<?php the_permalink() ?>">
                        <?php echo $logo_company; ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="loop-item-content"<?php echo $show_view_more == 'yes' ? ' style="width: 73%;float: left; padding-left:25px"' : ''; ?>>
                <h3 class="loop-item-title">
                    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permanent link to: "%s"', 'noo'), the_title_attribute('echo=0'))); ?>"><?php the_title(); ?></a>
                </h3>
                <?php jm_the_job_meta($list_job_meta, $post); ?>
                <div class="job-tools">

                    <?php if (noo_get_option('noo_jobs_show_send_to_friend', 1)) : ?>
                        <span class="noo-icon-tool noo-tool-email-job" data-id="<?php echo get_the_ID(); ?>"
                          data-title="<?php echo get_the_title(); ?>" data-url="<?php the_permalink(); ?>">
							<i class="fa fa-envelope"></i>
							<span class="noo-tool-label"><?php echo esc_html__('Send to friend', 'noo'); ?></span>
						</span>

                    <?php endif; ?>

                    <?php if (noo_get_option('noo_jobs_show_bookmark', 1)) : ?>

                        <span class="noo-icon-tool noo-btn-bookmark <?php echo (jm_is_job_bookmarked(0, get_the_ID())) ? 'bookmarked' : ''; ?>" data-job-id="<?php echo esc_attr(get_the_ID()); ?>" data-action="noo_bookmark_job" data-security="<?php echo wp_create_nonce('noo-bookmark-job'); ?>">
    							<i class="fa fa-heart"></i>
    							<span class="noo-tool-label"><?php echo (jm_is_job_bookmarked(0, get_the_ID())) ? esc_html__('Saved', 'noo') : esc_html__('Save', 'noo'); ?></span>
    						</span>

                    <?php endif; ?>
                    <?php if (noo_get_option('noo_jobs_show_share', 1)) : ?>

                        <span class="noo-icon-tool noo-tool-share">
    							<i class="fa fa-share-alt"></i>
    							<span class="noo-tool-label"><?php echo esc_html__('Share', 'noo'); ?></span>
                            <?php noo_share_social($post->ID); ?>
    					</span>

                    <?php endif; ?>
                    <?php do_action('noo_jobs_tools')?>
                </div>
            </div>
            <?php if ($show_view_more == 'yes') : ?>
                <div class="show-view-more">
                    <a class="btn btn-primary" href="<?php echo get_permalink($post->ID) ?>">
                        <?php _e('View more', 'noo') ?>
                    </a>
                    <?php
                    $date_pub = get_the_time('U', get_the_ID());
                    $pub_ago = noo_relative_time($date_pub);
                    ?>
                    <span class="job-date-ago"><?php echo sprintf(__("%s ago", 'noo'), $pub_ago); ?></span>
                </div>
            <?php endif; ?>

            <?php if (noo_get_option('noo_jobs_show_quick_view', 1)) : ?>

                <div class="show-quick-view">
                    <a title="<?php _e('Quick view', 'noo'); ?>" href="#" class="btn-quick-view btn-quick-view-popup" data-id="<?php the_ID(); ?>"  data-security="<?php echo wp_create_nonce('job-quick-action'); ?>"></a>
                </div>

            <?php endif; ?>

        </div>

        <?php do_action('job_loop_item', get_the_ID()); ?>

    </article>
    <?php do_action('job_list_single_after', $loop_args, $wp_query); ?>

<?php endwhile; ?>
    <?php do_action('job_list_after', $loop_args, $wp_query); ?>

    <?php if (!$ajax_item)://ajax item?>
    </div>
    </div>
    <?php if (empty($class)): ?>
        <?php if (!empty($google_ads) && $google_position == 'bottom') {
            echo $google_ads;
        } ?>
    <?php endif; ?>
    <?php if ($paginate == 'loadmore' && 1 < $wp_query->max_num_pages): ?>
        <div class="loadmore-action">
            <a href="#" class="btn btn-default btn-block btn-loadmore"
               title="<?php _e('Load More', 'noo') ?>"><?php _e('Load More', 'noo') ?></a>
            <div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    <?php endif; ?>
    <?php

    if ($paginate == 'nextajax') {
        if (1 < $wp_query->max_num_pages) {
            ?>
            <div class="pagination list-center"
                <?php
                if (is_array($paginate_data) && !empty($paginate_data)) :
                    foreach ($paginate_data as $key => $value) :
                        if (is_array($value)) {
                            echo ' data-' . $key . '="' . implode(",", $value) . '"';
                        } else {
                            echo ' data-' . $key . '="' . $value . '"';
                        }
                    endforeach;
                endif;
                ?>
                <?php echo(!empty($id_scroll) ? "data-scroll=\"{$id_scroll}\"" : ''); ?>
                 data-show="<?php echo esc_attr($featured) ?>"
                 data-show_view_more="<?php echo esc_attr($show_view_more); ?>"
                 data-current_page="1"
                 data-max_page="<?php echo absint($wp_query->max_num_pages) ?>"
                 data-action ="noo_nextajax">
                <a href="#" class="prev page-numbers disabled">
                    <i class="fa fa-chevron-left"></i>
                </a>

                <a href="#" class="next page-numbers">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </div>
            <?php
        }
    } else {
        if ($pagination) {
            $pagination_args = isset($pagination_args) ? $pagination_args : array();
            noo_pagination($pagination_args, $wp_query);
        }
    }
    ?>
    </div>
<?php endif;//ajax item
    ?>
<?php else: ?>
    <div class="result-filter-wraper mb30">
        <div class="value-filter-selected b-shadow">
            <div class="inner">
                <ul class="results-filter">
                    <?php  jm_url_job_filter_selected($_GET,$_SERVER['REQUEST_URI']); ?>
                </ul>
                <a class="filter-clear-all" href="<?php echo get_post_type_archive_link('noo_job') ?>"><?php esc_html_e('Clear All', 'noo'); ?></a>
            </div>
        </div>
    </div>
    <?php do_action('job_list_before', $loop_args, $wp_query); ?>
    <?php if(!$related_job): ?>
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
<?php endif; ?>