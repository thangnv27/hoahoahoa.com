<?php

/**
	Top Icon Featured Text
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_top_icon_box' ) ) {
	function shortcode_boc_top_icon_box( $atts, $content = null ) {
		
		$atts = vc_map_get_attributes('boc_top_icon_box', $atts );
		extract( $atts );
		
		$link = '';
		if($href){
			$link = '<a href="'.esc_url($href).'" target="'.esc_attr($target).'">';
		}
		
		$unique_id = rand(1,100000);
		if($has_icon_color){
			$icon_color = esc_attr($icon_color);
			$custom_css = '
			<style>
				#top_icon_box_'.$unique_id.'.top_icon_box.type1 .icon_holder .icon_bgr { background-color: '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type1:hover .icon_holder .icon_bgr { background-color: #fff; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type1:hover .icon_holder .icon_bgr { border: 2px solid '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type1:hover .icon_holder .icon_bgr:after { border: 2px solid '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type1:hover .icon_holder i { color: '.$icon_color.';}
				
				#top_icon_box_'.$unique_id.'.top_icon_box.type2 .icon_holder .icon_bgr { background-color: '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type2:hover .icon_holder .icon_bgr { background-color: #fff; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type2:hover .icon_holder i { color: '.$icon_color.';}
				
				#top_icon_box_'.$unique_id.'.top_icon_box.type3 .icon_holder .icon_bgr:after { border: 2px solid '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type3:hover .icon_holder .icon_bgr { background-color: '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type3 .icon_holder i { color: '.$icon_color.';}
				#top_icon_box_'.$unique_id.'.top_icon_box.type3:hover .icon_holder i { color: #fff; }
				
				#top_icon_box_'.$unique_id.'.top_icon_box.type4:hover .icon_holder  .icon_bgr { border: 2px solid '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type4:hover .icon_holder .icon_bgr:after { border: 3px solid '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type4 .icon_holder i{ color: '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type4:hover .icon_holder i { color:  '.$icon_color.'; }
		
				#top_icon_box_'.$unique_id.'.top_icon_box.type5 .icon_holder i{ color: '.$icon_color.'; }
				#top_icon_box_'.$unique_id.'.top_icon_box.type5:hover .icon_holder i { color:  '.$icon_color.'; }
			</style>
			';
		}
			
		$content = (isset($custom_css) ? $custom_css : '').'<div id="top_icon_box_'.$unique_id.'" class="top_icon_box type'.esc_attr($type.' '.$css_classes).' '.($animated ? "boc_animate_when_almost_visible" : "").'">'.($href ? $link : '').'<div class="icon_holder"><div class="icon_bgr"></div><div class="icon_center"><i class="'.esc_attr($icon).'"></i></div></div>'.($href ? '</a>' : '').'<h3>'.($href ? $link : '').wp_kses_post($title).($href ? '</a>' : '').'</h3><p>'.do_shortcode($content).'</p></div>';
		return $content;
	}
	
	add_shortcode('boc_top_icon_box', 'shortcode_boc_top_icon_box');
}




// Map Shortcode in Visual Composer
vc_map( array(
   "name" => __("Top Icon Box", 'Fortuna'),
   "description" => __("Top Icon Box", 'Fortuna'),
   "base" 	=> "boc_top_icon_box",
   "category" => "Fortuna Shortcodes",
   "icon" 	=> "boc_top_icon_box",
   "weight"	=> 72,
   "params" => array(
		array(
			"type" => "textfield",
			"heading" => __("Heading", "Fortuna"),
			"param_name" => "title",
			"admin_label"=> true,
			"value" => __("Featured Title", "Fortuna"),
			"description" => __("Enter the Heading for your Icon Box", "Fortuna"),
		),
		array(
			"type" 		=> "textarea_html",
			"heading" 	=> __("Text Below Title", "Fortuna"),
			"param_name" => "content",
			"value" 	=> __("Featured Text", "Fortuna"),
			"description"=> __("Enter the text to go below your Heading", "Fortuna")
		),			
		array(
			"type" => "textfield",
			"heading" => __("Extra class name", "Fortuna"),
			"param_name" => "css_classes",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "Fortuna")
		),
		array(
			"type"		=> 'checkbox',
			"heading"	=> __("Animate Box In?", "Fortuna"),
			"param_name"	=> "animated",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"	=> __("Check Yes if you want the box to be animated once it enters the browsers viewport.", "Fortuna"),
		),
		array(
			"type"          => "iconpicker",
			"heading"       => "Icon",
			"param_name"    => "icon",
			"admin_label"   => true,
			"settings" => array(
				'type' => 'fortuna',
				'emptyIcon' => false, // default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'description'   => __( 'Select icon from library.', 'Fortuna' ),
			"group"         => __( 'Icon', 'Fortuna' ),
		),
		array(
			"type"		=> 'checkbox',
			"heading"	=> __("Overwrite Icon Color", "Fortuna"),
			"param_name"	=> "has_icon_color",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"	=> __("If not checked Icon will inherit your main theme color",	"Fortuna"),
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
			"type" => "dropdown",
			"heading" => __("Style", "Fortuna"),
			"param_name" => "type",
			"value" => array('1','2','3','4','5'),
			"description" => __("Pick a style for your Icon", "Fortuna"),
			"group"		=> __( 'Icon', 'Fortuna' ),				
		),			
		array(
			"type" => "textfield",
			"heading" => __("URL Link", "Fortuna"),
			"param_name" => "href",
			"value" => "",
			"description" => __("Enter a link if you want one. Don't forget the http:// in front.", "Fortuna"),
			"group"		=> __( 'Link', 'Fortuna' ),	
		),
		array(
			"type" => "dropdown",
			"heading" => __("Target", "Fortuna"),
			"param_name" => "target",
			"value" => array('_self','_blank'),
			"description" => __("Pick '_blank' if you want the link to open in a new tab.", "Fortuna"),
			"group"		=> __( 'Link', 'Fortuna'),	
		),				

   )
));
