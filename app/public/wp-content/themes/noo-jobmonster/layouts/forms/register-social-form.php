<?php
$submit_label = empty( $submit_label ) ? __('Sign Up', 'noo') : $submit_label;
$allow_register = Noo_Member::get_setting('allow_register', 'both');
if( $allow_register == 'both' ) :
	$prefix = uniqid();
	?>
	<form class="noo-ajax-register-form form-horizontal" action="<?php echo esc_url( wp_registration_url() ); ?>" method="post" id="registerform-social">
		<div style="display: none">
			<input type="hidden" class="redirect_to" name="redirect_to" value="<?php echo esc_url(apply_filters('noo_register_redirect','')); ?>" />
			<input type="hidden" name="action" value="noo_ajax_register">
			<input type="hidden" name="user_login">
			<input type="hidden" name="user_email">
			<input type="hidden" name="user_password">
			<input type="hidden" name="cuser_password">
			<!-- <input type="hidden" class="security" name="security" value="<?php //echo wp_create_nonce( 'noo-ajax-register' ) ?>" /> -->
		</div>
		<div class="form-group text-center noo-ajax-result" style="display: none"></div>
		<h4 class="register-heading"></h4>
		<strong class="register-text"><?php _e('Please let us know who you are to finish the registration', 'noo'); ?></strong>
		<br/>
		<br/>
		<div class="form-group row">
			<div class="col-sm-9">
				<div class="form-control-flat">
					<?php 
					$register_role_options = Noo_Member::get_user_role_options();
					?>
					<?php foreach ($register_role_options  as $option_key => $option_label ):?>
						<label class="radio" for="<?php echo $prefix.'_'.$option_key; ?>_user_role" ><input id="<?php echo $prefix.'_'.$option_key; ?>_user_role" type="radio" name="user_role" value="<?php echo esc_attr($option_key)?>"><i></i><?php echo esc_html($option_label)?></label>
					<?php endforeach;?>
				</div>
			</div>
		</div>
		<?php do_action( 'noo_register_social_form' ); ?>
		<div class="form-group text-center">
			<button type="submit" class="btn btn-primary"><?php echo esc_html($submit_label)?></button>
		</div>
		<?php do_action( 'noo_register_form_end' ); ?>
	</form>
<?php endif; ?>