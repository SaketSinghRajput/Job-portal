<?php
$company_name = get_post_field('post_title', $company_id);
$all_socials = noo_get_social_fields();
wp_enqueue_script('noo-lightgallery');
wp_enqueue_style('noo-lightgallery');

$company_name = get_post_field( 'post_title', get_the_ID() );
$logo_company = Noo_Company::get_company_logo( get_the_ID(), 'thumbnail-logo' );
$post_view = noo_get_post_views( get_the_ID() );
//$slogan = get_post_meta( get_the_ID(), '_jm_company_field__slogan', true );
$slogan         = noo_get_post_meta( $company_id, '_slogan' );

?>
<div class="noo-company-info-simple">
    <div class="company-avatar">
        <a href="<?php echo get_permalink(); ?>"><?php echo $logo_company;?></a>
    </div>
    <div class="company-info">
        <h3 class="company-name">
            <?php echo noo_get_the_company_name(get_the_ID()); ?>
        </h3>
        <?php if ( !empty( $slogan ) ) : ?>
            <div class="slogan">
                <?php echo esc_html( $slogan ); ?>
            </div>
        <?php endif; ?>
	    <?php
	    // Job's social info
	    $socials = jm_get_company_socials();
	    $html = array();
	    if(is_array($socials)  && !empty($socials)){
		    foreach ($socials as $social) {
			    if (!isset($all_socials[$social])) continue;
			    $data = $all_socials[$social];
			    $value = get_post_meta($company_id, "_{$social}", true);
			    if (!empty($value)) {
				    $url = $social == 'email_address' ? 'mailto:' . $value : esc_url($value);
				    if($data['icon'] == 'fa-link'){
		              $html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fa ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
		            }else{
		              $html[] = '<a title="' . sprintf(esc_attr__('Connect with us on %s', 'noo'), $data['label']) . '" class="noo-icon fab ' . $data['icon'] . '" href="' . $url . '" target="_blank"></a>';
		            }
			    }
		    }

		    if (!empty($html) && count($html) > 0) : ?>
	            <div class="company-social">
				    <?php echo implode("\n", $html); ?>
	            </div>
		    <?php endif; ?>
		<?php }?>
	    <?php if ( Noo_Company::review_is_enable() ): ?>
        <span class="total-review">
            <?php noo_box_rating( noo_get_total_point_review( get_the_ID() ), true ) ?>
            <span><?php echo '(' . noo_get_total_review( get_the_ID() ) . ')' ?></span>
        </span>
	    <?php endif; ?>
    </div>
</div>

<?php
// Custom Fields
$fields = jm_get_company_custom_fields();

$settings_fields    = get_theme_mod('noo_company_list_fields');
$content_meta       = !is_array($settings_fields) ? explode(',', $settings_fields) : $settings_fields;

$html = array();

foreach ($fields as $field) {
	// || $field['name'] == '_address'
    if( $field['name'] == '_logo' || $field['name'] == '_cover_image' || $field['name'] == '_slogan' || $field['name'] == '_portfolio' ) {
        continue;
    }

    $id = jm_company_custom_fields_name($field['name'], $field);
    $value = noo_get_post_meta($company_id, $id, '');
    
    if(($field['type']=='multi_tax_location') || ($field['type']=='multi_tax_location_input')){
        $field['type'] = 'multi_company_location';
    }
    if ($field['name'] == '_job_category') {

        $archive_link = get_post_type_archive_link( 'noo_company' );

        $field['type'] = 'text';
        $field['is_tax'] = false;
        $meta = noo_get_post_meta($company_id, $id, '');
        if( ! empty($meta)){
            $meta=noo_json_decode($meta);
            $links = array();
            foreach ( $meta as $cat_id) {
                $term = get_term_by('id', $cat_id, 'job_category');
                if (!empty($term)){
                    $cat_name = $term->name;
                    $cat_url = esc_url( add_query_arg( array( 'company_category' => $term->term_id ), $archive_link ) );
                    $links[] = '<a href="' . $cat_url . '">' . $cat_name . '</a>';
                }
            }
            $value = join(", ", $links);
        }
    }

    if (!empty($value) && in_array($field['name'], $content_meta)) {
        $html[] = '<li class="' . $field['name'] . '">' . noo_display_field($field, $id, $value, array('label_tag' => 'strong', 'label_class' => 'company-cf', 'value_tag' => 'span'), false) . '</li>';
    }
}
if (!empty($html) && count($html) > 0) : ?>
    <div class="company-custom-fields">
        <h3 class="noo-heading"><?php _e('Company Information', 'noo'); ?></h3>
		<?php if ( is_singular( 'noo_company' ) ) :
					jm_the_job_meta( array(
						'show_company' => false,
						'fields'       => array(
							'job_category',
						),
					) );
			endif; ?>
        <ul>
        	<?php if(in_array('total_job', $content_meta)):?>
	            <li class="total-job">
	            	<strong><?php _e('Total Jobs','noo' ) ?></strong>
	                <span><?php echo sprintf( esc_html__( '%s Jobs', 'noo' ), $total_job ) ?></span>
	            </li>
	        <?php endif;?>
            <!-- <li class="_address"> -->
                <?php //echo noo_get_company_address( get_the_ID() ) ?>
            <!-- </li> -->
            <?php echo implode("\n", $html); ?>
        </ul>
    </div>
<?php endif; ?>
<?php
$content = get_post_field( 'post_content', get_the_ID() );
$content = apply_filters('the_content', $content);
if(!empty($content)){
?>
<div class="noo-company-content">
    <h3 class="noo-heading"><?php _e('About Us', 'noo'); ?></h3>
	<?php
	echo $content;
	?>
</div>
<?php } ?>
<?php
$photo_arr = noo_get_post_meta(get_the_ID(), "_portfolio", '');
if(!empty($photo_arr)) :
	if ( !is_array( $photo_arr ) ) {
		$photo_arr = explode(',', $photo_arr);
	}
	?>
    <div class="noo-company-content">
        <h3 class="noo-heading">
            <span><?php _e( 'Office Photos', 'noo' ); ?></span>
        </h3>
        <div id="company-photo" class="company-photo row is-flex">
			<?php
			foreach ( $photo_arr as $image_id ) :
				if ( empty( $image_id ) )
					continue;
				$image = wp_get_attachment_image_src( $image_id, array(145,180));
				$image_full = wp_get_attachment_image_src( $image_id, 'full');
				if(!empty($image) && !empty($image_full)){
					echo '<a class="col-md-6" href="' . $image_full[0] . '"><img src="' . esc_url( $image[0] ) . '" alt="*" /></a>';
				}

			endforeach;
			?>
        </div>
    </div>
<?php endif; ?>
<?php

// hidden is job submit page.
if ( ! is_page_template( 'page-post-job.php' ) && noo_get_option( 'noo_single_company_contact_form' ) ):
    noo_get_layout('company/contact-form');
endif;
?>

<?php add_action( 'wp_footer', function() { ?>
    <script>
		jQuery(document).ready(function() {
			lightGallery(document.getElementById('company-photo'), {
				thumbnail:true
			});
		});
    </script>
<?php }, 999);