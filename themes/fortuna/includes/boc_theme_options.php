<?php
///////////////////////////////////////////
//---- OT THEME SPECIFIC OPTIONS --------//
///////////////////////////////////////////


/**
 * Initialize the options before anything else. 
 */
add_action( 'admin_init', 'boc_custom_theme_options', 1 );


//  Add Theme Specific Fonts to Default Font list
add_filter( 'ot_recognized_font_families', 'boc_custom_fonts_add', 1, 3 );
function boc_custom_fonts_add( $families, $field_id ) {
	$families = array_merge($families,
		array(
			'montserrat'	=> 'Montserrat',
			'droidserif'  	=> 'Droid Serif',
			'lato' 			=> 'Lato',
		)
	);
	return $families;
}

//	Filter Out Some Typography Option Fields
add_filter( 'ot_recognized_typography_fields', 'boc_filter_typography_fields', 10, 2 );
function boc_filter_typography_fields( $array, $field_id ) {

    if ($field_id == 'nav_font') {
		$array = array( 'font-family', 'font-size', 'font-weight', 'letter-spacing', 'text-transform');
    }elseif ($field_id == 'sub_nav_font'){
		$array = array( 'font-family', 'font-size', 'font-weight', 'letter-spacing', 'text-transform');
    }elseif ($field_id == 'heading_font'){
		$array = array( 'font-family', 'font-weight', 'letter-spacing', 'text-transform');
    }elseif ($field_id == 'body_font'){
		$array = array( 'font-family', 'font-size', 'font-weight');
    }elseif ($field_id == 'button_font'){
		$array = array( 'font-family', 'letter-spacing', 'font-weight', 'text-transform');
    }
    return $array;
}

//	Change default OT Upload text
add_filter( 'ot_upload_text', 'boc_ot_upload_text', 10, 2 );
function boc_ot_upload_text($txt) {
	return 'Upload';
}



/**
 * BOC Custom types.
 */
