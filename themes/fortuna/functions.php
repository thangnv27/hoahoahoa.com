<?php

//--------- OT THEME OPTIONS ------------//

// Hide unneeded sections from Import/Export Page
add_filter( 'ot_show_options_ui', '__return_false' );
add_filter( 'ot_show_settings_import', '__return_false' );
add_filter( 'ot_show_settings_export', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_show_docs', '__return_false' );

// Run in Theme Mode
add_filter( 'ot_theme_mode', '__return_true' );

load_template( trailingslashit( get_template_directory() ) . 'includes/ext/option-tree/ot-loader.php' );
// BOC Theme Options
load_template( trailingslashit( get_template_directory() ) . 'includes/boc_theme_options.php' );

//--------- OT THEME OPTIONS :: END  ----//



//--------- Fortuna Specific Methods ------------//

// Fortuna Customizer Theme Options
load_template( trailingslashit( get_template_directory() ) . 'includes/customizer_theme-options.php' );
load_template( trailingslashit( get_template_directory() ) . 'includes/meta_boxes.php' );

// Default RSS feed links
add_theme_support('automatic-feed-links');


// Post Formats
add_theme_support( 'post-formats',  array( 'gallery','video' ));
add_post_type_support( 'post', 'post-formats' );
add_post_type_support( 'portfolio', 'post-formats' );

// Sets up the content width value based on the theme's design and stylesheet (Required by Theme Check)
if (!isset($content_width)){
	$content_width = 1200;
}

// Let WP manage title - it's removed from header.php
add_theme_support( 'title-tag' );

// Fortuna Customizer Theme Options
add_action( 'customize_register', 'aqua_customize_register' );


//	Fortuna suggested plugins
load_template( trailingslashit( get_template_directory() ) . 'includes/ext/class-tgm-plugin-activation.php');
add_action( 'tgmpa_register', 'boc_theme_register_required_plugins' );


//	Global theme specific JS params array : $boc_js_params
$boc_js_params = array();
$boc_js_params['boc_is_mobile_device'] 	= wp_is_mobile();
$boc_js_params['boc_theme_url'] 		= get_template_directory_uri();


//	Enqueue BOC Styles
add_action( 'wp_enqueue_scripts', 'boc_styles' );
function boc_styles() {
    
	wp_enqueue_style( 'boc-grid', get_template_directory_uri().'/stylesheets/grid.css');
	wp_enqueue_style( 'boc-icon', get_template_directory_uri().'/stylesheets/icons.css');
	
	// Load custom woo CSS if plugin is active
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_style( 'boc-woo-styles', get_template_directory_uri().'/stylesheets/woocommerce.css');
	}
	
	// Load Main CSS
	wp_enqueue_style( 'boc-main-styles', get_bloginfo( 'stylesheet_url' ), array('js_composer_front') );

	// Animations CSS - with a dependency, load it after VC Css to overwrite the animations
	wp_enqueue_style( 'boc-animation-styles', get_template_directory_uri().'/stylesheets/animations.css', array('js_composer_front'));

	// Load Responsive/NonResponsive styles
	if(ot_get_option('responsive_design','on')=='on'){
		wp_enqueue_style( 'boc-responsive-style', get_template_directory_uri().'/stylesheets/grid_responsive.css' );
	}else {
		wp_enqueue_style( 'boc-nonresponsive-style', get_template_directory_uri().'/stylesheets/non-responsive.css' );
	}

	// 	Load RTL if needed
	if (is_rtl()) {
		boc_styles_rtl();
	}
	
	// Attach Custom CSS to the last CSS file so one can override all CSS files
	$inline_css = boc_get_inline_CSS();
	wp_add_inline_style( 'boc-animation-styles', $inline_css );
}

