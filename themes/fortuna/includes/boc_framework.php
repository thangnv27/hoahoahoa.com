<?php
/* BlueOwlCreative Framework
 *
 * The following file defines the some Core Theme functionality methods
 *
 * @author Kal (BlueOwlCreative)
 * @link http://blueowlcreative.com
 */
 

// Aqua Live Image Resizer
load_template( trailingslashit( get_template_directory() ) . 'includes/ext/aq_resizer.php');


/**	
	BreadCrumbs method 
**/
if( ! function_exists( 'boc_breadcrumbs' ) ) { 
	function boc_breadcrumbs() {
			global $post;

			$breadcrumbs_position = wp_kses_post(ot_get_option('breadcrumbs_position','floated'));
			
			// If portfolio template (with cat filter) and right breadcrumbs -> no breadcrumbs
			if( ($breadcrumbs_position!="normal") && is_page_template( 'portfolio-three-column.php' ) ) {
				return;
			}
			
			echo '<div class="breadcrumb '.(!is_rtl() && ($breadcrumbs_position!="normal") ? "breadcrumb_right" : "") .'">';
			
			if ( !is_front_page() ) {
				echo '<a class="first_bc" href="';
				echo esc_url(home_url('/'));
				echo '"><span>'.__('Home','Fortuna');
				echo "</span></a>";
			}
			
			if (is_category() && !is_singular('portfolio')) {
				$current_cat = get_category(get_query_var('cat'),false);
				$parents_links = get_category_parents($current_cat->cat_ID, TRUE, '', FALSE );

				//Attach <span> to links      
				$parents_links = preg_replace("/(<a\s*href[^>]+>)/", "$1".'<span>', $parents_links);
				$parents_links = boc_str_lreplace("<a href","<a class='last_bc' href", $parents_links);
				$parents_links = str_replace("</a>", "</span></a>", $parents_links);
				
				echo $parents_links;
			}        
			
			// Woocommerce Breadcrumbs
			if(function_exists('is_woocommerce') && is_woocommerce()) {
				// For categories and home shop etc
				if(function_exists('is_shop') && is_shop()) {
					echo "<a class='last_bc' href='". esc_url(get_permalink( woocommerce_get_page_id( 'shop' ) ))."'><span>".__('Shop', 'Fortuna')."</span></a>";
				}elseif(is_product()){
					echo "<a href='". esc_url(get_permalink( woocommerce_get_page_id( 'shop' ) ))."'><span>".__('Shop', 'Fortuna')."</span></a>";

					$taxonomy = 'product_cat';
					$terms = get_the_terms( $post->ID , $taxonomy );

					if($terms){
						// Sort so ones with parent=0 list first
						usort($terms, create_function( '$a, $b', 'return strcmp($a->parent, $b->parent);'));
						
						if (! empty( $terms ) ) :
							foreach ( $terms as $term ) {

								$link = get_term_link( $term, $taxonomy );
								if ( !is_wp_error( $link ) )
									echo '<a href="' . esc_url($link) . '"><span>' . esc_html($term->name) . '</span></a>';
							}
						endif;
					}
					
				}elseif(is_product_category()){
					echo "<a href='".  esc_url(get_permalink( woocommerce_get_page_id( 'shop' ) ))."'><span>".__('Shop', 'Fortuna')."</span></a>";
					global $wp_query;
					// get the query object
					$cat_obj = $wp_query->get_queried_object();
					  
					if(isset($cat_obj) && $cat_obj->parent > 0){
						$parent_cat = get_term_by( 'id', $cat_obj->parent, 'product_cat');
						
						$link = get_term_link( $parent_cat, 'product_cat');
						echo '<a href="' . esc_url($link) . '"><span>' .  esc_html($parent_cat->name) . '</span></a>';
					}
				}else{
					echo "<a href='". esc_url(get_permalink( woocommerce_get_page_id( 'shop' ) ))."'><span>".__('Shop', 'Fortuna')."</span></a>";
				}	
			}	
			
			// Taxonomy
			if (is_tax()) {
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				echo '<a class="last_bc" href="' . esc_url(get_term_link($term)) . '" title="' . esc_attr($term->name) . '"><span>' . esc_html($term->name) . '</span></a>';
			}


			// Portfolio Breadcrumbs
			if(is_singular('portfolio')) {
		
				$taxonomy = 'portfolio_category';
				$terms = get_the_terms( $post->ID , $taxonomy );

				if (! empty( $terms ) ) :
					foreach ( $terms as $term ) {
						
						$link = get_term_link( $term, $taxonomy );
						if ( !is_wp_error( $link ) )
							echo '<a href="' . esc_url($link) . '"><span>' . esc_html($term->name) . '</span></a>';
					}
				endif;
			}

			if(is_home()) {
				echo '<a class="last_bc" href="#" title="' . esc_attr(single_post_title('', false )) . '"><span>' . esc_html(single_post_title('',false)) . '</span></a>';
			}
			
			if(is_page() && !is_front_page()) {
				$parents = array();
				$parent_id = $post->post_parent;
				while ( $parent_id ) :
					$page = get_page( $parent_id );
					$parents[]  = '<a href="' . esc_url(get_permalink( $page->ID )) . '" title="' . esc_attr(get_the_title( $page->ID )) . '"><span>' . esc_html(get_the_title( $page->ID )) . '</span></a>';
					$parent_id  = $page->post_parent;
				endwhile;
				$parents = array_reverse( $parents );
				echo join( ' ', $parents );
				echo '<a class="last_bc" href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '"><span>' . esc_html(get_the_title()). '</span></a>';
			}
			
			if(is_single()) {
				$args=array('orderby' => 'none');
				$terms = wp_get_post_terms( $post->ID , 'category', $args);
				foreach($terms as $term) {
				  echo '<a href="' . esc_url(get_term_link($term, 'category')) . '" title="' . esc_attr(get_the_title()) . '" ' . '><span>' . esc_html($term->name) .'</span></a> ';
				}

				echo '<a class="last_bc" href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '"><span>' . esc_html(get_the_title()). '</span></a>';
			}
			
			if(is_tag()){ echo '<a class="last_bc" href="#"><span>'.__("Tag", 'Fortuna').": ".esc_html(single_tag_title('', false)).'</span></a>'; }
			if(is_404()){ echo '<a class="last_bc" href="#"><span>'.__("404 - Page not Found", 'Fortuna').'</span></a>'; }
			if(is_search()){ echo '<a class="last_bc" href="#"><span>'.__("Search", 'Fortuna').'</span></a>'; }
			if(is_year()){ echo '<a class="last_bc" href="#"><span>'.esc_html(get_the_time('Y')).'</span></a>'; }
			if(is_month()){ echo '<a class="last_bc" href="#"><span>'.esc_html(get_the_time('F Y')).'</span></a>'; }
			if(is_day()){ echo '<a class="last_bc" href="#"><span>'.esc_html(get_the_time('F jS, Y')).'</span></a>'; }
			if(is_author()) { 	echo '<a class="last_bc" href="#"><span>'.esc_html(get_the_author()).'</span></a>'; }

			echo "</div>";
	}
}

/**
	Replace last occurrence
**/
if( ! function_exists( 'boc_str_lreplace' ) ) {
	function boc_str_lreplace($search, $replace, $subject)
	{
		return preg_replace('~(.*)' . preg_quote($search, '~') . '(.*?)~', '$1' . $replace . '$2', $subject, 1);
	}
}


/**	
	Add SEARCH to the header "main_navigation" slot if option is set
**/
add_filter( 'wp_nav_menu_items', 'boc_add_search_to_header', 10,2);

if( ! function_exists( 'boc_add_search_to_header' ) ) { 
	function boc_add_search_to_header( $items, $args ) {
	
		if( $args->theme_location == 'main_navigation' ){

			$show_search_option = boc_show_search_in_header();
			$show_search_separator_option = boc_show_search_separator();
			if($show_search_option!='on') {
				return $items;
			}else {
				return $items. ($show_search_separator_option!='off' ? '<li class="boc_search_border"><a href="#">|</a></li>' : '').
				'<li class="boc_search_toggle_li"><a href="#" class="header_search_icon icon icon-search3"></a></li>';
			}
		}else {
			return $items;
		}
	}
}

