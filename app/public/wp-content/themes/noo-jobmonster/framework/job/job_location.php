<?php
require_once NOO_FRAMEWORK . '/common/google-map/location.php';

if ( ! function_exists( 'jm_geolocation_enabled' ) ) :
    function jm_geolocation_enabled() {
        return apply_filters( 'noo_job_geolocation_enabled', true );
    }
endif;

if ( ! function_exists( 'jm_get_geolocation' ) ) :
    function jm_get_geolocation( $raw_address = '' ) {
        $invalid_chars = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "", "#" => "" );
        $raw_address   = trim( strtolower( str_replace( array_keys( $invalid_chars ), array_values( $invalid_chars ), $raw_address ) ) );

        if ( empty( $raw_address ) ) {
            return false;
        }

        $transient_name              = 'geocode_' . md5( $raw_address );
        $geocoded_address            = get_transient( $transient_name );
        $jm_geocode_over_query_limit = get_transient( 'jm_geocode_over_query_limit' );

        // Query limit reached - don't geocode for a while
        if ( $jm_geocode_over_query_limit && false === $geocoded_address ) {
            return false;
        }

        try {
            if ( false === $geocoded_address || empty( $geocoded_address->results[0] ) ) {
                $url    = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
                $result = wp_remote_get( apply_filters( 'noo_job_geolocation_endpoint', $url . $raw_address . "&region=" . apply_filters( 'noo_job_geolocation_region_cctld', '', $raw_address ), $raw_address ), array(
                    'timeout'     => 60,
                    'redirection' => 1,
                    'httpversion' => '1.1',
                    'user-agent'  => 'NooJob; ' . home_url( '/' ),
                    'sslverify'   => false,
                ) );
                if ( ! is_wp_error( $result ) && $result['body'] ) {
                    $result           = wp_remote_retrieve_body( $result );
                    $geocoded_address = json_decode( $result );

                    if ( $geocoded_address->status ) {
                        switch ( $geocoded_address->status ) {
                            case 'ZERO_RESULTS' :
                                throw new Exception( __( "No results found", 'noo' ) );
                                break;
                            case 'OVER_QUERY_LIMIT' :
                                set_transient( 'jm_geocode_over_query_limit', 1, MINUTE_IN_SECONDS );
                                throw new Exception( __( "Query limit reached", 'noo' ) );
                                break;
                            case 'OK' :
                                if ( ! empty( $geocoded_address->results[0] ) ) {
                                    set_transient( $transient_name, $geocoded_address, MONTH_IN_SECONDS );
                                } else {
                                    throw new Exception( __( "Geocoding error", 'noo' ) );
                                }
                                break;
                            default :
                                throw new Exception( __( "Geocoding error", 'noo' ) );
                                break;
                        }
                    } else {
                        throw new Exception( __( "Geocoding error", 'noo' ) );
                    }
                } else {
                    throw new Exception( __( "Geocoding error", 'noo' ) );
                }
            }
        } catch ( Exception $e ) {
            return false;
        }

        $address                      = array();
        $address['lat']               = sanitize_text_field( $geocoded_address->results[0]->geometry->location->lat );
        $address['long']              = sanitize_text_field( $geocoded_address->results[0]->geometry->location->lng );
        $address['formatted_address'] = sanitize_text_field( $geocoded_address->results[0]->formatted_address );

        if ( ! empty( $geocoded_address->results[0]->address_components ) ) {
            $address_data             = $geocoded_address->results[0]->address_components;
            $street_number            = false;
            $address['street']        = false;
            $address['city']          = false;
            $address['state_short']   = false;
            $address['state_long']    = false;
            $address['zipcode']       = false;
            $address['country_short'] = false;
            $address['country_long']  = false;

            foreach ( $address_data as $data ) {
                switch ( $data->types[0] ) {
                    case 'street_number' :
                        $address['street'] = sanitize_text_field( $data->long_name );
                        break;
                    case 'route' :
                        $route = sanitize_text_field( $data->long_name );

                        if ( ! empty( $address['street'] ) ) {
                            $address['street'] = $address['street'] . ' ' . $route;
                        } else {
                            $address['street'] = $route;
                        }
                        break;
                    case 'sublocality_level_1' :
                    case 'locality' :
                        $address['city'] = sanitize_text_field( $data->long_name );
                        break;
                    case 'administrative_area_level_1' :
                        $address['state_short'] = sanitize_text_field( $data->short_name );
                        $address['state_long']  = sanitize_text_field( $data->long_name );
                        break;
                    case 'postal_code' :
                        $address['postcode'] = sanitize_text_field( $data->long_name );
                        break;
                    case 'country' :
                        $address['country_short'] = sanitize_text_field( $data->short_name );
                        $address['country_long']  = sanitize_text_field( $data->long_name );
                        break;
                }
            }
        }

        return $address;
    }
