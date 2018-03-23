<?php

function wp_aff_leads_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_get_navbar();
    echo '<div id="wp_aff_inside">';

    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    echo '<div id="subnav">';
    echo '<li><a href="' . wp_aff_view_get_url_with_separator("clicks") . '">' . AFF_NAV_CLICKS . '</a></li>';
    if (get_option('wp_aff_use_2tier')) {
        echo '<li><a href="' . wp_aff_view_get_url_with_separator("sub-affiliates") . '">' . AFF_NAV_SUB_AFFILIATES . '</a></li>';
    }
    if ($wp_aff_platform_config->getValue('wp_aff_show_leads_to_affiliates') != '') {//Show leads nav menu
        echo '<li><a href="' . wp_aff_view_get_url_with_separator("leads") . '">' . AFF_NAV_LEADS . '</a></li>';
    }
    echo '<div style="clear:both;"></div>';
    echo '</div>';

    wp_aff_show_leads();
    echo '</div>';
    echo wp_aff_view_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_leads() {
    $currency = get_option('wp_aff_currency');
    global $wpdb;
    $aff_leads_table = WP_AFF_LEAD_CAPTURE_TBL_NAME;

    wp_aff_leads_history($aff_leads_table);
}

function wp_aff_leads_history($aff_leads_table) {
    include_once("aff_view_reports.php");

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
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            echo "<th>" . AFF_LEADS_USER_DETAILS . "</th>";
            echo "</tr>";

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
            echo "</table>";
        } else {
            echo "<br><br>" . AFF_LEADS_NO_CONVERSIONS;
        }
    } else {
        $resultsets = $wpdb->get_results("select * from $aff_leads_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);
        if ($resultsets) {
            echo '<strong>';
            echo AFF_LEADS_CONVERSIONS_FOR_YOUR_REFERRALS;
            echo '</strong>';
            echo "<br>" . AFF_LEADS_SHOWING_20;
            echo "<br>";

            echo "<table id='reports'>";
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            echo "<th>" . AFF_LEADS_USER_DETAILS . "</th>";
            echo "</tr>";

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
            echo "</table>";
        } else {
            echo "<br><br>" . AFF_LEADS_NO_CONVERSIONS;
        }
    }
}
