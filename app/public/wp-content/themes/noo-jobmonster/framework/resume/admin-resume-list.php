<?php

if( !function_exists( 'jm_admin_resume_list_columns_header' ) ) :
	function jm_admin_resume_list_columns_header( $columns ) {
		unset($columns['date']);

		$before = array_slice($columns, 0, 2);
		$after = array_slice($columns, 2);
		
		$new_columns = array(
			'candidate_id' => __('Candidate', 'noo'),
			'viewable' => __('Viewable', 'noo'),
			'job_category' => __('Job Category', 'noo'),
			'job_location' => __('Job Location', 'noo'),
			'status'  => __( 'Status', 'noo' ),
			'featured'  => __( 'Featured?', 'noo' ),
			'date' => __( 'Date', 'noo' ),
			'job_actions' => __( 'Actions', 'noo' )
		);

		if( !jm_viewable_resume_enabled() ) {
			unset( $new_columns['viewable'] );
		}

		$columns = array_merge($before, $new_columns, $after);

		return $columns;
	}
	add_filter( 'manage_edit-noo_resume_columns', 'jm_admin_resume_list_columns_header' );
endif;

if( !function_exists( 'jm_admin_resume_list_columns_data' ) ) :
	function jm_admin_resume_list_columns_data( $column ) {
			GLOBAL $post;
			$post_id = get_the_ID();

			if ($column == 'candidate_id') {
				$candidate_id = esc_attr( $post->post_author );

				if( !empty( $candidate_id ) ) {
					$candidate = get_userdata( $candidate_id );
					$name = !empty($candidate->display_name) ? $candidate->display_name : $candidate->login_name ;
//                   $name = !empty($candidate) ? $candidate->display_name : '';

					echo '<a href="'. get_edit_user_link( $candidate_id ) . '" target="_blank">' . $name. '</a>';
				}
				
			}

			if ( $column == 'viewable' ) {
				$viewable = esc_attr( noo_get_post_meta($post_id, '_viewable') );

				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=noo_viewable_resume&resume_id=' . $post_id ), 'resume-viewable' );
				echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle viewable', 'noo' ) . '">';
				if ( $viewable == 'yes' ) {
					echo '<span class="noo-post-viewable" title="'.esc_attr__('Yes','noo').'"><i class="dashicons dashicons-visibility "></i></span>';
				} else {
					echo '<span class="noo-post-viewable not-featured"  title="'.esc_attr__('No','noo').'"><i class="dashicons dashicons-hidden"></i></span>';
				}
				echo '</a>';
			}

			if ($column == 'job_category') {
				$job_category = noo_get_post_meta($post_id, '_job_category');
				$job_category = noo_json_decode($job_category);

				if( !empty( $job_category ) ) {
					$job_category_terms = get_terms( 'job_category', array('hide_empty' => 0, 'include' => array_merge( $job_category, array(-1) )) );
					$category_terms = array();
					foreach ($job_category_terms as $job_category_term ) {
						$category_terms[] = edit_term_link( $job_category_term->name, '', '', $job_category_term, false );
					}
					echo implode(', ', $category_terms);
				}
			}

			if ($column == 'job_location') {
				$job_location = noo_get_post_meta($post_id, '_job_location');
				$job_location = noo_json_decode($job_location);

				if( !empty( $job_location ) ) {
					$job_location_terms = get_terms( 'job_location', array('hide_empty' => 0, 'include' => array_merge( $job_location, array(-1) )) );
					$location_terms = array();
					foreach ($job_location_terms as $job_location_term ) {
						$location_terms[] = edit_term_link( $job_location_term->name, '', '', $job_location_term, false );
					}
					echo implode(', ', $location_terms);
				}
			}

            if ( $column == 'featured' ) {
                $featured = noo_get_post_meta( $post->ID, '_featured' );
                if ( empty( $featured ) ) {
                    // Update old data
                    update_post_meta( $post->ID, '_featured', 'no' );
                }
                $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=noo_resume_feature&resume_id=' . $post->ID ), 'noo-resume-feature' );
                echo '<a href="' . esc_url( $url ) . '" title="' . __( 'Toggle featured', 'noo' ) . '">';
                if ( 'yes' === $featured ) {
                    echo '<span class="noo-resume-feature" title="' . esc_attr__( 'Yes', 'noo' ) . '"><i class="dashicons dashicons-star-filled "></i></span>';
                } else {
                    echo '<span class="noo-resume-feature not-featured"  title="' . esc_attr__( 'No', 'noo' ) . '"><i class="dashicons dashicons-star-empty"></i></span>';
                }
                echo '</a>';

            }

			if ( $column == 'status' ) {
				$status   = $post->post_status;
				$status_text = '';
				$statuses = jm_get_resume_status();
				if ( isset( $statuses[ $status ] ) ) {
					$status_text = $statuses[ $status ];
				} else {
					$status_text = __( 'Inactive', 'noo' );
				}
				echo esc_html( $status_text );
			}
			if ( $column == 'job_actions' ) {
				echo '<div class="actions">';
				$admin_actions           = array();
				if ( $post->post_status == 'pending' && current_user_can ( 'publish_post', $post->ID ) ) {
					$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=noo_approve_resume&resume_id=' . $post->ID ), 'resume-approve' );
					echo '<a href="' . esc_url( $url ) . '" title="'. __( 'Toggle viewable', 'noo' ) . '">';
					$admin_actions['approve']   = array(
						'action'  => 'approve',
						'name'    => __( 'Approve', 'noo' ),
						'url'     =>  $url,
						'icon'	  => 'yes',
					);
				}
				if ( $post->post_status !== 'trash' ) {
					if ( current_user_can( 'read_post', $post->ID ) ) {
						$admin_actions['view']   = array(
							'action'  => 'view',
							'name'    => __( 'View', 'noo' ),
							'url'     => $post->post_status == 'draft' ? esc_url( get_preview_post_link( $post ) ) : get_permalink( $post->ID ),
							'icon'	  => 'visibility',
						);
					}
					if ( current_user_can( 'edit_post', $post->ID ) ) {
						$admin_actions['edit']   = array(
							'action'  => 'edit',
							'name'    => __( 'Edit', 'noo' ),
							'url'     => get_edit_post_link( $post->ID ),
							'icon'	  => 'edit',
						);
					}
					if ( current_user_can( 'delete_post', $post->ID ) ) {
						$admin_actions['delete'] = array(
							'action'  => 'delete',
							'name'    => __( 'Delete', 'noo' ),
							'url'     => get_delete_post_link( $post->ID ),
							'icon'	  => 'trash',
						);
					}
				}
			
				$admin_actions = apply_filters( 'resume_manager_admin_actions', $admin_actions, $post );
			
				foreach ( $admin_actions as $action ) {
					printf( '<a class="button tips action-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url( $action['url'] ), esc_attr( $action['name'] ), '<i class="dashicons dashicons-'.$action['icon'].'"></i>' );
				}
			
				echo '</div>';
			
			}
		}
	add_filter( 'manage_noo_resume_posts_custom_column', 'jm_admin_resume_list_columns_data' );
endif;

if( !function_exists( 'jm_admin_resume_list_filter' ) ) :
	function jm_admin_resume_list_filter() {
		$type = 'post';
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}

		//only add filter to post type you want
		if ('noo_resume' == $type){
			global $post;

			// Candidate
			$candidates = jm_get_members( Noo_Member::CANDIDATE_ROLE );
			$filter_custom_fields = jm_get_filter_resume();
		    $default_fields = jm_get_resume_custom_fields();
		    $filter_field = array();
		    foreach ($default_fields as $field){
		        if(!in_array($field['name'],$filter_custom_fields)){
		            continue;
                }
                if($field['type'] == 'multiple_select'){
		            $field['type'] = 'select';
                }
                $filter_field[] = $field;
            }
            foreach ($filter_field as $field){
                $field_id = jm_resume_custom_fields_name($field['name'], $field);
                $params = apply_filters('jm_resume_render_search_field_params', compact('field', 'field_id'));
                extract($params);

                $field['required'] = ''; // no need for required fields in search form

                $value = isset($_GET[$field_id]) ? $_GET[$field_id] : '';
                $value = !is_array($value) ? trim($value) : $value;
                noo_render_field($field, $field_id, $value, 'search','',$field['label']);
            }
            ?>
			<select name="candidate">
				<option value=""><?php _e('All Candidates', 'noo'); ?></option>
				<?php
				$current_v = isset($_GET['candidate'])? $_GET['candidate']:'';
				foreach ($candidates as $candidate) {
					printf
					(
						'<option value="%s"%s>%s</option>',
						$candidate->ID,
						$candidate->ID == $current_v ? ' selected="selected"':'',
						empty( $candidate->display_name ) ? $candidate->login_name : $candidate->display_name
					);
				}
				?>
			</select>
			<?php
			// Job Category
			$job_categories = get_terms( 'job_category', array( 'hide_empty' => false ) );
			?>
			<select name="category">
				<option value=""><?php _e('All Categories', 'noo'); ?></option>
				<?php
				$current_v = isset($_GET['category'])? $_GET['category']:'';
				foreach ($job_categories as $job_category) {
					printf
					(
						'<option value="%s"%s>%s</option>',
						$job_category->term_id,
						$job_category->term_id == $current_v ? ' selected="selected"':'',
						$job_category->name
					);
				}
				?>
			</select>
			<?php
			// Job Locations
			$job_locations = get_terms( 'job_location', array( 'hide_empty' => false )  );
			?>
			<select name="location">
				<option value=""><?php _e('All Locations', 'noo'); ?></option>
				<?php
				$current_v = isset($_GET['location'])? $_GET['location']:'';
				foreach ($job_locations as $job_location) {
					printf
					(
						'<option value="%s"%s>%s</option>',
						$job_location->term_id,
						$job_location->term_id == $current_v ? ' selected="selected"':'',
						$job_location->name
					);
				}
				?>
			</select>
            <input type="submit" name="export_all_posts" id="export_all_posts" class="button button-primary" value="Export Resume">
            <script type="text/javascript">
                jQuery(function ($) {
                    $('#export_all_posts').insertAfter('#post-query-submit');
                })
            </script>
			<?php

		}
	}

	add_action( 'restrict_manage_posts', 'jm_admin_resume_list_filter' );
