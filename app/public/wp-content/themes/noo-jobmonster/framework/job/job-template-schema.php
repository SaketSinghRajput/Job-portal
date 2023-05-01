<?php
if( !function_exists( 'jm_job_single_page_schema' ) ) :
	$is_schema = noo_get_option( 'noo_job_schema', false );
	function jm_job_single_page_schema( $schema = array() ) {
		if( is_singular( 'noo_job' )) {
			$schema['itemscope'] = '';
			$schema['itemtype'] = 'http://schema.org/JobPosting';
		}

		return $schema;
	}
	if($is_schema)
	add_filter( 'noo_job_schema', 'jm_job_single_page_schema' );
endif;

if( !function_exists( 'jm_job_single_title_schema' ) ) :
	$is_schema = noo_get_option( 'noo_job_schema', false );
	function jm_job_single_title_schema( $schema = array() ) {
		if( is_singular( 'noo_job' ) ) {
			$schema['itemprop'] = 'title';
		}

		return $schema;
	}
	if($is_schema)
	add_filter( 'noo_page_title_schema', 'jm_job_single_title_schema' );
endif;
