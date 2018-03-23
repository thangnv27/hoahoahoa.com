<?php
get_header(); ?>
	
	
	<div class="container">
		<div class="section">
							
			<?php
				$limit = ot_get_option('portfolio_items_per_page',9);
				$args = array( 'order' => 'ASC', 'posts_per_page' => (int)$limit );
				global $wp_query;
				$args = array_merge( $wp_query->query_vars, $args );
				query_posts( $args );	
		
				$portfolio_style = ot_get_option('portfolio_style') ? ot_get_option('portfolio_style') : 'type1';
				
				
				$portfolio_items_spacing = ot_get_option('portfolio_items_spacing','small_spacing');
			?>
				
				<div class="grid_holder animated_items <?php echo esc_attr($portfolio_items_spacing); ?>">
			<?php	
				while(have_posts()): the_post();
					if(has_post_thumbnail()):
					
					
						$data_types = '';
						$cats = array();
						
						$item_cats = get_the_terms($post->ID, 'portfolio_category');
						if($item_cats):
						foreach($item_cats as $item_cat) {
							$data_types .= $item_cat->slug . ' ';
							$cats[] = $item_cat->name;
						}
						endif;

						$portfolio_img_size = ot_get_option('portfolio_img_size','boc_medium');
						?>							
							<div class="col span_1_of_3 info_item isotope_element <?php echo esc_attr($data_types);?>">
								<a href="<?php the_permalink(); ?>" title="" class="pic_info_link_<?php echo esc_attr($portfolio_style);?>">
								  <div class="portfolio_animator_class boc_anim_hidden boc_right-to-left">
									<div class="pic_info <?php echo esc_attr($portfolio_style);?>">
										<div class="pic <?php echo esc_attr($portfolio_img_hover_effect);?>"><div class="plus_overlay"></div><div class="plus_overlay_icon"></div>
										<?php echo get_the_post_thumbnail( $post->ID, $portfolio_img_size ); ?> 
										<div class="img_overlay_icon"><span class="portfolio_icon icon_<?php echo esc_attr(getPortfolioItemIcon($post->ID));?>"></span></div></div>
										<div class="info_overlay">
											<div class="info_overlay_padding">
												<div class="info_desc">
													<span class="portfolio_icon icon_<?php echo esc_attr(getPortfolioItemIcon($post->ID));?>"></span>				
													<h3><?php the_title(); ?></h3>
													<p><?php echo implode(' / ', $cats);?></p>
												</div>
											</div>
										</div>
									</div>
								  </div>
								</a>
							</div>							
	
				<?php 
					endif; 
				endwhile; 
				?>
					<div class="h20 clear"></div>
				</div>
				<div class="h20 clear"></div>
				<div class="h20 clear"></div>
				
		<?php boc_pagination(); ?>
		</div>
	
	</div>
	
<?php get_footer(); ?>