endif;
if( !function_exists('func_export_all_posts')):
    function func_export_all_posts(){
        if(isset($_GET['export_all_posts'])){
            $arg = array(
                'post_type' => 'noo_resume',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
            $arr_post = get_posts($arg);
            if($arr_post){
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"resume.csv\";" );
                header("Content-Transfer-Encoding: binary");

                $file = fopen('php://output', 'w');
                $name_field = array();
                $fields = jm_get_resume_custom_fields();
                foreach ($fields as $field){
                    if($field['name'] =='_job_category' || $field['name']=='_job_location' || (in_array($field['type'],array('datepicker','single_image','image_gallery','file_upload','embed_video')))){
                        continue;
                    }
                    $name_field[] =$field['label'];
                }
                $name_field_put = array_merge(array('Post Title','Candidate Name','Location','Category','Resume Attachment','Facebook','Twitter','Email','Google'),$name_field);
                fputcsv($file,$name_field_put);
                foreach ($arr_post as $post) {
                    $candidate = get_user_by('id', $post->post_author);
                    $candidate_name = !empty($candidate) ? $candidate->display_name : '';
                    $resume_locations =noo_json_decode(noo_get_post_meta($post->ID, '_job_location', ''));
                    $resume_category =noo_json_decode(noo_get_post_meta($post->ID, '_job_category', ''));
                    $field_resumes = jm_get_resume_custom_fields();
                    $field_put = array();
                    foreach ($field_resumes as $field_resume){
                        if($field_resume['name'] =='_job_category' || $field_resume['name']=='_job_location' || (in_array($field_resume['type'],array('datepicker','single_image','image_gallery','file_upload','embed_video')))){
                            continue;
                        }
                        $id = jm_resume_custom_fields_name($field_resume['name'], $field_resume);
                        $value = $post->ID ? noo_get_post_meta($post->ID, $id, '') : '';
                        $value = (is_array($value)) ? implode('_',$value) : $value;
                        $field_put[$field_resume['name']] = $value ;
                    }
                    $file_cv = noo_json_decode(noo_get_post_meta($post->ID, '_noo_file_cv'));
                    $resume_attachment = (!empty($file_cv)) ? noo_get_file_upload( $file_cv[ 0 ]) : '';
                    $loc_name = array();
                    if(!empty($resume_locations)){
                        foreach ($resume_locations as $loc) {
                            $term = get_term_by('id', $loc, 'job_location');
                            $loc_name[] = (!empty($term))? $term->name : '';
                        }
                    }
                    $cat_name = array();
                    if(!empty($resume_category)){
                        foreach ($resume_category as $cat) {
                            $term = get_term_by('id', $cat, 'job_category');
                            $cat_name[] =(!empty($term)) ? $term->name : '';
                        }
                    }
                    $csv_default =array(
                        'title' => get_the_title($post),
                        'candidate' => $candidate_name,
                        'resume_location' => implode('-',$loc_name),
                        'resume_category' => implode('-',$cat_name),
                        'resume_attachment' =>(is_array($resume_attachment)) ? implode('-',$resume_attachment) : $resume_attachment ,
                        'facebook' => noo_get_post_meta($post->ID, 'facebook', ''),
                        'twitter' => noo_get_post_meta($post->ID, 'twitter', ''),
                        'email' => noo_get_post_meta($post->ID, 'email', ''),
                        'google' => noo_get_post_meta($post->ID, 'googleplus', ''),
                    );
                    $csv = array_merge($csv_default,$field_put);

                    fputcsv($file, $csv);
                }
                exit();
            }
        }
    }
    add_action( 'init', 'func_export_all_posts' );
