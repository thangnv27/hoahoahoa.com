<?php

/**
	Custom Google Maps
**/

if( ! function_exists( 'shortcode_boc_gmap' ) ) {

	function shortcode_boc_gmap($atts) {
		
		$atts = vc_map_get_attributes('boc_gmap', $atts );
		extract( $atts );
		
		// Add Google Maps Key
		wp_enqueue_script( 'google-maps-api', '//maps.google.com/maps/api/js?sensor=false&key='.esc_js($api_key) );
		
		$map_id = "map_".rand(0,10000);
		
		if(!$lat){
			$lat = 0;
		}
		
		if(!$lon){
			$lon = 0;
		}
		
		$content = '<div id="' .esc_attr($map_id) . '" style="height:' . esc_attr($height) . 'px;" class="boc_google_map"></div>';


		$getScheme = NULL;

		if(($colorscheme != '') && ($colorscheme != '1')) {
			
			$getScheme = 'styles: ';

			switch ($colorscheme) {
				
				// Default
				case 1:
					$getScheme .= '[]';
					break;
				// Vivid
				case 2:
					$getScheme .= '[{"featureType": "all","elementType": "labels.icon","stylers": [{"visibility": "off"}]},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}]';
					break;
				// Green
				case 3:
					$getScheme .= '[{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}]';
					break;
				// Blue
				case 4:
					$getScheme .= '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]';
					break;
				// Grey
				case 5:
					$getScheme .= '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
					break;
				// Dark Grey
				case 6:
					$getScheme .= '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
					break;
			}
			$getScheme .= ",";
		}

		$content .= '
		<script type="text/javascript">
		jQuery(window).load(function() {
			
			var latlng = new google.maps.LatLng(' . esc_js($lat) . ', ' . esc_js($lon) . ');
			var myOptions = {
				zoom: ' . esc_js($zoom) . ',
				center: latlng,
				scrollwheel: ' . (esc_js($scrollwheel) ? 'true' : 'false') .',
				disableDefaultUI: ' . (esc_js($hidecontrols) ? 'true' : 'false') .',
				scaleControl: ' . (esc_js($scale) ? 'true' : 'false') .',
				panControl: false,
				draggable: !boc_is_mobile,
				streetViewControl: false,
				'. $getScheme .'
				mapTypeId: google.maps.MapTypeId.' . esc_js($maptype) . '
			};
			var ' . esc_js($map_id) . ' = new google.maps.Map(document.getElementById("' . esc_js($map_id) . '"), myOptions);
			';

			// Add address
			if(($address != '') || ($lat))
			{
				if($address != ''){
					$geocoder_address_string = "{ 'address': address}";
				}else {
					$geocoder_address_string = "{ 'location': latlng}";
				}
				$content .= '
				var geocoder_' . esc_js($map_id) . ' = new google.maps.Geocoder();
				var address = \'' . esc_js($address) . '\';
				
				geocoder_' . $map_id . '.geocode( '.$geocoder_address_string.', function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						' . esc_js($map_id) . '.setCenter(results[0].geometry.location);
						';
						
						if ($marker !='')
						{
							// Add custom image
							if ($markerimage !='')
							{
								$image_src = wp_get_attachment_image_src($markerimage, 'full');
								$content .= 'var image = "'. esc_url($image_src[0]) .'";';
							}
							$content .= '
							var marker = new google.maps.Marker({
								map: ' . esc_js($map_id) . ', 
								';
								if ($markerimage !='')
								{
									$content .= 'icon: image,';
								}
							$content .= '
								position: ' . esc_js($map_id) . '.getCenter()
							});
							';

							//infowindow
							if($infowindow != '') 
							{
								//first convert and decode html chars
								$thiscontent = htmlspecialchars_decode(preg_replace( "/\r|\n/", "", $infowindow)); // HTML allowed, no escaping
								$content .= '
								var contentString = \'' . $thiscontent . '\';
								var infowindow = new google.maps.InfoWindow({
									content: contentString
								});
											
								google.maps.event.addListener(marker, \'click\', function() {
								  infowindow.open(' . esc_js($map_id) . ',marker);
								});
								';

								//infowindow default
								if ($infowindowdefault == 'yes')
								{
									$content .= '
										infowindow.open(' . esc_js($map_id) . ',marker);
									';
								}
							}
						}
				$content .= '
					} else {
						alert("Geocode was not successful for the following reason: " + status);
					}
				});
				';
			}

			// Show Marker
			if ($marker != '')
			{
				// Custom image
				if ($markerimage !='')
				{
					$image_src = wp_get_attachment_image_src($markerimage, 'full');
					$content .= 'var image = "'. esc_url($image_src[0]) .'";';
				}

				$content .= '
					var marker = new google.maps.Marker({
					map: ' . esc_js($map_id) . ', 
					';
					if ($markerimage !='')
					{
						$content .= 'icon: image,';
					}
				$content .= '
					position: ' . esc_js($map_id) . '.getCenter()
				});
				';

				// Infowindow
				if($infowindow != '') 
				{
					$content .= '
					var contentString = \'' . esc_js($infowindow) . '\';
					var infowindow = new google.maps.InfoWindow({
						content: contentString
					});
								
					google.maps.event.addListener(marker, \'click\', function() {
					  infowindow.open(' . esc_js($map_id) . ',marker);
					});
					';
					//infowindow default
					if ($infowindowdefault == 'yes')
					{
						$content .= '
							infowindow.open(' . esc_js($map_id) . ',marker);
						';
					}				
				}
			}

			$content .= '});</script>';
			
			return $content;
	}
	
	add_shortcode('boc_gmap', 'shortcode_boc_gmap');
}


