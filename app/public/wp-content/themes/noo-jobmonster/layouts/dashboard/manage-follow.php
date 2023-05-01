<?php
 $can_follow_company = noo_can_follow_company(); ?>
<?php if(!$can_follow_company): ?>
    <h3><?php echo __('The featured has disabled for the user. Please contact Administrator to enable this featured.', 'noo') ?></h3>
<?php else: ?>
    <?php
    $current_user = wp_get_current_user();
    $list_company_follow = get_user_meta( $current_user->ID, 'list_company_follow', true );
    $total_follow = !empty($list_company_follow) && is_array($list_company_follow) ? count( $list_company_follow ) : 0;
    ?>
    <div class="member-manage">
        <?php if( $total_follow ) : ?>
            <h3><?php echo sprintf( _n( "You followed %s company", "You followed %s companies", $total_follow, 'noo'), $total_follow ); ?></h3>
        <?php else : ?>
            <h3><?php echo __("No following found",'noo')?></h3>
        <?php endif; ?>

        <div class="noo-dashboard-table">
            <table class="table noo-datatable" id="noo-table-follow">
                <thead>
                <tr>
                    <th><?php _e('Company','noo')?></th>
                    <th class=""><?php _e('Active Jobs', 'noo'); ?></th>
                    <th class="hidden-xs"><?php _e('Location', 'noo'); ?></th>
                    <th class="hidden-xs"><?php _e('Followers','noo')?></th>
                    <th class=""><?php _e('Action','noo')?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($list_company_follow) && is_array($list_company_follow)): ?>
                    <?php foreach ( $list_company_follow as $company_item ) : ?>
                        <tr>
                            <td class="company-name">
                        <span>
                            <a href="<?php echo get_permalink( $company_item ) ?>">
                                <?php echo  Noo_Company::get_company_logo($company_item, array(50, 50));  ?>
                        </span>
                                <span>
                             <a href="<?php echo get_permalink( $company_item ) ?>">
                            <?php echo get_the_title( $company_item ) ?>
                        </a>
                        </span>
                            </td>
                            <td class="company-active-job">
                                <a href="<?php echo get_permalink( $company_item ) ?>">
                                    <?php echo noo_get_company_total_job( $company_item ) ?>
                                </a>
                            </td>
                            <td class="company-location hidden-xs">
                                <?php
                                $address = get_post_meta( $company_item, '_full_address', true );
                                if ($address) { ?>
                                    <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                                    <?php echo $address; ?>
                                <?php } ?>
                            </td>
                            <td class="company-followers hidden-xs">
                                <?php echo noo_total_follow( $company_item ) ?>
                            </td>
                            <td class="company-actions">
                        <span class="noo-follow-company" data-company-id="<?php echo $company_item ?>" data-user-id="<?php echo get_current_user_id();?>">
                            <?php echo noo_follow_status( $company_item, get_current_user_id() ) ?>
                        </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
