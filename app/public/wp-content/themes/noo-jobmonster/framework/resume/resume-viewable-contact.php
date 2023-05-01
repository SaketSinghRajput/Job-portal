<?php
if( !function_exists('jm_is_enabled_job_package_view_candidate_contact') ) :
	function jm_is_enabled_job_package_view_candidate_contact() {
		return 'package' == jm_get_action_control( 'view_candidate_contact' );
	}
endif;
if( !function_exists('jm_can_show_candidate_contact_with_package')):
    function jm_can_show_candidate_contact_with_package($resume_id = ''){
       $can_show = false;
        if('administrator'== Noo_Member::get_user_role(get_current_user_id())){
            return true;
        }
        $candidate_id = get_post_field('post_author',$resume_id);
        if($candidate_id == get_current_user_id()){
            return true;
        }
        if(isset($_GET['application_id']) && !empty($_GET['application_id'])){
            $job_id = get_post_field('post_parent',$_GET['application_id']);
            $company_id = noo_get_post_meta($job_id,'_company_id');
            $employer_id = get_post_field('post_author',$company_id);
            if($employer_id == get_current_user_id()){
                if($resume_id == noo_get_post_meta($_GET['application_id'],'_resume','')){
                    return true;
                }
            }
        }
        $resume_viewed_contact_saved =  jm_get_resume_candidate_contact_viewed(get_current_user_id());
        if(in_array((String)$resume_id,$resume_viewed_contact_saved)){
            $can_show = true;
        }
        return $can_show;
    }
endif;
if( !function_exists('jm_user_always_download_cv')):
    function jm_user_alwasy_download_cv($resume_id = ''){
        $can_download = false;
        if('administrator'== Noo_Member::get_user_role(get_current_user_id())){
            return true;
        }
        $candidate_id = get_post_field('post_author',$resume_id);
        if($candidate_id == get_current_user_id()){
            return true;
        }
        if(isset($_GET['application_id']) && !empty($_GET['application_id'])){
            $job_id = get_post_field('post_parent',$_GET['application_id']);
            $company_id = noo_get_post_meta($job_id,'_company_id');
            $employer_id = get_post_field('post_author',$company_id);
            if($employer_id == get_current_user_id()){
                if($resume_id == noo_get_post_meta($_GET['application_id'],'_resume','')){
                    return true;
                }
            }
        }
        return $can_download;
    }
endif;
if( !function_exists('jm_can_view_candidate_contact') ) :
	function jm_can_view_candidate_contact( $resume_id = null ) {
		if( jm_is_resume_posting_page() )  {
			return true;
		}
		if( empty( $resume_id ) ) {
			return false;
		}
        if( 'administrator' == Noo_Member::get_user_role(get_current_user_id()) ){
		    return true;
        }

		$can_view_candidate_contact_setting = jm_get_action_control('view_candidate_contact');
		if( empty( $can_view_candidate_contact_setting ) ) return true;

		// Resume's author can view his/her contact
		$candidate_id = get_post_field( 'post_author', $resume_id );
		if( $candidate_id == get_current_user_id() ) {
			return true;
		}

		if( isset($_GET['application_id'] ) && !empty($_GET['application_id']) ) {
			// Employers can view candidate contact from their applications

            $job_id = get_post_field( 'post_parent', $_GET['application_id'] );
            $company_id = noo_get_post_meta($job_id,'_company_id');
            $employer_id = get_post_field('post_author',$company_id);
			if( $employer_id == get_current_user_id() ) {
				if( $resume_id == noo_get_post_meta( $_GET['application_id'], '_resume', '' ) ) {
					return true;
				}
			}
		}

        $can_view_candidate_contact = true;
		switch( $can_view_candidate_contact_setting ) {
			case 'none':
				$can_view_candidate_contact = false;
				break;
			case 'employer':
				$can_view_candidate_contact = Noo_Member::is_employer();
				break;
			case 'package':
				$can_view_candidate_contact = false;
				$package = jm_get_job_posting_info();
				if( Noo_Member::is_employer() ) {
					$can_view_candidate_contact = isset( $package['can_view_candidate_contact'] ) && $package['can_view_candidate_contact']&& (jm_get_view_candidate_contact_remain()!=0);
				}
				break;
			default:
				$can_view_candidate_contact = true;
				break;
		}

		return  $can_view_candidate_contact;
	};
