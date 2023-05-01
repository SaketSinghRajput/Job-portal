<?php

if ( ! function_exists( 'jm_noo_jobs_shortcode' ) ) :
	function jm_noo_jobs_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title_type'      => 'text',
			'title'           => '',
			'show'            => 'featured',
			'btn_link'        => '',
			'list_column'     => 3,
			'show_pagination' => 'no',
			'choice_paginate' => 'nextajax',
			'show_autoplay'   => 'on',
			'slider_style'    => 'style-1',
			'slider_time'     => '3000',
			'slider_speed'    => '600',
			'posts_per_page'  => 3,
			'no_content'      => 'text',
			'job_category'    => 'all',
			'job_type'        => 'all',
			'job_location'    => 'all',
			'orderby'         => 'date',
			'order'           => 'desc',
			'display_style'   => 'list',
			'show_view_more'  => 'yes',
			'css_class'       => '',
		), $atts );
		extract( $atts );
		$paged             = 1;
		$atts['ajax_item'] = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'noo_nextajax';
		if ( $atts['ajax_item'] ) {
			$paged          = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
			$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : $post_per_page;
			$show           = isset( $_POST['show'] ) ? $_POST['show'] : $show;
			$display_style  = isset( $_POST['display_style'] ) ? ( $_POST['display_style'] ) : $display_style;
			$job_category   = isset( $_POST['job_category'] ) ? $_POST['job_category'] : $job_category;
			$job_type       = isset( $_POST['job_type'] ) ? $_POST['job_type'] : $job_type;
			$job_location   = isset( $_POST['job_location'] ) ? $_POST['job_location'] : $job_location;
			$orderby        = isset( $_POST['orderby'] ) ? $_POST['orderby'] : $orderby;
			$order          = isset( $_POST['order'] ) ? $_POST['order'] : $order;
			$show_view_more = isset( $_POST['show_view_more'] ) ? $_POST['show_view_more'] : $show_view_more;
		}
		//  -- args query

		$args = array(
			'post_type'           => 'noo_job',
			'post_status'         => 'publish',
			'paged'               => $paged,
			'posts_per_page'      => $posts_per_page,
			'ignore_sticky_posts' => true,
		);

		//  -- tax_query

		$job_category = explode( ",", $job_category );

		$args['tax_query'] = array( 'relation' => 'AND' );

		if ( ! in_array( 'all', $job_category ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_category',
				'field'    => 'slug',
				'terms'    => $job_category,
			);
		}

		$job_type = explode( ",", $job_type );

		if ( ! in_array( 'all', $job_type ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_type',
				'field'    => 'slug',
				'terms'    => $job_type,
			);
		}

		$job_location = explode( ",", $job_location );

		if ( ! in_array( 'all', $job_location ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_location',
				'field'    => 'slug',
				'terms'    => $job_location,
			);
		}

		//  -- Check order by......
		if ( $orderby == 'view' ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_noo_views_count';
		} elseif ( $orderby == 'date' ) {
			$args['orderby'] = 'date';
		} elseif ( $orderby == 'featured' ) {
			$args['orderby']  = 'meta_value post_date';
			$args['meta_key'] = '_featured';
		} else {
			$args['orderby'] = 'rand';
		}

		//  -- Check order
		if ( $orderby != 'rand' ) {
            if ( $order == 'asc' ) {
                $args['order'] = 'ASC';
            } else {
                $args['order'] = 'DESC';
            }
        }

		if ( $show == 'featured' ) {
			$args['meta_query'][] = array(
				'key'   => '_featured',
				'value' => 'yes',
			);
		}
		$r = new WP_Query( apply_filters('noo_jobs_shortcode_query_args', $args, $atts) );
		ob_start();

		$atts['query']          = $r;
		$atts['item_class']     = 'nextajax-item';
		$atts['pagination']     = $show_pagination == 'yes' ? 1 : 0;
		$atts['paginate']       = $show_pagination=='yes' ? $choice_paginate : '' ;
		$atts['paginate_data']  = array(
			'posts_per_page' => $posts_per_page,
			'job_category'   => $job_category,
			'job_type'       => $job_type,
			'job_location'   => $job_location,
			'orderby'        => $orderby,
			'order'          => $order,
		);
		$atts['show_view_more'] = $show_view_more;
		$atts['show_autoplay']  = $show_autoplay;
		$atts['slider_time']    = $slider_time;
		$atts['slider_speed']   = $slider_speed;
		$atts['display_style']  = $display_style;
		$atts['featured']       = ( $show == 'featured' ? 'featured' : 'recent' );
		$atts['is_shortcode']   = true;
		$atts['class']          = ' jobs-shortcode'.$atts['css_class'];
		jm_job_loop( $atts );
		$output = ob_get_clean();
		if ( $atts['ajax_item'] ) {
			echo $output;
			die;
		}

		return $output;
	}

	add_shortcode( 'noo_jobs', 'jm_noo_jobs_shortcode' );

	// ajax action
	add_action( 'wp_ajax_nopriv_noo_nextajax', 'jm_noo_jobs_shortcode' );
	add_action( 'wp_ajax_noo_nextajax', 'jm_noo_jobs_shortcode' );
endif;

