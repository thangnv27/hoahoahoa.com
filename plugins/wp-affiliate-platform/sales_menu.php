<?php
include_once('helper_func.php');
include_once('wp_aff_includes1.php');

$sales_table = $wpdb->prefix . "affiliates_sales_tbl";

function aff_top_sales_menu() {
    echo '<div class="wrap"><h2>WP Affiliate Platform - Sales Data</h2>';

    echo wp_aff_misc_admin_css();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    ?>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab <?php echo ($action == '') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=aff_sales">Overall Commission Data</a>
        <a class="nav-tab <?php echo ($action == 'affiliate') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=aff_sales&action=affiliate">Individual Affiliate Commission</a>
        <a class="nav-tab <?php echo ($action == 'top_referrer_c') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=aff_sales&action=top_referrer_c">Top Referrer by Commission</a>
        <a class="nav-tab <?php echo ($action == 'export_data') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=aff_sales&action=export_data">Export Data</a>
    </h2>
    <?php
    echo '<div id="poststuff"><div id="post-body">';
    switch ($action) {
        case 'affiliate':
            aff_individual_aff_commission_details();
            break;
        case 'top_referrer_c':
            wp_aff_show_top_referrers_in_sales_menu();
            break;
        case 'export_data':
            wp_aff_export_sales_data_menu();
            break;
        default:
            aff_sales_menu();
            break;
    }

    echo '</div></div>';
    echo '</div>';
}

function wp_aff_export_sales_data_menu()
{
    if(isset($_REQUEST['wpap_export_commission_data_to_csv'])){
        
        global $wpdb;
        $sales_table = WP_AFF_SALES_TBL_NAME;
        $resultset = $wpdb->get_results("SELECT * FROM $sales_table ORDER BY date DESC", OBJECT);
       
        //Create the CSV file with commission details
        if (WP_AFFILIATE_ENABLE_UTF_8_ENCODING === '1') {
            $separator = "\t";
        } else {
            $separator = ", ";
        }
        $csv_output = "";
        $csv_output.= "Affiliate ID" . $separator;
        $csv_output.= "Commission Amount" . $separator;
        $csv_output.= "Sale Amount" . $separator;
        $csv_output.= "Sale Date" . $separator;
        $csv_output.= "Sale Time" . $separator;
        $csv_output.= "Buyer Email" . $separator;
        $csv_output.= "Item ID" . $separator;
        $csv_output.= "Transaction ID" . $separator;
        $csv_output.= "\n";

        foreach ($resultset as $row){
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->refid)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(round($row->payment, 2)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->sale_amount)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->date)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->time)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->buyer_email)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->item_id)) . $separator;
            $csv_output.= wp_aff_escape_csv_value(stripslashes($row->txn_id)) . $separator;
            $csv_output.= "\n";
        }
        if (WP_AFFILIATE_ENABLE_UTF_8_ENCODING === '1') {
            $csv_output = chr(255) . chr(254) . mb_convert_encoding($csv_output, 'UTF-16LE', 'UTF-8');
        }

        $csv_file_abs_path = WP_AFF_PLATFORM_PATH . 'affiliate_commission_data.csv';
        $Handle = fopen($csv_file_abs_path, 'w') or die("can't open file named 'affiliate_commission_data.csv'");
        fwrite($Handle, $csv_output);
        fclose($Handle);
    	
        $file_url = WP_AFF_PLATFORM_URL . '/affiliate_commission_data.csv';
        $export_message = 'Data exported to <a href="'.$file_url.'" target="_blank">Commission Data File (Right click on this link and choose "Save As" to save the file to your computer)</a>';
    	echo '<div id="message" class="updated fade"><p>';
    	echo $export_message;
    	echo '</p></div>';   
        
    }
    
    echo '<div class="postbox">
	<h3><label for="title">Export Commission Data to CSV File</label></h3>
	<div class="inside">';

    ?>
    <form method="post" action="">
    <input type="submit" class="button" name="wpap_export_commission_data_to_csv" value="Export Data to a CSV File" />
    <p class="description">Use this to export all commission data to a CSV file (comma separated).</p>
    </form>
    <?php

    echo "</div></div>";
}