endif;

if( !function_exists('jm_get_view_candidate_contact_count')):
    function jm_get_view_candidate_contact_count($user_id=''){
        $view_candidate_contact_count = get_user_meta($user_id,'_view_candidate_contact_count',true);
        return empty($view_candidate_contact_count) ? 0 : absint($view_candidate_contact_count);
    }
endif;
if( !function_exists('jm_get_view_candidate_contact_remain')):
    function jm_get_view_candidate_contact_remain($user_id=''){
        if(empty($user_id)){
            $user_id = get_current_user_id();
        }
        $package = jm_get_job_posting_info($user_id);
        $view_candidate_contact_limit = empty($package) || !is_array($package) || !isset($package['view_candidate_contact_limit'])? 0 : $package['view_candidate_contact_limit'];
        if($view_candidate_contact_limit == -1) return -1;
        $view_candidate_contact_count = jm_get_view_candidate_contact_count($user_id);
        return max(absint($view_candidate_contact_limit)-absint($view_candidate_contact_count),0);
    }
endif;
if(!function_exists('jm_get_resume_candidate_contact_viewed')):
    function jm_get_resume_candidate_contact_viewed($user_id = ''){
        $resume_contact_viewed = get_user_meta($user_id,'_resume_contact_saved',true);
        $resume_contact_viewed = !is_array( $resume_contact_viewed) || empty( $resume_contact_viewed ) ? array() :   $resume_contact_viewed;

        return  $resume_contact_viewed;
    }
endif;
if( !function_exists('noo_count_view_candidate_contact_update')):
   function noo_count_view_candidate_contact_update($user_id = null,$resume_id = ''){
        $user_id = !empty($user_id) ? $user_id : get_current_user_id();
        $resume_contact_viewed = jm_get_resume_candidate_contact_viewed($user_id);
        $current_count_view = get_user_meta($user_id,'_view_candidate_contact_count',true);
        if(in_array($resume_id,$resume_contact_viewed)){
            return $current_count_view;
        }else{
            $view_candidate_count = update_user_meta($user_id,'_view_candidate_contact_count',$current_count_view + 1);
            $resume_contact_viewed[] = $resume_id;
            update_user_meta($user_id,'_resume_contact_saved',$resume_contact_viewed);
        }
        return $view_candidate_count;
   }
endif;
if(!function_exists('jm_display_candidate_contact')):
    function jm_display_candidate_contact($resume_id=''){
        $fields_candidate = jm_get_candidate_custom_fields();
        $candidate_id = get_post_field('post_author', $resume_id);
        $candidate = !empty($candidate_id) ? get_userdata($candidate_id) : false;
        $email = $candidate ? $candidate->user_email : '';
        $html = array();
        foreach ($fields_candidate as $field){
            if (isset($field['is_default'])) {
                if (in_array($field['name'], array('first_name', 'last_name', 'full_name', 'email',)))
                    continue; // don't display WordPress default user fields
            }
            $field_id = jm_candidate_custom_fields_name($field['name'], $field);
            $value = get_user_meta($candidate_id, $field_id, true);
            $value_file_upload = $value;
            $value = noo_convert_custom_field_value($field, $value);
            $value_date = noo_convert_custom_field_value($field, $value);
            $icon = isset($field['icon']) ? $field['icon'] : '';
            $icon_class = str_replace("|", " ", $icon);
            if(!empty($value)){
                if ($field['type'] == 'datepicker'){
                    $date = date('d/M/Y', $value_date);
                    $html[] = '<div class="'.esc_attr($field_id).' col-md-4"><div class="'.esc_attr($field_id).'"><span class="candidate-field-icon"><i class="'.esc_attr($icon_class).'"></i></span>'.$date.'</div></div>';
                }elseif ($field['type'] == 'file_upload') {
                    $files = noo_json_decode( $value_file_upload);
                    foreach ($files as $file) {
                        $file_url = noo_get_file_upload($file);
                        $html[] ='<div class="'.esc_attr($field_id).' col-md-4"><div class="'.esc_attr($field_id).'"><span class="candidate-field-icon"><i class="'.esc_attr($icon_class).'"></i></span> <a href="'. esc_url($file_url) .'" target="_blank" class="link-alt">'.esc_html($file). '</a></div></div>';
                    }
                }else{
                    $html[] = '<div class="'.esc_attr($field_id).' col-md-4"><div class="'.esc_attr($field_id).'"><span class="candidate-field-icon"><i class="'.esc_attr($icon_class).'"></i></span>'.$value.'</div></div>';
                }

            }
        }
        $html[] ='<div class="email col-md-4"><a href="mailto:'.esc_attr($email).'"><span class="candidate-field-icon"><i class="fa fa-envelope text-primary"></i></span>'.esc_html($email).'</a></div>';
        return $html;
    }
