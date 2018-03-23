<?php 
	// Related Projects
	if(ot_get_option('related_portfolio_projects', 'on') == 'on'){ 

		$projects = boc_get_related_portfolio_items($post->ID); 
		
		if($projects->have_posts()){ 
		
			$str = '';
			
			$portfolio_style = ot_get_option('portfolio_style') ? ot_get_option('portfolio_style') : 'type1';
			// Img Hover Effect
			$portfolio_img_hover_effect = 'img_hover_effect'.ot_get_option('portfolio_img_hover_effect','2');
						
			$str.='<div class="h20"></div>
			
			<div class="container">
				<div class="info_block">
					<div class="h40 clear"></div>
					<h3 class="boc_heading center"><span>'.__("Related Portfolio Items", "Fortuna").'</span></h3>
					<div class="boc_divider_holder"><div class="boc_divider  " style="margin-top: 20px;margin-bottom: 50px;width: 60px;margin-left: auto; margin-right: auto;height: 2px;background: #eeeeee;"></div></div>
					<div class="portfolio_carousel_holder '.($portfolio_style==6 ? "padded_carousel" : "").'">
						
						<div id="portfolio_carousel">';
							
							while($projects->have_posts()): $projects->the_post(); 
							if(has_post_thumbnail()): 
							
								$taxonomy = 'portfolio_category';
								$terms = get_the_terms( $post->ID , $taxonomy );
								$cats = array();
								
								if (! empty( $terms ) ) :
									foreach ( $terms as $term ) {
										
										$link = get_term_link( $term, $taxonomy );
										if ( !is_wp_error( $link ) )
											$cats[] = esc_html($term->name);
									}
								endif;
							
								$str.=
									'
									<div class="info_item boc_animate_when_almost_visible boc_right-to-left">
										<a href="'.esc_url(get_permalink()).'" title="" class="pic_info_link_'.esc_attr($portfolio_style).'">
											<div class="pic_info '.esc_attr($portfolio_style).'">
												<div class="pic '.esc_attr($portfolio_img_hover_effect).'"><div class="plus_overlay"></div><div class="plus_overlay_icon"></div>'.get_the_post_thumbnail($projects->post->ID, 'boc_medium').'<div class="img_overlay_icon"><span class="portfolio_icon icon_'.getPortfolioItemIcon($projects->post->ID).'"></span></div></div>
												<div class="info_overlay">
													<div class="info_overlay_padding">
														<div class="info_desc">
															<span class="portfolio_icon icon_'.getPortfolioItemIcon($projects->post->ID).'"></span>
															<h3>'.esc_html(get_the_title()).'</h3>
															<p>'.implode(' / ', $cats).'</p>
														</div>
													</div>
												</div>
											</div>
										</a>
									</div>
								';
								
								
							endif; endwhile;
							
							
							$autoplay_interval = 6000;
							$items_slided = 1;			
							
							$str.='
						</div>
					</div>
				</div>

				<div class="h40 clear"></div>
				<div class="h40"></div>

				<script type="text/javascript">

					jQuery(document).ready(function($) {

						$("#portfolio_carousel").owlCarousel({
								items: 				3,
								nav: 				true,
								autoHeight: 		false,
								navText:			["<span class=\'icon icon-arrow-left7\'></span>","<span class=\'icon icon-arrow-right7\'></span>"],
								slideBy: 			1,
								dots:				false,
								navRewind: 			false,
								rtl : 				' . (is_rtl() ? 'true' : 'false') . ',
								margin:30,
								responsive:{
									0:{
									  items:1,
									},
									480:{
									  items:1,
									  margin:20,
									},
									769:{
									  items: 3
									}
								}
						});
					});			
				
				</script>
			  </div>
			</div>';

			echo $str; 
	
		} 

	} // RELATED PROJECTS :: END ?>