/**	
	Generate custom Search Form Markup
**/
if ( ! function_exists( 'boc_search_form_in_header' ) ) {
	function boc_search_form_in_header() {
	
		$product_search_hidden_field = '';
		if(ot_get_option('woocommerce_header_product_search','off')=='on'){
			$product_search_hidden_field = "<input type='hidden' name='post_type' id='post_type' value='product'/>";
		}

		$search_form_html = '
			<div id="boc_searchform_in_header" class="">
				<div class="container">
					<form method="get" action="'.esc_url( home_url( '/' ) ).'" role="search" class="header_search_form"><input type="search" name="s" autocomplete="off" placeholder="'.__( 'Type then hit enter to search...', 'Fortuna' ).'" />'.$product_search_hidden_field.'</form>
					<span id="boc_searchform_close" class="icon icon-close"></span>
				</div>
			</div>';
		return $search_form_html;
	}
}




/**	
	Page Header method
**/
if( ! function_exists( 'boc_page_header' ) ) { 
	function boc_page_header() {

		// Get post id if on a post or page
		if(is_archive() || is_search() || is_tax()){
			$post_id = 0;
		}elseif(is_home()) {
			$post_id = get_option( 'page_for_posts' );
		}else {
			global $post;
			$post_id = $post->ID;
		}
		
		global $woocommerce;
		
		// If shop get shop page id
		if(function_exists('is_shop') && is_shop()){
			$post_id = woocommerce_get_page_id('shop');
		}
		
		// Shall we display the page heading according to Post meta
		$boc_show_heading = get_post_meta($post_id, 'boc_page_heading_set', true);
		$boc_content_top_margin = (get_post_meta($post_id, 'boc_content_top_margin', true)!=='off'? true : false);


		if($boc_show_heading!=='off') {
		
			if(is_archive() || is_search() || is_home() || is_404() || is_page() || is_tax() || is_single()) { 
			
				$boc_page_breadcrumbs = (get_post_meta($post_id, 'boc_page_breadcrumbs', true)!=='off'? true : false);
				$extra_style = "";
				if($boc_page_breadcrumbs){
					$breadcrumbs_position =  ot_get_option('breadcrumbs_position','floated');
					if($breadcrumbs_position!="normal"){
						$extra_style = " style='padding: 20px 0;'";
					}
				}
			?>
				<div class="full_container_page_title <?php echo ($boc_content_top_margin ? '' : 'no_bm');?>" <?php echo $extra_style;?>>	
					<div class="container">		
						<div class="section no_bm">
								<?php 
								if($boc_page_breadcrumbs) {
									boc_breadcrumbs(); 
								}
								?>
								
								<div class="page_heading"><h1>
								<?php 	if (is_home() && is_front_page()) {
											esc_html(bloginfo('name'));
											
										}elseif(is_home()){
											esc_html(single_post_title(''));
											
										}elseif(is_404()){
											_e('404 - Page Not Found', 'Fortuna');
											
										}elseif(is_archive() && !(function_exists('is_woocommerce') && is_woocommerce())){
											if(is_year()){ echo esc_html(get_the_time('Y')); }
											elseif(is_month()){ echo esc_html(get_the_time('F Y')); }
											elseif(is_day()){ echo esc_html(get_the_time('F jS, Y')); }
											elseif(is_author()) { 	echo esc_html(get_the_author()); }
											elseif(is_tag()) { 	echo __("Tag", 'Fortuna').": ". esc_html(single_tag_title('',false)); }
											else{ 
												echo esc_html(single_cat_title());
											}
										
										}elseif(is_search()){
											echo __('Search results for:', 'Fortuna').' '. esc_html(get_search_query());									
									
										}elseif(is_page()){
											esc_html(the_title(''));
															
										}elseif(is_tax()){
											echo esc_html(single_cat_title());
											
										}elseif((function_exists('is_woocommerce') && is_woocommerce()) && (is_shop() || is_product_category() || is_product_tag())) {
											echo (is_archive() ? _e('Shop', 'Fortuna') : 
														(is_search() ? _e('Search results for:', 'Fortuna').' '. esc_html(get_search_query()): 
																(is_home() ? esc_html(the_title('')) : esc_html(the_title())) ));
										}else {
											esc_html(the_title(''));
										}
								?>
								</h1></div>	
						</div>
					</div>
				</div>
			<?php			
			}else {
				if($boc_content_top_margin){
						echo '<div class="h20"></div>';
				}
			} 
	
		}else {
			if(get_page_template_slug( $post_id ) == "portfolio-three-column.php") {
				echo '<div class="h100"></div>';				
			}
			if($boc_content_top_margin){
				// For any page other than the contact Page template
				if(get_page_template_slug( $post_id ) != "contact.php") {
					echo '<div class="h20"></div>';
				}
			}	
		}
	}
}





/* Walker Class for Adding <span> tag to Menu items with children.
 * @author BOC 
 * @link http://blueowlcreative.com
 */

class boc_Menu_Walker extends Walker_Nav_Menu
{
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
    {
        $id_field = $this->db_fields['id'];
		// Add a SPAN element to current one's title if it has children
		if( !empty( $children_elements[$element->$id_field] ) ) {
        	$element->title .= '<span></span>';
        }
        return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

}


// Remove span from title
if( ! function_exists( 'boc_removeSpanFromTitle' ) ) { 
	function boc_removeSpanFromTitle($title){
			
		$title = str_replace('<span>','',$title);
		$title = str_replace('</span>','',$title);
			
		return $title;	
	}
}



// Comments
if( ! function_exists( 'boc_comment' ) ) { 
	function boc_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<?php $add_below = ''; ?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
		
			<div class="single_comment">
				<div class="comment_avatar">
					<div class="avatar">
						<?php echo get_avatar($comment, 50); ?>
					</div>
					<?php edit_comment_link(__('Edit','Fortuna'),'  ','') ?>
				</div>
				<div class="comment_content">
				
					<div class="comment-author meta">
						<div class="comment_name">
							<?php
								$author_link = wp_kses_post(get_comment_author_link());
								$reply_link = get_comment_reply_link(array_merge($args, array('reply_text' => 'Reply', 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])));
							?>
							<?php echo ((is_rtl()) ? $reply_link : $author_link); ?><span>-</span><?php echo ((is_rtl()) ? $author_link : $reply_link); ?>
						</div>
						<div class="comment_desc"><?php printf(__('%1$s at %2$s', 'Fortuna'), esc_html(get_comment_date()),  esc_html(get_comment_time())) ?></div>
						
					</div>
				
					<div class="comment_text">
						<?php if ($comment->comment_approved == '0') : ?>
						<em><?php _e('Your comment is awaiting moderation.', 'Fortuna') ?></em>
						<br />
						<?php endif; ?>
						<?php esc_html(comment_text()) ?>
					</div>
				
				</div>
				
			</div>

	<?php } 
}

      
if( ! function_exists( 'boc_limitString' ) ) { 
	function boc_limitString($str, $maxLen, $minLen = 0){
  
        if (strlen($str) <= $maxLen){//no need of trimming
            return $str;
        }
        
        $suffix = "";
        $suffixLen = strlen($suffix);

        // there's at least one space in the first $len chars
        if (strrpos(substr($str, 0, $maxLen), " ") !== false){
            $retString = substr($str, 0, strrpos(substr($str, 0, $maxLen)," ")) . $suffix;

            // If retstring's length is greater than $minLen or $minLen is to be ignored
            if (strlen($retString) > $minLen || $minLen == 0){
                return $retString;
                
            } else {//if the space is faaaar from the maxLen character
                return substr($str, 0, $maxLen - $suffixLen) . $suffix;
            }
        } else {
            return substr($str, 0, $maxLen - $suffixLen) . $suffix;
        }
	}
}

/*
function vd($o) {
	echo "<pre>";
    var_dump($o);
	echo "</pre>";
}        
*/  

