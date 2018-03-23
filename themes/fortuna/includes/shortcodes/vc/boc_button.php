<?php

/**
	Button Shortcode - part of the Theme Base -> shortcodes.php
**/



// Map Shortcode in Visual Composer
	

vc_map( array(
	"name" 		=> __("Fortuna Button", 'Fortuna'),
	"base" 		=> "boc_button",
	"class" 	=> "boc_button",
	"icon" 		=> "boc_button",
	"category" 	=> "Fortuna Shortcodes",
	"weight"	=> 73,
	"params" 	=> array(
		array(
			 "type" => "textfield",
			 "heading" => __("Text", 'Fortuna'),
			 "param_name" => "btn_content",
			 "value" => "Button Text",
			 "admin_label"	=> true,
			 "description" => __("Enter the text for your Button", 'Fortuna'),
		),
		array(
			 "type" => "textfield",
			 "heading" => __("URL Link", 'Fortuna'),
			 "param_name" => "href",
			 "admin_label"	=> true,
			 "value" => "",
			 "description" => __("Enter the link you want your button to take you to once clicked.", 'Fortuna'),
		),
		array(
			 "type" => "dropdown",
			 "heading" => __("Target", 'Fortuna'),
			 "param_name" => "target",
			 "value" => array('_self','_blank'),
			 "description" => __("Pick '_blank' if you want the button link to open in a new tab.", 'Fortuna'),
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
			 "type" => "textfield",
			 "heading" => __("Extra class name", 'Fortuna'),
			 "param_name" => "css_classes",
			 "value" => "",
			 "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Smooth scroll for 'Same page' link?", 'Fortuna'),
			"param_name" 	=> "smooth_scroll",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Activate smooth scrolling for link if it points to current page", 'Fortuna'),
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
				"Emerald" 		=> "btn_emerald",
				"Jade"	 		=> "btn_jade",
				"Orange"		=> "btn_orange",
				"Yellow"		=> "btn_yellow",
				"Purple"		=> "btn_purple",
				"Magenta"	=> "btn_magenta",
				"Red"		=> "btn_red",
				"Pink"		=> "btn_pink",
				"Brown"		=> "btn_brown",
			),
			"description"	=> "Select a button color. They are predefined for a number of reasons, like text color, hover effects etc. Use the CSS class option to define your own.",
			'group'			=> 'Button Design',
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
		),
		array(
			"type"		=> "dropdown",
			"heading"	=> "Border Radius",
			"param_name"	=> "border_radius",
			"value"		=> array(
				"Rounded" => "btn_rounded",
				"Squared" => "btn_squared",
				"Oval" => "btn_circled",
			),
			"description"	=> "Select a border radius style",
			'group'			=> 'Button Design',
		),
        array(
			"type"          => "iconpicker",
			"heading"       => "Add Button Icon",
			"param_name"    => "icon",
			"admin_label"   => true,
			"settings" 		=> array(
				'type'      => 'fortuna',
				'emptyIcon' => true, // default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'description'   => __( 'Select icon from library.', 'Fortuna' ),
			"group"         => __( 'Button Icon', 'Fortuna' ),
		),
		array(
			 "type" 		=> "dropdown",
			 "heading" 		=> "Icon Position",
			 "param_name" 	=> "icon_pos",
			 "value"		=> array(
				"Before Text" 	=> "icon_pos_before",
				"After Text" 	=> "icon_pos_after",
			 ),
			 "description" 	=> "Choose a position for the icon",
			 'group'		=> 'Button Icon',
			 "dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
				)
		),
		array(
			"type"			=> "dropdown",
			"heading"		=> "Icon Effect",
			"param_name"	=> "icon_effect",
			"value"			=> array(
				"None" 		=> "none",
				"Animate In" 	=> "btn_icon_anim_in",
				"Animate Out" 	=> "btn_icon_anim_out",
			),
			"description"	=> "Select an Icon Animate Effect if you want one",
			"group"			=> 'Button Icon',
			"dependency"	=> Array(
				'element'	=> "icon",
				'not_empty'	=> true,
			)
		)
	)
));