endif;

function noo_ajax_count_view_candidate_contact_remain(){
    $user_id = isset($_POST['user_id'])? intval($_POST['user_id']) : 0;
    $resume_id = isset($_POST['resume_id']) ? ($_POST['resume_id']) : '';
    $html = jm_display_candidate_contact($resume_id);
    if(empty($user_id)){
        $response = array(
                'status' => 'error',
                'message' => esc_html__('This user does not exist','noo'),
        );
    }else{
        if( -1 == jm_get_view_candidate_contact_remain($user_id)){
            noo_count_view_candidate_contact_update($user_id,$resume_id);
            $response = array(
                'status'  => 'success',
                'remain'  =>  esc_html__('Unlimited views time.', 'noo'),
                'message' => esc_html__( 'success.', 'noo' ),
                'contact'  => $html
            );
        }elseif(jm_get_view_candidate_contact_remain($user_id) > 0){
            noo_count_view_candidate_contact_update($user_id,$resume_id);
            $rm = jm_get_view_candidate_contact_remain();
            $response = array(
                'status'  => 'success',
                'remain'  =>  sprintf(esc_html__('Remain %s views time.', 'noo'), $rm),
                'message' => esc_html__( 'success.', 'noo' ),
                'contact'  => $html
            );
        }else{
            $response = array(
                'status'  => 'error',
                'message' => esc_html__( 'You has exceeded the number of view.', 'noo' )
            );
        }
    }
    wp_send_json($response);
}
add_action( 'wp_ajax_view_candidate_contact_remain', 'noo_ajax_count_view_candidate_contact_remain' );

/*----- Update dowload remain ----*/
if( !function_exists('jm_get_download_cv_remain')):
    function jm_get_download_cv_remain($user_id = ''){
        if(empty($user_id)){
            $user_id = get_current_user_id();
        }
        $package = jm_get_job_posting_info($user_id);
        $download_resume_limit= empty($package) || !is_array($package) || !isset($package['download_resume_limit']) ? 0 :$package['download_resume_limit'];
        if($download_resume_limit == -1) return -1;

        // $downloaded_resume_count = jm_get_downloaded_cv_count($user_id);
        $downloaded_resume_count = get_user_meta($user_id,'_download_cv_count',true);
        $downloaded_resume_count = empty($downloaded_resume_count) ? 0 : absint($downloaded_resume_count);
        return max(absint($download_resume_limit) - absint($downloaded_resume_count),0);
    }
