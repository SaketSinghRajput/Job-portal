<?php
wp_enqueue_style('noo-rating');
wp_enqueue_script('noo-rating');

global $noo_company_setting;
$filter_type = (isset($noo_company_setting['alphabet_filter_type']) && $noo_company_setting['alphabet_filter_type'] != '') ? $noo_company_setting['alphabet_filter_type'] : 1;
$enable_rss = noo_get_option( 'noo_company_enable_rss', false );
if( $filter_type === '1' ) {
	$letter_range       = range(__('A', 'noo'), __('Z', 'noo'));
	$letter_range       = apply_filters('noo_company_title_letter_range', $letter_range);
	$letter_range       = array_unique($letter_range);
} else {
    $custom_letters = isset($noo_company_setting['custom_letters']) ? $noo_company_setting['custom_letters'] : '';
    $custom_letters = preg_split('/\r\n|[\r\n]/', $custom_letters);
	$letter_range   = apply_filters('noo_company_title_letter_range', $custom_letters );
	$letter_range   = array_unique($letter_range);
}

?>
<?php if ($style == 'slider') : ?>
	<?php $id = noo_vc_elements_id_increment(); ?>
    <div class="wpb_wrapper">
        <div class="noo-text-block">
			<?php if (!empty($title)) : ?>
                <h3 style="text-align: center;"> <strong><?php echo $title; ?>  </strong> </h3>
			<?php endif; ?>
            <p style="text-align: center;">
				<?php echo $featured_content; ?>
            </p>
        </div>
    </div>
	<?php
	$option['owl'] = 'yes';
	noo_caroufredsel_slider($wp_query, !empty($option) ? $option : array()); ?>
<?php elseif ($style == 'style2' || $style==''): ?>
    <?php global $wp_query;
    ?>
	<?php if (!empty($title)) : ?>
        <div class="form-title">
            <h3><?php echo($title); ?></h3>
        </div>
	<?php endif; ?>
	<?php
	$current_key = (isset($_GET['key'])) ? $_GET['key'] : '';
	$main_url    = get_post_type_archive_link('noo_company');
	$feed        = $main_url . 'feed';
	$feed_url    = add_query_arg('key', $current_key, $feed);
	?>
    <?php if($enable_rss):?>
        <div class="noo-feed-button">
            <a class="noo-type-btn" href="<?php echo $feed_url; ?>">
                <i class="fa fa-rss"></i>
            </a>
        </div>
    <?php endif;?>
    <div class="company-letters">
		<?php
		if (!empty($archive) && $archive == 'yes')
		{
			$link = get_post_type_archive_link('noo_company');
		}
		else
		{
			$link = get_page_link();
		}

		?>
        <a href="<?php echo $link; ?>"
           class="<?php echo ($current_key == '') ? 'selected' : ''; ?>"><?php _e('All', 'noo'); ?></a>
		<?php foreach ($letter_range as $letter)
		{
            $letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
			$class  = ($current_key == $letter) ? 'selected' : '';
			echo '<a href="' . $link . '?key=' . $letter . '" class="' . $class . '">' . $letter . '</a>';
		} ?>

    </div>
    <?php
    ?>
    <div class="company-list">
		<?php do_action('noo_list_company_before'); ?>
        <div class="row">
			<?php
            if(!isset($_GET['s'])){
                $query = $wp_query;
            }
            else{
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $post_per_page = noo_get_option('noo_companies_style_count');
                $args = array(
                    'post_type' 		=> 'noo_company',
                    'post_status' 		=> 'publish',
                    's' 				=> esc_html($_GET['s']),
                    'paged' 			=> $paged,
                    'posts_per_page' 	=> $post_per_page,
                );
                $args = jm_company_query_from_request($args, $_GET);
                $query = new WP_Query( $args);
            }
            if($query->have_posts()){
                while ($query->have_posts()) : $query->the_post();
                    global $post; 
                    
                    $company_name = noo_get_the_company_name($post);

                    $count = noo_company_job_count($post->ID);

                    $ft           = ('yes' == noo_get_post_meta($post->ID, '_company_featured', '')) ? 'featured-company' : '';
                    $total_review = noo_get_total_review($post->ID);
                    ?>
                    <div class="col-sm-4 company-list-item">
                        <div class="company-item company-inner <?php echo esc_attr($ft); ?>">
                            <div class="company-item-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo Noo_Company::get_company_logo($post->ID,array(150,150)); ?>
                                </a>
                                <a class="btn btn-primary btn-company" href="<?php the_permalink(); ?>">
                                    <?php echo __('View More', 'noo'); ?>
                                </a>
                            </div>
                            <div class="company-item-meta">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo $company_name; ?>
                                </a>
                                <?php if (Noo_Company::review_is_enable()): ?>
                                    <div class="total-review">
                                        <?php noo_box_rating(noo_get_total_point_review($post->ID), true) ?>
                                        <span><?php echo sprintf(esc_html__('(%s %s)', 'noo'), $total_review, ($total_review > 1 ? esc_html__('reviews', 'noo') : esc_html__('review', 'noo'))) ?></span>
                                    </div>
                                <?php endif; ?>
                                <p>
                                    <i class="fa fa-briefcase"></i><span class="job-count"><?php echo $count > 0 ? sprintf(_n('%s Job', '%s Jobs', $count, 'noo'), $count) : __('No Jobs', 'noo'); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            }else{
                noo_get_layout('no-content');
            }?>
        </div>
		<?php
        if(1 < $query->max_num_pages){
            noo_pagination( array(), $query);
        }
       ?>
		<?php do_action('noo_list_company_after'); ?>
    </div>