if ( ! function_exists( 'jm_noo_job_search_shortcode' ) ) :
	function jm_noo_job_search_shortcode( $atts, $content = null ) {
		wp_enqueue_style( 'vendor-chosen' );
		wp_enqueue_script( 'vendor-chosen' );
		extract( shortcode_atts( array(
			'top_title'           => __( 'JobMonster WordPress Theme', 'noo' ),
			'title'               => __( 'Join us & Explore thousands of Jobs', 'noo' ),
			'sub_title'           => '',
			'title_color'         => '#44494b',
			'background_type'     => 'no_background',
			'style_horizontal'    => 'style-1',
			'slider_images'       => '',
			'slider_animation'    => 'fade',
			'slider_time'         => '3000',
			'slider_speed'        => '600',
			'slider_height'       => '600',
			'background_image'    => '',
			// 'disable_multiple'    => '',
			'revo_slider_id'      => '',
			'image_height_type'   => '',
			'image_height_custom' => '',
			'search_type'         => 'noo_job',
			'search_mode'         => 'noo_horizontal',
			'show_keyword'        => 'yes',
			'r_pos2'              => 'job_location',
			'r_pos3'              => 'job_category',
			'r_pos4'              => 'no',
			'pos2'                => 'job_location',
			'pos3'                => 'job_category',
			'pos4'                => 'no',
			'search_position'     => '120',
			'visibility'          => '',
			'class'               => '',
			'id'                  => '',
			'custom_style'        => '',
			'align_title'         => 'center',
			'hierarchical'        => 0,
			'depth'               => 0,
		), $atts ) );
		$fields_count = 1;
		$sub_title    = rawurldecode( base64_decode( strip_tags( $content ) ) );
		$sub_title    = wpb_js_remove_wpautop( apply_filters( 'vc_raw_html_module_content', $sub_title ) );
		if ( $search_type == 'noo_resume' ) {
			$disable_multiple = noo_get_option( 'noo_resume_search_field_type',0);
		}else{
			$disable_multiple     = noo_get_option( 'noo_job_search_field_type',0);
		}
		if ( $search_mode == 'noo_horizontal' ) {
			if ( $show_keyword == 'yes' ) {
				$fields_count ++;
			}
			if ( $search_type == 'noo_resume' ) {
				if ( $r_pos2 != 'no' ) {
					$fields_count ++;
				}
				if ( $r_pos3 != 'no' ) {
					$fields_count ++;
				}
				if ( $r_pos4 != 'no' ) {
					$fields_count ++;
				}
			} else {
				if ( $pos2 != 'no' ) {
					$fields_count ++;
				}
				if ( $pos3 != 'no' ) {
					$fields_count ++;
				}
				if ( $pos4 != 'no' ) {
					$fields_count ++;
				}
			}
		}
		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? esc_attr( $class ) : '';
		$class      .= noo_visibility_class( $visibility );

		$id     = ( $id != '' ) ? esc_attr( $id ) : 'job-search-slider-' . noo_vc_elements_id_increment();
		$id_out = ( $id != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		if ( $background_type == 'slider' && ! empty( $slider_images ) ) {
			$custom_style .= ';height:' . $slider_height . 'px;';
		}
		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';
		if ( $background_type == 'slider' ) {
			wp_enqueue_script( 'vendor-carouFredSel' );
		}
		if ( $background_type == '' || $background_type == 'no_background' ) {
			$search_position = '';
			$class           .= ' no-background';
		}
		ob_start();
		?>

        <div class="noo-job-search-wrapper <?php echo esc_attr( $class ); ?>" <?php echo( $id_out . ' ' . $custom_style ); ?>>
			
			<?php
			// background_type : revoSlider
			if ( $background_type == 'revo_slider' ) : ?>
                <div class="job-search-bg-revo-slider">
					<?php echo do_shortcode( '[rev_slider ' . $revo_slider_id . ']' ); ?>
                </div>
                <div class="container">
                	<div class="job-advanced-search job-revo-slider <?php echo $align_title ?> <?php echo( $search_mode == 'noo_vertical' ? ' vertical' : ' horizontal ' . $style_horizontal ); ?> column-<?php echo esc_attr( $fields_count ); ?>" <?php if ( ! empty( $search_position ) ) {
						echo 'style="top: ' . intval( $search_position ) . 'px;"';
					} ?>>
						<div class="job-search-info <?php echo $align_title ?>">
							<?php if ( ! empty( $title ) ) : ?>
	                            <h2 class="search-main-title"
	                                style="color: <?php echo $title_color ?>"><?php echo( $title ); ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( $top_title ) ) : ?>
	                            <p class="search-sub-title"
	                               style="color: <?php echo $title_color ?>"><?php echo( $top_title ); ?></p>
							<?php endif; ?>
	                    </div>
	                    <div class="job-advanced-search-wrap <?php echo $align_title ?>">
	                        <form method="get" class="form-inline" action="<?php echo esc_url( get_post_type_archive_link( $search_type ) ); ?>">
	                            <div class="job-advanced-search-form<?php if ( is_rtl() ) {
									echo ' chosen-rtl';
								} ?>">
	                                <!-- <input type="hidden" value="<?php //echo esc_attr( $search_type ) ?>" name="post_type"> -->
									<?php if ( $show_keyword == 'yes' ) : ?>
	                                    <div class="form-group">
	                                        <label class="sr-only" for="search-keyword"><?php _e( 'Keyword', 'noo' ) ?></label>
	                                        <input type="text" class="form-control" id="search-keyword" name="s"  placeholder="<?php _e( 'Keyword', 'noo' ) ?>" value="<?php echo get_search_query(); ?>">
	                                    </div>
									<?php else : ?>
	                                    <input type="hidden" value="" name="s">
									<?php endif; ?>
									<?php
									if ( $search_type == 'noo_job' ):
										jm_job_advanced_search_field( $pos2,$disable_multiple );
										jm_job_advanced_search_field( $pos3,$disable_multiple );
										jm_job_advanced_search_field( $pos4,$disable_multiple );
									else:
										jm_resume_advanced_search_field( $r_pos2,$disable_multiple );
										jm_resume_advanced_search_field( $r_pos3,$disable_multiple );
										jm_resume_advanced_search_field( $r_pos4,$disable_multiple );
									endif;
									?>
	                                <div class="form-action">
	                                    <button type="submit" class="btn btn-primary btn-search-submit"><?php _e( 'Search','noo' ) ?></button>
	                                </div>
	                            </div>
	                        </form>
	                    </div>
	                    <?php if ( $sub_title != '' ): ?>
	                        <div class="job-search-info <?php echo $align_title ?>">
	                            <p class="search-sub-title-bottom" style="color: <?php echo $title_color ?>"><?php echo $sub_title; ?></p>
	                        </div>
						<?php endif ?>
	                </div>
                </div>

			<?php else: ?>
				<?php if ( $background_type == 'image' ) :
				$thumbnail = '';
				if ( ! empty( $background_image ) ) {
					$thumbnail = wp_get_attachment_url( $background_image );
				}
				$style_bg = 'style="';
				$style_bg .= 'background-image: url(' . esc_url( $thumbnail ) . ');';
				$style_bg .= 'height: ' . $image_height_custom . 'px';
				$style_bg .= '"';
				?>
				<?php if ( $image_height_type == 'noo_fullscreen' ) : ?>
	                <script>
	                    jQuery('document').ready(function ($) {
	                        var navbar_height = 0;
	                        if($('.noo-header').hasClass('.header-1')){
								navbar_height = $('.navbar').outerHeight();
							}

	                        if ($('body').hasClass('admin-bar')) {
	                            navbar_height += $('#wpadminbar').outerHeight();
	                        }
	                        $('.job-search-bg-image').css('height', ($(window).height() - navbar_height) + 'px');
	                    })
	                </script>
				<?php endif;
				?>
				<?php endif; ?>
				<?php if ( $background_type == 'slider' && ! empty( $slider_images ) ) : ?>
	                <div class="job-search-bg-slider">
						<?php
						$html = array();

						$html[] = '  <ul class="sliders">';
						$images = explode( ',', $slider_images );
						foreach ( $images as $image ) {
							$thumbnail = wp_get_attachment_url( $image );
							$html[]    = '<li style="background-image : url('. $thumbnail .');height: ' . $slider_height . 'px;background-size: cover;background-repeat: no-repeat;background-position: center center;"></li>';
						}
						$html[] = '  </ul>';
						$html[] = '  <div class="clearfix"></div>';

						// slider script
						$html[] = '<script>';
						$html[] = "jQuery('document').ready(function ($) {";
						$html[] = " $('#{$id} .sliders').each(function(){";
						$html[] = '  var _this = $(this);';
						$html[] = '  imagesLoaded(_this,function(){';
						$html[] = "   _this.carouFredSel({";
						$html[] = "    infinite: true,";
						$html[] = "    circular: true,";
						$html[] = "    responsive: true,";
						$html[] = "    debug : false,";
						$html[] = '    scroll: {';
						$html[] = '      items: 1,';
						$html[] = ( $slider_speed != '' ) ? '      duration: ' . $slider_speed . ',' : '';
						$html[] = '      pauseOnHover: "resume",';
						$html[] = '      fx: "' . $slider_animation . '"';
						$html[] = '    },';
						$html[] = '    items: {';
						$html[] = '      visible: 1';
						$html[] = '    },';
						$html[] = '    auto: {';
						$html[] = ( $slider_time != '' ) ? '      timeoutDuration: ' . $slider_time . ',' : '';
						$html[] = '      play: true';
						$html[] = '    }';
						$html[] = '   });';
						$html[] = '  });';
						$html[] = ' });';
						$html[] = '});';
						$html[] = '</script>';

						echo implode( "\n", $html );
						?>
	                </div>
				<?php
				if ( ! empty( $slider_height ) ) {
					$html[] = '<style type="text/css" media="screen">';
					$html[] = "  #{$id}.noo-slider .caroufredsel_wrapper .sliders  .slide-item.noo-property-slide { max-height: {$slider_height}px; }";
					$html[] = '</style>';
				}
				?>
				<?php endif; ?>
				<?php 
					$thumbnail = wp_get_attachment_url( $background_image );
					$style_bg = 'style="';
					if(!empty($thumbnail)){
						$style_bg .= 'background-image: url(' . esc_url( $thumbnail ) . ');';
					}
					if(!empty($image_height_custom)){
						$style_bg .= 'height: ' . $image_height_custom . 'px;';
					}
					// Use for Background Type = Slider,
					if ( ! empty( $search_position && $background_type == 'slider') ) {
						$style_bg .= 'position: relative;';
						$style_bg .= 'top: ' . intval( $search_position ) . 'px;';
					}
					$style_bg .= '"';					
				?>
				<div class="job-search-bg-image <?php if ( $background_type == 'slider') { echo 'job-search-slider'; } ?>" <?php echo $style_bg; ?>>
					<div class="container job-advanced-search-container">
		                <div class="job-advanced-search <?php echo $align_title ?> <?php echo( $search_mode == 'noo_vertical' ? ' vertical' : ' horizontal ' . $style_horizontal ); ?> column-<?php echo esc_attr( $fields_count ); ?>" <?php if ( ! empty( $search_position ) && ($background_type == 'image' || $background_type == 'no_background')) {
						echo 'style="top: ' . intval( $search_position ) . 'px;"';
					} ?>>
		                    <div class="job-search-info <?php echo $align_title ?>">
								<?php if ( ! empty( $title ) ) : ?>
		                            <h2 class="search-main-title" style="color: <?php echo $title_color ?>"><?php echo( $title ); ?></h2>
								<?php endif; ?>
								<?php if ( ! empty( $top_title ) ) : ?>
		                            <p class="search-sub-title" style="color: <?php echo $title_color ?>"><?php echo( $top_title ); ?></p>
								<?php endif; ?>
		                    </div>
		                    <div class="job-advanced-search-wrap">
		                        <form method="get" class="form-inline" action="<?php echo esc_url( get_post_type_archive_link( $search_type ) ); ?>">
		                            <div class="job-advanced-search-form<?php if ( is_rtl() ) {
										echo ' chosen-rtl';
									} ?>">
		                               <!--  <input type="hidden" value="<?php //echo esc_attr( $search_type ) ?>" name="post_type"> -->
										<?php if ( $show_keyword == 'yes' ) : ?>
		                                    <div class="form-group">
		                                        <label class="sr-only"
		                                               for="search-keyword"><?php _e( 'Keyword', 'noo' ) ?></label>
		                                        <input type="text" class="form-control" id="search-keyword" name="s"
		                                               placeholder="<?php _e( 'Keyword', 'noo' ) ?>"
		                                               value="<?php echo get_search_query(); ?>">
		                                    </div>
										<?php else : ?>
		                                    <input type="hidden" value="" name="s">
										<?php endif; ?>
										<?php
										if ( $search_type == 'noo_job' ):
											jm_job_advanced_search_field( $pos2, $disable_multiple);
											jm_job_advanced_search_field( $pos3, $disable_multiple );
											jm_job_advanced_search_field( $pos4, $disable_multiple );
										else:
											jm_resume_advanced_search_field( $r_pos2, $disable_multiple );
											jm_resume_advanced_search_field( $r_pos3, $disable_multiple );
											jm_resume_advanced_search_field( $r_pos4, $disable_multiple );
										endif;
										?>
		                                <div class="form-action">
		                                    <button type="submit" class="btn btn-primary btn-search-submit">
												<?php if ( $style_horizontal == 'style-1' ): _e( 'Search Now', 'noo' ); else : _e( 'Search', 'noo' ); endif; ?></button>
		                                </div>
		                            </div>
		                        </form>
		                    </div>
							<?php if ( $sub_title != '' ): ?>
		                        <div class="job-search-info <?php echo $align_title ?>">
		                            <p class="search-sub-title-bottom" style="color: <?php echo $title_color ?>"><?php echo $sub_title; ?></p>
		                        </div>
							<?php endif ?>
		                </div>
	                </div>
	            </div>
			<?php endif; ?>
        </div>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_job_search', 'jm_noo_job_search_shortcode' );