// Map Shortcode in Visual Composer
vc_map( array(
		"name"					=> __( "Custom Google Map", 'Fortuna' ),
		"base"					=> "boc_gmap",
		"category" 				=> "Fortuna Shortcodes",
		"icon"					=> "boc_gmaps",
		"weight"				=> 20,
		"params"				=> array(
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> __( "Address", 'Fortuna' ),
				"param_name"	=> "address",
				"description"	=> __( "Insert a valid Google Maps address", 'Fortuna' ),
			),
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> __( "Latitude", 'Fortuna' ),
				"param_name"	=> "lat",
				"value"			=> "",
				"std"			=> "",
				"description"	=> __( "You can use coordinates <strong>instead of</strong> the address field.", 'Fortuna' ),
			),
			array(
				"type"			=> "textfield",
				"admin_label"	=> true,
				"heading"		=> __( "Longitude", 'Fortuna' ),
				"param_name"	=> "lon",
				"value"			=> "",
				"std"			=> "",
				"description"	=> __( "You can use coordinates <strong>instead of</strong> the address field.", 'Fortuna' ),
			),
			array(
				"type"			=> "textfield",
				"admin_label"	=> false,
				"heading"		=> __( "Height", 'Fortuna' ),
				"param_name"	=> "height",
				"value"			=> "400",
				"std"			=> "400",
				"description"	=> __( "Height of the Map in px (Default is 400)", 'Fortuna' ),
			),
			array(
				"type"			=> "textfield",
				"admin_label"	=> false,
				"heading"		=> __( "Google API Key", 'Alea' ),
				"param_name"	=> "api_key",
				"value"			=> "",
				"description"	=> __( "Enter your <a href='https://developers.google.com/maps/documentation/javascript/get-api-key' target='_blank'>Google API key</a>", 'Alea' ),
			),			
			array(
				"type"			=> "textfield",
				"admin_label"	=> false,
				"heading"		=> __( "Zoom Level", 'Fortuna' ),
				"param_name"	=> "zoom",
				"value"			=> "14",
				"std"			=> "14",
				"description"	=> __( "Value between 1-21. The Higher the number - the more zoomed in. (Default is 14)", 'Fortuna' ),
				"group"			=> __( 'Options', 'Fortuna' ),
			),
			array(
				"type"			=> "dropdown",
				"admin_label"	=> false,
				"heading"		=> __( "Map Type", 'Fortuna' ),
				"param_name"	=> "maptype",
				"value"			=> array(
					'Roadmap' 	=> 'ROADMAP',
					'Satellite' => 'SATELLITE',
					'Hybrid' 	=> 'HYBRID',
					'Terrain' 	=> 'TERRAIN',
				),
				"description"	=> __( "Pick a map type", 'Fortuna' ),
				"group"			=> __( 'Options', 'Fortuna' ),
			),
			
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Show Marker", 'Fortuna'),
				"param_name" 	=> "marker",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Show a Marker on your Map?", 'Fortuna'),
				"group"			=> __( 'Marker', 'Fortuna' ),
			),			
			
			array(
				"type"			=> "attach_image",
				"admin_label"	=> false,
				"heading"		=> __( "Marker Image", 'Fortuna' ),
				"param_name"	=> "markerimage",
				"value"			=> "",
				"description"	=> __( "If you want to use a Marker Image you can upload it here.", 'Fortuna' ),
				"group"			=> __( 'Marker', 'Fortuna' ),
				"dependency"	=> Array( 'element'	=> "marker", 'not_empty' => true ),
			),
			array(
				"type"			=> "textarea",
				"admin_label"	=> false,
				"heading"		=> __( "Info Text", 'Fortuna' ),
				"param_name"	=> "infowindow",
				"value"			=> "",
				"description"	=> __( "Text to add to the Infowindow", 'Fortuna' ),
				"group"			=> __( 'Marker', 'Fortuna' ),
				"dependency"	=> Array( 'element'	=> "marker", 'not_empty' => true ),
			),
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __( "Show Info Text", 'Fortuna' ),
				"param_name" 	=> "infowindowdefault",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Show Info Text on Page Load?", 'Fortuna'),
				"group"			=> __( 'Marker', 'Fortuna' ),
				"dependency"	=> Array( 'element'	=> "infowindow", 'not_empty' => true ),
			),
			
			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Scrollwheel Zoom", 'Fortuna'),
				"param_name" 	=> "scrollwheel",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Enable mouse scrollwheel zooming?", 'Fortuna'),
				"group"			=> __( 'Options', 'Fortuna' ),
			),				

			array(
				"type"			=> 'checkbox',
				"heading" 		=> __("Hide Map Controls?", 'Fortuna'),
				"param_name" 	=> "hidecontrols",
				"value"			=> Array(__("Yes", "Fortuna") => 'yes' ),
				"description" 	=> __("Enable mouse scrollwheel zooming?", 'Fortuna'),
				"group"			=> __( 'Options', 'Fortuna' ),
			),				

			array(
				"type"			=> "dropdown",
				"admin_label"	=> false,
				"heading"		=> __( "Map Style", 'Fortuna' ),
				"param_name"	=> "colorscheme",
				"value"			=> array(
					'1 Default Style' 	=> '1',
					'2 (Vivid)' 		=> '2',
					'3 (Green)' 		=> '3',
					'4 (Blue)' 			=> '4',
					'5 (Grey)' 			=> '5',
					'6 (Dark Grey)' 	=> '6',
				),
				"std"			=> "1",
				"description"	=> __( "Pick a Style for your Map", 'Fortuna' ),
				"group"			=> __( 'Options', 'Fortuna' ),
			),
		)
	) );