<?php
if(!function_exists('jm_addon_elementor_template')):
    function jm_addon_elementor_template($template_name, $default_path = '' ){
        // Set default plugin templates path.
        if ( ! $default_path ) :
            $default_path =JOB_ADDON_INCLUDES_DIR . '/elementor-template/'; // Path to the template folder
        endif;
        // Get plugins template file.
        $template = $default_path . $template_name;
        return apply_filters( 'jm_addon_get_elementor_template', $template, $template_name, $default_path );
    }
endif;
if(!function_exists('jm_addon_get_elementor_template')):
    function jm_addon_get_elementor_template($template_name, $default_path = ''){
        $template_file = jm_addon_elementor_template( $template_name,$default_path );

        if ( ! file_exists( $template_file ) ) :
            _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
            return;
        endif;
        return $template_file;
    }
endif;
if (!function_exists('jm_addon_job_loop')) :
    function jm_addon_job_loop($args = '')
    {
        $defaults = array(
            'paginate' => 'normal',
            'class' => '',
            'item_class' => 'loadmore-item',
            'query' => '',
            'title_type' => 'text',
            'title' => '',
            'pagination' => 1,
            'excerpt_length' => 30,
            'posts_per_page' => '',
            'ajax_item' => false,
            'featured' => 'recent',
            'no_content' => 'text',
            'display_style' => 'list',
            'list_job_meta' => array(),
            'paginate_data' => array(),
            'show_view_more' => 'yes',
            'show_autoplay' => 'on',
            'slider_style' => 'style-1',
            'is_elementor' => true,
            'related_job' => false,
        );
        $loop_args = wp_parse_args($args, $defaults);
        extract($loop_args);
        global $wp_query;

        if (!empty($loop_args['query'])) {
            $wp_query = $loop_args['query'];
        }
        $ajax_item = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'noo_nextelementor';
        if($ajax_item){
            remove_action( 'pre_get_posts', 'jm_job_pre_get_posts' );
            $new_args = jm_job_next_ajax_elementor();
            $wp_query = new WP_Query($new_args);
        }
        $content_meta = array();

        $content_meta['show_company'] =get_theme_mod('noo_jobs_show_company_name', true);
        $settings_fields = get_theme_mod('noo_jobs_list_fields', 'job_type,job_location,job_date,_closing');

        $content_meta['fields'] = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;

        $list_job_meta = array_merge($content_meta, $list_job_meta);
        $paginate_data = apply_filters('noo-job-loop-paginate-data', $paginate_data, $loop_args);

        $item_class = array($item_class);
        $item_class[] = 'noo_job';
        $item_class[] = $style;
        if ($is_elementor) {
            remove_action('job_list_before', 'noo_job_list_before');
            // Use for Noo Job elementor widget
            if ($display_style == 'slider') {
                include (jm_addon_get_elementor_template("job/slider.php"));
            }elseif ($display_style == 'list') {
                remove_action( 'pre_get_posts', 'jm_job_pre_get_posts' );
                include (jm_addon_get_elementor_template("job/list.php"));
            }elseif ($display_style == 'grid') {
                include ( jm_addon_get_elementor_template("job/grid.php"));
            }elseif ($display_style == 'list2'){
                include ( jm_addon_get_elementor_template("job/list2.php"));
            } else {
                include (jm_addon_get_elementor_template("job/list.php"));
            }
        }
        wp_reset_query();

    }
    add_action( 'wp_ajax_nopriv_noo_nextelementor', 'jm_addon_job_loop' );
    add_action( 'wp_ajax_noo_nextelementor', 'jm_addon_job_loop' );