endif;

if ( ! function_exists( 'jm_noo_job_map_shortcode' ) ) :
	function jm_noo_job_map_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
            'map_height' => '700',
            'map_style' => 'dark',
            'zoom' => '12',
            'center_latitude' => '40.714398',
            'center_longitude' => '-74.005279',
            'fit_bounds' => 'yes',
            'search_form' => 'yes',
            'show_keyword' => 'yes',
            'pos2' => 'job_location',
            'pos3' => 'job_category',
            'pos4' => 'no',
            'visibility' => '',
            'class' => '',
            'id' => '',
            'custom_style' => '',
            'show_company_marker' => 'no',
		), $atts ) );
		if ( isset( $show_location ) ) {
			if ( $show_location == 'yes' ) {
				$pos2 = 'job_location';
			} else {
				$pos2 = 'no';
			}
		}
		if ( isset( $job_category ) ) {
			if ( $job_category == 'yes' ) {
				$pos3 = 'job_category';
			} else {
				$pos3 = 'no';
			}
		}
		if ( isset( $job_type ) ) {
			if ( $job_type == 'yes' ) {
				$pos4 = 'job_type';
			} else {
				$pos4 = 'no';
			}
		}

		$fields_count = 1;
		if ( $show_keyword == 'yes' ) {
			$fields_count ++;
		}
		if ( $pos2 != 'no' ) {
			$fields_count ++;
		}
		if ( $pos3 != 'no' ) {
			$fields_count ++;
		}
		if ( $pos4 != 'no' ) {
			$fields_count ++;
		}

		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? 'noo-job-map ' . esc_attr( $class ) : 'noo-job-map';
		$class      .= noo_visibility_class( $visibility );

		$id    = ( $id != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? 'class="' . $class . '"' : '';

		$custom_style = ( $custom_style != '' ) ? 'style="' . $custom_style . '"' : '';

        jm_job_enqueue_map_script($show_company_marker);
		ob_start();
		
		$map_type = jm_get_location_setting('map_type','');
		?>
		
        <div <?php echo( $id . ' ' . $class . ' ' . $custom_style ); ?>>
            <div class="job-map">
                <div class="gmap-loading"><?php _e( 'Loading Maps', 'noo' ); ?>
                    <div class="gmap-loader">
                        <div class="rect1"></div>
                        <div class="rect2"></div>
                        <div class="rect3"></div>
                        <div class="rect4"></div>
                        <div class="rect5"></div>
                    </div>
                </div>
                <?php if ($map_type == 'google'): ?>
                	<div id="gmap" data-map_style="<?php echo $map_style; ?>"
                     data-latitude="<?php echo esc_html( $center_latitude ); ?>"
                     data-longitude="<?php echo esc_html( $center_longitude ); ?>" data-zoom="<?php echo $zoom; ?>"
                     data-fit_bounds="<?php echo $fit_bounds; ?>"
                     style="height: <?php echo esc_attr( $map_height ); ?>px;">
                     </div>
                     <div class="container-map-location-search">
	                    <i class="fa fa-search"></i>
	                    <input type="text" class="form-control" id="map-location-search" placeholder="<?php echo __( 'Search for a location...', 'noo' ); ?>" autocomplete="off">
	                </div>
                <?php else:?>
                	<div id="bmap" data-map_style="<?php echo $map_style; ?>"
                     data-latitude="<?php echo esc_html( $center_latitude ); ?>"
                     data-longitude="<?php echo esc_html( $center_longitude ); ?>" data-zoom="<?php echo $zoom; ?>"
                     data-id="bmap"
                     class="bmap"
                     style="height: <?php echo esc_attr( $map_height ); ?>px;">
                     </div>	
                <?php endif ?>
                
                
            </div>
			<?php if ( $search_form == 'yes' && $fields_count > 1 ) : ?>
                <div class="job-advanced-search container column-<?php echo esc_attr( $fields_count ); ?>">
                    <div class="job-advanced-search-wrap">
                        <form method="get" class="form-inline" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <div class="job-advanced-search-form">
                                <input type="hidden" value="noo_job" name="post_type">
								<?php if ( $show_keyword == 'yes' ) : ?>
                                    <div class="form-group">
                                        <label class="sr-only"
                                               for="search-keyword"><?php _e( 'Keyword', 'noo' ) ?></label>
                                        <input type="text" class="form-control" id="search-keyword" name="s"
                                               placeholder="<?php _e( 'Keyword', 'noo' ) ?>">
                                    </div>
								<?php else : ?>
                                    <input type="hidden" value="" name="s">
								<?php endif; ?>
								<?php
								jm_job_advanced_search_field( $pos2 );
								jm_job_advanced_search_field( $pos3 );
								jm_job_advanced_search_field( $pos4 );
								?>
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn-primary btn-search-submit"><?php _e( 'Search',
											'noo' ) ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
			<?php endif; ?>
        </div>
        <style>
        	.job-advanced-search-wrap .noo-mb-job ,.noo-mb-job-location{
        		display: none;
        	}
        </style>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_job_map', 'jm_noo_job_map_shortcode' );

