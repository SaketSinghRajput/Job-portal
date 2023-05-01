<?php
/**
 * Created by PhpStorm.
 * Date: 11/7/2018
 * Time: 3:28 PM
 */
$args = array(
    'post_type' => 'noo_job',
    'posts_per_page'=>-1,
    'post_status' => array('publish'),
);
$r = new WP_Query($args);
header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
do_action('rss_tag_pre', 'rss2');
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php
    /**
     * Fires at the end of the RSS root to add namespaces.
     *
     * @since 2.0.0
     */
    do_action('rss2_ns');
    ?>
>

    <channel>
        <title><?php wp_title_rss(); ?></title>
        <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml"/>
        <link><?php bloginfo_rss('url') ?></link>
        <description><?php bloginfo_rss("description") ?></description>
        <lastBuildDate><?php
            $date = get_lastpostmodified('GMT');
            echo $date ? mysql2date('r', $date, false) : date('r');
            ?></lastBuildDate>
        <language><?php bloginfo_rss('language'); ?></language>
        <sy:updatePeriod><?php
            $duration = 'hourly';

            /**
             * Filters how often to update the RSS feed.
             *
             * @since 2.1.0
             *
             * @param string $duration The update period. Accepts 'hourly', 'daily', 'weekly', 'monthly',
             *                         'yearly'. Default 'hourly'.
             */
            echo apply_filters('rss_update_period', $duration);
            ?></sy:updatePeriod>
        <sy:updateFrequency><?php
            $frequency = '1';

            /**
             * Filters the RSS update frequency.
             *
             * @since 2.1.0
             *
             * @param string $frequency An integer passed as a string representing the frequency
             *                          of RSS updates within the update period. Default '1'.
             */
            echo apply_filters('rss_update_frequency', $frequency);
            ?></sy:updateFrequency>
        <?php
        /**
         * Fires at the end of the RSS2 Feed Header.
         *
         * @since 2.0.0
         */
        do_action('rss2_head');

        while ($r->have_posts()) : $r->the_post();
            global $post;
            $company_id = jm_get_job_company($post);

            $company_name = !empty($company_id) ? get_the_title($company_id) : '';
            $company_link = !empty($company_id) ? get_the_permalink($company_id) : '';
            $company_logo = !empty($company_id) ? Noo_Company::get_company_logo($company_id) : '';
            $list_job_meta['show_company'] = false;
            $fields = jm_get_job_custom_fields();
            $settings_fields = get_theme_mod('noo_jobs_list_fields','job_location,job_category');
            $settings_fields = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;
            $display_fields = array();
            foreach ( $settings_fields as $index => $job_field) {
                if ($job_field == 'title') {
                    $field = array('name' => 'title', 'label' => __('Job  Title', 'noo'));
                } else {
                    $field = jm_get_job_field($job_field);
                }
                if (!empty($field)) {
                    $display_fields[] = $field;
                }
            }
            ?>
            <job-list-item>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <pubDate><?php echo mysql2date(' d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <company><![CDATA[<?php echo $company_name; ?>]]></company>
                <company-link><![CDATA[<?php echo $company_link; ?>]]></company-link>
                <?php
                    foreach ($display_fields as $field){
                        if($field['name'] == 'job_type'){
                            $types = get_the_terms($post->ID, 'job_type');
                            $type_name = array();
                            foreach ($types as $type) {
                                $type_name[] = $type->name;
                            }
                            if(!empty($type_name)){
                                echo '<job_type><![CDATA['. join(',', $type_name).']></job_type>';
                            }
                        }
                        elseif($field['name'] == 'job_location'){
                            $locations = get_the_terms($post->ID, 'job_location');
                            $loc_name = array();
                            foreach ($locations as $loc) {
                                $loc_name[] = $loc->name;
                            }
                            if(!empty($loc_name)){
                                echo '<job_address><![CDATA['. join(',', $loc_name).']></job_address>';
                            }

                        }
                        elseif($field['name'] == 'job_category'){
                            $categories = get_the_terms(get_the_ID(), 'job_category');
                            $cat_name = array();
                            foreach ($categories as $cat) {
                                $cat_name[] = $cat->name;
                            }
                            if(!empty($cat_name)){
                                echo '<job_category><![CDATA['. join(',', $cat_name).']></job_category>';
                            }

                        }
                        elseif($field['name'] == '_closing'){
                            $closing_date = noo_get_post_meta($post->ID, '_closing', '');
                            $closing_date = is_numeric($closing_date) ? $closing_date : strtotime($closing_date);
                            if(!empty($closing_date)){
                                echo '<job_closing><![CDATA[ '. esc_html(date_i18n(get_option('date_format'), $closing_date)).']]></job_closing>';
                            }

                        }
                        else{
                            $id = jm_job_custom_fields_name($field['name'], $field);
                            $value = noo_get_post_meta($post->ID, $id, '');
                            $value = is_array($value) ? implode(', ', $value) : $value;
                            if(!empty($value)){
                                if ($field['type'] == 'file_upload') {
                                    $files = noo_json_decode($value);
                                    $new_value = array();
                                    foreach ($files as $file) {
                                        $file_url = noo_get_file_upload($file);
                                        echo '<file_upload><![CDATA['.$file_url.']]></file_upload>';
                                    }
                                }

                                elseif ($field['type'] == 'single_image') {
                                    $image_link = wp_get_attachment_url($value);
                                    echo '<image_link><![CDATA['.$image_link.']]></image_link>';

                                }
                                elseif ($field['type'] == 'image_gallery') {
                                    $images = !is_array($value) ? explode(',', $value) : $value;
                                    $new_value = array();
                                    $gallery_id = uniqid();
                                    foreach ($images as $image) {
                                        $img_tag = wp_get_attachment_image($image, $size = 'thumbnail');
                                        $image_link = wp_get_attachment_url($image);
                                        echo '<image_gallery><![CDATA['.$image_link.']]></image_gallery>';
                                    }
                                }else{
                                    echo '<'.$field['name'].'><![CDATA['.$value.']></'.$field['name'].'>';
                                }

                            }
                        }
                    }
                ?>
                <?php if (get_option('rss_use_excerpt')) : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                <?php else : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                    <?php $content = get_the_content(); ?>
                    <?php if (strlen($content) > 0) : ?>
                        <content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
                    <?php else : ?>
                        <content:encoded><![CDATA[<?php the_excerpt_rss(); ?>]]></content:encoded>
                    <?php endif; ?>
                <?php endif; ?>
                <?php rss_enclosure(); ?>
                <?php
                /**
                 * Fires at the end of each RSS2 feed item.
                 *
                 * @since 2.0.0
                 */
                do_action('rss2_item');
                ?>
            </job-list-item>
        <?php endwhile; ?>
    </channel>
</rss>
