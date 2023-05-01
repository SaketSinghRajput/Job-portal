<?php
/**
 * Theme functions for NOO JobMonster Child Theme.
 */

// Below are some example code

// Jobs order by modified date
function jm_job_custom_order_by($query) {
	if( is_admin() || $query->is_singular ) {
		return $query;
	}

	if( jm_is_job_query( $query) ) {
		$query->set( 'orderby', 'modified' );
        $query->set( 'order', 'desc' );
	}
}

add_action( 'pre_get_posts', 'jm_job_custom_order_by' );

// Show expired job on job list
function jm_job_show_expired_jobs($query) {
	global $wp_post_statuses;

	if( isset( $wp_post_statuses['expired'] ) ) {
		$wp_post_statuses['expired']->public = true;
		$wp_post_statuses['expired']->exclude_from_search = false;
	}
}

add_action( 'init', 'jm_job_show_expired_jobs', 11 );

function jm_check_password( $errors, $args ) {
	$user_pass = $args['user_password'];
	// check password
	if( strlen( $user_pass ) < 8 ) {
		$errors->add( 'minlength_password', __( 'Password must be at least eight characters long.', 'noo' ) );
	}
	// your code for other check ....

	return $errors;
}
add_filter( 'noo_registration_errors', 'jm_check_password', 10, 2 );

function jm_add_custom_fields_to_job_rss() {
	if(get_post_type() == 'noo_job') {
		$locations = get_the_terms( get_the_ID(), 'job_location' );
		if( !empty( $locations ) && !is_wp_error( $locations ) ) {
			$locations_text = array();
			foreach ($locations as $location) {
				$locations_text[] = $location->name;
			}
			?>
				<locations><?php echo implode(', ', $locations_text); ?></locations>
			<?php
		}
	}
}
add_action('rss2_item', 'jm_add_custom_fields_to_job_rss');

// show expired jobs:
if( !function_exists('jm_show_expired_jobs') ) :
	function jm_show_expired_jobs($query) {
		if( is_admin() ) {
			return $query;
		}

		if( jm_is_job_query( $query) ) {
			if( $query->is_main_query() && !$query->is_singular ) {
				if( empty( $query->query_vars['post_status'] ) ) {
					// add expired to viewable link
					$post_status = array( 'publish', 'expired' );
					$query->set( 'post_status', $post_status );
				}
			}
		}

		return $query;
	}

	add_action( 'pre_get_posts', 'jm_show_expired_jobs', 11 );
endif;