<?php
/**
 * Template Name: Portfolio Page
 *
 * A Full Width custom page template without sidebar.
 * @package WordPress
 */

get_header(); ?>


	<!-- Portfolio -->

	<script type="text/javascript">
		jQuery(document).ready(function($){
			
			if(!boc_is_mobile) {
				$('#portfolio_filter').on('mouseenter touchstart', function(){ 
					 $('#filter_list').stop(false, true).slideDown({
						duration:500,
						easing:"easeOutExpo"});	
				});
				$('#portfolio_filter').on('mouseleave', function(){
					 $('#filter_list').stop(false, true).slideUp({
						duration:200,
						easing:"easeOutExpo"});
				});
			}else {
				
				$('#portfolio_filter').on('click', function(){
					$('#filter_list').stop(false, true).slideDown({
						duration:500,
						easing:"easeOutExpo"});	
				});
			}
		});

		// Custom filters function
		jQuery(window).load(function(){
			jQuery(function($){

				var $container = $('.grid_holder');

				$container.isotope({
				  itemSelector : '.isotope_element',	
				});

				var $optionSets = $('#filter_list'),
				$optionLinks = $optionSets.find('li div');

				$optionLinks.click(function(){
					var selector = $(this).attr('data-option-value');

					$container.isotope({ filter: selector });

					$("#current_filter").html($(this).html());
					$('#filter_list').stop(false, true).slideUp({
						duration:100,
						easing:"easeOutExpo"
					});
					return false;
				});
			});
		});
	</script>


	<div class="container">
		
		<div class="section portfolio_section">
				
				<?php
				$portfolio_category = get_terms('portfolio_category');
				
				if($portfolio_category): ?>			
						<div id="portfolio_filter">
							<span id="current_filter"><?php _e('All', 'Fortuna');?></span>
							<ul id="filter_list"  data-option-key="filter">
							<?php foreach($portfolio_category as $portfolio_cat): ?>		
								<li><div data-option-value=".<?php echo esc_attr($portfolio_cat->slug); ?>"><?php echo esc_html($portfolio_cat->name); ?></div></li>
							<?php endforeach; ?>			        
								<li><div data-option-value="*"><?php _e('All', 'Fortuna');?></div></li>
							</ul>
						</div>			
				
				<?php endif; ?>

				<?php $portfolio_items_spacing = ot_get_option('portfolio_items_spacing','small_spacing');?>
		
				<div class="grid_holder animated_items <?php echo esc_attr($portfolio_items_spacing); ?>">
				<?php 

					$portfolio_style = ot_get_option('portfolio_style') ? ot_get_option('portfolio_style') : 'type1';
					// Img Hover Effect
					$portfolio_img_hover_effect = 'img_hover_effect'.ot_get_option('portfolio_img_hover_effect','2');

					
					$limit = (int)ot_get_option('portfolio_items_per_page',9);
					$portfolio_order = esc_html(ot_get_option('portfolio_order','DESC'));
					$portfolio_orderby = esc_html(ot_get_option('portfolio_orderby','date'));
					
					$gallery = boc_get_portfolio_items($limit, $portfolio_orderby, $portfolio_order, '', $paged);
		
					while($gallery->have_posts()): $gallery->the_post();
						if(has_post_thumbnail()):
		
							$data_types = '';
							$cats = array();
							
							$item_cats = get_the_terms($post->ID, 'portfolio_category');
							if($item_cats):
							foreach($item_cats as $item_cat) {
								$data_types .= $item_cat->slug . ' ';
								$cats[] = esc_html($item_cat->name);
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
		
					<?php endif; endwhile; ?>
				</div>
				
				<script type="text/javascript">
						// Resize filter box
						var new_w = jQuery("#filter_list").width() - 20;
						jQuery("#current_filter").css('width',new_w);
		
				</script>
		
		</div>
		<!-- Portfolio::END -->		
		
		<?php boc_pagination($gallery->max_num_pages, $range = 2); ?>
		
		<div class="h20"></div>
	</div>

<?php get_footer(); ?>