// 	RTL styles
function boc_styles_rtl() {
    
	wp_enqueue_style( 'boc-grid-rtl', get_template_directory_uri().'/stylesheets/rtl-grid.css');
	
	// Load custom woo CSS if plugin is active
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_style( 'boc-woo-styles-rtl', get_template_directory_uri().'/stylesheets/rtl-woocommerce.css');
	}
	
	// Load Main CSS
	wp_enqueue_style( 'boc-main-styles-rtl', get_template_directory_uri().'/stylesheets/rtl-style.css');
	
	// Animations CSS - with a dependency, load it after VC Css to overwrite the animations
	wp_enqueue_style( 'boc-animation-styles', get_template_directory_uri().'/stylesheets/rtl-animations.css', array('js_composer_front'));
	
	if(ot_get_option('responsive_design','on')=='on'){
		wp_enqueue_style( 'boc-responsive-style-rtl', get_template_directory_uri().'/stylesheets/rtl-grid_responsive.css' );
	}else {
		wp_enqueue_style( 'boc-nonresponsive-style-rtl', get_template_directory_uri().'/stylesheets/rtl-non-responsive.css' );
	}	
}


// Register Default Google Fonts
function boc_fonts_url() {
    $font_url = '';
    
    /*
    Translators: If there are characters in your language that are not supported
    by chosen font(s), translate this to 'off'. Do not translate into your own language.
    */
    if ( 'off' !== _x( 'on', 'Default Google Fonts: on or off', 'Fortuna' ) ) {
        $font_url = add_query_arg( 'family', urlencode( 'Droid Serif:400,700,400italic,700italic|Lato:300,400,700,400italic|Montserrat:400,700' ), "//fonts.googleapis.com/css" );
    }
    return $font_url;
}

