<?php

function wp_aff_contact_view() {
    $output = "";
    $output .= wp_aff_view_2_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .=wp_aff_show_contact_form();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_contact_form() {
    $output = "";
    if (isset($_POST['send_msg'])) {
        $subj = AFF_C_MSG_FROM_AFFILIATE;
        $affiliate_details = "Affiliate ID: " . $_SESSION['user_id'] . "\n" . AFF_C_AFFILIATE_NAME . $_POST['aff_name'] . "\n" . AFF_C_AFFILIATE_EMAIL . $_POST['aff_email'];
        $body = "\n-------------------\n" . $affiliate_details .
                "\n-------------------\n\n" . $_POST['aff_msg'];


        $admin_email = get_option('wp_aff_contact_email');
        $headers = 'From: ' . $_POST['aff_email'] . "\r\n";

        wp_mail($admin_email, $subj, $body, $headers);
        wp_affiliate_log_debug("Email sent to (" . $admin_email . ") with the message submitted in the affiliate contact form.", true);
        $output .= "<br /><strong>" . AFF_C_MSG_SENT . "</strong><br /><br />";
    }

    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $editingaff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '" . $_SESSION['user_id'] . "'", OBJECT);
    ob_start();
    ?>

    <div class="wpap-vertical-buffer-10"></div>
    
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo AFF_C_USE_THE_FORM_BELOW; ?></div>
        <div class="panel-body">

            <form id="wp_aff_contact" action="" method="post">
                <input type="hidden" name="send_msg" id="send_msg" value="true" />

                <div class="input-group">
                    <?php echo AFF_C_NAME; ?><br />
                    <input name="aff_name" type="text" value=<?php echo $editingaff->firstname; ?> >
                </div>

                <div class="input-group">
                    <?php echo AFF_C_EMAIL; ?><br />
                    <input name="aff_email" type="email" value=<?php echo $editingaff->email; ?>>
                </div>

                <div class="input-group">
                    <?php echo AFF_C_MSG; ?><br />
                    <textarea name="aff_msg" rows="5" cols="20" required></textarea>
                </div>

                <button name="sendMsg" class="btn btn-default btn-lg wpap-buffer-5" type="submit" value="">
                    <?php echo AFF_C_SEND_MSG_BUTTON; ?>
                </button>            

            </form>

        </div>
    </div>      
    <?php
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}
