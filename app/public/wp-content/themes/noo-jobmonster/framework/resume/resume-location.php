<?php
if( !function_exists('jm_resume_enqueue_map_script')):
    function jm_resume_enqueue_map_script($query=''){
        $nooResumeMap = array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'zoom'      => jm_get_location_setting('zoom',10),
            'latitude'  => jm_get_location_setting('latitude',''),
            'longtitude'=> jm_get_location_setting('longitude',''),
            'draggable' => 0,
            'theme_dir'=> get_template_directory(),
            'theme_uri'=> get_template_directory_uri(),
            'marker_icon'=>NOO_ASSETS_URI.'/images/map-marker.png',
            'marker_data'=>jm_build_resume_map_data($query),
            'primary_color'=>noo_get_option('noo_site_link_color',noo_default_primary_color())
        );
        wp_localize_script(
            'noo-job',
            'nooResumeMap',
            $nooResumeMap
        );
        $map_type = jm_get_location_setting('map_type','google');
        if($map_type == 'google'){
            wp_enqueue_script('noo-resume-map');
        }else{
            wp_enqueue_script('bing-map');
            wp_enqueue_script('bing-map-api');
            wp_localize_script(
                'bing_map',
                'nooResumeMap',
                $nooResumeMap
            );
        }
    }
endif;
if ( ! function_exists( 'jm_build_resume_map_data' ) ) :
    function jm_build_resume_map_data($query='') {
        $can_view_resume_list = jm_can_view_resumes_list();
        if ($can_view_resume_list) {
            if (!empty($query)) {
                $r = $query;
            } else {
                $args = array(
                    'post_type' => 'noo_resume',
                    'post_status' => 'publish',
                );
                $r = new WP_Query($args);

            }

            $markers = array();

            if ($r->have_posts()):
                while ($r->have_posts()):
                    $r->the_post();
                    global $post;

                    $post_id = $post->ID;
                    $job_location = noo_get_post_meta($post_id, 'resume_address', '');
                    $lat = noo_get_post_meta($post_id, '_resume_address_lat', '');
                    $long = noo_get_post_meta($post_id, '_resume_address_lat', '');

                    if (empty($long) or empty($lat)) {

                        $job_location_geo_data = jm_job_get_term_geolocation($job_location);

                        if (empty($job_location_geo_data) or is_wp_error($job_location_geo_data)) {
                            continue;
                        }

                        $long = isset($job_location_geo_data['long']) ? $job_location_geo_data['long'] : '';
                        $lat = isset($job_location_geo_data['lat']) ? $job_location_geo_data['lat'] : '';
                        
                    }
                    $cover_image = noo_get_post_meta($post->ID, 'cover_image', '');
                    $image = '';
                    if (is_numeric($cover_image)) {
                        if (!empty($cover_image)) {
                            $image = wp_get_attachment_image_src($cover_image, 'cover-image');

                            $image = $image[0];
                        }
                    }
                    if (empty($cover_image)) {
                        $image = NOO_ASSETS_URI . '/images/heading-bg.jpg';
                    }
                    $candidate_id = get_post_field('post_author', $post->ID);
                    $candidate_avatar = '';
                    $candidate_name = '';
                    $profile_image = '';
                    if (!empty($candidate_id)) {
                        $profile_image = get_user_meta($candidate_id, 'profile_image', true);
                        if (empty($profile_image)) {
                            $profile_image = NOO_ASSETS_URI . '/images/candidate.png';
                        }elseif (is_numeric($profile_image)){
                            $profile_image = wp_get_attachment_image_src($profile_image, 'thumbnail');
                            $profile_image = $profile_image[0];
                        }
                        $candidate = get_userdata($candidate_id);
                        $candidate_name = $candidate->display_name;
                        $candidate_avatar = noo_get_avatar($candidate_id, 160);
                    }
                    $value = jm_resume_get_tax_value($post->ID, 'job_category');
                    $archive_link = get_post_type_archive_link('noo_resume');
                    $links = array();
                    foreach ($value as $cat) {
                        if (!is_numeric($cat)) {
                            continue;
                        }
                        $category_link = esc_url(add_query_arg(array('category' => $cat), $archive_link));
                        $term = get_term_by('id', $cat, 'job_category');
                        $cat_name = $term ? $term->name : '';
                        $links[] = '<a class="resume-category"  href="' . $category_link . '" >' . $cat_name . '</a>';
                    }
                    $cat = join('', $links);


                    $marker = array(
                        'post_type' => 'resume',
                        'latitude' => $lat,
                        'longitude' => $long,
                        'title' => htmlentities(get_the_title($post->ID)),
//                        'image' => ($candidate_avatar),
                        'icon' => $profile_image,
                        'url' => get_permalink($post->ID),
                        'candidate_name' => $candidate_name,
                        'category' => $cat,

                    );
                    $markers[] = $marker;
                endwhile;
                wp_reset_postdata();
            endif;

            $result = json_encode($markers);
            return $result;
        } else {
            return false;
        }
    }
