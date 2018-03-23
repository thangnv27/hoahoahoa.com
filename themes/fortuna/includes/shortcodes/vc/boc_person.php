<?php

/**
	Person
**/


// Register Shortcode
if( ! function_exists( 'shortcode_boc_person' ) ) {
	function shortcode_boc_person($atts, $content = null) {

		$atts = vc_map_get_attributes('boc_person', $atts );
		extract( $atts );
			
		// Pic
		$img = '';
		if($picture){
			$vc_image = wp_get_attachment_image($picture,'full');
			
			// If not passed via VC, we use default Person Icon
			if($vc_image){
				$img = $vc_image;
			}else {
				$img = get_template_directory_uri().'/images/user.png';
			}
		}else {
				$img = '<img src="'.get_template_directory_uri().'/images/user.png" />';
		}				
					
		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation !== '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation .'';
		}				
					
		$str='	<div class="team_block_content '.esc_attr($css_classes).' '.esc_attr($css_animation_classes).'">
					<div class="pic">
						'.($href ? '<a href="'.esc_url($href).'" target="'.esc_attr($target).'">' : '').'
						<div class="team_image '.(!$circled ? 'boxed' : '').' '.($flat_img ? 'flat_img' : '').'">'.$img.'</div>
						'.($href?'</a>':'').'
						
						
						<div class="team_block">
							<h4>'.($href?'<a href="'.esc_url($href).'">':'').wp_kses_post($name).($href?'</a>':'').'</h4>
							<p class="team_desc">'.wp_kses_post($title).'</p>
							<p class="team_text">'.do_shortcode($content).'</p>
							<div class="team_icons">
						'.($twitter ? '<a target="_blank" href="'.esc_url($twitter).'" title="Twitter"><span class="icon icon-twitter3"></span></a>': '').'
						'.($facebook ? '<a target="_blank" href="'.esc_url($facebook).'" title="Facebook"><span class="icon icon-facebook3"></span></a>': '').'
						'.($googleplus ? '<a target="_blank" href="'.esc_url($googleplus).'" title="Google+"><span class="icon icon-googleplus2"></span></a>': '').'
						'.($linkedin ? '<a target="_blank" href="'.esc_url($linkedin).'" title="LinkedIn"><span class="icon icon-linkedin3"></span></a>': '').'
						'.($pinterest ? '<a target="_blank" href="'.esc_url($pinterest).'" title="Pinterest"><span class="icon icon-pinterest2"></span></a>': '').'
						'.($instagram ? '<a target="_blank" href="'.esc_url($instagram).'" title="Instagram"><span class="icon icon-instagram2"></span></a>': '').'
						'.($xing ? '<a target="_blank" href="'.esc_url($xing).'" title="Xing"><span class="icon icon-xing"></span></a>': '').'
						'.($email ? '<a target="_blank" href="mailto:'.wp_kses_post($email).'" title="Email"><span style="font-size: 1.03em;" class="icon icon-mail2"></span></a>': '').'
							</div>
						</div>
					</div>
				</div>
			';

		return $str;
	}

	add_shortcode('boc_person', 'shortcode_boc_person');	
}




// Map Shortcode in Visual Composer
vc_map( array(
	"name"			=> __( "Person", 'Fortuna'),
	"description"	=> __( "Add a staff person summary", 'Fortuna'),
	"base"			=> "boc_person",		
	"icon" 			=> "boc_person",	
	"category" 		=> "Fortuna Shortcodes",
	"weight"		=> 28,
	"params"		=> array(
		array(
			"type" 			=> "attach_image",
			"heading" 		=> __("Picture", 'Fortuna'),
			"param_name" 	=> "picture",
			"description" 	=> __("Add a Person Image", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Circled Image?", 'Fortuna'),
			"param_name" 	=> "circled",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Flat Image?", 'Fortuna'),
			"param_name" 	=> "flat_img",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("No white border and shadow on Person Image?", 'Fortuna'),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("URL Link", 'Fortuna'),
			"param_name" 	=> "href",
			"value" 		=> "",
			"description" 	=> __("Enter a link if you want one for the picture and name", 'Fortuna'),
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
		),
		array(
			"type"			=> "textfield",
			"heading"		=> __("Name", 'Fortuna'),
			"admin_label"	=> true,
			"param_name"	=> "name",
			"value"			=> "Name",
		),
		array(
			"type"			=> "textfield",
			"heading"		=> __("Title", 'Fortuna'),
			"param_name"	=> "title",
			"value"			=> "Title",
		),
		array(
			"type"			=> "textarea_html",
			"heading"		=> __("Description", 'Fortuna'),
			"param_name"	=> "content",
			"value"			=> "Description",
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
			"description"	=> __("Select one if you want this element to be animated once it enters the browsers viewport.", "Fortuna"),
		),			
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
		array(
			"type"			=> "textfield",
			"heading"		=> "Twitter",
			"param_name"	=> "twitter",
			"group"			=> "Links",
		),	
		array(
			"type"			=> "textfield",
			"heading"		=> "Facebook",
			"param_name"	=> "facebook",
			"group"			=> "Links",
		),	
		array(
			"type"			=> "textfield",
			"heading"		=> "Google Plus",
			"param_name"	=> "googleplus",
			"group"			=> "Links",
		),	
		array(
			"type"			=> "textfield",
			"heading"		=> "LinkedIn",
			"param_name"	=> "linkedin",
			"group"			=> "Links",
		),	
		array(
			"type"			=> "textfield",
			"heading"		=> "Pinterest",
			"param_name"	=> "pinterest",
			"group"			=> "Links",
		),
		array(
			"type"			=> "textfield",
			"heading"		=> "Instagram",
			"param_name"	=> "instagram",
			"group"			=> "Links",
		),
		array(
			"type"			=> "textfield",
			"heading"		=> "Xing",
			"param_name"	=> "xing",
			"group"			=> "Links",
		),	
		array(
			"type"			=> "textfield",
			"heading"		=> "Email",
			"param_name"	=> "email",
			"group"			=> "Links",
		),	
		
	)
));