<?php
include_once('helper_func.php');
include_once('wp_aff_includes1.php');

$affiliates_leads_table_name = $wpdb->prefix . "affiliates_leads_tbl";

function aff_top_leads_menu()
{
	echo wp_aff_misc_admin_css();
	echo '<div class="wrap"><h2>WP Affiliate Platform - Leads Data</h2>';
        
	$action = isset($_GET['action'])?$_GET['action']:'';
	?>
	<h2 class="nav-tab-wrapper">
	<a class="nav-tab <?php echo ($action=='') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=manage_leads">Overall Leads Data</a>
	<a class="nav-tab <?php echo ($action=='affiliate_leads') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=manage_leads&action=affiliate_leads">Individual Affiliate Leads Data</a>
	<a class="nav-tab <?php echo ($action=='top_leads_referrers') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=manage_leads&action=top_leads_referrers">Top Referrer by Leads</a>
        <a class="nav-tab <?php echo ($action == 'export_data') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=manage_leads&action=export_data">Export Leads Data</a>
	</h2>
        <?php
   
	echo '<div id="poststuff"><div id="post-body">';	

        switch ($action)
        {
            case 'affiliate_leads':
                wp_aff_individual_leads_menu();
                break;
            case 'top_leads_referrers':
                wp_aff_show_top_referrer_by_leads_menu();
                break;
            case 'export_data':
                wp_aff_export_leads_data_menu();
                break;              
            default:
                aff_leads_menu();
                break;
        }
	  
   	echo '</div></div>';//end of poststuff
	echo '</div>';//end of wrap
}

function wp_aff_export_leads_data_menu()
{
    if(isset($_REQUEST['wpap_export_leads_data_to_csv'])){
        
        global $wpdb;
        $sales_table = WP_AFF_LEAD_CAPTURE_TBL_NAME;
        $resultset = $wpdb->get_results("SELECT * FROM $sales_table ORDER BY lead_id", OBJECT);
       
        $leads_file_path = dirname(__FILE__) . "/affiliate_leads_data.csv";
        $fp = fopen($leads_file_path, 'w');

        $header_names = array("Row ID", "Email", "Name", "Referrer ID", "Date", "Time", "IP Address", "Reference");
        fputcsv($fp, $header_names);

        foreach ($resultset as $result) {
            $fields = array($result->lead_id, $result->buyer_email, $result->buyer_name, $result->refid, $result->date, $result->time, $result->ipaddress, $result->reference);   
            fputcsv($fp, $fields);
        }

        fclose($fp);    

        $file_url = WP_AFF_PLATFORM_URL . '/affiliate_leads_data.csv';

        $export_message = 'Data exported to <a href="'.$file_url.'" target="_blank">Leads Data File (Right click on this link and choose "Save As" to save the file to your computer)</a>';
    	echo '<div id="message" class="updated fade"><p>';
    	echo $export_message;
    	echo '</p></div>';
        
    }
    
    echo '<div class="postbox">
	<h3><label for="title">Export Leads Data to CSV File</label></h3>
	<div class="inside">';

    ?>
    <form method="post" action="">
    <input type="submit" class="button" name="wpap_export_leads_data_to_csv" value="Export Data to a CSV File" />
    <p class="description">Use this to export all leads data to a CSV file (comma separated).</p>
    </form>
    <?php

    echo "</div></div>";
}


