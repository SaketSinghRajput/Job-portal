<?php
/**
 * Utilities Functions for NOO Framework.
 * This file contains various functions for getting and preparing data.
 *
 * @package    NOO Framework
 * @version    1.0.0
 * @author     NooTheme Team
 * @copyright  Copyright (c) 2014, NooTheme
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://www.nootheme.com
 */

if (!function_exists('noo_package_show_no_support_feature')){
	function noo_package_show_no_support_feature(){
		return apply_filters('noo_package_show_no_support_feature',true);
	}
}

if ( ! function_exists( 'noo_get_endpoint_url' ) ) {
	function noo_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $permalink, '?' ) ) {
				$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
				$permalink    = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}
			$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
		} else {
			$url = esc_url_raw( add_query_arg( $endpoint, $value, $permalink ) );
		}
		return apply_filters( 'noo_get_endpoint_url', $url, $endpoint );
	}
}

if ( ! function_exists( 'wp_body_open' ) ) {
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

if ( ! function_exists( 'smk_get_all_sidebars' ) ):
	function smk_get_all_sidebars() {
		global $wp_registered_sidebars;
		$sidebars      = array();
		$none_sidebars = array();
		for ( $i = 1; $i <= 4; $i ++ ) {
			$none_sidebars[] = "noo-top-{$i}";
			$none_sidebars[] = "noo-footer-{$i}";
		}
		if ( $wp_registered_sidebars && ! is_wp_error( $wp_registered_sidebars ) ) {

			foreach ( $wp_registered_sidebars as $sidebar ) {
				// Don't include Top Bar & Footer Widget Area
				if ( in_array( $sidebar[ 'id' ], $none_sidebars ) ) {
					continue;
				}

				$sidebars[ $sidebar[ 'id' ] ] = $sidebar[ 'name' ];
			}
		}

		return $sidebars;
	}
endif;

if ( ! function_exists( 'get_sidebar_name' ) ):
	function get_sidebar_name( $id = '' ) {
		if ( empty( $id ) ) {
			return '';
		}

		global $wp_registered_sidebars;
		if ( $wp_registered_sidebars && ! is_wp_error( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $sidebar ) {
				if ( $sidebar[ 'id' ] == $id ) {
					return $sidebar[ 'name' ];
				}
			}
		}

		return '';
	}
endif;

if ( ! function_exists( 'get_sidebar_id' ) ):
	function get_sidebar_id() {
		// Normal Page or Static Front Page
		if ( is_page() || ( is_front_page() && get_option( 'show_on_front' ) == 'page' ) ) {
			// Get the sidebar setting from
			$sidebar = noo_get_post_meta( get_the_ID(), '_noo_wp_page_sidebar', 'sidebar-main' );

			return $sidebar;
		}

		// NOO Resume
		if ( is_post_type_archive( 'noo_resume' ) ) {
			$resume_layout = noo_get_option( 'noo_resumes_layout', 'sidebar' );
			if ( $resume_layout != 'fullwidth' ) {
				return noo_get_option( 'noo_resume_list_sidebar', 'sidebar-resume' );
			}

			return '';
		}
		if ( is_singular( 'noo_resume' ) ) {
			return '';
		}

		// NOO Company
		if ( is_post_type_archive( 'noo_company' ) || is_singular( 'noo_company' ) ) {
			$companies_layout = noo_get_option( 'noo_companies_layout', 'fullwidth' );
			if ( $companies_layout != 'fullwidth' ) {
				return noo_get_option( 'noo_companies_sidebar', 'sidebar-company' );
			}

			return '';
		}

		// NOO Job
		$job_taxes = jm_get_job_taxonomies();
		if ( is_post_type_archive( 'noo_job' ) || is_tax( $job_taxes ) ) {

			$jobs_layout = noo_get_option( 'noo_jobs_layout', 'sidebar' );
			if ( $jobs_layout != 'fullwidth' ) {
				return noo_get_option( 'noo_jobs_sidebar', 'sidebar-job' );
			}

			return '';
		}

		// Single Job
		if ( is_singular( 'noo_job' ) ) {
			$single_job_id    = get_the_ID();
			$job_meta_sidebar = get_post_meta( $single_job_id, '_job_sidebar', true );
			if ( empty( $job_meta_sidebar ) ) {
				return noo_get_option( 'noo_single_jobs_sidebar', true );
			} else {
				return $job_meta_sidebar;
			}
		}

		// WooCommerce Product
		if ( NOO_WOOCOMMERCE_EXIST ) {
			if ( is_product() ) {
				$product_layout = noo_get_option( 'noo_woocommerce_product_layout', 'same_as_shop' );
				$sidebar        = '';
				if ( $product_layout == 'same_as_shop' ) {
					$product_layout = noo_get_option( 'noo_shop_layout', 'fullwidth' );
					$sidebar        = noo_get_option( 'noo_shop_sidebar', '' );
				} else {
					$sidebar = noo_get_option( 'noo_woocommerce_product_sidebar', '' );
				}

				if ( $product_layout == 'fullwidth' ) {
					return '';
				}

				return $sidebar;
			}

			// Shop, Product Category, Product Tag, Cart, Checkout page
			if ( is_shop() || is_product_category() || is_product_tag() ) {
				$shop_layout = noo_get_option( 'noo_shop_layout', 'fullwidth' );
				if ( $shop_layout != 'fullwidth' ) {
					return noo_get_option( 'noo_shop_sidebar', '' );
				}

				return '';
			}
		}

		// Single post page
		if ( is_single() ) {
			// Check if there's overrode setting in this post.
			$post_id          = get_the_ID();
			$override_setting = noo_get_post_meta( $post_id, '_noo_wp_post_override_layout', false );
			if ( $override_setting ) {
				// overrode
				$overrode_layout = noo_get_post_meta( $post_id, '_noo_wp_post_layout', 'fullwidth' );
				if ( $overrode_layout != 'fullwidth' ) {
					return noo_get_post_meta( $post_id, '_noo_wp_post_sidebar', 'sidebar-main' );
				}
			} else {

				$post_layout = noo_get_option( 'noo_blog_post_layout', 'same_as_blog' );
				$sidebar     = '';
				if ( $post_layout == 'same_as_blog' ) {
					$post_layout = noo_get_option( 'noo_blog_layout', 'sidebar' );
					$sidebar     = noo_get_option( 'noo_blog_sidebar', 'sidebar-main' );
				} else {
					$sidebar = noo_get_option( 'noo_blog_post_sidebar', 'sidebar-main' );
				}

				if ( $post_layout == 'fullwidth' ) {
					return '';
				}

				return $sidebar;
			}

			return '';
		}

		// Archive page
		if ( is_archive() ) {
			$archive_layout = noo_get_option( 'noo_blog_archive_layout', 'same_as_blog' );
			$sidebar        = '';
			if ( $archive_layout == 'same_as_blog' ) {
				$archive_layout = noo_get_option( 'noo_blog_layout', 'sidebar' );
				$sidebar        = noo_get_option( 'noo_blog_sidebar', 'sidebar-main' );
			} else {
				$sidebar = noo_get_option( 'noo_blog_archive_sidebar', 'sidebar-main' );
			}

			if ( $archive_layout == 'fullwidth' ) {
				return '';
			}

			return $sidebar;
		}

		// Archive, Index or Home
		if ( is_home() || is_archive() || ( is_front_page() && get_option( 'show_on_front' ) == 'posts' ) ) {

			$blog_layout = noo_get_option( 'noo_blog_layout', 'sidebar' );
			if ( $blog_layout != 'fullwidth' ) {
				return noo_get_option( 'noo_blog_sidebar', 'sidebar-main' );
			}

			return '';
		}

		return '';
	}
endif;

if ( ! function_exists( 'noo_default_primary_color' ) ) :
	function noo_default_primary_color() {
		return '#f5d006';
	}
endif;
if ( ! function_exists( 'noo_default_font_family' ) ) :
	function noo_default_font_family() {
		return 'Droid Serif';
	}
endif;
if ( ! function_exists( 'noo_default_text_color' ) ) :
	function noo_default_text_color() {
		return '#44494b';
	}
endif;
if ( ! function_exists( 'noo_default_headings_font_family' ) ) {
	function noo_default_headings_font_family() {
		return 'Montserrat';
	}
}
if ( ! function_exists( 'noo_default_headings_color' ) ) {
	function noo_default_headings_color() {
		return noo_default_text_color();
	}
}
if ( ! function_exists( 'noo_default_header_bg' ) ) {
	function noo_default_header_bg() {
		if ( noo_get_option( 'noo_site_skin', 'light' ) == 'dark' ) {
			return '#000000';
		}

		return '#FFFFFF';
	}
}
if ( ! function_exists( 'noo_default_nav_font_family' ) ) {
	function noo_default_nav_font_family() {
		return noo_default_headings_font_family();
	}
}
if ( ! function_exists( 'noo_default_logo_font_family' ) ) {
	function noo_default_logo_font_family() {
		return noo_default_headings_font_family();
	}
}
if ( ! function_exists( 'noo_default_logo_color' ) ) {
	function noo_default_logo_color() {
		return noo_default_headings_color();
	}
}
if ( ! function_exists( 'noo_default_font_size' ) ) {
	function noo_default_font_size() {
		return '14';
	}
}
if ( ! function_exists( 'noo_default_font_weight' ) ) {
	function noo_default_font_weight() {
		return '400';
	}
}

//
// This function help to create the dynamic thumbnail width,
// but we don't use it at the moment.
// 
if ( ! function_exists( 'noo_thumbnail_width' ) ) :
	function noo_thumbnail_width() {
		$site_layout = noo_get_option( 'noo_site_layout', 'fullwidth' );
		$page_layout = get_page_layout();
		$width       = 1200; // max width

		if ( $site_layout == 'boxed' ) {
			$site_width     = (int) noo_get_option( 'noo_layout_site_width', '90' );
			$site_max_width = (int) noo_get_option( 'noo_layout_site_max_width', '1200' );
			$width          = min( $width * $site_width / 100, $site_max_width );
		}

		if ( $page_layout != 'fullwidth' ) {
			$width = $width * 75 / 100; // 75% of col-9
		}

		return $width;
	}
endif;

if ( ! function_exists( 'get_thumbnail_width' ) ) :
	function get_thumbnail_width() {

		// if( is_admin()) {
		// 	return 'admin-thumb';
		// }

		$site_layout = noo_get_option( 'noo_site_layout', 'fullwidth' );
		$page_layout = get_page_layout();

		if ( $site_layout == 'boxed' ) {
			if ( $page_layout == 'fullwidth' ) {
				return 'boxed-fullwidth';
			} else {
				return 'boxed-sidebar';
			}
		} else {
			if ( $page_layout == 'fullwidth' ) {
				return 'fullwidth-fullwidth';
			} else {
				return 'fullwidth-sidebar';
			}
		}

		return 'fullwidth-fullwidth';
	}
endif;

if ( ! function_exists( 'get_page_layout' ) ):
	function get_page_layout() {

		// Normal Page or Static Front Page
		if ( is_page() || ( is_front_page() && get_option( 'show_on_front' ) == 'page' ) ) {
			// WP page,
			// get the page template setting
			$page_id       = get_the_ID();
			$page_template = noo_get_post_meta( $page_id, '_wp_page_template', 'default' );

			if ( strpos( $page_template, 'sidebar' ) !== false ) {
				if ( strpos( $page_template, 'left' ) !== false ) {
					return 'left_sidebar';
				}

				return 'sidebar';
			}

			return 'fullwidth';
		}

		// NOO Resume
		if ( is_post_type_archive( 'noo_resume' ) ) {
			return noo_get_option( 'noo_resumes_layout', 'sidebar' );
		}
		if ( is_singular( 'noo_resume' ) ) {
			return 'fullwidth';
		}

		// NOO Company
		if ( is_post_type_archive( 'noo_company' ) ) {
			return noo_get_option( 'noo_companies_layout', 'fullwidth' );
		}

		if ( is_singular( 'noo_company' ) ) {
			if ( noo_get_option( 'noo_companies_layout', 'fullwidth' ) == 'fullwidth' ) {
				return 'sidebar';
			} else {
				return noo_get_option( 'noo_companies_layout', 'fullwidth' );
			}
		}

		// NOO Job
		$job_taxes = jm_get_job_taxonomies();
		if ( is_post_type_archive( 'noo_job' ) || is_tax( $job_taxes ) ) {

			return noo_get_option( 'noo_jobs_layout', 'sidebar' );
		}

		// Single Job
		if ( is_singular( 'noo_job' ) ) {
			$single_job_id = get_the_ID();

			$job_meta_layout = get_post_meta( $single_job_id, '_layout_style', true );
			if ( empty( $job_meta_layout ) or $job_meta_layout == 'default' ) {
				return noo_get_option( 'noo_single_jobs_layout', 'right_company' );
			} else {
				return $job_meta_layout;
			}
		}

		// WooCommerce
		if ( NOO_WOOCOMMERCE_EXIST ) {
			if ( is_shop() || is_product_category() || is_product_tag() ) {
				return noo_get_option( 'noo_shop_layout', 'fullwidth' );
			}

			if ( is_product() ) {
				$product_layout = noo_get_option( 'noo_woocommerce_product_layout', 'same_as_shop' );
				if ( $product_layout == 'same_as_shop' ) {
					$product_layout = noo_get_option( 'noo_shop_layout', 'fullwidth' );
				}

				return $product_layout;
			}
		}

		// Single post page
		if ( is_single() ) {

			// WP post,
			// check if there's overrode setting in this post.
			$post_id          = get_the_ID();
			$override_setting = noo_get_post_meta( $post_id, '_noo_wp_post_override_layout', false );

			if ( ! $override_setting ) {
				$post_layout = noo_get_option( 'noo_blog_post_layout', 'same_as_blog' );
				if ( $post_layout == 'same_as_blog' ) {
					$post_layout = noo_get_option( 'noo_blog_layout', 'sidebar' );
				}

				return $post_layout;
			}

			// overrode
			return noo_get_post_meta( $post_id, '_noo_wp_post_layout', 'sidebar-main' );
		}

		// Archive
		if ( is_archive() ) {
			$archive_layout = noo_get_option( 'noo_blog_archive_layout', 'same_as_blog' );
			if ( $archive_layout == 'same_as_blog' ) {
				$archive_layout = noo_get_option( 'noo_blog_layout', 'sidebar' );
			}

			return $archive_layout;
		}

		// Index or Home
		if ( is_home() || ( is_front_page() && get_option( 'show_on_front' ) == 'posts' ) ) {

			return noo_get_option( 'noo_blog_layout', 'sidebar' );
		}

		return '';
	}
endif;

if ( ! function_exists( 'is_fullwidth' ) ) {
	function is_fullwidth() {
		return get_page_layout() == 'fullwidth';
	}
}

if ( ! function_exists( 'is_one_page_enabled' ) ):
	function is_one_page_enabled() {
		if ( ( is_front_page() && get_option( 'show_on_front' == 'page' ) ) || is_page() ) {
			$page_id = get_the_ID();

			return ( noo_get_post_meta( $page_id, '_noo_wp_page_enable_one_page', false ) );
		}

		return false;
	}
endif;

if ( ! function_exists( 'get_one_page_menu' ) ):
	function get_one_page_menu() {
		if ( is_one_page_enabled() ) {
			if ( ( is_front_page() && get_option( 'show_on_front' == 'page' ) ) || is_page() ) {
				$page_id = get_the_ID();

				return noo_get_post_meta( $page_id, '_noo_wp_page_one_page_menu', '' );
			}
		}

		return '';
	}
endif;

if ( ! function_exists( 'has_home_slider' ) ):
	function has_home_slider() {
		if ( class_exists( 'RevSlider' ) ) {
			if ( ( is_front_page() && get_option( 'show_on_front' == 'page' ) ) || is_page() ) {
				$page_id = get_the_ID();

				return ( noo_get_post_meta( $page_id, '_noo_wp_page_enable_home_slider',
						false ) ) && ( noo_get_post_meta( $page_id, '_noo_wp_page_slider_rev', '' ) != '' );
			}
		}

		return false;
	}
endif;

if ( ! function_exists( 'home_slider_position' ) ):
	function home_slider_position() {
		if ( has_home_slider() ) {
			return noo_get_post_meta( get_the_ID(), '_noo_wp_page_slider_position', 'below' );
		}

		return '';
	}
endif;

if ( ! function_exists( 'get_page_heading' ) ):
	function get_page_heading() {
		$heading     = '';
		$sub_heading = '';
		if ( is_home() ) {
			$heading = noo_get_option( 'noo_blog_heading_title', __( 'Blog', 'noo' ) );
		} elseif ( is_search() ) {
			$heading = __( 'Search Results', 'noo' );
			$search_query = get_search_query();
			if ( ! empty( $search_query ) ) {
				$heading = __( 'Search Results for:', 'noo' ) . " " . esc_attr( $search_query );
			}
		} elseif ( isset($_GET['resume_category']) && !empty($_GET['resume_category']) ) {
			$term = get_term( $_GET['resume_category']);
			$heading = $term->name;
		} elseif ( is_post_type_archive( 'noo_job' ) ) {
			$heading = noo_get_option( 'noo_job_heading_title', __( 'Jobs', 'noo' ) );
		} elseif ( is_post_type_archive( 'noo_company' ) ) {
			$heading = noo_get_option( 'noo_companies_heading_title', __( 'Companies', 'noo' ) );
		} elseif ( is_post_type_archive( 'noo_resume' ) ) {
			$heading = noo_get_option( 'noo_resume_heading_title', __( 'Resume Listing', 'noo' ) );
		} elseif ( NOO_WOOCOMMERCE_EXIST && is_shop() ) {
			$heading = noo_get_option( 'noo_shop_heading_title', __( 'Shop', 'noo' ) );
		} elseif ( is_author() ) {
			$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			$heading = __( 'Author Archive', 'noo' );

			if ( isset( $curauth->nickname ) ) {
				$heading .= ' ' . __( 'for:', 'noo' ) . " " . $curauth->nickname;
			}
		} elseif ( is_year() ) {
			$heading = __( 'Post Archive by Year: ', 'noo' ) . get_the_date( 'Y' );
		} elseif ( is_month() ) {
			$heading = __( 'Post Archive by Month: ', 'noo' ) . get_the_date( 'F,Y' );
		} elseif ( is_day() ) {
			$heading = __( 'Post Archive by Day: ', 'noo' ) . get_the_date( 'F j, Y' );
		} elseif ( is_404() ) {
			$heading     = __( 'Oops! We could not find anything to show you.', 'noo' );
			$sub_heading = __( 'Would you like to go somewhere else to find your stuff?', 'noo' );
		} elseif ( is_archive() ) {
			$heading     = single_cat_title( '', false );
			$sub_heading = term_description();
		} elseif ( is_page() ) {
			$page_temp = get_page_template_slug();
			if ( noo_get_post_meta( get_the_ID(), '_noo_wp_page_hide_page_title', false ) ) {
				$heading = '';
			} elseif ( get_the_ID() == Noo_Member::get_member_page_id() ) {
				$heading      = get_the_title();
				$current_user = wp_get_current_user();
				if ( 'username' == Noo_Member::get_setting( 'member_title', 'page_title' ) && 0 != $current_user->ID ) {
					$heading = Noo_Member::get_display_name( $current_user->ID );
					if ($heading == '') {
						$heading = get_the_title();
					}
				}
				$sub_heading = Noo_Member::get_member_heading_label();
				if ( empty( $sub_heading ) && ! is_user_logged_in() ) {
					$sub_heading = Noo_Member::can_register() ? __( 'Login or create an account', 'noo' ) : __( 'Login',
						'noo' );
				}
			} elseif ( 'page-post-job.php' === $page_temp ) {
				$heading = jm_get_button_text();
				$step    = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '';
				if ( $step == 'login' ) {
					$sub_heading = Noo_Member::can_register() ? __( 'Login or create an account', 'noo' ) : __( 'Login',
						'noo' );
				} elseif ( $step == 'job_package' ) {
					$sub_heading = __( 'Choose a package', 'noo' );
				} elseif ( $step == 'post_job' ) {
					$sub_heading = __( 'Describe your company and vacancy', 'noo' );
				} elseif ( $step == 'preview_job' ) {
					$sub_heading = __( 'Preview and submit your job', 'noo' );
				} else {
					$sub_heading = Noo_Member::can_register() ? __( 'Login or create an account', 'noo' ) : __( 'Login',
						'noo' );
				}
			} elseif ( 'page-post-resume.php' === $page_temp ) {
				$heading = jm_get_button_text('resume');
				$step    = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '';
				if ( $step == 'login' ) {
					$sub_heading = Noo_Member::can_register() ? __( 'Login or create an account', 'noo' ) : __( 'Login',
						'noo' );
				} elseif ( $step == 'resume_general' ) {
					$sub_heading = __( 'General Information', 'noo' );
				} elseif ( $step == 'resume_detail' ) {
					$sub_heading = __( 'Resume Details', 'noo' );
				} elseif ( $step == 'resume_preview' ) {
					$sub_heading = __( 'Preview and Finish', 'noo' );
				} else {
					$sub_heading = Noo_Member::can_register() ? __( 'Login or create an account', 'noo' ) : __( 'Login',
						'noo' );
				}
			} else {
				$heading = get_the_title();
			}
		} elseif ( is_singular() ) {
			$heading = get_the_title();
		}
		$heading = apply_filters('noo_get_page_heading', $heading);
		$sub_heading = apply_filters('noo_get_page_heading_sub', $sub_heading);
		return array( $heading, $sub_heading );
	}
endif;

if ( ! function_exists( 'get_page_heading_image' ) ):
	function get_page_heading_image() {
		$image = '';
		

		if ( NOO_WOOCOMMERCE_EXIST && is_shop() ) {
			$image = noo_get_image_option( 'noo_shop_heading_image', '' );
		} elseif ( is_home() ) {
			$image = noo_get_image_option( 'noo_blog_heading_image', '' );
		} elseif ( is_category() || is_tag() ) {
			$image = noo_get_image_option( 'noo_blog_heading_image', '' );
		} elseif ( NOO_WOOCOMMERCE_EXIST && ( is_product_category() || is_product_tag() ) ) {
			$image = noo_get_image_option( 'noo_shop_heading_image', '' );
		} elseif ( is_singular( 'noo_job' ) ) {
			$image = '';
			$image = noo_get_post_meta( get_the_ID(), '_cover_image', '' );

			if ( empty( $image ) ) {
				$company_id = jm_get_job_company( get_the_ID() );
				$image      = noo_get_post_meta( $company_id, '_cover_image', '' );
			}
			if ( empty( $image ) ) {
				$image = noo_get_image_option( 'noo_job_heading_image', '' );
			}
		} elseif ( is_singular( 'noo_company' ) ) {
			$image = noo_get_post_meta( get_the_ID(), '_cover_image', '' );
		} elseif ( is_singular( 'product' ) ) {
			$image = noo_get_image_option( 'noo_shop_heading_image', '' );
		} elseif ( is_page() ) {
			if (Noo_Member::get_member_page_id() == get_the_ID()) {
                $image = noo_get_post_meta(get_the_ID(), '_heading_image', '');
                if(empty($image)){
                    $image = noo_get_image_option('noo_member_page_title_bgimg', '');
                }
            }else{

                $image = noo_get_post_meta(get_the_ID(), '_heading_image', '');
            }

		} elseif ( is_singular( 'post' ) ) {
			$image = noo_get_image_option( 'noo_blog_heading_image', '' );
		} elseif ( is_tax( 'class_category' ) ) {
			$image = noo_get_image_option( 'noo_class_heading_image', '' );
		} elseif ( is_post_type_archive( 'noo_job' ) || is_tax( 'job_location' ) || is_tax( 'job_category' ) || is_tax('job_tag') || is_tax('job_type') ) {
			$image = noo_get_image_option( 'noo_job_heading_image', '' );
		} elseif ( is_post_type_archive( 'noo_company' ) ) {
			$image = noo_get_image_option( 'noo_companies_heading_image', '' );
		} elseif ( is_post_type_archive( 'noo_resume' ) || is_singular( 'noo_resume' ) ) {
			$image = noo_get_image_option( 'noo_resume_heading_image', '' );
		}
		
		$image_id = 0;
		if ( is_numeric( $image ) && ! empty( $image )  ) {
			$image_id = $image;
			if($image_data = wp_get_attachment_image_src( $image_id, apply_filters('get_page_heading_image_size', 'cover-image') )){
				$image =  $image_data[ 0 ];
			}
		}
		
		if ( empty( $image ) ) {
			$image = apply_filters('noo_default_page_heading_image', NOO_ASSETS_URI . '/images/heading-bg.png');
		}

		return apply_filters('get_page_heading_image', $image, $image_id);
	}
endif;

if ( ! function_exists( 'noo_get_post_format' ) ):
	function noo_get_post_format( $post_id = null, $post_type = '' ) {
		$post_id   = ( null === $post_id ) ? get_the_ID() : $post_id;
		$post_type = ( '' === $post_type ) ? get_post_type( $post_id ) : $post_type;

		$post_format = '';

		if ( $post_type == 'post' ) {
			$post_format = get_post_format( $post_id );
		}

		if ( $post_type == 'portfolio_project' ) {
			$post_format = noo_get_post_meta( $post_id, '_noo_portfolio_media_type', 'image' );
		}

		return $post_format;
	}
endif;

if ( ! function_exists( 'has_featured_content' ) ):
	function has_featured_content( $post_id = null ) {
		$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;

		$post_type   = get_post_type( $post_id );
		$prefix      = '';
		$post_format = '';

		if ( $post_type == 'post' ) {
			$prefix      = '_noo_wp_post';
			$post_format = get_post_format( $post_id );
		}

		if ( $post_type == 'portfolio_project' ) {
			$prefix      = '_noo_portfolio';
			$post_format = noo_get_post_meta( $post_id, "{$prefix}_media_type", 'image' );
		}

		switch ( $post_format ) {
			case 'image':
				$main_image = noo_get_post_meta( $post_id, "{$prefix}_main_image", 'featured' );
				if ( $main_image == 'featured' ) {
					$has_featured = has_post_thumbnail( $post_id );
					return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
				}
				$has_featured = has_post_thumbnail( $post_id ) || ( (bool) noo_get_post_meta( $post_id, "{$prefix}_image",'' ) );
				return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
				
			case 'gallery':
				if ( ! is_singular() ) {
					$preview_content = noo_get_post_meta( $post_id, "{$prefix}_gallery_preview", 'slideshow' );
					if ( $preview_content == 'featured' ) {
						$has_featured = has_post_thumbnail( $post_id );
						return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
					}
				}
				$has_featured = (bool) noo_get_post_meta( $post_id, "{$prefix}_gallery", '' );
				return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
				
			case 'video':
				if ( ! is_singular() ) {
					$preview_content = noo_get_post_meta( $post_id, "{$prefix}_preview_video", 'both' );
					if ( $preview_content == 'featured' ) {
						$has_featured = has_post_thumbnail( $post_id );
						return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
					}
				}

				$m4v_video   = (bool) noo_get_post_meta( $post_id, "{$prefix}_video_m4v", '' );
				$ogv_video   = (bool) noo_get_post_meta( $post_id, "{$prefix}_video_ogv", '' );
				$embed_video = (bool) noo_get_post_meta( $post_id, "{$prefix}_video_embed", '' );

				$has_featured = $m4v_video || $ogv_video || $embed_video;
				return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);

			case 'audio':
				$mp3_audio   = (bool) noo_get_post_meta( $post_id, "{$prefix}_audio_mp3", '' );
				$oga_audio   = (bool) noo_get_post_meta( $post_id, "{$prefix}_audio_oga", '' );
				$embed_audio = (bool) noo_get_post_meta( $post_id, "{$prefix}_audio_embed", '' );

				$has_featured = $mp3_audio || $oga_audio || $embed_audio;
				return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
				
			default: // standard post format
				$has_featured = has_post_thumbnail( $post_id );
				return apply_filters('noo_has_featured_content', $has_featured, $post_id, $post_format, $post_type);
				
		}

		return apply_filters('noo_has_featured_content', false, $post_id, $post_format, $post_type);
	}
