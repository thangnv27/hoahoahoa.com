<?php

/**
	Side Icon Featured Text
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_side_icon_box' ) ) {
	function shortcode_boc_side_icon_box( $atts, $content = null ) {
		

		$atts = vc_map_get_attributes('boc_side_icon_box', $atts );
		extract( $atts );	

		$icon_css = $icon_bgr_css = '';
		
		$link = '';
		if($href){
			$link = '<a href="'.esc_url($href).'" target="'.esc_attr($target).'">';
		}
		
		if($has_icon_color){
			if($icon_solid) {
				$icon_bgr_css = "background: ". esc_attr($icon_color) .";";
			}else {
				$icon_css = "color: ". esc_attr($icon_color) .";";
			}
		}
					
		$content = '<div class="side_icon_box '.esc_attr($icon_size).' '.esc_attr($css_classes).' '.($animated ? "boc_animate_when_almost_visible" : "").'"><span class="icon_feat '.($icon_solid ? ' icon_solid': '').'" style="'.$icon_bgr_css.'"><i class="' . esc_attr($icon) . '" style="'.$icon_css.'"></i></span><h3>'.($href ? $link : '') . $title . ($href ? '</a>' : '').'</h3><div class="side_icon_box_content">'.do_shortcode($content).'</div></div>';
		return $content;
	}
	
	add_shortcode('boc_side_icon_box', 'shortcode_boc_side_icon_box');
}

// Map Shortcode in Visual Composer
vc_map( array(
   "name" => __("Side Icon Text", 'Fortuna'),
   "description" => __("Side Iconed Featured Text", 'Fortuna'),
   "base" 	=> "boc_side_icon_box",
   "category" => "Fortuna Shortcodes",
   "icon" 	=> "boc_side_icon_box",
   "weight"	=> 70,
   "params" => array(
		array(
			"type" => "textfield",
			"heading" => __("Heading", 'Fortuna'),
			"param_name" => "title",
			"admin_label"=> true,
			"value" => __("Featured Title", 'Fortuna'),
			"description" => __("Enter the Heading for your Icon Box", 'Fortuna'),
		),
		array(
			"type" 		=> "textarea_html",
			"heading" 	=> __("Text Below Title", 'Fortuna'),
			"param_name" => "content",
			"value" 		=> __("Featured Text", 'Fortuna'),
			"description"=> __("Enter the text to go below your Heading", 'Fortuna'),
		),
		array(
			"type"		=> 'checkbox',
			"heading"	=> __("Animate Box In?", "Fortuna"),
			"param_name"	=> "animated",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"	=> __("Check Yes if you want the box to be animated once it enters the browsers viewport.", "Fortuna"),
		),		
		array(
			"type" => "textfield",
			"heading" => __("Extra class name", 'Fortuna'),
			"param_name" => "css_classes",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),

		array(
			"type"          => "iconpicker",
			"heading"       => "Icon",
			"param_name"    => "icon",
			"admin_label"   => true,
			"settings" => array(
				'type' => 'fortuna',
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'description'   => __( 'Select icon from library.', 'Fortuna' ),
			"group"         => __( 'Icon', 'Fortuna' ),
		),

		array(
			"type"			=> "dropdown",
			"class"			=> "",
			"heading"		=> __( "Icon Size", 'Fortuna' ),
			"param_name"		=> "icon_size",
			"description"		=> __( "Select an icon size.", 'Fortuna' ),
			"value"			=> array(
				__( "Normal", "Fortuna" )		=> "normal",
				__( "Large", "Fortuna" )		=> "large",
				__( "X-Large", "Fortuna" )		=> "xlarge",
			),
			"group"		=> __( 'Icon', 'Fortuna' ),
		),			
		array(
			"type"		=> 'checkbox',
			"heading"	=> __("Overwrite Icon Color", "Fortuna"),
			"param_name"	=> "has_icon_color",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"	=> __("If not checked Icon will inherit your main theme color", "Fortuna"),
			"group"		=> __( 'Icon', 'Fortuna' ),
		),				
		array(
			"type"		=> "colorpicker",
			"heading"	=> "Icon Color",
			"param_name"	=> "icon_color",
			"value"		=> "#333333",
			"group"		=> __( 'Icon', 'Fortuna' ),
			"dependency"	=> Array( 'element'	=> "has_icon_color", 'not_empty' => true ),				
		),				
		array(
			"type" 		=> "checkbox",
			"heading" 	=> __("Icon Solid", 'Fortuna'),
			"param_name" => "icon_solid",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"=> __("Do you want your icon to be a solid Background color and white icon", 'Fortuna'),
			"group"		=> __( 'Icon', 'Fortuna' ),
		),		
		array(
			"type" => "textfield",
			"heading" => __("URL Link", 'Fortuna'),
			"param_name" => "href",
			"value" => "",
			"description" => __("Enter a link if you want one", 'Fortuna'),
			"group"		=> __( 'Link', 'Fortuna'),	
		),
		array(
			"type" => "dropdown",
			"heading" => __("Target", 'Fortuna'),
			"param_name" => "target",
			"value" => array('_self','_blank'),
			"description" => __("Pick '_blank' if you want the link to open in a new tab.", 'Fortuna'),
			"group"		=> __( 'Link', 'Fortuna'),	
		),
   )
));