function wp_aff_individual_leads_menu()
{
    echo "<br /><br />";
    echo '<div class="postbox">
    <h3><label for="title">Individual Affiliate Leads Data</label></h3>
    <div class="inside">';		
    wpap_show_date_form_with_affiliate_id_field_new();	
    echo "</div></div>";
	
    if (isset($_POST['info_update']))
    {
    	$affiliate_id = $_POST['wp_aff_referrer'];
    	if(empty($affiliate_id))
    	{
            echo '<div id="message" class="error fade"><br />Error! You must specify an Affiliate ID<br /><br /></div>';
            return;
    	}
    	
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];		        	
        $curr_date = (date ("Y-m-d"));
        if(empty($start_date)){
            $start_date = '2008-01-01';//from the beginning
        }
        if(empty($end_date)){
            $end_date = $curr_date;
        }
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Displaying Leads Data Between '.$start_date.' And '. $end_date. ' For Affiliate: '.$affiliate_id;
        echo '</strong></p></div>';
        		
        global $wpdb;
        $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
        $resultset = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name WHERE (refid='$affiliate_id') AND (date BETWEEN '$start_date' AND '$end_date')", OBJECT);

        if(is_array($resultset)){
            $total_leads = count($resultset);//mysql_num_rows($resultset);
            echo '<p>Total Leads: '.$total_leads.'</p>';
        }

        wp_aff_display_leads_data($resultset,'');
    }	
}

function wp_aff_show_top_referrer_by_leads_menu()
{
	echo "<br /><br />";
	echo '<div class="postbox">
	<h3><label for="title">Top Referrers</label></h3>
	<div class="inside">';
	wpap_show_date_form_fields_new();	
	echo "</div></div>";
	
        if (isset($_POST['info_update']))
        {   	
            $start_date = (string)$_POST["start_date"];
            $end_date = (string)$_POST["end_date"];
            $data_range_msg = 'Displaying Top Referrer Data Between '.$start_date.' And '. $end_date;
	}
	else{
            $curr_date = (date ("Y-m-d"));
            $start_date = '2008-01-01';//from the beginning
            $end_date = $curr_date;
            $data_range_msg = 'Displaying All Time Top Referrer Data';
	}
	
	echo '<div class="wp_affiliate_yellow_box"><p><strong>';
	echo $data_range_msg;
	echo '</strong></p></div>';
	
	global $wpdb;
        $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
	$resultset = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
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
	$count = 0;
	$number_of_top_referrer = 50;
	echo '<table class="widefat">
	<thead><tr>
	<th scope="col">Affiliate ID</th>
	<th scope="col">Lead Count</th>
	</tr></thead>
	<tbody>';
	
	if(count($top_referrers_data)>1){
		foreach($top_referrers_data as $key => $value){
			if($count >= $number_of_top_referrer){break;}
			echo '<tr><td>'.$key.'</td><td>'.$top_referrers_data[$key].'</td></tr>';
			$count ++;
		}
	}else{
		echo '<tr><td colspan="2">No Lead Data Found.</td></tr>';
	}
	echo '</tbody></table>';
}

