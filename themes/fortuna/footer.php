
	</div>
	<!-- Page content::END -->
<?php 

	// Check if Header is disabled
	$footer_is_off = false;
	if(isset($post->ID)){
		// Check Page Settings also
		$footer_is_off = get_post_meta($post->ID, 'boc_footer_is_off', true)=='on' ? true : false;
	}	
	
	if(!$footer_is_off) {

		// Get footer Options
		$footer_style = (bool)ot_get_option('footer_style');
		$footer_columns = (int)ot_get_option('footer_columns', 4); 
	?>

		<!-- Footer::Start -->
		<div id="footer" class="<?php echo (!$footer_style ? 'footer_light' : '');?>" <?php echo (!$footer_columns ? "style='padding-top:0;  border-top: 0;'" : "");?>>
			

		<?php
			// Handle Column count
			if($footer_columns) { ?>
				
				<div class="container">	
					<div class="section">
				
					<?php 
					// Loop Columns
					for($i = 1; $i <= $footer_columns; $i++){ ?>

						<div class="col span_1_of_<?php echo $footer_columns;?>">
						<?php if ( ! dynamic_sidebar('Footer Widget '.$i) ) : ?>			
							<h3 class="widgettitle">Footer Widget Area <?php echo $i;?></h3>
							<p><a href="<?php echo admin_url('widgets.php'); ?>">Assign a widget to this area now.</a></p>	
						<?php endif; // end widget area ?>	
						</div>

					<?php } ?>
				
					</div> 
				</div>
			
			<?php } ?>
			
			<div class="footer_btm" <?php echo (($footer_columns==0) ? " style='margin-top: 0;'" : "");?>>
				<div class="container">
					<div class="footer_btm_inner">
					
					<?php 	if(is_array($footer_icons = ot_get_option('footer_icons'))){
								$footer_icons = array_reverse($footer_icons);							
								foreach($footer_icons as $footer_icon){
									echo "<a target='_blank' class='footer_soc_icon' href='". $footer_icon['icons_url_footer']."'>
											<span class='icon ". $footer_icon['icons_service_footer'] ."' title='". esc_attr($footer_icon['title']) ."'></span>
										  </a>";			
								}
							}
					?>
					
						<div id="powered"><?php echo ot_get_option('copyrights');?></div>
					</div>
				</div>
			</div>
	  </div>
	  <!-- Footer::END -->
<?php } ?>  
	
  
  </div>
  <!-- Page wrapper::END -->
  
  
  <?php wp_footer(); ?>
  
</body>
</html>