endif;

if ( ! function_exists( 'noo_get_page_id_by_template' ) ):
	function noo_get_page_id_by_template( $page_template = '' ) {
		global $page_id_by_template;
		if ( empty( $page_id_by_template ) || ! isset( $page_id_by_template[ $page_template ] ) ) {
			$pages = get_pages( array(
				'meta_key'   => '_wp_page_template',
				'meta_value' => $page_template,
			) );

			if ( $pages ) {
				// $page_id = apply_filters( 'wpml_object_id', $pages[0]->ID, 'page', true );
				$page_id                               = $pages[ 0 ]->ID;
				$page_id_by_template[ $page_template ] = $page_id;
			} else {
				$page_id_by_template[ $page_template ] = false;
			}
		}

		return $page_id_by_template[ $page_template ];
	}
endif;

if ( ! function_exists( 'noo_get_page_link_by_template' ) ):
	function noo_get_page_link_by_template( $page_template ) {
		global $page_link_by_template;
		if ( empty( $page_link_by_template ) || ! isset( $page_link_by_template[ $page_template ] ) ) {
			$page_id = noo_get_page_id_by_template( $page_template );
			if ( ! empty( $page_id ) ) {
				$page_link_by_template[ $page_template ] = get_permalink( $page_id );
			} else {
				$page_link_by_template[ $page_template ] = home_url();
			}
		}

		return $page_link_by_template[ $page_template ];
	}