/**
	BOC Pagination
**/
if( ! function_exists( 'boc_pagination' ) ) { 
	function boc_pagination($pages = '', $range = 2)
	{  
		 $showitems = ($range * 2)+1;  

		 global $paged;

		 if(empty($paged)) $paged = 1;

		 if($pages == '')
		 {
			 global $wp_query;
			 $pages = $wp_query->max_num_pages;
			 if(!$pages)
			 {
				 $pages = 1;
			 }
		 }   

		 if(1 != $pages)
		 {
			 echo "<div class='pagination section'>";
			 echo '<div class="links">';
			 if($paged > 1){
				echo "<a class='pagination-prev' href='".esc_url(get_pagenum_link($paged - 1))."'><span class='page-prev'></span>".__('Previous', 'Fortuna')."</a>";
			 }
			 for ($i=1; $i <= $pages; $i++)
			 {
				 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				 {
					 echo ($paged == $i)? " <b>".esc_html($i)."</b>":" <a href='".esc_url(get_pagenum_link($i))."'>".esc_html($i)."</a>";
				 }
			 }

			 if ($paged < $pages) echo " <a class='pagination-next' href='".esc_url(get_pagenum_link($paged + 1))."'>".__('Next', 'Fortuna')."<span class='page-next'></span></a>";  
			 echo "</div></div>\n";
		 }
	}
}



/**
	BOC Related Portfolio Items
**/
if( ! function_exists( 'boc_get_related_portfolio_items' ) ) { 
	function boc_get_related_portfolio_items($post_id) {
		
		$item_cats = get_the_terms($post_id, 'portfolio_category');
		if($item_cats):
		foreach($item_cats as $item_cat) {
			$item_array[] = $item_cat->term_id;
		}
		endif;

		$args = array(
			'post__not_in' => array($post_id),
			'ignore_sticky_posts' => 0,
			'post_type' => 'portfolio',
			'posts_per_page'	=> 100,
			'tax_query' => array(
				array(
					'taxonomy' => 'portfolio_category',
					'field' => 'term_id',
					'terms' => $item_array
				)
			)
		);
		
		$query = new WP_Query($args);
		
		wp_reset_postdata();
		
		return $query;
	}
}


/**
	BOC Main Portfolio Items Query
**/
if( ! function_exists( 'boc_get_portfolio_items' ) ) { 
	function boc_get_portfolio_items($limit = 10, $order_by = 'rand', $order = 'DESC', $category='', $paged = 1) {

	
		// WPML compatibility :: START
		if(isset($category) && $category){
			$categories_IDs = (explode( "," , str_replace(' ', '', $category)));
			$translated_terms = array() ;
			foreach ($categories_IDs as $cat) {
				$terms_for_wpml = get_term_by('slug', $cat, 'portfolio_category');
				$terms_for_wpml = apply_filters('wpml_object_id', $terms_for_wpml->term_id, 'custom taxonomy', true);
				$translated_terms []= get_term_by('term_id', $terms_for_wpml, 'category')->slug;
			}
			$category = implode($translated_terms, ",");
		}
		// WPML compatibility :: END
	
	
		$args = array(
			'ignore_sticky_posts' => 0,
			'post_type' => 'portfolio',
			'posts_per_page' => (int)$limit,
			'orderby'=> $order_by,
			'order'=> $order,
			'paged' => $paged,
			'portfolio_category' => $category, 
		);
		
		$query = new WP_Query($args);

		wp_reset_postdata();
		
		return $query;
	}
}


/**
	Expanded allowed params for wp_kses
**/
function boc_expand_allowed_tags() {
	$allowed = wp_kses_allowed_html( 'post' );
	// iframe
	$allowed['iframe'] = array(
		'src'             => array(),
		'height'          => array(),
		'width'           => array(),
		'frameborder'     => array(),
		'allowfullscreen' => array(),
	); 
	return $allowed;
}


/**
	BOC Hex to RGB conversion
**/
if( ! function_exists( 'boc_HexToRGB' ) ) { 
	function boc_HexToRGB($hex, $transparency) {
			$hex = str_replace("#", "", $hex);
			$color = array();

			if(strlen($hex) == 3) {
				$color['r'] = hexdec(substr($hex, 0, 1) . $r);
				$color['g'] = hexdec(substr($hex, 1, 1) . $g);
				$color['b'] = hexdec(substr($hex, 2, 1) . $b);
			}
			else if(strlen($hex) == 6) {
				$color['r'] = hexdec(substr($hex, 0, 2));
				$color['g'] = hexdec(substr($hex, 2, 2));
				$color['b'] = hexdec(substr($hex, 4, 2));
			}

			return 'rgba('.$color['r'].','.$color['g'].', '.$color['b'].', '.$transparency.')';
	}
}


/**
	BOC WooCommerce methods
**/
if( ! function_exists( 'boc_cart_in_header' ) ) {
	function boc_cart_in_header() {
			global $woocommerce;
			$woocommerce_cart_in_header = ot_get_option('woocommerce_cart_in_header', 'on');		
			if ($woocommerce_cart_in_header == 'on') { 		
				if ($woocommerce) { 
					return true;
				}
			}else return false;
	}
}

/**
	Generate the Cart in Header Drop Down Content
**/

if( ! function_exists( 'boc_render_cart_in_header' ) ) {
	function boc_render_cart_in_header () {
		
		global $woocommerce;
		$cart_classes = 'is_empty';
		if($woocommerce->cart->cart_contents_count > 0) {
			$cart_classes = 'is_not_empty';
		}
		if("style_dark" == ot_get_option('woocommerce_cart_in_header_bgr_color', 'style_dark')) {
			$cart_classes .= ' style_dark';
		}
	?>
				<div class="header_cart <?php echo esc_attr($cart_classes);?>">
					<div class="cart_widget_holder">
					
						<a class="cart-contents icon icon-shopping631" href="<?php echo esc_url($woocommerce->cart->get_cart_url()); ?>">
							<p class="cart-wrap"><span><?php echo esc_html($woocommerce->cart->cart_contents_count); ?></span></p>
						</a>
						
						<div class="cart-notification">
							<span class="item-name"></span> <?php echo __('was successfully added to your cart.','Fortuna'); ?>
						</div>
						
						<?php
							// Check for WooCommerce 2.0 and display the cart widget
							if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
								the_widget( 'WC_Widget_Cart', 'title= ' );
							} else {
								the_widget( 'WooCommerce_Widget_Cart', 'title= ' );
							}
						?>
					</div>
					
				</div>
	<?php
	}
}





