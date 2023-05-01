<?php
if ( ! function_exists( 'noo_render_field' ) ) :
	function noo_render_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array(),$place_holder = '',$list_post_query = array() ) {
		switch ( $field['type'] ) {
			case "textarea":
				noo_render_textarea_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "select":
			case "multiple_select":
				noo_render_select_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "radio" :
				noo_render_radio_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "checkbox" :
				noo_render_checkbox_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "text" :
				noo_render_text_field( $field, $field_id, $value, $form_type, $object,$place_holder);
				break;
			case "number" :
				noo_render_number_field( $field, $field_id, $value, $form_type, $object,$place_holder );
				break;
			case "email" :
				noo_render_email_field( $field, $field_id, $value, $form_type, $object,$place_holder );
				break;
			case "url" :
				noo_render_url_field( $field, $field_id, $value, $form_type, $object);
				break;
			case "hidden" :
				noo_render_hidden_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "datepicker" :
				noo_render_datepicker_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "single_image" :
				noo_render_single_image_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "image_gallery" :
				noo_render_image_gallery_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "file_upload" :
				noo_render_file_upload_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "embed_video" :
				noo_render_embed_video_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "location_picker" :
				noo_render_location_picker_field( $field, $field_id, $value, $form_type, $object );
				break;
			case "multi_job_tag_input":
			    noo_render_jobtag_with_input($field,$field_id,$value,$form_type,$object);
			     break;
			case "filter_radio":
			    noo_render_filter_radio_field($field,$field_id,$value,$form_type,$list_post_query);
			     break;
			case "filter_checkbox":
			    noo_render_filter_checkbox_field($field,$field_id,$value,$form_type,$list_post_query);
			     break;
			default :
				do_action( 'noo_render_field_' . $field['type'], $field, $field_id, $value, $form_type, $object );
				break;
		}

		if ( $form_type != 'search' && isset( $field['desc'] ) && ! empty( $field['desc'] ) ) : ?>
            <em><?php echo esc_html( $field['desc'] ); ?></em>
		<?php endif;

		do_action( 'noo_after_render_field', $field, $field_id, $value, $form_type, $object );
	}
endif;
if ( ! function_exists( 'noo_render_text_field' ) ) :
	function noo_render_text_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array(),$place_holder = ''  ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$field_value = (empty($field_value)) ? $place_holder : $field_value;
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';

		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="text"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $value ); ?>"
               placeholder="<?php echo $field_value; ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_number_field' ) ) :
	function noo_render_number_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array(),$place_holder) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$field_value = (empty($field_value)) ? $place_holder : $field_value;
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="number"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $value ); ?>"
               placeholder="<?php echo $field_value; ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_email_field' ) ) :
	function noo_render_email_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array(),$place_holder ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$field_value = (empty($field_value)) ? $place_holder : $field_value;
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="email"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $value ); ?>"
               placeholder="<?php echo $field_value; ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_url_field' ) ) :
	function noo_render_url_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$field_value = empty( $field_value ) ? '#' : $field_value;
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="url"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $value ); ?>"
               placeholder="<?php echo $field_value; ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_textarea_field' ) ) :
	function noo_render_textarea_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		?>
        <textarea <?php echo $class; ?> id="<?php echo esc_attr( $input_id ) ?>"
                                        name="<?php echo esc_attr( $field_id ) ?>"
                                        placeholder="<?php echo $field_value; ?>"
                                        rows="8"><?php echo esc_html( $value ); ?></textarea>
		<?php
	}

endif;
if ( ! function_exists( 'noo_render_radio_field' ) ) :
	function noo_render_radio_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class    = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';

		$field_value = noo_convert_custom_field_setting_value( $field );
		if ( $form_type == 'search' ) {
			$field_value + array( '' => __( 'All', 'noo' ) ) + $field_value;
		}
		$value = is_array( $value ) ? reset( $value ) : $value;
		foreach ( $field_value as $key => $label ) :
			$checked = ( $key == $value ) ? 'checked="checked"' : '';
			?>

            <div class="form-control-flat">
                <label class="radio">
                    <input type="radio" name="<?php echo esc_attr( $field_id ); ?>"
                           value="<?php echo esc_attr( $key ); ?>" <?php echo $class; ?> <?php echo esc_attr( $checked ); ?>><i></i><?php echo ( $label ); ?>
                </label>
            </div>
		<?php endforeach;
	}

