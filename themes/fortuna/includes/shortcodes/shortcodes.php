<?php

/**
	Fortuna Shortcodes
**/


// Add button to editor in Admin
add_action('admin_head', 'boc_add_button');


// Add buttons for our shortcodes
function boc_add_button() {  
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
   {
     add_filter('mce_external_plugins', 'add_plugin');
     add_filter('mce_buttons', 'register_button');
   }  
}

function add_plugin($plugin_array) {

   $plugin_array['boc_shortcodes_dropdown'] = get_template_directory_uri().'/includes/shortcodes/customcodes.js';
   return $plugin_array;
}

function register_button($buttons) {
   array_push($buttons, "boc_shortcodes_dropdown");    
   return $buttons;
}



/**
	Fortuna Base Shortcodes
**/

// Spacing
add_shortcode( 'boc_spacing', 'boc_shortcode_spacing' );
function boc_shortcode_spacing( $atts ) {
	
	$atts = vc_map_get_attributes('boc_spacing', $atts );
	extract( $atts );	

	return '<div class="boc_spacing '. esc_attr($css_classes) .'" style="height: '. esc_attr($height) .'"></div>';
}

// Button Link
if( ! function_exists( 'shortcode_boc_button' ) ) {
	function shortcode_boc_button($atts, $content = null){
		
		$atts = vc_map_get_attributes('boc_button', $atts );
		extract( $atts );

		$target 		= ($target 	? " target='".$target."'" : '');
		$icon 			= ($icon 	? " <i class='".$icon."'></i> " : '');
		$icon_pos 		= ($icon 	? $icon_pos : '');
		$icon_effect	= (($icon && $icon_effect!='none' )? $icon_effect : '');
		// CSS Animation
		$css_animation_classes = "";
		if ( $css_animation !== '' ) {
			$css_animation_classes = 'boc_animate_when_almost_visible boc_'. $css_animation .'';
		}    
		
		$icon_before = ($icon_pos == 'icon_pos_before' ? wp_kses_post($icon) : '');
		$icon_after = ($icon_pos == 'icon_pos_after' ? wp_kses_post($icon) : '');
		
		return	'<a	href="'.esc_url($href).'" '.($smooth_scroll ? ' rel="smooth_scroll" ' : '' ).'
			class="button '.esc_attr($size.' '.$color.' '.$border_radius.' '.$btn_style.' '.$icon_pos.' '.$icon_effect.' '.$css_animation_classes.' '.$css_classes).'" '.wp_kses_post($target).'>'.$icon_before.'<span>'.do_shortcode(esc_html($btn_content)).'</span>'.$icon_after.'</a>';  
	}
	
	add_shortcode('boc_button', 'shortcode_boc_button');
}


// Font Icon 
if( ! function_exists( 'shortcode_vc_boc_icon' ) ) {
	function shortcode_vc_boc_icon($atts, $content = null) {
		
			$atts = vc_map_get_attributes('boc_icon', $atts );
			extract( $atts );

			$color_css = 'color:'. $icon_color .';';
			
			$background_css = $border_radius_css = $icon_padding_css = $margin_bottom_css = '';
			
			if ( $has_icon_bg == 'yes' ) {
				if($icon_bg != '#ffffff'){
					$background_css = 'background-color:'. $icon_bg .';';
				}
				if($icon_bg_border != '#ffffff'){
					$background_css .= 'border: 1px solid '. $icon_bg_border .';';
				}
				if($border_radius != '100%'){
					$border_radius_css = 'border-radius:'. $border_radius .';';
				}
				
				$css_classes .= 'with_bgr';
			}
			
			if($margin_top){
				$margin_top_css = 'margin-top: '.$margin_top;
			}			
			if($margin_bottom){
				$margin_bottom_css = 'margin-bottom: '.$margin_bottom;
			}
			
			
			$css_animation_classes = "";
			if ( $css_animation !== '' ) {
				$css_animation_classes = 'wpb_animate_when_almost_visible wpb_'. $css_animation .'';
			}

				
			$str = '<div class="boc_icon_holder boc_icon_size_'. esc_attr($size) .' boc_icon_pos_'. esc_attr($icon_position) .' '. esc_attr($css_animation_classes) .' '.esc_attr($css_classes).'" style="'. esc_attr($background_css . $border_radius_css . (isset($margin_top_css) ? $margin_top_css : "") . (isset($margin_bottom_css) ? $margin_bottom_css : "")) .'"><span class="boc_icon '. esc_attr($icon) .'" style="'.esc_attr($color_css).'"></span></div>';
			
			return $str;
	}
	
	add_shortcode('boc_icon', 'shortcode_vc_boc_icon');
}



// Message
if( ! function_exists( 'shortcode_boc_message' ) ) {
	function shortcode_boc_message( $atts, $content = null ) {


		$atts = shortcode_atts(
			array(
				'type' => 'information',
			), $atts);	
		
		return '<div class="'.esc_attr($atts['type']).' closable">'.do_shortcode($content).'</div>';
	}

	add_shortcode('boc_message', 'shortcode_boc_message');
}


// Highlight
if( ! function_exists( 'shortcode_highlight' ) ) {
	function shortcode_highlight( $atts, $content = null ) {
		
		$atts = shortcode_atts(
			array(
				'dark' => 'no',
			), $atts);
		$dark = (($atts["dark"]=='yes')||($atts["dark"]=='Yes')) ? true : false;
		$content = '<strong class="hilite">'.$content.'</strong>';
		return $dark ? str_replace('class="hilite"', 'class="hilite_dark"', do_shortcode($content)) : do_shortcode($content);
	}

	add_shortcode('highlight', 'shortcode_highlight');
}
	
	
	
// Tooltip
if( ! function_exists( 'shortcode_boc_tooltip' ) ) {
	function shortcode_boc_tooltip( $atts, $content = null ) {
		
		$atts = shortcode_atts(
			array(
				'title' => '',
			), $atts);
		$content = '<span class="tooltipsy" original-title="'.esc_attr($atts['title']).'">'.do_shortcode($content).'</span>';
		return $content;
	}
	
	add_shortcode('tooltip', 'shortcode_boc_tooltip');
}