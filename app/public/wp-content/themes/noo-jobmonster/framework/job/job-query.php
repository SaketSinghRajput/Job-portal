<?php
if ( ! function_exists( 'jm_job_pre_get_posts' ) ) :
	/**
	 * 
	 * @param WP_Query $query
	 * @return mixed
	 */
    function jm_job_pre_get_posts( $query ) {
        if ( is_admin() ) {
            return $query;
        }

        if ( jm_is_job_query( $query ) ) {
        	if($query->is_main_query()){
        		if ( $query->is_singular ) {
        			if ( ! $query->is_preview && empty( $query->query_vars[ 'post_status' ] ) ) {
        				// add expired to viewable link
        				$post_status = array( 'publish');
        				
        				if ( current_user_can( 'edit_posts' ) ) {
        					$post_status[] = 'pending';
        					$post_status[] = 'expired';
        				}
        				
        				if(noo_get_option( 'noo_jobs_show_expired', false )) {
        					$post_status[] = 'expired';
        				}
        				
        				$query->set( 'post_status', $post_status );
        			}
        			
        			return $query;
        		}
        		if ( noo_get_option( 'noo_jobs_orderby_featured', false ) ) {
        			$query->set( 'orderby', 'meta_value date' );
        			$query->set( 'meta_key', '_featured' );
        		}
        	}
           
            if ( is_post_type_archive( 'noo_job' ) && noo_get_option( 'noo_jobs_show_expired', false ) ) {
                $post_status = array( 'publish', 'expired' );
                $query->set( 'post_status', $post_status );
            }

            $paged = get_query_var( 'page' );
            if ( ! empty( $paged ) ) {
                $query->set( 'paged', $paged );
            }
            $query = jm_job_query_from_request( $query, $_GET );

        }
    }
    add_action( 'pre_get_posts', 'jm_job_pre_get_posts' );
endif;

if ( ! function_exists( 'jm_is_job_query' ) ) :
    function jm_is_job_query( &$query ) {

        if (  isset( $query->query_vars[ 'post_type' ] ) && $query->query_vars[ 'post_type' ] === 'noo_job' ) {
            return true;
        }

        if($query->is_tax( get_object_taxonomies( 'noo_job' ))){
        	return true;
        }
        
        $page_on_front = get_option( 'page_on_front' );
        $home_query = $page_on_front && $page_on_front == $query->get( 'page_id' );
        
        if ( $home_query && get_post_field( 'post_name', $query->get( 'page_id' ) ) == jm_get_job_setting( 'archive_slug' ) ) {
            $query->set( 'post_type', 'noo_job' );
            $query->set( 'page_id', '' ); //Empty

            //Set properties that describe the page to reflect that
            //we aren't really displaying a static page
            $query->is_page              = 0;
            $query->is_singular          = 0;
            $query->is_post_type_archive = 1;
            $query->is_archive           = 1;

            return true;
        }

        return false;
    }
endif;

if ( ! function_exists( 'jm_user_job_query' ) ) :
    function jm_user_job_query( $employer_id = '', $is_paged = true, $status = array(), $per_page = null ) {
        if ( empty( $employer_id ) ) {
            $employer_id = get_current_user_ID();
        }
        $company_id  = jm_get_employer_company( $employer_id );
        $job_ids = Noo_Company::get_company_jobs($company_id, array(), -1, $status);
        $args = array(
            'post_type'  => 'noo_job',
            'post__in'   => array_merge($job_ids,array(0)),
        );

        if ( ! empty( $per_page ) ) {
            $args[ 'posts_per_page' ] = - 1;
        }

        if ( $is_paged ) {
            if ( is_front_page() || is_home() ) {
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );
            } else {
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            }
            $args[ 'paged' ] = $paged;
        }

        if ( ! empty( $status ) ) {
            $args[ 'post_status' ] = $status;
        } else {
            $args[ 'post_status' ] = array( 'publish', 'pending', 'pending_payment', 'expired', 'inactive' ,'draft');
        }
        
        $user_job_query = new WP_Query( $args );

        return $user_job_query;
    }
