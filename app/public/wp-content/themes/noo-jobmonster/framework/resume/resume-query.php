<?php

if( !function_exists('jm_resume_pre_get_posts') ) :
    function jm_resume_pre_get_posts($query) {
        if( is_admin() ) {
            return;
        }

        if ( jm_is_resume_query( $query ) ) {
            if( Noo_Member::is_logged_in() ) {
                $user_id = get_current_user_id();

                // Candidates can view their resumes
                if( isset($query->query_vars['author']) && $query->query_vars['author'] == $user_id ) {
                    return;
                }

                // Single resume, let the resume details page decided
                if( $query->is_singular || ( count($query->query_vars['post__in']) == 1 && !empty( $query->query_vars['post__in'][0] ) ) ) {
                    return;
                }
            }

            if( $query->is_post_type_archive ) {
                if( isset($_GET['resume_category']) && !empty($_GET['resume_category']) ) {
                    $resume_category = $_GET['resume_category'];
                    $query->query_vars['meta_query'][] = array(
                        'key' => '_job_category',
                        'value' => '"' . $resume_category . '"',
                        'compare' => 'LIKE'
                    );
                }
            }

            $query = jm_resume_query_from_request( $query, $_GET );
        }
    }

    add_action( 'pre_get_posts', 'jm_resume_pre_get_posts' );
endif;

if( !function_exists('jm_is_resume_query') ) :
    function jm_is_resume_query( $query ) {
        return isset($query->query_vars['post_type']) && ($query->query_vars['post_type'] === 'noo_resume');
    }
endif;


