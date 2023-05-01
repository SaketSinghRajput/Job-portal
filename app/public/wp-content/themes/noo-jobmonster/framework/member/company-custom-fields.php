<?php
if ( ! function_exists( 'jm_get_company_custom_fields_option' ) ) :
	function jm_get_company_custom_fields_option( $key = '', $default = null ) {
		$custom_fields = jm_get_setting( 'jm_company_custom_field', array() );

		if ( ! $custom_fields || ! is_array( $custom_fields ) ) {
			return $default;
		}

		if ( isset( $custom_fields['__options__'] ) && isset( $custom_fields['__options__'][ $key ] ) ) {

			return $custom_fields['__options__'][ $key ];
		}

		return $default;
	}
endif;

if ( ! function_exists( 'jm_get_company_custom_fields' ) ) :
	function jm_get_company_custom_fields( $include_disabled_fields = false, $suppress_filters = false ) {
		$custom_fields = noo_get_custom_fields( 'jm_company_custom_field', 'jm_company_field_' );

		if ( empty( $custom_fields ) ) {
			$custom_fields = noo_get_custom_fields( 'noo_company', 'jm_company_field_' );
		}

		$default_fields = jm_get_company_default_fields();

		$custom_fields = noo_merge_custom_fields( $default_fields, $custom_fields, $include_disabled_fields );

		return $suppress_filters ? $custom_fields : apply_filters( 'jm_company_custom_fields', $custom_fields );
	}
endif;

if (!function_exists('jm_get_company_search_custom_fields')) :
    function jm_get_company_search_custom_fields()
    {
        $custom_fields = jm_get_company_custom_fields();
        $not_searchable = noo_not_searchable_custom_fields_type();
        foreach ($custom_fields as $key => $field) {
            if (!empty($field['type'])) {
                if (in_array($field['type'], $not_searchable)) {
                    unset($custom_fields[$key]);
                }
            }
        }

        return apply_filters('jm_company_search_custom_fields', $custom_fields);
    }
endif;


if ( ! function_exists( 'jm_get_company_socials' ) ) :
	function jm_get_company_socials() {
		$socials = jm_get_company_custom_fields_option( 'socials', 'website,facebook,twitter,linkedin,instagram,googleplus' );
		$socials = ! is_array( $socials ) ? explode( ',', $socials ) : $socials;

		return apply_filters( 'jm_get_company_socials', $socials );
	}
endif;

if ( ! function_exists( 'jm_company_cf_settings_tabs' ) ) :
	function jm_company_cf_settings_tabs( $tabs = array() ) {
		$temp1 = array_slice( $tabs, 0, 2 );
		$temp2 = array_slice( $tabs, 2 );

		$resume_cf_tab = array( 'company' => __( 'Company ( Employer )', 'noo' ) );

		return array_merge( $temp1, $resume_cf_tab, $temp2 );
	}

	// add to page Custom field (cf) tab.
	add_filter( 'jm_cf_settings_tabs_array', 'jm_company_cf_settings_tabs' );
endif;

if ( ! function_exists( 'jm_company_custom_fields_setting' ) ) :
	function jm_company_custom_fields_setting() {
		wp_enqueue_style( 'noo-custom-fields' );
		wp_enqueue_script( 'noo-custom-fields' );

		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
		}
		wp_enqueue_style( 'vendor-chosen-css' );
		wp_enqueue_script( 'vendor-chosen-js' );

		$all_socials  = noo_get_social_fields();
		$selected_arr = jm_get_company_socials();

		noo_custom_fields_setting(
			'jm_company_custom_field',
			'jm_company_field_',
			jm_get_company_custom_fields( true )
		);
		?>
        <h3><?php echo __( 'Social Fields', 'noo' ) ?></h3>
        <table class="form-table" cellspacing="0">
            <tbody>
            <tr>
                <th>
					<?php _e( 'Select Social Networks', 'noo' ) ?>
                </th>
                <td>
					<?php if ( $all_socials ): ?>
                        <select class="social_list_field" name="jm_company_custom_field[__options__][socials]"
                                multiple="multiple" style="width: 500px;max-width: 100%;">
							<?php if ( $selected_arr ): ?>
								<?php foreach ( (array) $selected_arr as $index => $key ): ?>
									<?php if ( isset( $all_socials[ $key ] ) ) : ?>
                                        <option value="<?php echo esc_attr( $key ) ?>"
                                                selected><?php echo esc_html( $all_socials[ $key ]['label'] ); ?></option>
										<?php unset( $all_socials[ $key ] ); ?>
									<?php else : unset( $selected_arr[ $index ] ); ?>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
							<?php foreach ( $all_socials as $key => $social ): ?>
                                <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $social['label'] ); ?></option>
							<?php endforeach; ?>
                        </select>
                        <input name="jm_company_custom_field[__options__][socials]" type="hidden"
                               value="<?php echo implode( ',', $selected_arr ); ?>"/>
                        <script type="text/javascript">
                            jQuery(document).ready(function ($) {
                                $("select.social_list_field").chosen({
                                    placeholder_text_multiple: "<?php echo __( 'Select social networks', 'noo' ); ?>"
                                }).change(function (e, params) {
                                    var $this = $(this);
                                    var values = $(this).siblings('input').val();
                                    values = values !== "" ? values.split(',') : [];

                                    if( typeof params.deselected !== "undefined" ) {
                                    	values = $.grep(values, function(value) {
                                    		return value != params.deselected;
                                    	});
                                    } else if( typeof params.selected !== "undefined" ) {
                                    	values.push( params.selected );
                                    }

                                    $(this).siblings('input').val(values.join());
                                });
                            });
                        </script>
                        <style type="text/css">
                            .chosen-container input[type="text"] {
                                height: auto !important;
                            }
                        </style>
					<?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>
		<?php do_action( 'jm_company_custom_fields_setting_options' );
	}

	add_action( 'jm_cf_setting_company', 'jm_company_custom_fields_setting' );