/**
	Page Background
**/
if( ! function_exists( 'boc_get_BGR_css' ) ) {
	function boc_get_BGR_css(){

		$custom_background_css = '';

		$global_bgr_set = false;
		
		// Per Page background in Page Settings
		if ( is_single() || is_page() ) {
			
			$background = get_post_meta( get_the_ID(), 'boc_page_bgr', true );
			if ( !empty( $background ) /* && $background['background-image']*/ ) {
				$background_color       = ( $background['background-color'] != '' ) ? $background['background-color'] . ' ' : '';
				$background_image       = ( $background['background-image'] != '' ) ? 'url('.$background['background-image'].') ' : '';
				$background_repeat      = ( $background['background-repeat'] != '' ) ? $background['background-repeat']. ' ' : '';
				$background_positon     = ( $background['background-position'] != '' ) ? $background['background-position']. ' ' : '';
				$background_attachment  = ( $background['background-attachment'] != '' ) ? $background['background-attachment']. ' ' : '';
				$background_size        = ( $background['background-size'] != '' ) ? 'background-size: '. $background['background-size']. ';' : '';
				
				$custom_background_css .= 
					'/* Custom Background for: ' . get_the_title() . ' */' . "\n" .
					'body { background: '.wp_kses_post($background_color.$background_image.$background_repeat.$background_attachment.$background_positon).';'."\n". wp_kses_post($background_size) .'}';
				
				$global_bgr_set = true;
			}
		}

		// If not yet set, search for global setting
		if (!$global_bgr_set){
			
			// Global BGR in Theme Options
			$global_background = ot_get_option('boc_page_global_bgr', array());
			if ( (isset($global_background['background-color']) && $global_background['background-color']) || 
						(isset($global_background['background-image']) && $global_background['background-image'])) {
							
				$background_color       = ( $global_background['background-color'] != '' ) ? $global_background['background-color'] . ' ' : '';
				$background_image       = ( $global_background['background-image'] != '' ) ? 'url('.$global_background['background-image'].') ' : '';
				$background_repeat      = ( $global_background['background-repeat'] != '' ) ? $global_background['background-repeat']. ' ' : '';
				$background_positon     = ( $global_background['background-position'] != '' ) ? $global_background['background-position']. ' ' : '';
				$background_attachment  = ( $global_background['background-attachment'] != '' ) ? $global_background['background-attachment']. ' ' : '';
				$background_size        = ( $global_background['background-size'] != '' ) ? 'background-size: '. $global_background['background-size']. ';' : '';
				
				$custom_background_css .= 
					'/* Global Background */' . "\n" .
					'body { background: '.wp_kses_post($background_color.$background_image.$background_repeat.$background_attachment.$background_positon).';'."\n". wp_kses_post($background_size) .'}';
			}
		}
		

		$custom_page_heading_bgr_set = false;
		
		// Per Page Heading BGR in Page Settings
		if ( is_single() || is_page() || is_home() ) {
			
			
			if(is_home()) {
				$post_id = get_option( 'page_for_posts' );
			}else {
				$post_id = get_the_ID();
			}
			
			$page_heading_bgr_meta = get_post_meta( $post_id, 'boc_page_heading_bgr_meta', true );
			if ( !empty( $page_heading_bgr_meta ) /* && $page_heading_bgr_meta['background-image']*/ ) {
				$ph_background_color_meta       = ( $page_heading_bgr_meta['background-color'] != '' ) ? $page_heading_bgr_meta['background-color'] . ' ' : '';
				$ph_background_image_meta       = ( $page_heading_bgr_meta['background-image'] != '' ) ? 'url('.$page_heading_bgr_meta['background-image'].') ' : '';
				$ph_background_repeat_meta      = ( $page_heading_bgr_meta['background-repeat'] != '' ) ? $page_heading_bgr_meta['background-repeat']. ' ' : '';
				$ph_background_positon_meta     = ( $page_heading_bgr_meta['background-position'] != '' ) ? $page_heading_bgr_meta['background-position']. ' ' : '';
				$ph_background_attachment_meta  = ( $page_heading_bgr_meta['background-attachment'] != '' ) ? $page_heading_bgr_meta['background-attachment']. ' ' : '';
				$ph_background_size_meta        = ( $page_heading_bgr_meta['background-size'] != '' ) ? 'background-size: '. $page_heading_bgr_meta['background-size']. ';' : '';
				
				$custom_background_css .= 
					'/* Custom Background for: ' . get_the_title() . ' */' . "\n" .
					'.page_title_bgr .full_container_page_title,
					 .page_title_bgr.bgr_style1 .full_container_page_title { 
						background: '.wp_kses_post($ph_background_color_meta.$ph_background_image_meta.$ph_background_repeat_meta.$ph_background_attachment_meta.$ph_background_positon_meta).';'."\n". wp_kses_post($ph_background_size_meta) .'}';
						
				$custom_page_heading_bgr_set = true;
			}
			
			// Heading Text Color white?
			if(get_post_meta( $post_id, 'boc_page_heading_white_text_meta', true ) == 'on'){
				$custom_background_css .= 
					'/* Page Heading Text Color - White */' . "\n" .
					'.page_heading h1,
					 .breadcrumb a{ 
						color: #fff;
					}';
			}			
			
		
		}
		
		// If not yet set, search for global setting
		if(!$custom_page_heading_bgr_set){
			
			// Heading BGR Global		
			$page_heading_style = ot_get_option('page_heading_style') ? ot_get_option('page_heading_style') : '';
			if($page_heading_style == "page_title_bgr custom_bgr") {
				$page_heading_bgr = ot_get_option('boc_page_heading_style_bgr', array());
				if ( !empty( $page_heading_bgr)) {
					$ph_background_color       = ( $page_heading_bgr['background-color'] != '' ) ? $page_heading_bgr['background-color'] . ' ' : '';
					$ph_background_image       = ( $page_heading_bgr['background-image'] != '' ) ? 'url('.$page_heading_bgr['background-image'].') ' : '';
					$ph_background_repeat      = ( $page_heading_bgr['background-repeat'] != '' ) ? $page_heading_bgr['background-repeat']. ' ' : '';
					$ph_background_positon     = ( $page_heading_bgr['background-position'] != '' ) ? $page_heading_bgr['background-position']. ' ' : '';
					$ph_background_attachment  = ( $page_heading_bgr['background-attachment'] != '' ) ? $page_heading_bgr['background-attachment']. ' ' : '';
					$ph_background_size        = ( $page_heading_bgr['background-size'] != '' ) ? 'background-size: '. $page_heading_bgr['background-size']. ';' : '';
					
					$custom_background_css .= 
						'/* Page Heading Background */' . "\n" .
						'.page_title_bgr .full_container_page_title { 
							background: '.wp_kses_post($ph_background_color.$ph_background_image.$ph_background_repeat.$ph_background_attachment.$ph_background_positon).';'."\n". wp_kses_post($ph_background_size) .'}';
				}
			}
			
			// Heading Text Color white?
			if(ot_get_option('boc_page_heading_white_text','off') == 'on'){
				$custom_background_css .= 
					'/* Page Heading Text Color - White */' . "\n" .
					'.page_heading h1,
					 .breadcrumb a{ 
						color: #fff;
					}';
			}
			
		}
		
		return $custom_background_css;
	}
}




