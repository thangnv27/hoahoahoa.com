<?php

/**
	Text Box
**/


// Register Shortcode
if( ! function_exists( 'shortcode_boc_text_box' ) ) {
	function shortcode_boc_text_box($atts, $content = null) {

	
		$atts = vc_map_get_attributes('boc_text_box', $atts );
		extract( $atts );
	
		if(!$add_bgr) {
			$css_classes2 .= ' box_no_bgr';
		}
		if($left_border && $add_bgr ) {
			$css_classes2 .= ' left_border';
		}

		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation !== '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation;
		}	
		
		$str = '<div class="text_box '.esc_attr($css_classes2).' '.esc_attr($css_animation_classes).'">';

						
		$str .= '<div class="text_box_content '.($add_button ? "with_button" : "").'"><h2>'.wp_kses_post($title).'</h2>
					<p>'.do_shortcode($content).'</p>
				</div>';
		
		if($add_button){
		
			$target 		= ($target 	? " target='".esc_attr($target)."'" : '');
			$icon 			= ($icon 	? " <i class='icon ".esc_attr($icon)."'></i> " : '');
			$icon_pos 		= ($icon 	? $icon_pos : '');
			$icon_effect	= (($icon && $icon_effect!='none' )? $icon_effect : '');			
		
			$icon_before = ($icon_pos == 'icon_pos_before' ? wp_kses_post($icon) : '');
			$icon_after = ($icon_pos == 'icon_pos_after' ? wp_kses_post($icon) : '');
			
			$str .= '<div class="btn_holder"><a	href="'.esc_url($href).'" 
					class="button '.esc_attr($css_classes.' '.$size.' '.$color.' '.
					$border_radius.' '.$btn_style.' '.$icon_pos.' '.$icon_effect).'" '.$target.'>'.
					$icon_before . '<span>' . do_shortcode($btn_content).'</span>' . $icon_after . '</a></div>';					

		}
		
		$str .= '</div>';

		return $str;
	}
	
	add_shortcode('boc_text_box', 'shortcode_boc_text_box');
}