endif;
if ( ! function_exists( 'noo_render_checkbox_field' ) ) :
	function noo_render_checkbox_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class    = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';

		if ( ! is_array( $value ) ) {
			$value = noo_json_decode( $value );
		}
		$field_value = noo_convert_custom_field_setting_value( $field );
		foreach ( $field_value as $key => $label ) :
			$checked = in_array( $key, $value ) ? 'checked="checked"' : '';
			?>
            <div class="form-control-flat">
                <label class="checkbox">
                    <input name="<?php echo $field_id; ?>[]"
                           type="checkbox" <?php echo $class; ?> <?php echo $checked; ?>
                           value="<?php echo esc_attr( $key ); ?>"/><i></i>
					<?php echo ( $label ); ?>
                </label>
            </div>
		<?php endforeach;
	}

endif;
if ( ! function_exists( 'noo_render_select_field' ) ) :
	function noo_render_select_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$input_id           = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$is_multiple_select = isset( $field['type'] ) && $field['type'] === 'multiple_select';

		if(isset($field['disable_multiple']) && $field['disable_multiple']){

		$is_multiple_select = false;
		}
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
		$value = ( $is_multiple_select && ! is_array( $value ) ) ? noo_json_decode( $value ) : $value;

		$field_value = noo_convert_custom_field_setting_value( $field );
		$placeholder = $form_type != 'search' ? sprintf( __( "Select %s", 'noo' ), $label ) : $label;
		
		$placeholder = apply_filters('noo_render_select_field_placeholder', $placeholder, $label, $form_type);
		if ( ! $is_multiple_select ) {
			$field_value = array( '' => $placeholder ) + $field_value;
		}

		$is_chosen = $is_multiple_select || ( count( $field_value ) > 10 );
		$is_chosen = apply_filters( 'noo_select_field_is_chosen', $is_chosen, $field, $field_id );

		$rtl_class    = is_rtl() && $is_chosen ? ' chosen-rtl' : '';
		$chosen_class = $is_chosen ? ' form-control-chosen ignore-valid' : '';
		$chosen_class .= isset( $field['required'] ) && $field['required'] ? ' jform-chosen-validate' : '';

		$attrs = isset( $field['required'] ) && $field['required'] ? ' class="form-control' . $rtl_class . $chosen_class . '" required aria-required="true"' : ' class="form-control ' . $rtl_class . $chosen_class . '"';
        $label = isset($field['label_translated']) ? $field['label_translated'] : $field['label'];
		?>
		<?php if ( $is_multiple_select ) : ?>
            <select id="<?php echo esc_attr( $input_id ) ?>" <?php echo $attrs; ?> name="<?php echo esc_attr( $field_id ); ?>[]" multiple="multiple" data-placeholder="<?php echo $placeholder; ?>">
		<?php else : ?>
            <select id="<?php echo esc_attr( $input_id ) ?>" <?php echo $attrs; ?> name="<?php echo esc_attr( $field_id ); ?>" data-placeholder="<?php echo $placeholder; ?>">
		<?php endif; ?>
		<?php
		foreach ( $field_value as $key => $label ) :
			if ( is_array( $value ) ) {
				$selected = in_array( $key, $value ) ? 'selected="selected"' : '';
			} else {
				$selected = ( $key == $value ) ? 'selected="selected"' : '';
			}
			$class = ! empty( $key ) ? $key : '';
			?>
            <option value="<?php echo $key; ?>" <?php echo $selected; ?> class="<?php echo esc_attr( $class ); ?>"><?php echo esc_html( $label ); ?></option>
		<?php
		endforeach;
		?>
        </select>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_hidden_field' ) ) :
	function noo_render_hidden_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" class="form-control" type="hidden"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_attr( $value ); ?>"/>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_datepicker_field' ) ) :
	function noo_render_datepicker_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$class = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-datepicker jform-validate" required readonly aria-required="true"' : ' class="form-control jform-datepicker"';

		if ( $form_type != 'search' ) : ?>
			<?php
			$label      = isset( $field['label_translated'] ) ? $field['label_translated'] : $field['label'];
			$date_value = is_numeric( $value ) ? date_i18n( get_option( 'date_format' ), $value ) : $value;
			$value      = is_numeric( $value ) ? $value : strtotime( $value );

			$placeholder = ! empty( $label ) ? $label : __( 'Select datepicker', 'noo' );

			?>
            <input placeholder="<?php echo $placeholder; ?>" type="text"
                   value="<?php echo $date_value; ?>" <?php echo $class; ?> name="<?php echo esc_attr( $field_id ); ?>">
            <input type="hidden" class="jform-datepicker_value" name="<?php echo esc_attr( $field_id ); ?>"
                   value="<?php echo $value; ?>">
		<?php else : ?>
			<?php
			$_start      = isset( $_GET[ $field_id . '_start' ] ) ? $_GET[ $field_id . '_start' ] : '';
			$_start_date = is_numeric( $_start ) ? date_i18n( get_option( 'date_format' ), $_start ) : $_start;
			$_start      = is_numeric( $_start ) ? $_start : strtotime( $_start );

			$_end      = isset( $_GET[ $field_id . '_end' ] ) ? $_GET[ $field_id . '_end' ] : '';
			$_end_date = is_numeric( $_end ) ? date_i18n( get_option( 'date_format' ), $_end ) : $_end;
			$_end      = is_numeric( $_end ) ? $_end : strtotime( $_end );
			?>
            <fieldset>
                <input type="text" value="<?php echo $_start_date; ?>" class="form-control half jform-datepicker_start"
                       name="<?php echo esc_attr( $field_id ) . '_start'; ?>"
                       placeholder="<?php echo __( 'Start', 'noo' ); ?>">
                <input type="hidden" class="jform-datepicker_start_value" class="form-control"
                       name="<?php echo esc_attr( $field_id ) . '_start'; ?>" value="<?php echo $_start; ?>">
                <input type="text" value="<?php echo $_end_date; ?>" class="form-control half jform-datepicker_end"
                       name="<?php echo esc_attr( $field_id ) . '_end'; ?>"
                       placeholder="<?php echo __( 'End', 'noo' ); ?>">
                <input type="hidden" class="jform-datepicker_end_value" class="form-control"
                       name="<?php echo esc_attr( $field_id ) . '_end'; ?>" value="<?php echo $_end; ?>">
            </fieldset>
		<?php endif;
	}
