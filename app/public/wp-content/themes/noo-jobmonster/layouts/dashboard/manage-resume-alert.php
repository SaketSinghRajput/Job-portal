<?php

$current_user = wp_get_current_user();

$args = array(
    'post_type'      => 'noo_resume_alert',
    'posts_per_page' => - 1,
    'post_status'    => array( 'publish', 'pending' ),
    'author'         => $current_user->ID,
);
$r    = new WP_Query( $args );


$title_text = $r->found_posts ? sprintf( _n( "You've set up %s resume alert", "You've set up %s resume alerts", $r->found_posts, 'noo' ), $r->found_posts, $current_user->user_email ) : __( 'You have no resume alert', 'noo' );
?>
    <div class="member-manage">
        <h3><?php echo $title_text; ?></h3>
        <em><?php echo sprintf( __( 'The emails will be sent to "%s"', 'noo' ), $current_user->user_email ); ?></em>
        <form method="post">
            <div class="member-manage-toolbar top-toolbar clearfix">
            </div>
            <div style="display: none">
                <?php noo_form_nonce( 'job-resume-manage-action' ) ?>
            </div>
            <div class="noo-dashboard-table">
                <table class="table noo-datatable" id="noo-table-resume-alert">
                    <thead>
                    <tr>
                        <th><?php _e( 'Alert Name', 'noo' ) ?></th>
                        <th class="hidden-xs"><?php _e( 'keywords', 'noo' ) ?></th>
                        <th class="hidden-xs hidden-sm"><?php _e( 'Location', 'noo' ) ?></th>
                        <th class="hidden-xs"><?php _e( 'Category', 'noo' ) ?></th>
                        <th class="hidden-xs hidden-sm"><?php _e( 'Frequency', 'noo' ) ?></th>
                        <th class="text-center"><?php _e( 'Action', 'noo' ) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ( $r->have_posts() ):
                        ?>
                        <?php while ( $r->have_posts() ): $r->the_post();
                        global $post;
                        $job_location  = noo_get_post_meta( get_the_ID(), '_job_location' );
                        $job_locations = array();
                        if ( ! empty( $job_location ) ) {
                            $job_location  = noo_json_decode( $job_location );
                            $job_locations = empty( $job_location ) ? array() : get_terms( 'job_location', array(
                                'include'    => array_merge( $job_location, array( - 1 ) ),
                                'hide_empty' => 0,
                                'fields'     => 'names',
                            ) );
                        }
                        $job_category   = noo_get_post_meta( get_the_ID(), '_job_category', '' );
                        $job_categories = array();
                        if ( ! empty( $job_category ) ) {
                            $job_category   = noo_json_decode( $job_category );
                            $job_categories = empty( $job_category ) ? array() : get_terms( 'job_category', array(
                                'include'    => array_merge( $job_category, array( - 1 ) ),
                                'hide_empty' => 0,
                                'fields'     => 'names',
                            ) );
                        }

                        ?>
                        <tr>
                            <td><strong><?php the_title() ?></strong></td>
                            <td class="hidden-xs"><em><?php echo noo_get_post_meta( get_the_ID(), '_keyword' ) ?></em>
                            </td>
                            <td class="hidden-xs hidden-sm">
                                <span class="table-icon"><i class="fa fa-map-marker-alt"></i></span>
                                <em><?php echo implode( ', ', $job_locations ); ?></em>
                            </td>
                            <td class="hidden-xs">
                                <span class="table-icon"><i class="fa fa-bars"></i></span>
                                <em><?php echo implode( ', ', $job_categories ); ?></em>
                            </td>
                            <td class="hidden-xs hidden-sm"><em>
                                    <?php
                                    $frequency_arr =Noo_Resume_Alert::get_frequency();
                                    $frequency     = noo_get_post_meta( get_the_ID(), '_frequency' );
                                    echo $frequency && isset( $frequency_arr[ $frequency ] ) ? $frequency_arr[ $frequency ] : '';
                                    ?>
                                </em></td>
                            <td class="member-manage-actions text-center">
                                <a href="<?php echo Noo_Member::get_edit_resume_alert_url( get_the_ID() ) ?>"
                                   class="member-manage-action" data-toggle="tooltip"
                                   title="<?php esc_attr_e( 'Edit Resume Alert', 'noo' ) ?>"><i class="fas fa-pencil-alt"></i></a>
                                <a onclick="return confirm('<?php _e( 'Are you sure?', 'noo' ); ?>')"
                                   href="<?php echo wp_nonce_url( add_query_arg( array(
                                       'action'       => 'delete_resume_alert',
                                       'resume_alert_id' => get_the_ID(),
                                   ) ), 'edit-resume-alert' ); ?>" class="member-manage-action action-delete"
                                   data-toggle="tooltip" title="<?php esc_attr_e( 'Delete Resume Alert', 'noo' ) ?>"><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7"><h3><?php _e( 'No saved resume alerts', 'noo' ) ?></h3></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="member-manage-toolbar bottom-toolbar clearfix">
                <div class="member-manage-page pull-left">
                    <a href="<?php echo Noo_Member::get_endpoint_url( 'add-resume-alert' ); ?>"
                       class="btn btn-primary"><?php _e( 'Create New', 'noo' ); ?></a>
                </div>
            </div>
        </form>
    </div>
<?php
wp_reset_query();