// Map Shortcode in Visual Composer
vc_map( array(
	"name"			=> __("Textbox", 'Fortuna'),
	"base"			=> "boc_text_box",		
	"category" 		=> "Fortuna Shortcodes",
	"icon" 		=> "boc_text_box",
	"weight"		=> 24,
	"params"		=> array(
		array(
			"type"			=> "textfield",
			"heading"		=> __("Title", 'Fortuna'),
			"admin_label"	=> true,
			"param_name"	=> "title",
			"value"			=> "Title",
		),
		array(
			"type"			=> "textarea_html",
			"heading"		=> __("Text Content", 'Fortuna'),
			"param_name"	=> "content",
			"value"			=> "",
			"description"	=> __('Enter your text box content to go below the Title.', 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __('Add Background', 'Fortuna'),
			"param_name" 	=> 'add_bgr',
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description"	=> __('Add the default grey BGR?', 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __('Add Accent Color Left Border', 'Fortuna'),
			"param_name" 	=> 'left_border',
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"dependency"	=> Array(
				'element'	=> "add_bgr",
				'not_empty'	=> true,
			)			
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Add Button link?", 'Fortuna'),
			"param_name" 		=> "add_button",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Add a Button to your Pricing Column Bottom. Button properties are set in separate Tabs", 'Fortuna'),
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
				__("Fade In", "Fortuna")				=> "fade-in"),
			"description"	=> __("Select one if you want this element to be animated once it enters the browsers viewport.", "Fortuna"),
		),
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes2",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
		

		// Button 
		array(
			"type" => "textfield",
			"heading" => __("Text", 'Fortuna'),
			"param_name" => "btn_content",
			"value" => __("Button Text", 'Fortuna'),
			"description" => __("Enter the text for your Button", 'Fortuna'),
			'group'		=> 'Button Properties',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			 
		),
		array(
			"type" => "textfield",
			"heading" => __("URL Link", 'Fortuna'),
			"param_name" => "href",
			"value" => "",
			"description" => __("Enter the link you want your button to take you to once clicked.", 'Fortuna'),
			'group'		=> 'Button Properties',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type" => "dropdown",
			"heading" => __("Target", 'Fortuna'),
			"param_name" => "target",
			"value" => array('_self','_blank'),
			"description" => __("Pick '_blank' if you want the button link to open in a new tab.", 'Fortuna'),
			'group'		=> 'Button Properties',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type" => "textfield",
			"heading" => __("Extra class name", 'Fortuna'),
			"param_name" => "css_classes",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
			"group"		=> 'Button Properties',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)	
		),		  
		array(
			"type"			=> "dropdown",
			"heading"		=> "Button Size",
			"param_name"		=> "size",
			"value"			=> array(
				"Small" => "btn_small",
				"Medium"=> "btn_medium",
				"Large" => "btn_large",
				"Huge"  => "btn_huge",
				"Stretched Small" => "btn_small_stretched",
				"Stretched Medium"=> "btn_medium_stretched",
				"Stretched Large" => "btn_large_stretched",
				"Stretched Huge"  => "btn_huge_stretched",
			),
			"std"		=> "btn_medium",
			"description"	=> "Select a size",
			'group'		=> 'Button Design',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> "Button Color",
			"param_name"		=> "color",
			"admin_label"	=> true,
			"value"			=> array(
				"Theme Color"=> "btn_theme_color",
				"Dark" 		=> "btn_dark", // Default
				"White" 		=> "btn_white",
				"Blue" 		=> "btn_blue",
				"Bondi Blue"	=> "btn_bondi",
				"Royal Blue"	=> "btn_royalblue",
				"Turquoise" 	=> "btn_turquoise",
				"Green" 		=> "btn_green",
				"Lime Green"	=> "btn_limegreen",
				"Emerald" 	=> "btn_emerald",
				"Orange"		=> "btn_orange",
				"Yellow"		=> "btn_yellow",
				"Purple"		=> "btn_purple",
				"Magenta"	=> "btn_magenta",
				"Red"		=> "btn_red",
				"Brown"		=> "btn_brown",
			),
			"description"	=> "Select a button color. They are predefined for a number of reasons, like text color, hover effects etc. Use the CSS class option to define your own.",
			'group'			=> 'Button Design',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "Button Style",
			"param_name"	=> "btn_style",
			"value"		=> array(
				"Normal" 	=> "btn_normal_style", // Default
				"Gradient" 	=> "btn_gradient",
				"Outline" 	=> "btn_outline",
				"3D"	 		=> "btn_3d",
			),
			"description"	=> "Select a style",
			'group'		=> 'Button Design',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),			
		array(
			"type"		=> "dropdown",
			"heading"	=> "Border Radius",
			"param_name"	=> "border_radius",
			"value"		=> array(
				"Rounded" => "btn_rounded",
				"Sqaured" => "btn_squared",
				"Oval" => "btn_circled",
			),
			"description"	=> "Select a border radius style",
			'group'			=> 'Button Design',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type" 		=> "textfield",
			"heading" 	=> "Add Button Icon",
			"param_name"=> "icon",
			"value" 	=> "",
			"description"=> "Type in an icon name from the list to add an icon to the button text.",
			'group'		=> 'Button Icon',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)			
		),
		array(
			"type" => "dropdown",
			"heading" => "Icon Position",
			"param_name" => "icon_pos",
			"value"			=> array(
				"Before Text" => "icon_pos_before",
				"After Text" => "icon_pos_after",
			),
			"description" => "Choose a position for the icon",
			'group'		=> 'Button Icon',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "Icon Effect",
			"param_name"	=> "icon_effect",
			"value"		=> array(
				"None" 		=> "none",
				"Animate In" 	=> "btn_icon_anim_in",
				"Animate Out" => "btn_icon_anim_out",
			),
			"description"	=> "Select an Icon Animate Effect if you want one",
			"group"		=> 'Button Icon',
			"dependency"	=> Array(
				'element'	=> "add_button",
				'not_empty'	=> true,
			)
		)

	)
));