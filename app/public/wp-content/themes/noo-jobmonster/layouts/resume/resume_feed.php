<?php

$args = array(
    'post_type' => 'noo_resume',
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
            $candidate = get_user_by('id', $post->post_author);
            $candidate_name = !empty($candidate) ? $candidate->display_name : '';
            $field = jm_get_resume_field();
            $job_category =  (array) noo_get_post_meta($post->ID, '_job_category', array());
            foreach ($job_category as $cat){
                $term = get_term_by('id', $cat, 'job_category');
                $cat_name[] = $term->name;
            }
            $job_locations = (array) noo_get_post_meta($post->ID, '_job_location', array());
            foreach ($job_locations as $loc){
                $term = get_term_by('id', $loc, 'job_location');
                $loc_name[] = $term->name;
            }
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

            ?>
            <resume-list-item>
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <pubDate><?php echo mysql2date(' d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                <candidate><?php echo $candidate_name ?></candidate>
                <?php
                foreach ($display_fields as $field) {
                    if($field['name'] == '_portfolio'){
                        continue;
                    }
                    $id = jm_resume_custom_fields_name($field['name'], $field);
                    $value = noo_get_post_meta($post->ID, $id, '');
                    if ($id == '_job_category') {
                        if (!empty($value)) {
                            $value = jm_resume_get_tax_value($post->ID, $id);
                            $cat_name=array();
                            foreach ($value as $cat) {
                                if(!is_numeric($cat)){
                                    continue;
                                }
                                $term = get_term_by('id', $cat, 'job_category');
                                $cat_name[] = $term->name;
                            }
                            if(!empty($cat_name)){
                                echo '<category><![CDATA['. join(',', $cat_name).']></category>';
                            }
                        }
                    } elseif ($id == '_job_location') {
                        if (!empty($value)) {
                            $value = jm_resume_get_tax_value($post->ID, $id);
                            $loc_name=array();
                            foreach ($value as $loc) {
                                if(!is_numeric($loc)){
                                    continue;
                                }
                                $term = get_term_by('id', $loc, 'job_location');
                                $loc_name[]= $term->name;
                            }
                            if(!empty($loc_name)){
                                echo '<location><![CDATA['. join(',', $loc_name).']></location>';
                            }
                        }
                    } else {
                        $value = !is_array($value) ? trim($value) : $value;
                        if(!empty($value)){
                            echo '<'.$field['name'].'><![CDATA['.$value.']></'.$field['name'].'>';
                        }
                    }

                }
                ?>
                <?php if (get_option('rss_use_excerpt')) : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                <?php else : ?>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                    <?php $content = get_the_content_feed('rss2'); ?>
                    <?php if ( strlen( $content ) > 0 ) : ?>
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
            </resume-list-item>
        <?php endwhile; ?>
    </channel>
</rss>
