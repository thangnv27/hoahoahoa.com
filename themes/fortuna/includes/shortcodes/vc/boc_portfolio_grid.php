<?php

/**
	Portfolio Grid
**/

if ( ! function_exists('is_plugin_active')){ 
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // load is_plugin_active() function if no available
}

if(is_plugin_active('fortuna_portfolio_cpt/fortuna_portfolio_cpt.php')){ 

	// Register Shortcode
	if( ! function_exists( 'shortcode_boc_portfolio_grid' ) ) {
		function shortcode_boc_portfolio_grid($atts, $content = null) {
			
			$atts = vc_map_get_attributes('boc_portfolio_grid', $atts );
			extract( $atts );

			// Img Hover Effect
			if ($img_hover_effect){
				$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
			}

			// Display Type
			$portfolio_style = 'type'.$display_style;
			
			$portfolio_items_spacing = $spacing;

			$projects = boc_get_portfolio_items($limit, $order_by, $order, $category_name);
			
			$str = '';

			if($projects->have_posts()){

				$grid_id = md5(serialize($atts));

				
				if($filter_links){
				
					$portfolio_category = get_terms('portfolio_category');
					
					if($portfolio_category){
						
						$str.='
							<div class="portfolio_inline_filter">
								<ul class="grid_filter_inline"  data-option-key="filter" '.($center_filter? 'style="text-align: center;"' :'').'>
								'.($show_filter_label? '<li class="portfolio_filter_label">'.__("Filter", "Fortuna").'</li>	': '').'
									<li><div data-option-value="*" class="current_portfolio_item">'.__("All", "Fortuna").'</div></li>';
						

							if($category_name!=""){
								$cats_to_show = (explode( "," , str_replace(' ', '', $category_name)));
								
								// WPML compatibility :: START
								foreach ($cats_to_show as $cat) {
									$terms_for_wpml = get_term_by('slug', $cat, 'portfolio_category');
									$terms_for_wpml = apply_filters('wpml_object_id', $terms_for_wpml->term_id, 'custom taxonomy', true);
									$cats_to_show []= get_term_by('term_id', $terms_for_wpml, 'portfolio_category')->slug;
								}
								// WPML compatibility :: END
							}
							foreach($portfolio_category as $portfolio_cat){
								// Show only cats that are filtered
								if(($category_name=="") || (($category_name!="") && in_array($portfolio_cat->slug, $cats_to_show))){			
									$str.='<li><div data-option-value=".'.esc_attr($portfolio_cat->slug).'">'.esc_html($portfolio_cat->name).'</div></li>';
								}
							}	        
						
						$str.='				
								</ul>
							</div>
							';
					}
				}

				$str.='

					<div id="portfolio_grid_'.$grid_id.'" class="grid_holder '.($css_items_animation ? "animated_items" : "").' '.esc_attr($portfolio_items_spacing).' '.esc_attr($css_classes).'">';
							while($projects->have_posts()): $projects->the_post();
								if(has_post_thumbnail()):
				
									$data_types = '';
									$cats = array();
									
									$item_cats = get_the_terms($projects->post->ID, 'portfolio_category');
									if($item_cats):
										foreach($item_cats as $item_cat) {
											$data_types .= $item_cat->slug . ' ';
											$cats[] = $item_cat->name;
										}
									endif;
									
									// Feat. Image
									if($fixed_size) {
										if($img_width && $img_height){
											$featured_img_url	= wp_get_attachment_url( get_post_thumbnail_id($projects->post->ID) );
											$thumbnail_hard_crop = true;
											$new_feat_img_url = aq_resize( $featured_img_url, intval($img_width), intval($img_height), $thumbnail_hard_crop );
											$feat_img = '<img src="'.esc_url($new_feat_img_url).'" alt="'.esc_attr(get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true)).'">';
										}else{
											$alt = get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true);
											$title = get_the_title(get_post_thumbnail_id());
											$feat_img = get_the_post_thumbnail($projects->post->ID, 'boc_medium', array("alt"=> $alt,"title"=>$title));
										}
									}else {
										$alt = get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true);
										$title = get_the_title(get_post_thumbnail_id());
										$feat_img = get_the_post_thumbnail($projects->post->ID, 'full', array("alt"=> $alt,"title"=>$title));
									}
									
									
									$str.=
										'
										<div class="col span_1_of_'.esc_attr($columns).' info_item isotope_element '.esc_attr($data_types).'">
											<a href="'.get_the_permalink().'" title="" class="pic_info_link_'.esc_attr($portfolio_style).'">
											  <div class="portfolio_animator_class '.($css_items_animation ? 'boc_anim_hidden boc_'.esc_attr($css_items_animation) : '').'">
												<div class="pic_info '.esc_attr($portfolio_style).'">
													<div class="pic '.esc_attr($img_hover_effect).'"><div class="plus_overlay"></div><div class="plus_overlay_icon"></div>
													'.$feat_img.'
													<div class="img_overlay_icon"><span class="portfolio_icon icon_'.esc_attr(getPortfolioItemIcon($projects->post->ID)).'"></span></div></div>
													<div class="info_overlay">
														<div class="info_overlay_padding">
															<div class="info_desc">
																<span class="portfolio_icon icon_'.esc_attr(getPortfolioItemIcon($projects->post->ID)).'"></span>				
																<h3>'.get_the_title().'</h3>
																<p>'.implode(' / ', $cats).'</p>
															</div>
														</div>
													</div>
												</div>
											  </div>
											</a>
										</div>
										';

								endif; 
							endwhile;
							
							wp_reset_postdata();
							
							$str.='
				</div>
				
			<div class="clear"></div>';
			}
			
			return $str;

		}
		
		add_shortcode('boc_portfolio_grid', 'shortcode_boc_portfolio_grid');
	}



	// Map Shortcode in Visual Composer
	vc_map( array(
	   "name" => __("Portfolio Grid", 'Fortuna'),
	   "base" => "boc_portfolio_grid",
	   "category" => "Fortuna Shortcodes",
	   "icon" 	=> "boc_portfolio_grid",
	   "weight"	=> 56,
	   "params" => array(
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Columns", 'Fortuna'),
				"param_name" 	=> "columns",
				"value" 		=> Array(2,3,4),
				"std"			=> "3",
				"description" 	=> __("How many columns you want your items displayed in.", 'Fortuna'),
				"group"			=> "Grid Settings",
			),	   
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Enable Filter Links", 'Fortuna'),
				"param_name" 	=> "filter_links",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Set to Yes if you want Category Filter Links above your grid", 'Fortuna'),
				"group"			=> "Grid Settings",
			),
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Center Filter Options", 'Fortuna'),
				"param_name" 	=> "center_filter",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Set to Yes if you want to center Your Filter", 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "filter_links",
						'not_empty'	=> true,
				),
				"group"			=> "Grid Settings",
			),
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Show Filter Label", 'Fortuna'),
				"param_name" 	=> "show_filter_label",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Show 'Filter' label in front of filter", 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "filter_links",
						'not_empty'	=> true,
				),
				"group"			=> "Grid Settings",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Item Spacing", 'Fortuna'),
				"param_name" 	=> "spacing",
				"value" 		=> array('Big Spacing'=>'big_spacing','Small Spacing'=>'small_spacing','No Spacing'=>'no_spacing'),
				"std"			=> 'small_spacing',
				"description" 	=> __("Pick a spacing between the items in the grid", 'Fortuna'),
				"group"			=> "Grid Settings",
			),
			array(
				"type"		=> "dropdown",
				"heading"	=> __("CSS Animation", "Fortuna"),
				"param_name"	=> "css_items_animation",
				"admin_label"	=> true,				
				"value"			=> array(
					__("None", "Fortuna")					=> '',
					__("Top to bottom", "Fortuna")			=> "top-to-bottom",
					__("Bottom to top", "Fortuna")			=> "bottom-to-top",
					__("Left to right", "Fortuna")			=> "left-to-right",
					__("Right to left", "Fortuna")			=> "right-to-left",
					__("Fade In", "Fortuna")				=> "fade-in"),
				"description"	=> __("Animation will be applied to each single Item in your grid for a better effect", "Fortuna"),
				"group"			=> "Grid Settings",
			),	
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Extra class name", 'Fortuna'),
				"param_name" 	=> "css_classes",
				"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
				"group"			=> "Grid Settings",
			),
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Total Items Limit", 'Fortuna'),
				"param_name" 	=> "limit",
				"std"			=> "9",
				"description" 	=> __("How many items you want to limit your grid to", 'Fortuna'),
				"group"			=> "Query",
			),			
			array(
				"type" 			=> "textfield",
				"heading" 		=> __("Category Name Filter (slug)", 'Fortuna'),
				"param_name" 	=> "category_name",
				"value" 		=> "",
				"description" 	=> __("Filter only a certain Category from your portfolio grid (comma-separated)", 'Fortuna'),
				"group"			=> "Query",
			),				
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("'Order By' Clause", 'Fortuna'),
				"param_name" 	=> "order_by",
				"value" 		=> array('none','ID','title','name','date','rand'),
				"std"			=> 'date',
				"description" 	=> __("Order results by a certain field. <a href='http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters' target='_blank'>More on the available WP options here</a>.", 'Fortuna'),
				"group"			=> "Query",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("'Order' Clause", 'Fortuna'),
				"param_name" 	=> "order",
				"value" 		=> array('DESC','ASC'),
				"description" 	=> __("Order results in a Descending or Ascending order. <a href='http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters' target='_blank'>More on the order here</a>.", 'Fortuna'),
				"group"			=> "Query",
			),
			array(
				"type" 			=> "dropdown",
				"heading" 		=> __("Display Style", 'Fortuna'),
				"param_name" 	=> "display_style",
				"value" 		=> array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'),
				"description" 	=> __("Pick a Display Style. Explore them all to see what best fits your needs.", 'Fortuna'),
				"group"			=> "Design",
			),
			array(
				"type"			=> 'dropdown',
				"heading" 		=> __("Hover Image Effect", 'Fortuna'),
				"param_name" 	=> "img_hover_effect",
				"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Side" => 3, "Spin" => 4),
				"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
				"group"			=> "Design",
			),
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Fixed Image Size", 'Fortuna'),
				"param_name" 	=> "fixed_size",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Set to Yes if you want to use the fixed size (600x380) image preset or change the default dimension but still use a uniform proportion.", 'Fortuna'),
				"group"			=> "Design",
			),		
			array(
				'type'			=> "textfield",
				"heading"		=> __( "Overwrite Image Width", 'Fortuna'),
				'param_name'	=> "img_width",
				'value'			=> "",
				'description'	=> __( "Enter a width in pixels if you want to overwrite the default (600x380). Both W and H should be changed to define a new dimension. Leave empty for default.", 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "fixed_size",
						'not_empty'	=> true,
				),
				'group'			=> "Design",
			),
			array(
				'type'			=> "textfield",
				"heading"		=> __( "Overwrite Image Height", 'Fortuna'),
				'param_name'	=> "img_height",
				'value'			=> "",
				'description'	=> __( 'Enter a height in pixels if you want to overwrite the default (380). Both W and H should be changed to define a new dimension. Leave empty for default.', 'Fortuna'),
				"dependency"	=> Array(
						'element'	=> "fixed_size",
						'not_empty'	=> true,
				),
				'group'			=> "Design",
			),
	   )
	));	
}