endif;

if ( ! function_exists( 'noo_step_icon_shortcode' ) ) :
	function noo_step_icon_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'values'       => '',
			'new_values'   => '',
			'color'        => '#44494b',
			'style'        => '',
			'background'   => '',
			'btns'         => '',
			'visibility'   => '',
			'class'        => '',
			'id'           => '',
			'custom_style' => '',
		), $atts ) );

		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? 'noo-step-icon clearfix' . esc_attr( $class ) : 'noo-step-icon clearfix';
		$class      .= noo_visibility_class( $visibility );

		$id = ( $id != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

		$value_data = vc_param_group_parse_atts( $new_values );

		if ( empty( $value_data ) && ! empty( $values ) ) {
			$value_arr  = explode( ",", $values );
			$value_data = array();
			foreach ( $value_arr as $value ) {
				$new_value         = array();
				$data              = explode( "|", $value );
				$new_value['icon'] = isset( $data[0] ) ? $data[0] : 'fa fa-home';
				$new_value['text'] = isset( $data[1] ) ? $data[1] : __( 'Step Icon Title', 'noo' );
				$new_value['link'] = isset( $data[2] ) ? $data[2] : '#';
				$new_value['i_color'] = isset( $data[3] ) ? $data[3] : '#fff';
				$new_value['i_bgcolor'] = isset( $data[4] ) ? $data[4] : '#44494b';
				$value_data[]      = $new_value;
			}
		};

		ob_start();

		if ( ! empty( $style ) ):
			$class = ( $class != '' ) ? ' class="noo-step-icon-advanced ' . $class . '"' : '';
			$bg     = '';
			if ( ! empty( $background ) ) {
				$img = wp_get_attachment_image_src( $background, "full" );
				$bg  = 'style="background: url(' . $img[0] . ') no-repeat center;"';
			}

			$btns = vc_param_group_parse_atts( $btns );

			?>
            <div<?php echo( $id . $class . $custom_style ); ?> <?php echo $bg; ?>>

				<?php if ( isset( $content ) && ! empty( $content ) ) : ?>
                    <div class="noo-step-icon-content container-boxed max">
						<?php echo $content; ?>
                    </div>
				<?php endif; ?>

				<?php
				if ( ! empty( $btns ) ):
					?>
                    <div class="noo-step-icon-button container-boxed max">
						<?php
						foreach ( $btns as $key => $bt ) :
							$bt_link = isset( $bt['btn'] ) ? vc_build_link( $bt['btn'] ) : '';
							$bt_color = isset( $bt['btn_color'] ) ? $bt['btn_color'] : '';
							if ( ! empty( $bt_link ) ) : ?>
                                <div class="noo-step-icon-btn">
                                    <a style="background-color: <?php echo $bt_color; ?>;" class="btn btn-primary"
                                       href="<?php echo esc_url( $bt_link['url'] ) ?>"
									   <?php if ( isset( $bt_link['target'] ) && ! empty( $bt_link['target'] ) ): ?>target="_blank" <?php endif; ?>>
                                        <span><?php echo esc_html( $bt_link['title'] ) ?></span>
                                    </a>
                                </div>
							<?php endif; ?>
						<?php endforeach; ?>
                    </div>
				<?php endif; ?>
				<?php if ( ! empty( $value_data ) ) : ?>
                    <div class="noo-step-icon-advanced-list">
                        <ul class="noo-step-icon-<?php echo count( $value_data ) ?>">
							<?php foreach ( $value_data as $vd ) : ?>
                                <li>
									<span class="noo-step-icon-item">
										<?php $link = isset( $vd['link'] ) && ! empty( $vd['link'] ) ? $vd['link'] : ''; ?>
										<?php if ( ! empty( $link ) ) : ?>
                                        <a href="<?php echo esc_url( $link ) ?>">
										<?php endif; ?>
                                            <span
                                                    class="<?php echo esc_attr( $vd['icon'] ) ?> noo-step-icon-class" style="color: <?php echo isset($vd['i_color']) ? $vd['i_color'] : '#fff'?>; background-color:<?php echo isset($vd['i_bgcolor']) ? $vd['i_bgcolor'] : '#44494b'?>;"></span>
											<span
                                                    class="noo-step-icon-title"
                                                    style="color: <?php echo $color ?>"><?php echo esc_html( $vd['text'] ) ?></span>
											<?php if ( ! empty( $link ) ) : ?>
											</a>
									<?php endif; ?>
									</span>
                                </li>
							<?php endforeach; ?>
                        </ul>
                    </div>
				<?php endif; ?>
            </div>
		<?php
		else:
			$class = ( $class != '' ) ? ' class="' . $class . '"' : '';
			if ( ! empty( $value_data ) ) : ?>
                <div<?php echo( $id . $class . $custom_style ); ?>>
                    <ul class="noo-step-icon-<?php echo count( $value_data ) ?>">
						<?php foreach ( $value_data as $vd ): ?>
                            <li>
								<span class="noo-step-icon-item">
									<?php $link = isset( $vd['link'] ) && ! empty( $vd['link'] ) ? $vd['link'] : ''; ?>
									<?php if ( ! empty( $link ) ) : ?>
                                    <a href="<?php echo esc_url( $vd['link'] ) ?>">
									<?php endif; ?>
                                        <span
                                                class="<?php echo esc_attr( $vd['icon'] ) ?> noo-step-icon-class" style="color: <?php echo isset($vd['i_color']) ? $vd['i_color'] : '#fff'?>; background-color:<?php echo isset($vd['i_bgcolor']) ? $vd['i_bgcolor'] : '#44494b'?>;"></span>
										<span class="noo-step-icon-title"
                                              style="color: <?php echo $color ?>"><?php echo esc_html( $vd['text'] ) ?></span>
										<?php if ( ! empty( $link ) ) : ?>
										</a>
								<?php endif; ?>
								</span>
                            </li>
						<?php endforeach; ?>
                    </ul>
                </div>
			<?php
			endif;
		endif;

		return ob_get_clean();
	}

	add_shortcode( 'noo_step_icon', 'noo_step_icon_shortcode' );