endif;

if ( ! function_exists( 'noo_current_url' ) ):
	function noo_current_url( $encoded = false ) {
		global $wp;
		$current_url = esc_url( add_query_arg( $_SERVER[ 'QUERY_STRING' ], '', home_url( $wp->request ) ) );
		if ( $encoded ) {
			return urlencode( $current_url );
		}

		return $current_url;
	}
endif;

if ( ! function_exists( 'noo_upload_dir_name' ) ):
	function noo_upload_dir_name() {
		return apply_filters( 'noo_upload_dir_name', 'noo_jobmonster' );
	}
endif;

if ( ! function_exists( 'noo_upload_dir' ) ):
	function noo_upload_dir() {
		$upload_dir = wp_upload_dir();

		return $upload_dir[ 'basedir' ] . '/' . noo_upload_dir_name();
	}
endif;

if ( ! function_exists( 'noo_upload_url' ) ):
	function noo_upload_url() {
		$upload_dir = wp_upload_dir();

		return $upload_dir[ 'baseurl' ] . '/' . noo_upload_dir_name();
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
			if ( wp_mkdir_p( $noo_upload_dir) ) {
				return $noo_upload_dir;
			}

			return false;
		}

		return $noo_upload_dir;
	}
endif;

/**
 * This function is original from Visual Composer. Redeclare it here so that it could be used for site without VC.
 */