endif;

if ( ! function_exists( 'jm_application_job_list' ) ) :
    function jm_application_job_list( $employer_id = '', $status=array() ) {
        if (empty($employer_id)) {
            $employer_id = get_current_user_ID();
        }
        $company_id = jm_get_employer_company($employer_id);
        $job_ids = Noo_Company::get_company_jobs($company_id, array(), -1, $status);
        $jobs_list = array();
        if (!empty($job_ids)) {
            $jobs_list = get_posts(array(
                'post_type' => 'noo_job',
                'post__in' => $job_ids,
                'posts_per_page' => -1,
                'suppress_filters' => false,
                'post_status' => array('publish', 'pending', 'pending_payment', 'expired', 'inactive', 'draft'),
            ));
        }
        return $jobs_list;
    }
endif;

if (!function_exists('jm_remove_transient_application_job_list_by_employer')) :

	/**
	 * Remove application job list transient
	 */
	function jm_remove_transient_application_job_list_by_employer($post)
	{
		$employer_id = get_post_field('post_author', $post->ID);
		delete_transient('jm_application_job_list_by_employer_' . $employer_id);
	}
	
	add_action('jm_delete_job_transient', 'jm_remove_transient_application_job_list_by_employer');
endif;

if ( ! function_exists( 'jm_job_query_from_request' ) ) :
    function jm_job_query_from_request( &$query, $REQUEST = array() ) {

        $tax_query = array();
        $tax_list  = jm_get_job_taxonomies();
        foreach ( $tax_list as $term ) {
            $tax_key = str_replace( 'job_', '', $term );
            if ( isset( $REQUEST[ $tax_key ] ) && ! empty( $REQUEST[ $tax_key ] ) ) {

                if (is_array($REQUEST[$tax_key]) && empty($REQUEST[$tax_key][0])) {
                    continue;
                }
                $tax_query[] = array(
                    'taxonomy' => $term,
                    'field' => 'slug',
                    'terms' => $REQUEST[$tax_key],

                );

                unset( $REQUEST[ $tax_key ] );
            }
        }

        $tax_query = apply_filters( 'jm_job_search_tax_query', $tax_query, $REQUEST );
        if ( ! empty( $tax_query ) ) {
            $tax_query[ 'relation' ] = 'AND';
            if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                $query->tax_query->queries        = $tax_query;
                $query->query_vars[ 'tax_query' ] = $query->tax_query->queries;

                // tag is a reserved keyword so we'll have to remove it from the query
                unset( $query->query[ 'tag' ] );
                unset( $query->query_vars[ 'tag' ] );
                unset( $query->query_vars[ 'tag__in' ] );
                unset( $query->query_vars[ 'tag_slug__in' ] );
            } elseif ( is_array( $query ) ) {
                $query[ 'tax_query' ] = $tax_query;
            }
        }
        $meta_query = array();
        $get_keys   = array_keys( $REQUEST );

        $job_fields = jm_get_job_search_custom_fields();
        foreach ( $job_fields as $field ) {
            $field_id = jm_job_custom_fields_name( $field[ 'name' ], $field );
            if ( isset( $REQUEST[ $field_id ] ) && ! empty( $REQUEST[ $field_id ] ) ) {
                $value = noo_sanitize_field( $REQUEST[ $field_id ], $field );
                if ( is_array( $value ) ) {
                    $temp_meta_query = array( 'relation' => 'OR' );
                    foreach ( $value as $v ) {
                        if ( empty( $v ) ) {
                            continue;
                        }
                        $temp_meta_query[] = array(
                            'key'     => $field_id,
                            'value'   => '"' . $v . '"',
                            'compare' => 'LIKE',
                        );
                    }
                    $meta_query[] = $temp_meta_query;
                } else {
                    $meta_query[] = array(
                        'key'   => $field_id,
                        'value' => $value,
                        'compare' => 'LIKE',
                    );
                }
            } elseif ( ( isset( $field[ 'type' ] ) && $field[ 'type' ] == 'datepicker' ) && ( isset( $REQUEST[ $field_id . '_start' ] ) || isset( $REQUEST[ $field_id . '_end' ] ) ) ) {
                $value_start = isset( $REQUEST[ $field_id . '_start' ] ) && ! empty( $REQUEST[ $field_id . '_start' ] ) ? $REQUEST[ $field_id . '_start' ] : 0;
                $value_end   = isset( $REQUEST[ $field_id . '_end' ] ) && ! empty( $REQUEST[ $field_id . '_end' ] ) ? $REQUEST[ $field_id . '_end' ] : 0;
                if ( ! empty( $value_start ) || ! empty( $value_end ) ) {
                    if ( $field_id == 'date' ) {
                        $date_query = array();
                        if ( ! empty( $value_start ) ) {
                            $start                 = is_numeric( $value_start ) ? date( 'Y-m-d', $value_start ) : $value_start;
                            $date_query[ 'after' ] = date( 'Y-m-d', strtotime( $start . ' -1 day' ) );
                        }
                        if ( isset( $value_end ) && ! empty( $value_end ) ) {
                            $end                    = is_numeric( $value_end ) ? date( 'Y-m-d', $value_end ) : $value_end;
                            $date_query[ 'before' ] = date( 'Y-m-d', strtotime( $end . ' +1 day' ) );
                        }

                        if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                            $query->query_vars[ 'date_query' ][] = $date_query;
                        } elseif ( is_array( $query ) ) {
                            $query[ 'date_query' ] = $date_query;
                        }
                    } else {
                        $value_start = ! empty( $value_start ) ? noo_sanitize_field( $value_start, $field ) : 0;
                        $value_start = ! empty( $value_start ) ? strtotime( "midnight", $value_start ) : 0;
                        $value_end   = ! empty( $value_end ) ? noo_sanitize_field( $value_end, $field ) : 0;
                        $value_end   = ! empty( $value_end ) ? strtotime( "tomorrow", strtotime( "midnight", $value_end ) ) - 1 : strtotime( '2090/12/31' );

                        $meta_query[] = array(
                            'key'     => $field_id,
                            'value'   => array( $value_start, $value_end ),
                            'compare' => 'BETWEEN',
                            'type'    => 'NUMERIC',
                        );
                    }
                }
            }
        }
        if ( isset($_REQUEST['current_lat']) && !empty($REQUEST['current_lat']) && isset($_REQUEST['current_lon'])&& !empty($REQUEST['current_lon'])){
            $request_distance =(empty($REQUEST['filter_distance']) || !isset($REQUEST['filter_distance'])) ? 100 : $REQUEST['filter_distance'];
            $distance =($REQUEST['current_unit']=='km')?  $request_distance  * 0.62137 :  $request_distance ;
            $current_lat = $REQUEST['current_lat'];
            $current_lon = $REQUEST['current_lon'];
            $lon_1 = $current_lon - $distance/abs(cos(deg2rad($current_lat))*69);
            $lon_2 = $current_lon + $distance/abs(cos(deg2rad($current_lat))*69);
            $lat1 = $current_lat - ($distance/69);
            $lat2 = $current_lat + ($distance/69);
            $meta_query[] =
                array(
                    'relation' => 'AND',
                    array(
                        'key'  => '_full_address_lon',
                        'value' => $lon_1,
                        'compare' => '>',
                        'type'    => 'DECIMAL(10,5)',
                    ),
                    array(
                        'key'  => '_full_address_lon',
                        'value' => $lon_2,
                        'compare' => '<',
                        'type'    => 'DECIMAL(10,5)',
                    ),
                    array(
                        'key' => '_full_address_lat',
                        'value' => $lat1,
                        'compare' => '>',
                        'type'    => 'DECIMAL(10,5)',
                    ),
                    array(
                        'key' => '_full_address_lat',
                        'value' => $lat2,
                        'compare' => '<',
                        'type'    => 'DECIMAL(10,5)',
                    ),
                );
        }
        $meta_query = apply_filters( 'jm_job_search_meta_query', $meta_query, $REQUEST );
        if ( ! empty( $meta_query ) ) {
            $meta_query[ 'relation' ] = 'AND';
            if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                $query->query_vars[ 'meta_query' ][] = $meta_query;
            } elseif ( is_array( $query ) ) {
                $query[ 'meta_query' ] = $meta_query;
            }
        }

        return apply_filters( 'jm_job_search_query', $query, $REQUEST );
    }
