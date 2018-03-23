<?php

/**
	Image Box
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_image_box' ) ) {
	function shortcode_boc_image_box($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_image_box', $atts );
		extract( $atts );

		// Img Hover Effect
		if ($img_hover_effect){
			$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
		}

		// Display Type
		$design_style = 'type'.$display_style;
		
		// Pic
		$img = '';
		if($picture){
			$vc_image = wp_get_attachment_image($picture,'boc_medium');
			
			// If not passed via VC, we use default Person Icon
			if($vc_image){
				$img = $vc_image;
			}else {
				$img = '';
			}
		}else {
				$img = '';
		}
		
		
		$feat_img = '';
		
		// Image
		if($picture){		
			if($img_width && $img_height){
				$featured_img_url	= wp_get_attachment_url( $picture );
				$thumbnail_hard_crop = true;
				$new_feat_img_url = aq_resize( $featured_img_url, intval($img_width), intval($img_height), $thumbnail_hard_crop );
				$feat_img = '<img src="'.esc_url($new_feat_img_url).'" alt="'.esc_attr(get_post_meta(get_post_thumbnail_id(), "_wp_attachment_image_alt", true)).'">';
			}else{
				$feat_img = wp_get_attachment_image($picture,'boc_medium');
			}
		}
		
		$cursor_default_style = '';

		if($href == '') {
			$href = 'javascript:void(0)';
			$cursor_default_style = ' style="cursor: default;"'; 
		}else {
			$href = esc_url($href);
		}
		
		$str =
			'
				<a target="'.$target.'" href="'.$href.'" '.$cursor_default_style.' title="" class="pic_info_link_'.esc_attr($design_style).'">
				  <div class="boc_image_box portfolio_animator_class '.esc_attr($css_classes).' '.($css_items_animation ? 'boc_animate_when_almost_visible boc_'.esc_attr($css_items_animation) : '').'">
					<div class="pic_info '.esc_attr($design_style).'">
						<div class="pic '.esc_attr($img_hover_effect).'"><div class="plus_overlay"></div><div class="plus_overlay_icon"></div>
						'.$feat_img.'
						<div class="img_overlay_icon"><span class="portfolio_icon icon_link"></span></div></div>
						<div class="info_overlay">
							<div class="info_overlay_padding">
								<div class="info_desc">
									<span class="portfolio_icon icon_link"></span>				
									<h3>'.wp_kses_post($title).'</h3>
									<p>'.wp_kses_post($subtitle).'</p>
								</div>
							</div>
						</div>
					</div>
				  </div>
				</a>
			';


		return $str;
	}
	
	add_shortcode('boc_image_box', 'shortcode_boc_image_box');
}



// Map Shortcode in Visual Composer
vc_map( array(
   "name" => __("Image Box", 'Fortuna'),
   "base" => "boc_image_box",
   "category" => "Fortuna Shortcodes",
   "icon" 	=> "boc_img_box",
   "weight"	=> 44,
   "params" => array(
		array(
			"type" 			=> "attach_image",
			"heading" 		=> __("Image", 'Fortuna'),
			"param_name" 	=> "picture",
			"description" 	=> __("Add an Image", 'Fortuna'),
			"group"			=> "Box Settings",
		),   
   		array(
			"type"			=> "textfield",
			"heading"		=> __("Image Box Title", 'Fortuna'),
			"admin_label"	=> true,
			"param_name"	=> "title",
			"value"			=> "Title",
			"group"			=> "Box Settings",			
		),
		array(
			"type"			=> "textfield",
			"heading"		=> __("Image Box Sub Title", 'Fortuna'),
			"param_name"	=> "subtitle",
			"value"			=> "Sub Title",
			"group"			=> "Box Settings",			
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("URL Link", 'Fortuna'),
			"param_name" 	=> "href",
			"value" 		=> "",
			"description" 	=> __("Enter a link if you want one for the image box", 'Fortuna'),
			"group"			=> "Box Settings",			
		),
		array(
			"type" 		=> "dropdown",
			"heading" 		=> __("Target", 'Fortuna'),
			"param_name" 	=> "target",
			"value" 		=> array('_self','_blank'),
			"dependency"	=> array(
				'element'	=> "href",
				'not_empty'	=> true,
			), 
			"description" 	=> __("Pick '_blank' if you want the link to open in a new tab.", 'Fortuna'),
			"group"			=> "Box Settings",			
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
			"description"	=> __("Animation will be applied to your Image box", "Fortuna"),
			"group"			=> "Box Settings",
		),	
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
			"group"			=> "Box Settings",
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
			'type'			=> "textfield",
			"heading"		=> __( "Overwrite Image Width", 'Fortuna'),
			'param_name'	=> "img_width",
			'std'			=> "",
			'description'	=> __( "Enter a width in pixels if you want to overwrite the default (600x380). Both W and H should be changed to define a new dimension. Leave empty for default.", 'Fortuna'),
			'group'			=> "Design",
		),
		array(
			'type'			=> "textfield",
			"heading"		=> __( "Overwrite Image Height", 'Fortuna'),
			'param_name'	=> "img_height",
			'std'			=> "",
			'description'	=> __( 'Enter a height in pixels if you want to overwrite the default (380). Both W and H should be changed to define a new dimension. Leave empty for default.', 'Fortuna'),
			'group'			=> "Design",
		),		
   )
));	