if( !function_exists('jm_resume_query_from_request') ) :
    function jm_resume_query_from_request( &$query, $REQUEST = array() ) {
        if( empty( $query ) ) {
            return $query;
        }

        $author_in = array();
        $meta_query = array();
        if( jm_viewable_resume_enabled() ) {
            if( !is_admin() || ( defined('DOING_AJAX') && DOING_AJAX ) ) {
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key' => '_viewable',
                        'value' => 'yes',
                    )
                );
            }
        }

        if( !empty( $REQUEST ) ) {
            global $wpdb;
            $candidate_ids = array();
            $resume_ids = array();
            if( isset($REQUEST['candidate_name']) && !empty($REQUEST['candidate_name']) ) {
                $s_keyword = is_object( $query ) ? $query->query_vars['s'] : ( is_array( $query ) ? $query['s'] : '' );
                $candidate_ids = (array)$wpdb->get_col($wpdb->prepare('SELECT DISTINCT ID FROM %1$s AS u WHERE u.display_name LIKE \'%2$s\'', $wpdb->users, '%' . $s_keyword . '%'));

                if( !empty( $candidate_ids ) ) {
                    if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                        $query->query_vars['author__in'] = $candidate_ids;
                    } elseif( is_array( $query ) ) {
                        $query['author__in'] = $candidate_ids;
                    }
                }
            }
            $education = isset($REQUEST['education']) && !empty($REQUEST['education']) ? true : false;
            $experience = isset($REQUEST['experience']) && !empty($REQUEST['experience']) ? true : false;
            $skill = isset($REQUEST['skill']) && !empty($REQUEST['skill']) ? true : false;
            if( $education || $experience || $skill ) {
                $s_keyword = is_object( $query ) ? $query->query_vars['s'] : ( is_array( $query ) ? $query['s'] : '' );
                $where_string = array();
                if( $education )
                    $where_string[] = sprintf("(m.meta_key = '_education_school' AND m.meta_value LIKE '%%%s%%')",$s_keyword);
                if( $experience )
                    $where_string[] = sprintf("(m.meta_key = '_experience_employer' AND m.meta_value LIKE '%%%s%%')",$s_keyword);
                if( $skill )
                    $where_string[] = sprintf("(m.meta_key = '_skill_name' AND m.meta_value LIKE '%%%s%%')",$s_keyword);

                $query_string = "SELECT DISTINCT post_id FROM {$wpdb->postmeta} AS m WHERE " . implode(' OR ', $where_string);

                $resume_ids = (array)$wpdb->get_col($query_string);

                $resume_ids = array_merge( $resume_ids, array(0) );
                // if( !empty( $resume_ids ) ) {
                if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                    $query->query_vars['post__in'] = $resume_ids;
                } elseif( is_array( $query ) ) {
                    $query['post__in'] = $resume_ids;
                }
                // }
            }
            if( isset($REQUEST['no_content']) && !empty($REQUEST['no_content']) ) {
                if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                    $query->query['s'] = '';
                    $query->query_vars['s'] = '';
                } elseif( is_array( $query ) ) {
                    unset( $query['s'] );
                }
            }

            $resume_fields = jm_get_resume_search_custom_fields();
            foreach ($resume_fields as $field) {
                $field_id = jm_resume_custom_fields_name( $field['name'], $field );
                if( isset( $REQUEST[$field_id] ) && !empty( $REQUEST[$field_id]) ) {
                    $value = noo_sanitize_field( $REQUEST[$field_id], $field );
                    if( $field_id == 'candidate' ) {
                        $author_in = (array)$wpdb->get_col($wpdb->prepare('SELECT DISTINCT ID FROM %1$s AS u WHERE u.display_name LIKE \'%2$s\' OR u.user_email LIKE \'%2$s\'', $wpdb->users, '%' . $value . '%'));

                        $author_in = array_merge( $author_in, array( 0 ) ); // make sure to return blank when search fail.

                        continue;
                    }
                    if( $field_id == '_job_category' || $field_id == '_job_location' || $field_id == '_language') {
                        $value = !is_array( $value ) ? array( $value ) : $value;
                    }
                    if($field_id == '_job_category'){
                        $temp_meta_query = array( 'relation' => 'OR' );
                        foreach ($value as $v){
                            $term_child = get_term_children($v,'job_category');
                            $term_child = array_merge(array($v),$term_child);
                            foreach ($term_child as $child){
                                if(empty($child)) continue;
                                $temp_meta_query[] = array(
                                    'key'     => $field_id,
                                    'value'   => $child,
                                    'compare' => 'LIKE'
                                );
                            }
                        }
                        $meta_query[] = $temp_meta_query;
                        unset($field_id);
                    }elseif($field_id == '_job_location'){
                        $temp_meta_query = array( 'relation' => 'OR' );
                        foreach ($value as $v){
                            $term_child = get_term_children($v,'job_location');
                            $term_child = array_merge(array($v),$term_child);
                            foreach ($term_child as $child){
                                if(empty($child)) continue;
                                $temp_meta_query[] = array(
                                    'key'     => $field_id,
                                    'value'   => '"'.$child.'"',
                                    'compare' => 'LIKE'
                                );
                            }
                        }
                        $meta_query[] = $temp_meta_query;
                        unset($field_id);
                    }else{
                        if(is_array($value)){
                            $temp_meta_query = array( 'relation' => 'OR' );
                            foreach ($value as $v) {
                                if( empty( $v ) ) continue;
                                $temp_meta_query[]	= array(
                                    'key'     => $field_id,
                                    'value'   => '"'.$v.'"',
                                    'compare' => 'LIKE'
                                );
                            }
                            $meta_query[] = $temp_meta_query;
                        } else {
                            $meta_query[]	= array(
                                'key'     => $field_id,
                                'value'   => $value
                            );
                        }
                    }

                }
            }
            if (isset($REQUEST['current_lat'])&& !empty($REQUEST['current_lat']) && isset($REQUEST['current_lon'])&& !empty($REQUEST['current_lon'])){
                $get_distance = (isset($REQUEST['filter_distance']) && !empty($REQUEST['filter_distance'])) ? $REQUEST['filter_distance'] : 5;
                $distance =($REQUEST['current_unit']=='km')? $get_distance * 0.62137 : $get_distance;
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
                            'key'  => '_resume_address_lon',
                            'value' => $lon_1,
                            'compare' => '>',
                            'type'    => 'DECIMAL(10,5)',
                        ),
                        array(
                            'key'  => '_resume_address_lon',
                            'value' => $lon_2,
                            'compare' => '<',
                            'type'    => 'DECIMAL(10,5)',
                        ),
                        array(
                            'key' => '_resume_address_lat',
                            'value' => $lat1,
                            'compare' => '>',
                            'type'    => 'DECIMAL(10,5)',
                        ),
                        array(
                            'key' => '_resume_address_lat',
                            'value' => $lat2,
                            'compare' => '<',
                            'type'    => 'DECIMAL(10,5)',
                        ),
                    );
            }
        }

        $meta_query = apply_filters( 'jm_resume_search_meta_query', $meta_query, $REQUEST );

        if ( ! empty( $meta_query ) ) {
            $meta_query[ 'relation' ] = 'AND';
            if ( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                $query->query_vars[ 'meta_query' ][] = $meta_query;
            } elseif ( is_array( $query ) ) {
                $query[ 'meta_query' ] = $meta_query;
            }
        }

        if( !empty( $author_in ) ) {
            if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                $query->query_vars['author__in'] = isset( $query->query_vars['author__in'] ) ? $query->query_vars['author__in'] : array();
                $query->query_vars['author__in'] = !is_array( $query->query_vars['author__in'] ) ? array( $query->query_vars['author__in'] ) : $query->query_vars['author__in'];

                $query->query_vars['author__in'] = array_merge( $query->query_vars['author__in'], $author_in );
            } elseif( is_array( $query ) ) {
                $query['author__in'] = isset( $query['author__in'] ) ? $query['author__in'] : array();
                $query['author__in'] = !is_array( $query['author__in'] ) ? array( $query['author__in'] ) : $query['author__in'];

                $query['author__in'] = array_merge( $query['author__in'], $author_in );
            }
        }

        return $query;
    }