endif;

if ( ! function_exists( 'jm_job_location_save_geo_data' ) ) :
    function jm_job_location_save_geo_data( $term_id, $tt_id, $taxonomy ) {
        if ( 'job_location' === $taxonomy && jm_geolocation_enabled() ) {
            if ( function_exists( 'get_term_meta' ) ) {
                // $geolocation = get_term_meta( $term_id, '_geolocation', true );

                // if( empty( $geolocation ) ) {
                $term = get_term( $term_id, 'job_location' );
                if ( $term && ! is_wp_error( $term ) ) {
                    $geolocation = jm_get_geolocation( $term->slug );

                    update_term_meta( $term_id, '_geolocation', $geolocation );
                }
                // }
            } else {
                // Support for WordPress version 4.3 and older.
                $noo_job_geolocation = get_option( 'noo_job_geolocation' );
                if ( ! $noo_job_geolocation ) {
                    $noo_job_geolocation = array();
                }

                $term = get_term( $term_id, 'job_location' );
                if ( $term && ! is_wp_error( $term ) ) {
                    if ( ! isset( $noo_job_geolocation[ $term->slug ] ) ) {
                        $location_geo_data = jm_get_geolocation( $term->name );
                        if ( $location_geo_data && ! is_wp_error( $location_geo_data ) ) {
                            $noo_job_geolocation[ $term->slug ] = $location_geo_data;
                        }
                    }
                }

                //update geo option
                update_option( 'noo_job_geolocation', $noo_job_geolocation );
            }

            delete_transient( 'jm_transient_job_markers' );
        }
    }

    add_action( 'created_term', 'jm_job_location_save_geo_data', 10, 3 );
    add_action( 'edit_term', 'jm_job_location_save_geo_data', 10, 3 );
endif;

if ( ! function_exists( 'jm_location_enqueue_scripts' ) ) :
    function jm_location_enqueue_scripts() {
        if ( is_page() && ( jm_is_job_posting_page() || jm_is_resume_posting_page() || get_the_ID() == Noo_Member::get_member_page_id() ) ) {
            wp_enqueue_script( 'google-map' );
        }
    }

    add_action( 'wp_enqueue_scripts', 'jm_location_enqueue_scripts', 100 );
endif;

