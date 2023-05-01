<?php
$company = get_post( get_the_ID() );
$company_author = $company->post_author;
$company_author = get_user_by( 'id', $company_author );
if ( $company_author ):
    $company_email = $company_author->user_email;
    if ( ! empty( $company_email ) ):
?>
		<div class="noo-company-contact">
			<h3 class="noo-company-contact-title">
				<?php _e( 'Contact Us', 'noo' ); ?>
			</h3>
			<div class="noo-company-contact-form">
				<form id="contact_company_form" class="form-horizontal jform-validate">
					<div style="display: none">
						<input type="hidden" name="action" value="noo_ajax_send_contact">
						<input type="hidden" name="to_email" value="<?php echo $company_email; ?>"/>
						<input type="hidden" class="security" name="security" value="<?php echo wp_create_nonce( 'noo-ajax-send-contact' ) ?>"/>
					</div>
					<div class="form-group">
                    <span class="input-icon">
                        <input type="text" class="form-control jform-validate" id="name" name="from_name" required="" placeholder="<?php _e( 'Enter Your Name', 'noo' ); ?>"/>
                        <i class="fa fa-home"></i>
                    </span>
					</div>
					<div class="form-group">
                    <span class="input-icon">
                        <input type="email" class="form-control jform-validate jform-validate-email" id="email" name="from_email" required="" placeholder="<?php _e( 'Email Address', 'noo' ); ?>"/>
                        <i class="fa fa-envelope"></i>
                        <input class="hide" type="text" name="email_rehot" autocomplete="off"/>
                    </span>
					</div>
					<div class="form-group">
                    <span class="input-icon">
                        <textarea class="form-control jform-validate" id="message" name="from_message" rows="5" placeholder="<?php _e( 'Message...', 'noo' ); ?>"></textarea>
                        <i class="fa fa-comment"></i>
                    </span>
					</div>
					<?php do_action( 'noo_company_contact_form' ); ?>
					<div class="form-actions">
						<button type="submit" class="btn btn-primary"><?php _e( 'Send Message', 'noo' ); ?></button>
					</div>
					<div class="noo-ajax-result"></div>
				</form>
			</div>
		</div>
		<?php
	endif;
endif;