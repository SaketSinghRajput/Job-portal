<?php
/**
 * Created by PhpStorm.
 * Date: 12/6/2018
 * Time: 5:16 PM
 */
if( !function_exists('jm_company_query_from_request') ) :
    function jm_company_query_from_request( &$query, $REQUEST = array() ) {
        if( empty( $query ) ) {
            return $query;
        }

        $author_in = array();
        $meta_query = array();
        if( !empty( $REQUEST ) ) {
            global $wpdb;
            if( isset($REQUEST['no_content']) && !empty($REQUEST['no_content']) ) {
                if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
                    $query->query['s'] = '';
                    $query->query_vars['s'] = '';
                } elseif( is_array( $query ) ) {
                    unset( $query['s'] );
                }
            }

            $company_fields = jm_get_company_search_custom_fields();
            foreach ($company_fields as $field) {
                $field_id = jm_company_custom_fields_name( $field['name'], $field );
                if( isset( $REQUEST[$field_id] ) && !empty( $REQUEST[$field_id]) ) {
                    $value = noo_sanitize_field( $REQUEST[$field_id], $field );
                    if( $field_id == '_job_category' || $field_id == '_job_location' ) {
                        $value = !is_array( $value ) ? array( $value ) : $value;
                    }
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
                            'value'   => $value,
                            'compare' => 'LIKE'
                        );
                    }
                }
            }
        }

        $meta_query = apply_filters( 'jm_company_meta_query', $meta_query, $REQUEST );

        if( is_object( $query ) && get_class( $query ) == 'WP_Query' ) {
            $query->query_vars['meta_query'][] = $meta_query;
        } elseif( is_array( $query ) ) {
            $query['meta_query'] = $meta_query;
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