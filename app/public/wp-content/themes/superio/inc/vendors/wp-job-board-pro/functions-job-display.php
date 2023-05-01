<?php

function superio_job_display_employer_logo($post, $link = true, $link_employer = false) {
	$author_id = superio_get_post_author($post->ID);
	$employer_id = WP_Job_Board_Pro_User::get_employer_by_user_id($author_id);
	if ( $link ) {
		if ( $link_employer ) {
			$url = get_permalink($employer_id);
		} else {
			$url = get_permalink($post);
		}
	}
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);
	?>
    <div class="employer-logo">
    	<?php if ( $link ) { ?>
        	<a href="<?php echo esc_url( $url ); ?>">
        <?php } ?>
        		<?php if ( $obj_job_meta->check_post_meta_exist('logo') && ($logo_url = $obj_job_meta->get_post_meta( 'logo' )) ) {
        			$logo_id = WP_Job_Board_Pro_Job_Listing::get_post_meta($post->ID, 'logo_id', true);
        			if ( $logo_id ) {
	        			echo wp_get_attachment_image( $logo_id, 'thumbnail' );
	        		} else {
	        			?>
	        			<img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_the_title($employer_id)); ?>">
	        			<?php
	        		}
    			} else {
    				
    				if ( has_post_thumbnail($employer_id) ) {
    					echo get_the_post_thumbnail( $employer_id, 'thumbnail' );
    				} else { ?>
	            		<img src="<?php echo esc_url(superio_placeholder_img_src()); ?>" alt="<?php echo esc_attr(get_the_title($employer_id)); ?>">
	            	<?php } ?>
            	<?php } ?>
        <?php if ( $link ) { ?>
        	</a>
        <?php } ?>
    </div>
    <?php
}

function superio_job_display_employer_title($post, $display_type = 'no-icon') {
	$author_id = superio_get_post_author($post->ID);
	$employer_id = WP_Job_Board_Pro_User::get_employer_by_user_id($author_id);
	if ( $employer_id ) {
		$employer_obj = get_post($employer_id);
		?>
	        <h3 class="employer-title">
	            <a href="<?php echo esc_url( get_permalink($employer_id) ); ?>">
	            	<?php if ($display_type == 'icon') { ?>
							<i class="ti-home"></i>
					<?php } ?>
	                <?php echo superio_employer_name($employer_obj); ?>
	            </a>
	        </h3>
	    <?php
	}
}

function superio_job_display_job_category($post, $display_category = 'no-title') {
	$categories = get_the_terms( $post->ID, 'job_listing_category' );
	if ( $categories ) { $i = 1;
		?>
		<div class="category-job">
			<?php
			if ( $display_category == 'title' ) {
				?>
				<div class="job-category with-title">
					<strong><?php esc_html_e('Job Category:', 'superio'); ?></strong>
				<?php
			} elseif ($display_category == 'icon') {
				?>
				<div class="job-category with-icon">
					<i class="flaticon-briefcase-1"></i>
			<?php
			} else {
				?>
				<div class="job-category">
				<?php
			}
				foreach ($categories as $term) {
					$color = get_term_meta( $term->term_id, '_color', true );
					$style = '';
					if ( $color ) {
						$style = 'color: '.$color;
					}
					?>
						<?php if( $i < count($categories) ) { ?>
							<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>, 
						<?php }else{ ?>
							<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
						<?php } ?>	
		        	<?php $i++;
		    	}
	    	?>
	    	</div>
	    </div>
    	<?php
    }
}

function superio_job_display_job_first_category($post) {
	$categories = get_the_terms( $post->ID, 'job_listing_category' );
	if ( $categories ) { $i = 1; 
		?>
		<div class="category-job">
			<i class="flaticon-briefcase-1"></i>
			<?php foreach ($categories as $term) {
				$color = get_term_meta( $term->term_id, '_color', true );
				$style = '';
				if ( $color ) {
					$style = 'color: '.$color;
				}
				?>
					<?php if($i ==1 ) { ?>
					<a href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
					<?php } ?>
	        	<?php $i++;
		    } ?>
	    </div>
    	<?php
    }
}

