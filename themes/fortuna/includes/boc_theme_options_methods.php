<?php
//  THEME OPTIONS

/**	
	Return Theme Default Color
**/
if( ! function_exists( 'boc_get_default_color' ) ) { 
	function boc_get_default_color() {
	
		$boc_default_color = "#08ada7";
		return $boc_default_color;
	}
}

/**	
	Return Main Theme Color
**/
if( ! function_exists( 'boc_get_main_color' ) ) { 
	function boc_get_main_color() {
	
		$boc_main_color = get_theme_mod('boc_main_color', boc_get_default_color());
		return $boc_main_color;
	}
}


/**
	Return Responsive Page Setting
**/
if( ! function_exists( 'boc_responsive_option' ) ) { 
	function boc_responsive_option() {
	
		if(ot_get_option('responsive_design','on')=='on'){
			$responsive_option = true;
		}else {
			$responsive_option = false;
		}
		
		return $responsive_option ? 'responsive' : 'non-responsive';
	}
}

/**
	Return Wrapper Style
**/
if( ! function_exists( 'boc_page_wrapper_style' ) ) { 
	function boc_page_wrapper_style() {
	
		global $post;
		// Get wrapper style set in Theme Options
		$wrapper_style = ot_get_option('wrapper_style','full_width_wrapper');
		
		// Overwrite Default wrapper style from Page Settings if different than "default" or Null
		if ( is_single() || is_page() ) {
			$page_wrapper_style = get_post_meta($post->ID, 'boc_page_wrapper_style', true);
			if(!($page_wrapper_style && $page_wrapper_style == 'default')) {
				$wrapper_style = $page_wrapper_style;
			}
		}
		return $wrapper_style;
	}
}

/**
	Return Page Sidebar Layout
**/
if( ! function_exists( 'boc_page_sidebar_layout' ) ) { 
	function boc_page_sidebar_layout() {
	
		global $post;
		if(!isset($post->ID)){
			return 'full-width';
		}
		
		$sidebar_layout = ot_get_option('sidebar_layout','full-width');
		$meta_box_layout = get_post_meta($post->ID, "boc_meta_sidebar_layout", true);

		// Overwrite is set in the editor Meta Box
		if($meta_box_layout && ($meta_box_layout!='default')) {
			$sidebar_layout = $meta_box_layout;
		}
		return $sidebar_layout;
	}
}

/**	
	Return Post Sidebar Layout
**/
if( ! function_exists( 'boc_post_sidebar_layout' ) ) { 
	function boc_post_sidebar_layout() {
	
		global $post;
		
		$sidebar_layout = ot_get_option('sidebar_layout_posts','right-sidebar');
		$meta_box_layout = get_post_meta($post->ID, "boc_meta_sidebar_layout", true);
		// Overwrite is set in the editor Meta Box (only if we are not on the index.php template)
		if(($meta_box_layout!='default') && is_single()) {
			$sidebar_layout = $meta_box_layout;
		}
		return $sidebar_layout;
	}
}

/**	
	Return Whether We have SEARCH in the header
**/
if( ! function_exists( 'boc_show_search_in_header' ) ) { 
	function boc_show_search_in_header() {
	
		$show_search = ot_get_option('show_search','on');
		return $show_search;
	}
}

/**	
	Return Whether We have SEARCH SEPARATOR in the header
**/
if( ! function_exists( 'boc_show_search_separator' ) ) { 
	function boc_show_search_separator() {
	
		$show_search_sep = ot_get_option('show_search_separator','on');
		return $show_search_sep;
	}
}

/**	
	Return Main Menu Block Style
**/
if( ! function_exists( 'boc_is_main_nav_block_style' ) ) { 
	function boc_is_main_nav_block_style() {

		return ot_get_option('nav_top_block_style',0);
	}
}
/**	
	Return Header Height
**/
if( ! function_exists( 'boc_header_height' ) ) { 
	function boc_header_height() {

		return ot_get_option('header_height','92');
	}
}

/**	
	Return Whether Header is Sticky
**/
if( ! function_exists( 'boc_is_header_sticky' ) ) { 
	function boc_is_header_sticky() {

		return (ot_get_option('sticky_header','on') == 'on');
	}
}


/**
	Return Sticky Header BGR Color 
**/
if( ! function_exists( 'boc_get_sticky_header_color' ) ) { 
	function boc_get_sticky_header_color() {
	
		return ot_get_option('sticky_header_color', '#ffffff');
	}
}
/**
	Return Sticky Header Opacity 
**/
if( ! function_exists( 'boc_get_sticky_header_opacity' ) ) { 
	function boc_get_sticky_header_opacity() {
	
		return ot_get_option('sticky_header_opacity', '0.97');
	}
}

/**	
	Return Sticky Header Height
**/
if( ! function_exists( 'boc_sticky_header_height' ) ) { 
	function boc_sticky_header_height() {

		return ot_get_option('sticky_header_height','64');
	}
}

/**	
	Return Whether Main Navigation has Underline Effect Setting ON
**/
if( ! function_exists( 'boc_is_main_nav_underline_effect' ) ) { 
	function boc_is_main_nav_underline_effect() {

		return (ot_get_option('main_nav_underline_effect','on') == 'on');
	}
}