endif;
function noo_ajax_download_cv_count() {

    $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
    $resume_id = isset($_POST['resume_id']) ? intval($_POST['resume_id']) : '';
    $link_download = isset($_POST['link_download']) ? ($_POST['link_download']) : '';
    if ( empty( $user_id ) ) {
        $response = array(
            'status'  => 'error',
            'message' => esc_html__( 'This user does not exist.', 'noo' )
        );
    }elseif(empty($link_download)){
        $response = array(
            'status'  => 'error',
            'message' => esc_html__( 'No resume found.', 'noo' )
        );
    }else {
        $download_remain = jm_get_download_cv_remain($user_id);
        if( $download_remain > 0){            
            $current = get_user_meta($user_id, '_download_cv_count', true);
            $downloaded_cv = get_user_meta($user_id,'_download_cv_saved',true);
            if(empty($downloaded_cv)) $downloaded_cv = array();
            if(in_array($resume_id,$downloaded_cv)){
                if(strlen((string)$download_remain) >= 7) $download_remain = esc_html__('Unlimited','noo'); // check download resume unlimit -1 <-> 99999999 download times
                $response = array(
                    'status'  => 'success',
                    'remain'  =>  sprintf(esc_html__('Remain %s download times.', 'noo'), $download_remain),
                    'link' => noo_get_file_upload($link_download),
                    'message' => esc_html__( 'success.', 'noo' )
                );
            }elseif (!in_array($resume_id,$downloaded_cv)){
                // Update number of resume downloads
                update_user_meta($user_id, '_download_cv_count', $current + 1);

                // Update the downloaded ID profile
                $downloaded_cv[] = $resume_id;
                update_user_meta($user_id,'_download_cv_saved', $downloaded_cv);

                // Get remain download resume after update.
                $rm = jm_get_download_cv_remain($user_id);
                if(strlen((string)$rm) >= 7) $rm = esc_html__('Unlimited','noo'); // check download resume unlimit -1 <-> 99999999 download times
                $response = array(
                    'status'  => 'success',
                    'remain'  =>  sprintf(esc_html__('Remain %s download times.', 'noo'), $rm),
                    'link' => noo_get_file_upload($link_download),
                    'message' => esc_html__( 'success.', 'noo' )
                );
            }
        }else{
            $response = array(
                'status'  => 'error',
                'message' => esc_html__( 'You has exceeded the number of download.', 'noo' )
            );
        }

    }
    wp_send_json( $response );
}

add_action( 'wp_ajax_noo_ajax_download_cv_count', 'noo_ajax_download_cv_count' );

/* ----- End Update download remain --------*/

/* ------ Check account can download ----------*/
if( !function_exists('jm_can_download_cv_upload')):
    function jm_can_download_cv_upload($resume_id = null)
    {
        if (jm_is_resume_posting_page()) {
            return true;
        }
        if (empty($resume_id)) {
            return false;
        }
        if ('administrator' == Noo_Member::get_user_role(get_current_user_id())) {
            return true;
        }
        $can_download_resume_setting = jm_get_resume_setting('who_can_download_resume');
        if(empty($can_download_resume_setting)){
            return true;
        }
        // Resum's author can view his/her contact
        $candidate_id = get_post_field('post_author',$resume_id);
        if($candidate_id == get_current_user_id()){
            return true;
        }
        $can_download_resume = false;
        switch ($can_download_resume_setting){
            case 'public':
                $can_download_resume = true;
                break;
            case 'user':
                $can_download_resume = Noo_Member::is_logged_in();
                break;
            case 'employer':
                $can_download_resume = Noo_Member::is_employer();
                break;
            case 'package':
                if( Noo_Member::is_employer() ) {
                    $package = jm_get_job_posting_info();
                    $can_download_resume = ( isset( $package['download_resume_limit'] ) && $package['download_resume_limit'] >= 1 ) && ( jm_get_download_cv_remain() != 0 );
                }
                break;
        }
        return $can_download_resume;
    }
endif;
if( !function_exists('jm_message_cannot_download_cv_candidate')):
    function jm_message_cannot_download_cv_candidate(){
        $message = '';
        $link = '';
        $can_download_cv = jm_get_resume_setting('who_can_download_resume');
        switch( $can_download_cv ) {
            case 'public':
                $message = __( 'There\'s an unknown error. Please retry or contact Administrator.<br />', 'noo' );
                break;
            case 'user':
                $message= __('Only logged in users can download candidate\'s CV.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login', 'noo' ) . '</a>';
                }
                break;
            case 'employer':
                $message = __('Only employers can download  candidate\'s CV.<br />','noo');
                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Logout', 'noo' ) . '</a>';
                }
                break;
            case 'package':
                $message = __('Only employers with package can download candidate\'s CV.<br />','noo');
                $link = Noo_Member::get_endpoint_url('manage-plan');

                if( !Noo_Member::is_logged_in() ) {
                    $link = Noo_Member::get_login_url();
                    $link = '<a href="' . esc_url( $link ) . '" class=" member-login-link">' . __( 'Login as Employer', 'noo' ) . '</a>';
                } elseif( !Noo_Member::is_employer() ) {
                    $link = Noo_Member::get_logout_url();
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Logout', 'noo' ) . '</a>';
                } else {
                    $message = __('Your membership doesn\'t allow you to download candidate\'s CV.<br />','noo');
                    $link = Noo_Member::get_endpoint_url('manage-plan');
                    $link = '<a href="' . esc_url( $link ) . '" class="">' . __( 'Click here to upgrade your Membership.', 'noo' ) . '</a>';
                }
                break;
        }
        return array($message,$link);
    }