/**
	BOC Inline CSS Generation
**/
if( ! function_exists( 'boc_get_inline_CSS' ) ) { 
	function boc_get_inline_CSS() {


		global $boc_js_params;

		$fonts_available = 	array_merge(get_theme_mod( 'ot_google_fonts', array() ) , boc_get_system_fonts());
		
		// Inline CSS to be included
		$inline_css = '';

		// Page Background image
		$inline_css .= boc_get_BGR_css();
		
		
		//---- MAIN MENU ITEMS (Top Level)
	
		// Nav Font Family
		$nav_font = ot_get_option('nav_font');
		if(is_array($nav_font) && ($nav_font['font-family'] != 'montserrat')  && ($nav_font['font-family'])) {
			$inline_css .="
				#menu > ul > li > a {
					font-family: '".wp_kses_post($fonts_available[$nav_font['font-family']]['family'])."', Montserrat, Arial, Helvetica, sans-serif;
				}\n";
		}
		// Nav font size
		if(is_array($nav_font) && ($nav_font['font-size']!="14px") && ($nav_font['font-size'])){
			$inline_css .="
				#menu > ul > li > a {
					font-size: ".wp_kses_post($nav_font['font-size']).";
				}\n";
		}
		// Nav font weight
		if(is_array($nav_font) && (!in_array($nav_font['font-weight'],array('normal','400'))) && ($nav_font['font-weight'])){
			$inline_css .="
				#menu > ul > li > a {
					font-weight: ".wp_kses_post($nav_font['font-weight']).";
				}\n";
		}	
		// Nav text transform
		if(is_array($nav_font) && ($nav_font['text-transform']!="uppercase") && ($nav_font['text-transform'])){
			$inline_css .="
				#menu > ul > li > a {
					text-transform: ".wp_kses_post($nav_font['text-transform']).";
				}\n";
		}		
		// Nav letter spacing
		if(is_array($nav_font) && ($nav_font['letter-spacing']!='0em') && ($nav_font['letter-spacing'])){
			$inline_css .="
				#menu > ul > li > a {
					letter-spacing: ".wp_kses_post($nav_font['letter-spacing']).";
				}\n";
		}	

		
		//---- SUBMENU ITEMS 
		
		// Nav Font Family
		$sub_nav_font = ot_get_option('sub_nav_font');
		
		if(is_array($sub_nav_font) && ($sub_nav_font['font-family'] != 'montserrat')  && ($sub_nav_font['font-family'])) {
			
			$inline_css .="
				#menu > ul > li ul > li > a {
					font-family: '".wp_kses_post($fonts_available[$sub_nav_font['font-family']]['family'])."', Montserrat, Arial, Helvetica, sans-serif;
				}\n";
		}
		// Nav font size
		if(is_array($sub_nav_font) && ($sub_nav_font['font-size']!="13px") && ($sub_nav_font['font-size'])){
			$inline_css .="
				#menu > ul > li ul > li > a {
					font-size: ".wp_kses_post($sub_nav_font['font-size']).";
				}\n";
		}
		// Nav font weight
		if(is_array($sub_nav_font) && (!in_array($sub_nav_font['font-weight'],array('normal','400'))) && ($sub_nav_font['font-weight'])){
			$inline_css .="
				#menu > ul > li ul > li > a {
					font-weight: ".wp_kses_post($sub_nav_font['font-weight']).";
				}\n";
		}
		// Nav text transform
		if(is_array($sub_nav_font) && ($sub_nav_font['text-transform']!="capitalize") && ($sub_nav_font['text-transform'])){
			$inline_css .="
				#menu > ul > li ul > li > a {
					text-transform: ".wp_kses_post($sub_nav_font['text-transform']).";
				}\n";
		}
		// Nav letter spacing
		if(is_array($sub_nav_font) && ($sub_nav_font['letter-spacing']!='0em') && ($sub_nav_font['letter-spacing'])){
			$inline_css .="
				#menu > ul > li ul > li > a {
					letter-spacing: ".wp_kses_post($sub_nav_font['letter-spacing']).";
				}\n";
		}



		//---- Headings
			
		// Headings Font Family
		$heading_font = ot_get_option('heading_font');
		if(is_array($heading_font) && ($heading_font['font-family'] != 'montserrat')  && ($heading_font['font-family'])) {
			$inline_css .="
				h1, h2, h3, h4, h5, h6, .title, .heading_font, .counter-digit, .htabs a, .woocommerce-page div.product .woocommerce-tabs ul.tabs li {
					font-family: '".wp_kses_post($fonts_available[$heading_font['font-family']]['family'])."', Montserrat, Arial, Helvetica, sans-serif;
				}\n";
		}
		// Headings font weight
		if(is_array($heading_font) && (!in_array($heading_font['font-weight'],array('normal','400'))) && ($heading_font['font-weight'])){
			$inline_css .="
				h1, h2, h3, h4, h5, h6, .title, .heading_font, .counter-digit, .htabs a, .woocommerce-page div.product .woocommerce-tabs ul.tabs li {
					font-weight: ".wp_kses_post($heading_font['font-weight']).";
				}\n";
		}
		// Headings text transform
		if(is_array($heading_font) && ($heading_font['text-transform']!="uppercase") && ($heading_font['text-transform'])){
			$inline_css .="
				h1, h2, h3, h4, h5, h6, .title, .heading_font, .counter-digit, .htabs a, .woocommerce-page div.product .woocommerce-tabs ul.tabs li {
					text-transform: ".wp_kses_post($heading_font['text-transform']).";
				}\n";
		}
		// Headings letter spacing
		if(is_array($heading_font) && ($heading_font['letter-spacing']!='-0.02em') && ($heading_font['letter-spacing'])){
			$inline_css .="
				h1, h2, h3, h4, h5, h6, .title, .heading_font, .counter-digit, .htabs a, .woocommerce-page div.product .woocommerce-tabs ul.tabs li {
					letter-spacing: ".wp_kses_post($heading_font['letter-spacing']).";
				}\n";
		}

		


		//---- Body
			
		// Body Font Family
		$body_font = ot_get_option('body_font');
		if(is_array($body_font) && ($body_font['font-family'] != 'lato')  && ($body_font['font-family'])) {
			$inline_css .="
				body, .body_font, .body_font h1, .body_font h2, .body_font h3, .body_font h4, .body_font h5 {
					font-family: '".wp_kses_post($fonts_available[$body_font['font-family']]['family'])."', Arial, Helvetica, sans-serif;
				}\n";
		}
		// Body font size
		if(is_array($body_font) && ($body_font['font-size']!="16px") && ($body_font['font-size'])){
			$inline_css .="
				body {
					font-size: ".wp_kses_post($body_font['font-size']).";
				}\n";
		}	
		// Body font weight
		if(is_array($body_font) && (!in_array($body_font['font-weight'],array('normal','400'))) && ($body_font['font-weight'])){
			$inline_css .="
				body {
					font-weight: ".wp_kses_post($body_font['font-weight']).";
				}\n";
		}

		
		//---- Buttons
			
		// Button Font Family
		$button_font = ot_get_option('button_font');
		if(is_array($button_font) && ($button_font['font-family'] != 'montserrat')  && ($button_font['font-family'])) {
			$inline_css .="
				.button, a.button, button, input[type='submit'], input[type='reset'], input[type='button'] {
					font-family: '".wp_kses_post($fonts_available[$button_font['font-family']]['family'])."', Arial, Helvetica, sans-serif;
				}\n";
		}
		// Button text transform
		if(is_array($button_font) && ($button_font['text-transform']!="none") && ($button_font['text-transform'])){
			$inline_css .="
				.button, a.button, button, input[type='submit'], input[type='reset'], input[type='button'] {
					text-transform: ".wp_kses_post($button_font['text-transform']).";
				}\n";
		}	
		// Button font weight
		if(is_array($button_font) && (!in_array($button_font['font-weight'],array('normal','400'))) && ($button_font['font-weight'])){
			$inline_css .="
				.button, a.button, button, input[type='submit'], input[type='reset'], input[type='button'] {
					font-weight: ".wp_kses_post($button_font['font-weight']).";
				}\n";
		}		
		// Button letter spacing
		if(is_array($button_font) && isset($button_font['letter-spacing']) && ($button_font['letter-spacing']!='0em') && ($button_font['letter-spacing'])){
			$inline_css .="
				.button, a.button, button, input[type='submit'], input[type='reset'], input[type='button'] {
					letter-spacing: ".wp_kses_post($button_font['letter-spacing']).";
				}\n";
		}
		

		// Header Height
		$boc_js_params['header_height'] = 92;
		if((($header_height = boc_header_height())!=92) && !boc_is_main_nav_block_style()) {
			$inline_css .="
			  @media only screen and (min-width: 1018px){
				#menu > ul > li > a, #header .header_cart .icon { line-height: ".(int)($header_height - 4)."px; }
				.header_cart .widget_shopping_cart { top: ".(int)($header_height - 4)."px; }
				#menu > ul > li.boc_nav_button { height: ".(int)($header_height - 4)."px; }
				#logo .logo_img { height: ".(int)$header_height."px; }
				#boc_searchform_close { top:".round((int)($header_height - 22)/2)."px; }
			  }\n";
			  $boc_js_params['header_height'] = (int)$header_height;
		}
		
		// Normal/Sticky Header
		if(!($sticky_header = boc_is_header_sticky())){
			$inline_css .="
				#header { 
					position: relative;
					-webkit-transition: 0;
					-moz-transition: 0;
					-ms-transition: 0;
					-o-transition: 0;
					transition: 0;
				}\n";
			$boc_js_params['sticky_header'] = 0;
		}else {
			$boc_js_params['sticky_header'] = 1;
		}
			
		// Submenu Arrow Effect
		if(!($submenu_arrow_effect = boc_is_submenu_arrow_effect())){
			$boc_js_params['submenu_arrow_effect'] = 0;
		}else {
			$boc_js_params['submenu_arrow_effect'] = 1;
		}
		
		// MegaMenu bordered columns
		if(!($mm_bordered_columns = boc_is_mm_bordered_columns())){
			$inline_css .='	
				#menu > ul > li.megamenu  > div > ul.sub-menu > li{ 
					border-left: none!important; 
				}
			';
			$boc_js_params['mm_bordered_columns'] = 0;
		}else {
			$boc_js_params['mm_bordered_columns'] = 1;
		}
		
		// If using Sticky Header overwrite Sticky BGR if any of the 2 values have been changed
		if($sticky_header){			
			$sticky_header_opacity = boc_get_sticky_header_opacity();
			$sticky_header_color = boc_get_sticky_header_color();
			if(($sticky_header_opacity != 0.97) || ($sticky_header_color!= '#ffffff')){
				$inline_css .='	
					#header.scrolled {
						background: '.boc_HexToRGB(esc_attr($sticky_header_color), esc_attr($sticky_header_opacity)).';
					}
				';
			}
		}
		
		// Sticky Header Height
		if((($sticky_header_height = boc_sticky_header_height())!=64) && $sticky_header) {
			$sticky_header_height = esc_attr($sticky_header_height);
			$inline_css .="
			  @media only screen and (min-width: 1018px){	
				#header.scrolled #menu > ul > li > a, #header.scrolled .header_cart .icon { line-height: ".($sticky_header_height - 4)."px; }
				#header.scrolled .header_cart .widget_shopping_cart { top: ".($sticky_header_height - 4)."px; }
				#header.scrolled #menu > ul > li.boc_nav_button { height: ".($sticky_header_height - 4)."px; }
				#header.scrolled #logo .logo_img { height: ".$sticky_header_height."px;}
				#header.scrolled #boc_searchform_close { top:".round(($sticky_header_height - 22)/2)."px; }
			  }\n";
		}	
		
		// Transparent Header
		if(boc_is_transparent_header()){
			$boc_js_params['transparent_header'] = 1;
			if(!boc_is_header_sticky()){
				$inline_css .="
					@media only screen and (min-width: 1018px){
							#header {
								position: absolute;
								-webkit-transition: 0;
								-moz-transition: 0;
								-ms-transition: 0;
								-o-transition: 0;
								transition: 0;
							}
					}\n";
			}
		}else {
			$boc_js_params['transparent_header'] = 0;
		}
		
		// Global Border Radius
		if($rounded_images = ot_get_option('rounded_images',1)){
			$inline_css .="
				img, 
				.pic, 
				.pic_info.type7 .info_overlay { 
					-moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px;
				}
				.post_item_block.boxed {
					-moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;
				}

				.pic_info.type1 img,
				.pic_info.type2 .pic, .pic_info.type2 img,
				.pic_info.type3 .pic,
				.pic_info.type4 .pic,
				.pic_info.type5 .pic,
				.post_item_block.boxed .pic,
				.post_item_block.boxed .pic img {
					-moz-border-radius: 3px 3px 0 0; -webkit-border-radius: 3px 3px 0 0; border-radius: 3px 3px 0 0;
				}\n";
		}
		
		// Top Level Main Menu Top level Item Colors
		if(($boc_main_nav_color = boc_get_main_navigation_color())!='#333333'){
			$boc_main_nav_color = esc_attr($boc_main_nav_color);
			$inline_css .="
				#menu > ul > li > a, #header .header_cart a.icon { color: ".$boc_main_nav_color."; }
				#menu > ul > li.boc_nav_button a{ color: ".$boc_main_nav_color."; border: 2px solid ".$boc_main_nav_color."; }\n";
		}
		if(($boc_main_nav_hover_color = boc_get_main_navigation_hover_color())!='#333333'){
			$boc_main_nav_hover_color = esc_attr($boc_main_nav_hover_color);
			$inline_css .="
				#menu > ul > li:not(.boc_nav_button):hover > a, #header .header_cart li a.icon:hover { color: ".$boc_main_nav_hover_color."; }
				#menu > ul > li.boc_nav_button a:hover{ background: ".$boc_main_nav_hover_color."; border: 2px solid ".$boc_main_nav_hover_color."; }\n";
		}
		
		// Update Main Nav underline effect Color
		if(boc_is_main_nav_underline_effect()){
			$inline_css .="
				.main_menu_underline_effect #menu > ul > li > a:after{ background-color: ".esc_attr(boc_main_nav_underline_effect_color())."; }\n";
		}
		
		// Custom Menu BGR color
		if(($nav_bgr_color = get_theme_mod('nav_bgr_color', "#08ada7"))!="#08ada7"){
			$nav_bgr_color = esc_attr($nav_bgr_color);
			$inline_css .="
				.custom_menu_1 #menu > ul > li div { border-top: 2px solid ".$nav_bgr_color."; }

				.custom_menu_2 #menu > ul > li div { border-top: 2px solid ".$nav_bgr_color."; }

				.custom_menu_3 #menu > ul > li div { border-top: 2px solid ".$nav_bgr_color.";}
				.custom_menu_3 #menu > ul > li ul > li > a:hover { background-color: ".$nav_bgr_color.";}

				.custom_menu_4 #menu > ul > li div { border-top: 2px solid ".$nav_bgr_color.";}			
				.custom_menu_4 #menu > ul > li ul > li > a:hover { background-color: ".$nav_bgr_color.";}
				
				.custom_menu_5 #menu > ul > li ul > li > a:hover { background-color: ".$nav_bgr_color.";}
				.custom_menu_5 #menu > ul > li:hover > a { border-top: 2px solid ".$nav_bgr_color.";}

				.custom_menu_6 #menu > ul > li ul > li > a:hover { background-color: ".$nav_bgr_color.";}
				.custom_menu_6 #menu > ul > li:not(.boc_nav_button):hover > a { border-top: 2px solid ".$nav_bgr_color.";}
			";
		}

		// Main Color
		$boc_main_color = boc_get_main_color();
		$boc_default_color = boc_get_default_color();
		
		if($boc_main_color != $boc_default_color){
			$boc_main_color = esc_attr($boc_main_color);
			$inline_css .='	
				a:hover, a:focus,
				.post_content a:not(.button), 
				.post_content a:not(.button):visited,
				.post_content .wpb_widgetised_column a:not(.button):hover {	color: '.$boc_main_color.'; }
				
				.post_content .wpb_widgetised_column .side_bar_menu a:not(.button):hover { color: #333; }
				
				.boc_preloader_icon:before { border-color: '.$boc_main_color.' rgba(0,0,0,0) rgba(0,0,0,0); }
				
				.dark_links a:hover, .white_links a:hover, .dark_links a:hover h2, .dark_links a:hover h3 { color: '.$boc_main_color.' !important; }
				
				.side_icon_box h3 a:hover, 
				.post_content .team_block h4 a:hover,
				.team_block .team_icons a:hover{ color:'.$boc_main_color.'; }

				.button:hover,a:hover.button,button:hover,input[type="submit"]:hover,input[type="reset"]:hover,	input[type="button"]:hover, .btn_theme_color, a.btn_theme_color { color: #fff; background-color:'.$boc_main_color.';}
				input.btn_theme_color, a.btn_theme_color, .btn_theme_color { color: #fff; background-color:'.$boc_main_color.';}
				.btn_theme_color:hover, input.btn_theme_color:hover, a:hover.btn_theme_color { color: #fff; background-color: #444444;}
				
				input.btn_theme_color.btn_outline, a.btn_theme_color.btn_outline, .btn_theme_color.btn_outline {
					color: '.$boc_main_color.' !important;
					border: 2px solid '.$boc_main_color.';
				}
				input.btn_theme_color.btn_outline:hover, a.btn_theme_color.btn_outline:hover, .btn_theme_color.btn_outline:hover{
					background-color: '.$boc_main_color.' !important;
				}
				
				#boc_searchform_close:hover { color:'.$boc_main_color.';}
				
				.section_big_title h1 strong, h1 strong, h2 strong, h3 strong, h4 strong, h5 strong { color:'.$boc_main_color.';}
				.top_icon_box h3 a:hover { color:'.$boc_main_color.';}

				.htabs a.selected  { border-top: 2px solid '.$boc_main_color.';}
				.resp-vtabs .resp-tabs-list li.resp-tab-active { border-left: 2px solid '.$boc_main_color.';}
				.minimal_style.horizontal .resp-tabs-list li.resp-tab-active,
				.minimal_style.resp-vtabs .resp-tabs-list li.resp-tab-active { background: '.$boc_main_color.';}
				
				#s:focus {	border: 1px solid '.$boc_main_color.';}
				
				.breadcrumb a:hover{ color: '.$boc_main_color.';}

				.tagcloud a:hover { background-color: '.$boc_main_color.';}
				.month { background-color: '.$boc_main_color.';}
				.small_month  { background-color: '.$boc_main_color.';}

				.post_meta a:hover{ color: '.$boc_main_color.';}
				
				.horizontal .resp-tabs-list li.resp-tab-active { border-top: 2px solid '.$boc_main_color.';}
				.resp-vtabs li.resp-tab-active { border-left: 2px solid '.$boc_main_color.'; }

				#portfolio_filter { background-color: '.$boc_main_color.';}
				#portfolio_filter ul li div:hover { background-color: '.$boc_main_color.';}
				.portfolio_inline_filter ul li div:hover { background-color: '.$boc_main_color.';}

				.counter-digit { color: '.$boc_main_color.';}

				.tp-caption a:hover { color: '.$boc_main_color.';}

				.more-link1:before { color: '.$boc_main_color.';}
				.more-link2:before { background: '.$boc_main_color.';}

				.image_featured_text .pos { color: '.$boc_main_color.';}

				.side_icon_box .icon_feat i.icon { color: '.$boc_main_color.';}
				.side_icon_box .icon_feat.icon_solid { background-color: '.$boc_main_color.'; }
				
				.boc_list_item .li_icon i.icon { color: '.$boc_main_color.';}
				.boc_list_item .li_icon.icon_solid { background: '.$boc_main_color.'; }

				.top_icon_box.type1 .icon_holder .icon_bgr { background-color: '.$boc_main_color.'; }
				.top_icon_box.type1:hover .icon_holder .icon_bgr { border: 2px solid '.$boc_main_color.'; }
				.top_icon_box.type1 .icon_holder .icon_bgr:after,
				.top_icon_box.type1:hover .icon_holder .icon_bgr:after { border: 2px solid '.$boc_main_color.'; }
				.top_icon_box.type1:hover .icon_holder i { color: '.$boc_main_color.';}

				.top_icon_box.type2 .icon_holder .icon_bgr { background-color: '.$boc_main_color.'; }
				.top_icon_box.type2:hover .icon_holder .icon_bgr { background-color: #fff; }
				.top_icon_box.type2:hover .icon_holder i { color: '.$boc_main_color.';}

				.top_icon_box.type3 .icon_holder .icon_bgr:after { border: 2px solid '.$boc_main_color.'; }
				.top_icon_box.type3:hover .icon_holder .icon_bgr { background-color: '.$boc_main_color.'; }
				.top_icon_box.type3:hover .icon_holder .icon_bgr:after { border: 2px solid '.$boc_main_color.'; }
				.top_icon_box.type3 .icon_holder i { color: '.$boc_main_color.';}
				.top_icon_box.type3:hover .icon_holder i { color: #fff; }

				.top_icon_box.type4:hover .icon_holder .icon_bgr { border: 2px solid '.$boc_main_color.'; }
				.top_icon_box.type4:hover .icon_holder .icon_bgr:after { border: 3px solid '.$boc_main_color.'; }
				.top_icon_box.type4 .icon_holder i{ color: '.$boc_main_color.'; }
				.top_icon_box.type4:hover .icon_holder i { color:  '.$boc_main_color.'; }

				.top_icon_box.type5 .icon_holder i{ color: '.$boc_main_color.'; }
				.top_icon_box.type5:hover .icon_holder i { color: '.$boc_main_color.'; }

				a .pic_info.type11 .plus_overlay { border-bottom: 50px solid '.boc_HexToRGB($boc_main_color, 0.8).'; }
				a:hover .pic_info.type11 .plus_overlay { border-bottom: 1000px solid '.boc_HexToRGB($boc_main_color, 0.8).';}
				
				a .pic_info.type12 .img_overlay_icon,
				a:hover .pic_info.type12 .img_overlay_icon { background: '.boc_HexToRGB($boc_main_color, 0.8).';}
				
				h2.title strong {  color: '.$boc_main_color.';}
				ul.theme_color_ul li:before { color: '.$boc_main_color.'; }

				.custom_slides.nav_design_1 .cs_nav_item.active .cs_nav_icon i.icon{ color: '.$boc_main_color.';}
				.custom_slides.nav_style_1.nav_design_1 .cs_nav_item:hover .cs_nav_icon i.icon,
				.custom_slides.nav_style_1.nav_design_2 .cs_nav_item:hover .cs_nav_icon i.icon { color: '.$boc_main_color.';}
				.custom_slides.nav_design_2 .cs_nav_item.active .cs_nav_icon { background: '.$boc_main_color.';}
				.cs_nav_item.has_no_text:hover .cs_nav_icon i.icon { color: '.$boc_main_color.';}
				.custom_slides.nav_style_2 .cs_txt { color: '.$boc_main_color.';}
				
				.acc_control, .active_acc .acc_control,
				.acc_holder.with_bgr .active_acc .acc_control { background-color: '.$boc_main_color.';}

				.text_box.left_border {	border-left: 3px solid '.$boc_main_color.'; }

				.owl-theme .owl-controls .owl-nav div { background: '.$boc_main_color.';}
				.owl-theme .owl-dots .owl-dot.active span { background: '.$boc_main_color.';}
				.img_slider.owl-theme .owl-controls .owl-nav div:not(.disabled):hover { background: '.$boc_main_color.';}		

				.testimonial_style_big.owl-theme .owl-controls .owl-nav div:hover,
				.posts_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover, 
				.img_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover,
				.content_slides_arrowed.owl-theme .owl-controls .owl-nav div:hover,
				.portfolio_carousel_holder.owl_side_arrows .owl-theme .owl-controls .owl-nav div:hover	{ color: '.$boc_main_color.';}
				
				.boc_text_slider_word, .boc_text_slider_word_start { background: '.$boc_main_color.'; }

				.post_item_block.boxed .pic { border-bottom: 3px solid '.$boc_main_color.'; }

				.team_block .team_desc { color: '.$boc_main_color.';}

				.bar_graph span, .bar_graph.thin_style span { background-color: '.$boc_main_color.'; }

				.pagination .links a:hover{ background-color: '.$boc_main_color.';}
				.hilite{ background: '.$boc_main_color.';}
				.price_column.price_column_featured ul li.price_column_title{ background: '.$boc_main_color.';}

				blockquote{ border-left: 3px solid '.$boc_main_color.'; }
				.text_box.left_border { border-left: 3px solid '.$boc_main_color.'; }

				.fortuna_table tr:hover td { background: '.boc_HexToRGB($boc_main_color, 0.08).';}

				.header_cart ul.cart_list li a, .header_cart ul.product_list_widget li a { color: '.$boc_main_color.';}
				.header_cart .cart-notification { background-color: '.$boc_main_color.';}
				.header_cart .cart-notification:after { border-bottom-color: '.$boc_main_color.';}
				
				.woocommerce .product_meta a { color: '.$boc_main_color.';}
				
				.woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit, .woocommerce #content input.button, .woocommerce-page a.button, .woocommerce-page button.button, .woocommerce-page input.button, .woocommerce-page #respond input#submit, .woocommerce-page #content input.button { background-color: '.$boc_main_color.'!important; }
				.header_cart .cart-wrap	{ background-color: '.$boc_main_color.'; }
				.header_cart .cart-wrap:before { border-color: transparent '.$boc_main_color.' transparent; }
				.woocommerce .widget_price_filter .ui-slider .ui-slider-range, .woocommerce-page .widget_price_filter .ui-slider .ui-slider-range{ background-color: '.$boc_main_color.' !important;}

				.woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus{ background-color: '.$boc_main_color.' !important;}
				
				.info h2{ background-color: '.$boc_main_color.';}
				#footer a:hover { color: '.$boc_main_color.';}
				
				
				
				a .pic_info.type1 .plus_overlay {	border-bottom: 50px solid '.boc_HexToRGB($boc_main_color,0.8).';}
				a:hover .pic_info.type1 .plus_overlay { border-bottom: 1000px solid '.boc_HexToRGB($boc_main_color,0.8).'; }
				
				a .pic_info.type2 .plus_overlay { border-bottom: 50px solid '.boc_HexToRGB($boc_main_color,0.75).'; }
				a:hover .pic_info.type2 .plus_overlay {	border-bottom: 860px solid '.boc_HexToRGB($boc_main_color,0.8).';}
				
				a .pic_info.type3  .img_overlay_icon {	background: '.boc_HexToRGB($boc_main_color,0.8).'; }
				a:hover .pic_info.type3 .img_overlay_icon {	background: '.boc_HexToRGB($boc_main_color,0.8).';}
				
				a .pic_info.type4 .img_overlay_icon { border-bottom: 2px solid '.boc_HexToRGB($boc_main_color,0.9).';}
				
				a:hover .pic_info.type5 .info_overlay {	background: '.$boc_main_color.';}
				
				.pic_info.type6 .info_overlay {	background: '.$boc_main_color.';}
				a .pic_info.type6 .plus_overlay { border-bottom: 50px solid '.$boc_main_color.'; }
				
				.pic_info.type7 .info_overlay {	background: '.boc_HexToRGB($boc_main_color,0.85).';}				

				@media only screen and (max-width: 768px) {
					.cs_nav .cs_nav_item.active { background: '.$boc_main_color.' !important;}
				}
			';
			
		}	

		
		
		// If using Transparent Header overwrite Main Nav Color
		if(boc_is_transparent_header()){	
			$transparent_header_color = boc_get_transparent_header_color();
			$transparent_header_opacity = boc_get_transparent_header_opacity();
			$transparent_header_nav_hover_color = boc_get_transparent_header_nav_hover_color();
			$inline_css .='	
				@media only screen and (min-width: 1018px){
					#header.transparent_header:not(.scrolled) { background: '.esc_attr(boc_HexToRGB($transparent_header_color, $transparent_header_opacity)).'; }'.
					
					(($transparent_header_opacity==0) ? '#header.transparent_header:not(.scrolled) { -webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none; }' : '').'

					#header.transparent_header:not(.scrolled) #subheader, 
					#header.transparent_header:not(.scrolled) #menu > ul > li > a, 
					#header.transparent_header:not(.scrolled) .header_cart a.icon, 
					#header.transparent_header:not(.scrolled) #menu > ul > li.boc_search_border > a:hover  { 
						color:'.esc_attr(boc_get_transparent_header_nav_color()).'; }	
					
					#header.transparent_header:not(.scrolled) .boc_menu_icon_ham  { 
						background:'.esc_attr($transparent_header_nav_hover_color).'; }
					
					
					#header.transparent_header:not(.scrolled) #menu > ul > li:hover > a,
					#header.transparent_header:not(.scrolled) .header_cart a.icon:hover,
					#header.transparent_header:not(.scrolled) #subheader a:hover { 
						color:'.esc_attr($transparent_header_nav_hover_color).'; }
						

					#header.transparent_header:not(.scrolled) #menu > ul > li.boc_nav_button a	{
						color:'.esc_attr(boc_get_transparent_header_nav_color()).';
						border: 2px solid '.esc_attr(boc_get_transparent_header_nav_color()).';
					}
					#header.transparent_header:not(.scrolled) #menu > ul > li.boc_nav_button a:hover	{
						background:'.esc_attr($transparent_header_nav_hover_color).';
						border: 2px solid '.esc_attr($transparent_header_nav_hover_color).';
					'.(($transparent_header_nav_hover_color=="#ffffff") ? '	color: #333 !important;' : '').'
					}
				}				
			';
		}

		// Breadcrumbs
		if(ot_get_option('breadcrumbs','on')!='on'){
			$inline_css .="
			.breadcrumb {
				display: none;
			}\n";
		}

		// Footer Position
		if(!$footer_position = ot_get_option('footer_position')){
			$inline_css .="
			#footer {
				position: relative;
			}\n";
			$boc_js_params['fixed_footer'] = 0;
		} else {
			$boc_js_params['fixed_footer'] = 1;
		}

		// Custom CSS
		if($boc_custom_css = ot_get_option('custom_css')){
			$inline_css .="\n\n".esc_attr($boc_custom_css)."\n";
		}	
		
		return html_entity_decode($inline_css);
	}
}

