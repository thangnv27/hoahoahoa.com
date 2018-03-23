<?php

function wp_aff_referrals_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_get_navbar();
    echo '<div id="wp_aff_inside">';

    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    echo '<div id="subnav">';
    //echo '<li><a href="'.wp_aff_view_get_url_with_separator("clicks").'">'.AFF_NAV_CLICKS.'</a></li>';
    if (get_option('wp_aff_use_2tier')) {
        echo '<li><a href="' . wp_aff_view_get_url_with_separator("sub-affiliates") . '">' . AFF_NAV_SUB_AFFILIATES . '</a></li>';
    }
    if ($wp_aff_platform_config->getValue('wp_aff_show_leads_to_affiliates') != '') {//Show leads nav menu
        echo '<li><a href="' . wp_aff_view_get_url_with_separator("leads") . '">' . AFF_NAV_LEADS . '</a></li>';
    }
    echo '<div style="clear:both;"></div>';
    echo '</div>';

    echo '<div class="wp_aff_referral_page_graphic wp_aff_portal_page_graphic"><img src="' . WP_AFF_PLATFORM_URL . '/affiliates/images/click_throughs_icon.jpg" alt="click through icon" /></div>';
    wp_aff_show_referrals();
    echo '</div>';
    echo wp_aff_view_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_referrals() {
    $clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    wp_aff_clicks_history($clickthroughs_table_name);
}

function wp_aff_clicks_history($clickthroughs_table_name) {
    include_once("aff_view_reports.php");

    global $wpdb;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<p><strong>';
        echo AFF_C_DISPLAYING_REFERRALS . ' <font class="blue">' . $start_date . '</font> ' . AFF_AND . ' <font class="blue">' . $end_date;
        echo '</font></strong></p>';

        $curr_date = (date("Y-m-d"));

        $wp_aff_clicks = $wpdb->get_results("select * from $clickthroughs_table_name where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER BY date DESC", OBJECT);

        if ($wp_aff_clicks) {
            print "<table id='reports'>";
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                echo '<th>' . AFF_CUSTOM_VALUE . '</th>';
            }
            echo "<th>" . AFF_C_REFERREDFROM . "</th></tr>";

            foreach ($wp_aff_clicks as $resultset) {
                print "<tr>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</td>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</td>";
                if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                    print "<td class='reportscol col1'>" . $resultset->campaign_id . "</td>";
                }
                print "<td class='reportscol'>";
                print $resultset->referralurl;
                print "</td>";
                print "</tr>";
            }
            print "</table>";
        } else {
            echo "<br><br>" . AFF_C_NO_CLICKS;
        }
    } else {
        $resultset = $wpdb->get_results("select * from $clickthroughs_table_name where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<strong>';
            echo AFF_C_CLICKS;
            echo '</strong>';
            echo "<br>" . AFF_C_SHOWING_20;
            print "<br><br>";

            echo "<table id='reports'>";
            echo "<tr><th>" . AFF_G_DATE . "</th><th>" . AFF_G_TIME . "</th>";
            if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                echo '<th>' . AFF_CUSTOM_VALUE . '</th>';
            }
            echo "<th>" . AFF_C_REFERREDFROM . "</th></tr>";

            foreach ($resultset as $resultset) {
                print "<tr>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</td>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</td>";
                if (get_option('wp_aff_enable_clicks_custom_field') != '') {
                    print "<td class='reportscol col1'>" . $resultset->campaign_id . "</td>";
                }
                print "<td class='reportscol'>";
                print $resultset->referralurl;
                print "</td>";
                print "</tr>";
            }
            print "</table>";
        } else {
            echo "<br><br>" . AFF_C_NO_CLICKS;
        }
    }
}
