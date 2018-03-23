<?php

include_once ('misc_func.php');
if (!isset($_SESSION)) {
    @session_start();
}

if (!aff_check_security()) {
    aff_redirect('index.php');
    exit;
}

include "header.php";

$wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

echo '<div id="subnav"><li><a href="clicks.php">' . AFF_NAV_CLICKS . '</a></li></div>';
if (get_option('wp_aff_use_2tier')) {
    echo '<div id="subnav"><li><a href="sub-affiliates.php">' . AFF_NAV_SUB_AFFILIATES . '</a></li></div>';
}
if ($wp_aff_platform_config->getValue('wp_aff_show_leads_to_affiliates') != '') {//Show leads nav menu
    echo '<div id="subnav"><li><a href="leads.php">' . AFF_NAV_LEADS . '</a></li></div>';
}
echo '<div style="clear:both;"></div><br />';

$currency = get_option('wp_aff_currency');
$aff_leads_table = WP_AFF_LEAD_CAPTURE_TBL_NAME;

leads_history($aff_leads_table);

include "footer.php";

function leads_history($aff_leads_table) {

    include ("reports.php");

    $currency = get_option('wp_aff_currency');
    global $wpdb;
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<h4>';
        echo AFF_LEADS_DISPLAYING_CONVERSION_HISTORY . ' <font style="color:#222;">' . $start_date . '</font> ' . AFF_AND . ' <font style="color:#222;">' . $end_date;
        echo '</font></h4>';

        $curr_date = (date("Y-m-d"));

        $resultsets = $wpdb->get_results("select * from $aff_leads_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);

        if ($resultsets) {
            echo "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_LEADS_USER_DETAILS . "</TH>";
            echo "</TR>";

            foreach ($resultsets as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</td>";
                echo "<td class='reportscol'>";
                echo '<div class="wpap_leads_email">'.$resultset->buyer_email.'</div>';
                if (!empty($resultset->buyer_name)) {
                    echo '<div class="wpap_leads_name">'. $resultset->buyer_name .'</div>';
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</TABLE>";
        } else {
            echo "<br><br><font face=arial>" . AFF_LEADS_NO_CONVERSIONS;
        }
    } else {
        $resultsets = $wpdb->get_results("select * from $aff_leads_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultsets) {
            echo '<strong>';
            echo "<br><br><font face=arial>" . AFF_LEADS_CONVERSIONS_FOR_YOUR_REFERRALS;
            echo '</strong>';
            echo "<br><br>" . AFF_LEADS_SHOWING_20;
            echo "<br><br>";

            echo "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_LEADS_USER_DETAILS . "</TH>";
            echo "</TR>";

            foreach ($resultsets as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</td>";
                echo "<td class='reportscol'>";
                echo '<div class="wpap_leads_email">'.$resultset->buyer_email.'</div>';
                if (!empty($resultset->buyer_name)) {
                    echo '<div class="wpap_leads_name">'. $resultset->buyer_name .'</div>';
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</TABLE>";
        } else {
            echo "<br><br><font face=arial>" . AFF_LEADS_NO_CONVERSIONS;
        }
    }
}
