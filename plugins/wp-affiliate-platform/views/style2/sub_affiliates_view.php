<?php

function wp_aff_sub_affiliates_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_2_get_navbar();
    echo '<div id="wp_aff_inside">';
    wp_aff_show_sub_affiliates();
    echo '</div>';
    echo wp_aff_view_2_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_sub_affiliates() {
    
    echo '<div class="wpap-vertical-buffer-10"></div>';
    include_once("aff_view_reports.php");

    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div class="alert alert-info">';
        echo '<strong>'.AFF_C_DISPLAYING_REFERRALS . ' ' . $start_date . ' ' . AFF_AND . ' ' . $end_date.'</strong>';
        echo '</div>';

        $curr_date = (date("Y-m-d"));

        $resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);

        if ($resultset) {
            echo "<table class='table' id='reports'>";
            echo "<tr><th>" . AFF_TIER_SUB_AFFILIATES_DATE_JOINED . "</th><th>" . AFF_TIER_SUB_AFFILIATES_ID . "</th>";
            echo "</tr>";

            foreach ($resultset as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol'>";
                echo $resultset->refid;
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_TIER_SUB_AFFILIATES_NO_RECORDS .'</div>';
        }
    } else {
        $resultset = $wpdb->get_results("select * from $affiliates_table_name where referrer = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<div class="alert alert-info"><strong>';
            echo AFF_TIER_SUB_AFFILIATES_UNDER_YOU;
            echo '</strong>';
            echo '<br>' . AFF_TIER_SUB_AFFILIATES_20;
            echo '</div>';

            echo "<table class='table' id='reports'>";
            echo "<tr><th>" . AFF_TIER_SUB_AFFILIATES_DATE_JOINED . "</th><th>" . AFF_TIER_SUB_AFFILIATES_ID . "</th>";
            echo "</tr>";

            foreach ($resultset as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol'>";
                echo $resultset->refid;
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_TIER_SUB_AFFILIATES_NO_RECORDS .'</div>';
        }
    }
}
