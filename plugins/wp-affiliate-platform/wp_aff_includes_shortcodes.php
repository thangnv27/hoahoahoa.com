<?php

function wp_affiliate_leaderboard_handler($args)
{
	extract(shortcode_atts(array(
		'type' => 'commission',
		'number' => '10',
	), $args));	

	$output = "";
	
	$currency_code = get_option('wp_aff_currency');
	if($type == "commission"){
		$leaderboard = get_top_referrer_by_commission_data($number);
	}
	else if($type == "clicks"){
		$leaderboard = get_top_referrer_by_clicks_data($number);
	}

	$output .= '<table class="widefat wp_aff_leaderboard">
	<thead><tr>
	<th scope="col">'.AFF_USERNAME.'</th>
	<th scope="col">'.$type.'</th>
	</tr></thead>
	<tbody>';
	
	if(count($leaderboard)>1){
		foreach($leaderboard as $key => $value){
			$output .= '<tr><td>'.$key.'</td>';
			$output .= '<td>';
			$output .= $leaderboard[$key];
			if($type == "commission"){$output .= ' '.$currency_code;}
			$output .= '</td>';
			$output .= '</tr>';
		}
	}else{
		$output .= '<tr><td colspan="2">No Data Found.</td></tr>';
	}
	$output .= '</tbody></table>';
	return $output;
}

function get_top_referrer_by_clicks_data($number =10,$start_date='',$end_date='')
{
	global $wpdb;
	$affiliates_clicks_tbl = WP_AFF_CLICKS_TBL_NAME;
	$curr_date = (date ("Y-m-d"));
        if(empty($start_date)){
            $start_date = '2008-01-01';//from the beginning
        }
        if(empty($end_date)){
            $end_date = $curr_date;//current date
        }
        
	$resultset = $wpdb->get_results("SELECT * FROM $affiliates_clicks_tbl WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);

	$top_referrers_data = array();
	foreach($resultset as $row){
		if(array_key_exists($row->refid, $top_referrers_data)){
			$current_count = $top_referrers_data[$row->refid];
			$top_referrers_data[$row->refid] = $current_count + 1;
		}else{
			$top_referrers_data[$row->refid] = 1;
		}
	}
	arsort($top_referrers_data);//sort high to low
	$top_referrers = array_slice($top_referrers_data, 0, $number);
	return $top_referrers;
}

function get_top_referrer_by_commission_data($number = 10,$start_date='',$end_date='')
{
	global $wpdb;
	$aff_sales_tbl = WP_AFF_SALES_TBL_NAME;
	
	$curr_date = (date ("Y-m-d"));
	if(empty($start_date)){
		$start_date = '2008-01-01';//from the beginning
	}
	if(empty($end_date)){
		$end_date = $curr_date;
	}
		
	$resultset = $wpdb->get_results("SELECT * FROM $aff_sales_tbl WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);

	$top_referrers_data = array();
	$current_amt = 0;
	foreach($resultset as $row){
		if(array_key_exists($row->refid, $top_referrers_data))
		{
			$current_amt = $top_referrers_data[$row->refid];
			$top_referrers_data[$row->refid] = number_format(($current_amt + $row->payment),2);
		}
		else
		{
			$top_referrers_data[$row->refid] = number_format($row->payment,2);
		}
	}
	arsort($top_referrers_data);//sort high to low
	$top_referrers = array_slice($top_referrers_data, 0, $number);
	return $top_referrers;
}