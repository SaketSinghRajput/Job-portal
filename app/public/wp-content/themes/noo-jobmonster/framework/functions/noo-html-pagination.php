<?php
/**
 *
 * @param array|null $args
 * @param WP_Query   $query
 *
 * @return void|mixed
 */
function noo_pagination( $args = array(), $query = null, $live_search = null ) {
	global $wp_rewrite, $wp_query;

	do_action( 'noo_pagination_start' );

	if ( ! empty( $query ) ) {
		$wp_query = $query;
	}

	if ( 1 >= $wp_query->max_num_pages ) {
		return;
	}

	$paged = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

	$max_num_pages = intval( $wp_query->max_num_pages );

	$defaults = array(
		'base'                   => esc_url( add_query_arg( 'paged', '%#%' ) ),
		'format'                 => '',
		'total'                  => $max_num_pages,
		'current'                => $paged,
		'prev_next'              => true,
		'prev_text'              => '<i class="fas fa-long-arrow-alt-left"></i>',
		'next_text'              => '<i class="fas fa-long-arrow-alt-right"></i>',
		'show_all'               => false,
		'end_size'               => 1,
		'mid_size'               => 1,
		'add_fragment'           => '',
		'type'                   => 'plain',
		'before'                 => '<div class="pagination list-center">',
		'after'                  => '</div>',
		'echo'                   => true,
		'use_search_permastruct' => true,
	);

	$defaults = apply_filters( 'noo_pagination_args_defaults', $defaults );

	if ( $wp_rewrite->using_permalinks() && ! is_search() ) {
		$defaults[ 'base' ] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );
	}

	if ( is_search() ) {
		$defaults[ 'use_search_permastruct' ] = false;
	}

	if ( is_search() ) {
		if ( class_exists( 'BP_Core_User' ) || $defaults[ 'use_search_permastruct' ] == false ) {
			$search_query       = get_query_var( 's' );
			$paged              = get_query_var( 'paged' );
			$base               = esc_url_raw( add_query_arg( 's', urlencode( $search_query ) ) );
			$base               = esc_url_raw( add_query_arg( 'paged', '%#%' ) );
			$defaults[ 'base' ] = $base;
		} else {
			$search_permastruct = $wp_rewrite->get_search_permastruct();
			if ( ! empty( $search_permastruct ) ) {
				$base               = get_search_link();
				$base               = esc_url_raw( add_query_arg( 'paged', '%#%', $base ) );
				$defaults[ 'base' ] = $base;
			}
		}
	}
	
	$is_job_search = isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'live_search';

	if ( $is_job_search ) {
		$base               = esc_url_raw( add_query_arg( 's', urlencode( $search_query ) ) );
		$defaults['base'] = str_replace('wp-admin/admin-ajax.php', 'jobs/', $base);
	}

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'noo_pagination_args', $args );

	if ( 'array' == $args[ 'type' ] ) {
		$args[ 'type' ] = 'plain';
	}

	$pattern = '/\?(.*?)\//i';

	preg_match( $pattern, $args[ 'base' ], $raw_querystring );
	if ( ! empty( $raw_querystring ) ) {
		if ( $wp_rewrite->using_permalinks() && $raw_querystring ) {
			$raw_querystring[ 0 ] = str_replace( '', '', $raw_querystring[ 0 ] );
		}
		$args[ 'base' ] = str_replace( $raw_querystring[ 0 ], '', $args[ 'base' ] );
		$args[ 'base' ] .= substr( $raw_querystring[ 0 ], 0, - 1 );
	}
	$page_links = paginate_links( $args );

	$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'' ), '\'', $page_links );

	$page_links = $args[ 'before' ] . $page_links . $args[ 'after' ];

	$page_links = apply_filters( 'noo_pagination', $page_links );

	do_action( 'noo_pagination_end' );

	if ( $args[ 'echo' ] ) {
		echo $page_links;
	} else {
		return $page_links;
	}
}

// Posts Link Attributes
// =============================================================================

if ( ! function_exists( 'posts_link_attributes' ) ):
	function posts_link_attributes() {
		return 'class="prev-next hidden-phone"';
	}

	add_filter( 'next_posts_link_attributes', 'posts_link_attributes' );
	add_filter( 'previous_posts_link_attributes', 'posts_link_attributes' );
endif;

/**
 * 
 * @param WP_Query $query
 */
function noo_result_count($query, $per_page, $current_page){
	$total = $query->found_posts;
	if ( 1 === $total ) {
		_e( 'Showing the single result', 'noo' );
	} elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %d: total results */
		printf( _n( 'Showing all %d result', 'Showing all %d results', $total, 'noo' ), $total );
	} else {
		$first = ( $per_page * $current_page ) - $per_page + 1;
		$last  = min( $total, $per_page * $current_page );
		/* translators: 1: first result 2: last result 3: total results */
		printf( _nx( 'Showing %1$d&ndash;%2$d of %3$d result', 'Showing %1$d&ndash;%2$d of %3$d results', $total, 'with first and last result', 'noo' ), $first, $last, $total );
	}
}