endif;
if(!function_exists(' jm_url_resume_filter_selected')):
    function  jm_url_resume_filter_selected($request=array(),$url=''){
        unset($request['widget_cs_field']);
        unset($request['_wp_http_referer']);
        $resume_fields = jm_get_resume_search_custom_fields();
        $http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        foreach ($resume_fields as $field) {
            $field_id = jm_resume_custom_fields_name( $field['name'], $field );
            if( isset( $request[$field_id] ) && !empty( $request[$field_id]) ) {
                $value = noo_sanitize_field( $request[$field_id], $field );
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (empty($v)) {
                            continue;
                        }
                        $name = $v;
                        if(in_array($field_id, array('_job_category', '_job_location'))){
                            $term = get_term_by('term_id',$v,substr( $field_id, 1 ));
                            $name    = (!empty($term)) ? $term->name: '';
                        }
                        $url = (!empty($url)) ? $url : $http_referer;
                        $current_url = str_replace('&'.$field_id.'%5B%5D='.$v.'','',$url);
                        echo '<li class="new"><a href="'.$current_url.'"><i  class="fa fa-times"></i> '.$name.'</a></li>';
                    }
                } else {
                    if(in_array($field_id, array('_job_category', '_job_location'))){
                        $term = get_term_by('term_id',$value,substr( $field_id, 1 ));
                        $value    = (!empty($term)) ? $term->name: '';
                    }
                    
                    $field_value = noo_convert_custom_field_setting_value($field);
                    if(is_array($field_value)){
                    	if(isset($field_value[$request[$field_id]]) ){
                    		$value = $field_value[$request[$field_id]];
                    	}
                    }elseif($value===sanitize_title($field_value)){
                    	$value = $field_value;
                    }
                    
                    $url_back = remove_query_arg($field_id,$http_referer);
                    echo '<li><a href="'.$url_back.'"><i  class="fa fa-times"></i> '.$value.'</a></li>';
                }
            }
        }
    }
endif;