function aff_leads_menu()
{
	echo '<div class="wp_affiliate_grey_box">';
	echo '<p>Please read the lead capture documentation before using this feature</p>';
	echo '&raquo; <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=215" target="_blank">Capturing Lead Using Contact Form 7 Plugin</a><br /><br />';
	echo '&raquo; <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/?p=385" target="_blank">Capturing Lead Using Gravity Forms Plugin</a><br /><br />';
	echo '</div>';
        
	global $affiliates_leads_table_name;
	global $wpdb;
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

	if(isset($_POST['Award']))
	{		
		?>			
		<div class="postbox">
		<div style="border:1px solid #CC0000;">
		<h3 style="background: #FFDED9; border-bottom:1px solid #CC0000;"><label for="title">Award Commission (Finalize the commission awarding data below)</label></h3>
		<div class="inside">	
		Please enter the sale Amount and hit the "Award Commission" button. Commission will be calculated based on this sale amount and awarded to the appropriate affiliate.	
		<form name="award_comm" method=post action="admin.php?page=manage_leads"> 
		<br />Sale Amount: <input type="text" name="sale_amt" value=" "> <br />
		<input type="hidden" name="lead_id" value="<?php echo $_POST['lead_id']; ?>">
		<br /><input type="submit" value="Award Commission" name="award_commission">
		</form> 
		</div></div></div>		
		<?php
	}
    if(isset($_POST['award_commission']))
    {
    	$sale_amt = $_POST['sale_amt'];
    	$lead_id = $_POST['lead_id'];
		$aff_leads = $wpdb->get_row("SELECT * FROM $affiliates_leads_table_name where lead_id='$lead_id'", OBJECT);  		
    	wp_aff_award_commission($aff_leads->refid,$sale_amt,'',$aff_leads->reference,$aff_leads->buyer_email,$aff_leads->ipaddress,'',$aff_leads->buyer_name);
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo "Commission awarded to referrer: ".$aff_leads->refid;
	    echo '</strong></p></div>';    	
    }
    if(isset($_POST['lead_settings_submit'])){
    	$wp_aff_platform_config->setValue('wp_aff_show_leads_to_affiliates', ($_POST['wp_aff_show_leads_to_affiliates']=='1') ? '1':'' );
    	$wp_aff_platform_config->saveConfig();
		echo '<div id="message" class="updated fade"><p>';
	    echo "Settings saved!";
	    echo '</p></div>';      	
    }

    wp_aff_leads_settings();
    wp_aff_add_leads_data();

    wpap_show_date_form_fields_new();

    if(isset($_POST['Delete']))
    {
        if(wp_aff_delete_leads_data($_POST['lead_id']))
        {
            $message = "Record successfully deleted";
        }
        else
        {
            $message = "Could not delete the entry. Please check and make sure the Lead ID field is unique and has a value";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
	    echo $message;
	    echo '</strong></p></div>';
    }
    
	if (isset($_POST['Submit']))
	{
		if (!empty($_POST['refid']))
		{
			$referrer = $_POST['refid'];
			if (empty($_POST['date']))
	        	$clientdate = (date ("Y-m-d"));
	        else
	        	$clientdate = $_POST['date'];
	        	
			if (empty($_POST['time']))
	        	$clienttime	= (date ("H:i:s"));	
	        else
	        	$clienttime = $_POST['time'];  
       
	    	$buyer_email = $_POST['buyer_email'];
	    	$buyer_name = $_POST['buyer_name'];
	    	$reference = $_POST['reference'];
	    	$ipaddress = $_POST['ipaddress'];
	      	$updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress,buyer_name) VALUES ('$buyer_email','$referrer','$reference','$clientdate','$clienttime','$ipaddress','$buyer_name')";
			$results = $wpdb->query($updatedb);				
		}
	}
		
	$msg = '';
    if (isset($_POST['info_update']))
    {
    	$start_date = (string)$_POST["start_date"];
    	$end_date = (string)$_POST["end_date"];
        $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
        $msg .= 'Displaying Leads Data Between '.$start_date.' and '. $end_date;
        $msg .= '</strong></p></div>';		        	
		$curr_date = (date ("Y-m-d"));				
		$wp_aff_leads = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
	}
	if ($msg == '')
	{
	    $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
	    $msg .= 'Displaying 20 Recent Leads Below';
	    $msg .= '</strong></p></div>';	   			
		$wp_aff_leads = $wpdb->get_results("SELECT * FROM $affiliates_leads_table_name ORDER BY date DESC LIMIT 20", OBJECT);	    	
	}
	wp_aff_display_leads_data($wp_aff_leads,$msg);
}

function wp_aff_leads_settings()
{
	$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
	?>
	<div class="postbox">
	<h3><label for="title">Settings</label></h3>
	<div class="inside">

	<br />
	<form method="post" action="">
	<input name="wp_aff_show_leads_to_affiliates" type="checkbox"<?php if($wp_aff_platform_config->getValue('wp_aff_show_leads_to_affiliates')!='') echo ' checked="checked"'; ?> value="1"/>
	Show leads/conversions data to affiliates
	<br /><i>Check this option if you want to show the lead conversion data to your affiliates. The leads data will be shown under the <code>Referrals</code> menu in their affiliate area.</i>
	<p class="submit"><input type="submit" name="lead_settings_submit" class="button-primary" value="Save" /></p>
	</form>
	
	</div></div>	
	<?php	
}

