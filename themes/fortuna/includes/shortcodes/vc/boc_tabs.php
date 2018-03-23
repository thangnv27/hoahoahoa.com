<?php

/**
	Tabs
**/

// Register Shortcodes
if( ! function_exists( 'shortcode_boc_tabs' ) ) {
	function shortcode_boc_tabs( $atts, $content = null ) {
		
		$atts = vc_map_get_attributes('boc_tabs', $atts );
		extract( $atts );	

		if($minimal_style) {
			$css_classes .= ' minimal_style';
		}				
			
		$str = '
		<!--New Tabs-->
		<div class="newtabs clearfix '.esc_attr($type).' '.esc_attr($css_classes).'">
			<ul class="resp-tabs-list">';
		
		$str .= '
			</ul>
			<div class="resp-tabs-container">
		';
		
		$str .= do_shortcode($content);
		
		$str .= '</div>
				</div>';
			
		return $str;
	}
	
	add_shortcode('boc_tabs', 'shortcode_boc_tabs');
}

// Tab
if( ! function_exists( 'shortcode_boc_tab' ) ) {
	function shortcode_boc_tab( $atts, $content = null ) {
		
		$atts = vc_map_get_attributes('boc_tab', $atts );
		extract( $atts );	

		return '<div class="single_tab_div" rel-title="'.esc_attr($atts['title']).'" rel-icon="'.esc_attr($atts['icon']).'">' . do_shortcode($content) . '</div>';
	}
	
	add_shortcode('boc_tab', 'shortcode_boc_tab');
}

// Map Shortcodes in Visual Composer
vc_map( array(
	"name" => __("Tabs", 'Fortuna'),
	"base" => "boc_tabs",
	"as_parent" => array('only' => 'boc_tab'), // limit child shortcodes
	"content_element" => true,
	"category" => "Fortuna Shortcodes",
	"show_settings_on_create" => true,
	"js_view" => 'VcColumnView',
	"icon" 		=> "boc_tabs",
	"weight"	=> 36,
	"params" => array(			
		array(
			"type" => "dropdown",
			"heading" => __("Type", 'Fortuna'),
			"param_name" => "type",
			"value" => array('horizontal','vertical'),
			"std"	=> "horizontal",
			"description" => __("Pick Tab Style", 'Fortuna'),
		),
		array(
			"type"		=> 'checkbox',
			"heading"	=> __("Use minimal Style?", "Fortuna"),
			"param_name"	=> "minimal_style",
			"value"		=> Array(__("Yes", "Fortuna") => 'yes' ),
		),		
		array(
			"type" 			=> "textfield",
			"heading" 		=> __("Extra class name", 'Fortuna'),
			"param_name" 	=> "css_classes",
			"description" 	=> __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
	)
));

vc_map( array(
	"name" => __("Tab Item", 'Fortuna'),
	"base" => "boc_tab",
	"icon" 		=> "boc_tabs",
	"as_child" => array('only' => 'boc_tabs'),
	"as_parent" => array('except' => 'boc_tab','boc_tabs'),
	"js_view" => 'VcColumnView',
	"params" => array(

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
			'description'   => __( 'Select icon from library.', 'Fortuna')
		),

		array(
			"type" => "textfield",
			"heading" => __("Title", 'Fortuna'),
			"param_name" => "title",
			"value" => __("Tab Title", 'Fortuna'),
			"description" => __("Set your Tab Title", 'Fortuna'),
		),
	)
));

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
	class WPBakeryShortCode_boc_tabs extends WPBakeryShortCodesContainer {
	}
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
	class WPBakeryShortCode_boc_tab extends WPBakeryShortCodesContainer {
	}
}