if ( ! function_exists( 'noo_handler_shortcode_content' ) ):
	function noo_handler_shortcode_content( $content, $autop = false ) {
		if ( $autop ) {
			$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
		}

		return do_shortcode( shortcode_unautop( $content ) );
	}
endif;

if ( ! function_exists( '_wp_render_title_tag' ) ) {
	function noo_theme_slug_render_title() {
		?>
        <title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php
	}

	add_action( 'wp_head', 'noo_theme_slug_render_title' );
}

if ( ! function_exists( 'noo_mail' ) ) :
	function noo_mail( $to = '', $subject = '', $body = '', $headers = '', $key = '', $attachments = '' ) {

		if ( empty( $headers ) ) {
			$headers    = array();
			$from_name  = jm_et_get_setting( 'from_name', '' );
			$from_email = jm_et_get_setting( 'from_email', '' );

			if ( empty( $from_name ) ) {
				if ( is_multisite() ) {
					$from_name = $GLOBALS[ 'current_site' ]->site_name;
				} else {
					$from_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				}
			}

			if ( ! empty( $from_name ) && ! empty( $from_email ) ) {
				$headers[] = 'From: ' . $from_name . ' <' . strtolower( $from_email ) . '>';
			}
		}

		$headers = apply_filters( $key . '_header', apply_filters( 'noo_mail_header', $headers ) );

		if ( ! empty( $key ) ) {
			$subject = apply_filters( $key . '_subject', apply_filters( 'noo_mail_subject', $subject ) );
			$body    = apply_filters( $key . '_body', apply_filters( 'noo_mail_body', $body ) );
		}

		// RTL HTML email
		if ( is_rtl() ) {
			$body = '<div dir="rtl">' . $body . '</div>';
		}

		add_filter( 'wp_mail_content_type', 'noo_mail_set_html_content' );

		$result = wp_mail( $to, $subject, $body, $headers, $attachments );

		// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
		remove_filter( 'wp_mail_content_type', 'noo_mail_set_html_content' );

		return $result;
	}
endif;

if ( ! function_exists( 'noo_mail_set_html_content' ) ) :
	function noo_mail_set_html_content() {
		return 'text/html';
	}
endif;

if ( ! function_exists( 'noo_mail_do_not_reply' ) ) :
	function noo_mail_do_not_reply() {
		$sitename = strtolower( $_SERVER[ 'SERVER_NAME' ] );
		if ( substr( $sitename, 0, 4 ) === 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		return apply_filters( 'noo_mail_do_not_reply', 'noreply@' . $sitename );
	}
endif;

/* -------------------------------------------------------
 * Create functions noo_set_post_views
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_set_post_views' ) ) :

	function noo_set_post_views( $id ) {

		$key_meta = '_noo_views_count';
		// echo($id); die;
		$count = noo_get_post_meta( $id, $key_meta );
		// echo $count; die;
		if ( $count == '' ) :
			$count = 1;
		else :
			$count ++;
		endif;
		update_post_meta( $id, $key_meta, $count );
		// return $content;

	}

	// add_action( 'the_content', 'noo_set_post_views' );

endif;

/** ====== END noo_set_post_views ====== **/

/* -------------------------------------------------------
 * Create functions noo_get_post_views
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_get_post_views' ) ) :

	function noo_get_post_views( $id ) {
		$key_meta = '_noo_views_count';
		$count    = noo_get_post_meta( $id, $key_meta );
		if ( empty($count) ) :
			update_post_meta($id, $key_meta, 0);
			return 0;
		endif;

		return $count;
	}

endif;

/** ====== END noo_get_post_views ====== **/

/* -------------------------------------------------------
 * Create functions track_post_views
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_track_post_views' ) ) :

	function noo_track_post_views( $post_id = '' ) {

		if ( ! is_single() ) {
			return;
		}

		if ( empty ( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		}
		if ( get_post_status( $post_id ) !== 'publish' ) {
			return;
		}

		if ( is_singular( 'noo_job' ) ) {
			$name_cookie = 'noo_jobs_' . $post_id;
		}
		if ( is_singular( 'noo_resume' ) ) {
			$name_cookie = 'noo_resume_' . $post_id;
		}
		if ( is_singular( 'noo_company' ) ) {
			$name_cookie = 'noo_company_' . $post_id;
		}
		if ( isset( $name_cookie ) ) {
			if ( ! isset ( $_COOKIE[ $name_cookie ] ) ) {
				noo_set_post_views( $post_id );
			}
			setcookie( $name_cookie, $post_id, time() + ( 86400 * 3 ), "/" );
		}
	}

	add_action( 'wp_head', 'noo_track_post_views' );

endif;

/** ====== END track_post_views ====== **/

/* -------------------------------------------------------
 * Create functions noo_get_job_applications_count
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_get_job_applications_count' ) ) :

	function noo_get_job_applications_count( $job_id ) {
		$key_meta = '_noo_job_applications_count';
		$count    = noo_get_post_meta( $job_id, $key_meta );
		if ( $count === '' || $count === null ) :
			global $wpdb;
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'noo_application' AND post_parent = {$job_id}" );
			update_post_meta( $job_id, $key_meta, absint( $count ) );

			return $count;
		endif;

		return $count;
	}

endif;

/** ====== END noo_get_job_applications_count ====== **/

/* -------------------------------------------------------
 * Create functions track_applications_post
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_track_applications_post' ) ) :

	function noo_track_applications_post( $post_id = '', $post = null, $update = true ) {

		if ( $update || 'noo_application' !== $post->post_type ) {
			return;
		}

		$job_id = $post->post_parent;
		if ( ! empty( $job_id ) ) {
			global $wpdb;
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'noo_application' AND post_parent = {$job_id}" );
			update_post_meta( $job_id, '_noo_job_applications_count', absint( $count ) );
		}
	}

	add_action( 'wp_insert_post', 'noo_track_applications_post', 10, 3 );

endif;

/** ====== END track_applications_post ====== **/

/* -------------------------------------------------------
 * Create functions noo_caroufredsel_slider
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_caroufredsel_slider' ) ) :

	// function noo_caroufredsel_slider( $r, $id, $show = 'company', $max = 6 ) {
	function noo_caroufredsel_slider( $r, $options = array() ) {
		// Default config options.
		$defaults = array(
			'id'                => uniqid() . '_show_slider',
			'show'              => 'company',
			'style'             => 'style-1',
			'min'               => 1,
			'max'               => 5,
			'autoplay'          => 'false',
			'autoheight'          => 'true',
			'slider_speed'      => '800',
			'width'             => 180,
			'height'            => 'variable',
			'hidden_pagination' => 'false',
			'show_navigation' => 'false',
			'owl'               => '',
		);
		$options                  = wp_parse_args( $options, $defaults );
		if ( $options[ 'show' ] == 'testimonial' ) {
			$options[ 'width' ] = 767;
		}
		// -- Check query
		if ( $r->have_posts() ):
			wp_enqueue_script( 'vendor-carousel' );
			?>
			<div class="featured_slider <?php echo $options['style'] ?>">
			<?php if ( ! empty( $options[ 'title' ]) ): ?>
                <div class="testimonial-title">
                    <h3><?php echo  $options[ 'title' ]; ?></h3>
                </div>
			<?php endif;
			echo '<div id="slider_' . $options[ 'id' ] . '">';
			if ( $options[ 'style' ] == 'style-1' ) :
				while ( $r->have_posts() ): $r->the_post();
					global $post;
					if ( $options[ 'show' ] == 'company' ) :
						$logo_company = Noo_Company::get_company_logo( $post->ID );
						echo "<div class='bg_images'><a href='" . get_permalink( $post->ID ) . "' >{$logo_company}</a></div>";
                    elseif ( $options[ 'show' ] == 'testimonial' ) :
						$name = get_post_meta( get_the_ID(), '_noo_wp_post_name', true );
						$position = get_post_meta( get_the_ID(), '_noo_wp_post_position', true );
						$url      = get_post_meta( get_the_ID(), '_noo_wp_post_image', true );
						?>
                        <div class="box_testimonial">
                            <div class="box-content">
								<?php the_content(); ?>
                            </div>
                            <div class="icon"></div>
                            <div class="box-info">
                                <div class="box-info-image">
                                    <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                </div>
                                <div class="box-info-entry">
                                    <h4><?php echo $name; ?></h4>
                                    <h5><?php echo $position ?></h5>
                                </div>
                            </div>
                        </div>
						<?php
					endif;

				endwhile;
			elseif ( $options[ 'style' ] == 'style-3' ) :
				while ( $r->have_posts() ): $r->the_post();
					global $post;
				if ( $options[ 'show' ] == 'testimonial' ) :
						$name = get_post_meta( get_the_ID(), '_noo_wp_post_name', true );
						$position = get_post_meta( get_the_ID(), '_noo_wp_post_position', true );
						$url      = get_post_meta( get_the_ID(), '_noo_wp_post_image', true );
						?>
                            <div class="box_testimonial_single2">
                                <div class="box-info">
                                    <div class="box-info-image">
                                        <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                     </div>
                                </div>
                                <div class="box-content">
								    <?php the_content(); ?>
                                    <div class="box-info-entry">
                                        <h4><?php echo $name;?><span><?php echo ' - '.$position ?></span></h4>
                                    </div>
                                </div>
                             </div>
						<?php
					endif;

				endwhile;
            else :
				while ( $r->have_posts() ): $r->the_post();
					global $post;
					if ( $options[ 'show' ] == 'testimonial' ) :
						$name = get_post_meta( get_the_ID(), '_noo_wp_post_name', true );
						$position = get_post_meta( get_the_ID(), '_noo_wp_post_position', true );
						$url      = get_post_meta( get_the_ID(), '_noo_wp_post_image', true );
						?>
                        <div class="box_testimonial_single">
                            <div class="box-info">
                                <div class="box-info-image">
                                    <?php echo wp_get_attachment_image($url,'thumbnail-logo');?>
                                </div>
                                <div class="box-info-entry">
                                    <h4><?php echo $name; ?></h4>
                                    <h5><?php echo $position ?></h5>
                                </div>
                            </div>
                            <div class="box-content">
								<?php the_content(); ?>
                            </div>
                        </div>
						<?php
					endif;
				endwhile;
			endif;
			wp_reset_query();
			echo '</div>
	 			<div class="clearfix"></div>
	 		</div>';
				echo '<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$("#slider_' . $options[ 'id' ] . '").owlCarousel({
								items : ' . $options[ 'max' ] . ',
								itemsDesktop : false,
								itemsDesktopSmall: false,
                        		itemsTablet: false,
                        		itemsMobile: false,
                                navigation:' . $options[ 'show_navigation' ] . ',
								autoPlay 		: ' . ($options[ 'autoplay' ] === 'true' ? $options[ 'slider_speed' ] : $options[ 'autoplay' ]) . ',
								autoHeight:' . $options[ 'autoheight' ] . ',
								slideSpeed : 200,
								navigationText: ["", ""],
						
					});
				});
				</script>';
		endif;
	}

endif;

/** ====== END noo_caroufredsel_slider ====== **/

/* -------------------------------------------------------
 * Create functions check_view_application
 * ------------------------------------------------------- */

if ( ! function_exists( 'check_view_applied' ) ) :

	function check_view_applied() {
		if ( Noo_Member::is_employer() ) :
			// -- get id candidate
			$user = wp_get_current_user();
			// -- default meta
			$key_meta = '_check_view_applied';
			// get value in meta -> array
			$check_view = get_user_meta( $user, $key_meta, true ) ? (array) get_user_meta( $user, $key_meta,
				true ) : array();

			$id_applications = array( $_POST[ 'application_id' ] );
			$arr_value       = array_merge( $check_view, $id_applications );

			if ( ! in_array( $_POST[ 'application_id' ], $check_view ) ):
				update_user_meta( $user, $key_meta, $arr_value );
			endif;

		endif;
	}

	add_action( 'wp_ajax_nopriv_check_view_applied', 'check_view_applied' );
	add_action( 'wp_ajax_check_view_applied', 'check_view_applied' );
endif;

/** ====== END check_view_application ====== **/

/* -------------------------------------------------------
 * Create functions unseen_applications_number
 * ------------------------------------------------------- */

if ( ! function_exists( 'unseen_applications_number' ) ) :

	function unseen_applications_number( $html = true ) {
		global $wpdb;
		$count_view = 0;

		if ( Noo_Member::is_employer() ) {
			$count_view = noo_employer_unseen_application_count();
		} elseif ( Noo_Member::is_candidate() ) {
			$user              = wp_get_current_user();
			$total_applied     = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} 
				INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE post_type = 'noo_application' AND (post_status = 'publish' OR post_status = 'rejected')
					AND {$wpdb->postmeta}.meta_key = '_candidate_email'
					AND {$wpdb->postmeta}.meta_value = '{$user->user_email}'" );
			$check_view_applied= get_user_meta( $user->ID, '_check_view_applied', true ) ;
			if(is_array($check_view_applied)){
			    $view_applications = count($check_view_applied);
			    $count_view = $total_applied - $view_applications;
			}
		}

		$count_view = apply_filters( 'noo-unseen-applications-number', $count_view );

		if ( $count_view > 0 ) {
			return $html ? '<span class="badge">' . $count_view . '</span>' : absint( $count_view );
		} else {
			return $html ? '' : 0;
		}
	}

