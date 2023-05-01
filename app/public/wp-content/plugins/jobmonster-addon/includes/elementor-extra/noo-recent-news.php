<?php
/**
 * Elementor Post Layout Widget.
 * @since 1.0.0
 */
namespace Noo_Elementor_Widgets;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Noo_Recent_News extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'noo-recent-news';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Noo Recent News', 'noo' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'fa fa-newspaper-o';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'noo-element-widgets' ];
	}
	/*
	* Depend Style
	*/
	public function get_style_depends() {
        return [
            'owl-carousel',
        ];
    }
	/*
	* Depend Script
	*/
	public function get_script_depends() {
        return [
            'owl-carousel',
            'noo-elementor',
        ];
    }
		/**
	 * Get categories.
	 */
	private function get_post_type_categories( $taxonomy = 'category' ) {
		$options = array();
		if ( ! empty( $taxonomy ) ) {
			// Get categories for post type.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => true,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
						}
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		// Tab Content
		$this->noo_recent_news_option();
		$this->noo_recent_news_title();
		$this->noo_recent_news_meta();
		$this->noo_recent_news_content();

		// Tab Style
		$this->noo_recent_news_style();
	}

	/*
	* Recent New Option
	*/
	private function noo_recent_news_option(){
		$this->start_controls_section(
			'noo_recent_news_section',
			[
				'label' => esc_html__( 'General Options', 'noo' )
			]
		);
		$this->add_control(
			'style',
			[
				'label' 	=> esc_html__( 'Layout', 'noo' ),
				'type' 		=> Controls_Manager::SELECT,
				'options' 	=> array(
					'grid' 		=> esc_html__( 'Grid', 'noo' ),
					'stanđard' 	=> esc_html__( 'Standard', 'noo' ),
					'slider' 	=> esc_html__( 'Slider', 'noo' ),
				),
				'default' 	=> 'grid',
			]
		);
		$this->add_control(
			'slider_style',
			[
				'label' 	=> esc_html__( 'Slider Style', 'noo' ),
				'type' 		=> Controls_Manager::SELECT,
				'options' 	=> array(
					'slider-boxshadow' 	=> esc_html__( 'Box Shadow', 'noo' ),
					'slider-border' 	=> esc_html__( 'Border', 'noo' ),
				),
				'default' 	=> 'slider-box-shadow',
				'condition' => [
					'style' => 'slider',
				]
			]
		);
		$this->add_control(
			'post_category',
			[
				'label' 	=> esc_html__( 'Category', 'noo' ),
				'type' 		=> Controls_Manager::SELECT2,
				'multiple' 	=> true,
				'options' 	=> $this->get_post_type_categories('category'),
			]
		);
		// Columns.
		$this->add_responsive_control(
			'columns',
			[
				'type'           => Controls_Manager::SELECT,
				'label'          => '<i class="fa fa-columns"></i> ' . esc_html__( 'Columns', 'noo' ),
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'options'        => [
					2 => 2,
					3 => 3,
					4 => 4,
				],
			]
		);
		// Order by.
		$this->add_control(
			'order_by',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => '<i class="fa fa-sort"></i> ' . esc_html__( 'Order by', 'noo' ),
				'default' => 'date',
				'options' => [
					'date'          => esc_html__( 'Date', 'noo' ),
					'title'         => esc_html__( 'Title', 'noo' ),
					'modified'      => esc_html__( 'Modified date', 'noo' ),
					'comment_count' => esc_html__( 'Comment count', 'noo' ),
					'rand'          => esc_html__( 'Random', 'noo' ),
				],
			]
		);
		// Order by.
		$this->add_control(
			'order',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => '<i class="fa fa-sort"></i> ' . esc_html__( 'Sort by', 'noo' ),
				'default' => 'desc',
				'options' => [
					'asc'         => esc_html__( 'ASC', 'noo' ),
					'desc'          => esc_html__( 'DESC', 'noo' ),
				],
			]
		);
		$this->add_control(
			'post_per_page',
			[
				'label' => esc_html__( 'Post Per Page', 'noo' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => esc_html__( '8', 'noo' ),
				'description' => esc_html__( '-1 = Get all post.', 'noo' ),
				'default'     => 8,
			]
		);
		$this->add_control(
			'loop',
			[
				'label' => esc_html__( 'Loop', 'noo' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'noo' ),
				'label_on' => esc_html__( 'On', 'noo' ),
				'separator' => 'before',
				'default'   => 'yes',
				'condition' => [
					'style' => 'slider',
				]
			]
		);
		$this->add_control(
			'auto_play',
			[
				'label' => esc_html__( 'Auto Play', 'noo' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'noo' ),
				'label_on' => esc_html__( 'On', 'noo' ),
				'separator' => 'before',
				'default'   => 'yes',
				'condition' => [
					'style' => 'slider',
				]
			]
		);
        $this->add_control(
            'auto_height',
            [
                'label' => esc_html__('Auto Height', 'noo'),
                'type'     => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'noo'),
                'label_of' => esc_html__('No', 'noo'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
					'style' => 'slider',
				]
            ]
        );
		$this->add_control(
			'show_nav',
			[
				'label' => esc_html__( 'Show Navigation', 'noo' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'noo' ),
				'label_on' => esc_html__( 'On', 'noo' ),
				'separator' => 'before',
				'default'   => 'yes',
				'condition' => [
					'style' => 'slider',
				]
			]
		);
		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Show Pagination', 'noo' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'noo' ),
				'label_on' => esc_html__( 'On', 'noo' ),
				'separator' => 'before',
				'condition' => [
					'style' => 'slider',
				]
			]
		);
		$this->end_controls_section();
	}

	/*
	* Recent New -> Title
	*/

	private function noo_recent_news_title(){
		$this->start_controls_section(
			'noo_recent_news_title',
			[
				'label' => esc_html__( 'Title', 'noo' ),
			]
		);

		// Hide title.
		$this->add_control(
			'title_hide',
			[
				'label'   => '<i class="fa fa-minus-circle"></i> ' . esc_html__( 'Hide', 'noo' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		// Title tag.
		$this->add_control(
			'title_tag',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => '<i class="fa fa-code"></i> ' . esc_html__( 'Tag', 'noo' ),
				'default' => 'h5',
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'span' => 'span',
					'p'    => 'p',
					'div'  => 'div',
				],
			]
		);

		// Title link.
		$this->add_control(
			'title_link',
			[
				'label'   => '<i class="fa fa-link"></i> ' . esc_html__( 'Link', 'noo' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Meta Options.
	 */
	private function noo_recent_news_meta() {
		$this->start_controls_section(
			'noo_recent_news_meta',
			[
				'label' => esc_html__( 'Meta', 'noo' ),
			]
		);

		// Hide content.
		$this->add_control(
			'meta_hide',
			[
				'label'   => '<i class="fa fa-minus-circle"></i> ' . esc_html__( 'Hide', 'noo' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		// Meta.
		$this->add_control(
			'meta_display',
			[
				'label'       => '<i class="fa fa-info-circle"></i> ' . esc_html__( 'Display', 'noo' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => [ 'date' ],
				'multiple'    => true,
				'options'     => [
					'author'   => esc_html__( 'Author', 'noo' ),
					'date'     => esc_html__( 'Date', 'noo' ),
					'category'       => esc_html__( 'Categories', 'noo' ),
					'tags'     => esc_html__( 'Tags', 'noo' ),
					'comments' => esc_html__( 'Comments', 'noo' ),
				],
			]
		);

		// No. of Categories.
		$this->add_control(
			'meta_categories_max',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => esc_html__( 'No. of Categories', 'noo' ),
				'placeholder' => esc_html__( 'How many categories to display?', 'noo' ),
				'default'     => esc_html__( '1', 'noo' ),
				'condition'   => [
					'meta_display' => 'category',
				],
			]
		);

		// No. of Tags.
		$this->add_control(
			'meta_tags_max',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => esc_html__( 'No. of Tags', 'noo' ),
				'placeholder' => esc_html__( 'How many tags to display?', 'noo' ),
				'condition'   => [
					'meta_display' => 'tags',
				],
			]
		);

		// Remove meta icons.
		$this->add_control(
			'meta_remove_icons',
			[
				'label'   => '<i class="fa fa-minus-circle"></i> ' . esc_html__( 'Remove icons', 'noo' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Content Options.
	 */
	private function noo_recent_news_content() {
		$this->start_controls_section(
			'noo_recent_news_content',
			[
				'label' => esc_html__( 'Content', 'noo' ),
			]
		);

		// Hide content.
		$this->add_control(
			'content_hide',
			[
				'label'   => '<i class="fa fa-minus-circle"></i> ' . esc_html__( 'Hide', 'noo' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		// Length.
		$this->add_control(
			'excerpt_length',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => '<i class="fa fa-arrows-h"></i> ' . esc_html__( 'Excerpt Length (words)', 'noo' ),
				'placeholder' => esc_html__( 'Length of content (words)', 'noo' ),
				'default'     => 15,
			]
		);

		// Read more button hide.
		$this->add_control(
			'noo_recent_news_btn',
			[
				'label'     => '<i class="fa fa-check-square"></i> ' . esc_html__( 'Button', 'noo' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		// Default button text.
		$this->add_control(
			'noo_recent_news_btn_text',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => esc_html__( 'Button text', 'noo' ),
				'placeholder' => esc_html__( 'Read more', 'noo' ),
				'default'     => esc_html__( 'Read more >', 'noo' ),
				'condition'   => [
					'noo_recent_news_btn!'    => '',
				],
			]
		);

		$this->end_controls_section();
	}
	/**
	 * Render Noo Post Layout widget output on the frontend.
	 */
	protected function render() {

		// Get settings.
		$settings = $this->get_settings();
		$item_class = $data_slide = $grid_class = '';
		$mobile_class = ( ! empty( $settings['columns_mobile'] ) ? ' noo-mobile-' . $settings['columns_mobile'] : '' );
		$tablet_class = ( ! empty( $settings['columns_tablet'] ) ? ' noo-tablet-' . $settings['columns_tablet'] : '' );
		$desktop_class = ( ! empty( $settings['columns'] ) ? ' noo-desktop-' . $settings['columns'] : '' );

		$style = $settings['style'];
		$slider_style = $settings['slider_style'];
		if('grid' == $style || 'stanđard' == $style){
			$grid_class = $desktop_class . $tablet_class . $mobile_class;
			$item_class = ' noo-grid-item';
		}elseif('slider' == $style){
			$grid_class = ' owl-carousel';
			$auto_play = $settings['auto_play'] == 'yes' ? true : false;
			$loop 	= $settings['loop'] == 'yes' ? true : false;
			$show_nav = $settings['show_nav'] == 'yes' ? true : false;
			$auto_height = $settings['auto_height'] == 'yes' ? true : false;
			$show_pagination = $settings['show_pagination'] == 'yes' ? true : false;
			$data_slide = array(
				'items' 	=> $settings['columns'],
				'loop'		=> $loop,
				'autoplay'  => $auto_play,
				'auto_height'=> $auto_height,
				'show_nav'  => $show_nav,
				'dot'  		=> $show_pagination,
				'next'      => sprintf(__('%s <i class="fa fa-angle-right" aria-hidden="true"></i>', 'noo'),esc_html__('Next', 'noo')),
				'prev'      => sprintf(__('<i class="fa fa-angle-left" aria-hidden="true"></i> %s', 'noo'),esc_html__('Previous', 'noo'))
			);
			$data_slide = 'data-slide="'.esc_attr(json_encode($data_slide) ). '"';
		}
		// Arguments for query.
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,

		);
		// Display posts in category.
		if ( ! empty( $settings['post_category'] )) {
			$cat_name = implode(',', $settings['post_category']);
			$args['category_name'] = $cat_name;
		}

		// Items to display.
		$args['posts_per_page'] = $settings['post_per_page'];

		// Order by.
		if ( ! empty( $settings['order_by'] ) ) {
			$args['orderby'] = $settings['order_by'];
		}
		// Order.
		if ( ! empty( $settings['order'] ) ) {
			$args['order'] = $settings['order'];
		}

		// // Pagination.
		// if ( ! empty( $settings['grid_pagination'] ) ) {
		// 	$paged         = get_query_var( 'paged' );
		// 	if ( empty( $paged ) ) {
		// 		$paged         = get_query_var( 'page' );
		// 	}
		// 	$args['paged'] = $paged;
		// }
		// Query.
		$query = new \WP_Query( $args );
		// Output.
		echo '<div class="noo-recent-news posts-loop ' . $style . ' ' . $slider_style . '">';
		echo '<div class="noo-grid-col' . $grid_class . '" '. $data_slide .'>';
		// Query results.
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<div class="noo-pl-item-wrap '.$item_class.'">';

				echo '<div class="loop-item-wrap">';
				// Image.
				$this->renderImage();
				
				echo '<div class="loop-item-content">';
				// Title.
				$this->renderTitle();

				// Meta.
				$this->renderMeta();

				// Content.
				$this->renderContent();

				echo '</div><!-- .loop-item-content -->';
				
				echo '</div><!-- .loop-item-wrap -->';

				echo '</div>';

			} // End while().

			
		} // End if().

		// Restore original data.
		wp_reset_postdata();

		echo '</div><!-- . noo-pl-element -->';
		echo '</div><!-- .noo-recent-news -->';

	}
	/**
	 * Render image.
	 */
	protected function renderImage() {
		$settings = $this->get_settings();
		// Check if post type has featured image.
		if ( has_post_thumbnail() ) {
			?>
				<div class="loop-item-featured">
					<a clas="content-thumb" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<?php
						the_post_thumbnail(
							'full', array(
								'class' => 'img-responsive',
								'alt'   => get_the_title( get_post_thumbnail_id() ),
							)
						); ?>
					</a>
				</div>
			<?php
		} else{
			?>
			<div class="loop-item-featured">
				<a clas="content-thumb" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<img width="600" height="450"
		            src="<?php echo NOO_ASSETS_URI; ?>/images/thumb-news-bg.gif"
		            class="attachment-noo-thumbnail-square wp-post-image"
		            alt="<?php the_title(); ?>">
				</a>
			</div>
            <?php 
		}
	}
	/**
	 * Render title.
	 */
	protected function renderTitle() {
		$settings = $this->get_settings();
		if ( $settings['title_hide'] !== 'yes' ) { ?>
			<<?php echo $settings['title_tag']; ?> class="loop-item-title">
			<?php if ( $settings['title_link'] == 'yes' ) { ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php the_title(); ?>
				</a>
				<?php
			} else {
				the_title();
			} ?>
			</<?php echo $settings['title_tag']; ?>>
			<?php
		}
	}
	/**
	 * Render Content Left.
	 */
	protected function renderContentLeft() {
		?>
			<div class="content-left">
	         
                <div class="author-bio">
                    <div class="author-avatar">
                        <?php echo get_avatar( get_the_author_meta( 'user_email' ),95); ?>
                    </div>
                </div>
	            
	            <?php noo_social_share();?>
	        </div>
		<?php
	}
	/**
	 * Display categories in meta section.
	 */
	protected function renderMetaGridCategories() {
		$settings       	= $this->get_settings();
		$post_type_category = get_the_category();
		$maxCategories      = $settings['meta_categories_max'] ? $settings['meta_categories_max'] : '-1';
		$i              	= 0; // counter

		if ( $post_type_category ) { ?>
			<span class="category-post">
				<?php
				echo ( $settings['meta_remove_icons'] == '' ) ? '<i class="fa fa-list-ul"></i>' : '';

				foreach ( $post_type_category as $category ) {
					if ( $i == $maxCategories ) {
						break;
					} ?>
					<span class="noo-cat-item ">
						<a class="br4 b-all" href="<?php echo get_category_link( $category->term_id ); ?>"
						   title="<?php echo esc_html($category->name); ?>">
							<?php echo esc_html($category->name); ?>
						</a>
					</span>
					<?php
				} ?>
			</span>
			<?php
		}
	}

	/**
	 * Display tags in meta section.
	 */
	protected function renderMetaGridTags() {
		$settings       = $this->get_settings();
		$post_type_tags = get_the_tags();
		$maxTags        = $settings['meta_tags_max'] ? $settings['meta_tags_max'] : '-1';
		$i              = 0; // counter

		if ( $post_type_tags ) { ?>
			<span class="noo-pl-tags">
				<?php
				echo ( $settings['meta_remove_icons'] == '' ) ? '<i class="fa fa-tags"></i>' : '';

				foreach ( $post_type_tags as $tag ) {
					if ( $i == $maxTags ) {
						break;
					} ?>
					<span class="noo-tags-item">
						<a href="<?php echo get_tag_link( $tag->term_id ); ?>" title="<?php echo $tag->name; ?>">
							<?php echo $tag->name; ?>
						</a>
					</span>
					<?php
					$i ++;
				} ?>
			</span>
			<?php
		}
	}
	/**
	 * Render meta of post type.
	 */
	protected function renderMeta() {
		$settings = $this->get_settings();

		if ( $settings['meta_hide'] !== 'yes' ) {
			if ( ! empty( $settings['meta_display'] ) ) { ?>
				<p class="content-meta">
					
					<?php
						if($settings['style'] != 'grid' ){
							foreach ( $settings['meta_display'] as $meta ) {

								switch ( $meta ) :
									// Author
									case 'author': ?>
										<span class="entry-author">
											<?php
											echo ( $settings['meta_remove_icons'] == '' ) ? '<i class="fa fa-pencil"></i>' : '';

											$authordata = get_userdata($post->post_author);

											?>
											<a href="<?php echo esc_url( get_author_posts_url( $authordata->ID, get_the_author_meta( 'nicename',$authordata->ID) ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'Posts by %s', 'noo'),  $authordata->display_name ) ); ?>" rel="author"><?php echo get_the_author(); ?></a>
										</span>
										<?php
										// Date
										break;
									case 'date': ?>
										<span class="entry-date">
											<?php
											echo ( $settings['meta_remove_icons'] == '' ) ? '<i class="fa fa-calendar"></i>' : '';
											?>
											<time datetime="' . esc_attr(get_the_date('c')) . '">
												<?php echo get_the_date(); ?>
											</time>
										</span>
										<?php
										break;
									case 'category':
										$this->renderMetaGridCategories();
										break;
									case 'tags':
										$this->renderMetaGridTags();
										break;
									case 'comments': ?>
										<span class="entry-comments">
											<?php
											echo ( $settings['meta_remove_icons'] == '' ) ? '<i class="fa fa-comments"></i>' : '';
											echo comments_number( esc_html__( 'No comments', 'noo' ), esc_html__( '1 comment', 'noo' ), esc_html__( '% comments', 'noo' ) );
											?>
										</span>
										<?php
										break;
								endswitch;
							} // End foreach().
						} else{
							
							?>
								<span>
									<?php if ( get_post_format() == 'image' ): ?>
	                                    <i class="fa fa-file-image-o"></i>
									<?php elseif ( get_post_format() == 'video' ): ?>
	                                    <i class="fa fa-file-video-o"></i>
									<?php elseif ( get_post_format() == 'audio' ): ?>
	                                    <i class="fa fa-file-audio-o"></i>
									<?php elseif ( get_post_format() == 'gallery' ): ?>
	                                    <i class="fa fa-file-image-o"></i>
									<?php else: ?>
	                                    <i class="fa fa-pencil"></i>
									<?php endif; ?>
									<?php _e( 'under', 'noo' ) ?>
									<?php echo get_the_category_list( ', ' ); ?>
									<?php _e( 'by', 'noo' ) ?>
									<?php the_author_posts_link(); ?>
									<?php _e( 'on', 'noo' ) ?>
									<?php echo get_the_date( get_option( 'date_format' ) ); ?>
								</span>
							<?php
							
						}
					?>
					
				</p>
				<?php
			}// End if().
		}// End if().
	}
	/**
	 * Render content
	 */
	protected function renderContent() {
		$settings = $this->get_settings();
		if ( $settings['content_hide'] !== 'yes' ) { ?>
			<div class="loop-item-excerpt">
				<p>
					<?php
					if ( empty( $settings['excerpt_length'] ) ) {
						the_excerpt();
					} else {
						echo wp_trim_words( get_the_excerpt(), $settings['excerpt_length'] );
					}
					?>
				</p>
				<?php 
					// Button.
					$this->renderButton();
				?>
			</div>
			<?php
		}
	}
	/**
	 * Render button
	 */
	protected function renderButton() {
		$settings = $this->get_settings();
		if( $settings['noo_recent_news_btn'] == 'yes' && ! empty( $settings['noo_recent_news_btn'] ) ) { 
			 ?>
				<a class="btn-readmore" href="<?php echo get_the_permalink(); ?>" title="<?php echo $settings['noo_recent_news_btn_text']; ?>"><?php echo $settings['noo_recent_news_btn_text']; ?></a>
			<?php 
		}
	}
	private function noo_recent_news_style() {
		// Tab.
		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Options', 'fitsica' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		// Columns margin.
		$this->add_responsive_control(
			'style_columns_margin',
			[
				'label'     => esc_html__( 'Columns Space', 'fitsica' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 15,
				], 
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .noo-recent-news' => 'margin: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .noo-recent-news .noo-pl-item-wrap '   => 'padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_section();
	}
}