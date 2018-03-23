<?php

function wp_aff_leads_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_2_get_navbar();
    echo '<div id="wp_aff_inside">';
    wp_aff_show_leads();
    echo '</div>';
    echo wp_aff_view_2_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_leads() {
    
    echo '<div class="wpap-vertical-buffer-10"></div>';
    include_once("aff_view_reports.php");

    global $wpdb;
    $aff_leads_table = WP_AFF_LEAD_CAPTURE_TBL_NAME;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div class="alert alert-info">';
        echo '<strong>'.AFF_LEADS_DISPLAYING_CONVERSION_HISTORY . ' ' . $start_date . ' ' . AFF_AND . ' ' . $end_date.'</strong>';
        echo '</div>';

        $curr_date = (date("Y-m-d"));
        $resultsets = $wpdb->get_results("select * from $aff_leads_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);

        if ($resultsets) {
            echo "<table class='table' id='reports'>";
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
            echo '<div class="alert alert-warning">' . AFF_LEADS_NO_CONVERSIONS .'</div>';
        }
    } else {
        $resultsets = $wpdb->get_results("select * from $aff_leads_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);
        if ($resultsets) {
            echo '<div class="alert alert-info"><strong>';
            echo AFF_LEADS_CONVERSIONS_FOR_YOUR_REFERRALS;
            echo '</strong>';
            echo '<br>' . AFF_LEADS_SHOWING_20;
            echo '</strong></div>';

            echo "<table class='table' id='reports'>";
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
            echo '<div class="alert alert-warning">' . AFF_LEADS_NO_CONVERSIONS . '</div>';
        }
    }
}
