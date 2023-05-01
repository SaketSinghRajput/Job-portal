<?php
if( !function_exists( 'jm_job_enqueue_scripts' ) ) :
    function jm_job_enqueue_scripts(){
        $js_folder_uri = SCRIPT_DEBUG ? NOO_ASSETS_URI . '/js' : NOO_ASSETS_URI . '/js/min';
        $js_suffix = SCRIPT_DEBUG ? '' : '.min';
        $google_api = jm_get_location_setting( 'google_api', '' );

        $map_type = jm_get_location_setting('map_type','');
        if ($map_type == 'google') {
            wp_register_script( 'google-map','http'.(is_ssl() ? 's':'').'://maps.googleapis.com/maps/api/js?language='.get_locale().'&libraries=places' . ( !empty( $google_api ) ? '&key=' .$google_api : '' ), array('jquery'), null , true);

            wp_register_script( 'google-map-infobox', $js_folder_uri . '/infobox' . $js_suffix . '.js', array( 'jquery' , 'google-map' ), null, true );
            wp_register_script( 'google-map-markerclusterer', $js_folder_uri . '/markerclusterer' . $js_suffix . '.js', array( 'jquery' , 'google-map' ), null, true );
            wp_register_script( 'noo-job-map', $js_folder_uri . '/job-map' . $js_suffix . '.js', array( 'google-map-infobox','google-map-markerclusterer',), null, true );
            wp_register_script(
                'noo-resume-map',
                $js_folder_uri . '/resume-map' . $js_suffix . '.js',
                array( 'google-map-infobox','google-map-markerclusterer'),
                null,
                true
            );
        }
        if ($map_type == 'bing') {
            $bing_api = jm_get_location_setting( 'bing_api', '' );
            wp_register_script( 'bing-map-api', 'https://www.bing.com/api/maps/mapcontrol?key='.$bing_api.'&callback=JM_Bing_Map', array( 'jquery' ), null, true );
            wp_register_script( 'bing-map', $js_folder_uri . '/location-bing-map' .$js_suffix. '.js', array( 'jquery','bing-map-api' ), null, true );
            wp_localize_script( 'bing-map', 'JM_Bing_Value', array(
                'lat'                  => floatval( jm_get_location_setting( 'latitude', '40.714398' ) ),
                'lng'                  => floatval( jm_get_location_setting( 'longitude', '-74.005279' ) ),
                'zoom'                 => absint( jm_get_location_setting( 'zoom', '17' ) ),
            ) );
        }

        $is_rtl = is_rtl();
        wp_register_style( 'vendor-wysihtml5-css', NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/bootstrap-wysihtml5'.($is_rtl ? '-rtl.css' : '.css'), array( 'noo-main-style' ), null );
        wp_register_script( 'vendor-bootstrap-wysihtml5', NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/bootstrap3-wysihtml5.custom' . $js_suffix . '.js', array( 'jquery', 'vendor-bootstrap'), null, true );
        $wysihtml5L10n = array(
            'normal'         => esc_html__('Normal text', 'noo'),
            'h1'             => __('Heading 1', 'noo'),
            'h2'             => __('Heading 2', 'noo'),
            'h3'             => __('Heading 3', 'noo'),
            'h4'             => __('Heading 4', 'noo'),
            'h5'             => __('Heading 5', 'noo'),
            'h6'             => __('Heading 6', 'noo'),
            'bold'           => __('Bold', 'noo'),
            'italic'         => __('Italic', 'noo'),
            'underline'      => __('Underline', 'noo'),
            'small'          => __('Small', 'noo'),
            'unordered'      => __('Unordered list', 'noo'),
            'ordered'        => __('Ordered list', 'noo'),
            'outdent'        => __('Outdent', 'noo'),
            'indent'         => __('Indent', 'noo'),
            'align_left'     => __('Align Left', 'noo'),
            'align_right'    => __('Align Right', 'noo'),
            'align_center'   => __('Align Center', 'noo'),
            'align_justify'  => __('Align Justify', 'noo'),
            'insert_link'    => __('Insert link', 'noo'),
            'cancel'         => __('Cancel', 'noo'),
            'target'         => __('Open link in a new window', 'noo'),
            'insert_image'   => __('Insert image', 'noo'),
            'cancel'         => __('Cancel', 'noo'),
            'edit_html'      => __('Edit HTML', 'noo'),
            'black'          => __('Black', 'noo'),
            'silver'         => __('Silver', 'noo'),
            'gray'           => __('Grey', 'noo'),
            'maroon'         => __('Maroon', 'noo'),
            'red'            => __('Red', 'noo'),
            'purple'         => __('Purple', 'noo'),
            'green'          => __('Green', 'noo'),
            'olive'          => __('Olive', 'noo'),
            'navy'           => __('Navy', 'noo'),
            'blue'           => __('Blue', 'noo'),
            'orange'         => __('Orange', 'noo'),
            'stylesheet_rtl' => $is_rtl ? NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/stylesheet-rtl.css' : NOO_FRAMEWORK_URI . '/vendor/bootstrap-wysihtml5/stylesheet.css'
        );
        wp_localize_script('vendor-bootstrap-wysihtml5', 'wysihtml5L10n', $wysihtml5L10n);

        wp_register_style( 'vendor-datetimepicker', NOO_FRAMEWORK_URI . '/vendor/datetimepicker/jquery.datetimepicker.css', null );
        wp_register_script( 'vendor-datetimepicker', NOO_FRAMEWORK_URI . '/vendor/datetimepicker/jquery.datetimepicker.js', array( 'jquery' ), null, true );

        $datetimeL10n = array(
            'lang' => substr(get_bloginfo ( 'language' ), 0, 2),
            'rtl' => $is_rtl,

            'January'=>ucfirst(esc_html__('January', 'noo')),
            'February'=>ucfirst(esc_html__('February', 'noo')),
            'March'=>ucfirst(esc_html__('March', 'noo')),
            'April'=>ucfirst(esc_html__('April', 'noo')),
            'May'=>ucfirst(esc_html__('May', 'noo')),
            'June'=>ucfirst(esc_html__('June', 'noo')),
            'July'=>ucfirst(esc_html__('July', 'noo')),
            'August'=>ucfirst(esc_html__('August', 'noo')),
            'September'=>ucfirst(esc_html__('September', 'noo')),
            'October'=>ucfirst(esc_html__('October', 'noo')),
            'November'=>ucfirst(esc_html__('November', 'noo')),
            'December'=>ucfirst(esc_html__('December', 'noo')),

            'Sunday'=>ucfirst(esc_html__('Sunday', 'noo')),
            'Monday'=>ucfirst(esc_html__('Monday', 'noo')),
            'Tuesday'=>ucfirst(esc_html__('Tuesday', 'noo')),
            'Wednesday'=>ucfirst(esc_html__('Wednesday', 'noo')),
            'Thursday'=>ucfirst(esc_html__('Thursday', 'noo')),
            'Friday'=>ucfirst(esc_html__('Friday', 'noo')),
            'Saturday'=>ucfirst(esc_html__('Saturday', 'noo')),
        );
        wp_localize_script( 'vendor-datetimepicker', 'datetime', $datetimeL10n );

        wp_register_script( 'vendor-jquery-validate', NOO_FRAMEWORK_URI . '/vendor/jquery-validate/jquery.validate.min.js', array( 'jquery'), null, true );

        wp_register_script( 'noo-job', $js_folder_uri . '/job'. $js_suffix .'.js', array( 'vendor-bootstrap-wysihtml5','vendor-chosen','vendor-jquery-validate','vendor-datetimepicker','jquery-ui-slider','vendor-quicksearch'), null, true );

        wp_enqueue_style('vendor-datetimepicker');
        wp_enqueue_style('vendor-chosen');
        wp_enqueue_style('vendor-wysihtml5-css');

        $allowed_exts = jm_get_allowed_attach_file_types();

        $nooJobL10n = array(
            'ajax_url'        => admin_url( 'admin-ajax.php', 'relative' ),
            'ajax_action'     => add_query_arg( 'action', '__action__',  admin_url( 'admin-ajax.php', 'relative' ) ),
            'ajax_finishedMsg'=>__('All jobs displayed','noo'),
            'validate_messages'=>array(
                'required'=>__("This field is required.",'noo'),
                'remote'=>__("Please fix this field.",'noo'),
                'email'=>__("Please enter a valid email address.",'noo'),
                'url'=>__("Please enter a valid URL.",'noo'),
                'date'=>__("Please enter a valid date.",'noo'),
                'dateISO'=>__("Please enter a valid date (ISO).",'noo'),
                'number'=>__("Please enter a valid number.",'noo'),
                'digits'=>__("Please enter only digits.",'noo'),
                'creditcard'=>__("Please enter a valid credit card number.",'noo'),
                'equalTo'=>__("Please enter the same value again.",'noo'),
                'maxlength'=>__("Please enter no more than {0} characters.",'noo'),
                'minlength'=>__("Please enter at least {0} characters.",'noo'),
                'rangelength'=>__("Please enter a value between {0} and {1} characters long.",'noo'),
                'range'=>__("Please enter a value between {0} and {1}.",'noo'),
                'max'=>__("Please enter a value less than or equal to {0}.",'noo'),
                'min'=>__("Please enter a value greater than or equal to {0}.",'noo'),
                'chosen'=>__('Please choose a option','noo'),
                'uploadimage'=>__('Please select a image file','noo'),
                'extension'=>__('Please upload a valid file extension.','noo')
            ),
            'date_format'=>get_option('date_format'),
            'file_exts' => implode('|', $allowed_exts),
            'dataTable' => array(
                'emptyTable' 	=> __('No data available in table','noo'),
                'next' 			=> __('Next','noo'),
                'previous' 		=> __('Previous','noo'),
                'info' 			=> sprintf(__('Showing %s to %s of %s entries','noo'),'_START_','_END_','_TOTAL_'),
                'infoEmpty' 	=> __('Showing 0 to 0 of 0 entries','noo'),
                'lengthMenu'	=> sprintf(__('Show %s entries','noo'),'_MENU_'),
                'search'		=> __('Search:','noo'),
            	'searchByCandidate' => __('Search by candidate','noo')
            ),
            'close'							=> __('Close','noo'),
            'map_using'                 	=> (jm_get_location_setting('map_type','')=='google') ?'google':'bing',
            'bing_map_api'              	=> jm_get_location_setting( 'bing_api', '' ),
            'google_map_key'            	=> jm_get_location_setting( 'google_api', '' ),
            'distance_text'             	=> __('Proximity: ','noo'),
        	'distance_slider_max_value'     => apply_filters('noo_distance_slider_max_value', 100),
        );
        wp_localize_script('noo-job', 'nooJobL10n', $nooJobL10n);
        wp_enqueue_script('noo-job');
    }

    add_action( 'wp_enqueue_scripts', 'jm_job_enqueue_scripts', 99 );
endif;

if( !function_exists( 'jm_job_enqueue_map_script' ) ) :
    function jm_job_enqueue_map_script($show_company_marker='no',$query='') {
        // prevent conflict with Ultimate VC Add-ons
        define('DISABLE_ULTIMATE_GOOGLE_MAP_API', true);
        $nooJobGmapL10n = array(
            'ajax_url'		=> admin_url( 'admin-ajax.php' ),
            'zoom'			=> 10,
            'latitude'		=> 40.714398,
            'longitude'		=>-74.005279,
            'draggable'		=>0,
            'theme_dir'		=> get_template_directory(),
            'theme_uri'		=> get_template_directory_uri(),
            'marker_icon'	=> NOO_ASSETS_URI.'/images/map-marker.png',
            'marker_data'	=> jm_build_job_map_data($query),
            'primary_color'	=>noo_get_option('noo_site_link_color',noo_default_primary_color()),
            'total_job'    	=> esc_html__('Total Jobs:','noo') 
        );
        if($show_company_marker == 'yes'){
            $nooJobGmapL10n['marker_company_data'] = jm_build_company_map_data();
        }else{
            $nooJobGmapL10n['marker_company_data'] = 0;
        }
        wp_localize_script('noo-job','nooJobGmapL10n', $nooJobGmapL10n);
        $jobs_layout = noo_get_option('noo_jobs_layout','sidebar');
        $mode_type   = (isset($_GET['mode'])) ? $_GET['mode'] : '';
            $map_type = jm_get_location_setting('map_type','google');
            if($map_type == 'google'){
                wp_enqueue_script('noo-job-map');
            }else{
                wp_enqueue_script('bing-map');
                wp_enqueue_script('bing-map-api');
            }
    }
endif;