endif;
if( !function_exists( 'jm_admin_resume_list_remove_date_filter' ) ) :
	function jm_admin_resume_list_remove_date_filter() {
		if( 'noo_resume' == get_post_type() ) {
			return true;
		}
	}

	add_filter('disable_months_dropdown', 'jm_admin_resume_list_remove_date_filter');
endif;

if( !function_exists( 'jm_admin_resume_list_filter_action' ) ) :
	function jm_admin_resume_list_filter_action( $query ){
		global $pagenow;
		$type = 'post';
        $filter_custom_fields = jm_get_filter_resume();
		if (isset($_GET['post_type'])) {
			$type = $_GET['post_type'];
		}
		if ( 'noo_resume' == $type && is_admin() && $pagenow=='edit.php' ) {
			if( !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] == 'noo_resume' ) {
				if( isset($_GET['candidate']) && $_GET['candidate'] != '') {
					$candidate_id = $_GET['candidate'];
					if( !is_numeric( $candidate_id ) ) {
						// try get by email
						$candidate = get_user_by( 'email', trim( $candidate_id ) );
						$candidate_id = !empty( $candidate ) ? $candidate->ID : '';
					}

					$query->query_vars['author'] = $candidate_id;
				}
				if( isset($_GET['category']) && $_GET['category'] != '') {
					$query->query_vars['meta_query'][]	= array(
						'key'     => '_job_category',
						'value'   =>'"'.$_GET['category'].'"',
						'compare' =>'LIKE'
					);
				}
				if( isset($_GET['location']) && $_GET['location'] != '') {
					$query->query_vars['meta_query'][]	= array(
						'key'     => '_job_location',
						'value'   =>'"'.$_GET['location'].'"',
						'compare' =>'LIKE'
					);
				}
				foreach ($filter_custom_fields as $field){
				    if(isset($_GET[$field]) && ($_GET[$field] != '')){
                        $query->query_vars['meta_query'][]	= array(
                            'key'     => $field,
                            'value'   =>$_GET[$field],
                            'compare' =>'LIKE'
                        );
                    }
                }
			}
		}
	}

	add_filter( 'parse_query', 'jm_admin_resume_list_filter_action' );
