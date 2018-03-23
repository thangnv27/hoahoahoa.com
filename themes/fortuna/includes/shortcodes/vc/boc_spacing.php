<?php

/**
	Spacing - part of the Theme Base -> shortcodes.php
**/

// Map Shortcode in Visual Composer
vc_map( array(
	"name"				=> "Spacing",
	"description"		=> "Add a custom height spacing",
	"base"				=> "boc_spacing",
	'category'			=> "Fortuna Shortcodes",
	"icon"				=> "boc_spacing",
	"weight"			=> 80,
	"params"			=> array(
		array(
			"type"			=> "textfield",
			"admin_label"	=> true,
			"heading"		=> "Spacing",
			"param_name"	=> "height",
			"value"			=> "20px",
			"description"	=> "Enter a height in pixels for your spacing"
		),
		array(
		 "type" 		=> "textfield",
		 "heading" 		=> "Extra class name",
		 "param_name" 	=> "css_classes",
		 "description" 	=> "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file."
		),
	)
));	