endif;
if ( ! function_exists( 'noo_render_embed_video_field' ) ) :
	function noo_render_embed_video_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		$placeholder = ! empty( $field_value ) ? $field_value : __( 'Youtube or Vimeo link', 'noo' );
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="url"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_url( $value ) ?>"
               placeholder="<?php echo $placeholder; ?>">
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_single_image_field' ) ) :
	function noo_render_single_image_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$attrs        = isset( $field['required'] ) && $field['required'] ? ' required aria-required="true"' : '';
    	noo_image_upload_form_field( $field_id, $value, false, $field['value'] ,$attrs);
	}
endif;
if ( ! function_exists( 'noo_render_image_gallery_field' ) ) :
	function noo_render_image_gallery_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
        $class        = isset( $field['required'] ) && $field['required'] ? ' required aria-required="true"' : '';
		noo_image_upload_form_field( $field_id, $value, true, $field['value'],$class );
	}
endif;
if ( ! function_exists( 'noo_render_file_upload_field' ) ) :
	function noo_render_file_upload_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$file_exts    = ! empty( $field['value'] ) ? $field['value'] : 'pdf,doc,docx';
		$allowed_exts = noo_upload_convert_extension_list( $file_exts );
		$class        = isset( $field['required'] ) && $field['required'] ? ' required aria-required="true"' : '';
		?>
        <div class="form-control-flat">
            <div class="upload-to-cv clearfix">
				<?php noo_file_upload_form_field( $field_id, $allowed_exts,$value ,False, $class) ?>
            </div>
        </div>
		<?php
	}
endif;
if ( ! function_exists( 'noo_render_embed_video_field' ) ) :
	function noo_render_embed_video_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		$field_value = noo_convert_custom_field_setting_value( $field );
		$input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$class       = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		$placeholder = ! empty( $field_value ) ? $field_value : __( 'Youtube or Vimeo link', 'noo' );
		?>
        <input id="<?php echo esc_attr( $input_id ) ?>" <?php echo $class; ?> type="url"
               name="<?php echo esc_attr( $field_id ) ?>" value="<?php echo esc_url( $value ) ?>"
               placeholder="<?php echo $placeholder; ?>">
		<?php
	}
endif;