if ( ! function_exists( 'jm_job_render_field_job_location' ) ) :
    function jm_job_render_field_job_location( $field = array(), $field_id = '', $value = array(), $form_type = '', $object = array() ) {
        $company_location = false;
        $checkbox_value   = false;
        if ( ! empty( $object ) && isset( $object['ID'] ) && apply_filters('noo_allow_job_use_company_location', true) ) {
            $job_id         = absint( $object['ID'] );
            $checkbox_label = __( 'The same as company location', 'noo' );
            $checkbox_id    = '_use_company_location';

            $company_id         = jm_get_employer_company();
            $location_term_id   = ! empty( $company_id ) ? get_post_meta( $company_id, '_address', true ) : '';
            $location_term      = ! empty( $location_term_id ) ? get_term( $location_term_id, 'job_location' ) : '';
            $location_term      = ! empty( $location_term ) ? $location_term->term_id : '';

            if ( ! empty( $location_term ) )  :
                $company_location = true;
                $checkbox_value = empty( $job_id ) ? 1 : get_post_meta( $job_id, $checkbox_id, true );
                if ( $checkbox_value && empty( $value ) ) {
                    $value = array( $location_term );
                }
                ?>
                <input name="<?php echo $checkbox_id; ?>" type="hidden" value="0"/>
                <div class="form-control-flat">
                    <label class="checkbox">
                        <input id="use_company_location" name="<?php echo $checkbox_id; ?>" type="checkbox" <?php checked( $checkbox_value ); ?> value="1"/><i></i>
                        <?php echo esc_html( $checkbox_label ); ?>
                    </label>
                </div>

            <?php endif;
        }

        ?>
        <div id="job_location_field" class="<?php echo $checkbox_value ? 'hidden' : ''; ?> job_location_field">
            <?php
            $allow_user_input = strpos( $field['type'], 'input' ) !== false;
            $allow_multiple_select = strpos( $field['type'], 'multi' ) !== false;
            $field['type']    = strpos( $field['type'], 'single' ) !== false ? 'select' : 'multiple_select';
            if ($field['name']=='job_location'){
                $name = 'location';
                if ($allow_multiple_select) {
                    $name = 'location[]';
                }

                $selected = $value;
                if ($job_id) {
                    $cats = get_the_terms($job_id, 'job_location');
                    if (!empty($cats) && !is_wp_error($cats)) {
                        foreach ((array)$cats as $cat) {
                            $selected[] = $cat->term_id;
                        }
                    }

                }
                $required = $field['required'] ? 'required' : '';
                $location_args = array(
                    'hide_empty'      => 0,
                    'echo'            => 1,
                    'selected'        => $selected,
                    'hierarchical'    => 1,
                    'name'            => $name,
                    'id'              => 'noo-field-job_location',
                    'class'           => 'form-control noo-select form-control-chosen',
                    'depth'           => 0,
                    'taxonomy'        => 'job_location',
                    'value_field'     => 'term_id',
                    'required'          => $required,
                    'orderby' => 'name',
                    'multiple' => $allow_multiple_select,
                    'walker' => new Noo_Walker_TaxonomyDropdown(),
                ); ?>
                <?php  wp_dropdown_categories( $location_args); ?>


                <?php
            }else{
                noo_render_select_field( $field, $field_id, $value, $form_type );
            }
            if ( $form_type != 'search' && $allow_user_input ) {
                jm_job_add_new_location();
            } ?>
        </div>
        <?php if ( $company_location ) : ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery("#use_company_location").change(function () {
                        if (jQuery(this).is(":checked")) {
                            jQuery("#job_location_field").addClass('hidden');
                        } else {
                            jQuery("#job_location_field").removeClass('hidden');
                        }
                    }).change();
                });
            </script>
        <?php endif;
    }

    add_filter( 'noo_render_field_job_location', 'jm_job_render_field_job_location', 10, 5 );
    add_filter( 'noo_render_field_multi_location_input', 'jm_job_render_field_job_location', 10, 5 );
    add_filter( 'noo_render_field_multi_location', 'jm_job_render_field_job_location', 10, 5 );
    add_filter( 'noo_render_field_single_location_input', 'jm_job_render_field_job_location', 10, 5 );
    add_filter( 'noo_render_field_single_location', 'jm_job_render_field_job_location', 10, 5 );
endif;

if ( ! function_exists( 'jm_job_add_new_location' ) ) :
    function jm_job_add_new_location( $data_type = 'id' ) {
        ?>
        <p class="help-block add-new-location">
            <a class="add-new-location-btn btn-map" href="#"><?php esc_html_e( '+ Add New Location', 'noo' ) ?></a>
        </p>
        <?php noo_get_layout( 'forms/job_form_maps_picker' ); ?>
        <?php
    }
