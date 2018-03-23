<?php

/**
	Post Grid
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_post_grid' ) ) {
	function shortcode_boc_post_grid($atts, $content = null) {
		
		$atts = vc_map_get_attributes('boc_post_grid', $atts );
		extract( $atts );

		// Img Hover Effect
		if ($img_hover_effect) {
			$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
		}

		
		$args = array(
				'post_type' 		=> array($post_type),
			//	'category_name' => ($category_slug ? $category_slug : null), ,  # MOVED TO BOTTOM IF Statement 
				'orderby'		=> $order_by,
				'order'			=> $order,
				'showposts' 		=> $limit,
		);
		
		//  WPML compatibility
		if($category_slug) {
			$args['tax_query'] = array(
			  array(
				'taxonomy' => 'category',
				'field' => 'slug',
				'terms' => (explode( "," , str_replace(' ', '', $category_slug))),
			  )
			);
		}		
		
		$wp_query = new WP_Query($args);
		
		
		$str = '';

		if ( $wp_query->have_posts()){

			$grid_id = md5(serialize($atts));

			$str .= '<div id = "post_grid_'. $grid_id .'" class="grid_holder '.($css_items_animation ? "animated_items" : "").'">';
			
			while( $wp_query->have_posts() ) : $wp_query->the_post();
				//$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($wp_query->post->ID), 'boc_medium'); 
				$excerpt = get_the_excerpt();
				$short_excerpt = boc_limitString($excerpt,$excerpt_char_limit);
				$str .='<div class="col span_1_of_'.$columns.' info_item isotope_element">
							<div class="post_item_block '.($css_items_animation ? 'boc_anim_hidden boc_'.esc_attr($css_items_animation) : '').' '.($is_boxed ? "boxed" : "").'">';
				$id = $wp_query->post->ID;

				// Show Featured Image
				if( function_exists( 'get_post_format' ) && $show_pic){

					if($img_cropping){
						
						if( ($img_cropping == 'custom') && $img_width && $img_height){

							$featured_img_url	= wp_get_attachment_url( get_post_thumbnail_id() );
							$thumbnail_hard_crop = true;
							$feat_img = aq_resize( $featured_img_url, intval($img_width), intval($img_height), $thumbnail_hard_crop );
							
						}else{
							// $img_cropping == 'cropped'
							$feat_img_arr = wp_get_attachment_image_src( get_post_thumbnail_id(), 'boc_medium');
							$feat_img = $feat_img_arr[0];
						}
					} else {
							$feat_img_arr = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
							$feat_img = $feat_img_arr[0];				
					}
					// If image isset
					if($feat_img){					
						$str .='<div class="pic '.esc_attr($img_hover_effect).'"><a href="'. get_permalink().'"><img src="'.esc_url($feat_img).'"/><div class="img_overlay"><span class="hover_icon icon_plus"></span></div></a></div>'; 
					}
				}

				$str .= '<div class="post_item_desc dark_links">';

				// Show date according to style set (1 = side, 2 = below title)
				$str .= (($show_date == 1) ? 	'<div class="small_post_date_left">
												<span class="small_day">'.get_the_date('j').'</span>
												<span class="small_month">'.get_the_date('M').'</span>
											</div>' : '');
									
				$str .= (($show_date == 1) ? '<div class="small_post_desc_right">' : '');			
				
				$str .='<h4><a href="'. get_permalink().'">'.esc_html(get_the_title()).'</a></h4>';
				$str .= (($show_date == 2) ? '<div class="small_post_date"><span class="icon icon-calendar2"></span> &nbsp; '.get_the_date().'</div>' : "");
				$str .= ($show_excerpt ? '<p>'.esc_html($short_excerpt).' '.($add_dots ? "..." : "").'</p>': '');
				$str .= ($show_read_more ? '<a href="'. get_permalink().'" class=\''.boc_more_link_classes_sh($read_more_style).'\'>'.__('Read more','Fortuna').'</a>' : '');			
				$str .= (($show_date == 1) ? '</div>' : '');

				$str .='		</div>
				</div>
				</div>';
			endwhile;  // close the Loop			
			
			wp_reset_postdata();

			$str.='
			</div>
			<div class="h10 clear"></div>';
		}
		
		return $str;

	}
	
	add_shortcode('boc_post_grid', 'shortcode_boc_post_grid');
}


// Map Shortcode in Visual Composer
vc_map( array(
   "name" => __("Post Grid", 'Fortuna'),
   "base" => "boc_post_grid",
   "category" => "Fortuna Shortcodes",
   "icon" 	=> "boc_post_grid", 
   "weight"	=> 60,
   "params" => array(
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Total Items Limit", 'Fortuna'),
			"param_name" 	=> "limit",
			"std"			=> "9",
			"description" 	=> __("How many items you want to limit your grid to", 'Fortuna'),
			"group"			=> "Grid Settings",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Columns", 'Fortuna'),
			"param_name" 	=> "columns",
			"value" 		=> Array(2,3,4,5),
			"std"			=> "3",
			"description" 	=> __("How many columns you want your items displayed in.", 'Fortuna'),
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
			"heading" 		=> __("Post Type Filter", 'Fortuna'),
			"param_name" 	=> "post_type",
			"admin_label"	=> true,
			"value" 		=> "post",
			"description" 	=> __("Filter only a certain Post Type. Could be used for Custom Post Types as well. Default is 'post'.", 'Fortuna'),
			"group"			=> "Query",
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Category Slug Filter", 'Fortuna'),
			"param_name" 	=> "category_slug",
			"value" 		=> "",
			"description" 	=> __("Filter only a certain Category Slug from the specified Post Type. You can list more than one (comma-separated).", 'Fortuna'),
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
			"type" 			=> "checkbox",
			"heading" 		=> __("Boxed Post Item", 'Fortuna'),
			"param_name" 	=> "is_boxed",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Adds a bordered container to your Post Items. (Slightly changes the styling of the Post Date)", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Post Date", 'Fortuna'),
			"param_name" 	=> "show_date",
			"value" 		=> Array("Don't show" => 0, "Left Date/Month" => 1, "Below Title" =>2 ),
			"description" 	=> __("Select Post Date Style", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Show Excerpt", 'Fortuna'),
			"param_name" 	=> "show_excerpt",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want an excerpt from each post to be displayed.", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Excerpt Character limit", 'Fortuna'),
			"param_name" 	=> "excerpt_char_limit",
			"value" 		=> "64",
			"description" 	=> __("How many characters from a post you wish to be shown as excerpt. Play around with the number for best results.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_excerpt",
					'not_empty'	=> true,
			),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Add dots to excerpt", 'Fortuna'),
			"param_name" 	=> "add_dots",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want to add '...' to the excerpt end", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_excerpt",
					'not_empty'	=> true,
			),
			"group"			=> "Design",
		),
		array(
			"type" 			=> "checkbox",
			"heading" 		=> __("Show 'Read More' link", 'Fortuna'),
			"param_name" 	=> "show_read_more",
			"value" 		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want a 'Read More' link below each item in the carousel.", 'Fortuna'),
			"group"			=> "Design",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("'Read More' link style", 'Fortuna'),
			"param_name" 	=> "read_more_style",
			"value"			=> Array(__("Simple", "Fortuna") => '1', __("Round Icon", "Fortuna") => '2', __("Square Icon", "Fortuna") => '3' ),
			"description" 	=> __("Pick a style for your 'Read More' link.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_read_more",
					'value'		=> "yes",
			),
			"std"			=> "2",
			"group"			=> "Design",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Show Featured Image", 'Fortuna'),
			"param_name" 	=> "show_pic",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"std"			=> "yes",
			"description" 	=> __("Set to Yes if you want the Featured Images of displayed posts to be shown", 'Fortuna'),
			"group"			=> "Images",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Hover Image Effect", 'Fortuna'),
			"param_name" 	=> "img_hover_effect",
			"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Side" => 3, "Spin" => 4),
			"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_pic",
					'value'		=> "yes",
			),
			"group"			=> "Images",
		),
		
		array(
			'type'			=> "dropdown",
			"heading"		=> __( "Image Cropping", 'Fortuna'),
			'param_name'	=> "img_cropping",
			"value"			=> Array("Original Size (No Crop)" => '', "Cropped" => 'cropped', "Custom" => 'custom'),
			'description'	=> __( "Preferable value is 'Cropped'. It uses the Theme Default Preset (600x380). For Grid effect use the 'Original Size' option.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "show_pic",
					'value'		=> "yes" ,
			),
			'group'			=> "Images",
		),			
		
		array(
			'type'			=> "textfield",
			"heading"		=> __("Overwrite Image Width", 'Fortuna'),
			'param_name'	=> "img_width",
			'value'			=> "",
			'description'	=> __( "Enter a width in pixels if you want to overwrite the default (460). Leave empty for default.", 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "img_cropping",
					'value'		=> "custom" ,
			),
			'group'			=> "Images",
		),
		array(
			'type'			=> "textfield",
			"heading"		=> __( "Overwrite Image Height", 'Fortuna'),
			'param_name'	=> "img_height",
			'value'			=> "",
			'description'	=> __('Enter a height in pixels if you want to overwrite the default (290). Leave empty for default.', 'Fortuna'),
			"dependency"	=> Array(
					'element'	=> "img_cropping",
					'value'		=> "custom" ,
			),
			'group'			=> "Images",
		),
   )
));