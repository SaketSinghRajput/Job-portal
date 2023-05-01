<?php

function noo_admin_ajax_get_users() {

	$search        = isset( $_GET['search'] ) ? $_GET['search'] : '';
	$rs['results'] = array();
	if ( ! empty( $search ) ) {

		$users       = new WP_User_Query( array(
			'search'         => '*' . esc_attr( $search ) . '*',
			'search_columns' => array(
				'user_login',
				'user_nicename',
				'user_email',
				'user_url',
			),
		) );
		$users_found = $users->get_results();

		if ( ! empty( $users_found ) ) {
			foreach ( $users_found as $user ) {
				$id              = $user->ID;
				$name            = $user->display_name;
				$rs['results'][] = array(
					'id'   => $id,
					'text' => $name,
				);
			}
		}
	}
	wp_send_json( $rs );

}

add_action( 'wp_ajax_noo_admin_ajax_get_users', 'noo_admin_ajax_get_users' );

function noo_admin_ajax_get_companies(){
	$search = isset( $_GET['search'] ) ? $_GET['search'] : '';
	$rs['results'] = array();
	if ( ! empty( $search ) ) {

		$args = array(
			'post_type' => 'noo_company',
			'posts_per_page' => -1,
			's' => esc_attr($search),
		);
		$args = apply_filters('noo_admin_ajax_get_companies_args', $args);
		$companies = new WP_Query($args );

		if ( $companies->have_posts() ) {
			while ( $companies->have_posts() ) {
				$companies->the_post();
				$id = get_the_ID();
				$name = get_the_title($id);
				$rs['results'][] = array(
					'id'   => $id,
					'text' => $name,
				);
			}
			wp_reset_postdata();
		}
	}
	wp_send_json( $rs );
}

add_action( 'wp_ajax_noo_admin_ajax_get_companies', 'noo_admin_ajax_get_companies' );