endif;

if ( ! function_exists( 'jm_job_get_term_geolocation' ) ) :
    function jm_job_get_term_geolocation( $term = null ) {
        $term_id = is_object( $term ) ? $term->term_id : ( is_numeric( $term ) ? $term : 0 );
        if ( empty( $term_id ) ) {
            return false;
        }

        $term = is_object( $term ) ? $term : get_term( $term_id, 'job_location' );
        if ( empty( $term ) || is_wp_error( $term ) ) {
            return false;
        }
        $geolocation = false;
        if ( function_exists( 'get_term_meta' ) ) {
            $geolocation = get_term_meta( $term_id, '_geolocation', true );

            if ( empty( $geolocation ) ) {
                $geolocation = jm_get_geolocation( $term->slug );

                update_term_meta( $term_id, '_geolocation', $geolocation );
            }
        } else {
            // Support for WordPress version 4.3 and older.
            $noo_job_geolocation = get_option( 'noo_job_geolocation' );
            if ( ! empty( $noo_job_geolocation ) && isset( $noo_job_geolocation[ $term->slug ] ) ) {
                $geolocation = $noo_job_geolocation[ $term->slug ];
            } else {
                $geolocation = jm_get_geolocation( $term->slug );

                $noo_job_geolocation                = empty( $noo_job_geolocation ) ? array() : $noo_job_geolocation;
                $noo_job_geolocation[ $term->slug ] = $geolocation;

                update_option( 'noo_job_geolocation', $noo_job_geolocation );
            }
        }

        return $geolocation;
    }
endif;
if(!function_exists('jm_build_company_map_data')):
    function jm_build_company_map_data(){
        if ( false !== ( $result = get_transient( 'jm_transient_company_markers' ) ) ) {
            return $result;
        }
        $args = array(
            'post_type' 			=> 'noo_company',
            'nopaging'    			=> true,
            'post_status'   		=> 'publish',
        	'posts_per_page' 		=> -1,
        	'ignore_sticky_posts' 	=> true,
        	'no_found_rows' 		=> true,
        );
        $markers = array();
        $r = new WP_Query(apply_filters('jm_build_company_map_data_query_args', $args) );
        
        if($r->have_posts()):
            while ($r->have_posts()):
                $r->the_post();
                global $post;
                $post_id = $post->ID;
                $lat     = noo_get_post_meta( $post_id, '_full_address_lat', '' );
                $long     = noo_get_post_meta( $post_id, '_full_address_lon', '' );
                $company_logo = Noo_Company::get_company_logo( $post_id );
                $company_url  = get_permalink( $post_id );
                $company_name = noo_get_the_company_name( $post_id );
                $total_job = Noo_Company::get_company_jobs($post_id, array(), -1, 'publish');
                $total_job = count($total_job);
                $slogan = noo_get_post_meta($post_id,'_slogan');
                $marker    = array(
                    'post_type'		=> 'company',
                    'latitude' 		=> $lat,
                    'longitude' 	=> $long,
                    'title' 		=> html_entity_decode(get_the_title($post->ID)),
                    'image' 		=> $company_logo,
                    'company_url' 	=> $company_url,
                    'total_job' 	=> $total_job,
                    'slogan' 		=> $slogan,
                    'company' 		=> html_entity_decode($company_name),
                );
                $markers[] = $marker;
            endwhile;
            wp_reset_postdata();
        endif;

        $result = json_encode( $markers );
        set_transient( 'jm_transient_company_markers', $result, DAY_IN_SECONDS );
        return $result;
    }
