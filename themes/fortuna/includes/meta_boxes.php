<?php 
/**
 * Initialize the meta boxes. 
 */
add_action( 'admin_init', 'boc_meta_boxes' );

function boc_meta_boxes() {

  /**
   * Create a custom meta boxes array that we pass to 
   * the OptionTree Meta Box API Class.
   */

	$sidebars = ot_get_option('boc_sidebars');
	$sidebars_array = array();
	$sidebars_array[0] = array (
		'label' => "Fortuna Default Sidebar",
		'value' => 'Fortuna Default Sidebar'
	);

	$sidebars_k = 1;
	if(!empty($sidebars)){
		foreach($sidebars as $sidebar){
			$sidebars_array[$sidebars_k++] = array(
				'label' => $sidebar['title'],
				'value' => $sidebar['id']
			);
		}
	}
   
  $boc_sidebar_meta = array(
    'id'        => 'boc_sidebar_settings',
    'title'     => 'Fortuna Sidebar Settings',
	'desc'      => 'Overwrite the Default Sidebar Options set in your <a href="'.esc_url(admin_url("?page=ot-theme-options#section_general")).'">Fortuna Options</a>',
    'pages'     => array( 'post','page'),
    'context'   => 'side',
    'priority'  => 'default',
    'fields'    => array(
      array(
        'id'          => 'boc_meta_sidebar_layout',
        'label'       => 'Sidebar Layout',
        'std'         => 'default',
        'type'        => 'select',
        'choices'     => array (
							array(
								'label' => "Default (Set in Theme Options)",
								'value' => 'default'
							),	
							array(
								'label' => "No Sidebar",
								'value' => 'full-width'
							),		
							array(
								'label' => "Left Sidebar",
								'value' => 'left-sidebar'
							),	
							array(
								'label' => "Right Sidebar",
								'value' => 'right-sidebar'
							)
						)
	  ),
	  array(
        'id'          => 'boc_sidebar_set',
        'label'       => 'Sidebar',
        'type'        => 'select',
        'choices'     => $sidebars_array,
		'condition'	  => "boc_meta_sidebar_layout:not(full-width)",
        ),     
	  )
    );
	ot_register_meta_box( $boc_sidebar_meta );
	


	
	// Gallery post type
	$boc_post_gallery_options = 
	array(
	'id'        => 'boc_post_gallery_options',
	'title'     => 'Gallery',
	'desc'      => '',
	'pages'     => array( 'post','portfolio' ),
	'context'   => 'normal',
	'priority'  => 'high',
	'fields'    => array(

	  array(
		'id'          => 'gallery',
		'label'       => 'Pick your Gallery Images',
		'type'        => 'gallery',
		)
	  )  
	);
	ot_register_meta_box( $boc_post_gallery_options );	
	
	// Video post type
	$boc_post_video_options = 
	array(
	'id'        => 'boc_post_video_options',
	'title'     => 'Video Post Options',
	'desc'      => '',
	'pages'     => array( 'post','portfolio' ),
	'context'   => 'normal',
	'priority'  => 'high',
	'fields'    => array(

	  array(
		'id'          => 'video_embed_code',
		'label'       => 'Paste your Video Embed Code',
		'type'        => 'text',
		)
	  )  
	);
	ot_register_meta_box( $boc_post_video_options );

	
  
  $boc_page_settings = array(
    'id'        => 'boc_page_settings',
    'title'     => 'Fortuna Page Settings',
    'pages'     => array( 'post','page', 'product', 'portfolio'),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
		array(
			'id'          => 'boc_page_wrapper_style',
			'label'       => 'Page Wrapper Style',
			'desc'        => 'Overwrite the Default Wrapper Style (set in Theme Options) for this page.',
			'std'         => 'default',
			'type'        => 'select',
			'class'       => '',
			'choices'     => array (
								array(
									'label' => "Default (Set in Theme Options)",
									'value' => 'default'
								),
								array(
									'label' => "Boxed",
									'value' => 'boxed_wrapper'
								),
								array(
									'label' => "Full Width",
									'value' => 'full_width_wrapper'
								),
							)
		),
		array(
			'id'          => 'boc_page_heading_set',
			'label'       => 'Show Page Heading',
			'desc'        => 'This is the area that contains the Page Title and Breadcrumbs, right below the header',
			'std'         => 'on',
			'type'        => 'on_off',
		),
		array(
			'id'          => 'boc_page_heading_bgr_meta',
			'label'       => 'Overwrite default Page Heading background',
			'type'        => 'background',
			'desc'		=> 'Set this to overwrite the default setting from Theme Options for this specific page. Type "cover" for background-size if you want the background image to stretch full width.',
			'condition'	  => "boc_page_heading_set:is(on)",
		),
		array(
			'id'          => 'boc_page_heading_white_text_meta',
			'label'       => 'White Text in Page Heading',
			'desc'        => 'Force white color for breadcrumb links and Page Title in Page Heading area (if using a dark BGR)',
			'std'         => 'off',
			'type'        => 'on_off',
			'condition'	  => "boc_page_heading_set:is(on)",
		),		
		array(
			'id'          => 'boc_page_breadcrumbs',
			'label'       => 'Show Breadcrumbs',
			'desc'        => 'This is the area within your Page heading that looks like: Home->Page Name',
			'std'         => 'on',
			'type'        => 'on_off',
			'condition'	  => "boc_page_heading_set:is(on)",
		),
		array(
			'id'          => 'boc_content_top_margin',
			'label'       => 'Page Content Top Margin',
			'desc'        => 'Page content will be slightly pushed down from your header/page heading area if On',
			'std'         => 'on',
			'type'        => 'on_off',
		),
		array(
			'id'          => 'boc_transparent_header_set',
			'label'       => 'Activate Transparent Header',
			'std'         => 'off',
			'type'        => 'on_off',
			'desc'        => 'Further Manage your Transparent Menu Settings in your <a href="'.admin_url("?page=ot-theme-options#tab_transparent_header").'">Fortuna Options</a>',	
		),		
		array(
			'id'          => 'boc_page_bgr',
			'label'       => 'Set explicit Page background',
			'type'        => 'background',
			'desc'		=> 'Type "cover" for background-size if you want the background image to stretch full width.'
		),

		array(
			'id'          => 'has_page_preloader_set',
			'label'       => 'Activate Preloader Animation for this Page',
			'std'         => 'off',
			'type'        => 'on_off',
			'desc'        => 'If "Preloader Animation" is globally set to OFF in your Theme Options (General) you can overwrite it here for this page only',	
		),	

		array(
			'id'          => 'boc_header_is_off',
			'label'       => 'Disable Header for this Page',
			'std'         => 'off',
			'type'        => 'on_off'
		),		
		array(
			'id'          => 'boc_footer_is_off',
			'label'       => 'Disable Footer for this Page',
			'std'         => 'off',
			'type'        => 'on_off'
		),		
	)
   );

	ot_register_meta_box( $boc_page_settings );	
	
}


/**
 * Handle Gallery Metabox on Post editing page 
 */

add_action( 'admin_print_scripts', 'boc_display_metaboxes', 1000 );

function boc_display_metaboxes() {
	global $pagenow;
	if( 'post.php' != $pagenow && 'post-new.php' != $pagenow ) return; // Not editing a page, bye!
	if ((get_post_type() == "post") || (get_post_type() == "portfolio")) { ?>
		<script type="text/javascript">// <![CDATA[
		jQuery( function($) {
			$ = jQuery;
			function displayBOCMetaboxes() {
				$('#boc_post_gallery_options, #boc_post_video_options').hide();
				
				var selectedPostFormat = $("input[name='post_format']:checked").val();			
				
				if ( selectedPostFormat ) {
					if ( selectedPostFormat == 'gallery' ) {
						$('#boc_post_gallery_options').fadeIn();
					}
					if ( selectedPostFormat == 'video' ) {
						$("#boc_post_video_options").fadeIn();
					}
				}		
			}
			$(function() {
				displayBOCMetaboxes();
				$("input[name='post_format']").change(function() {
					displayBOCMetaboxes();
				});
			});
		 });
		// ]]></script>
		<?php
	} // End if post	
}
