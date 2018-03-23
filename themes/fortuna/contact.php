<?php
/**
 * Template Name: Contact Page
 *
 * A Fully Operational Contact Page
 * @package WordPress
 */

get_header();

	if(ot_get_option('gmaps_address')): ?>
	
	<script src="https://maps.google.com/maps/api/js?sensor=false&key=<?php echo ot_get_option('gmaps_API_key'); ?>" type="text/javascript"></script>
	<style>
	#map_canvas .gm-style-cc {
		line-height: 1.2em;
	}
	</style>
	<div>

				<div id="map_canvas" style="width:100%; height: 560px;"></div>

				<script type="text/javascript">
				
					var geocoder;
					var map;
					var address = '<?php echo esc_attr(ot_get_option('gmaps_address')); ?>';
					function initialize() {
						geocoder = new google.maps.Geocoder();					
						var myOptions = {
							zoom: <?php echo esc_attr(ot_get_option('gmaps_zoom',14)); ?>,
							scrollwheel: false,
							draggable: !boc_is_mobile,
							styles: 
							
					<?php 	
							

								switch (ot_get_option('gmaps_style','1')) {
									// Default
									case 1:
										echo '[]';
										break;
									// Vivid
									case 2:
										echo '[{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}]';
										break;
									// Green
									case 3:
										echo '[{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}]';
										break;
									// Blue
									case 4:
										echo '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
										break;
									// Grey
									case 5:
										echo '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]';
										break;
									// Dark Grey
									case 6:
										echo '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
										break;
									}
						?>	,
							mapTypeControl: true,
							mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
							navigationControl: true,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
						if (geocoder) {
						  geocoder.geocode( { 'address': address}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
							  if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
							  map.setCenter(results[0].geometry.location);

								var infowindow = new google.maps.InfoWindow(
									{ content: '<b>'+address+'</b>',
									  size: new google.maps.Size(150,50)
									});

								var marker = new google.maps.Marker({
									position: results[0].geometry.location,
									map: map,
									title:address,
									title: 'Location',
									icon: '<?php 
										$custom_marker = ot_get_option('custom_marker_upload');
										if($custom_marker) {
											echo $custom_marker;
										}else {
											echo get_template_directory_uri().'/images/custom_marker1.png';
										}?>',
									animation: google.maps.Animation.DROP

								}); 
								google.maps.event.addListener(marker, 'click', function() {
									infowindow.open(map,marker);
								});

							  } else {
								alert("No results found");
							  }
							} else {
							  alert("Geocode was not successful for the following reason: " + status);
							}
						  });
						}
					}

					jQuery( document ).ready(function() {
						initialize();
					});

				</script>
	</div>
	<?php endif; ?>

	<?php
		// Check Sidebar Layout
		$sidebar_layout = boc_page_sidebar_layout();
		$boc_content_top_margin = (get_post_meta($post->ID, 'boc_content_top_margin', true)!=='off'? true : false);
		if($boc_content_top_margin){
			echo '<div class="h60"></div>';
		}
	?>

	<div class="contact_page_template container <?php echo (($sidebar_layout == 'left-sidebar') ? "has_left_sidebar" : (($sidebar_layout == 'right-sidebar') ? "has_right_sidebar" : "")); ?>">

	  <div class="section">

		<?php
			// IF Sidebar Left
			if($sidebar_layout == 'left-sidebar'){
				get_sidebar();
			}

			if($sidebar_layout != 'full-width'){
				echo "<div class='post_content col span_3_of_4'>";
			}else {
				echo "<div class='post_content'>";
			}
			?>

			<?php while (have_posts()) : the_post(); ?>
			<?php the_content() ?>
			<?php endwhile; ?>

			<?php
			// Close "post_content"
			echo "</div>";

			// IF Sidebar Right
			if($sidebar_layout == 'right-sidebar'){
				get_sidebar();
			}
		?>
     </div>
  </div>

<?php get_footer(); ?>