endif;
if ( ! function_exists( 'jm_build_job_map_data' ) ) :
    function jm_build_job_map_data($query='') {
        if(!empty($query)){
            $r = $query;
        }else{
            $args    = array(
                'post_type'   			=> 'noo_job',
                'post_status' 			=> 'publish',
            	'posts_per_page' 		=> -1,
            	'ignore_sticky_posts' 	=> true,
            	'no_found_rows' 		=> true,
            );
            $r = new WP_Query(apply_filters('jm_build_job_map_data_query_args', $args) );

        }
        
        $markers = array();

        if ( $r->have_posts() ):
            while ( $r->have_posts() ):
                $r->the_post();
                global $post;

                $post_id = $post->ID;
                // Get lat, long from taxonomy
                $is_using_company_address = noo_get_post_meta($post_id, '_use_company_address', '');
                if($is_using_company_address){
                    $company_id = jm_get_job_company($post_id);
                    $job_location = noo_get_post_meta($company_id, 'full_address', '');
                    $lat = noo_get_post_meta($company_id, '_full_address_lat', '');
                    $long = noo_get_post_meta($company_id, '_full_address_lon', '');
                } else {
                    $job_location = noo_get_post_meta($post_id, 'full_address', '');
                    $lat = noo_get_post_meta($post_id, '_full_address_lat', '');
                    $long = noo_get_post_meta($post_id, '_full_address_lon', '');
                }

                $job_locations = get_the_terms( $post_id, 'job_location' );
                $term_url = array();
                if ( $job_locations && ! is_wp_error( $job_locations ) ) {
                    $term_name = array();
                    foreach ( $job_locations as $job_location ) {
                        $term_url[] = get_term_link( $job_location->term_id, 'job_location' );
                    }
                }
                
                if(isset($term_url) && !empty($term_url)) {
                	$term_url = implode(',',(array)$term_url);
                }
                
                if ( empty( $long ) or empty( $lat ) ) {

                    $job_location_geo_data = jm_job_get_term_geolocation( $job_location );
                    
                    if ( empty( $job_location_geo_data ) or is_wp_error( $job_location_geo_data ) ) {
                        continue;
                    }

                    $long = isset($job_location_geo_data['long']) ? $job_location_geo_data['long'] : '';
                    $lat  = isset($job_location_geo_data['lat']) ? $job_location_geo_data['lat'] : '';
                    
                }
                $company_logo = '';
                $company_url  = '';
                $company_name = '';
                $company_id   = jm_get_job_company( $post );
                $type_name    = '';
                $type_color   = '';

                $types = jm_get_job_type( $post_id , false );
                if (!empty($types) ) {
                    foreach ($types as $type){
                        $type_name  = $type->name;
                        $type_url   = get_term_link( $type, 'job_type' );
                        $type_color = $type->color;
                    }
                }
                if ( ! empty( $company_id ) ) {
                    $company_logo_id = noo_get_post_meta($company_id, 'logo', '');
                    $company_logo = wp_get_attachment_image_url($company_logo_id);
                    if(empty($company_logo)){
                        $company_logo = NOO_ASSETS_URI . '/images/company-logo.png' ;
                    }
                    $company_url = get_permalink($company_id);
                    $company_name = noo_get_the_company_name($company_id);
                }
                $markers[] = array(
                    'post_type' 	=> 'job',
                    'latitude' 		=> $lat,
                    'longitude' 	=> $long,
                	'title' 		=> html_entity_decode(get_the_title($post->ID)),
                    'image' 		=> $company_logo,
                    'type' 			=> $type_name,
                    'type_url' 		=> $type_url,
                    'type_color' 	=> $type_color,
                    'url' 			=> get_permalink($post->ID),
                    'company_url' 	=> $company_url,
                	'company' 		=> html_entity_decode($company_name,ENT_QUOTES),
                );
            endwhile;
           
        endif;
        
        wp_reset_postdata();
        
        $result = json_encode( $markers );
        return $result;
    }