endif;

if ( ! function_exists( 'noo_service_shortcode' ) ) :
	function noo_service_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'values'       => '',
			'new_values'   => '',
			'color'        => '#f5d006',
			'visibility'   => '',
			'class'        => '',
			'id'           => '',
			'custom_style' => '',
		), $atts ) );

		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? 'noo-service clearfix' . esc_attr( $class ) : 'noo-service clearfix';
		$class      .= noo_visibility_class( $visibility );

		$id = ( $id != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';

		$value_data = vc_param_group_parse_atts( $new_values );
		if ( empty( $value_data ) && ! empty( $values ) ) {
			$value_arr  = explode( ",", $values );
			$value_data = array();
			foreach ( $value_arr as $value ) {
				$new_value         = array();
				$data              = explode( "|", $value );
				$new_value['icon'] = isset( $data[0] ) ? $data[0] : 'fa fa-home';
				$new_value['text'] = isset( $data[1] ) ? $data[1] : __( 'Service Title', 'noo' );
				$new_value['text_content'] = isset( $data[2] ) ? $data[2] : __( 'Service Content', 'noo' );
				$value_data[]      = $new_value;
			}
		};

		ob_start();
		
		$class = ( $class != '' ) ? ' class="' . $class . '"' : '';
		if ( ! empty( $value_data ) ) : ?>
            <div<?php echo( $id . $class . $custom_style ); ?>>
                
				<?php foreach ( $value_data as $vd ): ?>
                   
						<div class="noo-service-item col-md-4 col-sm-6 col-xs-12">
							
							<div class="noo-service-title">
								<i class="<?php echo esc_attr( $vd['icon'] ) ?> noo-service-class" style="color: <?php echo $color ?>"></i>
								<h6><?php echo esc_html( $vd['text'] ) ?></h6>
							</div>
							<p class="noo-service-content">
								<?php echo esc_html( $vd['text_content'] ) ?>	
							</p>
								
						</div>
                   
				<?php endforeach; ?>
                
            </div>
		<?php
		endif;
		
		return ob_get_clean();
	}

	add_shortcode( 'noo_service', 'noo_service_shortcode' );

