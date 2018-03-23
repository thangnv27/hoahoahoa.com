<?php

function wp_aff_referrals_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_2_get_navbar();
    echo '<div id="wp_aff_inside">';
    wp_aff_show_referrals();
    echo '</div>';
    echo wp_aff_view_2_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_referrals() {

    echo '<div class="wpap-vertical-buffer-10"></div>';
    include_once("aff_view_reports.php");

    global $wpdb;
    $clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div class="alert alert-info">';
        echo '<strong>' . AFF_C_DISPLAYING_REFERRALS . ' ' . $start_date . ' ' . AFF_AND . ' ' . $end_date . '</strong>';
        echo '</div>';

        $curr_date = (date("Y-m-d"));

        $wp_aff_clicks = $wpdb->get_results("select * from $clickthroughs_table_name where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER BY date DESC", OBJECT);

        if ($wp_aff_clicks) {
            echo "<table class='table' id='reports'>";
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                echo '<th>' . AFF_CUSTOM_VALUE . '</th>';
            }
            echo "<th>" . AFF_C_REFERREDFROM . "</th></tr>";

            foreach ($wp_aff_clicks as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</td>";
                if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                    echo "<td class='reportscol col1'>" . $resultset->campaign_id . "</td>";
                }
                echo "<td class='reportscol'>";
                echo $resultset->referralurl;
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_C_NO_CLICKS . '</div>';
        }
    } else {
        $resultset = $wpdb->get_results("select * from $clickthroughs_table_name where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<div class="alert alert-info"><strong>';
            echo AFF_C_CLICKS;
            echo '</strong>';
            echo "<br>" . AFF_C_SHOWING_20;
            echo "</div>";

            echo "<table class='table' id='reports'>";
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                echo '<th>' . AFF_CUSTOM_VALUE . '</th>';
            }
            echo "<th>" . AFF_C_REFERREDFROM . "</th></tr>";

            foreach ($resultset as $resultset) {
                echo "<tr>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</td>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</td>";
                if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                    echo "<td class='reportscol col1'>" . $resultset->campaign_id . "</td>";
                }
                echo "<td class='reportscol'>";
                echo $resultset->referralurl;
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_C_NO_CLICKS . '</div>';
        }
    }
}