endif;
if(!function_exists('jm_get_marker_job_data')):
    function jm_get_marker_job_data($job_id){
        $job_location = noo_get_post_meta($job_id, 'full_address', '');
        $lat = noo_get_post_meta($job_id, '_full_address_lat', '');
        $long = noo_get_post_meta($job_id, '_full_address_lon', '');


        $job_locations = get_the_terms($job_id, 'job_location');
        $term_url = array();
        if ($job_locations && !is_wp_error($job_locations)) {
            $term_name = array();
            foreach ($job_locations as $job_location) {
                $term_url[] = get_term_link($job_location->term_id, 'job_location');
            }
        }
        if (isset($term_url) && !empty($term_url)) $term_url = implode(',', (array)$term_url);

        if (empty($long) or empty($lat)) {

            $job_location_geo_data = jm_job_get_term_geolocation($job_location);
            if(!is_wp_error($job_location_geo_data) && !empty($job_location_geo_data)){
                $long = isset($job_location_geo_data['long']) ? $job_location_geo_data['long'] : '';
                $lat = isset($job_location_geo_data['lat']) ? $job_location_geo_data['lat'] : '';
            }
            if (empty($job_location_geo_data) or is_wp_error($job_location_geo_data)) {
                return false;
            }
        }
        $company_logo = '';
        $company_url = '';
        $company_name = '';
        $company_id = jm_get_job_company($job_id);
        $type_name = '';
        $type_url = '';
        $type_color = '';

        $types = jm_get_job_type($job_id, false);
        if (!empty($types)) {
            foreach ($types as $type) {
                $type_name = $type->name;
                $type_url = get_term_link($type, 'job_type');
                $type_color = $type->color;
            }
        }
        if (!empty($company_id)) {
            $company_logo_id = noo_get_post_meta($company_id, 'logo', '');
            $company_logo = wp_get_attachment_image_url($company_logo_id);
            if(empty($company_logo)){
                $company_logo = NOO_ASSETS_URI . '/images/company-logo.png' ;
            }
            $company_url = get_permalink($company_id);
            $company_name = noo_get_the_company_name($company_id);
        }

        $marker = array(
            'post_type' => 'job',
            'latitude' => $lat,
            'longitude' => $long,
            'title' => html_entity_decode(get_the_title($job_id)),
            'image' => ($company_logo),
            'type' => $type_name,
            'type_url' => $type_url,
            'type_color' => $type_color,
            'url' => get_permalink($job_id),
            'company_url' => $company_url,
            'company' => html_entity_decode($company_name),
        );
        return $marker;
    }
endif;
if ( ! function_exists( 'jm_remove_transient_job_markers' ) ) :

    /**
     * Remove job markers transient whenever a job is created or updated
     *
     * @param  int $post_id ID of the job
     */
    function jm_remove_transient_job_markers( $post_id ) {
        if ( 'noo_job' == get_post_type( $post_id ) ) {
            delete_transient( 'jm_transient_job_markers' );
        }
    }

    add_action( 'save_post', 'jm_remove_transient_job_markers', 10, 1 );
endif;

if ( ! function_exists( 'jm_search_job_location' ) ) :
    function jm_search_job_location( $search_name = '' ) {
        $data = array();
        $args = array(
            'hide_empty' => false,
        );
        if ( ! empty( $search_name ) ) {
            $args['name__like'] = $search_name;
        }
        $locations = (array) get_terms( 'job_location', $args );
        foreach ( $locations as $location ) {
            $key          = esc_attr( $location->slug );
            $data[ $key ] = $location->name;
        };

        return $data;
    }
endif;

//Job Location Term Meta Filed // Remove on version  4.5.1.4