endif;

if(! function_exists('jm_get_marker_resume_data')):
    function jm_get_marker_resume_data($resume_id='')
    {

        // Get lat, long from taxonomy
        $job_location = noo_get_post_meta($resume_id, 'resume_address', '');
        $lat = noo_get_post_meta($resume_id, '_resume_address_lat', '');
        $long = noo_get_post_meta($resume_id, '_resume_address_lat', '');

        if ( empty( $long ) or empty( $lat ) ) {

            $job_location_geo_data = jm_job_get_term_geolocation( $job_location );

            if ( empty( $job_location_geo_data ) or is_wp_error( $job_location_geo_data ) ) {
                return false;
            }
            
            $long = isset($job_location_geo_data['long']) ? $job_location_geo_data['long'] : '';
            $lat  = isset($job_location_geo_data['lat']) ? $job_location_geo_data['lat'] : '';
            
        }
        $cover_image  = noo_get_post_meta( $resume_id, 'cover_image', '' );
        $image='';
        if ( is_numeric( $cover_image ) ) {
            if ( ! empty( $cover_image ) ) {
                $image = wp_get_attachment_image_src( $cover_image, 'cover-image' );

                $image   = $image[ 0 ];
            }
        }
        if ( empty( $cover_image ) ) {
            $image = NOO_ASSETS_URI . '/images/heading-bg.jpg';
        }
        $candidate_id = get_post_field( 'post_author', $resume_id);
        $candidate_avatar='';
        $candidate_name ='';
        $profile_image = '';
        if(!empty($candidate_id)){
            $profile_image = get_user_meta( $candidate_id, 'profile_image', true );
            if(empty($profile_image)){
                $profile_image =NOO_ASSETS_URI . '/images/candidate.png';
            }elseif (is_numeric($profile_image)){
                $profile_image = wp_get_attachment_image_src($profile_image, 'thumbnail');
                $profile_image = $profile_image[0];
            }
            $candidate = get_userdata($candidate_id);
            $candidate_name = $candidate->display_name;
            $candidate_avatar = noo_get_avatar( $candidate_id, 160);
        }

        $value = jm_resume_get_tax_value($resume_id, 'job_category');
        $archive_link = get_post_type_archive_link('noo_resume');
        $links = array();
        foreach ($value as $cat) {
            if(!is_numeric($cat)){
                continue;
            }
            $category_link = esc_url(add_query_arg(array('category' => $cat), $archive_link));
            $term = get_term_by('id', $cat, 'job_category');
            $cat_name =  $term ? $term->name : '';
            $links[] = '<a class="resume-category"  href="' . $category_link . '" >' . $cat_name . '</a>';
        }
        $cat = join('', $links);


        $marker    = array(
            'post_type' => 'resume',
            'latitude' => $lat,
            'longitude' => $long,
            'title' => htmlentities(get_the_title($resume_id)),
//            'image' => ($candidate_avatar),
            'icon'  => $profile_image,
            'url' => get_permalink($resume_id),
            'candidate_name'=> $candidate_name,
            'cover_image'   => $image,
            'category'      => $cat,

        );
        return $marker;
    }
endif;