endif;

if ( ! function_exists( 'jm_company_render_form_field' ) ) :
	function jm_company_render_form_field( $field = array(), $company_id = 0 ) {
		$field_id = jm_company_custom_fields_name( $field['name'], $field );

		$value = ! empty( $company_id ) ? get_post_meta( $company_id, $field_id, true ) : '';
		$value = ! is_array( $value ) ? trim( $value ) : $value;

		$params = apply_filters( 'jm_company_render_form_field_params', compact( 'field', 'field_id', 'value' ), $company_id );
		extract( $params );
		$object = array( 'ID' => $company_id, 'type' => 'post' );
		
		$ex_class = ( '_address' == $field['name']) ? 'job_location_field' : '';
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;

		?>
        <div class="form-group row <?php noo_custom_field_class( $field, $object ); ?> <?php echo $ex_class; ?>">
            <label for="<?php echo esc_attr( $field_id ) ?>" class="col-sm-3 control-label"><?php echo $label ?></label>
            <div class="col-sm-9">
				<?php noo_render_field( $field, $field_id, $value, '', $object ); ?>
            </div>
        </div>
		<?php
	}
endif;

if ( ! function_exists( 'jm_company_save_custom_fields' ) ) :
	function jm_company_save_custom_fields( $company_id = 0, $args = array() ) {
		if ( empty( $company_id ) ) {
			return;
		}

		$fields = jm_get_company_custom_fields();
		if ( $fields ) {
			foreach ( $fields as $field ) {
				$id = jm_company_custom_fields_name( $field['name'], $field );

				$value = isset( $args[ $id ] ) ? $args[ $id ] : '';

				if ( isset( $field['type'] ) && $field['type'] == 'location_picker' ) {
					update_post_meta( $company_id, $id . '_lat', $args[ $id . '_lat' ] );
					update_post_meta( $company_id, $id . '_lon', $args[ $id . '_lon' ] );
				}

				update_post_meta( $company_id, $id, $value );
			}
		}

		$socials = jm_get_company_socials();
		if ( ! empty( $socials ) ) {
			foreach ( $socials as $social ) {
				$field_id = "_{$social}";
				if ( isset( $args[ $field_id ] ) ) {
					$value = $args[ $field_id ];
					update_post_meta( $company_id, $field_id, $value );
				}
			}
		}
	}
endif;

if ( ! function_exists( 'jm_company_custom_fields_prefix' ) ) :
	function jm_company_custom_fields_prefix() {
		return apply_filters( 'jm_company_custom_fields_prefix', '_jm_company_field_' );
	}
endif;

if ( ! function_exists( 'jm_company_custom_fields_name' ) ) :
	function jm_company_custom_fields_name( $field_name = '', $field = array() ) {
		if ( empty( $field_name ) ) {
			return '';
		}

		$cf_name = jm_company_custom_fields_prefix() . sanitize_title( $field_name );

		if ( ! empty( $field ) && isset( $field['is_default'] ) ) {
			$cf_name = $field['name'];
		}

		return apply_filters( 'jm_company_custom_fields_name', $cf_name, $field_name, $field );
	}
