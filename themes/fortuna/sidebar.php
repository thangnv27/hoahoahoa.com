<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 */

if ( !isset($current_layout) || $current_layout != 'content' ) :
?>
		<!-- Sidebar -->
		<div id="sidebar" class="col span_1_of_4 sidebar">
		
		<?php 
		if(isset($post)){
			$sidebar = get_post_meta($post->ID, "boc_sidebar_set", $single = true);
			if ($sidebar) {
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar($sidebar)) :
				endif;
			}
		}
		
		if (!isset($sidebar) || !$sidebar) { ?>	
			<?php if ( ! dynamic_sidebar('Fortuna Default Sidebar') ) : ?>
			<?php endif; // end sidebar widget area ?>
<?php   } ?>	
			
		</div>
		<!-- Sidebar :: END -->
		
<?php endif; ?>