if ( ! function_exists( 'noo_render_location_picker_field' ) ) :
	function noo_render_location_picker_field( $field = array(), $field_id = '', $value = '', $form_type = '', $object = array() ) {
		
		
		$post_id  = ! empty( $object ) ? $object['ID'] : 0;
		$input_id = $field_id;
		$class    = isset( $field['required'] ) && $field['required'] ? 'class="noo-mb-location-address form-control jform-validate" required aria-required="true"' : ' class="noo-mb-location-address form-control"';

		$address = noo_get_post_meta( $post_id, $field_id, '' );
		$lat     = noo_get_post_meta( $post_id, $field_id . '_lat', '' );
		$lon     = noo_get_post_meta( $post_id, $field_id . '_lon', '' );
		
		$is_frontend_submit = ( isset( $_GET['action'] ) && $_GET['action'] == 'post_job' ) ? true : false;
		$is_edit_job = ( isset( $_GET['job_id'] ) && ! empty( $_GET['job_id'] ) ) ? true : false;
        $checkbox_label = __( 'The same as company address', 'noo' );
        $checkbox_id    = '_use_company_address';
		$company_id         = jm_get_employer_company();
		$full_address_company   = ! empty( $company_id ) ? get_post_meta( $company_id, '_full_address', true ) : '';
		$checkbox_value = empty( $post_id ) ? 1 : get_post_meta( $post_id, $checkbox_id, true );
		$a= get_post_meta( $post_id, $checkbox_id, true );
		$map_type = jm_get_location_setting('map_type','');
		if (  apply_filters('noo_allow_job_use_company_address', true) && 
			( ($is_frontend_submit && !empty($full_address_company)) or (!empty($full_address_company) && $is_edit_job) ) 
		) :

			?>
			<input name="<?php echo $checkbox_id; ?>" type="hidden" value="0"/>
            <div class="form-control-flat">
                <label class="checkbox">
                    <input id="use_company_address" name="<?php echo $checkbox_id; ?>" type="checkbox" <?php checked( $checkbox_value ); ?> value="1"/><i></i>
                    <?php echo esc_html( $checkbox_label ); ?>
                </label>
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery("#use_company_address").change(function () {
                        if (jQuery(this).is(":checked")) {
                            jQuery(".noo-location-picker-field-wrap").addClass('hidden');
                        } else {
                            jQuery(".noo-location-picker-field-wrap").removeClass('hidden');
                        }
                    }).change();
                });
            </script>

		<?php endif; ?>
		<?php if ($map_type == 'google'): ?>
			<?php wp_enqueue_script( 'location-picker' ); ?>
			<div class="noo-location-picker-field-wrap <?php echo $checkbox_value ? '' : ''; ?>">
                <input <?php echo $class; ?> type="text" name="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_html($address); ?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'noo') ?>" />
                <input type="hidden" class="noo-mb-lat" id="noo-mb-lat" name="<?php echo esc_attr( $input_id ) ?>_lat" value="<?php echo $lat; ?>">
                <input type="hidden" class="noo-mb-lon" id="noo-mb-lon" name="<?php echo esc_attr( $input_id ) ?>_lon" value="<?php echo $lon; ?>">
                <div class="noo-mb-job-location" id="<?php echo esc_attr( $input_id ); ?>" data-lat="<?php echo $lat; ?>"
                     data-lon="<?php echo $lon; ?>" style="height: 300px;">
                </div>
            </div>
            <?php elseif ($map_type == 'bing'): ?>
            	<?php $uniqID = uniqid(); ?>
            	<div class="noo-location-picker-field-wrap ">
            		<div class="map_type">
            			<input id="noo-mb-location-address" <?php echo $class; ?> type="text" name="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_html($address); ?>" placeholder="<?php echo esc_html__('Enter an exact address.', 'noo') ?>" />
            		</div>
                <input type="hidden" class="noo-mb-lat" name="<?php echo esc_attr( $input_id ) ?>_lat" value="<?php echo $lat; ?>" id="noo-mb-lat">
                <input type="hidden" class="noo-mb-lon" name="<?php echo esc_attr( $input_id ) ?>_lon" value="<?php echo $lon; ?>" id="noo-mb-lon">
                <div class="noo-mb-job" data-id='_full_address<?php echo  esc_attr( $uniqID  ) ?>'>
                    <div id='_full_address<?php echo  esc_attr( $uniqID  ) ?>' style="height: 300px;" ></div>
                </div>
            </div>
            <?php endif ?>
		<?php
	}
