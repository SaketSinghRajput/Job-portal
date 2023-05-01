<?php

function noo_clone_resume( $post_id ) {
    global $wpdb;
    $post            = get_post( $post_id );
    $status = ((bool)jm_get_resume_setting( 'resume_approve','' )) ? 'pending' : $post->post_status;
    $current_user    = wp_get_current_user();
    $new_post_author = $current_user->ID;

    $post_prefix = esc_html__( ' New', 'noo' );

    /*
     * if post data exists, create the post duplicate
     */
    if ( isset( $post ) && $post != null ) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name . $post_prefix,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => $status,
            'post_title'     => $post->post_title . $post_prefix,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        /*
         * insert the post by wp_insert_post() function
         */
        $new_post_id = wp_insert_post( $args );

        /*
         * get all current post terms ad set them to the new post draft
         */
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
            wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
        if ( count( $post_meta_infos ) != 0 ) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ( $post_meta_infos as $meta_info ) {
                $meta_key = $meta_info->meta_key;
                if ( $meta_key == '_wp_old_slug' ) {
                    continue;
                }
                if( $meta_key == '_noo_views_count'){
                    continue;
                }
                if( $meta_key == '_viewable'){
                    continue;
                }
                $meta_value      = addslashes( $meta_info->meta_value );
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode( " UNION ALL ", $sql_query_sel );
            $wpdb->query( $sql_query );
        }
    }

    return $new_post_id;
}

function noo_ajax_clone_resume() {

    $resume_id = isset( $_POST['resume_id'] ) ? intval( $_POST['resume_id'] ) : 0;

    if ( empty( $resume_id ) ) {
        $response = array(
            'status'  => 'error',
            'message' => esc_html__( 'Resume not found.', 'noo' )
        );

    } else {

        $cloned   = noo_clone_resume( $resume_id );
        $redirect = Noo_Member::get_endpoint_url( 'manage-resume' );

        if ( $cloned ) {
            noo_message_add( esc_html__( 'Resume clone success.', 'noo' ) );

            $response = array(
                'status'  => 'success',
                'url'     => $redirect,
                'message' => esc_html__( ' Resume clone success.', 'noo' )
            );

        } else {
            $response = array(
                'status'  => 'error',
                'message' => esc_html__( 'Resume clone action failed', 'noo' )
            );
        }
        wp_send_json( $response );
    }

}

add_action( 'wp_ajax_noo_ajax_clone_resume', 'noo_ajax_clone_resume' );