endif;

if ( ! function_exists( 'noo_offered_shortcode' ) ):
	function noo_offered_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'icon'            => '',
			'icon_size'       => '',
			'icon_color'      => '',
			'text_same_size'  => '',
			'text_size'       => '',
			'text_same_color' => '',
			'text_color'      => '',
			'title'           => '',
			'visibility'      => '',
			'class'           => '',
			'id'              => '',
			'custom_style'    => '',
		), $atts ) );

		$class        = ( $class != '' ) ? 'noo-offered-icon' . esc_attr( $class ) : 'noo-offered-icon';
		$icon_class   = ( $icon != '' ) ? 'fa ' . esc_attr( $icon ) : '';
		$custom_style = ( $custom_style != '' ) ? esc_attr( $custom_style ) : '';
		$icon_style   = '';


		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      .= noo_visibility_class( $visibility );


		$id           = ( $id != '' ) ? 'id= "' . esc_attr( $id ) . '"' : '';
		$class        = ( $class != '' ) ? 'class="' . esc_attr( $class ) . '"' : '';
		$custom_style = ( $custom_style != '' ) ? ' style="' . esc_attr( $custom_style ) . '"' : '';
		$icon_class   = ( $icon_class != '' ) ? 'class="' . esc_attr( $icon_class ) . '"' : '';

		$title = ( $title != '' ) ? esc_attr( $title ) : '';


		if ( $text_same_size == 'true' ) {
			$custom_style .= ( $icon_size != '' ) ? ' font-size: ' . $icon_size . 'px;' : '';
		} else {
			$custom_style .= ( $text_size != '' ) ? ' font-size: ' . $text_size . 'px;' : '';
			$icon_style   .= ( $icon_size != '' ) ? ' font-size: ' . $icon_size . 'px;' : '';
		}

		if ( $text_same_color == 'true' ) {
			$custom_style .= ( $icon_color != '' ) ? ' color: ' . $icon_color . ';' : '';
		} else {
			$custom_style .= ( $text_color != '' ) ? ' color: ' . $text_color . ';' : '';
			$icon_style   .= ( $icon_color != '' ) ? ' color: ' . $icon_color . ';' : '';
		}

		ob_start();
		?>
        <div class="noo-offered-block">

            <span <?php echo $class; ?>><i
                        style="<?php echo $icon_style; ?><?php echo $icon_style_color; ?>" <?php echo $icon_class; ?>></i></span>
			<?php if ( isset( $title ) && ! empty( $title ) ): ?>
                <span class="noo-offered-title" style="<?php echo $custom_style; ?>">
			 		<?php echo $title; ?>
			 	</span>
			<?php endif; ?>
			<?php if ( isset( $content ) && ! empty( $content ) ): ?>
                <div class="noo-offered-content">
					<?php echo $content; ?>
                </div>
			<?php endif; ?>
        </div>

		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_offered', 'noo_offered_shortcode' );

endif;

if ( ! function_exists( 'noo_register_company_shortcode' ) ) :
	function noo_register_company_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'            => '',
			'sub_title'        => '',
			'icon'             => '',
			'size_icon'        => '40',
			'icon_color'       => '',
			'button_text'      => '',
			'link'             => '',
			'btn_bg_color'     => '',
			'btn_color'        => '',
			'btn_border_color' => '',
//			'background_image' => '',
			'class'            => '',
		), $atts ) );

		if ( $icon == '' ) {
			return '';
		}
		if ( $button_text == '' ) {
			$button_text = 'Register Company';
		}
		$url         = vc_build_link( $link );
		$link_url    = $url['url'];
		$custom_btn  = '';
		$custom_icon = '';
		$class       = ( $class != '' ) ? 'noo-register-company clearfix' . esc_attr( $class ) : 'noo-register-company clearfix ';
		$custom_icon .= ( $size_icon != '' ) ? 'font-size:' . $size_icon . 'px ;' : '';
		$custom_icon .= ( $icon_color != '' ) ? 'color:' . esc_attr( $icon_color ) . ';' : '';
		$custom_btn  .= ( $btn_bg_color != '' ) ? 'background-color:' . esc_attr( $btn_bg_color ) . ';' : '';
		$custom_btn  .= ( $btn_color != '' ) ? 'color:' . esc_attr( $btn_color ) . ';' : '';
		$custom_btn  .= ( $btn_border_color != '' ) ? 'border: 1px solid ' . esc_attr( $btn_border_color ) . ';' : '';
		if ( $link == '' ) {
			$link_href = Noo_Member::get_member_page_url();
		} else {
			$link_href = $link_url;
		}
		ob_start();