endif;
if(!function_exists('noo_render_jobtag_with_input')):
    function noo_render_jobtag_with_input($field=array(),$field_id='',$value='',$form_type='',$object=array()){
            $value=implode(",",$value);
            $class       = isset( $field['required'] ) && $field['required'] ? 'required  aria-required="true"' : ' ';
            $field_value = noo_convert_custom_field_setting_value( $field );
		    $input_id    = $form_type == 'search' ? 'search-' . $field_id : $field_id;
            noo_render_job_tags_json();
            // Tag InPut
            wp_enqueue_style('bootstrap-tagsinput',NOO_FRAMEWORK_URI.'/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css');
			wp_register_script('typeahead',NOO_FRAMEWORK_URI.'/vendor/bootstrap-tagsinput/typeahead.bundle.min.js',array('jquery'),null,false);
            wp_enqueue_script('bootstrap-tagsinput',NOO_FRAMEWORK_URI.'/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js',array('typeahead'),null,false);
		    ?>


		    <input id="<?php echo esc_attr( $input_id ) ?>" class="tagsinput tag-required" <?php echo $class ?> type="text"
               name="<?php echo esc_attr( $field_id ) ?>" data-value="<?php echo esc_url(noo_upload_url()) ?>/job-tags.json" placeholder="<?php echo esc_html__('Enter Job tags','noo')?>" value="<?php echo esc_attr($value) ?>"
              />
		    <?php


    }
endif;
if ( ! function_exists( 'noo_render_property_tags_json' ) ) :
    function noo_render_job_tags_json() {
        $noo_terms = get_terms(array(
            'taxonomy' 		=> 'job_tag',
            'hide_empty'	=> false
        ));
        if(is_array($noo_terms) && !empty($noo_terms)){
            $noo_terms_json = array();
            foreach ($noo_terms as $key => $value) {
                $noo_terms_json[$value->term_id] = $value->name;
            }
            $noo_terms_json = wp_json_encode($noo_terms_json);
            if(!empty($noo_terms_json)){
                require_once ABSPATH . 'wp-admin/includes/file.php';
                WP_Filesystem();
                global $wp_filesystem;

                $tag_dir = noo_create_upload_dir( $wp_filesystem );

                if (!$wp_filesystem->put_contents( $tag_dir ."/job-tags.json", $noo_terms_json, FS_CHMOD_FILE)) {
                    return array(
                        'status'  => 'error',
                        'message' => esc_html__('Could not create property tags file','noo')
                    );
                }
            }
        }
    }
endif;
if ( ! function_exists( 'noo_upload_dir_name' ) ):
    function noo_upload_dir_name() {
        return apply_filters( 'noo_upload_dir_name', 'jobmonster' );
    }
endif;

if ( ! function_exists( 'noo_create_upload_dir' ) ):
    function noo_create_upload_dir( $wp_filesystem = null ) {
        if ( empty( $wp_filesystem ) ) {
            return false;
        }

        $upload_dir = wp_upload_dir();
        global $wp_filesystem;

        $noo_upload_dir = $wp_filesystem->find_folder( $upload_dir[ 'basedir' ] ) . noo_upload_dir_name();
        if ( ! $wp_filesystem->is_dir( $noo_upload_dir ) ) {
            if ( wp_mkdir_p( $noo_upload_dir ) ) {
                return $noo_upload_dir;
            }

            return false;
        }

        return $noo_upload_dir;
    }
endif;
if (!function_exists('noo_upload_url')):
    function noo_upload_url() {
        $upload_dir = wp_upload_dir();

        $url = $upload_dir['baseurl'];
        if ( $upload_dir['baseurl'] && is_ssl() ) {
            $url = str_replace( 'http://', 'https://', $upload_dir['baseurl'] );
        }
        return $url . '/' . noo_upload_dir_name();
    }