endif;

if( !function_exists('jm_resume_is_show_candidate_contact') ) :
	function jm_resume_is_show_candidate_contact( $show_contact = true, $resume_id = '' ) {
		return jm_can_view_candidate_contact( $resume_id );
	};

	add_filter( 'jm_resume_show_candidate_contact', 'jm_resume_is_show_candidate_contact', 10, 2 );
endif;

if( !function_exists('jm_job_package_view_candidate_contact_data') ) :
	function jm_job_package_view_candidate_contact_data() {
		global $post;
		if( jm_is_enabled_job_package_view_candidate_contact() ) {
			woocommerce_wp_checkbox(
				array(
					'id' => '_can_view_candidate_contact',
					'label' => __( 'Can view Candidate Contact', 'noo' ),
					'description' => __( 'Allowing buyers to see Candidate Contact.', 'noo' ),
                    'value'       => get_post_meta( $post->ID, '_can_view_candidate_contact', true ),
					'desc_tip' => false,) );
            $custom_attributes = (!get_post_meta( $post->ID, '_can_view_candidate_contact', true )) ? 'disabled' : '';
            woocommerce_wp_text_input( array(
                'id'                => '_view_candidate_contact_limit',
                'label'             => __( 'View Candidate\'s Contact Info Limit', 'noo' ),
                'description'       => __( 'The number which user can view candidate\'s contact info with this package. Input -1 for unlimited.', 'noo' ),
                'value'             => max( get_post_meta( $post->ID, '_view_candidate_contact_limit', true ), -1 ),
                'placeholder'       => '',
                'type'              => 'number',
                'desc_tip'          => true,
                'custom_attributes' => array(
                    'min'              => '',
                    'step'             => '1',
                    $custom_attributes => $custom_attributes,
                ),
            ) );?>
            <script type="text/javascript">
                jQuery('.pricing').addClass('show_if_job_package');
					jQuery(document).ready(function ($) {
                        $("#_can_view_candidate_contact").change(function () {
                            if (this.checked) {
                                $('#_view_candidate_contact_limit').prop('disabled', false);
                            } else {
                                $('#_view_candidate_contact_limit').prop('disabled', true);
                            }
                        });
                    });
				</script>
            <?php
		}
	}

	add_action( 'noo_job_package_data', 'jm_job_package_view_candidate_contact_data' );
endif;

