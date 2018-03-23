<?php

/**
	Image Gallery
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_img_gallery' ) ) {
	function shortcode_boc_img_gallery($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_img_gallery', $atts );
		extract( $atts );

		// Get Attachments
		$images = explode(",",$image_ids);
		if($images){
			
			// Img Hover Effect
			if ($img_hover_effect){
				$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
			}

			// generate checksum of $atts array
			$gallery_id = md5(serialize($atts));
			
			$str = '';
			$str .= '<div class="grid_holder mfp_gallery '.$css_classes.' '.$spacing.'" id="img_gallery_'.$gallery_id.'">';

			// Prep custom links array if we have it
			if('custom' == $img_link) {
				$custom_links = explode( ",", $custom_links);
			}
			
			// Walk images
			$count = 0;
			foreach ( $images as $img_id ){
				
				// Attachment VARS
				$att_img_link = wp_get_attachment_image_src( $img_id, 'full' );
				$att_img_url = wp_get_attachment_image_src( $img_id, ($fixed_size ? 'boc_medium' : 'full') );
				
				$img_attachment = boc_get_attachment_data($img_id);
				
				$att_img_title = $img_attachment['title'];
				$att_img_alt = $img_attachment['alt'];
				
				$att_img_caption = $img_attachment['caption'];
				$att_img_description =  $img_attachment['description'];
				
				// Image output
				$image_output = '<img src="'. esc_url($att_img_url[0]) .'" alt="'. ($show_img_alt ? esc_attr($att_img_alt) : "") .'" />';

				// Gallery Item
				$str .= '<div class="col span_1_of_'.esc_attr($columns).' info_item isotope_element">
							<div class="pic '.esc_attr($img_hover_effect).'">';
				
				// Lightbox link
				if ( 'lightbox' == $img_link ) {
					$str .= '			
							<a href="'. esc_url($att_img_link[0]) .'" class="mfp_popup_gal" title="'. ($show_img_title ? esc_attr($att_img_title) : "") .'">
								'.$image_output.'
								'.($show_hover_overlay ? "<div class='img_overlay'><span class='hover_icon icon_zoom'></span></div>" : "").'
							</a>';	
				}		
				// Custom Links
				elseif ('custom' == $img_link) {
					
					if(isset($custom_links[$count])){
						$custom_link = esc_url($custom_links[$count]);
					}else {
						$custom_link = "#";
					}
					
					if($custom_link == "#"){
						$custom_link = "javascript:void(0)";
					}
					
					$str .= '			
							<a href="'. $custom_link .'" target="'. esc_attr($target) .'" title="'. ($show_img_title ? esc_attr($att_img_title) : "") .'">
								'.$image_output.'
								'.($show_hover_overlay ? "<div class='img_overlay'><span class='hover_icon icon_zoom'></span></div>" : "").'
							</a>';
				}
				// None
				else {
					$str .= $image_output;
				}
				
				$str .= '	</div>';
				
				if( $show_caption_description && $att_img_caption ){
					$str .= '<h3 class="img_gallery_caption">'. $att_img_caption .'</h3>';
				}
				if( $show_caption_description && $att_img_description ){
					$str .= '<p class="img_gallery_description">'. $att_img_description	.'</p>';
				}
				
				$str .= '</div>';

				$count ++;
			}		
			
			$str .= '</div>';
			

			return $str;
		}
	}
	
	add_shortcode('boc_img_gallery', 'shortcode_boc_img_gallery');
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Image Gallery", 'Fortuna'),
	"base" => "boc_img_gallery",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_img_gallery",
	"weight"	=> 50,
	"params" => array(
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Columns", 'Fortuna'),
			"param_name" 	=> "columns",
			"value" 		=> Array(2,3,4,5),
			"std"			=> "3",
			"description" 	=> __("How many columns you want your items displayed in.", 'Fortuna'),
			"group"			=> "Settings",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Item Spacing", 'Fortuna'),
			"param_name" 	=> "spacing",
			"value" 		=> array('Big Spacing'=>'big_spacing','Small Spacing'=>'small_spacing','No Spacing'=>'no_spacing'),
			"std"			=> 'small_spacing',
			"description" 	=> __("Pick a spacing between the items in the grid", 'Fortuna'),
			"group"			=> "Settings",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Fixed Image Size", 'Fortuna'),
			"param_name" 	=> "fixed_size",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want to use the fixed size (600x380) image preset", 'Fortuna'),
			"group"			=> "Settings",
		),
		array(
			"type"			=> 'dropdown',
			"heading" 		=> __("Hover Image Effect", 'Fortuna'),
			"param_name" 	=> "img_hover_effect",
			"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Side" => 3, "Spin" => 4),
			"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
			"group"			=> "Settings",
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
			"group"			=> "Settings",
		),	
		array(
			"type"			=> "attach_images",
			"admin_label"	=> true,
			"heading"		=> __("Attach Images", 'Fortuna'),
			"param_name"	=> "image_ids",
			"description"	=> __('Select the images you want to include in your Slider.', 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> __( "Image Link", 'Fortuna'),
			"param_name"	=> "img_link",
			"value"			=> array(
				"None"				=> "none",
				"Lightbox Popup"	=> "lightbox",
				"Custom Links"		=> "custom",
			),
			"std"			=> "lightbox",
			"description"	=> __( "Do you want lightbox/links for your Gallery?", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> "exploded_textarea",
			"heading"		=> __("Custom Links", 'Fortuna'),
			"param_name"	=> "custom_links",
			"description"	=> __('Enter links for each separate image here (starting with "http://"). Divide each link with a line-break (Enter). For images without a link enter the "#" symbol.', 'Fortuna'),
			"dependency"	=> Array(
				'element'	=> "img_link",
				'value'		=> "custom",
			),
			"group"			=> "Gallery",
		),
		array(
			"type" 			=> "dropdown",
			"heading" 		=> __("Target", 'Fortuna'),
			"param_name" 	=> "target",
			"value" 		=> array('_self','_blank'),
			"description" 	=> __("Pick '_blank' if you want the Image link to open in a new tab.", 'Fortuna'),
			"dependency"	=> Array(
				'element'	=> "img_link",
				'value'		=> "custom",
			),
			"group"			=> "Gallery",
		),		
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Show Dark Overlay on Image Hover", 'Fortuna'),
			"param_name" 	=> "show_hover_overlay",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Show Dark Overlay with Zoom Icon upon hovering linked images", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Caption/Description below Image", 'Fortuna'),
			"param_name" 	=> "show_caption_description",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want to add the Caption & Desctiption fields below the image", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Image Title", 'Fortuna'),
			"param_name" 	=> "show_img_title",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the Title Attribute added to your images' links", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Image Alt", 'Fortuna'),
			"param_name" 	=> "show_img_alt",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want the Alt Attribute added to your image(s)", 'Fortuna'),
			"group"			=> "Gallery",
		),		
	)
));