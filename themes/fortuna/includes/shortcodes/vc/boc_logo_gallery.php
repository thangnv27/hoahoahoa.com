<?php

/**
	Logo Gallery
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_logo_gallery' ) ) {
	function shortcode_boc_logo_gallery($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_logo_gallery', $atts );
		extract( $atts );

		// Get Attachments
		$images = explode(",",$image_ids);

		// Custom Links
		if ($add_links) {
			$custom_links = explode( ',', $custom_links);
		}
		
		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation !== '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation .'';
		} 
		
		if($images){

			// Img Hover Effect
			if ($img_hover_effect){
				$img_hover_effect = ' img_hover_effect'.$img_hover_effect;
			}

			// generate checksum of $atts array
			$gallery_id = md5(serialize($atts));
			
			$str = '<div class="logo_gallery '.($left_border ? " left_border" : "").'">';
			$str .= '<div class="grid_holder '.$css_classes.' '.$spacing.'" id="img_gallery_'.$gallery_id.'">';

			$count = 0;
			foreach ( $images as $img_id ){

				// Attachment VARS
				$att_img_url = wp_get_attachment_image_src( $img_id, ($fixed_size ? 'boc_medium' : 'full') );
				$att_img_title = get_the_title($img_id);
				$alt = get_post_meta($img_id, '_wp_attachment_image_alt', true);
				
				// Image output
				$image_output = '<img src="'. esc_url($att_img_url[0]) .'" alt="'.$alt.'"/>';
				
				// Gallery Item
				$str .= '<div class="col span_1_of_'.esc_attr($columns).' info_item isotope_element '.esc_attr($css_animation_classes).'">
							<div class="pic '.esc_attr($img_hover_effect).'">';

				if ($add_links){
					$att_img_link = !empty($custom_links[$count]) ? esc_url($custom_links[$count]) : '#';
					if ( $att_img_link == '#' ) {
						$att_img_link = "javascript:void(0)";
					}
					$str .= '
						<a href="'. $att_img_link .'" target="'. esc_attr($target) .'" '. ($show_img_title ? ' class="tooltipsy" original-title="'.esc_attr($att_img_title).'"' : '').'>
							'.$image_output.'
						</a>';
				}else {
					$str .= '
						<a href="javascript:void(0)" style="cursor: default;" '. ($show_img_title ? ' class="tooltipsy" original-title="'.esc_attr($att_img_title).'"' : '').'>
							'.$image_output.'
						</a>';
				}
				
				$str .= '</div>
					</div>';

				$count ++;
			}
			
			$str .= '</div></div>';

			// If we Want the fading logos on Hover
			if($fading_logos){
				$str .= '
					<!-- Logo Gallery Fading logos -->
					<style>
					#img_gallery_'.$gallery_id.' .col {
						-webkit-transition: all 0.6s ease;
						-moz-transition: all 0.6s ease;
						-ms-transition: all 0.6s ease;
						-o-transition: all 0.6s ease;
						transition: all 0.6s ease;
					}
					</style>
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							$("#img_gallery_'.$gallery_id.' .col").hover(				
								function() {
									$(this).siblings(".col").each(function (i, el) {
										$(["-webkit-", "-moz-", "-o-", "-ms-", ""]).each(function (i, p) {
											$(el).css(p + "transition-delay" , 0 + "ms");
										});
									});
									$(this).siblings(".col").stop().fadeTo(0, 0.3);
								},
								function() {
									$(this).siblings(".col").stop().fadeTo(0, 1);
								}
							);
						});
					</script>';
			}

			return $str;
		}
	}

	add_shortcode('boc_logo_gallery', 'shortcode_boc_logo_gallery');
}


// Map Shortcode in Visual Composer
vc_map( array(
	"name" => __("Logo Grid", 'Fortuna'),
	"base" => "boc_logo_gallery",
	"category" => "Fortuna Shortcodes",
	"icon" 		=> "boc_logo_gallery",
	"weight"	=> 46,
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
			"heading" 		=> __("Add Grey Left Border", 'Fortuna'),
			"param_name" 	=> "left_border",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
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
			"value"			=> Array("None" => '', "Zoom Out" => 1, "Zoom In" => 2, "Grey Out"=>9),
			"description" 	=> __("Pick a hover Image Effect", 'Fortuna'),
			"group"			=> "Settings",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Fading Logos on Hover", 'Fortuna'),
			"param_name" 	=> "fading_logos",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"group"			=> "Settings",
		),		
		array(
			"type"		=> "dropdown",
			"heading"	=> __("CSS Animation", "Fortuna"),
			"param_name"	=> "css_animation",
			"admin_label"	=> true,				
			"value"			=> array(
				__("None", "Fortuna")					=> '',
				__("Top to bottom", "Fortuna")			=> "top-to-bottom",
				__("Bottom to top", "Fortuna")			=> "bottom-to-top",
				__("Left to right", "Fortuna")			=> "left-to-right",
				__("Right to left", "Fortuna")			=> "right-to-left",
				__("Appear from center", "Fortuna")	=> "appear"),
			"description"	=> __("Select one if you want the images to be animated once the section enters the browsers viewport.", "Fortuna"),
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
			"description"	=> __('Select the images you want to have in your Slider.', 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Image Title Popup on Hover", 'Fortuna'),
			"param_name" 	=> "show_img_title",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Do you want the Title Attribute to appear in a Popup when image is hovered", 'Fortuna'),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Links", 'Fortuna'),
			"param_name" 	=> "add_links",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"group"			=> "Gallery",
		),
		array(
			"type"			=> "exploded_textarea",
			"heading"		=> __("Custom Links", 'Fortuna'),
			"param_name"	=> "custom_links",
			"description"	=> __('Enter links for each separate image here (starting with "http://"). Divide each link with a line-break (Enter). For images without a link enter the "#" symbol.', 'Fortuna'),
			"dependency"	=> Array(
				'element'	=> "add_links",
				'not_empty'	=> true,
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
				'element'	=> "add_links",
				'not_empty'	=> true,
			),
			"group"			=> "Gallery",
		),		
	)
));