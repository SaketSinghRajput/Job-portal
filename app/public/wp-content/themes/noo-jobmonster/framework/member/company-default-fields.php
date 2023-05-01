<?php

if ( ! function_exists( 'jm_get_company_default_fields' ) ) :
	function jm_get_company_default_fields() {
		$default_fields = array(
			'_logo'        => array(
				'name'       => '_logo',
				'label'      => __( 'Company Logo', 'noo' ),
				'type'       => 'single_image',
				'value'      => __( 'Recommend size: 160x160px', 'noo' ),
				'allowed_type' => array(
					'single_image' => __( 'Single Image', 'noo' ),
				),
				'is_default' => true,
				'required'   => false
			),
			'_cover_image' => array(
				'name'       => '_cover_image',
				'label'      => __( 'Company Cover Image', 'noo' ),
				'type'       => 'single_image',
				'value'      => __( 'Recommend size: 1400x600px', 'noo' ),
				'allowed_type' => array(
					'single_image' => __( 'Single Image', 'noo' ),
				),
				'is_default' => true,
				'required'   => false
			),
			'_portfolio' => array(
				'name'       => '_portfolio',
				'label'      => __( 'Portfolio', 'noo' ),
				'type'       => 'image_gallery',
				'value'      => __( 'Recommend size: 145x180px', 'noo' ),
				'allowed_type' => array(
					'image_gallery' => __( 'Image Gallery', 'noo' ),
				),
				'is_default' => true,
				'required'   => false
			),
            '_job_category' => array(
                'name' => '_job_category',
                'label' => __('Category', 'noo'),
                'type' => 'multiple_select',
                'allowed_type' => array(
                    'select'			=> __('Select', 'noo'),
                    'multiple_select'	=> __( 'Multiple Select', 'noo' ),
                ),
                'value' => '',
                'std' => '',
                'is_default' => true,
                'is_tax' => true,
                'required' => true
            ),
			'_slogan' => array(
				'name'       => '_slogan',
				'label'      => __( 'Slogan', 'noo' ),
				'type'       => 'text',
			),
			'_address'     => array(
				'name'         => '_address',
				'label'        => __( 'Location', 'noo' ),
				'type'         => 'single_tax_location_input',
				'allowed_type' => array(
					// 'company_location' => __('Location', 'noo')
					'multi_tax_location_input'	=> __('Multiple Location with Input', 'noo'),
					'multi_tax_location'		=> __('Multiple Location', 'noo'),
					'single_tax_location_input' => __( 'Single Location with Input', 'noo' ),
					'single_tax_location'       => __( 'Single Location', 'noo' ),
				),
				'value'        => '',
				'is_default'   => true,
				'required'     => true
			),
			'_full_address'     => array(
				'name'         => '_full_address',
				'label'        => __( 'Full Address', 'noo' ),
				'type'         => 'location_picker',
				'allowed_type' => array(
					'location_picker' => __( 'Location Picker', 'noo' ),
				),
				'value'        => '',
				'is_default'   => true,
				'required'     => true
			),
		);

		return apply_filters( 'jm_company_default_fields', $default_fields );
	}
endif;

if ( ! function_exists( 'jm_company_location_field_params' ) ) :
	function jm_company_location_field_params( $args = array(), $company_id = 0 ) {
		extract( $args );
		$location_tax_field_types = array(
			'company_location',
			'single_tax_location',
			'single_tax_location_input',
			'multi_tax_location',
			'multi_tax_location_input'
		);
		if ( in_array( $field['type'], $location_tax_field_types ) ) {
			$field_id = $field['name'];

			$field_value = array();
			$terms       = get_terms( 'job_location', array( 'hide_empty' => 0 ) );
			foreach ( $terms as $term ) {
				$field_value[] = $term->term_id . '|' . $term->name;
			}
			$field['value']        = $field_value;
			$field['no_translate'] = true;

			if ( ! empty( $company_id ) ) {
				$value = jm_resume_get_tax_value( $company_id, $field_id );
			}
		}

		return compact( 'field', 'field_id', 'value' );
	}

	add_filter( 'jm_company_render_form_field_params', 'jm_company_location_field_params', 10, 2 );
endif;

if ( ! function_exists( 'jm_location_render_field_tax_location' ) ) :
	function jm_location_render_field_tax_location( $field = array(), $field_id = '', $value = array(), $form_type = '', $object = array() )
    {
        $allow_user_input = strpos($field['type'], 'input') !== false || $field['type'] == 'company_location';

        $allow_multiple_select = strpos( $field['type'], 'multi' ) !== false;

        $field['type'] = (strpos($field['type'], 'single') !== false || $field['type'] == 'company_location') ? 'select' : 'multiple_select';
        if ($field['name'] == '_address') {
        	$name = '_address';
            if ($allow_multiple_select) {
                $name = '_address[]';
            }
            $company_id = jm_get_employer_company();

            $selected = array();
            if ($company_id) {
                $selected = get_post_meta($company_id, '_address', true);
                // $selected = json_decode($selected);
            }else{
            	$selected = '';
            }
            $selected = isset($_GET[$field_id]) ? $_GET[$field_id] : $selected;

            $location_args = array(
                'hide_empty' => 0,
                'echo' => 1,
                'selected' => $selected,
                'hierarchical' => 1,
                'name' => $name,
                'id' => 'noo-field-job_location',
                'class' => 'form-control noo-select form-control-chosen',
                'depth' => 0,
                'taxonomy' => 'job_location',
                'value_field' => 'term_id',
                'multiple' => $allow_multiple_select,
                'walker' => new Noo_Walker_TaxonomyDropdown(),
            ); ?>

            <?php 
            wp_dropdown_categories($location_args); ?>
            <?php
        } else {
            noo_render_select_field($field, $field_id, $value, $form_type);
        }
        if ($form_type != 'search' && $allow_user_input) {
            jm_job_add_new_location();
        }
    }

	add_filter( 'noo_render_field_company_location', 'jm_location_render_field_tax_location', 10, 5 );
	add_filter( 'noo_render_field_single_tax_location', 'jm_location_render_field_tax_location', 10, 5 );
	add_filter( 'noo_render_field_single_tax_location_input', 'jm_location_render_field_tax_location', 10, 5 );
	add_filter( 'noo_render_field_multi_tax_location', 'jm_location_render_field_tax_location', 10, 5 );
	add_filter( 'noo_render_field_multi_tax_location_input', 'jm_location_render_field_tax_location', 10, 5 );
endif;