<?php
wp_enqueue_script( 'noo-swiper' );
wp_enqueue_style( 'noo-swiper' );

$settings_fields = get_theme_mod( 'noo_resume_list_fields', 'title,_job_location,_job_category' );
$settings_fields = ! is_array( $settings_fields ) ? explode( ',', $settings_fields ) : $settings_fields;
$display_fields  = array();
foreach ( $settings_fields as $index => $resume_field ) {
	if ( $resume_field == 'title' ) {
		$field = array( 'name' => 'title', 'label' => __( 'Resume Title', 'noo' ) );
	} else {
		$field = jm_get_resume_field( $resume_field );
	}
	if ( ! empty( $field ) ) {
		$display_fields[] = $field;
	}
}


?>
<div class="noo-resume noo-resumes-slider <?php echo esc_attr( $resume_style ); ?>" id="<?php echo ( $id_resume = uniqid( 'resume-id-' ) ); ?>">
    <div class="swiper-wrapper">
		<?php while ( $wp_query->have_posts() ): $wp_query->the_post();
			global $post; ?>
			<?php
            $enable_block_company = jm_get_action_control('enable_block_company');
            if($enable_block_company=='enable'){
                $block_company_meta = get_post_meta($post->ID,'_block_company');
                $block_company = (!empty($block_company_meta)) ? noo_json_decode($block_company_meta[0]) : '';
                if ($block_company != '') {
                    $employer_id = get_current_user_id();
                    if(Noo_Member::is_employer($employer_id)){
                        $company_id = jm_get_employer_company($employer_id);
                        if(in_array($company_id,$block_company) && $company_id!== '0'){
                            continue;
                        }
                    }
                }
            }
			$candidate_avatar   = '';
			$candidate_name     = '';
			if ( ! empty( $post->post_author ) ) :
				$candidate_avatar = noo_get_avatar( $post->post_author, 40 );
				$candidate      = get_user_by( 'id', $post->post_author );
				$candidate_name = $candidate->display_name;
				$candidate_link = esc_url( apply_filters( 'noo_resume_candidate_link', get_the_permalink(), $post->ID,
					$post->post_author ) );
				?>
                <div class="swiper-slide noo-resume-item <?php echo ( 'yes' == noo_get_post_meta( $post->ID, '_featured',
						'' ) ) ? 'featured-resume' : '' ?>">
                    <a class="resume-details-link" href="<?php the_permalink(); ?>"></a>
                    <div class="noo-resume-info">
                        <div class="item-featured">
                            <a href="<?php echo $candidate_link; ?>">
								<?php echo $candidate_avatar; ?>
                            </a>
                        </div>

                        <div class="item-content">
                            <h5 class="item-author">
                                <a href="<?php echo $candidate_link; ?>"
                                   title="<?php echo esc_html( $candidate_name ); ?>">
									<?php echo esc_html( $candidate_name ); ?>
                                </a>
                            </h5>
                            <h4 class="item-title">
                                <a href="<?php the_permalink() ?>" title="<?php echo get_the_title(); ?>">
									<?php echo get_the_title(); ?>
                                </a>
                            </h4>
                            <div class="item-meta">
								<?php foreach ( $display_fields as $index => $field ) : ?>
									<?php if ( ! isset( $field[ 'name' ] ) || empty( $field[ 'name' ] ) ) {
										continue;
									} ?>
									<?php if ( $field[ 'name' ] !== 'title' ) : ?>
                                        <span class="<?php echo esc_attr( $field[ 'name' ] ) ?>">
                                                <?php
                                                $value = jm_get_resume_field_value( $post->ID, $field );
                                                if ( ! empty( $value ) ) {
	                                                $html  = array();
	                                                $value = noo_convert_custom_field_value( $field, $value );
	                                                if (count( $display_fields ) <= 1  ) {
		                                                if ( is_array( $value ) ) {
			                                                $value = implode( ', ', $value );
		                                                }
		                                                $html[] = $value;
	                                                } else {
                                                        $icon=isset($field['icon'])? $field['icon']:'';
                                                        $icon_class=str_replace("|"," ",$icon);
		                                                $label  = isset( $field[ 'label_translated' ] ) ? $field[ 'label_translated' ] : $field[ 'label' ];
		                                                $html[] = '<span class="resume-' . $field[ 'name' ] . '" style="display: inline-block;">';
                                                        $html[] = '<i class="'.$icon_class.'">';
                                                        $html[] ='</i>';
		                                                $html[] = '<em>';
		                                                $html[] = is_array( $value ) ? implode( ', ', $value ) : $value;
		                                                $html[] = '</em></span>';
	                                                }

	                                                echo implode( "\n", $html );
                                                }
                                                ?>
                                            </span>
									<?php endif; ?>
								<?php endforeach;
								reset( $display_fields ); ?>
                            </div>
                        </div>
                        <?php $can_shortlist_candidate=noo_can_shortlist_candidate() ?>
                        <?php if($can_shortlist_candidate): ?>
                        <a class="noo-shortlist" href="#" data-resume-id="<?php echo esc_attr( $post->ID ) ?>"
                           data-user-id="<?php echo get_current_user_id() ?>" data-type="icon">
							<?php echo noo_shortlist_icon( $post->ID, get_current_user_id() ) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
			<?php endif; ?>
		<?php endwhile; ?>
    </div>

</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var mySwiper = new Swiper ("#<?php echo esc_attr( $id_resume ) ?>", {
            speed: <?php echo absint( $slider_speed ) ?>,
            spaceBetween: 30,
            slidesPerView: <?php echo absint( $column ) ?>,
            slidesPerColumn: <?php echo absint( $rows ) ?>,
            autoplay: <?php echo esc_attr( $autoplay ) ?>,
            pagination: <?php echo esc_attr( $pagination ) ?>,
            preloadImages: false,
            lazy: true,
            navigation: {
                nextEl: '.swiper-next',
                prevEl: '.swiper-prev',
            },
        });

        if(mySwiper){
            mySwiper.update();
            $('.vc_tta-tab').click(function () {
                mySwiper.update();
            });
        }

    });
</script>