endif;

//Process Unseen Application

function noo_employer_unseen_application_count() {

	$key_meta = '_noo_applications_unseen_count';
	$count    = get_user_meta( get_current_user_id(), $key_meta, true );
	if ( $count === '' || $count === null ) :
		$count = noo_employer_unseen_application_updating_count();
	endif;

	return apply_filters('noo_employer_unseen_application_count', $count);
}

function noo_employer_unseen_application_updating_count() {
	global $wpdb;
	$key_meta             = '_noo_applications_unseen_count';
	$user_id              = get_current_user_id();
	$company_id           = jm_get_employer_company( $user_id );
	$job_ids 			  = Noo_Company::get_company_jobs($company_id, array(), -1, array('publish','pending','expired') );
	$pending_applications = 0;
	if ( ! empty( $job_ids ) ) {
		$job_ids              = array_merge( $job_ids, array( 0 ) );
		$job_ids_where        = implode( ',', $job_ids );
		$pending_applications = $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'noo_application' AND post_parent IN ( {$job_ids_where} ) AND post_status = 'pending'" );
	}
	update_user_meta( $user_id, $key_meta, absint( $pending_applications ) );

	return $pending_applications;
}

add_action( 'transition_post_status', 'noo_employer_unseen_application_updating_count' );

/** ====== END unseen_applications_number ====== **/

/* -------------------------------------------------------
 * Create functions user_notifications_number
 * ------------------------------------------------------- */

if ( ! function_exists( 'user_notifications_number' ) ) :

	function user_notifications_number( $html = true ) {
		$count_view = unseen_applications_number( false );
		$count_view = apply_filters( 'noo-user-notifications-number', $count_view );
		
		if ( $count_view > 0 ) {
			return $html ? '<span class="badge">' . $count_view . '</span>' : $count_view;
		} else {
			return $html ? '' : 0;
		}
	}

endif;

/** ====== END user_notifications_number ====== **/

/* -------------------------------------------------------
 * Create functions noo_auto_create_order_free_package
 * ------------------------------------------------------- */

if ( ! function_exists( 'noo_auto_create_order_free_package' ) ) :

	function noo_auto_create_order_free_package() {
		check_ajax_referer( 'noo-free-package', 'security' );

		if ( ! is_user_logged_in() ) {
			wp_die();
		}

		$product_id    = absint( $_POST[ 'package_id' ] );
		$user_id       = absint( $_POST[ 'user_id' ] );
		$login_user_id = get_current_user_id();
		if ( $user_id != $login_user_id ) {
			wp_die();
		}

		$user_info    = get_userdata( $user_id );
		$new_order_ID = create_new_order( $user_info->display_name, $user_id );

		// Add product to this order.

		$product = wc_get_product( $product_id );
		wc_get_order( $new_order_ID )->add_product( $product, 1 );

		$order = new WC_Order( $new_order_ID );

		if ( $user_id != $order->get_customer_id() ) {
			wp_die();
		}

		$order->update_status( 'completed' );

		$product = wc_get_product( $product_id );

		if ( $product && $product->get_price() == 0 && is_user_logged_in() ) {

			if ( $product->is_type( 'job_package' ) ) {
				$package_interval      = absint( $product->get_package_interval() );
				$package_interval_unit = $product->get_package_interval_unit();

				$package_data = array(
					'product_id'            => $product->get_id(),
					'order_id'              => $order_id,
					'created'               => current_time( 'mysql' ),
					'package_interval'      => $package_interval,
					'package_interval_unit' => $package_interval_unit,
					'job_duration'          => absint( $product->get_job_display_duration() ),
					'job_limit'             => absint( $product->get_post_job_limit() ),
					'job_featured'          => absint( $product->get_job_feature_limit() ),
					'job_refresh'           => absint( $product->get_job_refresh_limit() ),
					'download_resume_limit' => absint( $product->get_download_resume_limit()),
					'company_featured'      => $product->get_company_featured(),
				);

				$package_data = apply_filters( 'jm_job_package_user_data', $package_data, $product );

				if ( ! empty( $package_interval ) ) {
					$expired                   = strtotime( "+{$package_interval} {$package_interval_unit}" );
					$package_data[ 'expired' ] = $expired;
					Noo_Job_Package::set_expired_package_schedule( $user_id, $package_data );
				}

				update_user_meta( $user_id, '_free_package_bought', 1 );
				update_user_meta( $user_id, '_job_package', $package_data );
				update_user_meta( $user_id, '_job_added', 0 );
				update_user_meta( $user_id, '_job_featured', 0 );
				update_user_meta( $user_id, '_job_refresh', 0 );

				do_action( 'jm_job_package_order_completed', $product, $user_id );
			} elseif ( $product->is_type( 'resume_package' ) ) {
				$package_interval      = absint( $product->get_package_interval() );
				$package_interval_unit = $product->get_package_interval_unit();

				$package_data = array(
					'product_id'            => $product->get_id(),
					'created'               => current_time( 'mysql' ),
					'package_interval'      => $package_interval,
					'package_interval_unit' => $package_interval_unit,
					'resume_limit'          => absint( $product->get_post_resume_limit() ),
					'resume_refresh'        => absint( $product->get_resume_refresh_limit() ),
					'resume_featured'  		=> absint($product ->get_resume_feature_limit()),
				);

				$package_data = apply_filters( 'jm_resume_package_user_data', $package_data, $product );

				if ( ! empty( $package_interval ) ) {
					$expired                   = strtotime( "+{$package_interval} {$package_interval_unit}" );
					$package_data[ 'expired' ] = $expired;
					Noo_Resume_Package::set_expired_package_schedule( $user_id, $package_data );
				}

				update_user_meta( $user_id, '_free_resume_package_bought', 1 );
				update_user_meta( $user_id, '_resume_package', $package_data );
				update_user_meta( $user_id, '_resume_added', 0 );
				update_user_meta( $user_id, '_resume_refresh', 0 );


				do_action( 'jm_resume_package_order_completed', $product, $user_id );
			}
		}

		wp_die();
	}

	add_action( 'wp_ajax_auto_create_order', 'noo_auto_create_order_free_package' );

endif;

/** ====== END noo_auto_create_order_free_package ====== **/

/**
 * Get Location Long Lat from Addresss.
 */
