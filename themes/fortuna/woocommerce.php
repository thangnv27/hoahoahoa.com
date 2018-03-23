<?php get_header(); ?>


<?php 

//change to 3 columns per row when using sidebar
if (!function_exists('boc_loop_3columns')) {
	function boc_loop_3columns() {
		return 3; // 3 products per row
	}
}


$rel_products = array(4, 4);
// Method that outputs related products, takes in an array of 2 numbers, total products + products per row
function boc_woocommerce_output_related_products() {

	global $rel_products;
	$output = null;

	ob_start();
	woocommerce_related_products(array(
	  'posts_per_page' => $rel_products[0],
	  'columns'        => $rel_products[1]
	));  // Display A products in rows of B
	
	$content = ob_get_clean();
	if($content) { $output .= $content; }

	echo '<div class="clear"></div>' . $output;	
}


// Add Static Content (woo_static_top_content) set in Theme Options
if(is_shop()) {

	if($woo_static_top_content = ot_get_option('woo_static_top_content',0)) {
		echo '<!-- Woo Static Top Content :: start --><div class="container"><div class="section">';
		echo do_shortcode(wp_kses_post($woo_static_top_content));
		echo '</div></div><!-- Woo Static Top Content :: end -->';
	}
}
?>

<div class="container">
	<div class="section">
				
			<?php			
			
			$woocommerce_layout = ot_get_option( 'woocommerce_sidebar_layout', 'no-sidebar' );
			$single_product_layout = ot_get_option( 'woocommerce_single_product_sidebar_layout', 'no-sidebar' );

			//single product layout
			if(is_product()){	
				
				if($single_product_layout == 'right-sidebar' || $single_product_layout == 'left-sidebar'){
					add_filter('loop_shop_columns', 'boc_loop_3columns');
				}
				
				switch($single_product_layout) {
					case 'no-sidebar':
						$rel_products = array(4,4);
						add_action( 'woocommerce_after_single_product_summary', 'boc_woocommerce_output_related_products', 20);
						
						echo '<div class="woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						break;
						
						
					case 'right-sidebar':
						$rel_products = array(3,3);
						add_action( 'woocommerce_after_single_product_summary', 'boc_woocommerce_output_related_products', 20);
						
						echo '<div class="col span_3_of_4 woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						
						echo '<!-- WooSidebar -->
							  <div id="sidebar" class="col span_1_of_4 sidebar">';
						if ( ! dynamic_sidebar('WooCommerce Product Page Sidebar') ) : ?>
							<h4 class="left_title">WooCommerce Product Page Sidebar</h4>
							<p><a href="<?php echo admin_url('widgets.php'); ?>">Assign a widget to this area now.</a></p>	
				<?php	endif;
						echo '</div><!-- WooSidebar :: End -->';
						break;
						
						
					case 'left-sidebar':
						$rel_products = array(3,3);
						add_action( 'woocommerce_after_single_product_summary', 'boc_woocommerce_output_related_products', 20);
						echo '<!-- WooSidebar -->
							  <div id="sidebar" class="col span_1_of_4 sidebar">';
						if ( ! dynamic_sidebar('WooCommerce Product Page Sidebar') ) : ?>
							<h4 class="left_title">WooCommerce Product Page Sidebar</h4>
							<p><a href="<?php echo admin_url('widgets.php'); ?>">Assign a widget to this area now.</a></p>	
				<?php	endif;
						echo '</div><!-- WooSidebar :: End -->';
						
						echo '<div class="col span_3_of_4 woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						break;
						
						
					default:
						$rel_products = array(4,4);
						add_action( 'woocommerce_after_single_product_summary', 'boc_woocommerce_output_related_products', 20);
						
						echo '<div class="woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						break;
				}
			}
			
			//Main Shop page layout 
			elseif(is_shop() || is_product_category() || is_product_tag()) {
	
				
				if($woocommerce_layout == 'right-sidebar' || $woocommerce_layout == 'left-sidebar'){ 
					add_filter('loop_shop_columns', 'boc_loop_3columns');
				}

				switch($woocommerce_layout) {
					case 'no-sidebar':

						echo '<div class="woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						break;

					case 'right-sidebar':
						echo '<div class="col span_3_of_4 woo_content">';
							woocommerce_content();
						echo '</div><!--columns::end-->';
						
						echo '<!-- WooSidebar -->
							  <div id="sidebar" class="col span_1_of_4 sidebar">';
						if ( ! dynamic_sidebar('WooCommerce Sidebar') ) : ?>
							<h4 class="left_title">WooCommerce Sidebar</h4>
							<p><a href="<?php echo admin_url('widgets.php'); ?>">Assign a widget to this area.</a></p>	
				<?php	endif;
						echo '</div><!-- WooSidebar :: End -->';
						break; 			
						
					case 'left-sidebar':
						echo '<!-- WooSidebar -->
							  <div id="sidebar" class="col span_1_of_4 sidebar">';
						if ( ! dynamic_sidebar('WooCommerce Sidebar') ) : ?>
							<h4 class="left_title">WooCommerce Sidebar</h4>
							<p><a href="<?php echo admin_url('widgets.php'); ?>">Assign a widget to this area.</a></p>	
				<?php	endif;
						echo '</div><!-- WooSidebar :: End -->';
						
						echo '<div class="col span_3_of_4 woo_content">';
							woocommerce_content();
						echo '</div><!--columns::end-->';
						break; 
						
					default: 
						echo '<div class="woo_content">';
						woocommerce_content();
						echo '</div><!--columns::end-->';
						break; 
				}

			}
			
			//regular WooCommerce page layout 
			else {
				
				echo '<div class="woo_content">';
				woocommerce_content();
				echo '</div><!--columns::end-->';
			}
			
			?>

	</div><!--section::end-->
</div><!-- container::end-->

<?php get_footer(); ?>