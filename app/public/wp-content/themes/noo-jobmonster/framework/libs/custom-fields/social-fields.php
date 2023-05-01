<?php
if ( ! function_exists( 'noo_get_social_fields' ) ) :
	function noo_get_social_fields() {
		$social_fields = array(
			'googleplus'    => array( // Be careful with google plus field name
				'label'       => __( 'Google+', 'noo' ),
				'icon'        => 'fa-google-plus-g',
				'icon_square' => 'fa-google-plus-square',
			),
			
			'facebook'      => array(
				'label'       => __( 'Facebook', 'noo' ),
				'icon'        => 'fa-facebook',
				'icon_square' => 'fa-facebook-square',
				'alt_icon'    => 'fa-facebook-f',
			),
			'twitter'       => array(
				'label'       => __( 'Twitter', 'noo' ),
				'icon'        => 'fa-twitter',
				'icon_square' => 'fa-twitter-square',
			),
			'instagram'     => array(
				'label' => __( 'Instagram', 'noo' ),
				'icon'  => 'fa-instagram',
			),
			
			'linkedin'      => array(
				'label'       => __( 'LinkedIn', 'noo' ),
				'icon'        => 'fa-linkedin',
				'icon_square' => 'fa-linkedin-in',
			),
			'xing'      => array(
				'label'       => __( 'Xing', 'noo' ),
				'icon'        => 'fa-xing',
				'icon_square' => 'fa-xing-square',
			),
			'email_address' => array(
				'label'    => __( 'Email', 'noo' ),
				'icon'     => 'fa-envelope',
				'icon_square' => 'fa-envelope-square',
			),
			'pinterest'     => array(
				'label'       => __( 'Pinterest', 'noo' ),
				'icon'        => 'fa-pinterest',
				'icon_square' => 'fa-pinterest-square',
				'alt_icon'    => 'fa-pinterest-p',
			),
			'youtube'       => array(
				'label'       => __( 'Youtube', 'noo' ),
				'icon'        => 'fa-youtube',
				'icon_square' => 'fa-youtube-square',
				'alt_icon'    => 'fa-play',
			),
			'tumblr'        => array(
				'label'       => __( 'Tumblr', 'noo' ),
				'icon'        => 'fa-tumblr',
				'icon_square' => 'fa-tumblr-square',
			),
			'behance'       => array(
				'label'       => __( 'Behance', 'noo' ),
				'icon'        => 'fa-behance',
				'icon_square' => 'fa-behance-square',
			),
			'flickr'        => array(
				'label' => __( 'Flickr', 'noo' ),
				'icon'  => 'fa-flickr',
			),
			'vimeo'         => array(
				'label'       => __( 'Vimeo', 'noo' ),
				'icon'        => 'fa-vimeo',
				'icon_square' => 'fa-vimeo-square',
			),
			'github'        => array(
				'label'       => __( 'GitHub', 'noo' ),
				'icon'        => 'fa-github',
				'icon_square' => 'fa-github-square',
				'alt_icon'    => 'fa-github-alt',
			),
			'vk'            => array(
				'label' => __( 'VKontakte', 'noo' ),
				'icon'  => 'fa-vk',
			),
			'website'       => array(
				'label' => __( 'Website', 'noo' ),
				'icon'  => 'fa-link',
			)
		);

		return apply_filters( 'noo_social_fields', $social_fields );
	}
endif;