endif;
if ( ! function_exists( 'noo_render_filter_checkbox_field' ) ) :
	function noo_render_filter_checkbox_field( $field = array(), $field_id = '', $value = '', $form_type = '', $list_post_query = array()) {
		$input_id = $form_type == 'search' ? 'search-' . $field_id : $field_id;
		$input_id = $input_id.'-'.uniqid();
		$class    = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';
		if ( ! is_array( $value ) ) {
			$value = noo_json_decode( $value );
		}
		$field_value = noo_convert_custom_field_setting_value( $field );
		echo'<div class="form-group filter-search-group">';
		echo'<input type="text" class="form-control filter-search-option" id="'.$input_id.'" placeholder="search '.$field['label'].'">';
		// echo'<i class="fa fa-filter"></i>';
		echo'</div>';
		echo'<div class="job-list-filter">';
		if($field_id=='category'|| $field_id=='location' || $field_id=='_job_category' || $field_id=='_job_location'){
		        $tax_args = array(
		                'orderby'    => 'name',
                        'hide_empty' => 1,
                        'hierarchical' => 1,);
		    if($field_id =='category' || $field_id =='location'){
		        $tax_args['taxonomy'] = 'job_'.$field_id;
		    }else{
		         $tax_args['taxonomy'] = substr( $field_id, 1 );
		    }
		    $field_value = get_categories($tax_args);
            foreach ($field_value as $field_val){
                 if($form_type == 'filter_resume'){
                     $checked = in_array($field_val->term_id,$value) ? 'checked="checked"' : '';
                     $count = noo_get_count_resume_field_filter($field_id,$field_val->term_id,$list_post_query);
                 }else{
                      $checked = in_array($field_val->slug,$value) ? 'checked="checked"' : '';
                      $count   = noo_get_count_job_field_filter($field_id,$field_val->slug,$list_post_query);
                     }
                  $parent = get_term_by('id',$field_val->parent,'job_'.$field_id);
                if($parent){
                    continue;
                }
                ?>
                    <div class="form-control-flat">
                        <label class="checkbox">
                            <input name="<?php echo $field_id; ?>[]"
                                   type="checkbox" <?php echo $class; ?> <?php echo $checked; ?>
                                   value="<?php echo ($form_type == 'filter_resume')? esc_attr($field_val->term_id): esc_attr( $field_val->slug ); ?>"/><i></i>
                            <?php echo esc_html( $field_val->name ); ?>
                                <?php if(!empty($count)): ?>
                                 <span><?php echo esc_html($count)?></span>
                                 <?php endif; ?>
                                  <?php
                            	$parent_id = $field_val->term_id;
                                $cat_args = array(
                                    'orderby'    => 'name',
                                    'hide_empty' => 1,
                                    'parent'     => $parent_id,
                                    'hierarchical' => 1,
                                );
                                if($field_id =='category' || $field_id =='location'){
                                    $cat_args['taxonomy'] = 'job_'.$field_id;
                                }else{
                                     $cat_args['taxonomy'] = substr( $field_id, 1 );
                                }

                                $subs = get_categories( $cat_args );
                                if ( ! empty( $subs ) ):
                                ?>
                                <div class="children form-control-flat">
                                    <?php

                                    foreach ( $subs as $sub ) {
                                        if($form_type == 'filter_resume'){
                                              $sub_checked = in_array($sub->term_id,$value) ? 'checked="checked"' : '';
                                              $sub_count = noo_get_count_resume_field_filter($field_id,$sub->term_id,$list_post_query);
                                        }else{
                                               $sub_checked = in_array($sub->slug,$value) ? 'checked="checked"' : '';
                                               $sub_count   = noo_get_count_job_field_filter($field_id,$sub->slug,$list_post_query);
                                        }
                                        $sub_value = ($form_type !='filter_resume')? $sub->slug: $sub->term_id ;
                                        echo '<label class="checkbox">';
                                        echo '<input name="'.$field_id.'[]" type="checkbox" '.$class .' '.$sub_checked.' value="'.$sub_value.'">';                                  echo '<i></i>';
                                        if(!empty($sub_count)){
                                            echo '<span>'.esc_html($sub_count).'</span>';
                                        }
                                        echo $sub->name;
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                                <?php endif; ?>
                        </label>

                    </div>
                <?php
            }
		}else{
		    foreach ( $field_value as $key => $label ) :
                if($form_type == 'filter_resume'){
                     if(in_array($field_id,array('category','location'))){
                         $term = get_term_by('slug',$key,'job_'.$field_id);
                         $key  = (!empty($term)) ? $term->term_id : '';
                     }
                    $count = noo_get_count_resume_field_filter($field_id,$key,$list_post_query);
                }else{
                    $count   = noo_get_count_job_field_filter($field_id,$key,$list_post_query);
                }
                    $checked = in_array( $key, $value ) ? 'checked="checked"' : '';
			?>
			<?php if($field_id=='tag'): ?>
			    <?php if(!empty($count)): ?>
			     <div class="form-control-flat">
                    <label class="checkbox">
                        <input name="<?php echo $field_id; ?>[]"
                               type="checkbox" <?php echo $class; ?> <?php echo $checked; ?>
                               value="<?php echo esc_attr( $key ); ?>"/><i></i>
                        <?php echo esc_html( $label ); ?>
                            <?php if(!empty($count)): ?>
                             <span><?php echo esc_html($count)?></span>
                             <?php endif; ?>
                    </label>
                    </div>
                    <?php endif; ?>
			<?php else: ?>
                <div class="form-control-flat">
                    <label class="checkbox">
                        <input name="<?php echo $field_id; ?>[]"
                               type="checkbox" <?php echo $class; ?> <?php echo $checked; ?>
                               value="<?php echo esc_attr( $key ); ?>"/><i></i>
                        <?php echo esc_html( $label ); ?>
                        <span class="job-list-filter__count <?php echo empty($count) ? 'job-list-filter__count-empty' : '' ?>"><?php echo esc_html($count)?></span>
                    </label>
                </div>
			<?php endif; ?>
		<?php endforeach;
		}

		echo'</div>';
	}
endif;

if(!function_exists('noo_render_filter_radio_field')):
   function noo_render_filter_radio_field( $field = array(), $field_id = '', $value = '', $form_type = '', $list_post_query = array() ) {
		$input_id = $form_type == 'search' ? 'filter-' . $field_id : $field_id;
		$input_id = $input_id.uniqid();
		$class    = isset( $field['required'] ) && $field['required'] ? ' class="form-control jform-validate" required aria-required="true"' : ' class="form-control"';

		$field_value = noo_convert_custom_field_setting_value( $field );
		if ( $form_type == 'search' || $form_type == 'filter_resume') {
			$field_value = array( '' => esc_html__( 'All', 'noo' ) ) + $field_value;
		}
		$value = is_array( $value ) ? reset( $value ) : $value;
		echo'<div class="form-group filter-search-group">';
		echo'<input type="text" class="form-control filter-search-option" id="'.$input_id.'" placeholder="search '.$field['label'].'">';
		// echo'<i class="fa fa-filter"></i>';
		echo'</div>';
		echo'<div class="job-list-filter">';
		foreach ( $field_value as $key => $label ) :
		    if($form_type == 'filter_resume'){
		         if(in_array($field_id,array('category','location'))){
                     $term = get_term_by('slug',$key,'job_'.$field_id);
                     $key  = (!empty($term)) ? $term->term_id : '';
		         }
		        $count = noo_get_count_resume_field_filter($field_id,$key,$list_post_query);
		    }else{
		        $count   = noo_get_count_job_field_filter($field_id,$key,$list_post_query);
		    }
		    $checked = ( $key == $value ) ? 'checked="checked"' : '';
			?>
                <div class="form-control-flat">
                    <label class="radio">
                        <input type="radio" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php echo $class; ?> <?php echo esc_attr( $checked ); ?>><i></i><?php echo esc_html( $label ); ?>
                       <span class="job-list-filter__count <?php echo empty($count) ? 'job-list-filter__count-empty' : '' ?>"><?php echo esc_html($count)?></span>
                    
                    </label>
                </div>
		<?php endforeach;
		echo'</div>';
	}

endif;
if(!function_exists('noo_get_count_job_field_filter')):
    function noo_get_count_job_field_filter($field_id='',$key='',$list_post_query){
       	$list_job_id_field = array();
       	if( in_array( 'job_'.$field_id, jm_get_job_taxonomies() ) ){
        	if(!empty($list_post_query) || $list_post_query != null){
               //--- remove this action to that the query not overwrite
               remove_action('pre_get_posts','jm_job_pre_get_posts');
               $args = array(
                'post_type'  		=> 'noo_job',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'tax_query' => array(
	                    array(
	                            'taxonomy' =>'job_'.$field_id,
	                            'field' => 'slug',
	                            'terms' => $key,
	                    )
	                )
                );
                 $status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
                 $args['post_status'] = $status;
                 $query = new WP_Query( $args );
                 while ($query->have_posts()){
                     $query->the_post();
                     $list_job_id_field[] = get_the_ID();
                 }
                 if(!empty($list_post_query) || $list_post_query!= null){
                     if($list_post_query=='none'){
                         $count = 0;
                     }else{
                    	 $array_intersect = array_intersect($list_job_id_field,$list_post_query);
                     	 $count = count($array_intersect);
                     }
                 }else{
                     $count = $query->found_posts;
                 }

           }else{
	           if(is_tax( get_object_taxonomies( 'noo_job' ) )){
	           		global $wp_query;
	           		$current_tax = $wp_query->queried_object;
	           		$args = array(
	           			'post_type'  		=> 'noo_job',
	           			'post_status'       => 'publish',
	           			'posts_per_page'    => -1,
	           			'tax_query' => array(
	           				array(
	           					'taxonomy' =>'job_'.$field_id,
	           					'field' => 'slug',
	           					'terms' => $key,
	           				),
	           				array(
	           					'taxonomy' =>$current_tax->taxonomy,
	           					'field' => 'slug',
	           					'terms' => $current_tax->slug,
	           				)
	           			)
	           		);
	           		$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
	           		$args['post_status'] = $status;
	           		$query = new WP_Query( $args );
	           		$count = absint($query->found_posts);
	           }else{
	               $term = get_term_by('slug',$key,'job_'.$field_id);
	               $count =(!empty($term)) ? $term->count : '';
	           }
           }
       }else{
             //--- remove this action to that the query not overwrite
             remove_action('pre_get_posts','jm_job_pre_get_posts');
             $args = array(
                'post_type'  => 'noo_job',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'meta_query' => array(
                    array(
                        'key'     => $field_id,
                         'value'  => $key,
                        'compare' => 'LIKE',
                    ),
                ),
            );
               if($field_id=='_noo_job_field_posted_date_filter'){
                  switch ( $key ) {
					case 'monthly':
						$date_query['after'] = '-1 month';
						break;
					case 'fortnight':
						$date_query['after'] = '-1 fortnight';
						break;
					case 'daily':
						$date_query['after'] = '-1 day';
						break;
					case 'last-hour':
						$date_query['after'] = '-1 hour';
						break;
					default: // weekly
						$date_query['after'] = '-1 week';
						break;
				}
				unset($args['meta_query']);
				$args['date_query'] = array($date_query);
               }
                 $query = new WP_Query( $args );
                 while ($query->have_posts()){
                     $query->the_post();
                     $list_job_id_field[] = get_the_ID();
                 }
                 if(!empty($list_post_query) || $list_post_query!= null){
                      if($list_post_query=='none'){
                         $count = 0;
                     }else{
                     $array_intersect =array_intersect($list_job_id_field,$list_post_query);
                     $count = count($array_intersect);
                     }
                 }else{
                     $count = $query->found_posts;
                 }
        }
        wp_reset_postdata();
        return $count;
    }
endif;
if(!function_exists('noo_get_count_resume_field_filter')):
   function noo_get_count_resume_field_filter($field_id='',$value='',$list_post_query){
        $list_job_id_field = array();
             //--- remove this action to that the query not overwrite
            remove_action('pre_get_posts','jm_resume_pre_get_posts');
            $meta_query = array();
            if( jm_viewable_resume_enabled() ) {
                $meta_query[] = array(
                        'relation' => 'AND',
                               array(
                                    'key' => '_viewable',
                                    'value' => 'yes',
                                )
                            );
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
                                    'value'   => '"'.$child.'"',
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
            $args = array(
                'post_type'  => 'noo_resume',
                'post_status'=> 'publish',
                'posts_per_page'    => -1,
                'meta_query' => $meta_query,
            );
                 $query = new WP_Query( apply_filters('noo_get_count_resume_field_filter_query_args', $args) );
                 while ($query->have_posts()){
                     $query->the_post();
                     $list_job_id_field[] = get_the_ID();
                 }
                 if(!empty($list_post_query) || $list_post_query!= null){
                      if($list_post_query=='none'){
                         $count = 0;
                     }else{
                     $array_intersect =array_intersect($list_job_id_field,$list_post_query);
                     $count = count($array_intersect);
                     }
                 }else{
                     $count = $query->found_posts;
                 }
        return $count;
    }
endif;


