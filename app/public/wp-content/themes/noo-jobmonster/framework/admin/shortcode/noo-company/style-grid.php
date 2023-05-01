<?php
/**
 * style-grid.php
 *
 * @author  : NooTheme
 * @since   : 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$args = array(
    'post_type' => 'noo_company',
    'post_status' => 'publish',
    'posts_per_page' => $posts_per_page,
);

if ($featured_company == true) {
    $args['meta_query'][] = array(
        'key' => '_company_featured',
        'value' => 'yes',
    );
}
$wp_query = new WP_Query($args);
?>
<div class="swiper-container noo-company-sc <?php echo esc_attr($style); ?>"
     id="<?php echo esc_attr($id_company = uniqid('company-id-')); ?>">
    <div class="swiper-wrapper">
        <?php
        while ($wp_query->have_posts()) : $wp_query->the_post();
            global $post;

            $company_name = $post->post_title;
            $count = noo_company_job_count($post->ID);
            ?>
            <div class="swiper-slide company-item">
                <div class="company-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo Noo_Company::get_company_logo($post->ID, 'company-logo'); ?>
                    </a>
                </div>
                <div class="company-meta">
                    <a class="company-name" href="<?php the_permalink(); ?>">
                        <?php echo noo_get_the_company_name($post); ?>
                    </a>
                    <p>
                        <span class="job-count"><?php echo sprintf(_n('%s Job', '%s Jobs', $count, 'noo'), $count); ?></span>
                        <span class="company-address"><?php echo noo_get_company_address($post->ID); ?></span>
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="company-pagination text-center">
        <a href="#" class="swiper-prev">
            <i class="fa fa-chevron-left"></i>
        </a>

        <a href="#" class="swiper-next">
            <i class="fa fa-chevron-right"></i>
        </a>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var mySwiper = new Swiper("#<?php echo esc_attr($id_company) ?>", {
            speed: <?php echo absint($slider_speed) ?>,
            spaceBetween: 30,
            slidesPerView: <?php echo absint($column) ?>,
            slidesPerColumn: <?php echo absint($rows) ?>,
            autoplay: <?php echo esc_attr($autoplay) ?>,
            preloadImages: false,
            breakpoints: {
                480: {
                    slidesPerView: 1,
                    slidesPerColumn: 1,
                    spaceBetween: 10
                },
                // when window width is <= 640px
                640: {
                    slidesPerView: 2,
                    slidesPerColumn: 1,
                    spaceBetween: 20
                },
            },
            lazy: true,
            navigation: {
                nextEl: '.swiper-next',
                prevEl: '.swiper-prev',
            },
        })
    });
</script>