function boc_custom_theme_options() {

  /**
   * Get a copy of the saved settings array. 
   */
  $saved_settings = get_option( 'option_tree_settings', array() );
  
  
  /**
   * Create a custom settings array that we pass to 
   * the OptionTree Settings API Class.
   */

$custom_settings = array(
  'sections'        => array(
      array(
        'title'       => '<span class="ot-icon-desktop"></span> Header',
        'id'          => 'header',
        ),
      array(
        'title'       => '<span class="ot-icon-font"></span>Typography',
        'id'          => 'typography'
        ),
      array(
        'id'          => 'general',
        'title'       => '<span class="ot-icon-gear"></span> General'
        ),
      array(
        'id'          => 'footer',
        'title'       => '<span class="ot-icon-desktop"></span> Footer'
        ),
      array(
        'id'          => 'portfolio',
        'title'       => '<span class="ot-icon-briefcase"></span> Portfolio'
        ),
      array(
        'id'          => 'blog',
        'title'       => '<span class="ot-icon-th-list"></span> Blog'
        ),
      array(
        'id'          => 'sidebars',
        'title'       => '<span class="ot-icon-columns"></span> Sidebars'
        ),
	  array(
        'id'          => 'contact',
        'title'       => '<span class="ot-icon-envelope-o"></span> Contact Page'
        ),
	  array(
        'id'          => 'woocommerce',
        'title'       => '<span class="ot-icon-tag"></span> WooCommerce'
        ),
	  array(
        'id'          => 'custom_styles',
        'title'       => '<span class="ot-icon-code"></span> Custom CSS'
        ),
      ),
  'settings'        => array(
	
	array(
	  'id'          => 'tab_Main',
	  'label'       => 'Main',
	  'type'        => 'tab',
	  'section'     => 'header'
	),
	
	array(
	  'label'       => 'Upload Logo',
	  'id'          => 'logo_upload',
	  'type'        => 'upload',
	  'desc'        => 'Upload your Logo here. Our sample logo is 180x54, if yours is much different in size and looks way off, you can use the header height options below to change its default margins.',
	  'section'     => 'header',
	),	
	array(
	  'label'       => 'Upload Retina Logo (not required)',
	  'id'          => 'logo_upload_retina',
	  'type'        => 'upload',
	  'desc'        => 'Upload your Retina Logo version here. Make sure it is double the size of the regular Logo',
	  'section'     => 'header',
	),
	  
	  
  	array(
    'label'       => 'Upload Favicon',
    'id'          => 'favicon_uploaded',
    'type'        => 'upload',
    'desc'        => 'Upload a favicon image (16x16)',
    'section'     => 'header',
    ),
	
	array(
	  'label'       => 'Header Height',
	  'id'          => 'header_height',
	  'desc'        => 'Change your Heager Height in <strong>px</strong>. Default value is <strong>92px</strong>',
	  'std'         => '92',
	  'type'        => 'numeric-slider',
	  'section'     => 'header',
	  'min_max_step'=> '50,150,1'
	),	
	
	
	array(
	  'label'       => 'Sticky Header',
	  'id'          => 'sticky_header',
	  'type'        => 'on_off',
	  'desc'        => 'Set to OFF if you want a normal header that doesn\'t stay fixed at the top of the browser when a user scrolls down.',
	  'std'         => 'on',
	  'section'     => 'header',
	),  
	
	array(
	  'label'       => 'Sticky Header Height',
	  'id'          => 'sticky_header_height',
	  'desc'        => 'Change your Sticky Heager Height in <strong>px</strong>. Once a user scrolls down and the sticky header will resize to this height. Default value is <strong>64px</strong>',
	  'std'         => '64',
	  'type'        => 'numeric-slider',
	  'section'     => 'header',
	  'min_max_step'=> '40,100,1',
	  'condition'	=> "sticky_header:is(on)",
	),	
	
	
	array(
	  'label'       => 'Sticky Header Background Color',
	  'id'          => 'sticky_header_color',
	  'type'        => 'colorpicker',
	  'desc'        => 'Change your Sticky Header Background Color when a user has scrolled down',
	  'section'     => 'header',
	  'std'         => '#ffffff',
	  'condition'	=> "sticky_header:is(on)",
	  ),
	array(
	  'label'       => 'Sticky Header Opacity',
	  'id'          => 'sticky_header_opacity',
	  'desc'        => 'Set your Sticky Header Opacity',
	  'std'         => '0.97',
	  'type'        => 'numeric-slider',
	  'section'     => 'header',
	  'min_max_step'=> '0.5,1,0.01',
	  'condition'	=> "sticky_header:is(on)",	  	  
	),	

	array(
	  'id'          => 'tab_main_navigation',
	  'label'       => 'Main Navigation',
	  'type'        => 'tab',
	  'section'     => 'header',
	),
	
	array(
	  'label'       => 'Main Navigation Text Color (Top level items only)',
	  'id'          => 'main_navigation_color',
	  'type'        => 'colorpicker',
	  'desc'        => 'Overwrite your Main Navigation color. <strong>To Select your Main Menu Style please head over to Appearance -> Customize -> Main Menu Style</strong>',
	  'section'     => 'header',
	  'std'         => '#333333',
	  ),
	array(
	  'label'       => 'Main Navigation Hover Text Color (Top level items only)',
	  'id'          => 'main_navigation_hover_color',
	  'type'        => 'colorpicker',
	  'desc'        => 'Overwrite your Main Navigation Hover color',
	  'section'     => 'header',
	  'std'         => '#333333',
	  ),	
	
	array(
	  'label'       => 'Main Navigation Animated Underline Effect',
	  'id'          => 'main_nav_underline_effect',
	  'type'        => 'on_off',
	  'desc'        => 'Animates an underline effect when hovering top level Main Navigation items. (It only makes sense for menus 1-4)',
	  'std'         => 'on',
	  'section'     => 'header',
	),
	array(
	  'label'       => 'Main Navigation Animated Underline Effect Color',
	  'id'          => 'main_nav_underline_effect_color',
	  'type'        => 'colorpicker',
	  'section'     => 'header',
	  'std'         => boc_get_main_color(),
	  'condition'	=> "main_nav_underline_effect:is(on)",	 
	  ),
	  
	array(
	  'label'       => 'Main Navigation Block Style',
	  'id'          => 'nav_top_block_style',
	  'type'        => 'select',
	  'desc'        => 'Change to Yes if you want the Main Navigation to become Block Style - to start on a new line, below the Logo.',
	  'choices'     => array(
	    array(
	      'label'       => 'Yes',
	      'value'       => 1
	      ),
	    array(
	      'label'       => 'No',
	      'value'       => 0
	      )	      
	    ),
	  'std'         => 0,
	  'section'     => 'header',
	), 

	
	array(
	  'label'       => 'SubMenu/Dropdown Appear Animation Effect',
	  'id'          => 'submenu_animation_effect',
	  'type'        => 'select',
	  'desc'        => 'Pick how the submenu should appear',
	  'choices'     => array(
	    array(
	      'label'       => 'Fade In',
	      'value'       => 'sub_fade_in'
	      ),
	    array(
	      'label'       => 'Fade In from Bottom',
	      'value'       => 'sub_fade_from_btm'
	      )	      
	    ),
	  'section'     => 'header',
	),	
	
	array(
	  'label'       => 'SubMenu/Dropdown Arrow Effect on Hover',
	  'id'          => 'submenu_arrow_effect',
	  'type'        => 'on_off',
	  'desc'        => 'Animates an arrow in front of the submenu item when hovered',
	  'std'         => 'on',
	  'section'     => 'header',
	),	
	
	array(
	  'label'       => 'Search Field in the Header',
	  'id'          => 'show_search',
	  'desc'        => 'You can choose "Off" if you want to hide the Search Field in the Header',
	  'type'        => 'on_off',
	  'std'			=> 'on',
	  'section'     => 'header',
	  ),
	array(
	  'label'       => 'Search Field Separator',
	  'id'          => 'show_search_separator',
	  'desc'        => 'You can choose "Off" if you want to hide the Search Field Separator in the header',
	  'type'        => 'on_off',
	  'std'			=> 'on',
	  'section'     => 'header',
	  'condition'	=> "show_search:is(on)",	  
	  ),
	
	
	array(
	  'label'       => 'Mega Menu Bordered Columns',
	  'id'          => 'mm_bordered_columns',
	  'type'        => 'on_off',
	  'desc'        => 'Have a divider border between columns in your megamenu dropdown',
	  'std'         => 'on',
	  'section'     => 'header',
	),	
	
	
	array(
	  'id'          => 'tab_subheader',
	  'label'       => 'Subheader',
	  'type'        => 'tab',
	  'section'     => 'header'
	),
	  
	array(
	  'label'       => 'Subheader Section in Header (with contact details, social icons and Search)',
	  'id'          => 'subheader',
	  'type'        => 'on_off',
	  'std'			=> 'off',
	  'desc'        => 'Show the top subheader section within your header.',
	  'section'     => 'header',
	),  
	array(
	  'label'       => 'Hide Subheader on Scroll for Sticky Header',
	  'id'          => 'hide_subheader_on_scroll',
	  'type'        => 'on_off',
	  'std'			=> 'on',
	  'desc'        => 'When a user scrolls down and the Sticky Header is activated the Subheader will hide',
	  'section'     => 'header',
	  'condition'	  => "subheader:is(on)",
	), 	
    array(
	  'label'       => 'Header Contact Email',
	  'id'          => 'header_email',
	  'type'        => 'text',
	  'desc'        => 'Enter your Email if you want it to appear in the theme header',
	  'section'     => 'header',
	  'condition'	  => "subheader:is(on)",
	  ),
	array(
	  'label'       => 'Header Contact Phone',
	  'id'          => 'header_phone',
	  'type'        => 'text',
	  'desc'        => 'Enter your Phone Number if you want it to appear in the theme header',
	  'section'     => 'header',
	  'condition'	  => "subheader:is(on)",
	  ),       
 
	array(
	  'label'       => 'Header contacts position',
	  'id'          => 'header_contacts_position_is_left',
	  'type'        => 'select',
	  'desc'        => 'Align the contact details in your header (phone number/email) left or right',
	  'choices'     => array(
	    array(
	      'label'       => 'Left',
	      'value'       => 1
	      ),
	    array(
	      'label'       => 'Right',
	      'value'       => 0
	      )	      
	    ),
	  'std'         => 1,
	  'section'     => 'header',
	  'condition'	  => "subheader:is(on)",
	),

 
	array(
	'label'       => 'Header Social Icons',
	'id'          => 'header_icons',
	'type'        => 'list-item',
	'desc'        => 'Manage your Socials Icons in the theme header. For Skype use "callto://YourSkypeName".',
	'settings'    => array(
		array(
			'id'          => 'icons_service',
			'label'       => 'Choose service',
			'type'        => 'select',
			'choices'     => array(
					  //	Value maps to the Icon font
					  array('value'=> 'icon-facebook3','label' => 'Facebook','src'=> ''),
					  array('value'=> 'icon-googleplus2','label' => 'Google+','src'=> ''),
					  array('value'=> 'icon-pinterest2','label' => 'Pinterest','src'=> ''),
					  array('value'=> 'icon-skype2','label' => 'Skype','src'=> ''),
					  array('value'=> 'icon-twitter3','label' => 'Twitter','src'=> ''),
					  array('value'=> 'icon-linkedin3','label' => 'LinkedIn','src'=> ''),
					  array('value'=> 'icon-instagram2','label' => 'Instagram','src'=> ''),
					  array('value'=> 'icon-youtube','label' => 'Youtube','src'=> ''),
					  array('value'=> 'icon-vimeo','label' => 'Vimeo','src'=> ''),
					  array('value'=> 'icon-tumblr3','label' => 'Tumblr','src'=> ''),
					  array('value'=> 'icon-xing','label' => 'Xing','src'=> ''),
					  array('value'=> 'icon-vk','label' => 'VK','src'=> ''),
					  ),
			),
		
		array(
		  'label'       => 'URL to profile page',
		  'id'          => 'icons_url',
		  'type'        => 'text',
		  )

	),
	'section'     => 'header',
	'condition'	  => "subheader:is(on)",
	),

	
	
	array(
		'id'          => 'tab_transparent_header',
		'label'       => 'Transparent Header',
		'type'        => 'tab',
		'section'     => 'header'
	),	
	array(
		'label'       => 'Upload Transparency Logo',
		'id'          => 'logo_transparent_upload',
		'type'        => 'upload',
		'desc'        => 'Upload your Transparent Header logo version here. Activate your transparent header on a per page basis within the Page Settings when editing a page. Once a user scrolls down it will be substituted with the default logo version.',
		'section'     => 'header',
	),	
	array(
		'label'       => 'Upload Retina Transparency Logo (not required)',
		'id'          => 'logo_transparent_upload_retina',
		'type'        => 'upload',
		'desc'        => 'Upload the Retina version of your Transparent Header logo here. Make sure it is double the size of the regular Transparency Logo',
		'section'     => 'header',
	),	
	
	array(
		'label'       => 'Transparent Logo Transition Effect',
		'id'          => 'transparent_logo_effect',
		'type'        => 'select',
		'desc'        => 'Once a user scrolls down when using the Sticky Header the logo will be switched to the default logo using this effect',
		'choices'     => array(
		array(
		  'label'       => 'Flip',
		  'value'       => 'flip'
		  ),
		array(
		  'label'       => 'Fade',
		  'value'       => 'fade'
		  )	      
		),
		'std'         => 'flip',
		'section'     => 'header',
	),
	
	array(
		'label'       => 'Transparent Header Background Color',
		'id'          => 'transparent_header_color',
		'type'        => 'colorpicker',
		'desc'        => 'Change your Transparent Header Background Color',
		'section'     => 'header',
		'std'         => '#ffffff',
	  ),
	array(
		'label'       => 'Transparent Header Opacity',
		'id'          => 'transparent_header_opacity',
		'desc'        => 'Set your Transparent Header Opacity',
		'std'         => '0.1',
		'type'        => 'numeric-slider',
		'section'     => 'header',
		'min_max_step'=> '0,1,0.05'
	), 	  
	  
	array(
		'label'       => 'Transparent Header Main Navigation Text Color',
		'id'          => 'transparent_header_navigation_color',
		'type'        => 'colorpicker',
		'desc'        => 'Overwrite your Main Navigation color for your Transparent header',
		'section'     => 'header',
		'std'         => '#ffffff',
	  ),
	  
	array(
		'label'       => 'Transparent Header Main Navigation Hover Text Color',
		'id'          => 'transparent_header_navigation_hover_color',
		'type'        => 'colorpicker',
		'desc'        => 'Overwrite your Main Navigation Hover color for your Transparent header',
		'section'     => 'header',
		'std'         => '#ffffff',
	  ),
	  
	array(
		'label'       => 'Add Google Fonts to your Website',
		'id'          => 'google_fonts',
		'desc'        => 'Select the Google Fonts you want to use. Once you click "SAVE CHANGES" they will be added as options into the Font-Family dropdowns below. You don\' have to add "Montserrat", "Droid Serif" and "Lato" google fonts as they are part of the theme by default.',
		'type'        => 'google-fonts',
		'section'     => 'typography',
	  ), 	  
	
	array(
		'label'       => 'Main Navigation Font (Top Level Items)',
		'id'          => 'nav_font',
		'desc'        => 'Choose your Main Navigation Font (Default is Montserrat).<br> Have in mind some Google fonts do not support all font-weights, when adding it you will see what styles the font supports.',
		'std'         => array('font-family' => 'montserrat', 'font-size' => '14px','font-weight' => 'Normal', 'text-transform'=>'uppercase', 'letter-spacing' => ''),
		'type'        => 'typography',
		'section'     => 'typography',
	  ), 	
	  
	array(
		'label'       => 'Main Sub-Navigation Font (Submenu items/Drop Down Menu Items)',
		'id'          => 'sub_nav_font',
		'desc'        => 'Choose your Main Navigation Sub Menu Font (Default is Montserrat).<br> Have in mind some Google fonts do not support all font-weights, when adding it you will see what styles the font supports.',
		'std'         => array('font-family' => 'montserrat', 'font-size' => '13px','font-weight' => 'Normal', 'text-transform'=>'capitalize', 'letter-spacing' => ''),
		'type'        => 'typography',
		'section'     => 'typography',
	  ),  
	 
	array(
		'label'       => 'Heading Font',
		'id'          => 'heading_font',
		'desc'        => 'Choose Font Family for your headings (H1-H6). (Default is Montserrat). Have in mind some Google fonts do not support all font-weights, when adding it you will see what styles the font supports.',
		'std'         => array('font-family' => 'montserrat', 'font-weight' => 'normal', 'text-transform'=>'uppercase', 'letter-spacing' => '-0.02em'),
		'type'        => 'typography',
		'section'     => 'typography',
	  ),  
	  
	array(
		'label'       => 'Body Font',
		'id'          => 'body_font',
		'desc'        => 'Choose Font Family for body. (Default is Lato).',
		'std'         => array('font-family' => 'lato', 'font-color' => '#333','font-size' => '16px','font-weight' => '400', 'line-height' => '1.7em'),
		'type'        => 'typography',
		'section'     => 'typography',
	  ), 

	array(
		'label'       => 'Button Font',
		'id'          => 'button_font',
		'desc'        => 'Choose Font Family for your Buttons and Button Links. (Default is Montserrat). You can further style your buttons in the editor.',
		'std'         => array('font-family' => 'montserrat', 'font-size' => '13px','font-weight' => '400', 'text-transform'=>'uppercase'),
		'type'        => 'typography',
		'section'     => 'typography',
	),

	array(
	  'label'       => 'Wrapper Style',
	  'id'          => 'wrapper_style',
	  'type'        => 'select',
	  'desc'        => 'Select your Wrapper style. This value can be changed on a per Page basis under Page Settings.',
	  'choices'     => array(
		array(
		  'label'       => 'Boxed',
		  'value'       => 'boxed_wrapper'
		  ),
		array(
		  'label'       => 'Full Width',
		  'value'       => 'full_width_wrapper'
		  )
		),
	  'std'         => 'full_width_wrapper',
	  'section'     => 'general'
	),


	array(
		'label'       => 'Global Background',
		'id'          => 'boc_page_global_bgr',
		'desc'        => 'Select the global Background color/image. This value can be changed on a per Page basis under Page Settings. Type "cover" for background-size if you want the background image to stretch full width.',
		'type'        => 'background',
		'section'     => 'general',
	),
	
	array(
		'label'       => 'Enable Responsive Design',
		'id'          => 'responsive_design',
		'type'        => 'on_off',  
		'section'     => 'general',
		'std'		  => 'on',
		'desc'        => 'Turn if OFF only if you absolutely have to and have in mind that things like Sticky/Transparent Header & Unrolling Footer cannot be used. Make sure you also Turn Off Visual Composer\'s Responsiveness from Settings->Visual Composer.'
	),
	
	array(
		'label'       => 'Enable Retina Support',
		'id'          => 'retina',
		'type'        => 'on_off',  
		'section'     => 'general',
		'std'		  => 'off',
		'desc'        => 'This will swap images with larger image-size alternatives if available on Retina supported screens.',
	),

	array(
		'label'       => 'Smooth Scroll',
		'id'          => 'smooth_scrolling',
		'type'        => 'on_off', 
		'std'		 => 'on',
		'desc'        => 'Scrolling with the mouse creates a smooth movement effect.',
		'section'     => 'general'
	),

	array(
		'label'       => 'Preloader Animation - Spinner Icon',
		'id'          => 'has_page_preloader',
		'type'        => 'on_off', 
		'std'		 => 'off',
		'desc'        => 'A Spinning Circle Icon will appear until the current page is fully loaded.',
		'section'     => 'general'
	),  
  
	array(
		  'label'       => 'Page Heading Style',
		  'id'          => 'page_heading_style',
		  'type'        => 'select',
		  'desc'        => 'Change your Page Heading (the area below Header) Style from here.',
		  'choices'     => array(
			array(
			  'label'       => 'Gray',
			  'value'       => 'page_title_bgr'
			  ),
			array(
			  'label'       => 'Triangles BGR',
			  'value'       => 'page_title_bgr bgr_style1'
			  ),
			array(
			  'label'       => 'Custom',
			  'value'       => 'page_title_bgr custom_bgr'
			  ),
			),
		  'std'         => 'page_title_bgr',
		  'section'     => 'general'
		),


	array(
		'label'       => 'Page Heading Custom Background',
		'id'          => 'boc_page_heading_style_bgr',
		'desc'        => 'Select a custom background for your regular page heading.',
		'type'        => 'background',
		'section'     => 'general',
		'condition'	=> "page_heading_style:is(page_title_bgr custom_bgr)",	
	),		
	
	array(
		'label'       => 'Page Heading White Text Color',
		'id'          => 'boc_page_heading_white_text',
		'type'        => 'on_off', 
		'std'			=> 'off',
		'desc'        => 'Force White color for Page Heading text (use when using a dark BGR for your Page Heading)',
		'section'     => 'general'
	  ),  	
		
	array(
	  'label'       => 'Enable breadcrumbs',
	  'id'          => 'breadcrumbs',
	  'type'        => 'on_off', 
	  'std'         => 'on',
	  'section'     => 'general'
	  ),	
	array(
	  'label'       => 'Breadcrumb Position',
	  'id'          => 'breadcrumbs_position',
	  'type'        => 'select',
	  'desc'        => 'Change your Page Heading (the area below Header) Style from here.',
	  'choices'     => array(
			array(
			  'label'       => 'Above Title',
			  'value'       => 'normal'
			  ),    
			array(
			  'label'       => 'Floated to the right',
			  'value'       => 'floated'
			  )
	  ),
	  'std'         => 'floated',
	  'section'     => 'general'
	  ),


	array(
	  'label'       => 'Enable Comments for Pages',
	  'id'          => 'show_page_comments',
	  'type'        => 'on_off', 
	  'std'         => 'off',
	  'section'     => 'general'
	  ),



	array(
	  'label'       => 'Image Edges',
	  'id'          => 'rounded_images',
	  'type'        => 'select',
	  'desc'        => 'Add Border Radius to your Images globally',
	  'choices'     => array(
		array(
		  'label'       => 'Square',
		  'value'       => 0
		  ),
		array(
		  'label'       => 'Rounded',
		  'value'       => 1
		  )
		),
	  'std'         => 0,
	  'section'     => 'general'
	  ),



  

	array(
	  'label'       => 'Footer BGR Color',
	  'id'          => 'footer_style',
	  'type'        => 'select',
	  'choices'     => array(
		array(
		  'label'       => 'Light',
		  'value'       => 0
		  ),
		array(
		  'label'       => 'Dark',
		  'value'       => 1
		  )
		),
	  'std'         => 1,
	  'section'     => 'footer'
	),

	array(
	  'label'       => 'Footer Columns (Widget Areas)',
	  'id'          => 'footer_columns',
	  'type'        => 'select',
	  'choices'     => array(
		array(
		  'label'       => 'None',
		  'value'       => 0
		  ),
		array(
		  'label'       => '1',
		  'value'       => 1
		  ),
		array(
		  'label'       => '2',
		  'value'       => 2
		  ),
		array(
		  'label'       => '3',
		  'value'       => 3
		  ),
		array(
		  'label'       => '4',
		  'value'       => 4
		  )
		),
	  'std'         => 4,
	  'section'     => 'footer',
	  'desc'		=> 'How Many columns you want your footer split into'
	  ), 
	  
	array(
	  'label'       => 'Footer Fixed Position',
	  'id'          => 'footer_position',
	  'type'        => 'select',
	  'desc'        => 'Set to "Fixed" if you want the unrolling footer effect like in the Demo. Make sure your pages are long enough for this option, otherwise your footer may look awkward.',
	  'choices'     => array(
		array(
		  'label'       => 'Normal',
		  'value'       => 0
		  ),
		array(
		  'label'       => 'Fixed',
		  'value'       => 1
		  )
		),
	  'std'         => 0,
	  'rows'        => '',
	  'post_type'   => '',
	  'taxonomy'    => '',
	  'class'       => '',
	  'section'     => 'footer'
	  ),  

	array(
		'label'       => 'Copyrights Text in Footer',
		'id'          => 'copyrights',
		'type'        => 'text',
		'desc'        => 'Copyrights Text in Footer',
		'std'         => '<a href="http://themeforest.net/item/fortuna-responsive-multipurpose-wordpress-theme/12496833?ref=blueowlcreative" target="_blank">Fortuna Theme</a> &copy; 2017 &nbsp; | &nbsp; <a href="http://themeforest.net/user/blueowlcreative/portfolio?ref=blueowlcreative" target="_blank">BlueOwlCreative</a>',
		'section'     => 'footer'
	),

	array(
	'label'       => 'Footer Social Icons',
	'id'          => 'footer_icons',
	'type'        => 'list-item',
	'desc'        => 'Manage your Socials Icons in the theme footer. For Skype URL use "callto://YourSkypeName".',
	'settings'    => array(
		array(
			'id'          => 'icons_service_footer',
			'label'       => 'Choose service',
			'type'        => 'select',
			'choices'     => array(
					  //	Value maps to the Icon font
					  array('value'=> 'icon-facebook3','label' => 'Facebook','src'=> ''),
					  array('value'=> 'icon-googleplus2','label' => 'Google+','src'=> ''),
					  array('value'=> 'icon-pinterest2','label' => 'Pinterest','src'=> ''),
					  array('value'=> 'icon-skype2','label' => 'Skype','src'=> ''),
					  array('value'=> 'icon-twitter3','label' => 'Twitter','src'=> ''),
					  array('value'=> 'icon-linkedin3','label' => 'LinkedIn','src'=> ''),
					  array('value'=> 'icon-instagram2','label' => 'Instagram','src'=> ''),
					  array('value'=> 'icon-youtube','label' => 'Youtube','src'=> ''),
					  array('value'=> 'icon-vimeo','label' => 'Vimeo','src'=> ''),
					  array('value'=> 'icon-tumblr3','label' => 'Tumblr','src'=> ''),
					  array('value'=> 'icon-xing','label' => 'Xing','src'=> ''),
					  array('value'=> 'icon-vk','label' => 'VK','src'=> ''),
					  ),
			),
		
		array(
		  'label'       => 'URL to profile page',
		  'id'          => 'icons_url_footer',
		  'type'        => 'text',
		  )

	),
	'section'     => 'footer'
),
  
  
  
  
array(
  'label'       => 'Show Full Post Content',
  'desc'        => 'By default (when OFF) only the excerpt is shown - starting 60 words. If you set to ON you can still use <a href="http://en.support.wordpress.com/splitting-content/more-tag/" target="_blank">THE MORE tag</a> to manually set where your content would be cut off.',
  'id'          => 'blog_full_post_content',
  'type'        => 'on_off',
  'std'         => 'off',
  'section'     => 'blog'
  ),

array(
  'label'       => 'Full height Featured Images on Blog List Page',
  'desc'        => 'By default (when OFF) a cropped, smaller height version is used (1200x600).',
  'id'          => 'blog_full_img_height',
  'type'        => 'on_off',
  'std'         => 'off',
  'section'     => 'blog'
  ), 
   
array(
  'label'       => 'More Link Style',
  'id'          => 'blog_more_link_style',
  'type'        => 'select',
  'desc'        => 'Choose a Style for your "Read More" link',
  'std'         => '1',
  'section'     => 'blog',
  'choices'		=> array(
	array(
      'label'       => 'Style 1',
      'value'       => '1'
      ),
    array(
      'label'       => 'Style 2',
      'value'       => '2'
      ),
    array(
      'label'       => 'Style 3',
      'value'       => '3'
      ),
    ),
  ),  


array(
  'label'       => 'WooCommerce layout',
  'id'          => 'woocommerce_sidebar_layout',
  'type'        => 'radio-image',
  'desc'        => 'Choose Sidebar Layout for your WooCommerce Pages',
  'std'         => 'no-sidebar',
  'section'     => 'woocommerce',
  'choices'		=> array(
          array(
            'value'   => 'no-sidebar',
            'label'   => 'No Sidebar',
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
          ),
		  array(
            'value'   => 'right-sidebar',
            'label'   => 'Right Sidebar',
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
          ),
          array(
            'value'   => 'left-sidebar',
            'label'   => 'Left Sidebar',
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
          ),
	),
  ),   
array(
  'label'       => 'WooCommerce Single Product Page layout',
  'id'          => 'woocommerce_single_product_sidebar_layout',
  'type'        => 'radio-image',
  'desc'        => 'Choose Sidebar Layout for your WooCommerce Product Pages - That would be a page where a single product is displayed.',
  'std'         => 'no-sidebar',
  'section'     => 'woocommerce',
  'choices'		=> array(
          array(
            'value'   => 'no-sidebar',
            'label'   => 'No Sidebar',
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
          ),
		  array(
            'value'   => 'right-sidebar',
            'label'   => 'Right Sidebar',
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
          ),
          array(
            'value'   => 'left-sidebar',
            'label'   => 'Left Sidebar',
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
          ),
	),
  ), 
  
array(
  'label'       => 'Products per Page',
  'id'          => 'woocommerce_products_per_page',
  'type'        => 'select',
  'std'         => '12',
  'choices'     => array(
		array(
		  'label'       => '9',
		  'value'       => '9'
		  ),		
		array(
		  'label'       => '12',
		  'value'       => '12'
		  ),
		array(
		  'label'       => '16',
		  'value'       => '16'
		  ),
		array(
		  'label'       => '24',
		  'value'       => '24'
		  ),
		array(
		  'label'       => '48',
		  'value'       => '48'
		  )
		),
  'section' => 'woocommerce',
  'desc'	=> 'Products per Page on product listing pages like Shop, Category etc.',	  
  ),
 
	
array(
  'label'       => 'Enable WooCommerce Shopping Cart in Header',
  'id'          => 'woocommerce_cart_in_header',
  'type'        => 'on_off',
  'std'         => 'on',
  'section'     => 'woocommerce'
  ),   
		
array(
  'label'       => 'Background Color for WooCommerce Shopping Cart in Header',
  'id'          => 'woocommerce_cart_in_header_bgr_color',
  'type'        => 'select',
  'std'         => 'style_dark',
  'choices'     => array(
		array(
		  'label'       => 'Dark',
		  'value'       => 'style_dark'
		  ),
		array(
		  'label'       => 'White',
		  'value'       => 'style_white'
		  )
		),
  'section'     => 'woocommerce',
  'condition'	   => "woocommerce_cart_in_header:is(on)",	  
  ),
  

	array(
		'label' => 'Force Header Search Product Searching',
		'id' => 'woocommerce_header_product_search',
		'type' => 'on_off',
		'std' => 'off',
		'desc' => 'Switch this ON if you want the website search in the header to work for Woocommerce products and use the Woocommerce template for the search results',
		'section' => 'woocommerce'
	),			
  

array(
  'label'       => 'Static Content on top of your Shop page (great for adding sliders)',
  'id'          => 'woo_static_top_content',
  'type'        => 'textarea',
  'desc'        => 'This is some static content that will go on top of your Shop page. Perfect for adding sliders (Revolution slider for example) as normal page content for the Shop page entered via the WP editor is rendered above the products. So if you have a sidebar on your Shop page a full width slider would mess up your layout.',
  'section'     => 'woocommerce'
  ),
  
  
  

array(
  'label'       => 'Default <b>Page</b> Sidebar Layout',
  'id'          => 'sidebar_layout',
  'type'        => 'radio-image',
  'desc'        => 'Choose default Sidebar Layout for your Pages. This value can be overwritten and set on a per-page basis while editing a page.',
  'std'         => 'full-width',
  'section'     => 'sidebars',
  'choices'		=> array(
          array(
            'value'   => 'full-width',
            'label'   => 'Full Width',
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
          ),  
          array(
            'value'   => 'right-sidebar',
            'label'   => 'Right Sidebar',
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
          ),
          array(
            'value'   => 'left-sidebar',
            'label'   => 'Left Sidebar',
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
          ),
	),
  ), 
    
array(
  'label'       => 'Default <b>Post</b> Sidebar Layout',
  'id'          => 'sidebar_layout_posts',
  'type'        => 'radio-image',
  'desc'        => 'Choose default Sidebar Layout for your Posts. This value can be overwritten and set on a per-page basis while editing a page. This Layout will also be used for your Blog, Category & Taxonomy post list pages.',
  'std'         => 'right-sidebar',
  'section'     => 'sidebars',
  'choices'		=> array(
          array(
            'value'   => 'full-width',
            'label'   => 'Full Width',
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
          ),  
          array(
            'value'   => 'right-sidebar',
            'label'   => 'Right Sidebar',
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
          ),
          array(
            'value'   => 'left-sidebar',
            'label'   => 'Left Sidebar',
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
          ),
	),
  ), 
    
  
  
  
array(
  'id'          => 'sidebars_text',
  'label'       => 'About sidebars',
  'desc'        => 'All Dynamic sidebars that you create here will appear both in the Appearance > Widgets. You can then assign them to specific pages or posts.',
  'type'        => 'textblock',
  'section'     => 'sidebars',
  ),
  
array(
  'label'       => 'Create Sidebars',
  'id'          => 'boc_sidebars',
  'type'        => 'list-item',
  'desc'        => 'Choose a unique title for each sidebar',
  'section'     => 'sidebars',
  'settings'    => array(
    array(
      'label'       => 'ID',
      'id'          => 'id',
      'type'        => 'text',
      'desc'        => 'Write a lowercase single word as ID (no spaces & it shouldn\'t start with a digit)',
      )
    )
  ),
  
  
  array(
  'label'       => 'Image Cropping',
  'id'          => 'portfolio_img_size',
  'type'        => 'select',
  'desc'        => 'Original - preserves default image proportions, Cropped - Uses the theme preset (600x380) cropped image version',
  'choices'     => array(
	array(
      'label'       => 'Original Size (No Crop)',
      'value'       => 'full'
      ),
    array(
      'label'       => 'Cropped (Same Height Effect)',
      'value'       => 'boc_medium'
      ),
    ),
  'section'     => 'portfolio',
  'std'         => 'boc_medium',
  ),    

  array(
  'label'       => 'Portfolio Preset Style',
  'id'          => 'portfolio_style',
  'type'        => 'select',
  'desc'        => 'Choose one of the available presets for displaying your portfolio items for the Portfolio Filter Page and the Related Portfolio Items',
  'choices'     => array(
    array(
      'label'       => 'Style 1',
      'value'       => 'type1'
      ),
    array(
      'label'       => 'Style 2',
      'value'       => 'type2'
      ),
    array(
      'label'       => 'Style 3',
      'value'       => 'type3'
      ),
    array(
      'label'       => 'Style 4',
      'value'       => 'type4'
      ),
    array(
      'label'       => 'Style 5',
      'value'       => 'type5'
      ),
    array(
      'label'       => 'Style 6',
      'value'       => 'type6'
      ),
    array(
      'label'       => 'Style 7',
      'value'       => 'type7'
      ),
    array(
      'label'       => 'Style 8',
      'value'       => 'type8'
      ),
    array(
      'label'       => 'Style 9',
      'value'       => 'type9'
      )
    ),
  'std'         => 'type1',
  'section'     => 'portfolio'
  ), 
  
  
  array(
  'label'       => 'Portfolio Hover Effect',
  'id'          => 'portfolio_img_hover_effect',
  'type'        => 'select',
  'desc'        => 'Choose one of the available portfolio Item img Hover effects for your Portfolio Filter Page',
  'choices'     => array(
	array(
      'label'       => 'None',
      'value'       => 'none'
      ),
    array(
      'label'       => 'Zoom Out',
      'value'       => '1'
      ),
    array(
      'label'       => 'Zoom In',
      'value'       => '2'
      ),
    array(
      'label'       => 'Side',
      'value'       => '3'
      ),
    array(
      'label'       => 'Spin',
      'value'       => '4'
      )
    ),
  'std'         => '2',
  'section'     => 'portfolio'
  ),    
  
  array(
  'label'       => 'Portfolio Item Order Clause',
  'id'          => 'portfolio_order',
  'type'        => 'select',
  'desc'        => 'Choose the Order clause type for your Portfolio Filter Page (Descending or Ascending)',
  'choices'     => array(
	array(
      'label'       => 'DESC',
      'value'       => 'DESC'
      ),
    array(
      'label'       => 'ASC',
      'value'       => 'ASC'
      ),
    ),
  'section'     => 'portfolio'
  ),  
  
  array(
  'label'       => 'Portfolio Item OrderBy Clause',
  'id'          => 'portfolio_orderby',
  'type'        => 'select',
  'desc'        => 'Choose the OrderBy clause type for your Portfolio Filter Page',
  'choices'     => array(
	array(
      'label'       => 'Date',
      'value'       => 'date'
      ),
    array(
      'label'       => 'Title',
      'value'       => 'title'
      ),
	array(
      'label'       => 'Name',
      'value'       => 'name'
      ),
    array(
      'label'       => 'ID',
      'value'       => 'ID'
      ),
    array(
      'label'       => 'Random',
      'value'       => 'rand'
      ),
    ),
  'section'     => 'portfolio'
  ),


  
  array(
  'label'       => 'Portfolio Item Spacing',
  'id'          => 'portfolio_items_spacing',
  'type'        => 'select',
  'desc'        => 'Choose whether you want spacing or not for your Portfolio items on your Portfolio Filter Page',
  'choices'     => array(
	array(
      'label'       => 'Big Spacing',
      'value'       => 'big_spacing'
      ),
    array(
      'label'       => 'Small Spacing',
      'value'       => 'small_spacing'
      ),
    array(
      'label'       => 'No Spacing',
      'value'       => 'no_spacing'
      ),
    ),
  'std'		=> 'small_spacing',
  'section'     => 'portfolio'
  ),  

	array(
		'label'       => 'Enable Related Projects in Portfolio',
		'id'          => 'related_portfolio_projects',
		'type'        => 'on_off',
		'std'         => 'on',
		'section'     => 'portfolio'
	),

	array(
		'label'       => 'Portfolio Items per Page',
		'id'          => 'portfolio_items_per_page',
		'type'        => 'text',
		'desc'        => 'Enter how many items per Page you want in your Portfolio Page (Integer). Increase this number if you don\'t want pagination on your Portfolio Filter page. This might be useful as filtering the items only applies to the current set (page) of items shown.',
		'std'         => '24',
		'section'     => 'portfolio'
	),
	array(
		'id'          => 'custom_css',
		'label'       => 'Custom CSS',
		'desc'        => 'Write any custom CSS code you want here or use Child Themes to prevent data-loss upon theme updates.',
		'type'        => 'css',
		'section'     => 'custom_styles',
	),
	  
	  
	array(
		'label'       => 'Google Maps Address',
		'id'          => 'gmaps_address',
		'type'        => 'text',
		'desc'        => 'If you want a map to appear on your contact page, when using the Contact Page template, please enter the Google Address you want to appear on it here. You can then create your Form using Contact Form 7 plugin and add its shortcode to the page.',
		'section'     => 'contact'
	),
	
	array(
	  'label'       => 'Google Maps API Key',
	  'id'          => 'gmaps_API_key',
	  'type'        => 'text',
	  'desc'        => 'Since mid 2016 Google requires that you get a <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google API key</a> if you want to use Google maps. Enter your key here, if you don\' have one <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">get it here</a>.',
	  'std'         => '',
	  'rows'        => '',
	  'post_type'   => '',
	  'taxonomy'    => '',
	  'class'       => '',
	  'section'     => 'contact'
	  ),
	
	array(
	  'label'       => 'Upload Custom Marker',
	  'id'          => 'custom_marker_upload',
	  'type'        => 'upload',
	  'desc'        => 'Upload a custom marker image for your Contact Page Google template map',
	  'section'     => 'contact',
	),	
	
	array(
		'label'       => 'Google Map Color Scheme',
		'id'          => 'gmaps_style',
		'type'        => 'select',
		'desc'        => 'Choose from the available color scheme presets',
		'choices'     => array(
			array(
			  'label'       => 'Default',
			  'value'       => '1'
			  ),
			array(
			  'label'       => 'Vivid',
			  'value'       => '2'
			  ),
			array(
			  'label'       => 'Green',
			  'value'       => '3'
			  ),
			array(
			  'label'       => 'Blue',
			  'value'       => '4'
			  ),		
			array(
			  'label'       => 'Light Grey',
			  'value'       => '5'
			  ),
			array(
			  'label'       => 'Dark Grey',
			  'value'       => '6'
			  ),
		),
		'std'		=> '1',
		'section'     => 'contact',
		'condition'	=> "gmaps_address:not('')",
	),
	array(
		'label'       => 'Google Maps Zoom Option',
		'id'          => 'gmaps_zoom',
		'type'        => 'text',
		'desc'        => 'You can change the Google Maps Default Zoom Option from here.',
		'std'         => '14',
		'section'     => 'contact',
		'condition'	=> "gmaps_address:not()",	
	  ),	  
  
),



);

	/* settings are not the same update the DB */
	if ( $saved_settings !== $custom_settings ) {
	  update_option( 'option_tree_settings', $custom_settings ); 
	}

}


