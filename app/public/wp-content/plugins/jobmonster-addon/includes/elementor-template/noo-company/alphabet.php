<?php
global $wpdb;
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
$not_like_str = ''; ?>
<?php if ($show_filter): ?>
    <div class="company-letters">
        <a data-filter="*" href="#all" class="selected"><?php _e('All', 'noo'); ?></a>
        <?php foreach ($letter_range as $letter) {
            $not_like_str .= "AND post_title NOT LIKE '{$letter}%' ";
            $letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
            echo '<a data-filter=".filter-' . $letter . '" href="#' . $letter . '">' . $letter . '</a>';
        } ?>
    </div>
<?php endif; ?>
<?php
$show_company_no_job = (Noo_Company::get_setting('show_no_jobs', 1)) ? 0 : 1;
$start = 0;
$limit_show = $noo_company_setting['number_company_show'] == "" ? 5 : $noo_company_setting['number_company_show'];
$ajax_nonce = wp_create_nonce("show_more_company");
$companies = [];
foreach ($letter_range as $letter) {
    $letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter) : strtoupper($letter);
    $company_query = "SELECT p.ID, p.post_title, p2.total_company 
                                   FROM {$wpdb->prefix}posts as p 
                                   LEFT JOIN {$wpdb->postmeta} AS pm
                                   ON p.ID = pm.post_id AND pm.meta_key = '_noo_job_count'
                                   JOIN (SELECT COUNT(ID) as total_company FROM {$wpdb->prefix}posts WHERE post_type = 'noo_company' AND post_status = 'publish' AND post_title <> '' AND post_title LIKE '{$letter}%') p2
                                   WHERE p.post_type = 'noo_company' AND p.post_status = 'publish' AND pm.meta_value >= $show_company_no_job AND p.post_title <> '' AND p.post_title LIKE '{$letter}%'
                                   ORDER BY p.post_title ASC
                                   LIMIT $start, $limit_show";
    $companies[$letter] = $wpdb->get_results($company_query);
}
$other_companies_query = "SELECT p.ID, p.post_title
                                   FROM {$wpdb->prefix}posts as p 
                                   LEFT JOIN {$wpdb->postmeta} AS pm
                                   ON p.ID = pm.post_id AND pm.meta_key = '_noo_job_count'
                                   WHERE p.post_type = 'noo_company' AND p.post_status = 'publish'  AND pm.meta_value >= $show_company_no_job AND p.post_title <> '' {$not_like_str}
                                   ORDER BY p.post_title ASC";
$other_companies = $wpdb->get_results($other_companies_query);
?>
<?php
if ((!isset($_GET['company_category'])) && (!isset($_GET['s'])) && (!empty($companies) || !empty($other_companies))) {
    ?>
    <div class="masonry">
        <ul class="companies-overview masonry-container ">
            <?php
            if (!empty($companies)) {
                $current_letter = '';
                foreach ($companies as $letter => $company_letter) {

                    if (!empty($company_letter)) {
                        $start = $limit_show;
                        $current_letter = $letter;
                        $total_company = $company_letter[0]->total_company;
                        echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
                        echo '<ul data-total_company="' . $total_company . '">';
                        foreach ($company_letter as $post) {
                            $company_name = $post->post_title;
                            if (empty($company_name))
                                continue;
                            $count = noo_company_job_count($post->ID);

                            echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . esc_attr($company_name) . ' (' . $count . ')</a></li>';

                        }

                        echo '</ul>';
                        if ($total_company > $limit_show) {
                            echo '<div class="show-more-company"><a href="javascript:void(0)" class="js-btn-sm-company" title="' . __('Show more', 'noo') . '" data-start="' . $start . '" data-limit="' . $limit_show . '" data-security="' . $ajax_nonce . '" data-filter_letter="' . $current_letter . '">' . __('+ Show more', 'noo') . '</a>';
                        }
                    }

                }
            }

            if (!empty($other_companies)) {
                $current_letter = '';

                foreach ($other_companies as $post) {
                    $company_name = $post->post_title;
                    if (empty($company_name))
                        continue;

                    $company_letter = function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($company_name, 0, 1)) : strtoupper(substr($company_name, 0, 1));
                    $count = noo_company_job_count($post->ID);

                    if ($company_letter != $current_letter) {
                        if ($current_letter != '') {
                            echo '</ul>';
                            echo '</li>';
                        }
                        $current_letter = $company_letter;

                        echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
                        echo '<ul>';
                    }

                    echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . esc_attr($company_name) . ' (' . $count . ')</a></li>';
                }
                echo '</ul>';
                echo '</li>';
            }

            ?>
        </ul>
    </div>
    <?php
} elseif (isset($_GET['company_category']) || (isset($_GET['s']))) {
    $args = array(
        'post_type' => 'noo_company',
        'post_status' => 'publish',
        's' => esc_html($_GET['s']),
        'paged' => isset($_GET['paged']) ? $_GET['paged'] : 1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $get_query = jm_company_query_from_request($args, $_GET);
    $query = new WP_Query($get_query);
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
                if (empty($company_name)) continue;

                $company_letter = function_exists('mb_strtoupper') ? mb_strtoupper(mb_substr($company_name, 0, 1)) : strtoupper(substr($company_name, 0, 1));
                $count = noo_company_job_count($post->ID);
                if ($company_letter != $current_letter) {
                    if ($current_letter != '') {
                        echo '</ul>';
                        echo '</li>';
                    }

                    $current_letter = $company_letter;


                    echo '<li class="company-group masonry-item filter-' . $current_letter . '"><div id="' . $current_letter . '" class="company-letter text-primary">' . $current_letter . '</div>';
                    echo '<ul>';
                }

                echo '<li class="company-name"><a href="' . get_permalink($post->ID) . '">' . esc_attr($company_name) . ' (' . $count . ')</a></li>';
                ?>
            <?php endwhile; ?>
        </ul>
        </li>
        <?php
        else:?>
            <h3 class="text-center"><?php _e('Nothing Found', 'noo'); ?></h3>
        <?php
        endif; ?>
        </ul>
    </div>
    <?php
}