if ( ! function_exists( 'noo_address_to_lng_lat' ) ):
	function noo_address_to_lng_lat( $address ) {

		$geo = file_get_contents( 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&sensor=false' );
		$geo = json_decode( $geo, true );

		if ( $geo[ 'status' ] == 'OK' ) {
			$location[ 'lat' ]  = $geo[ 'results' ][ 0 ][ 'geometry' ][ 'location' ][ 'lat' ];
			$location[ 'long' ] = $geo[ 'results' ][ 0 ][ 'geometry' ][ 'location' ][ 'lng' ];

			return $location;
		}

		return '';
	}
endif;

if ( ! function_exists( 'noo_wp_editor' ) ):
	function noo_wp_editor( $content, $editor_id, $editor_name = '', $media_buttons = false) {
		$configs = array(
			'editor_class'  => 'noo-editor',
			'media_buttons' => $media_buttons,
		);
		if ( ! empty( $editor_name ) ) {
			$configs[ 'textarea_name' ] = $editor_name;
		}

		$configs = apply_filters( 'noo_editor_config', $configs, $editor_id, $editor_name);

		return wp_editor( $content, $editor_id, $configs );
	}
endif;

if ( ! function_exists( 'noo_form_nonce' ) ):
	function noo_form_nonce( $action ) {
		$nonce = wp_create_nonce( $action );
		echo '<input type="hidden" id="_wpnonce" name="_wpnonce" value="' . $nonce . '">';
	}
endif;

function noo_company_job_count( $company_id ) {
	$key_meta = '_noo_job_count';
	$count    = noo_get_post_meta( $company_id, $key_meta, '' );
	// if ( empty( $count ) ) {
		$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
		$count = Noo_Company::count_jobs( $company_id, $status );
		update_post_meta( $company_id, $key_meta, $count );
	// }

	return $count;
}

function noo_update_company_job_count( $new_status, $old_status, $post ) {
	$company_id = '';
	if ( $post->post_type == 'noo_job' ) {
		$company_id = jm_get_job_company( $post->ID );
	} elseif ( $post->post_type == 'noo_company' ) {
		$company_id = $post->ID;
	}
	if ( ! empty( $company_id ) ) {
		$key_meta = '_noo_job_count';
		$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
		$count    = Noo_Company::count_jobs( $company_id, $status );
		update_post_meta( $company_id, $key_meta, $count );
	}
}

add_action( 'transition_post_status', 'noo_update_company_job_count', 10, 3 );

if ( ! function_exists( 'noo_follow_status' ) ) :

	function noo_follow_status( $company_id, $user_id ) {

		if ( empty( $user_id ) || empty( $company_id ) ) {
			return esc_html__( 'Follow', 'noo' );
		}

		$list_company_follow = get_user_meta( $user_id, 'list_company_follow', true );

		if ( empty( $list_company_follow ) || ! is_array( $list_company_follow ) ) {
			$list_company_follow = array();
		}

		if ( in_array( $company_id, $list_company_follow ) ) {
			return esc_html__( 'Unfollow', 'noo' );
		}

		return esc_html__( 'Follow', 'noo' );
	}

endif;

if ( ! function_exists( 'noo_total_follow' ) ) :

	function noo_total_follow( $company_id ) {

		if ( empty( $company_id ) ) {
			return false;
		}

		$list_follow = get_post_meta( $company_id, 'list_user_follow', true );
		if ( empty( $list_follow ) || ! is_array( $list_follow ) ) {
			$list_follow = array();
		}

		return count( $list_follow );
	}

endif;

if ( ! function_exists( 'noo_follow_company' ) ) :

	function noo_follow_company() {

		$response = array();
		if ( ! empty( $_POST[ 'company_id' ] ) && ! empty( $_POST[ 'user_id' ] ) ) {


			$company_id  = absint( $_POST[ 'company_id' ] );
			$user_id     = absint( $_POST[ 'user_id' ] );

			if(Noo_Member::is_employer($user_id)){
			    $company_user_id = Noo_Company::get_employer_id($company_id);

			    if($company_user_id == $user_id){
			        $response[ 'status' ]  = 'error';
			        $response[ 'message' ] = esc_html__( 'You can not follow yourself', 'noo' );
			        wp_send_json( $response );
			    }
			}

			$list_follow = get_post_meta( $company_id, 'list_user_follow', true );
			if ( empty( $list_follow ) || ! is_array( $list_follow ) ) {
				$list_follow = array();
			}

			$list_company_follow = get_user_meta( $user_id, 'list_company_follow', true );
			if ( empty( $list_company_follow ) || ! is_array( $list_company_follow ) ) {
				$list_company_follow = array();
			}

			if ( in_array( $user_id, $list_follow ) ) {
				$item = array_search( $user_id, $list_follow );
				unset( $list_follow[ $item ] );

				update_post_meta( $company_id, 'list_user_follow', $list_follow );

				$item_company = array_search( $company_id, $list_company_follow );
				unset( $list_company_follow[ $item_company ] );

				update_user_meta( $user_id, 'list_company_follow', $list_company_follow );

				$response[ 'status' ]  = 'success';
				$response[ 'message' ] = esc_html__( 'You are unfollow this company', 'noo' );
				$response[ 'label' ]   = esc_html__( 'Follow', 'noo' );
				$response[ 'total' ]   = count( $list_follow );

			} else {
				$list_follow = array_merge( $list_follow, array( $user_id ) );
				$list_follow = array_unique( $list_follow );

				update_post_meta( $company_id, 'list_user_follow', $list_follow );

				$list_company_follow = array_merge( $list_company_follow, array( $company_id ) );
				$list_company_follow = array_unique( $list_company_follow );

				update_user_meta( $user_id, 'list_company_follow', $list_company_follow );
				$response[ 'status' ]  = 'success';
				$response[ 'message' ] = esc_html__( 'You are following this company', 'noo' );
				$response[ 'label' ]   = esc_html__( 'Unfollow', 'noo' );
				$response[ 'total' ]   = count( $list_follow );
			}
		} else {
			$response['need_login'] = 1;
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = esc_html__( 'Please login to follow company', 'noo' );
		}

		wp_send_json( $response );
	}

	add_action( 'wp_ajax_noo_follow_company', 'noo_follow_company' );
	add_action( 'wp_ajax_nopriv_noo_follow_company', 'noo_follow_company' );

endif;
if (! function_exists('noo_can_follow_company')):
    function noo_can_follow_company(){
	    $can_follow_setting = jm_get_action_control('follow');
	    $can_follow_company = false;
	    
        switch( $can_follow_setting ) {
            case 'public':
                $can_follow_company = true;
                break;
            case 'candidate':
                $can_follow_company = Noo_Member::is_candidate();
                break;
            case 'employer':
                $can_follow_company = Noo_Member::is_employer();
                break;
            case 'disable':
                return false;
                break;
         }

        if( 'administrator' == Noo_Member::get_user_role(get_current_user_id()) || !is_user_logged_in()) {
	      	return true;
	    }
        
        return $can_follow_company;
    }
endif;
if(! function_exists('noo_can_shortlist_candidate')):
    function noo_can_shortlist_candidate(){
        $can_shortlist_setting=jm_get_action_control('shortlist');
        $can_shortlist_candidate = false;

	    switch(  $can_shortlist_setting ) {
	        case 'public':
	           $can_shortlist_candidate = true;
	            break;
	        case 'candidate':
	            $can_shortlist_candidate = Noo_Member::is_candidate();
	            break;
	        case 'employer':
	            $can_shortlist_candidate = Noo_Member::is_employer();
	            break;
	        case 'disable':
	           	return  false;
	 	}

        if( 'administrator' == Noo_Member::get_user_role(get_current_user_id()) || !is_user_logged_in()) {
          return true;
        }

        return $can_shortlist_candidate;
    }
endif;
if(! function_exists('noo_resume_review')):
    function noo_resume_review(){
    	$response = array();
    	if(isset($_POST[ 'email_rehot' ]) && !empty($_POST[ 'email_rehot' ])){
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = '<span class="error-response">' . esc_html__('You can not perform this action.', 'noo') . '</span>';
			wp_send_json( $response );
			die;
		}
		if(empty($_POST['user_name'])){
			$response['status']  = 'error';
			$response['message'] = '<span class="error-response">' . esc_html__('Please enter your name.', 'noo') . '</span>';
			wp_send_json($response);
			die;
		}
		$email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
		if(!is_email($email)){
			$response['status']  = 'error';
			$response['message'] = '<span class="error-response">' . esc_html__('Please enter your email.', 'noo') . '</span>';
			wp_send_json($response);
			die;
		}
    	if(empty($_POST[ 'message' ])){
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = '<span class="error-response">' . esc_html__('Please enter your review.', 'noo') . '</span>';
			wp_send_json( $response );
			die;
		}
		if (isset($_POST['g-recaptcha-response']) && !noo_recaptcha_verification()) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please complete the Recaptcha challenge.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
		if ( isset( $_POST[ 'resume_id' ] ) && isset( $_POST[ 'user_name' ] ) && isset( $_POST[ 'user_email' ] ) && isset( $_POST[ 'score-ed' ] ) && isset( $_POST[ 'score-wx' ] ) && isset( $_POST[ 'score-sok' ] ) ) {
			$resume_id = absint( $_POST[ 'resume_id' ] );
			$user_name  = sanitize_text_field( $_POST[ 'user_name' ] );
			$user_email = sanitize_text_field( $_POST[ 'user_email' ] );
			$subject    = sanitize_text_field( $_POST[ 'subject' ] );
			$message    = sanitize_text_field( $_POST[ 'message' ] );
			//$rating     = floatval( $_POST[ 'score' ] );
			$rating1     = floatval( $_POST[ 'score-ed' ] );
			$rating2     = floatval( $_POST[ 'score-wx' ] );
			$rating3     = floatval( $_POST[ 'score-sok' ] );

			$total_review = (int)noo_get_post_meta( $resume_id, 'total_review', 0 );
			$total_score = (float)noo_get_post_meta( $resume_id, 'total_score', 0 );
			$rating_total= (($rating1+$rating2+$rating3)/3);

			$time = current_time( 'mysql' );


			$data = array(
				'comment_post_ID'      => $resume_id,
				'comment_author'       => esc_html( $user_name ),
				'comment_author_email' => esc_attr( $user_email ),
				'comment_content'      => esc_html( $message ),
				'comment_date'         => $time,
				'comment_approved'     => apply_filters('noo_resume_review_comment_approved', 1)
			);
			
			$comment_id = wp_insert_comment( $data, true);

			if ( is_wp_error( $comment_id ) ) {
				$response['status'] = 'error';
				$response['message'] = '<span class="error-response">' . $comment_id . '</span>';
				wp_send_json( $response );
				die;
			}
			
			update_comment_meta( $comment_id, 'user_rating1', $rating1 );
			update_comment_meta( $comment_id, 'user_rating2', $rating2 );
			update_comment_meta( $comment_id, 'user_rating3', $rating3);
			update_comment_meta( $comment_id, 'subject', $subject );

			$education           = esc_html__( 'Education', 'noo' );
			$work_experience     = esc_html__( 'Work Experience', 'noo' );
		    $summary_of_skill    = esc_html__( 'Summary of skills', 'noo' );



			$response[ 'status' ]  = 'success';
			$response[ 'message' ] = esc_html__( 'Comment success!', 'noo' );

			$response[ 'status' ]  = 'success';
			$response[ 'message' ] = esc_html__( 'You are following this company', 'noo' );
			$response[ 'html' ]    = '<li class="comment-item">
                <div class="comment-head">
                    <h4 class="subject">
                        ' . $subject . '
                    </h4>
                    <time datetime="' . get_comment_date( 'd-m-Y', $comment_id ) . '">
                        ' . get_comment_date( 'M d, Y', $comment_id ) . '
                    </time>
                </div>

                <div class="comment-info">
               	   <span class="er-rate-count">'.noo_box_rating( $rating_total, true, false ).'</span>
               	   <span class="noo-box-reviewed">
               	   	  <span class="reviewed-box-icon"><i class="fa fa-caret-down" data-unicode="f0d7"></i></span>
	               	   <div class="noo-review-voted">
		                  <div class="line-vote"><span>' .$education .'</span>'.noo_box_rating( $rating1, true, false ) .'</div>
		                   <div class="line-vote"><span>' .$work_experience.'</span>'.noo_box_rating( $rating2, true, false ).'</div>
		                   <div class="line-vote"><span>' .$summary_of_skill.'</span>'. noo_box_rating( $rating3, true, false ). '</div>
		               
		               
		                </div>
		            </span>
		            <span class="user-name">' . $user_name . '</span>
                </div>
                <p class="comment-content">
                    ' . $message . '
                </p>
            </li>';

			update_post_meta( $resume_id, 'total_review', $total_review+1 );
			update_post_meta( $resume_id, 'total_score', $total_score);

		} else {
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = esc_html__( 'Do not support this action', 'noo' );
		}

		wp_send_json( $response );
	}

	add_action( 'wp_ajax_noo_resume_review', 'noo_resume_review' );
	add_action( 'wp_ajax_nopriv_noo_resume_review', 'noo_resume_review' );
endif;

if ( ! function_exists( 'noo_company_review' ) ) :

	function noo_company_review() {

		$response = array();
		if(isset($_POST[ 'email_rehot' ]) && !empty($_POST[ 'email_rehot' ])){
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = '<span class="error-response">' . esc_html__('You can not perform this action.', 'noo') . '</span>';
			wp_send_json( $response );
			die;
		}
		if(empty($_POST['user_name'])){
			$response['status']  = 'error';
			$response['message'] = '<span class="error-response">' . esc_html__('Please enter your name.', 'noo') . '</span>';
			wp_send_json($response);
			die;
		}
		$email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
		if(!is_email($email)){
			$response['status']  = 'error';
			$response['message'] = '<span class="error-response">' . esc_html__('Please enter your email.', 'noo') . '</span>';
			wp_send_json($response);
			die;
		}
		if(empty($_POST[ 'message' ])){
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = '<span class="error-response">' . esc_html__('Please enter your review.', 'noo') . '</span>';
			wp_send_json( $response );
			die;
		}
		if (isset($_POST['g-recaptcha-response']) && !noo_recaptcha_verification()) {
            $response['status'] = 'error';
            $response['message'] = '<span class="error-response">' . esc_html__('Please complete the Recaptcha challenge.', 'noo') . '</span>';
            wp_send_json( $response );
            die;
        }
		if ( isset( $_POST[ 'company_id' ] ) && isset( $_POST[ 'user_name' ] ) && isset( $_POST[ 'user_email' ] ) && isset( $_POST[ 'score-wlb' ] ) && isset( $_POST[ 'score-cb' ] ) && isset( $_POST[ 'score-sm' ] ) && isset( $_POST[ 'score-cv' ] ) ) {
			$company_id = absint( $_POST[ 'company_id' ] );
			$user_name  = sanitize_text_field( $_POST[ 'user_name' ] );
			$user_email = sanitize_text_field( $_POST[ 'user_email' ] );
			$subject    = sanitize_text_field( $_POST[ 'subject' ] );
			$message    = sanitize_text_field( $_POST[ 'message' ] );
			//$rating     = floatval( $_POST[ 'score' ] );
			$rating1     = floatval( $_POST[ 'score-wlb' ] );
			$rating2     = floatval( $_POST[ 'score-cb' ] );
			$rating3     = floatval( $_POST[ 'score-sm' ] );
			$rating4     = floatval( $_POST[ 'score-cv' ] );

			$total_review = (int)noo_get_post_meta( $company_id, 'total_review', 0 );
			$total_score = (float)noo_get_post_meta( $company_id, 'total_score', 0 );
			$rating_total= (($rating1+$rating2+$rating3+$rating4)/4);

			$time = current_time( 'mysql' );
			

			$data = array(
				'comment_post_ID'      => $company_id,
				'comment_author'       => esc_html( $user_name ),
				'comment_author_email' => esc_attr( $user_email ),
				'comment_content'      => esc_html( $message ),
				'comment_date'         => $time,
				'comment_approved'     => apply_filters('noo_company_review_comment_approved', 1)
			);

			$comment_id = wp_insert_comment( $data );


			update_comment_meta( $comment_id, 'user_rating1', $rating1 );
			update_comment_meta( $comment_id, 'user_rating2', $rating2 );	
			update_comment_meta( $comment_id, 'user_rating3', $rating3);
			update_comment_meta( $comment_id, 'user_rating4', $rating4 );
			update_comment_meta( $comment_id, 'subject', $subject );

			$work_life_balance = esc_html__( 'Work/Life Balance', 'noo' );
			$comp_benefits     = esc_html__( 'Comp & Benefits', 'noo' );
		    $senior_managent   = esc_html__( 'Senior Management', 'noo' );
		    $culture_value     = esc_html__( 'Culture & Value', 'noo' );
		   
		   

			$response[ 'status' ]  = 'success';
			$response[ 'message' ] = esc_html__( 'Comment success!', 'noo' );

			$response[ 'status' ]  = 'success';
			$response[ 'message' ] = esc_html__( 'You are following this company', 'noo' );
			$response[ 'html' ]    = '<li class="comment-item">
                <div class="comment-head">
                    <h4 class="subject">
                        ' . $subject . '
                    </h4>
                    <time datetime="' . get_comment_date( 'd-m-Y', $comment_id ) . '">
                        ' . get_comment_date( 'M d, Y', $comment_id ) . '
                    </time>
                </div>

                <div class="comment-info">
               	   <span class="er-rate-count">'.noo_box_rating( $rating_total, true, false ).'</span>
               	   <span class="noo-box-reviewed">
               	   	  <span class="reviewed-box-icon"><i class="fa fa-caret-down" data-unicode="f0d7"></i></span>
	               	   <div class="noo-review-voted">
		                  <div class="line-vote"><span>' .$work_life_balance.'</span>'.noo_box_rating( $rating1, true, false ) .'</div>
		                   <div class="line-vote"><span>' .$comp_benefits.'</span>'.noo_box_rating( $rating2, true, false ).'</div>
		                   <div class="line-vote"><span>' .$senior_managent.'</span>'. noo_box_rating( $rating3, true, false ). '</div>
		                   <div class="line-vote"><span>'.$culture_value.'</span>'.noo_box_rating( $rating4, true, false ). '</div></span>
		                   
		                </div>
		            </span>
		            <span class="user-name">' . $user_name . '</span>
                </div>
                <p class="comment-content">
                    ' . $message . '
                </p>
            </li>';

			update_post_meta( $company_id, 'total_review', $total_review+1 );
			update_post_meta( $company_id, 'total_score', $total_score );

		} else {
			$response[ 'status' ]  = 'error';
			$response[ 'message' ] = esc_html__( 'Do not support this action', 'noo' );
		}

		wp_send_json( $response );
	}

	add_action( 'wp_ajax_noo_company_review', 'noo_company_review' );
	add_action( 'wp_ajax_nopriv_noo_company_review', 'noo_company_review' );

endif;

if ( ! function_exists( 'noo_box_rating' ) ) :

	function noo_box_rating( $star = '5', $realonly = false, $show = true, $name = '' ) {
		$name = !empty($name) ? 'score-'.$name : 'score';
		$html = '<span data-score-name="'.$name.'" class="noo-rating" data-readonly="' . $realonly . '" data-score="' . $star . '"></span>';

		if ( $show ) {
			echo $html;
		} else {
			return $html;
		}
	}

endif;

if ( ! function_exists( 'noo_get_company_total_job' ) ) :

	function noo_get_company_total_job( $company_id ) {
		if ( empty( $company_id ) ) {
			return 0;
		}
		$status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
		$job_ids = Noo_Company::get_company_jobs( $company_id, array(), - 1, $status );
		$args    = array(
			'paged'       => - 1,
			'post_type'   => 'noo_job',
			'post__in'    => array_merge( $job_ids, array( 0 ) ),
			'post_status' => $status,
		);

		$r = new WP_Query( $args );

		return $r->found_posts;
	}

endif;

if ( ! function_exists( 'noo_get_company_address' ) ) :

	function noo_get_company_address( $company_id ) {
		if ( empty( $company_id ) ) {
			return false;
		}

		$term_id = get_post_meta( $company_id, '_address', true );

		$term = get_term( $term_id, 'job_location' );

		if ( is_wp_error( $term ) || empty( $term )) {
			return false;
		}

		return $term->name;

	}

endif;

if ( !function_exists( 'noo_get_company_all_reviews' ) ) {
	function noo_get_company_all_reviews($company_id){

	    $reviews = get_comments(array(
            'post_id' => $company_id,
            'fields' => 'ids',
        ));

	    return $reviews;
    }
}

if ( ! function_exists( 'noo_get_total_review' ) ) :
    
    function noo_get_total_review( $company_id = '' ) {

        if ( empty( $company_id ) ) {
            return 0;
        }

        return (int) count(noo_get_company_all_reviews($company_id));
        
    }
    
endif;

if ( !function_exists( 'noo_get_review_item_point' ) ) {
	function noo_get_review_item_point($comment_id){
        $user_rating1 = (float) get_comment_meta($comment_id, 'user_rating1', true);
        $user_rating2 = (float) get_comment_meta($comment_id, 'user_rating2', true);
        $user_rating3 = (float) get_comment_meta($comment_id, 'user_rating3', true);
        $user_rating4 = (float) get_comment_meta($comment_id, 'user_rating4', true);
        $total_rating = (float)(($user_rating1 + $user_rating2 + $user_rating3 + $user_rating4) / 4);

        return $total_rating;
    }
}

if ( ! function_exists( 'noo_get_total_point_review' ) ) :

    function noo_get_total_point_review( $company_id = '' ) {

        if ( empty( $company_id ) ) {
            return 5;
        }

        $total_review = (int)noo_get_post_meta( $company_id, 'total_review', 0 );
        if ( empty( $total_review ) ) {
            return 5;
        }

        $reviews = noo_get_company_all_reviews($company_id);

        $total_arr = array();

        foreach ($reviews as $review_id){
            $item_point = noo_get_review_item_point($review_id);
            $total_arr[] = $item_point;
        }
        $total_point = array_sum($total_arr);
        $total = (count($total_arr)>0)? $total_point / count($total_arr): 5;
        return  round($total, 2);

    }

endif;
if ( !function_exists( 'noo_get_review_resume_item_point' ) ) {
	function noo_get_review_resume_item_point($comment_id){
        $user_rating1 = (float) get_comment_meta($comment_id, 'user_rating1', true);
        $user_rating2 = (float) get_comment_meta($comment_id, 'user_rating2', true);
        $user_rating3 = (float) get_comment_meta($comment_id, 'user_rating3', true);
        $total_rating = (float)(($user_rating1 + $user_rating2 + $user_rating3) / 3);

        return $total_rating;
    }
}
if(!function_exists('noo_get_total_point_review_resume')):
    function noo_get_total_point_review_resume($resume_id = ''){
        if(empty($resume_id)){
            return 5;
        }
        $total_review = (int)noo_get_post_meta($resume_id,'total_review',0);
            if(empty($total_review)){
                return 5;
            }
         $reviews = get_comments(array(
            'post_id' => $resume_id,
            'fields' => 'ids',
        ));
            $total_arr = array();
            foreach ($reviews as $review_id){
            $item_point = noo_get_review_resume_item_point($review_id);
            $total_arr[] = $item_point;
        }

        $total_point = array_sum($total_arr);
        $total = (count($total_arr)>0)? $total_point / count($total_arr): 0;
        return  round($total, 2);
    }
endif;

if(!function_exists('noo_get_resume_tax')):
    function noo_get_resume_tax($tax = 'job_category'){
    	$meta_key = '_'. $tax;
        $args = array(
            'post_type'      => 'noo_resume',
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'pending', 'pending_payment' ),
            'author'         => get_current_user_id(),
        );
        $posts = get_posts($args);
        $tax_rs =array();
        foreach ($posts as $post){
            $item = noo_get_post_meta($post->ID, $meta_key);
            $tax_rs = array_merge($tax_rs,  noo_json_decode($item));
        }

        $tax_rs = array_unique($tax_rs);
        return $tax_rs;
    }