endif;

if( !function_exists( 'jm_admin_resume_list_views_status' ) ) :
	function jm_admin_resume_list_views_status( $views ) {
		if( isset( $views['publish'] ) )
			$views['publish'] = str_replace( 'Published ', _x('Active', 'Resume status', 'noo') . ' ', $views['publish'] );

		return $views;
	}

	add_filter( 'views_edit-noo_resume', 'jm_admin_resume_list_views_status' );
endif;

if( !function_exists( 'jm_admin_resume_list_viewable_ajax' ) ) :
	function jm_admin_resume_list_viewable_ajax(){
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'noo' ), '', array( 'response' => 403 ) );
		}

		if ( ! check_admin_referer( 'resume-viewable' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'noo' ), '', array( 'response' => 403 ) );
		}

		$resume_id = ! empty( $_GET['resume_id'] ) ? (int) $_GET['resume_id'] : '';

		if ( ! $resume_id || get_post_type( $resume_id ) !== 'noo_resume' ) {
			die();
		}

		$viewable = noo_get_post_meta( $resume_id, '_viewable', true );

		if ( $viewable && $viewable !== 'yes' ) {
			update_post_meta( $resume_id, '_viewable', 'yes' );
		} else {
			update_post_meta( $resume_id, '_viewable', 'no' );
		}

		wp_safe_redirect( esc_url_raw( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) ) );
		die();
	}

	add_filter( 'wp_ajax_noo_viewable_resume', 'jm_admin_resume_list_viewable_ajax' );
endif;

if( !function_exists( 'jm_admin_resume_list_approve_ajax' ) ) :
	function jm_admin_resume_list_approve_ajax(){
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'noo' ), '', array( 'response' => 403 ) );
		}

		if ( ! check_admin_referer( 'resume-approve' ) ) {
			wp_die( __( 'You have taken too long. Please go back and retry.', 'noo' ), '', array( 'response' => 403 ) );
		}

		$resume_id = ! empty( $_GET['resume_id'] ) ? (int) $_GET['resume_id'] : '';

		if ( ! $resume_id || get_post_type( $resume_id ) !== 'noo_resume' ) {
			die();
		}

		$resume_data = array(
			'ID'          => $resume_id,
			'post_status' => 'publish'
		);
		wp_update_post( $resume_data );

		wp_safe_redirect( esc_url_raw( remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) ) );
		die();
	}

	add_filter( 'wp_ajax_noo_approve_resume', 'jm_admin_resume_list_approve_ajax' );