function wp_aff_show_top_referrers_in_sales_menu() {
    echo "<br /><br />";
    echo '<div class="postbox">
	<h3><label for="title">Top Referrers by Commission</label></h3>
	<div class="inside">';
    wpap_show_date_form_fields_new();
    echo "</div></div>";

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        $data_range_msg = 'Displaying Top Referrer Data Between ' . $start_date . ' And ' . $end_date;
    } else {
        $curr_date = (date("Y-m-d"));
        $start_date = '2008-01-01'; //from the beginning
        $end_date = $curr_date;
        $data_range_msg = 'Displaying All Time Top Referrer by Commission Data';
    }

    echo '<div class="wp_affiliate_yellow_box"><p><strong>';
    echo $data_range_msg;
    echo '</strong></p></div>';

    wp_aff_stats_show_top_referrers_by_commission($start_date, $end_date);
}

function aff_individual_aff_commission_details() {
    echo "<h2>Individual Affiliate Commission Data</h2>";
    ?>
    <br />
    <div class="postbox">
        <h3><label for="title">Find Commission Details for a Particular Affiliate</label></h3>
        <div class="inside">
            <strong>Enter the Affiliate ID</strong>
            <br /><br />
            <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                <input type="hidden" name="info_update" id="info_update" value="true" />

                <input name="wp_aff_referrer" type="text" size="30" value=""/>
                <div class="submit">
                    <input type="submit" name="info_update" class="button" value="Display Data &raquo;" />
                </div>   
            </form> 
        </div></div>
    <?php
    if (isset($_POST['Delete'])) {
        if (wp_aff_delete_sales_data($_POST['txn_id'])) {
            $message = "Record successfully deleted";
        } else {
            $message = "Could not delete the entry. Please check and make sure the Transaction ID field is unique and has a value";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
        echo $message;
        echo '</strong></p></div>';
    }
    if (isset($_POST['info_update'])) {
        $searched_aff_id = (string) $_POST["wp_aff_referrer"];
        update_option('wp_aff_referrer_details', (string) $_POST["wp_aff_referrer"]);
        display_affiliate_commission_data($searched_aff_id);
    } else {
        $aff_ref = get_option('wp_aff_referrer_details');
        if (!empty($aff_ref)) {
            display_affiliate_commission_data($aff_ref);
        }
    }
}

function display_affiliate_commission_data($searched_aff_id) {
    global $wpdb;
    $sales_table = WP_AFF_SALES_TBL_NAME;
    $payouts_table = WP_AFF_PAYOUTS_TBL_NAME;

    $wp_aff_sales = $wpdb->get_results("SELECT * FROM $sales_table WHERE refid = '$searched_aff_id'", OBJECT);
    $sales_data_query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE refid = '$searched_aff_id' and payment >= 0", OBJECT);
    $total_sales_count = $sales_data_query->total_record;
    $refund_data_query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE refid = '$searched_aff_id' and payment < 0", OBJECT);
    $total_refund_count = $refund_data_query->total_record;

    $total_payout_amt = 0;
    $wp_aff_payouts = $wpdb->get_results("SELECT * FROM $payouts_table WHERE refid = '$searched_aff_id'", OBJECT);
    if ($wp_aff_payouts) {
        foreach ($wp_aff_payouts as $payout_row) {
            $total_payout_amt += $payout_row->payout_payment;
        }
    }

    $msg = '<strong>Total number of sales referred by <span style="color:green;">' . $searched_aff_id . '</span>: ' . $total_sales_count . '</strong><br />';
    if ($total_refund_count > 0) {
        $msg .= '<strong>Total number of refunds: ' . $total_refund_count . '</strong><br />';
    }
    $msg .= '<strong>Total payout made to <span style="color:green;">' . $searched_aff_id . '</span> so far: ' . $total_payout_amt . '</strong><br />';
    $msg .= '<br />';

    wp_aff_display_sales_data($wp_aff_sales, $msg);
}

function aff_sales_menu() {
    echo "<h2>Overall Commission Data</h2>";

    global $wpdb;
    $sales_table = WP_AFF_SALES_TBL_NAME;

    wp_aff_add_sales_data();

    wpap_show_date_form_fields_new();

    if (isset($_POST['Delete'])) {
        if (wp_aff_delete_sales_data($_POST['txn_id'])) {
            $message = "Record successfully deleted";
        } else {
            $message = "Could not delete the entry. Please check and make sure the Transaction ID field is unique and has a value";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
        echo $message;
        echo '</strong></p></div>';
    }
    if (isset($_POST['add_commission_manual'])) {
        $message = "";
        if (!empty($_POST['user_id'])) {
            $referrer = $_POST['user_id'];
            if (empty($_POST['sale_date']))
                $clientdate = (date("Y-m-d"));
            else
                $clientdate = $_POST['sale_date'];

            if (empty($_POST['sale_time']))
                $clienttime = (date("H:i:s"));
            else
                $clienttime = $_POST['sale_time'];

            $txn_id = $_POST['txn_id'];
            if (empty($txn_id)) {
                $txn_id = uniqid();
            }
            $commission_amount = number_format($_POST['comm_amount'], 2, '.', '');
            $sale_amount = number_format($_POST['sale_amount'], 2, '.', '');
            $item_id = $_POST['item_id'];
            $buyer_email = $_POST['buyer_email'];
            $buyer_name = '';
            $updatedb = "INSERT INTO $sales_table (refid,date,time,browser,ipaddress,payment,sale_amount,txn_id,item_id,buyer_email) VALUES ('$referrer','$clientdate','$clienttime','','','$commission_amount','$sale_amount','$txn_id','$item_id','$buyer_email')";
            $results = $wpdb->query($updatedb);

            //Do any necessary 2nd tier commission awarding
            $wp_aff_referrer_record = wp_aff_get_affiliate_record_from_db($referrer);
            wp_aff_award_second_tier_commission($wp_aff_referrer_record, $sale_amount, $txn_id, $item_id, $buyer_email, $buyer_name);

            $message .= "Manual commission has been added.";
            if (isset($_POST['send_email'])) {
                $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
                $resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$referrer'", OBJECT);
                $affiliate_email = $resultset->email;
                wp_aff_send_commission_notification($affiliate_email, $txn_id);
            }
        } else {
            $message .= "You must enter an affiliate ID to add a manual commission!";
        }
        echo '<div id="message" class="updated fade"><p>';
        echo $message;
        echo '</p></div>';
    }

    $msg = '';
    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
        $msg .= 'Displaying Sales History Between ' . $start_date . ' And ' . $end_date;
        $msg .= '</strong></p></div>';
        $curr_date = (date("Y-m-d"));
        $wp_aff_sales = $wpdb->get_results("SELECT * FROM $sales_table WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    }
    if ($msg == '') {
        $msg .= '<div class="wp_affiliate_yellow_box"><p><strong>';
        $msg .= 'Displaying 20 Recent Sales Data Below';
        $msg .= '</strong></p></div>';
        $wp_aff_sales = $wpdb->get_results("SELECT * FROM $sales_table ORDER BY date DESC LIMIT 20", OBJECT);
    }
    wp_aff_display_sales_data($wp_aff_sales, $msg);
}

function wp_aff_display_sales_data($wp_aff_sales, $msg) {
    echo $msg;

    echo '<table class="widefat">
		<thead><tr>
		<th scope="col">' . __('Affiliate ID', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Commission Amount', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Sale Amount', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Sale Date', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Sale Time', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Buyer Email', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Item ID', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Unique Transaction ID', 'wp_affiliate') . '</th>';
    if (get_option('wp_aff_enable_clicks_custom_field') != '') {
        echo '<th scope="col">' . __('Custom Value', 'wp_affiliate') . '</th>';
    }
    echo '<th scope="col">' . __('Delete Entry', 'wp_affiliate') . '</th>
		</tr></thead>
		<tbody>';

    if ($wp_aff_sales) {
        foreach ($wp_aff_sales as $wp_aff_sales) {
            echo '<tr>';
            echo '<td><strong>' . $wp_aff_sales->refid . '</strong></td>';
            $commission_amt = number_format($wp_aff_sales->payment, 2, '.', '');
            if (!empty($wp_aff_sales->is_tier_comm) && $wp_aff_sales->is_tier_comm == "yes") {
                echo '<td><strong>' . $commission_amt . ' (Tier Commission)</strong></td>';
            } else {
                echo '<td><strong>' . $commission_amt . '</strong></td>';
            }
            echo '<td><strong>' . $wp_aff_sales->sale_amount . '</strong></td>';
            echo '<td><strong>' . $wp_aff_sales->date . '</strong></td>';
            echo '<td><strong>' . $wp_aff_sales->time . '</strong></td>';
            echo '<td><strong>' . $wp_aff_sales->buyer_email . '</strong></td>';
            echo '<td><strong>' . $wp_aff_sales->item_id . '</strong></td>';
            echo '<td><strong>' . $wp_aff_sales->txn_id . '</strong></td>';
            if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                echo '<td><strong>' . $wp_aff_sales->campaign_id . '</strong></td>';
            }

            echo "<td>";
            echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";
            echo "<input type=\"hidden\" name=\"txn_id\" value=" . $wp_aff_sales->txn_id . " />";
            echo "<input type=\"submit\" value=\"Delete\" name=\"Delete\">";
            echo "</form>";
            echo "</td>";

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8">No Sale Data Found</td></tr>';
    }
    echo '</tbody></table>';
}

function wp_aff_add_sales_data() {
    $send_comm_notification = get_option('wp_aff_notify_affiliate_for_commission');
    ?>
    <div class="postbox">
        <h3><label for="title">Manual Commission Award</label></h3>
        <div class="inside">

            <form method="post" action="">
                <table width="850">

                    <thead><tr>
                            <th align="left">Affiliate ID</th>
                            <th align="left">Commission Amount</th>
                            <th align="left">Sale Amount</th>
                            <th align="left">Date (yyyy-mm-dd)</th>
                            <th align="left">Time (hh:mm:ss)</th>
                            <th align="left">Buyer Email</th>
                            <th align="left">Item ID</th>
                            <th align="left">Unique Transaction ID</th>
    <?php if ($send_comm_notification) { ?>
                                <th align="left">Send Notification Email</th>
    <?php } ?>
                        </tr></thead>
                    <tbody>

                        <tr>
                            <td width="160"><input name="user_id" type="text" id="user_id" value="" size="10" /></td>
                            <td width="160"><input name="comm_amount" type="text" id="comm_amount" value="" size="5" /></td>
                            <td width="160"><input name="sale_amount" type="text" id="sale_amount" value="" size="5" /></td>
                            <td width="150"><input name="sale_date" type="text" id="sale_date" value="" size="10" /></td>
                            <td width="160"><input name="sale_time" type="text" id="sale_time" value="" size="10" /></td>
                            <td width="160"><input name="buyer_email" type="text" id="buyer_email" value="" size="25" /></td>
                            <td width="160"><input name="item_id" type="text" id="item_id" value="" size="4" /></td>
                            <td width="160"><input name="txn_id" type="text" id="txn_id" value="" size="10" /></td>
    <?php if ($send_comm_notification) { ?>
                                <td width="160"><input name="send_email" type="checkbox" value="" /></td>
    <?php } ?>
                            <td>
                                <p class="submit"><input type="submit" class="button primary" name="add_commission_manual" value="Add" /></p>
                            </td></tr>	

                        <tr><td colspan="7"><i><strong>Tip:</strong> Leave the Date and Time field empty to use current Date and Time. You can leave the Transaction ID field empty to automatically generate a Unique ID for this field too.</i></td></tr>
                    </tbody>
                </table>
            </form>
        </div></div>	
    <?php
}

function wp_aff_delete_sales_data($txn_id) {
    global $wpdb;
    $sales_table = WP_AFF_SALES_TBL_NAME;
    $updatedb = "DELETE FROM $sales_table WHERE txn_id='$txn_id'";
    $results = $wpdb->query($updatedb);
    if ($results > 0) {
        return true;
    } else {
        return false;
    }
}