endif;
if(!function_exists('noo_get_suggest_tax')):
    function noo_get_suggest_tax($tax='job_category'){
        $args=array(
                'post_type' 	=> 'noo_job',
                'post_per_page'	=> -1,
                'post_status' 	=> array( 'publish', 'pending', 'pending_payment', 'expired', 'inactive' ,'draft'),
                'author'     	=> get_current_user_id(),
        );
        $posts = get_posts($args);
        $tax_rs=array();
        foreach ($posts as $post){
            $terms = get_the_terms($post->ID, $tax);
            if(is_array($terms)){
                 $term_id= wp_list_pluck($terms,'term_id');
	             if($term_id){
	             	$tax_rs=array_merge($tax_rs,$term_id);
	             }
            }
        }
        $tax_rs = array_unique($tax_rs);
        return $tax_rs;
    }
endif;
if(!function_exists('noo_get_resume_suggest_id')):
    function noo_get_resume_suggest_id(){
        $cats = noo_get_suggest_tax('job_category');
        $locations = noo_get_suggest_tax('job_location');
        $args = array(
            'post_type' => 'noo_resume',
            'post_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'publish',
        );
        $resumes = new WP_Query($args);

        $ids = array();

        if (isset($resumes->posts) && is_array($resumes->posts)) {
            foreach ($resumes->posts as $post_id) {
                $job_category = noo_get_post_meta($post_id, '_job_category', '');
                $job_location = noo_get_post_meta($post_id, '_job_location', '');
                $job_category = is_array($job_category) ? $job_category : json_decode($job_category);
                $job_location = is_array($job_location) ? $job_location : json_decode($job_location);

                if(!empty($job_location) && !empty($job_category)){
                	$l = !empty($locations) ? array_intersect((array) $locations, $job_location) : '';
                	$c = !empty($cats) ? array_intersect((array)$cats, $job_category) : '';
	                if ((is_array($l) && count($l) > 0) && (is_array($c) && count($c) > 0)) {
	                    $ids[] = $post_id;
	                }
	            }
            }
        }
        return $ids;

    }