<?php elseif($style == 'style1') : ?>
	<?php
	global $wpdb;
	?>
	<?php if (!empty($title)) : ?>
        <div class="form-title">
            <h3><?php echo($title); ?></h3>
        </div>
	<?php endif; ?>
	<?php
	$main_url = get_post_type_archive_link('noo_company');
	$feed     = $main_url . 'feed';
	?>
    <?php if($enable_rss):?>
        <div class="noo-feed-button">
            <a class="noo-type-btn" href="<?php echo $feed; ?>">
                <i class="fa fa-rss"></i>
            </a>
        </div>
    <?php endif;?>
    <?php $not_like_str = ''; ?>
    <div class="company-letters">
        <a data-filter="*" href="#all" class="selected"><?php _e('All', 'noo'); ?></a>
		<?php foreach ($letter_range as $letter)
		{
			$not_like_str .= "AND post_title NOT LIKE '{$letter}%' ";
			$letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
			echo '<a data-filter=".filter-' . $letter . '" href="#' . $letter . '">' . $letter . '</a>';
		} ?>
    </div>
	<?php
    $show_company_no_job = (Noo_Company::get_setting( 'show_no_jobs', 1 )) ? 0 : 1;
    $start = 0;
	$limit_show = $noo_company_setting['number_company_show'] == "" ? 5 : $noo_company_setting['number_company_show'];
	$ajax_nonce = wp_create_nonce( "show_more_company" );
	$companies = [];
	foreach ($letter_range as $letter)
	{
		$letter             = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
		$company_query      = "SELECT DISTINCT p.ID, p.post_title, p2.total_company 
                                   FROM {$wpdb->prefix}posts as p 
                                   LEFT JOIN {$wpdb->postmeta} AS pm
                                   ON p.ID = pm.post_id AND pm.meta_key = '_noo_job_count'
                                   JOIN (SELECT COUNT(ID) as total_company FROM {$wpdb->prefix}posts WHERE post_type = 'noo_company' AND post_status = 'publish' AND post_title <> '' AND post_title LIKE '{$letter}%') p2
                                   WHERE p.post_type = 'noo_company' AND p.post_status = 'publish' AND pm.meta_value >= $show_company_no_job AND p.post_title <> '' AND p.post_title LIKE '{$letter}%'
                                   ORDER BY p.post_title ASC
                                   LIMIT $start, $limit_show";
		$companies[$letter] = $wpdb->get_results($company_query);
	}
	$other_companies_query = "SELECT DISTINCT p.ID, p.post_title
                                   FROM {$wpdb->prefix}posts as p 
                                   LEFT JOIN {$wpdb->postmeta} AS pm
                                   ON p.ID = pm.post_id AND pm.meta_key = '_noo_job_count'
                                   WHERE p.post_type = 'noo_company' AND p.post_status = 'publish'  AND pm.meta_value >= $show_company_no_job AND p.post_title <> '' {$not_like_str}
                                   ORDER BY p.post_title ASC";
	$other_companies = $wpdb->get_results($other_companies_query);
	?>
    <?php
    if( (!isset($_GET['company_category'])) && (!isset($_GET['s'])) && (!empty($companies) || !empty($other_companies) )) {

		wp_enqueue_script('vendor-isotope');
		?>
        <div class="masonry">
            <ul class="companies-overview masonry-container ">
                <?php
                if( !empty($companies) )
                {
	                $current_letter = '';
	                foreach ($companies as $letter => $company_letter)
	                {

		                if (!empty($company_letter))
		                {
			                $start = $limit_show;
			                $current_letter = $letter;
			                $total_company  = $company_letter[0]->total_company;
			                echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
			                echo '<ul data-total_company="'.$total_company.'">';
			                foreach ($company_letter as $post)
			                {
				                $company_name = $post->post_title;
				                if (empty($company_name))
					                continue;
				                $count = noo_company_job_count($post->ID);

				                echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . noo_get_the_company_name($post) . ' (' . $count . ')</a></li>';

			                }

			                echo '</ul>';
			                if ($total_company > $limit_show)
			                {
				                echo '<div class="show-more-company"><a href="javascript:void(0)" class="js-btn-sm-company" title="' . __('Show more', 'noo') . '" data-start="'.$start.'" data-limit="'.$limit_show.'" data-security="'.$ajax_nonce.'" data-filter_letter="'.$current_letter.'">' . __('+ Show more', 'noo') . '</a>';
			                }
		                }

	                }
                }

                if( !empty($other_companies) )
                {
	                $current_letter = '';

	                foreach ($other_companies as $post)
	                {
                        $company_name = $post->post_title;
                        if (empty($company_name)){
                            continue;
                        }

                        $company_letter = function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($company_name, 0, 1)) : strtoupper(substr($company_name, 0, 1));
                        $count          = noo_company_job_count($post->ID);

                        if ($company_letter != $current_letter)
                        {
                            if ($current_letter != '')
                            {
                                echo '</ul>';
                                echo '</li>';
                            }
                            $current_letter = $company_letter;

                            echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
                            echo '<ul>';
                        }

                        echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . noo_get_the_company_name($post) . ' (' . $count . ')</a></li>';
	                }
	                echo '</ul>';
	                echo '</li>';
                }

                ?>
            </ul>
        </div>
    <?php
    }elseif (isset($_GET['company_category']) || (isset($_GET['s']))){
        wp_enqueue_script('vendor-isotope');
        $args = array(
            'post_type' => 'noo_company',
            'post_status' => 'publish',
            's' => esc_html($_GET['s']),
            'paged' => isset($_GET['paged']) ? $_GET['paged'] : 1,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        $get_query = jm_company_query_from_request($args, $_GET);
        $query = new WP_Query( $get_query);
    $current_letter = '';
    ?>
<div class="masonry">
    <ul class="companies-overview masonry-container ">
        <?php if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()): $query->the_post();
            global $post;
            ?>
            <?php
            $company_name = $post->post_title;
            if (empty($company_name)) {
            	continue;
            }

            $company_letter = function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($company_name, 0, 1)) : strtoupper(substr($company_name, 0, 1));
            $count          = noo_company_job_count($post->ID);
            if ($company_letter != $current_letter) {
                if ($current_letter != '') {
                    echo '</ul>';
                    echo '</li>';
                }

                $current_letter = $company_letter;


                echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
                echo '<ul>';
            }

            echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . noo_get_the_company_name($post) . ' (' . $count . ')</a></li>';
            ?>
        <?php endwhile; ?>
    </ul>
    <?php
   	else:
        noo_get_layout('no-content');
    endif; ?>
</div>
<?php
}
    endif;

