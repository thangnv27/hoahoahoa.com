<?php
include_once('helper_func.php');
include_once('wp_aff_includes1.php');

$payouts_table = $wpdb->prefix . "affiliates_payouts_tbl";

function payouts_history_menu() {
    echo '<div class="wrap"><h2>WP Affiliate Platform - Payouts History</h2>';
    echo wp_aff_misc_admin_css();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    ?>
    <h2 class="nav-tab-wrapper">
        <a class="nav-tab <?php echo ($action == '') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=payouts_history">Overall Payout Data</a>
        <a class="nav-tab <?php echo ($action == 'individual_payouts') ? 'nav-tab-active' : ''; ?>" href="admin.php?page=payouts_history&action=individual_payouts">Individual Affiliate Payouts</a>
    </h2>
    <?php
    echo '<div id="poststuff"><div id="post-body">';
    switch ($action) {
        case 'individual_payouts':
            aff_individual_aff_payouts_details();
            break;
        default:
            wp_aff_all_payout_history();
            break;
    }

    echo '</div></div>';
    echo '</div>';//End of wrap
}

function wp_aff_all_payout_history() {   
    echo '<div class="postbox">
	<h3><label for="title">Affiliate Payouts History</label></h3>
	<div class="inside">';

    wpap_show_date_form_fields_new();
    echo '</div></div>';

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Displaying Payouts History Between ' . $start_date . ' And ' . $end_date;
        echo '</strong></p></div>';

        $curr_date = (date("Y-m-d"));

        global $wpdb;
        $payouts_table = WP_AFF_PAYOUTS_TBL_NAME;

        echo '
		<table class="widefat">
		<thead><tr>
		<th scope="col">' . __('Affiliate ID', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Payout Amount', 'wp_affiliate') . '</th>
		<th scope="col">' . __('Date Paid', 'wp_affiliate') . '</th>
		</tr></thead>
		<tbody>';

        $wp_aff_payouts = $wpdb->get_results("SELECT * FROM $payouts_table WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
        if ($wp_aff_payouts) {
            foreach ($wp_aff_payouts as $wp_aff_payouts) {
                echo '<tr>';
                echo '<td><strong>' . $wp_aff_payouts->refid . '</strong></td>';
                echo '<td><strong>' . $wp_aff_payouts->payout_payment . '</strong></td>';
                echo '<td><strong>' . $wp_aff_payouts->date . '</strong></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr> <td colspan="4">' . __('No Payouts Data Found.', 'wp_affiliate') . '</td> </tr>';
        }
        echo '</tbody></table>';
    }
}

function aff_individual_aff_payouts_details() {
    echo "<h2>Individual Affiliate Payout Data</h2>";
    global $wpdb;
    $payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
    ?>
    <br />
    <div class="postbox">
        <h3><label for="title">Find Payout Details for a Particular Affiliate</label></h3>
        <div class="inside">
            <strong>Enter the Affiliate ID</strong>
            <br /><br />
            <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

                <input name="wp_aff_referrer" type="text" size="30" value=""/>
                <div class="submit">
                    <input type="submit" name="show_affiliate_payout" value="Display Data &raquo;" class="button" />
                </div>   
            </form> 
        </div></div>
    <?php
    if (isset($_POST['delete_payout'])) {
        $ref_id = $_POST['payout_ref_id'];
        $amt = $_POST['payout_amt'];
        $date = $_POST['payout_date'];
        $del_payout = "DELETE FROM $payouts_table WHERE refid='$ref_id' AND date='$date' AND payout_payment='$amt'";
        $results = $wpdb->query($del_payout);
        if ($results > 0) {
            $message = "Record successfully deleted";
        } else {
            $message = "Could not delete the entry";
        }
        echo '<div id="message" class="updated fade"><p><strong>';
        echo $message;
        echo '</strong></p></div>';
    }
    if (isset($_POST['show_affiliate_payout'])) {
        $searched_aff_id = (string) $_POST["wp_aff_referrer"];
        update_option('wp_aff_payout_referrer_details', (string) $_POST["wp_aff_referrer"]);
        display_affiliate_payout_data($searched_aff_id);
    } else {
        $aff_ref = get_option('wp_aff_payout_referrer_details');
        if (!empty($aff_ref)) {
            display_affiliate_payout_data($aff_ref);
        }
    }
}

function display_affiliate_payout_data($aff_id) {
    global $wpdb;
    $payouts_table = WP_AFF_PAYOUTS_TBL_NAME;

    $total_payout_amt = 0;
    $wp_aff_payouts = $wpdb->get_results("SELECT * FROM $payouts_table WHERE refid = '$aff_id'", OBJECT);
    if ($wp_aff_payouts) {
        foreach ($wp_aff_payouts as $payout_row) {
            $total_payout_amt += $payout_row->payout_payment;
        }
    }

    echo '<p><strong>Total payout made to <span style="color:green;">' . $aff_id . '</span> so far: ' . $total_payout_amt . '</strong></p>';

    echo '
	<table class="widefat">
	<thead><tr>
	<th scope="col">' . __('Affiliate ID', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Payout Amount', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Date Paid', 'wp_affiliate') . '</th>
	<th scope="col">' . __('Action', 'wp_affiliate') . '</th>
	</tr></thead>
	<tbody>';

    if ($wp_aff_payouts) {
        foreach ($wp_aff_payouts as $wp_aff_payout) {
            echo '<tr>';
            echo '<td><strong>' . $wp_aff_payout->refid . '</strong></td>';
            echo '<td><strong>' . $wp_aff_payout->payout_payment . '</strong></td>';
            echo '<td><strong>' . $wp_aff_payout->date . '</strong></td>';
            echo '<td>';
            echo "<form method=\"post\" action=\"\" onSubmit=\"return confirm('Are you sure you want to delete this entry?');\">";
            echo "<input type=\"hidden\" name=\"payout_ref_id\" value=" . $wp_aff_payout->refid . " />";
            echo "<input type=\"hidden\" name=\"payout_amt\" value=" . $wp_aff_payout->payout_payment . " />";
            echo "<input type=\"hidden\" name=\"payout_date\" value=" . $wp_aff_payout->date . " />";
            echo "<input type=\"submit\" value=\"Delete\" name=\"delete_payout\">";
            echo "</form>";
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="4">' . __('No Payouts Data Found.', 'wp_affiliate') . '</td> </tr>';
    }
    echo '</tbody></table>';
}
