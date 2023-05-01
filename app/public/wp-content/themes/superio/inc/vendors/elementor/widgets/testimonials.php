<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Superio_Elementor_Testimonials extends Widget_Base {

    public function get_name() {
        return 'apus_element_testimonials';
    }

    public function get_title() {
        return esc_html__( 'Apus Testimonials', 'superio' );
    }

    public function get_icon() {
        return 'eicon-testimonial';
    }

    public function get_categories() {
        return [ 'superio-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'superio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'superio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'content', [
                'label' => esc_html__( 'Content', 'superio' ),
                'type' => Controls_Manager::TEXTAREA
            ]
        );

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Choose Image', 'superio' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Brand Image', 'superio' ),
            ]
        );
        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Name', 'superio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'job',
            [
                'label' => esc_html__( 'Job', 'superio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link To', 'superio' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your social link here', 'superio' ),
                'placeholder' => esc_html__( 'https://your-link.com', 'superio' ),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__( 'Testimonials', 'superio' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'superio' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '1'
            ]
        );
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'superio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'superio'),
                    'style2' => esc_html__('Style 2', 'superio'),
                    'style3' => esc_html__('Style 3', 'superio'),
                    'style4' => esc_html__('Style 4', 'superio'),
                    'style5' => esc_html__('Style 5', 'superio'),
                    'style6' => esc_html__('Style 6', 'superio'),
                    'style7' => esc_html__('Style 7', 'superio'),
                    'style8' => esc_html__('Style 8', 'superio'),
                ),
                'default' => 'style1'
            ]
        );
        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'superio' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'superio' ),
                'label_off' => esc_html__( 'Show', 'superio' ),
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'superio' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'superio' ),
                'label_off' => esc_html__( 'Show', 'superio' ),
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label'         => esc_html__( 'Infinite Loop', 'superio' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'superio' ),
                'label_off'     => esc_html__( 'No', 'superio' ),
                'return_value'  => true,
                'default'       => true,
            ]
        );

        $this->add_control(
            'action',
            [
                'label' => esc_html__( 'Button Action', 'superio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'superio'),
                    'st_light' => esc_html__('Light', 'superio'),
                ),
                'default' => ''
            ]
        );

        $this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'superio' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'superio' ),
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Tyles', 'superio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'superio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .title' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'superio' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_control(
            'test_title_color',
            [
                'label' => esc_html__( 'Testimonial Name Color', 'superio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .name-client a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .name-client' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Testimonial Name Typography', 'superio' ),
                'name' => 'test_title_typography',
                'selector' => '{{WRAPPER}} .name-client',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Content Color', 'superio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Content Typography', 'superio' ),
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );

        $this->add_control(
            'job_color',
            [
                'label' => esc_html__( 'Job Color', 'superio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .job' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Job Typography', 'superio' ),
                'name' => 'job_typography',
                'selector' => '{{WRAPPER}} .job',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($testimonials) ) {
            ?>
            <div class="widget-testimonials <?php echo esc_attr($el_class.' '.$style); ?>">
                <?php if($style == 'style7') { ?>

                    <div class="slick-carousel v1 testimonial-main" data-items="1" data-smallmedium="1" data-extrasmall="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                        <?php foreach ($testimonials as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="testimonials-item style7">
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="wrapper-testimonial-thumbnail">
                        <div class="slick-carousel testimonial-thumbnail" data-centerMode="true" data-items="3" data-smallmedium="3" data-extrasmall="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                            <?php foreach ($testimonials as $item) { ?>
                                <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                    <div class="item">
                                        <div class="bottom-info flex-middle">
                                            <?php if ( $img_src ) { ?>
                                                <div class="avarta">
                                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                                </div>
                                            <?php } ?>
                                            <div class="info-testimonials">

                                                <?php if ( !empty($item['name']) ) {

                                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                    if ( ! empty( $item['link']['url'] ) ) {
                                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                    }
                                                    echo trim($title);
                                                ?>
                                                <?php } ?>

                                                <?php if ( !empty($item['job']) ) { ?>
                                                    <div class="job"><?php echo trim($item['job']); ?></div>
                                                <?php } ?>

                                            </div>
                                        </div>
                                    </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                <?php }else{ ?>

                    <div class="slick-carousel testimonial-main <?php echo esc_attr($action); ?>" data-items="<?php echo esc_attr($columns); ?>" data-smallmedium="1" data-extrasmall="1" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>">
                        <?php foreach ($testimonials as $item) { ?>
                        <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                        <div class="item">
                                <div class="testimonials-item <?php echo esc_attr($style); ?>">
                                    <?php if(($style == 'style1') || ($style == 'style5')) { ?>

                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title text-theme"><?php echo trim($item['title']); ?></h3>
                                        <?php } ?>

                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>

                                        <div class="bottom-info flex-middle">
                                            <?php if ( $img_src ) { ?>
                                                <div class="avarta">
                                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                                </div>
                                            <?php } ?>
                                            <div class="info-testimonials">

                                                <?php if ( !empty($item['name']) ) {

                                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                    if ( ! empty( $item['link']['url'] ) ) {
                                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                    }
                                                    echo trim($title);
                                                ?>
                                                <?php } ?>

                                                <?php if ( !empty($item['job']) ) { ?>
                                                    <div class="job"><?php echo trim($item['job']); ?></div>
                                                <?php } ?>

                                            </div>
                                        </div>
                                    <?php } elseif($style == 'style4') { ?>

                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title"><?php echo trim($item['title']); ?></h3>
                                        <?php } ?>

                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>

                                        <?php if ( $img_src ) { ?>
                                            <div class="avarta">
                                                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                            </div>
                                        <?php } ?>
                                        
                                        <div class="info-testimonials">

                                            <?php if ( !empty($item['name']) ) {

                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>

                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo trim($item['job']); ?></div>
                                            <?php } ?>

                                        </div>
                                    <?php } elseif($style == 'style6') { ?>
                                        
                                        <?php if ( $img_src ) { ?>
                                            <div class="avarta">
                                                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                            </div>
                                        <?php } ?>

                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title"><?php echo trim($item['title']); ?></h3>
                                        <?php } ?>

                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>

                                        <div class="info-testimonials">

                                            <?php if ( !empty($item['name']) ) {

                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>

                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo trim($item['job']); ?></div>
                                            <?php } ?>

                                        </div>
                                    <?php } elseif($style == 'style8') { ?>
                                        
                                        <div class="bottom-info flex-middle">
                                            <?php if ( $img_src ) { ?>
                                                <div class="avarta">
                                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                                </div>
                                            <?php } ?>
                                            <div class="info-testimonials">

                                                <?php if ( !empty($item['name']) ) {

                                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                    if ( ! empty( $item['link']['url'] ) ) {
                                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                    }
                                                    echo trim($title);
                                                ?>
                                                <?php } ?>

                                                <?php if ( !empty($item['job']) ) { ?>
                                                    <div class="job"><?php echo trim($item['job']); ?></div>
                                                <?php } ?>

                                            </div>
                                        </div>

                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title text-theme"><?php echo trim($item['title']); ?></h3>
                                        <?php } ?>

                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>

                                    <?php } else { ?>
                                        <?php if ( $img_src ) { ?>
                                            <div class="avarta">
                                                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                            </div>
                                        <?php } ?>
                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title text-theme"><?php echo trim($item['title']); ?></h3>
                                        <?php } ?>
                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>
                                        <div class="info-testimonials">

                                            <?php if ( !empty($item['name']) ) {

                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>

                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo trim($item['job']); ?></div>
                                            <?php } ?>

                                        </div>
                                    <?php } ?>
                                </div>

                        </div>
                        <?php } ?>
                    </div>

                <?php } ?>
            </div>
            <?php
        }
    }
}
if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Superio_Elementor_Testimonials );
} else {
    Plugin::instance()->widgets_manager->register( new Superio_Elementor_Testimonials );
}