if ( ! function_exists( 'jm_location_map_field' ) ):

    function jm_location_map_field() {

        wp_enqueue_script( 'noo-admin-location-map' );

        ?>
        <div class="form-field term-location-map-wrap">
            <label><?php _e( 'Location Map', 'noo' ); ?></label>
            <div id="jm_location_term_map" style="width: 100%; height: 300px;"></div>
        </div>

        <div class="form-field term-location-lon-wrap">
            <label for="map-lon"><?php _e( 'Longitude', 'noo' ); ?></label>
            <input type="text" name="map_lon" id="map-lon" value=""/>
        </div>

        <div class="form-field term-location-lon-wrap">
            <label for="map-lat"><?php _e( 'Latitude', 'noo' ); ?></label>
            <input type="text" name="map_lat" id="map-lat" value=""/>
        </div>
        <?php
    }

endif;

if ( ! function_exists( 'jm_location_map_edit_field' ) ) :

    function jm_location_map_edit_field( $term, $taxonomy ) {

        wp_enqueue_script( 'noo-admin-location-map' );

        $term_id = $term->term_id;

        $long = get_term_meta( $term_id, 'location_long', true );
        $lat  = get_term_meta( $term_id, 'location_lat', true );

        if ( empty( $long ) or empty( $lat ) ) {

            $job_location_geo_data = jm_job_get_term_geolocation( $term );

            if ( ! is_wp_error( $job_location_geo_data ) ) {

                $long = $job_location_geo_data['long'];
                $lat  = $job_location_geo_data['lat'];
            }
        }

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Location Map', 'noo' ); ?></label></th>
            <td>
                <div id="jm_location_term_map" style="width: 100%; height: 400px;"></div>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Longitude', 'noo' ); ?></label></th>
            <td>
                <input type="text" name="map_lon" id="map-lon" value="<?php echo esc_html( $long ); ?>"/>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label><?php _e( 'Latitude', 'noo' ); ?></label></th>
            <td>
                <input type="text" name="map_lat" id="map-lat" value="<?php echo esc_html( $lat ); ?>"/>
            </td>
        </tr>
        <?php
    }

//	add_action( 'job_location_edit_form_fields', 'jm_location_map_edit_field', 10, 3 );

endif;

if ( ! function_exists( 'jm_location_map_js' ) ) :
    function jm_location_map_js( $hook ) {
        $map_type = jm_get_location_setting('map_type','');
        if ($map_type == 'google') {
            $location_picker = jm_location_picker_options();
            $google_api = jm_get_location_setting( 'google_api', '' );
            wp_register_script( 'noo-admin-google-map', 'http' . ( is_ssl() ? 's' : '' ) . '://maps.googleapis.com/maps/api/js?language=' . get_locale() . '&libraries=places' . ( ! empty( $google_api ) ? '&key=' . $google_api : '' ), array( 'jquery' ), null, true );
            wp_register_script( 'noo-admin-location-picker', NOO_FRAMEWORK_URI . '/vendor/locationpicker.jquery.js', array(
                'jquery',
                'noo-admin-google-map',
            ), null, false );
            wp_register_script( 'noo-admin-location-map', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/noo-admin-job-location.js', array(
                'jquery',
                'noo-admin-location-picker',
            ), null, true );
            wp_localize_script('noo-admin-location-map', 'nooLocationPicker', $location_picker);
        }elseif($map_type == 'bing'){
            $bing_api = jm_get_location_setting( 'bing_api', '' );
            wp_register_script( 'bing-map-api', 'https://www.bing.com/api/maps/mapcontrol?key='.$bing_api.'&callback=JM_Bing_Map', array( 'jquery' ), null, true );
            wp_register_script( 'bing-map', NOO_FRAMEWORK_ADMIN_URI . '/assets/js/bing-map.js', array( 'jquery','bing-map-api' ), null, true );
            wp_localize_script( 'bing-map', 'JM_Bing_Value', array(
                'lat'                  => floatval( jm_get_location_setting( 'latitude', '40.714398' ) ),
                'lng'                  => floatval( jm_get_location_setting( 'longitude', '-74.005279' ) ),
                'zoom'                 => absint( jm_get_location_setting( 'zoom', '17' ) ),
            ) );
        }
    }

    add_action( 'admin_enqueue_scripts', 'jm_location_map_js' );