endif;
if (!function_exists('noo_employer_job_inactive_query')) :
    function noo_employer_job_inactive_query($query)
    {
        if (!is_admin() && is_user_logged_in() && isset($_GET['job_id'])) {
            $job = get_post((int) $_GET['job_id']);
            if ($job && $job->post_author == get_current_user_id()) {
            	$query->set('post_status', array('publish', 'pending', 'pending_payment', 'expired', 'inactive', 'draft'));
            }
        }
    }
    add_action('pre_get_posts', 'noo_employer_job_inactive_query');
endif;
if(!function_exists(' jm_url_job_filter_selected')):
    function  jm_url_job_filter_selected($request=array(),$url=''){
        $tax_list  = jm_get_job_taxonomies();
        unset($request['widget_cs_field']);
        unset($request['_wp_http_referer']);
        $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        foreach ( $tax_list as $term ) {
            $tax_key = str_replace('job_', '', $term);
            if (isset($request[$tax_key]) && !empty($request[$tax_key])) {
                if (is_array($request[$tax_key]) && empty($request[$tax_key][0])) {
                    continue;
                }
                if (!is_array($request[$tax_key])) {
                    $term = get_term_by('slug',$request[$tax_key],'job_'.$tax_key);
                    $name    = (!empty($term)) ? $term->name: '';
                    $url_back = remove_query_arg($tax_key,$http_referer);
                    echo '<li class="new"><a href="'.esc_url($url_back).'"><span class="close-value linearicons-cross"></span>'.$name.'</a></li>';
                }else{
                    foreach ($request[$tax_key] as $key=>$value) {
                        $term = get_term_by('slug',$value,'job_'.$tax_key);
                        $name    = (!empty($term)) ? $term->name: '';
                        $url = (!empty($url)) ? $url : $http_referer;
                        $current_url = str_replace('&'.$tax_key.'%5B%5D='.$value.'','',$url);
                        echo '<li><a href="'.esc_url($current_url).'"><i class="fa fa-times"></i> '.$name.'</a></li>';
                    }
                }
            }
        }
        $job_fields = jm_get_job_search_custom_fields();
        foreach ( $job_fields as $field ) {
            $field_id = jm_job_custom_fields_name($field['name'], $field);
            if (isset($request[$field_id]) && !empty($request[$field_id])) {
            
                $value = noo_sanitize_field($request[$field_id], $field);
             
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (empty($v)) {
                            continue;
                        }
                        $url = (!empty($url)) ? $url : $http_referer;
                        $current_url = str_replace('&'.$field_id.'%5B%5D='.$v.'','',$url);
                        echo '<li class="new"><a href="'.$current_url.'"><span class="close-value linearicons-cross"></span>'.$v.'</a></li>';
                    }
                } else {
                	$field_value = noo_convert_custom_field_setting_value($field);
                	if(is_array($field_value) ){
                		if( isset($field_value[$request[$field_id]])){
                			$value = $field_value[$request[$field_id]];
                		}
                	}elseif($value===sanitize_title($field_value)){
                		$value = $field_value;
                	}
                	
                	$url_back = remove_query_arg( $field_id, $http_referer );
                    echo '<li><a href="'.$url_back.'"><i  class="fa fa-times"></i> '.$value.'</a></li>';
                }
            }
        }
        ?>
        <?php
    }
endif;