endif;

if( !function_exists( 'jm_admin_resume_transition_post_status' ) ) :
	function jm_admin_resume_transition_post_status( $new_status, $old_status, $post ) {
		if( $post->post_type !== 'noo_resume' )
			return;

		if( !noo_get_post_meta( $post->ID, '_in_review', '' ) ) {
			return;
		}

		if( !is_admin() ) {
			return;
		}

		if( $new_status == 'publish' && $old_status != 'publish' ) {
			$candidate_id = $post->post_author;

			wp_update_post(array(
				'ID'=>$post->ID,
				'post_date'		=> current_time( 'mysql' ),
				'post_date_gmt'	=> current_time( 'mysql' , 1 )
			));

			jm_set_resume_expired( $post->ID );
			update_post_meta( $post->ID, '_in_review', '' );

			// candidate email
			if( jm_et_get_setting('candidate_resume_approved_activated') ) {

				if ( is_multisite() )
					$blogname = $GLOBALS['current_site']->site_name;
				else
					$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
				$candidate = get_user_by( 'id', $candidate_id );

				$to = $candidate->user_email;

				$array_replace = array(
					'[resume_title]' => $post->post_title,
					'[resume_url]' => get_permalink($post),
					'[site_name]' => $blogname,
					'[site_url]' => esc_url(home_url('')),
				);

				$subject = jm_et_get_setting('candidate_resume_approved_subject');
				$subject = str_replace(array_keys($array_replace), $array_replace, $subject);

				$message = jm_et_get_setting('candidate_resume_approved_content');
				$message = str_replace(array_keys($array_replace), $array_replace, $message);

				noo_mail($to, $subject, $message,'','noo_notify_resume_review_approve_candidate');
			}
		}

		if( $new_status == 'trash' ) {
			$candidate_id = $post->post_author;

			update_post_meta( $post->ID, '_in_review', '' );

			jm_decrease_resume_posting_count( $candidate_id );

			// candidate email
			if( jm_et_get_setting('candidate_resume_rejected_activated') ) {

				if ( is_multisite() )
					$blogname = $GLOBALS['current_site']->site_name;
				else
					$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
				$candidate = get_user_by( 'id', $candidate_id );

				$to = $candidate->user_email;

				$array_replace = array(
					'[resume_title]' => $post->post_title,
					'[resume_url]' => get_permalink($post),
					'[site_name]' => $blogname,
					'[site_url]' => esc_url(home_url('')),
				);

				$subject = jm_et_get_setting('candidate_resume_rejected_subject');
				$subject = str_replace(array_keys($array_replace), $array_replace, $subject);

				$message = jm_et_get_setting('candidate_resume_rejected_content');
				$message = str_replace(array_keys($array_replace), $array_replace, $message);

				noo_mail($to, $subject, $message,'','noo_notify_resume_review_reject_candidate');
			}
		}
	}
	add_action('transition_post_status', 'jm_admin_resume_transition_post_status', 10, 3);
endif;

if ( ! function_exists( 'jm_admin_resume_feature_action' ) ) :
	function jm_admin_resume_feature_action() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'noo_resume_feature' ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'noo' ), '', array( 'response' => 403 ) );
			}

			if ( ! check_admin_referer( 'noo-resume-feature' ) ) {
				wp_die( __( 'You have taken too long. Please go back and retry.', 'noo' ), '', array( 'response' => 403 ) );
			}

			$post_id = ! empty( $_GET['resume_id'] ) ? (int) $_GET['resume_id'] : '';

			if ( ! $post_id || get_post_type( $post_id ) !== 'noo_resume' ) {
				die;
			}

			$featured = noo_get_post_meta( $post_id, '_featured' );

			if ( 'yes' === $featured ) {
				update_post_meta( $post_id, '_featured', 'no' );
			} else {
				update_post_meta( $post_id, '_featured', 'yes' );
			}


			wp_safe_redirect( esc_url_raw( remove_query_arg( array(
				'trashed',
				'untrashed',
				'deleted',
				'ids'
			), wp_get_referer() ) ) );
			die();
		}
	}

	add_action( 'admin_init', 'jm_admin_resume_feature_action' );
endif;