endif;

if ( ! function_exists( 'jm_location_picker_options' ) ) :

    function jm_location_picker_options() {

        $enable_auto_complete = jm_get_location_setting( 'enable_auto_complete', 1 );

        $country_restriction = jm_get_location_setting( 'country_restriction', '' );
        $location_type       = jm_get_location_setting( 'location_type', 'cities' );;

        return array(
            'enable_auto_complete'  => $enable_auto_complete,
            'componentRestrictions' => $country_restriction,
            'types'                 => $location_type,
            'marker_icon'           => NOO_ASSETS_URI . '/images/map-marker.png',
            'lat'                   => floatval( jm_get_location_setting( 'latitude_google', '40.714398' ) ),
            'lng'                   => floatval( jm_get_location_setting( 'longitude_google', '-74.005279' ) ),
            'zoom'                 => absint( jm_get_location_setting( 'zoom_google', '17' ) ),
        );
    }

endif;

//    new logic job location since v.4.5.1.4
//
if ( ! function_exists( 'jm_display_full_address_field' ) ) :
    function jm_display_full_address_field( $post_id = null ) {
        $fields = jm_get_job_custom_fields();
        $disable = true;
        if(isset($fields['_full_address'])){
            $disable = false;
        }
       
        $post_id = empty( $post_id ) ? get_the_ID() : $post_id;
        // check job use company address.
        $is_using_company_address = noo_get_post_meta($post_id, '_use_company_address', '');
        if($is_using_company_address){
            $company_id = jm_get_job_company($post_id);
            $address = noo_get_post_meta( $company_id, '_full_address', '' );
            $lat     = noo_get_post_meta( $company_id, '_full_address_lat', '' );
            $lon     = noo_get_post_meta( $company_id, '_full_address_lon', '' );
        } else{
            $address = noo_get_post_meta( $post_id, '_full_address', '' );
            $lat     = noo_get_post_meta( $post_id, '_full_address_lat', '' );
            $lon     = noo_get_post_meta( $post_id, '_full_address_lon', '' );
        }
        $zoom = jm_get_location_setting( 'zoom_google','17');

        if ( ! empty( $address ) && ! empty( $lat ) && ! empty( $lon ) && (!$disable) ) :
            ?>
            <div class="single-job-location">
                <h3 class="noo-job-location"><?php echo esc_html__('Job Location', 'noo'); ?></h3>
                <div class="noo-job-full-address-wrap">
                    <?php $map_type = jm_get_location_setting('map_type',''); ?>
                    <?php if ($map_type == 'google'): 
                        wp_enqueue_script('google-map');
                        wp_enqueue_script('google-map-custom');
                        ?>
                        <div class="google-map">
                            <div id="googleMap" style="height: 250px;"
                                 data-map_style="apple"
                                 data-address="<?php echo esc_html( $address ); ?>"
                                 data-icon=""
                                 data-lat="<?php echo esc_attr( $lat ); ?>"
                                 data-lon="<?php echo esc_attr( $lon ); ?>"
                                 data-zoom="<?php echo esc_attr( $zoom ); ?>">
                            </div>
                        </div>
                    <?php else: ?>
                        <?php
                        wp_enqueue_script('bing-map'); ?>
                        <div class="noo-mb-job" data-id='_full_address' data-zoom="10" data-dragged="1">
                            <div id='_full_address' style="height: 250px;" ></div>
                            <input type="hidden"  name="<?php echo esc_attr( $address ) ?>_lat" value="<?php echo $lat; ?>" id="latitude">
                            <input type="hidden"  name="<?php echo esc_attr( $address ) ?>_lon" value="<?php echo $lon; ?>" id="longitude">
                        </div>
                    <?php endif ?>

                </div>
            </div>
        <?php
        endif;
    }

endif;

add_action( 'jm_job_detail_content_after', 'jm_display_full_address_field', 10 );