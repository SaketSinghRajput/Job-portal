<?php
/*
Plugin Name: Noo Import Jobmonster - WP All Import
Plugin URI: http://nootheme.com/
Description: Supporting imports into the JobMonster theme.
Version: 1.0.0
Author: KENT
*/

if( !class_exists( 'RapidAddon' ) ) {
    include "rapid-addon.php";
}

$jobmonster_addon = new RapidAddon( 'JobMonster Add-On', 'jobmonster_addon' );

$jobmonster_addon->disable_default_images();

// $jobmonster_addon->add_field( 'job_category', 'Job Category', 'text' );

// $jobmonster_addon->add_field( 'job_type', 'Job Type', 'text' );

// $jobmonster_addon->add_field( 'job_location', 'Job Location', 'text' );

$jobmonster_addon->add_field( '_application_email', 'Application Notify Email', 'text', null, 'Email to receive application notification. Leave it blank to use Employer\'s profile email.' );

$jobmonster_addon->add_field( '_closing', 'Job Closing Date', 'text' );

$jobmonster_addon->add_field( '_expires', 'Job Expires Date', 'text' );

$jobmonster_addon->add_field( '_featured', 'Featured', 'text');

$jobmonster_addon->add_field( '_noo_views_count', 'Views', 'text');

$jobmonster_addon->add_field( '_custom_application_url', 'Custom Application link', 'text');

// $jobmonster_addon->add_options(
//         null,
//         'Social Media Options', 
//         array(
//             $jobmonster_addon->add_field( '_company_facebook', 'Company Facebook', 'text' ),
//             $jobmonster_addon->add_field( '_company_twitter', 'Company Twitter', 'text' ),
//             $jobmonster_addon->add_field( '_company_linkedin', 'Company LinkedIn', 'text' ),
//             $jobmonster_addon->add_field( '_company_googleplus', 'Company Google+', 'text' ),
//             $jobmonster_addon->add_field( '_company_instagram', 'Company Instagram', 'text' ),
//         )
// );

$jobmonster_addon->set_import_function( 'jm_wpai_addon_import' );

$jobmonster_addon->run( array(
        'themes' => array( 'NOO JobMonster' ),
        'post_types' => array( 'noo_job' ) 
) );

if( !function_exists( 'jm_wpai_addon_import' ) ) :
    function jm_wpai_addon_import( $post_id, $data, $import_options ) {
        
        global $jobmonster_addon;

        // ===== <<< [ Process Text Fields ] >>> ===== //
            $fields = array(
                '_application_email',
                '_featured',
                '_noo_views_count',
            );

            foreach ( $fields as $field ) :

                if ( $jobmonster_addon->can_update_meta( $field, $import_options ) ) :

                    update_post_meta( $post_id, $field, $data[$field] );

                endif;

            endforeach;

        // ===== <<< [ End Process Text Fields ] >>> ===== //


        // ===== <<< [ Process Date Fields ] >>> ===== //
            $fields_date = array(
                '_expires',
                '_closing'
            );

            
            foreach ( $fields_date as $field ) :

                $date = $data[$field];
                $date = strtotime( $date );

                if ( $jobmonster_addon->can_update_meta( $field, $import_options ) && !empty( $date ) ) :

                    update_post_meta( $post_id, $field, $date );

                endif;

            endforeach;

        // ===== <<< [ End Process Date ] >>> ===== //
        
        // ===== <<< [ Process Taxonomy ] >>> ===== //
            
            // ===== <<< [ Job Category ] >>> ===== //
                // if ( $jobmonster_addon->can_update_meta( 'job_category', $import_options ) ) :

                //     $job_cat = get_term_by( 'name', $data['job_category'], 'job_category' );
                                            
                //     if ( $job_cat ) :

                //         wp_set_post_terms( $post_id, array($job_cat->term_id), 'job_category' );
                    
                //     else :

                //         $job_cat_id = wp_insert_term( $data['job_category'], 'job_category', array() );
                //         wp_set_post_terms( $post_id, array($job_cat_id), 'job_category' );

                //     endif;

                // endif;

            // ===== <<< [ Job Type ] >>> ===== //
                // if ( $jobmonster_addon->can_update_meta( 'job_type', $import_options ) ) :
                //     $post_item['job_type'] = array();
                //     $job_type = get_term_by( 'name', $data['job_type'], 'job_type' );
                                            
                //     if ( $job_type ) :

                //         wp_set_post_terms( $post_id, array($job_type->term_id), 'job_type' );
                    
                //     else :

                //         $job_type_id = wp_insert_term( $data['job_type'], 'job_type', array() );
                //         wp_set_post_terms( $post_id, array($job_type_id), 'job_type' );

                //     endif;

                // endif;

            // ===== <<< [ Job Location ] >>> ===== //
                // if ( $jobmonster_addon->can_update_meta( 'job_location', $import_options ) ) :
                //     $post_item['job_location'] = array();
                //     $job_location = get_term_by( 'name', $data['job_location'], 'job_location' );
                                            
                //     if ( $job_location ) :

                //         wp_set_post_terms( $post_id, array($job_location->term_id), 'job_location' );
                    
                //     else :

                //         $job_location_id = wp_insert_term( $data['job_location'], 'job_location', array() );
                //         wp_set_post_terms( $post_id, array($job_location_id), 'job_location' );

                //     endif;

                // endif;

    }
endif;