<?php

function wp_aff_forgot_pass_view() {
    $output = '';
    $output .= wp_aff_view_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_forgot_pass_page();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_forgot_pass_page() {
    
    ob_start();
    $success_msg = '';
    $error_msg = '';
    if (isset($_POST['doReset'])) {
        global $wpdb;
        $user_email = esc_sql($_POST['user_email']);

        //check if activ code and user is valid as precaution
        $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
        $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where email='$user_email'", OBJECT);

        // Match row found with more than 1 results  - the user is authenticated. 
        if (!$result) {
            $error_msg = AFF_FORGOT_PASS_NO_ACCOUNT_EXISTS;
        } else {
            //generate 6 digit random number
            $new_pass = rand(100000, 999999);

            //Hash the password
            include_once(ABSPATH . WPINC . '/class-phpass.php');
            $wp_hasher = new PasswordHash(8, TRUE);
            $password = $wp_hasher->HashPassword($new_pass);

            //Set the new password here
            $user_id = $result->refid;
            $updatedb = "UPDATE $affiliates_table_name SET pass = '" . $password . "' WHERE refid = '" . $user_id . "'";
            $results = $wpdb->query($updatedb);

            //send email
            $aemailbody =
                    "Here is your new password details ...\n
	        User Email: $user_email \n
	        User ID: $user_id \n
	        Password: $new_pass \n
	        
	        Thank You
	        
	        Administrator
	        ______________________________________________________
	        THIS IS AN AUTOMATED RESPONSE.
	        ***DO NOT RESPOND TO THIS EMAIL****
	        ";
            $email_subj = "New Affiliate Password";
            $from_email_address = get_option('wp_aff_senders_email_address');
            $attachment = '';

            $headers = 'From: ' . $from_email_address . "\r\n";
            wp_mail($user_email, $email_subj, $aemailbody, $headers);
            $success_msg = AFF_FORGOT_PASS_PASSWORD_HAS_BEEN_RESET;
        }
    }
    ?>

    <!-- Load jQuery Validation -->
    <script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/js/jquery.validate.min.js'; ?>"></script>
    <script type="text/javascript">
        /* <![CDATA[ */
        jQuery(document).ready(function($) {
            $("#forgotPassForm").validate();
        });
        /*]]>*/
    </script>

    <h3 class="wp_aff_title"><?php echo AFF_FORGOT_PASS_PAGE_TITLE; ?></h3>

    <?php
    if (!empty($error_msg)) {
        $output .= '<div class="alert alert-danger">' . $error_msg . '</div>';
    } else if (!empty($success_msg)) {
        $output .= '<div class="alert alert-success">' . $success_msg . '</div>';
    }

    ?>
    <div class="alert alert-info"><?php echo AFF_FORGOT_PASS_MESSAGE; ?></div>

    <!-- Start Forgot Pwd Form -->
    <form action="" method="post" name="reset-pass-form" id="forgotPassForm" >

        <div class="input-group wpap-max-width-300 wpap-col-centered">
            <span class="input-group-addon">@</span>
            <input type="text" name="user_email" class="form-control required email" placeholder="<?php echo AFF_FORGOT_PASS_EMAIL; ?>">
        </div>

        <div class="row wpap-buffer-5"></div>

        <div class="row text-center">
            <button name="doReset" class="btn btn-default btn-lg wpap-buffer-5" type="submit" id="doLogin3" value="">
                <span class="glyphicon glyphicon-refresh"></span> <?php echo AFF_RESET_BUTTON_LABEL; ?>
            </button>
        </div>

        <div class="row text-center wpap-vertical-buffer-10">
            <span class="glyphicon glyphicon-pencil"></span>
            <a href="<?php echo wp_aff_view_get_url_with_separator("signup"); ?>"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a>
            | 
            <span class="glyphicon glyphicon-lock"></span>
            <a href="<?php echo wp_aff_view_get_url_with_separator("login"); ?>"><?php echo AFF_LOGIN_HERE; ?></a>
        </div>


    </form>	
    <?php
    $output .= ob_get_contents();
    ob_end_clean();

    return $output;
}