function superio_job_display_job_type($post, $display_type = 'no-title', $color = true, $echo = true) {
	$types = get_the_terms( $post->ID, 'job_listing_type' );
	ob_start();
	if ( $types ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="job-type with-title">
					<strong><?php esc_html_e('Job Type:', 'superio'); ?></strong>
				<?php
			} elseif ($display_type == 'icon') {
				?>
				<div class="job-type with-icon">
					<i class="ti-calendar"></i>
			<?php
			} else {
				?>
				<div class="job-type with-title">
				<?php
			}
				foreach ($types as $term) {
					$style = '';
					if ( $color ) {
						$bg_color = get_term_meta( $term->term_id, 'bg_color', true );
						$text_color = get_term_meta( $term->term_id, 'text_color', true );
						
						if ( !empty($bg_color) ) {
							$style .= 'background-color: '.$bg_color.';';
						}
						if ( !empty($text_color) ) {
							$style .= 'color: '.$text_color.';';
						}
					}
					?>
		            	<a class="type-job" href="<?php echo get_term_link($term); ?>" style="<?php echo esc_attr($style); ?>"><?php echo esc_html($term->name); ?></a>
		        	<?php
		    	}
	    	?>
	    	</div>
    	<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_tags($post, $display_type = 'no-title', $echo = true) {
	$tags = get_the_terms( $post->ID, 'job_listing_tag' );
	ob_start();
	if ( $tags ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="job-tags">
				<strong><?php esc_html_e('Tagged as:', 'superio'); ?></strong>
				<?php
			} else {
				?>
				<div class="job-tags">
				<?php
			}
				foreach ($tags as $term) {
					?>
		            	<a class="tag-job" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
		        	<?php
		    	}
	    	?>
	    	</div>
    	<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_tags_version2($post, $display_type = 'no-title',$echo = true, $limit = 3) {
	$tags = get_the_terms( $post->ID, 'job_listing_tag' );
	ob_start();
	$i = 1;
	if ( $tags ) {
		?>
			<?php
			if ( $display_type == 'title' ) {
				?>
				<div class="job-tags">
				<strong><?php esc_html_e('Tagged as:', 'superio'); ?></strong>
				<?php
			} else {
				?>
				<div class="job-tags">
				<?php
			}
				foreach ($tags as $term) {
					if ( $limit > 0 && $i > $limit ) {
	                    break;
	                }
					?>
		            	<a class="tag-job" href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
		        	<?php $i++;
		    	} 
	    	?>

	    	<?php if ( $limit > 0 && count($tags) > $limit ) { ?>
	    		<span class="count-more-tags"><?php echo sprintf(esc_html__('+%d', 'superio'), (count($tags) - $limit) ); ?></span>
	    	<?php } ?>

	    	</div>
    	<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_short_location($post, $display_type = 'no-icon', $echo = true) {
	$locations = get_the_terms( $post->ID, 'job_listing_location' );
	ob_start();
	if ( $locations ) {
		$terms = array();
        superio_locations_walk($locations, 0, $terms);
        if ( empty($terms) ) {
        	$terms = $locations;
        } else {
        	$terms = array_reverse($terms, true);
        }
		?>
		<div class="job-location">
			<?php if ($display_type == 'icon') { ?>
	            <i class="flaticon-location"></i>
	        <?php } ?>
            <?php $i=1; foreach ($terms as $term) { ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo trim($term->name); ?></a><?php echo esc_html( $i < count($terms) ? ', ' : '' ); ?>
            <?php $i++; } ?>
        </div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_full_location($post, $display_type = 'no-icon-title') {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	$location = $obj_job_meta->get_post_meta( 'address' );
	if ( empty($location) ) {
		$location = $obj_job_meta->get_post_meta( 'map_location_address' );
	}
	if ( $location ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="job-location with-icon"><i class="flaticon-location"></i> <?php echo trim($location); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="job-location with-title">
				<strong><?php esc_html_e('Location:', 'superio'); ?></strong> <?php echo trim($location); ?>
			</div>
			<?php
		} else {
			?>
			<div class="job-location"><?php echo trim($location); ?></div>
			<?php
		}
    }
}

function superio_job_display_salary($post, $display_type = 'no-icon-title', $echo = true) {
	$salary = WP_Job_Board_Pro_Job_Listing::get_salary_html($post->ID);
	ob_start();
	if ( $salary ) {
		if ( $display_type == 'icon' ) {
			?>
			<div class="job-salary with-icon"><i class="flaticon-money-1"></i> <?php echo trim($salary); ?></div>
			<?php
		} elseif ( $display_type == 'title' ) {
			?>
			<div class="job-salary with-title">
				<strong><?php esc_html_e('Salary:', 'superio'); ?></strong> <span><?php echo trim($salary); ?></span>
			</div>
			<?php
		} else {
			?>
			<div class="job-salary"><?php echo trim($salary); ?></div>
			<?php
		}
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_add_shortlist_btn($html, $post_id) {
    if ( WP_Job_Board_Pro_Candidate::check_added_shortlist($post_id) ) {
        $classes = 'btn-action-job added btn-added-job-shortlist';
        $nonce = wp_create_nonce( 'wp-job-board-pro-remove-job-shortlist-nonce' );
    } else {
        $classes = 'btn-action-job btn-add-job-shortlist';
        $nonce = wp_create_nonce( 'wp-job-board-pro-add-job-shortlist-nonce' );
    }
    ob_start();
    ?>
    <a href="javascript:void(0);" class="btn-follow <?php echo esc_attr($classes); ?>" data-job_id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="flaticon-bookmark"></i></a>
    <?php
    $html = ob_get_clean();
    
    return $html;
}
add_filter('wp-job-board-pro-job-display-shortlist-link', 'superio_job_display_add_shortlist_btn', 10, 2);


function superio_job_display_deadline($post, $display_type = 'no-icon-title', $echo = true) {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	$application_deadline_date = $obj_job_meta->get_post_meta( 'application_deadline_date' );
	ob_start();
	if ( empty($application_deadline_date) || date('Y-m-d', strtotime($application_deadline_date)) >= date('Y-m-d', strtotime('now')) ) {
		if ( $application_deadline_date ) {
			$deadline_date = strtotime($application_deadline_date);
			?>
			<div class="deadline-time"><?php echo date_i18n(get_option('date_format'), $deadline_date); ?></div>
			<?php
		}
	} else {
		?>
		<div class="deadline-closed"><?php esc_html_e('Application deadline closed.', 'superio'); ?></div>
		<?php
	}
	$ouput = ob_get_clean();

	ob_start();
	if ( $display_type == 'icon' ) {
		?>
		<div class="job-deadline with-icon"><i class="flaticon-wall-clock"></i> <?php echo trim($ouput); ?></div>
		<?php
	} elseif ( $display_type == 'title' ) {
		?>
		<div class="job-deadline with-title">
			<strong><?php esc_html_e('Deadline date:', 'superio'); ?></strong> <?php echo trim($ouput); ?>
		</div>
		<?php
	} else {
		?>
		<div class="job-deadline"><?php echo trim($ouput); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_postdate($post, $display_type = 'no-icon-title', $echo = true) {

	ob_start();
	if ( $display_type == 'icon' ) {
		?>
		<div class="job-deadline with-icon"><i class="flaticon-wall-clock"></i> <?php the_time(get_option('date_format')); ?></div>
		<?php
	} elseif ( $display_type == 'title' ) {
		?>
		<div class="job-deadline with-title">
			<strong><?php esc_html_e('Posted date:', 'superio'); ?></strong> <?php the_time(get_option('date_format')); ?>
		</div>
		<?php
	} else {
		?>
		<div class="job-deadline"><?php the_time(get_option('date_format')); ?></div>
		<?php
	}
	$output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_featured_icon($post, $display = 'icon') {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	$featured = $obj_job_meta->get_post_meta( 'featured' );
	if ( $featured ) { ?>
		<?php if($display == 'icon') {?>
        	<span class="featured" data-toggle="tooltip" title="<?php esc_attr_e('featured', 'superio'); ?>"><i class="flaticon-tick"></i></span>
        <?php } else { ?>
        	<span class="featured-text"><?php esc_html_e('Featured', 'superio'); ?></span>
        <?php } ?>
    <?php }
}

function superio_job_display_urgent_icon($post, $display = 'text') {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	$urgent = $obj_job_meta->get_post_meta( 'urgent' );
	if ( $urgent ) { ?>
		<?php if($display == 'text') { ?>
        	<span class="urgent"><?php esc_html_e('Urgent', 'superio'); ?></span>
        <?php } else { ?>
    		<span class="urgent urgent-icon" data-toggle="tooltip" title="<?php esc_attr_e('Urgent', 'superio'); ?>"><i class="flaticon-waiting"></i></span>
        <?php } ?>
    <?php }
}

function superio_job_display_filled_label($post, $echo = true) {
	if ( WP_Job_Board_Pro_Job_Listing::is_filled($post->ID) ) {
		?>
		<span class="filled-text"><?php esc_html_e('Filled', 'superio'); ?></span>
		<?php
	}
}

function superio_job_item_map_meta($post) {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	$latitude = $obj_job_meta->get_post_meta( 'map_location_latitude' );
	$longitude = $obj_job_meta->get_post_meta( 'map_location_longitude' );

	$author_id = superio_get_post_author($post->ID);
	$employer_id = WP_Job_Board_Pro_User::get_employer_by_user_id($author_id);

	$url = '';
	if ( $obj_job_meta->check_post_meta_exist('logo') && ($logo_url = $obj_job_meta->get_post_meta( 'logo' )) ) {
		$logo_id = WP_Job_Board_Pro_Job_Listing::get_post_meta($post->ID, 'logo_id', true);
		if ( $logo_id ) {
			$url = wp_get_attachment_image_url($logo_id, 'thumbnail');
		} else {
			$url = $logo_url;
		}
	} elseif ( has_post_thumbnail($employer_id) ) {
		$url = get_the_post_thumbnail_url($employer_id, 'thumbnail');
	}

	echo 'data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'" data-img="'.esc_url($url).'"';
}

function superio_job_display_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_job_meta->check_post_meta_exist($meta_key) && ($value = $obj_job_meta->get_post_meta( $meta_key )) ) {
		?>
		<div class="job-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="job-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_job_meta->get_post_meta_title($meta_key);
				?>
					<span class="title-meta">
						<?php echo esc_html($title); ?>
					</span>
				<?php } ?>

				<?php if ( !empty($icon) ) { ?>
					<i class="<?php echo esc_attr($icon); ?>"></i>
				<?php } ?>
				<?php
					if ( is_array($value) ) {
						echo implode(', ', $value);
					} else {
						echo esc_html($value);
					}
				?>
				<?php echo trim($suffix); ?>
			</div>

		</div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}

function superio_job_display_custom_field_meta($post, $meta_key, $icon = '', $show_title = false, $suffix = '', $echo = false) {
	$obj_job_meta = WP_Job_Board_Pro_Job_Listing_Meta::get_instance($post->ID);

	ob_start();
	if ( $obj_job_meta->check_custom_post_meta_exist($meta_key) && ($value = $obj_job_meta->get_custom_post_meta( $meta_key )) ) {
		?>
		<div class="job-meta with-<?php echo esc_attr($show_title ? 'icon-title' : 'icon'); ?>">

			<div class="job-meta">

				<?php if ( !empty($show_title) ) {
					$title = $obj_job_meta->get_custom_post_meta_title($meta_key);
				?>
					<span class="title-meta">
						<?php echo esc_html($title); ?>
					</span>
				<?php } ?>

				<?php if ( !empty($icon) ) { ?>
					<i class="<?php echo esc_attr($icon); ?>"></i>
				<?php } ?>
				<?php
					if ( is_array($value) ) {
						echo implode(', ', $value);
					} else {
						echo esc_html($value);
					}
				?>
				<?php echo trim($suffix); ?>
			</div>

		</div>
		<?php
    }
    $output = ob_get_clean();
    if ( $echo ) {
    	echo trim($output);
    } else {
    	return $output;
    }
}



// Job Archive hooks
function superio_job_display_filter_btn() {
	$layout_type = superio_get_jobs_layout_type();
	$layout_sidebar = superio_get_jobs_layout_sidebar();
	$filter_sidebar = superio_get_jobs_filter_sidebar();
	if ( ($layout_type == 'half-map' || ($layout_type == 'default' && $layout_sidebar == 'main' && superio_get_jobs_show_offcanvas_filter() ) || ($layout_type == 'top-map' && $layout_sidebar == 'main' && superio_get_jobs_show_offcanvas_filter())) && is_active_sidebar( $filter_sidebar ) ) {
		?>
		<div class="filter-in-sidebar-wrapper">
			<span class="filter-in-sidebar btn-theme-light btn"><i class="ti-filter"></i><span class="text"><?php esc_html_e('Filter', 'superio'); ?></span></span>
		</div>
		<?php
	}
}

function superio_job_display_per_page_form($wp_query) {
    $total              = $wp_query->found_posts;
    $per_page           = $wp_query->get( 'posts_per_page' );
    $_per_page          = wp_job_board_pro_get_option('number_jobs_per_page', 12);

    // Generate per page options
    $products_per_page_options = array();
    while ( $_per_page < $total ) {
        $products_per_page_options[] = $_per_page;
        $_per_page = $_per_page * 2;
    }

    if ( empty( $products_per_page_options ) ) {
        return;
    }

    $products_per_page_options[] = -1;

    ?>
    <form method="get" action="<?php echo esc_url(WP_Job_Board_Pro_Mixes::get_jobs_page_url()); ?>" class="form-superio-ppp">
        
    	<select name="jobs_ppp" onchange="this.form.submit()">
            <?php foreach( $products_per_page_options as $key => $value ) { ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $per_page ); ?>>
                	<?php
                		if ( $value == -1 ) {
                			esc_html_e( 'All', 'superio' );
                		} else {
                			echo sprintf( esc_html__( '%s Per Page', 'superio' ), $value );
                		}
                	?>
                </option>
            <?php } ?>
        </select>

        <input type="hidden" name="paged" value="1" />
		<?php WP_Job_Board_Pro_Mixes::query_string_form_fields( null, array( 'jobs_ppp', 'submit', 'paged' ) ); ?>
    </form>
    <?php
}