if( !function_exists('jm_job_package_save_view_candidate_contact_data') ) :
	function jm_job_package_save_view_candidate_contact_data($post_id) {
		if( jm_is_enabled_job_package_view_candidate_contact() ) {
			// Save meta
			$fields = array(
				'_can_view_candidate_contact'  => '',
                '_view_candidate_contact_limit' => 'int',
			);
			foreach ( $fields as $key => $value ) {
				$value = ! empty( $_POST[ $key ] ) ? $_POST[ $key ] : '';
				switch ( $value ) {
					case 'int' :
						$value = intval( $value );
						break;
					case 'float' :
						$value = floatval( $value );
						break;
					default :
						$value = sanitize_text_field( $value );
				}
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	add_action( 'noo_job_package_save_data', 'jm_job_package_save_view_candidate_contact_data' );
endif;

if( !function_exists('jm_job_package_view_candidate_contact_user_data') ) :
	function jm_job_package_view_candidate_contact_user_data( $data, $product ) {
		if( jm_is_enabled_job_package_view_candidate_contact() && is_object( $product ) ) {
			$data['can_view_candidate_contact'] = $product->get_can_view_candidate_contact();
			$data['view_candidate_contact_limit'] = $product-> get_view_candidate_contact_limit();
		}

		return $data;
	}

	add_filter( 'jm_job_package_user_data', 'jm_job_package_view_candidate_contact_user_data', 10, 2 );
endif;

if( !function_exists('jm_job_package_view_candidate_contact_features') ) :
	function jm_job_package_view_candidate_contact_features( $product ) {
		if( jm_is_enabled_job_package_view_candidate_contact() && $product->get_can_view_candidate_contact()) :
            $view_candidate_contact_limit = $product->get_view_candidate_contact_limit();
            ?>
			<!-- <li class="noo-li-icon"><i class="fa fa-check-circle"></i> <?php //_e('View Candidate contact info','noo');?></li> -->
            <?php if( $view_candidate_contact_limit == -1 ) : ?>
                <li class="noo-li-icon"><i class="fa fa-check-circle"></i> <?php _e('View Candidate contact info unlimited','noo');?></li>
            <?php elseif( $view_candidate_contact_limit > 0 ) : ?>
                <li class="noo-li-icon"><i class="fa fa-check-circle"></i> <?php echo sprintf( _n( '%d View Candidate\'s Contact Info', '%d Views Candidate\'s contact',$view_candidate_contact_limit, 'noo' ), $view_candidate_contact_limit ); ?></li>
            <?php endif; ?>
    	<?php endif;
	}

	add_action( 'jm_job_package_features_list', 'jm_job_package_view_candidate_contact_features' );
endif;

if( !function_exists('jm_manage_plan_view_candidate_contact_features') ) :
	function jm_manage_plan_view_candidate_contact_features( $package ) {
        if(!Noo_Member::is_employer() || !jm_is_enabled_job_package_view_candidate_contact() || !isset($package['product_id'])){
            return;
        }
		if(is_array( $package ) && isset( $package['product_id'] ) && !empty( $package['product_id'] ) ) {
			$product = wc_get_product( absint( $package['product_id'] ) );
            $view_candidate_contact_limit = isset($package['view_candidate_contact_limit']) ? $package['view_candidate_contact_limit'] : '' ;
            $resume_view_remain = jm_get_view_candidate_contact_remain();
			if( jm_is_enabled_job_package_view_candidate_contact() && $package['can_view_candidate_contact']) : ?>
                <div class="col-xs-6"><strong><?php _e('View Candidate \'s Contact','noo')?></strong></div>
                <div class="col-xs-6"><?php _e('Yes','noo')?></div>
                <?php if(  $view_candidate_contact_limit ) : ?>
                    <div class="col-xs-6"><strong><?php _e(' View Candidate\'s Contact Info Limit','noo')?></strong></div>
                    <?php if( $view_candidate_contact_limit == -1) : ?>
                        <div class="col-xs-6"><?php _e('Unlimited','noo');?></div>
                    <?php elseif(  $view_candidate_contact_limit > 0 ) : ?>
                        <div class="col-xs-6"><?php echo sprintf( _n( '%d view', '%d views',  $view_candidate_contact_limit, 'noo' ),  $view_candidate_contact_limit ); ?>
                            <?php if( $resume_view_remain <  $view_candidate_contact_limit ) echo '&nbsp;' . sprintf( __('( %d remain )', 'noo'), $resume_view_remain ); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
				
	    	<?php endif;
		}
	}

	add_action( 'jm_manage_plan_features_list', 'jm_manage_plan_view_candidate_contact_features' );
endif;
if( !function_exists('jm_job_package_view_resume_contact_order_completed') ) :
    function jm_job_package_view_resume_contact_order_completed( $product, $user_id ) {
        if( jm_is_enabled_job_package_view_candidate_contact() && $product->get_can_view_candidate_contact() ) {
            update_user_meta( $user_id, '_view_candidate_contact_count', 0);
        }
    }

    add_action( 'jm_job_package_order_completed', 'jm_job_package_view_resume_contact_order_completed', 10, 2 );
endif;