<?php

function aqua_customize_register($wp_customize) {

	$wp_customize->add_section( 'main_menu_styles_section', array(
	    'title'          => __( 'Main Menu Style', 'Fortuna' ),
		'description' => 'Pick your Menu Style and its Accent Color. For more Settings (Top level items color overriding, Sticky/Transparent menu settings) head over to Fortuna Options -> Header -> Main Navigation',
	    'priority'       => 35,
	));	
	
  $wp_customize->add_setting( 'boc_main_color', array(
    'default'        => '#08ada7',
    'transport' =>'postMessage',
    'priority'       => 1,
	'sanitize_callback' => 'esc_attr',
    ));
  $wp_customize->add_setting( 'main_menu_style', array(
    'default'        => 'custom_menu_4',
    'transport' =>'postMessage',
    'priority'       => 1,
	'sanitize_callback' => 'esc_attr',
    ));

  $wp_customize->add_setting( 'nav_bgr_color', array(
    'default'        => '#08ada7',
    'transport' =>'postMessage',
    'priority'       => 3,
	'sanitize_callback' => 'esc_attr',
    ));
    
    

  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'boc_main_color', array(
    'label'   => 'Main Color (Default: #08ada7)',
    'section' => 'colors',
    'settings'   => 'boc_main_color',
	'description' => 'Set Main Theme Color',
	'sanitize_callback' => 'esc_attr',
    )));
    
	
    $wp_customize->add_control( 'main_menu_style', array(
	    'label'   => 'Select Navigation Style Preset:',
	    'section' => 'main_menu_styles_section',
	    'type'    => 'select',
	    'choices'    => array(
			'custom_menu_1' => 'Menu Style 1',
			'custom_menu_2' => 'Menu Style 2',
			'custom_menu_3' => 'Menu Style 3',
			'custom_menu_4' => 'Menu Style 4',
			'custom_menu_5' => 'Menu Style 5',
			'custom_menu_6' => 'Menu Style 6'			
	    ),
		'sanitize_callback' => 'esc_attr',
	));       
    
  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nav_bgr_color', array(
    'label'   => 'Navigation Accent Color',
    'section' => 'main_menu_styles_section',
    'settings'   => 'nav_bgr_color',
	'sanitize_callback' => 'esc_attr',
    )));    
    
  // Get it on in preview  
  if ( $wp_customize->is_preview() && ! is_admin() )
    add_action( 'wp_footer', 'aqua_customize_preview', 21);
}