//		$bg = '';
//		if ( ! empty( $background_image ) ) {
//			$img = wp_get_attachment_image_src( $background_image, "full" );
//			$bg  = 'style="background: url(' . $img[ 0 ] . ') no-repeat center;"';
//		}

		?>

        <div class="<?php echo $class; ?>">
            <span class="noo-icon-register" style="<?php echo $custom_icon; ?>"><i
                        class="<?php echo $icon ?>"></i></span>
            <span class="register-title"><?php echo esc_html( $title ); ?></span>
            <p class="register-sub-title"><?php echo esc_html( $sub_title ) ?></p>
            <a href=" <?php echo esc_url( $link_href ) ?>" class="btn-register"
               style="<?php echo $custom_btn; ?>"> <?php echo esc_html( $button_text ); ?>
            </a>
        </div>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_register_company', 'noo_register_company_shortcode' );

endif;

if ( ! function_exists( 'noo_job_resume_alert_shortcode' ) ) :
	function noo_job_resume_alert_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'alert_type'       => 'job',
			'button_text'      => __('Create Job Alert', 'noo'),
			'button_color'	   => '#44494b',
			'icon'             => '',
			'class'            => '',
		), $atts ) );

		$class       = ( $class != '' ) ? 'noo-job-resume-alert clearfix' . esc_attr( $class ) : 'noo-job-resume-alert clearfix ';
		ob_start();

		$color = ( $button_color != '' ) ? 'color:' . esc_attr( $button_color ) . ';' : '';

		?>
		<div class="row noo-job-resume-alert">
			<?php if ($alert_type == 'job') { 
					if ( Noo_Job_Alert::enable_job_alert() ) : ?>
				        <div class="noo-btn-job-alert-form shortcode">
				            <i class="fa <?php echo $icon ?>" style="<?php echo $color; ?>"></i></i><span style="<?php echo $color; ?>"><?php echo esc_html( $button_text ); ?></span>
				        </div>
		    	<?php 
			    	add_action('wp_footer', function(){
			    		noo_get_layout('forms/job_alert_form_popup');
			    	},99);
		    		endif;
			} else {
					if(Noo_Resume_Alert::enable_resume_alert()): ?>
	                    <div class="noo-btn-resume-alert-form shortcode">
	                        <i class="fa <?php echo $icon ?>" style="<?php echo $color; ?>"></i><span style="<?php echo $color; ?>"><?php echo esc_html( $button_text ); ?></span>
	                    </div>
	                <?php 
	                add_action('wp_footer', function(){
	                	noo_get_layout('forms/resume_alert_form_popup'); 
	                },99);
	                endif; 
	                
			}?>
		</div>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_job_resume_alert', 'noo_job_resume_alert_shortcode' );

endif;