function wp_aff_display_leads_data($wp_aff_leads,$msg)
{  
	echo $msg;   
	echo '
		<table class="widefat">
		<thead><tr>
		<th scope="col">'.__('Lead ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Email', 'wp_affiliate').'</th>
		<th scope="col">'.__('Name', 'wp_affiliate').'</th>
		<th scope="col">'.__('Referrer ID', 'wp_affiliate').'</th>
		<th scope="col">'.__('Reference', 'wp_affiliate').'</th>
		<th scope="col">'.__('Date', 'wp_affiliate').'</th>
		<th scope="col">'.__('Time', 'wp_affiliate').'</th>
		<th scope="col">'.__('IP Address', 'wp_affiliate').'</th>
		<th scope="col">'.__('Award Commission', 'wp_affiliate').'</th>
        <th scope="col">'.__('Delete Entry', 'wp_affiliate').'</th>
		</tr></thead>
		<tbody>';
			
	if ($wp_aff_leads)
	{
		foreach ($wp_aff_leads as $wp_aff_leads)
		{
			echo '<tr>';
			echo '<td><strong>'.$wp_aff_leads->lead_id.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->buyer_email.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->buyer_name.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->refid.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->reference.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->date.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->time.'</strong></td>';
			echo '<td><strong>'.$wp_aff_leads->ipaddress.'</strong></td>';
			
			echo "<td>";
			?>	
			<form name="convert" method=post action="admin.php?page=manage_leads"> 
			<input type="hidden" name="lead_id" value="<?php echo $wp_aff_leads->lead_id; ?>">
			<input type="submit" value="Award" name="Award">
			</form> 			
			<?php			
			echo "</td>";
			
            echo "<td>";
			echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";
            echo "<input type=\"hidden\" name=\"lead_id\" value=".$wp_aff_leads->lead_id." />";
            echo "<input type=\"submit\" value=\"Delete\" name=\"Delete\">";
            echo "</form>";
            echo "</td>";

			echo '</tr>';					
		}	
	}
	else
	{
		echo '<tr> <td colspan="9">'.__('No Leads Data Found.', 'wp_affiliate').'</td> </tr>';
	}		
	echo '</tbody></table>';
}

function wp_aff_add_leads_data()
{
	?>
	<div class="postbox">
	<h3><label for="title">Add a Lead Manually</label></h3>
	<div class="inside">

	<form method="post" action="">
	<table width="960">
    
	<thead><tr>
	<th align="left"><strong>Email</strong></th>
	<th align="left"><strong>Name</strong></th>
	<th align="left"><strong>Referrer ID</strong></th>
	<th align="left"><strong>Reference</strong></th>
	<th align="left"><strong>Date (yyyy-mm-dd)</strong></th>
	<th align="left"><strong>Time (hh:mm:ss)</strong></th>
	<th align="left"><strong>IP Address</strong></th>
	</tr></thead>
	<tbody>
	
	<tr>
	<td width="160"><input name="buyer_email" type="text" id="buyer_email" value="" size="20" /></td>
	<td width="160"><input name="buyer_name" type="text" id="buyer_name" value="" size="20" /></td>
    <td width="160"><input name="refid" type="text" id="refid" value="" size="10" /></td>
    <td width="160"><input name="reference" type="text" id="reference" value="" size="4" /></td>    
    <td width="150"><input name="date" type="text" id="date" value="" size="10" /></td>
    <td width="160"><input name="time" type="text" id="time" value="" size="10" /></td>
    <td width="160"><input name="ipaddress" type="text" id="ipaddress" value="" size="12" /></td>
	<td><input type="submit" name="Submit" class="button" value="Add Lead" /></td>
        </tr>	
		
	<tr><td colspan="7"><i>Tip: Leave the Date and Time field empty to use current Date and Time.</i></td></tr>	
	</tbody>
	</table>
	</form>
	</div></div>	
	<?php
}

function wp_aff_delete_leads_data($lead_id)
{
    global $wpdb;
    global $affiliates_leads_table_name;
    $updatedb = "DELETE FROM $affiliates_leads_table_name WHERE lead_id='$lead_id'";
    $results = $wpdb->query($updatedb);
    if($results>0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