endif;

if (!function_exists('jm_company_render_search_field')) :
    function jm_company_render_search_field($field = array(), $disable_multiple_select = false)
    {
        $field_id = jm_company_custom_fields_name($field['name'], $field);
        $value = '';
        $params = apply_filters('jm_resume_render_search_field_params', compact('field', 'field_id', 'value'));
        extract($params);

        $field['required'] = ''; // no need for required fields in search form

        if ($disable_multiple_select) {
            $field['disable_multiple'] = true;
        }

        $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
        $value = !is_array($value) ? trim($value) : $value;
        $field_label = (!empty($field['plural'])) ? $field['plural'] : $field['label'];
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field_label;
        ?>
        <div class="form-group" data-placeholder="<?php echo esc_attr($label); ?>">
            <label for="<?php echo 'search-' . esc_attr($field_id) ?>"
                   class="control-label"><?php echo($label); ?></label>
            <div class="advance-search-form-control">
                <?php noo_render_field($field, $field_id, $value, 'search'); ?>
            </div>
        </div>
        <?php
    }
endif;

if (!function_exists('jm_company_advanced_search_field')) :
    function jm_company_advanced_search_field($field_val = '', $disable_multiple_select = false)
    {
        if (empty($field_val) || $field_val == 'no') {
            return '';
        }

        $field_arr = explode('|', $field_val);
        $field_id = isset($field_arr[0]) ? $field_arr[0] : '';

        if (empty($field_id)) {
            return '';
        }

        $fields = jm_get_company_search_custom_fields();
        $field_prefix = jm_company_custom_fields_prefix();
        $field_id = str_replace($field_prefix, '', $field_id);

        foreach ($fields as $field) {
            if (sanitize_title($field['name']) == $field_id) {
                $tax_fields = array('_job_location', '_job_category');
                if (in_array($field['name'], $tax_fields)) {
                    $field['type'] = 'company_tax_select';
                }
                jm_company_render_search_field($field, $disable_multiple_select);
                break;
            }
        }

        return '';
    }
endif;


if ( ! function_exists( 'jm_get_company_field' ) ) :
	function jm_get_company_field( $field_name = '' ) {

		$custom_fields = jm_get_company_custom_fields( false, true );
		if ( isset( $custom_fields[ $field_name ] ) ) {
			return $custom_fields[ $field_name ];
		}

		foreach ( $custom_fields as $field ) {
			if ( $field_name == $field['name'] ) {
				return $field;
			}
		}

		return array();
	}
endif;

if ( ! function_exists( 'jm_get_company_field_value' ) ) :
	function jm_get_company_field_value( $post_id, $field = array() ) {
		$field['type'] = isset( $field['type'] ) ? $field['type'] : 'text';

		$id = jm_company_custom_fields_name( $field['name'], $field );

		$value = $post_id ? get_post_meta( $post_id, $id, true ) : '';
		$value = ! is_array( $value ) ? trim( $value ) : $value;
		if ( ! empty( $value ) ) {
			$value = noo_convert_custom_field_value( $field, $value );
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
		}

		return $value;
	}
endif;
if (!function_exists('jm_company_advanced_search_tax_field')) :
    function jm_company_advanced_search_tax_field($field, $field_id, $value, $form_type, $object)
    {

        $disable_multiple = isset($field['disable_multiple']) ? $field['disable_multiple'] : false;

        $label = $field['label'];
        $tax = str_replace('_job_', 'job_', $field['name']);

        $field_name = $disable_multiple ? $field_id : $field_id . '[]';

        $selected = isset($_GET[$field_id]) ? $_GET[$field_id] : '';

        if ($field['name'] == '_job_category' && empty($selected)) {
            $selected = isset($_GET['company_category']) ? intval($_GET['company_category']) : '';
        }
        $field_args = array(
            'hide_empty' => 0,
            'echo' => 1,
            'selected' => $selected,
            'hierarchical' => 1,
            'name' => $field_name,
            'id' => 'noo-field-' . $tax,
            'class' => 'form-control noo-select form-control-chosen',
            'depth' => 0,
            'taxonomy' => $tax,
            'value_field' => 'term_id',
            'orderby' => 'name',
            'multiple' => !$disable_multiple,
            'walker' => new Noo_Walker_TaxonomyDropdown(),
        );

        wp_dropdown_categories($field_args);
    }

    add_action('noo_render_field_company_tax_select', 'jm_company_advanced_search_tax_field', 10, 5);
endif;