// Noo Counter
// ============================
if ( ! function_exists( 'noo_shortcode_counter_icon' ) ) :
	function noo_shortcode_counter_icon( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'number'       => '',
			'text'         => '',
			'icon'         => '',
			'data'         => 'public_job',
			'visibility'   => '',
			'class'        => '',
			'id'           => '',
			'custom_style' => '',
		), $atts ) );

		wp_enqueue_script( 'vendor-countTo' );
		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? 'noo-counter-icon clearfix' . esc_attr( $class ) : 'noo-counter-icon clearfix';
		$class      .= noo_visibility_class( $visibility );

		$id    = ( $id != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? ' class="' . $class . '"' : '';

		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
		$icon         = ( $icon != '' ) ? $icon : 'fa fa-home';
		$number       = ( $number != '' ) ? $number : 99;

		$number_show = '';
		$text_show   = '';
		if ( ! empty( $data ) ) {
			switch ( $data ) {
				case 'public_job' :
					$job_count   = wp_count_posts( 'noo_job' );
					$number_show = isset($job_count->publish) ? $job_count->publish : 0;
					$text_show   = ( $text != '' ) ? $text : __( 'Public Jobs', 'noo' );
					break;
				case 'public_resume' :
					//$resume_count = wp_count_posts( 'noo_resume' );
					//$number_show  = isset($resume_count->publish) ? $resume_count->publish : 0;
					$number_show = Noo_Resume::count_viewable_resumes( '', true );
					$text_show    = ( $text != '' ) ? $text : __( 'Public Resumes', 'noo' );
					break;
				case 'all_job' :
					$job_count   = wp_count_posts( 'noo_job' );
					$job_pending = isset($job_count->pending) ? $job_count->pending : 0;
					$job_public  = isset($job_count->publish) ? $job_count->publish : 0;
					$number_show = ( $job_pending + $job_public );
					$text_show   = ( $text != '' ) ? $text : __( 'All Jobs', 'noo' );
					break;
				case 'all_resume' :
					$resume_count = wp_count_posts( 'noo_resume' );
					
					$resume_pending = isset($resume_count->pending) ? $resume_count->pending : 0;
					//$resume_public  = isset($resume_count->publish) ? $resume_count->publish : 0;
					$resume_public = \Noo_Resume::count_viewable_resumes( '', true );
					$number_show  = $resume_pending + $resume_public;

					$text_show    = ( $text != '' ) ? $text : __( 'All Resumes', 'noo' );
					break;
				case 'all_company' :
					$company_count = wp_count_posts( 'noo_company' );

					$company_pending = isset($company_count->pending) ? $company_count->pending : 0;
					$company_public  = isset($company_count->publish) ? $company_count->publish : 0;

					$number_show   = $company_pending + $company_public;

					$text_show     = ( $text != '' ) ? $text : __( 'All Companies', 'noo' );
					break;
				case 'all_application' :
					$application_count = wp_count_posts( 'noo_application' );

					$apply_pending = isset($application_count->pending) ? $application_count->pending : 0;
					$apply_public  = isset($application_count->publish) ? $application_count->publish : 0;

					$number_show       = $apply_pending + $apply_public;

					$text_show         = ( $text != '' ) ? $text : __( 'All Applications', 'noo' );
					break;
				case 'user_all' :
					$result      = count_users();
					$number_show = $result['total_users'];
					$text_show   = ( $text != '' ) ? $text : __( 'All Users', 'noo' );
					break;
				case 'user_candidate' :
					$result      = count_users();
					$number_show = isset($result['avail_roles']['candidate']) ? $result['avail_roles']['candidate'] : 0;
					$text_show   = ( $text != '' ) ? $text : __( 'Candidate', 'noo' );
					break;
				case 'user_employer' :
					$result      = count_users();
					$number_show = isset($result['avail_roles']['employer']) ? $result['avail_roles']['employer'] : 0;
					$text_show   = ( $text != '' ) ? $text : __( 'Employer', 'noo' );
					break;
				case 'woo_order' :
					if(NOO_WOOCOMMERCE_EXIST){
						$number_show = wc_orders_count('completed');
						$text_show   = ( $text != '' ) ? $text : '';
					}					
					break;
				case 'custom' :
					$number_show = $number;
					$text_show   = ( $text != '' ) ? $text : '';
					break;
			}
		}
		ob_start();

		?>
        <div<?php echo( $id . $class ); ?>>
            <div class="noo-counter-item">
                <div class="noo-counter-font-icon pull-left">
                    <i class="<?php echo esc_attr( $icon ) ?>"></i>
                </div>
                <div class="noo-counter-icon-content pull-left" <?php echo $custom_style ?>>
                    <div data-number="<?php echo esc_attr( $number_show ); ?>" class="noo-counter"><?php echo esc_html( $number_show ); ?></div>
                    <span class="noo-counter-text"><?php echo esc_html( $text_show ); ?></span>
                </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_counter', 'noo_shortcode_counter_icon' );
endif;


if ( ! function_exists( 'noo_list_job_category_shortcode' ) ) :
	function noo_list_job_category_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'title'              => '',
			'style_job_category' => 'style-1',
			'show_job_count'     => 'true',
			'hide_empty'         => 'true',
			'list_job_category'  => '',
			'list_column'        => '',
			'visibility'         => '',
			'class'              => '',
			'id'                 => '',
			'custom_style'       => '',
			'viewmore_url'       => '',
			'limit_category'     => '12',

		), $atts ) );

		$visibility = ( $visibility != '' ) && ( $visibility != 'all' ) ? esc_attr( $visibility ) : '';
		$class      = ( $class != '' ) ? 'noo-list-job-category clearfix' . esc_attr( $class ) : 'noo-list-job-category clearfix';
		$class      .= noo_visibility_class( $visibility );

		$id    = ( $id != '' ) ? ' id="' . esc_attr( $id ) . '"' : '';
		$class = ( $class != '' ) ? ' class="' . $class . '"' : '';

		$custom_style = ( $custom_style != '' ) ? ' style="' . $custom_style . '"' : '';
		$link_url     = ( ! empty( $link_url ) ) ? $link_url : '#';

		$list_column = ( ! empty( $list_column ) ) ? $list_column : '4';
		ob_start();
		?>
        <div<?php echo( $id . $class . $custom_style ); ?>>
			<?php if ( ! empty( $title ) ): ?>
                <h3 class="noo-heading-title">
					<?php echo esc_html( $title ); ?>
                </h3>
			<?php endif; ?>
            <div class="noo-list-job-category-content noo-list-job-category-col-<?php echo esc_attr( $list_column ) . ' ' . $style_job_category; ?>">
                <ul>
					<?php
					$i = 0;

					if ( $list_job_category == 'all' or $list_job_category == '' ) {
						$categories = get_terms( 'job_category', array(
							'orderby'    => 'NAME',
							'order'      => 'ASC',
							'hide_empty' => ( 'true' == $hide_empty ) ? false : true,
						) );
						foreach ( $categories as $key => $cat ) :
							if ( $i >= $limit_category ) {
								break;
							}

							$cate_name = $cat->name;
							$job_count = $cat->count;
							$cate_link = get_term_link( $cat );
							?>
                            <li class="col-lg-<?php echo( 12 / $list_column ) ?> col-md-<?php echo( 12 / $list_column ) ?> col-sm-<?php echo( 12 / $list_column ) ?> col-xs-12">
                                <a href="<?php echo esc_url( $cate_link ); ?>"><?php echo esc_html( $cate_name ); ?>
									<?php if ( 'true' == $show_job_count ) : ?>
                                        <span class="job-count">(<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?>)</span>
									<?php endif; ?>
                                </a>
                            </li>
							<?php $i ++; endforeach;
					} else {
						$list_cat = explode( ',', $list_job_category );
						foreach ( $list_cat as $key => $cat ) :
							$cate = get_term_by( 'id', absint( $cat ), 'job_category' );
							if ( ! empty( $cate ) ):
								if ( $i >= $limit_category ) {
									continue;
								}

								$cate_name = $cate->name;
								$job_count = $cate->count;
								$cate_link = get_term_link( $cate );
								?>
                                <li class="col-lg-<?php echo( 12 / $list_column ) ?> col-md-<?php echo( 12 / $list_column ) ?> col-sm-<?php echo( 12 / $list_column ) ?> col-xs-12">
                                    <a href="<?php echo esc_url( $cate_link ); ?>"><?php echo esc_html( $cate_name ); ?>
										<?php if ( 'true' == $show_job_count ) : ?>
                                            <span class="job-count">(<?php echo sprintf( _n( '%s Job', '%s Jobs', $job_count, 'noo' ), $job_count ); ?>)</span>
										<?php endif; ?>
                                    </a>
                                </li>
							<?php
							endif;
							$i ++; endforeach;
					}
					?>
                </ul>
            </div>
			<?php
			$viewmore_url = isset( $viewmore_url ) ? vc_build_link( $viewmore_url ) : '';
			if ( ! empty( $viewmore_url['url'] ) ) {
				echo '<div class="view-more"><a class="btn btn-primary" href="' . esc_url( $viewmore_url['url'] ) . '">' . esc_html( $viewmore_url['title'] ) . '</a></div>';
			}
			?>
        </div>
		<?php
		return ob_get_clean();
	}

	add_shortcode( 'noo_list_job_category', 'noo_list_job_category_shortcode' );

endif;