if( ! function_exists( 'boc_get_system_fonts' ) ) {
	function boc_get_system_fonts() {
			
		$families = array(
		  'arial'     =>  array(
							'family' => 'Arial',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'georgia'   => array(
							'family' => 'Georgia',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'helvetica' => array(
							'family' => 'Helvetica',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'palatino'  => array(
							'family' => 'Palatino',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'tahoma'    => array(
							'family' => 'Tahoma',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'times'     => array(
							'family' => '"Times New Roman", sans-serif',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'trebuchet' => array(
							'family' => 'Trebuchet',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		  'verdana'   => array(
							'family' => 'Verdana',
							'variants' => array("regular", "italic", "700"),
							'subsets' => array(),  
						  ),
		);
		
		return $families;
	}
}

if( ! function_exists( 'boc_get_attachment_data' ) ) {
	function boc_get_attachment_data( $attachment_id ) {  
		$attachment = get_post( $attachment_id );  
		return array(  
		   'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),  
		   'caption' => $attachment->post_excerpt,  
		   'description' => $attachment->post_content,  
		   'href' => get_permalink( $attachment->ID ),  
		   'src' => $attachment->guid,  
		   'title' => $attachment->post_title  
		);  
	}  
}


if( ! function_exists( 'boc_theme_register_required_plugins' ) ) {
	function boc_theme_register_required_plugins() {

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			array(
				'name'					=> 'Fortuna Portfolio CPT', // The plugin name
				'slug'					=> 'fortuna_portfolio_cpt', // The plugin slug (typically the folder name)
				'source'				=> get_template_directory() . '/plugins/fortuna_portfolio_cpt.zip', // The plugin source
				'required'				=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'	=> true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'					=> 'WPBakery Visual Composer', // The plugin name
				'slug'					=> 'js_composer', // The plugin slug (typically the folder name)
				'source'				=> get_template_directory() . '/plugins/js_composer.zip', // The plugin source
				'required'				=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation'		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'					=> 'Parallax & Video Backgrounds for Visual Composer', // The plugin name
				'slug'					=> 'parallax_video_backgrounds_vc', // The plugin slug (typically the folder name)
				'source'				=> get_template_directory() . '/plugins/parallax_video_backgrounds_vc.zip', // The plugin source
				'required'				=> false, // If false, the plugin is only 'recommended' instead of required
				'force_activation'		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name'					=> 'Revolution Slider', // The plugin name
				'slug'					=> 'revslider', // The plugin slug (typically the folder name)
				'source'				=> get_template_directory() . '/plugins/revslider.zip', // The plugin source
				'required'				=> false, // If false, the plugin is only 'recommended' instead of required
				'force_activation'		=> false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation'	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'			=> '', // If set, overrides default API URL and points to an external URL
			),
			array(
				'name' 		=> 'Contact Form 7',
				'slug' 		=> 'contact-form-7',
				'required' 	=> false,
			),
			array(
				'name' 		=> 'Really Simple CAPTCHA',
				'slug' 		=> 'really-simple-captcha',
				'required' 	=> false,
			),
			array(
				'name' 		=> 'Custom Post Template',
				'slug' 		=> 'custom-post-template',
				'required' 	=> false,
			),	
			array(
				'name' 		=> 'Wordpress Importer',
				'slug' 		=> 'wordpress-importer',
				'required' 	=> false,
			),			

		);

		// Change this to your theme text domain, used for internationalising strings
		$theme_text_domain = 'Fortuna';

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'parent_slug' 		=> 'themes.php', 				// Default parent menu slug
			'menu'         		=> 'install-required-plugins', 	// Menu slug
			'has_notices'      	=> true,                       	// Show admin notices or not
			'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
		);

		tgmpa( $plugins, $config );
	}
}