<?php
$can_follow_company = noo_can_follow_company(); ?>
<?php if(!$can_follow_company): ?>
	<h3><?php echo __('The featured has disabled for the user. Please contact Administrator to enable this featured.', 'noo') ?></h3>
<?php else: ?>
    <?php
    $current_user        = wp_get_current_user();
    $list_company_follow = get_user_meta( $current_user->ID, 'list_company_follow', true );
    $list_company_follow = !empty($list_company_follow) && is_array($list_company_follow) ? $list_company_follow : array();

    $list_job = array();
    $status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
    foreach ( $list_company_follow as $company_id ) {
        $job_ids  = Noo_Company::get_company_jobs( $company_id, array(), - 1, $status );
        $list_job = array_merge( $list_job, $job_ids );
    }

    $args = array(
        'paged'       => - 1,
        'post_type'   => 'noo_job',
        'post__in'    => array_merge( $list_job, array( 0 ) ),
        'post_status' => $status
    );

    $r = new WP_Query( $args );
    ?>
    <div class="member-manage">
        <?php if ( $r->found_posts ) : ?>
            <h3><?php echo sprintf( _n( "You followed %s Job", "You followed %s Jobs ", $r->found_posts, 'noo' ), $r->found_posts ); ?></h3>
        <?php else : ?>
            <h3><?php echo __( "No following found", 'noo' ) ?></h3>
        <?php endif; ?>
        <div class="noo-dashboard-table">
            <table class="table noo-datatable" id="noo-table-job-follow">
                <thead>
                <tr>
                    <th><?php _e( 'Logo', 'noo' ) ?></th>
                    <th><?php _e( 'Job title', 'noo' ) ?></th>
                    <th class="hidden-xs"><?php _e( 'Location', 'noo' ); ?></th>
                    <th class="hidden-xs hidden-sm"><?php _e( 'Expiry Date', 'noo' ) ?></th>
                    <th><?php _e( 'Action', 'noo' ) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if ( $r->have_posts() ) : ?>
                    <?php while ( $r->have_posts() ): $r->the_post();
                        global $post; ?>
                        <tr>
                            <?php
                            $company_id=jm_get_job_company($post);
                            $company_name=noo_get_the_company_name($company_id);
                            $company_url=get_the_permalink($company_id);
                            ?>
                            <td class="logo">
                                <a href="<?php echo $company_url ?>">
                                    <?php echo Noo_Company::get_company_logo($company_id, array(50, 50)); ?>
                                </a>
                            </td>
                            <td class="job-name">
                                <a href="<?php echo get_permalink() ?>">
                                    <?php echo get_the_title() ?>
                                </a>
                                <p>
                                <span style="font-size: 12px;">
                                    <a href="<?php echo $company_url ?>">
                                        <?php echo sprintf(esc_html__('Post by:%s','noo'),$company_name); ?>
                                    </a>
                                </span>
                                </p>
                            </td>
                            <td class="job-location hidden-xs">
                                <i class="fa fa-map-marker-alt"></i>&nbsp
                                <em><?php echo get_the_term_list( get_the_ID(), 'job_location', '', ', ' ) ?></em>
                            </td>
                            <td class="job-date hidden-xs hidden-sm">
                                <?php
                                $closing = noo_get_post_meta( $post->ID, '_closing' );
                                $closing = ! is_numeric( $closing ) ? strtotime( $closing ) : $closing;
                                $closing = ! empty( $closing ) ? date_i18n( get_option( 'date_format' ), $closing ) : '';
                                if ( ! empty( $closing ) ) :
                                    ?>
                                    <span><i class="fa fa-calendar-alt"></i>&nbsp;<em><?php echo $closing; ?></em></span>
                                <?php else : ?>
                                    <span class="text-center"><?php echo __( 'Equal to expired date', 'noo' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="company-actions">
                                <a href="<?php the_permalink() ?>">
                                    <?php echo esc_html__( 'View Details', 'noo' ) ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php wp_reset_query(); ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