function aqua_customize_preview() {
?>  
    
    <script type="text/javascript">

    function convertHex(hex,opacity){
        hex = hex.replace('#','');
        r = parseInt(hex.substring(0,2), 16);
        g = parseInt(hex.substring(2,4), 16);
        b = parseInt(hex.substring(4,6), 16);

        result = 'rgba('+r+','+g+','+b+','+opacity/100+')';
        return result;
    }

    
    ( function( $ ){
	    wp.customize('boc_main_color',function( value ) {
	    	value.bind(function(to) {
	    		$('#footer').append('<style type="text/css">'+
				
	    			'	a:hover, a:focus,'+
	    			'	.post_content a:not(.button), '+
	    			'	.post_content a:not(.button):visited {	color: '+ to +'; }'+

	    			'	.dark_links a:hover, .white_links a:hover, .dark_links a:hover h2, .dark_links a:hover h3 { color: '+ to +' !important; }'+

	    			'	.side_icon_box h3 a:hover, '+
	    			'	.post_content .team_block h4 a:hover,'+
	    			'	.team_block .team_icons a:hover{ color:'+ to +'; }'+

	    			'	.button:hover,a:hover.button,button:hover,input[type="submit"]:hover,input[type="reset"]:hover,	input[type="button"]:hover, .btn_theme_color, a.btn_theme_color { color: #fff; background-color:'+ to +';}'+
	    			'	input.btn_theme_color, a.btn_theme_color, .btn_theme_color { color: #fff; background-color:'+ to +';}'+
	    			'	.btn_theme_color:hover, input.btn_theme_color:hover, a:hover.btn_theme_color { color: #fff; background-color: #444444;}'+

	    			'	input.btn_theme_color.btn_outline, a.btn_theme_color.btn_outline, .btn_theme_color.btn_outline {'+
	    			'		color: '+ to +' !important;'+
	    			'		border: 2px solid '+ to +';'+
	    			'	}'+
	    			'	input.btn_theme_color.btn_outline:hover, a.btn_theme_color.btn_outline:hover, .btn_theme_color.btn_outline:hover{'+
	    			'		background-color: '+ to +' !important;'+
	    			'	}'+

	    			'	#boc_searchform_close:hover { color:'+ to +';}'+

	    			'	.section_big_title h1 strong, h1 strong, h2 strong, h3 strong, h4 strong, h5 strong { color:'+ to +';}'+
	    			'	.top_icon_box h3 a:hover { color:'+ to +';}'+

	    			'	.htabs a.selected  { border-top: 2px solid '+ to +';}'+
	    			'	.resp-vtabs .resp-tabs-list li.resp-tab-active { border-left: 2px solid '+ to +';}'+
	    			'	.minimal_style.horizontal .resp-tabs-list li.resp-tab-active,'+
	    			'	.minimal_style.resp-vtabs .resp-tabs-list li.resp-tab-active { background: '+ to +';}'+

	    			'	#s:focus {	border: 1px solid '+ to +';}'+

	    			'	.breadcrumb a:hover{ color: '+ to +';}'+

	    			'	.tagcloud a:hover { background-color: '+ to +';}'+
	    			'	.month { background-color: '+ to +';}'+
	    			'	.small_month  { background-color: '+ to +';}'+

	    			'	.post_meta a:hover{ color: '+ to +';}'+

	    			'	.horizontal .resp-tabs-list li.resp-tab-active { border-top: 2px solid '+ to +';}'+
	    			'	.resp-vtabs li.resp-tab-active { border-left: 2px solid '+ to +'; }'+

	    			'	#portfolio_filter { background-color: '+ to +';}'+
	    			'	#portfolio_filter ul li div:hover { background-color: '+ to +';}'+
	    			'	.portfolio_inline_filter ul li div:hover { background-color: '+ to +';}'+

	    			'	.counter-digit { color: '+ to +';}'+

	    			'	.tp-caption a:hover { color: '+ to +';}'+

	    			'	.more-link1:before { color: '+ to +';}'+
	    			'	.more-link2:before { background: '+ to +';}'+

	    			'	.image_featured_text .pos { color: '+ to +';}'+

	    			'	.side_icon_box .icon_feat i.icon { color: '+ to +';}'+
	    			'	.side_icon_box .icon_feat.icon_solid { background-color: '+ to +'; }'+

	    			'	.boc_list_item .li_icon i.icon { color: '+ to +';}'+
	    			'	.boc_list_item .li_icon.icon_solid { background: '+ to +'; }'+

	    			'	.top_icon_box.type1 .icon_holder .icon_bgr { background-color: '+ to +'; }'+
	    			'	.top_icon_box.type1:hover .icon_holder .icon_bgr { border: 2px solid '+ to +'; }'+
	    			'	.top_icon_box.type1 .icon_holder .icon_bgr:after,'+
	    			'	.top_icon_box.type1:hover .icon_holder .icon_bgr:after { border: 2px solid '+ to +'; }'+
	    			'	.top_icon_box.type1:hover .icon_holder i { color: '+ to +';}'+

	    			'	.top_icon_box.type2 .icon_holder .icon_bgr { background-color: '+ to +'; }'+
	    			'	.top_icon_box.type2:hover .icon_holder .icon_bgr { background-color: #fff; }'+
	    			'	.top_icon_box.type2:hover .icon_holder i { color: '+ to +';}'+

	    			'	.top_icon_box.type3 .icon_holder .icon_bgr:after { border: 2px solid '+ to +'; }'+
	    			'	.top_icon_box.type3:hover .icon_holder .icon_bgr { background-color: '+ to +'; }'+
	    			'	.top_icon_box.type3:hover .icon_holder .icon_bgr:after { border: 2px solid '+ to +'; }'+
	    			'	.top_icon_box.type3 .icon_holder i { color: '+ to +';}'+
	    			'	.top_icon_box.type3:hover .icon_holder i { color: #fff; }'+

	    			'	.top_icon_box.type4:hover .icon_holder .icon_bgr { border: 2px solid '+ to +'; }'+
	    			'	.top_icon_box.type4:hover .icon_holder .icon_bgr:after { border: 3px solid '+ to +'; }'+
	    			'	.top_icon_box.type4 .icon_holder i{ color: '+ to +'; }'+
	    			'	.top_icon_box.type4:hover .icon_holder i { color:  '+ to +'; }'+

	    			'	.top_icon_box.type5 .icon_holder i{ color: '+ to +'; }'+
	    			'	.top_icon_box.type5:hover .icon_holder i { color: '+ to +'; }'+

	    			'	h2.title strong {  color: '+ to +';}'+
	    			'	ul.theme_color_ul li:before { color: '+ to +'; }'+

	    			'	.custom_slides.nav_design_1 .cs_nav_item.active .cs_nav_icon i.icon{ color: '+ to +';}'+
	    			'	.custom_slides.nav_style_1.nav_design_1 .cs_nav_item:hover .cs_nav_icon i.icon,'+
	    			'	.custom_slides.nav_style_1.nav_design_2 .cs_nav_item:hover .cs_nav_icon i.icon { color: '+ to +';}'+
	    			'	.custom_slides.nav_design_2 .cs_nav_item.active .cs_nav_icon { background: '+ to +';}'+
	    			'	.cs_nav_item.has_no_text:hover .cs_nav_icon i.icon { color: '+ to +';}'+
	    			'	.custom_slides.nav_style_2 .cs_txt { color: '+ to +';}'+

	    			'	.acc_control, .active_acc .acc_control,'+
	    			'	.acc_holder.with_bgr .active_acc .acc_control { background-color: '+ to +';}'+

	    			'	.text_box.left_border {	border-left: 3px solid '+ to +'; }'+

	    			'	.owl-theme .owl-controls .owl-nav div { background: '+ to +';}'+
	    			'	.owl-theme .owl-dots .owl-dot.active span { background: '+ to +';}'+
	    			'	.img_slider.owl-theme .owl-controls .owl-nav div:not(.disabled):hover { background: '+ to +';}	'+	

	    			'	.testimonial_style_big.owl-theme .owl-controls .owl-nav div:hover,'+
	    			'	.posts_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover, '+
	    			'	.img_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover,'+
	    			'	.portfolio_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover	{ color: '+ to +';}'+

	    			'	.post_item_block.boxed .pic { border-bottom: 3px solid '+ to +'; }'+

	    			'	.team_block .team_desc { color: '+ to +';}'+

	    			'	.bar_graph span, .bar_graph.thin_style span { background-color: '+ to +'; }'+

	    			'	.pagination .links a:hover{ background-color: '+ to +';}'+
	    			'	.hilite{ background: '+ to +';}'+
	    			'	.price_column.price_column_featured ul li.price_column_title{ background: '+ to +';}'+

	    			'	blockquote{ border-left: 3px solid '+ to +'; }'+

	    			'	.fortuna_table tr:hover td { background: '+ convertHex(to, 8) +';}'+

	    			'	.header_cart ul.cart_list li a, .header_cart ul.product_list_widget li a { color: '+ to +';}'+
	    			'	.header_cart .cart-notification { background-color: '+ to +';}'+
	    			'	.header_cart .cart-notification:after { border-bottom-color: '+ to +';}'+

	    			'	.woocommerce .product_meta a { color: '+ to +';}'+

	    			'	.woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit, .woocommerce #content input.button, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button { background-color: '+ to +'!important; }'+
	    			'	.header_cart .cart-wrap	{ background-color: '+ to +'; }'+
	    			'	.header_cart .cart-wrap:before { border-color: transparent '+ to +' transparent; }'+
	    			'	.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce-page .widget_price_filter .ui-slider .ui-slider-range{ background-color: '+ to +' !important;}'+

					
	    			'	.woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus{ background-color: '+ to +' !important;}'+

	    			'	.info h2{ background-color: '+ to +';}'+
	    			'	#footer a:hover { color: '+ to +';}'+

	    			'	a .pic_info.type1 .plus_overlay {	border-bottom: 50px solid '+ convertHex(to, 85) +';}'+
	    			'	a:hover .pic_info.type1 .plus_overlay { border-bottom: 1000px solid '+ convertHex(to, 85) +'; }'+

	    			'	a .pic_info.type2 .plus_overlay { border-bottom: 50px solid '+ convertHex(to, 85) +'; }'+
	    			'	a:hover .pic_info.type2 .plus_overlay {	border-bottom: 860px solid '+ convertHex(to, 85) +';}'+

	    			'	a .pic_info.type3  .img_overlay_icon {	background: '+ convertHex(to, 85) +'; }'+
	    			'	a:hover .pic_info.type3 .img_overlay_icon {	background: '+ convertHex(to, 85) +';}'+

	    			'	a .pic_info.type4 .img_overlay_icon { border-bottom: 2px solid '+ convertHex(to, 85) +';}'+

	    			'	a:hover .pic_info.type5 .info_overlay {	background: '+ to +';}'+

	    			'	.pic_info.type6 .info_overlay {	background: '+ to +';}'+
	    			'	a .pic_info.type6 .plus_overlay { border-bottom: 50px solid '+ to +'; }'+

	    			'	.pic_info.type7 .info_overlay {	background: '+ convertHex(to, 85) +';}'+					
				
		 	'</style>');
	    	 });
	    });

	    wp.customize('main_menu_style',function( value ) {
	        value.bind(function(to) {
	        	$('#menu').parent().removeClass('custom_menu_1').removeClass('custom_menu_2').removeClass('custom_menu_3').removeClass('custom_menu_4').removeClass('custom_menu_5').removeClass('custom_menu_6').addClass(to);
	        });
	    });

	    wp.customize('nav_bgr_color',function( value ) {
	        value.bind(function(to) {
	        	$('#footer').append('<style type="text/css">'+        

					'   	.custom_menu_1 #menu > ul > li div { border-top: 2px solid '+ to +'; }' +

					'   	.custom_menu_2 #menu > ul > li div { border-top: 2px solid '+ to +'; }' +

					'   	.custom_menu_3 #menu > ul > li div { border-top: 2px solid '+ to +'; }' +
					'   	.custom_menu_3 #menu > ul > li ul > li > a:hover { background-color: '+ to +'; }' +

					'   	.custom_menu_4 #menu > ul > li div { border-top: 2px solid '+ to +'; }' +
					'   	.custom_menu_4 #menu > ul > li ul > li > a:hover { background-color: '+ to +'; }' +
						
					'   	.custom_menu_5 #menu > ul > li ul > li > a:hover { background-color: '+ to +'; }' +
					'   	.custom_menu_5 #menu > ul > li:hover > a { border-top: 2px solid '+ to +'; }' +

					'   	.custom_menu_6 #menu > ul > li ul > li > a:hover { background-color: '+ to +'; }' +
					'   	.custom_menu_6 #menu > ul > li:not(.boc_nav_button):hover > a { border-top: 2px solid '+ to +'; }' +
	
			 	'</style>');
	        });
	    });     

	} )( jQuery )
    </script>
    <?php 
}