// Enqueue Default Google Fonts
function boc_add_default_fonts() {
    wp_enqueue_style( 'boc-fonts', boc_fonts_url(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'boc_add_default_fonts' );




// Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'boc_scripts' );	
function boc_scripts() {
	global $boc_js_params;

	$boc_js_params['boc_submenu_animation_effect'] = ot_get_option('submenu_animation_effect', 'sub_fade_in');

	wp_enqueue_script('fortuna.lib', get_template_directory_uri().'/js/libs.min.js', array('jquery'));
	
	wp_enqueue_script('fortuna.common', get_template_directory_uri().'/js/common.js');
	wp_localize_script('fortuna.common', 'bocJSParams', $boc_js_params );

	// Smooth Scrolling
	if(ot_get_option('smooth_scrolling','on') == 'on'){
		wp_enqueue_script('smoothscroll', get_template_directory_uri().'/js/jquery.smoothscroll.js');
	}

	// Retina.js
	if(ot_get_option('retina','off') == 'on'){
		//wp_enqueue_script('retina_js', get_template_directory_uri().'/js/retina.min.js' ); // Commented out since 1.3+
		
		// Retina Logo substitute
		if ( ! function_exists( 'boc_retina_logo' ) ) {
			function boc_retina_logo() {
				$retina_logo_url = ot_get_option('logo_upload_retina','');
				$retina_logo_transparent_url = ot_get_option('logo_transparent_upload_retina','');
				
				if ($retina_logo_url !== '') {
					$output = '<!-- Retina Logo -->
					<script type="text/javascript">
						jQuery(function($){
							if (window.devicePixelRatio >= 2) {
								$(".logo_img > a > img").attr("src", "'. esc_url($retina_logo_url) .'");
							}
						});
					</script>';	
					$output =  preg_replace( '/\s+/', ' ', $output );
					echo $output;
				}				
				if ($retina_logo_transparent_url !== '') {
					$output = '<!-- Retina Logo -->
					<script type="text/javascript">
						jQuery(function($){
							if (window.devicePixelRatio >= 2) {
								$("#transparent_logo > img").attr("src", "'. esc_url($retina_logo_transparent_url) .'");
							}
						});
					</script>';	
					$output =  preg_replace( '/\s+/', ' ', $output );
					echo $output;
				}
			}
		}
		add_action('wp_head', 'boc_retina_logo');		
	}	
		
	// Load RTL scripts if needed
	if (is_rtl()) {
		wp_enqueue_script('fortuna.common-rtl', get_template_directory_uri().'/js/rtl-common.js');
	}		
	
}


// Include Core Theme Functions + Widgets
load_template( trailingslashit( get_template_directory() ) . 'includes/boc_theme_options_methods.php' );
load_template( trailingslashit( get_template_directory() ) . 'includes/boc_framework.php' );

// BOC Shortcodes
load_template( trailingslashit( get_template_directory() ) . 'includes/shortcodes/shortcodes.php' );
load_template( trailingslashit( get_template_directory() ) . 'includes/boc_widgets.php' );

add_action('widgets_init', 'boc_load_widgets');


// Make theme available for translation
load_theme_textdomain( 'Fortuna', get_template_directory() . '/languages' );


// Images
add_theme_support('post-thumbnails');
//set_post_thumbnail_size(600, 380, true); //size of thumbs

add_image_size('boc_thumb', 150, 150, true);
add_image_size('boc_medium', 600, 380, true);
add_image_size('boc_thin', 1200, 600, true);

// Load WP Comment Reply JS script
if(is_singular()){
	wp_enqueue_script( 'comment-reply' );
}

// Replace the excerpt and THE-MORE "more" links with custom
function boc_excerpt_more($more) {
	global $post;
	return '<div class="h10"></div><a class="'.boc_more_link_classes().'" href="'. esc_url(get_permalink($post->ID)) . '">'.__('Read more','Fortuna').'</a>';
}
add_filter('excerpt_more', 'boc_excerpt_more');

function boc_the_more_tag_link() {
	return '<div class="h10"></div><a class="'.boc_more_link_classes().'" href="' . esc_url(get_permalink()) . '">'.__('Read more','Fortuna').'</a>';
}
add_filter( 'the_content_more_link', 'boc_the_more_tag_link' );

// Change default length of Excerpt
function boc_excerpt_length() {
	return 60;
}
add_filter('excerpt_length', 'boc_excerpt_length');

// Custom WP_title filter
function boc_wp_title( $title, $sep ) {		
	
	if ( is_feed() ) {
		return $title;
	}
	
	global $page, $paged;

	// Add the blog name
	$title .= get_bloginfo( 'name', 'display' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	// Add a page number if necessary:
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title .= " $sep " . sprintf( __( 'Page %s', 'Fortuna' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'boc_wp_title', 10, 2 );



// Main Fortuna Startup
add_action('init', 'boc_fortuna_init');

function boc_fortuna_init(){
	boc_register_menus();
	boc_register_widgets();
}

	 
// Register Menus
function boc_register_menus(){
	register_nav_menus( array(
			'main_navigation' => 'Main Navigation',
			'subheader_navigation' => 'Sub-Header Menu'
	));
}

// Register Widgets
function boc_register_widgets(){

	if(function_exists('register_sidebar')) {
		
		// Register Dynamic Widgets (OT)
		if (ot_get_option('boc_sidebars')){
			$dynamic_sidebars = ot_get_option('boc_sidebars');
			foreach ($dynamic_sidebars as $dynamic_sidebar) {
				register_sidebar(array(
					'name' => $dynamic_sidebar["title"],
					'id' => $dynamic_sidebar["id"],
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h4 class="boc_heading bgr_dotted"><span>',
					'after_title' => '</span></h4>',
					));
			}
		}
	
		// Register widgetized locations
		register_sidebar(array(
			'name' => 'Fortuna Default Sidebar',
			'id'   => 'fortuna_default_sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="boc_heading bgr_dotted"><span>',
			'after_title' => '</span></h4>',
		));
		register_sidebar(array(
			'name' => 'Footer Widget 1',
			'id'   => 'fortuna_footer_widget1',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'Footer Widget 2',
			'id'   => 'fortuna_footer_widget2',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'Footer Widget 3',
			'id'   => 'fortuna_footer_widget3',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'Footer Widget 4',
			'id'   => 'fortuna_footer_widget4',
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'WooCommerce Sidebar',
			'id'   => 'fortuna_woocommerce_sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="boc_heading bgr_dotted"><span>',
			'after_title' => '</span></h4>',
		));	
		register_sidebar(array(
			'name' => 'WooCommerce Product Page Sidebar',
			'id'   => 'fortuna_woocommerce_product_sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="boc_heading bgr_dotted"><span>',
			'after_title' => '</span></h4>',
		));		
		
	}	
	
}


/**
 * add a default-gravatar to options
 */
if ( !function_exists('fb_addgravatar') ) {
	function fb_addgravatar( $avatar_defaults ) {
		$myavatar = get_template_directory_uri() . '/images/comment_avatar.png';
		$avatar_defaults[$myavatar] = 'people';
		return $avatar_defaults;
	}
	add_filter( 'avatar_defaults', 'fb_addgravatar' );
}


// Use shortcodes in Widgets
add_filter('widget_text', 'do_shortcode');

// Customize Tag Cloud
function boc_tag_cloud_args($in){
    return 'smallest=13&largest=13&number=25&orderby=name&unit=px';
}
add_filter( 'widget_tag_cloud_args', 'boc_tag_cloud_args');




///////////////////////////////////////////
// --------   Visual Composer  --------- //
///////////////////////////////////////////

// Initialize Visual Composer as part of the theme
if(function_exists('vc_set_as_theme')){

	vc_set_as_theme(true);

	// Disable front end editor
	vc_disable_frontend();

	// Remove Brainstormforce link in Dashboard & Nag message
	define('BSF_UNREG_MENU', true);
	define('BSF_7320433_NAG', false);
	
	// Set custom VC templates DIR
	$vc_res_dir = get_template_directory().'/vc/vc_templates/';
	vc_set_shortcodes_templates_dir($vc_res_dir);

	// Call custom Fortuna method that extends VC shortcodes
	load_template( trailingslashit( get_template_directory() ) . 'includes/shortcodes/vc_shortcodes.php' );

	// Extend VC
	boc_extend_VC_shortcodes();
	
	// Remove VC default modules
	boc_modify_default_VC_modules();
	
	// Set VC by default for Page & Portfolio post types
	vc_set_default_editor_post_types(array('page',	'portfolio'));	
	
	// Replace VC waypoints, we'll use our own in libs.js
	function boc_remove_VC_default_waypoints() {
		// Dequeue VC waypoints.js that is dynamically included when an animated element is found
		wp_deregister_script('waypoints');
	}
	add_action( 'vc_base_register_front_js', 'boc_remove_VC_default_waypoints', 100);
}


///////////////////////////////////////////
// ----------   Rev Slider   ----------- //
///////////////////////////////////////////

// Disable the notification for activation
if ( is_admin() ) {
	if(function_exists( 'set_revslider_as_theme' )){
		add_action( 'init', 'boc_set_Rev_Slider_as_theme' );
		function boc_set_Rev_Slider_as_theme() {
			update_option('revslider-valid-notice', 'false');
			set_revslider_as_theme();
		}
	}
}

///////////////////////////////////////////
// ----------   WooCommerce  ----------- //
///////////////////////////////////////////

add_theme_support( 'woocommerce' );
// IF woocommerce is activated
if ( class_exists( 'woocommerce' ) ) {
	// IF lightbox is enabled in admin we shall enable the custom one
	$boc_js_params['boc_woo_lightbox_enabled'] = get_option( 'woocommerce_enable_lightbox' ) == 'yes' ? 1 : 0;

	//	Remove Default WOO lightbox js (prettyphoto)
	add_action( 'wp_print_scripts', 'boc_deregister_woo_pp_js', 100 );
	function boc_deregister_woo_pp_js() {
		wp_deregister_script( 'prettyPhoto' );
		wp_deregister_script( 'prettyPhoto-init' );
	}
	//	Remove Default WOO lightbox css (prettyphoto)
	add_action( 'wp_print_styles', 'boc_deregister_woo_pp_css', 100 );
	function boc_deregister_woo_pp_css() {
		wp_deregister_style( 'woocommerce_prettyPhoto_css' );
	}

	// Remove Page Title
	add_filter( 'woocommerce_show_page_title', 'boc_remove_woo_shop_title');
	// Remove woo page title from shop
	if ( !function_exists( 'boc_remove_woo_shop_title' ) ) {
		function boc_remove_woo_shop_title() {
			if (function_exists('is_shop') && is_shop() && is_singular('product') ) {
				return false;
			}
		}
	}

	function boc_close_div() {
		echo '</div>';
	}

	//wrap single product image in an extra div
	add_action( 'woocommerce_before_single_product_summary', 'boc_images_div', 2);
	add_action( 'woocommerce_before_single_product_summary',  'boc_close_div', 20);
	function boc_images_div()
	{
		echo "<div class='col span_5 single_product_left'>";
	}

	//wrap product description
	add_action('woocommerce_before_single_product_summary', 'boc_summary_div', 35);
	add_action('woocommerce_after_single_product_summary',  'boc_close_div', 4);
	function boc_summary_div() {
		echo "<div class='col span_7 single_product_right'>";
	}

	//change tab position to be inside summary
	remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
	add_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1);	


	// Show upsells and related products
	remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products',20);
	remove_action('woocommerce_after_single_product', 'woocommerce_output_related_products',10);


	remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
	remove_action('woocommerce_after_single_product', 'woocommerce_upsell_display',10);
	add_action('woocommerce_after_single_product_summary', 'boc_woocommerce_output_upsells', 21);

	function boc_woocommerce_output_upsells() {

		$output = null;

		ob_start();
		woocommerce_upsell_display(4,4); 
		$content = ob_get_clean(); 
		if($content) { $output .= $content; }

		echo $output;
	}


	// Custom Cart in Header
	add_filter('add_to_cart_fragments', 'boc_woocommerce_header_add_to_cart_fragment');
	function boc_woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		
		ob_start(); ?>
		
		<a class="cart-contents icon icon-shopping631" href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>">
			<p class="cart-wrap"><span><?php echo $woocommerce->cart->cart_contents_count; ?></span></p>
		</a>
		
		<?php
		$fragments['a.cart-contents'] = ob_get_clean();
		
		return $fragments;
	}


	add_action( 'woocommerce_before_single_product', 'boc_wrap_single_product_image', 8);
	add_action( 'woocommerce_after_single_product', 'boc_close_div', 9);

	function boc_wrap_single_product_image() {

		echo "<div class='boc_single_product'>";
	}


	// Set products per page as defined in OT
	add_filter( 'loop_shop_per_page', 'boc_products_per_page', 20 );
	function boc_products_per_page() {
		return ot_get_option('woocommerce_products_per_page','12');
	}

	// Add 'dark_links' class wrapper div to product loop items
	add_action('woocommerce_before_shop_loop_item','boc_product_item_dark_link_open');
	add_action('woocommerce_after_shop_loop_item','boc_close_div');
	function boc_product_item_dark_link_open(){
		echo '<div class="dark_links">';
	}
}

// Fix Chrome Dashboard Menu problem
function boc_chromefix_inline_css()
{ 
  wp_add_inline_style( 'wp-admin', '#adminmenu { transform: translateZ(0); }' );
}

add_action('admin_enqueue_scripts', 'boc_chromefix_inline_css');

/**
 * Change permalink for Affiliate
 */
function ppo_wp_aff_append_query_string($permalink, $post, $leavename) {
    global $wp_post_types;
    foreach ($wp_post_types as $type => $custom_post) {
        if($type == $post->post_type and isset($_SESSION['user_id'])){
            if(strpos($permalink, "?") !== FALSE){
                $permalink .= "&ap_id=" . $_SESSION['user_id'];
            }else{
                $permalink .= "?ap_id=" . $_SESSION['user_id'];
            }
        }
    }
    return $permalink;
}

add_filter('post_type_link', 'ppo_wp_aff_append_query_string', 10, 3);