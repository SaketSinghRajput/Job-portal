<?php get_header(); ?>

<div class="container-wrap">
	
	<div class="main-content container-boxed max offset">		
		<div class="row">
			<div class="col span_9">
				<div id="search-results">
					<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
							<?php if( get_post_type($post->ID) == 'post' ){ ?>
                                <?php noo_get_layout( 'post/post', get_post_format()); ?>
							<?php } else if( get_post_type($post->ID) == 'page' ){ ?>
								<article class="result">
                                    <header class="entry-header">
                                        <h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    </header>
                                        <?php noo_content_meta(); ?>
									<?php if(get_the_excerpt()) the_excerpt(); ?>
									<hr/>
								</article><!--/search-result-->	
							<?php } else if( get_post_type($post->ID) == 'product' ){ ?>
								<article class="result">
									<?php if(has_post_thumbnail( $post->ID )) {	
										echo '<a href="'.get_permalink().'">'. get_the_post_thumbnail($post->ID, 'full', array('title' => '')).'</a>'; 
									} ?>
									<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> <small><?php echo __('Product', 'noo'); ?></small></h2>	
									<hr/>
								</article><!--/search-result-->	
							<?php } else { ?>
								<article class="result">
									<?php if(has_post_thumbnail( $post->ID )) {	
										echo '<a href="'.get_permalink().'">'.get_the_post_thumbnail($post->ID, 'full', array('title' => '')).'</a>'; 
									} ?>
									<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

									<?php if(get_the_excerpt()) the_excerpt(); ?>
									<hr/>
								</article><!--/search-result-->	
							<?php } ?>
						
					<?php endwhile;
					else: 
						echo "<p>" . __('No results found', 'noo') . "</p>"; 
					endif;?>
				
						
				</div><!--/search-results-->
				
				
				<?php if( get_next_posts_link() || get_previous_posts_link() ) { ?>
					<div id="pagination">
						<div class="prev"><?php previous_posts_link('&laquo; Previous Entries') ?></div>
						<div class="next"><?php next_posts_link('Next Entries &raquo;','') ?></div>
					</div>	
				<?php }?>
				
			</div><!--/span_9-->
			
			<div id="sidebar" class="col span_3 col_last">
				<?php get_sidebar(); ?>
			</div><!--/span_3-->
		
		</div><!--/row-->
		
	</div><!--/container-->

</div><!--/container-wrap-->

<?php get_footer(); ?>