endif;
if( !function_exists('jm_addon_resume_loop') ) :
    function jm_addon_resume_loop( $args = '' ) {
        $defaults = array(
            'query'           => '',
            'title'           => '',
            'rows'            => '2',
            'column'          => '2',
            'autoplay'        => 'false',
            'slider_speed'    => '800',
            'pagination'      => 'no',
            'resume_style'    => 'list',
            'paginate'        => 'normal',
            'ajax_item'       => false,
            'excerpt_length'  => 30,
            'no_content'      => 'text',
            'posts_per_page'  => 3,
            'is_slider'       => false,
            'is_elementor'    => true,
            'job_category'    => 'all',
            'job_location'    => 'all',
            'orderby'         => 'date',
            'order'           => 'desc',
            'live_search'     => false
        );

        $loop_args = wp_parse_args($args, $defaults);

        extract($loop_args);
        global $wp_query;
        if (!empty($loop_args['query'])) {
            $wp_query = $loop_args['query'];
        }

        ob_start();
        $arr_type = array( 'list', 'grid' );

        if($is_elementor){
            $type  = $resume_style;
            $display_style = jm_addon_get_elementor_template('resume/' . esc_attr( $type ) .'.php');
            include( $display_style );
        }

        echo ob_get_clean();

        wp_reset_postdata();

    }
endif;
if (!function_exists('noo_get_company_location')) :

    function noo_get_company_location($company_id)
    {

        if (empty($company_id)) {
            return false;
        }

        $term_id = get_post_meta($company_id, '_address', true);
        $term    = get_term($term_id, 'job_location');

        if (is_wp_error($term) || empty($term)) {
            return false;
        }
        return $term->name;

    }

endif;
if(!function_exists('jm_job_next_ajax_elementor')){
    function jm_job_next_ajax_elementor(){
            $paged          = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
            $posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : '-1';
            $show           = isset( $_POST['show'] ) ? $_POST['show'] : 'recent';
            $display_style  = isset( $_POST['display_style'] ) ? ( $_POST['display_style'] ) : 'list';
            $job_category   = isset( $_POST['job_category'] ) ? $_POST['job_category'] : 'all' ;
            $job_type       = isset( $_POST['job_type'] ) ? $_POST['job_type'] : 'all' ;
            $job_location   = isset( $_POST['job_location'] ) ? $_POST['job_location'] : 'all';
            $orderby        = isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'date';
            $order          = isset( $_POST['order'] ) ? $_POST['order'] : 'desc';
            $show_view_more = isset( $_POST['show_view_more'] ) ? $_POST['show_view_more'] : 'yes' ;
        $args = array(
            'post_type'           => 'noo_job',
            'post_status'         => 'publish',
            'paged'               => $paged,
            'posts_per_page'      => $posts_per_page,
            'ignore_sticky_posts' => true,
        );

        //  -- tax_query

        $job_category = explode( ",", $job_category );

//        $args['tax_query'] = array( 'relation' => 'AND' );

        if ( ! in_array( 'all', $job_category ) && empty($job_category) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_category',
                'field'    => 'slug',
                'terms'    => $job_category,
            );
        }

        $job_type = explode( ",", $job_type );

        if ( ! in_array( 'all', $job_type ) && empty($job_type) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_type',
                'field'    => 'slug',
                'terms'    => $job_type,
            );
        }

        $job_location = explode( ",", $job_location );

        if ( ! in_array( 'all', $job_location ) && empty($job_location)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_location',
                'field'    => 'slug',
                'terms'    => $job_location,
            );
        }

        //  -- Check order by......

        if ( $orderby == 'view' ) {
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_noo_views_count';
        } elseif ( $orderby == 'date' ) {
            $args['orderby'] = 'date';
        } elseif ( $orderby == 'featured' ) {
            $args['orderby']  = 'meta_value post_date';
            $args['meta_key'] = '_featured';
        } else {
            $args['orderby'] = 'rand';
        }

        //  -- Check order
        if ( $orderby != 'rand' ) {
            if ( $order == 'asc' ) {
                $args['order'] = 'ASC';
            } else {
                $args['order'] = 'DESC';
            }
        }

        if ( $show == 'featured' ) {
            $args['meta_query'][] = array(
                'key'   => '_featured',
                'value' => 'yes',
            );
        }

        return $args ;

    }
}