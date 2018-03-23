<?php

/**
	Side Img Featured Text
**/

// Register Shortcode
if( ! function_exists( 'shortcode_boc_side_img_box' ) ) {
	function shortcode_boc_side_img_box( $atts, $content = null ) {
		
		$atts = vc_map_get_attributes('boc_side_img_box', $atts );
		extract( $atts );	

		// Adding a check for VC picture added
		$img = '';
		if($picture_url){
			$vc_image = wp_get_attachment_image($picture_url,'full');
			// If not passed via VC, we get the URL
			if($vc_image){
				$img = $vc_image;
			}else {
				$img = get_template_directory_uri().'/images/user.png';
			}
		}else {
			$img = '<img src="'.get_template_directory_uri().'/images/user.png" />';
		}	
		
		if($round_img) {
			$css_classes .= " round_img";
		}
		if($img_3d) {
			$css_classes .= " img_3d";
		}
		if($img_small) {
			$css_classes .= " img_small";
		}

		$str = '	<div class="image_featured_text '.$css_classes.'">
						'.wp_kses_post($img).'
						<div class="text">
							'.($title ? '<h3>'.do_shortcode(wp_kses_post($title)).'</h3>' : '')
							.do_shortcode($content).'</div>
						'.($author ? '
						<div class="author_position">
							<span class="auth heading_font">'.wp_kses_post($author).'</span>'. ($position ? ' / <span class="pos">'.wp_kses_post($position).'</span>' : '').'
						</div> ' : '').'
					</div>';
		
		$str .=' 
			<script type="text/javascript">
				jQuery(document).ready(function($) {				
					if(typeof side_img_last_row_set === "undefined") {
			
						// Add class "last" to left_img_box section to remove borders.
						$(".section .row_img_featured_texts_border").last().addClass( "last" );
						side_img_last_row_set = true;
					}
				});			
			</script>';
			
		return $str;
	}
	
	add_shortcode('boc_side_img_box', 'shortcode_boc_side_img_box');
}

// Map Shortcode in Visual Composer
vc_map( array(
   "name" => __("Side Img Box", 'Fortuna'),
   "description" => __("Side Image Featured Text", 'Fortuna'),
   "base" 	=> "boc_side_img_box",
   "category" => "Fortuna Shortcodes",
   "icon" 	=> "boc_side_img_box",
   "weight"	=> 64,
   "params" => array(
		array(
			"type" => "attach_image",
			"heading" => __("Picture", 'Fortuna'),
			"param_name" => "picture_url",
		),
		array(
			"type" => "textfield",
			"heading" => __("Heading", 'Fortuna'),
			"param_name" => "title",
			"admin_label"=> true,
			"std" => "",
			"description" => __("Enter a Heading if you want one", 'Fortuna'),
		),
		array(
			"type" 		=> "textarea_html",
			"heading" 	=> __("Text Below Title", 'Fortuna'),
			"param_name" => "content",
			"value" 		=> __("Featured Text", 'Fortuna'),
			"description"=> __("Enter the Featured text", 'Fortuna'),
		),
		array(
			"type" => "textfield",
			"heading" => __("Author", 'Fortuna'),
			"param_name" => "author",
			"admin_label"=> true,
			"description" => __("Enter the Author Name (if any)", 'Fortuna'),
		),
		array(
			"type" => "textfield",
			"heading" => __("Author Position", 'Fortuna'),
			"param_name" => "position",
			"admin_label"=> true,
			"description" => __("Enter the Author Position (if any)", 'Fortuna'),
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Round Image?", 'Fortuna'),
			"param_name" 	=> "round_img",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Side image to be round", 'Fortuna'),						
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("3d Image?", 'Fortuna'),
			"param_name" 	=> "img_3d",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Side image to have a fancy border and a slight shadow", 'Fortuna'),							
		),
		array(
			"type"			=> 'checkbox',
			"heading" 		=> __("Small Image?", 'Fortuna'),
			"param_name" 	=> "img_small",
			"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
			"description" 	=> __("Set to Yes if you want your Side image to be smaller", 'Fortuna'),						
		),		
		array(
			"type" => "textfield",
			"heading" => __("Extra class name", 'Fortuna'),
			"param_name" => "css_classes",
			"value" => "",
			"description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'Fortuna'),
		),
   )
));