endif;
if(!function_exists('noo_get_resume_suggest_count')):
    function noo_get_resume_suggest_count($html = true){
        $count=count(noo_get_resume_suggest_id());
        if ( $count > 0 ) {
			return $html ? '<span class="badge">' . $count . '</span>' : absint( $count);
		} else {
			return $html ? '' : 0;
		}

    }
endif;
if ( ! function_exists( 'noo_get_total_resume_category' ) ) :
    function noo_get_total_resume_category( $term_id = '' ) {
    
        if ( empty( $term_id ) )  {
        return 0;
        }

        $args = array(
            'post_type' => 'noo_resume',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_job_category',
                    'value' => $term_id,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => '_viewable',
                    'value' => 'yes',
                ),
            ),
        );
        $query = new WP_Query( $args );
        return $query->found_posts;

    }
    
endif;

if ( ! function_exists( 'noo_get_user_permission' ) ) :
function noo_get_user_permission( $user_id = null ) {
	$user_id = ! empty( $user_id ) ? $user_id : get_current_user_id();
	if(Noo_Member::get_user_role($user_id)=='administrator'){
	    return true;
	}
	$per     = 'public';
	if ( Noo_Member::is_employer( $user_id ) ) {
		$per     = 'employer';
		$package = get_user_meta($user_id, '_job_package', true);
		if ( !empty( $package ) ) {
			$per = 'employer_with_package';
		}
		 if(is_singular('noo_resume')&&(!empty($package))){
	        return true;
	    }
	    if(is_singular('noo_job')){
		    $job = get_post(get_the_ID());
		    $author = $job->post_author;
		    if($author==$user_id){
		        return true;
		    }
	    }
	} elseif ( Noo_Member::is_candidate( $user_id ) ) {
	    if ( is_singular( 'noo_resume' ) ) {
		$resume = get_post( get_the_ID() );
		$id     = $resume->post_author;
            if($id==$user_id){
                return true;
            }
	    }
		$per     = 'candidate';
		$package = get_user_meta($user_id, '_resume_package', true);
		if ( !empty( $package ) ) {
			$per = 'candidate_with_package';
		}
	}
	if(isset($_GET['action'])){
	    if($_GET['action'] == 'resume_preview'){
	        $resume_id = isset($_GET['resume_id']) ? esc_attr($_GET['resume_id']) : 0;
	        $resume = get_post($resume_id );
		    $id     = $resume->post_author;
		    return $id == $user_id;
	    }elseif($_GET['action'] == 'preview_job'){
	        $job_id = isset($_GET['job_id']) ? esc_attr($_GET['job_id']) : 0;
	        $job = get_post($job_id);
	        $id = $job->post_author;
	        return $id == $user_id;
	    }

	}

	return $per;
}
endif;

if(! function_exists('noo_get_profile_percent_resume')):
    function noo_get_profile_percent_resume(){
        $candidate_id = get_current_user_id();
        $current_user = wp_get_current_user();
        $fields = jm_get_candidate_custom_fields();
        $all_socials = noo_get_social_fields();
        $socials = jm_get_candidate_socials();
        $value_profile = array();
        $value_socials = array();
        foreach ($fields as $field){
           if( $field['name'] == 'email' ) {
                $value_profile[] = $current_user->user_email;
            } elseif( $field['name'] == 'full_name' ) {
                $value_profile[] = $current_user->display_name;
            }else{
               $field_id = jm_candidate_custom_fields_name( $field['name'], $field );
               $value_profile[] = !empty( $candidate_id ) ? get_user_meta( $candidate_id, $field_id, true ) : '';
           }

        }
        foreach($socials as $social){
            if(isset( $all_socials[$social] )){
		        $field = $all_socials[$social];
		        $field['name'] = $social;
		        $field['type'] = 'text';
		        $field['value'] = $social == 'email' ? 'email@' . $_SERVER['HTTP_HOST'] : 'http://';
		        $field_id = $field['name'];
		        $value_socials[] = !empty( $candidate_id ) ? get_user_meta( $candidate_id, $field_id, true ) : '';
            }
        }
        $value = array_merge($value_socials,$value_profile);
        $count_profile=0;
        foreach ($value as $val){
            $count_profile = (!empty($val)) ? $count_profile+1 : $count_profile;
        }

        $profile_percent = floor((100*$count_profile)/(count($value)));
        return $profile_percent;
    }
endif;

if(!function_exists('noo_check_company_empty_require_field')){
	function noo_check_company_empty_require_field(){
		$company_id = jm_get_employer_company();
		$fields = jm_get_company_custom_fields();
		$result = false;
		if(empty($company_id)){
			return $result;
		}
		foreach($fields as $field){
			if(isset($field['required']) && $field['required']){
				$field_id = jm_company_custom_fields_name( $field['name'], $field );
				$value = get_post_meta( $company_id, $field_id, true );
				if(empty($value)){
					$result = true;
					break;
				}
			}
		}
		return $result;
	}
}

if(! function_exists('noo_get_profile_percent_company')) :
    function noo_get_profile_percent_company(){
        $company_id = jm_get_employer_company();
        $current_user = wp_get_current_user();
        $fields = jm_get_company_custom_fields();
        $all_socials = noo_get_social_fields();
        $socials  = jm_get_company_socials();
        $value_profile = array();
        $value_socials = array();
        foreach($fields as $field){
            $field_id = jm_company_custom_fields_name( $field['name'], $field );
		    $value_profile[] = ! empty( $company_id ) ? get_post_meta( $company_id, $field_id, true ) : '';
        }
        foreach($socials as $social){
            if(isset( $all_socials[$social] )){
                $field = $all_socials[$social];
		        $field['name'] = '_' . $social;
		        $field['type'] = 'text';
		        $field['value'] = $social == 'email' ? 'email@' . $_SERVER['HTTP_HOST'] : 'http://';
		        $field_id = $field['name'];
		        $value_socials[] = !empty( $company_id ) ? get_post_meta( $company_id, $field_id, true ) : '';
            }
        }
        $count_profile=0;
        $value = array_merge($value_socials,$value_profile);
        foreach ($value as $val){
            $count_profile = (!empty($val)) ? $count_profile+1 : $count_profile;
        }
        $profile_percent = floor((100*$count_profile)/(count($value)));
        return $profile_percent;
    }
endif;

// escape language with HTML.