/**	
	Return Whether the Submenu Hover Arrow Effect Setting ON
**/
if( ! function_exists( 'boc_is_submenu_arrow_effect' ) ) { 
	function boc_is_submenu_arrow_effect() {

		return (ot_get_option('submenu_arrow_effect','on') == 'on');
	}
}

/**	
	Return Whether the mm_bordered_columns Setting ON
**/
if( ! function_exists( 'boc_is_mm_bordered_columns' ) ) { 
	function boc_is_mm_bordered_columns() {

		return (ot_get_option('mm_bordered_columns','on') == 'on');
	}
}

/**	
	Return Main Navigation Underline Effect Color
**/
if( ! function_exists( 'boc_main_nav_underline_effect_color' ) ) { 
	function boc_main_nav_underline_effect_color() {

		return ot_get_option('main_nav_underline_effect_color', boc_get_default_color());
	}
}

/**
	Return Header Transparency Option 
**/
if( ! function_exists( 'boc_is_transparent_header' ) ) { 
	function boc_is_transparent_header() {
	
		
		// Get post id if on a post or page
		if(is_archive() || is_search() || is_tax() || is_home()){
			$post_id = 0;
		}else {
			global $post;
			$post_id = $post->ID;
		}

		global $woocommerce;
				
		// If shop get shop page id
		if(function_exists('is_shop') && is_shop()){
			$post_id = woocommerce_get_page_id('shop');
		}

		$boc_transparent_header = get_post_meta($post_id, "boc_transparent_header_set", true);
		
		if($boc_transparent_header == 'on') {
			return true;
		}else {
			return false;
		}
	}
}

/**
	Return Transparent Logo Effect (flip or fade) 
**/
if( ! function_exists( 'boc_get_transparent_logo_effect' ) ) { 
	function boc_get_transparent_logo_effect() {

		return ot_get_option('transparent_logo_effect', 'flip');
	}
}

/**
	Return Transparent Header BGR Color 
**/
if( ! function_exists( 'boc_get_transparent_header_color' ) ) { 
	function boc_get_transparent_header_color() {
	
		return ot_get_option('transparent_header_color', '#ffffff');
	}
}
/**
	Return Transparent Header Opacity 
**/
if( ! function_exists( 'boc_get_transparent_header_opacity' ) ) { 
	function boc_get_transparent_header_opacity() {
	
		return ot_get_option('transparent_header_opacity', '0.1');
	}
}

/**
	Return Main Navigation Color 
**/
if( ! function_exists( 'boc_get_main_navigation_color' ) ) { 
	function boc_get_main_navigation_color() {
	
		return ot_get_option('main_navigation_color', '#333333');
	}
}
/**
	Return Main Navigation Hover Color 
**/
if( ! function_exists( 'boc_get_main_navigation_hover_color' ) ) { 
	function boc_get_main_navigation_hover_color() {
	
		return ot_get_option('main_navigation_hover_color', '#333333');
	}
}

/**
	Return Transparent Header Main Navigation Color 
**/
if( ! function_exists( 'boc_get_transparent_header_nav_color' ) ) { 
	function boc_get_transparent_header_nav_color() {
	
		return ot_get_option('transparent_header_navigation_color', '#ffffff');
	}
}
/**
	Return Transparent Header Main Navigation Hover Color 
**/
if( ! function_exists( 'boc_get_transparent_header_nav_hover_color' ) ) { 
	function boc_get_transparent_header_nav_hover_color() {
	
		return ot_get_option('transparent_header_navigation_hover_color', '#ffffff');
	}
}



/**	
	Return Whether full post content or excerpt should be shown
**/
if( ! function_exists( 'boc_blog_full_post_content' ) ) { 
	function boc_blog_full_post_content() {

		return (ot_get_option('blog_full_post_content','off') == 'on');
	}
}

/**	
	Return More link Classes for Blog
**/
if( ! function_exists( 'boc_more_link_classes' ) ) { 
	function boc_more_link_classes() {

		// If 3, output "2 flat"
		return "more-link".((ot_get_option('blog_more_link_style','1')!='3') ? esc_html(ot_get_option('blog_more_link_style','1')) : '2').
		((ot_get_option('blog_more_link_style','1')==3) ? " flat" : "");
	}
}

/**	
	Return More link Classes for Shortcodes
**/
if( ! function_exists( 'boc_more_link_classes_sh' ) ) { 
	function boc_more_link_classes_sh($style=1) {

		// If 3, output "2 flat"
		return "more-link".(($style!='3') ? esc_html($style) : '2').
		(($style==3) ? " flat" : "");
	}
}



/**
	BOC Return Portfolio Item according to type
**/
if( ! function_exists( 'getPortfolioItemIcon' ) ) { 
	function getPortfolioItemIcon($postID) {

		// If Regular type - photo
		if(function_exists( 'get_post_format' ) && get_post_format($postID) != 'gallery' && get_post_format($postID) != 'video' && has_post_thumbnail()) {
			return 'camera';
		}elseif ( function_exists( 'get_post_format' ) && get_post_format( $postID ) == 'gallery' ) {
			return 'gallery';
		}elseif ( function_exists( 'get_post_format' ) && get_post_format( $postID ) == 'video') {
			return 'video';
		}
		return 'camera';
	}
}

