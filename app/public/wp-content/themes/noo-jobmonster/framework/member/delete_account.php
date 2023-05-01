<?php


function noo_delete_account_url()
{
    $url = add_query_arg(
        array(
            'action' => 'delete_account',
            'user' => get_current_user_id(),
            'nonce' => wp_create_nonce('noo_delete_account')
        )
        , home_url());
    return esc_url($url);
}

function noo_delete_account_action()
{
    if (
        is_user_logged_in() &&
        isset($_GET['action']) &&
        isset($_GET['nonce']) &&
        $_GET['action'] === 'delete_account' &&
        wp_verify_nonce($_GET['nonce'], 'noo_delete_account')
    ) {
        $user = isset($_GET['user']) ? $_GET['user'] : 0;

        // Exclude Demo Account.
        include_once(ABSPATH . 'wp-admin/includes/user.php');

        if (is_plugin_active('noo-jobmonster-login-demo/noo-jobmonster-login-demo.php')) {
            if (in_array($user, array('59', '60'))) {
                noo_message_add(esc_html__('This is demo account, you can\'t delete the account,', 'noo'), 'error');
                $redirect_uri = Noo_Member::get_member_page_url();
                wp_redirect($redirect_uri);
                exit;
            }
        }

        // Delete User.
        if ($user == get_current_user_id()) {
            wp_delete_user($user);
            wp_redirect(home_url());
            exit;
        }
    }
}

add_action('init', 'noo_delete_account_action');

function noo_delete_account_output()
{
    ?>
    <div class="form-title">
        <h3><?php echo esc_html__('Delete Account', 'noo'); ?></h3>
    </div>
    <p><?php echo esc_html__('Warning! Can not restore operation.', 'noo'); ?></p>
    <a onclick='javascript:return confirm("<?php echo esc_html__('Are you sure you want to delete your account? Note, this operation can not be undone.', 'noo'); ?>")'
       href="<?php echo noo_delete_account_url(); ?>" class="btn btn-primary">
        <?php echo esc_html__('Delete Account', 'noo'); ?>
    </a>
    <?php
}

add_action('noo_edit_candidate_after', 'noo_delete_account_output');
add_action('noo_edit_company_after', 'noo_delete_account_output');