<?php
include_once ('misc_func.php');
if (!isset($_SESSION)) {
    @session_start();
}
include_once('countries.php');

$auto_responder_handler_path = ABSPATH . 'wp-content/plugins/' . WP_AFF_PLATFORM_FOLDER . '/wp_aff_auto_responder_handler.php';
include_once($auto_responder_handler_path);

if (!isset($_POST['afirstname'])) {
    $_POST['afirstname'] = '';
}
if (!isset($_POST['alastname'])) {
    $_POST['alastname'] = '';
}
if (!isset($_POST['acompany'])) {
    $_POST['acompany'] = '';
}
if (!isset($_POST['awebsite'])) {
    $_POST['awebsite'] = '';
}
if (!isset($_POST['aemail'])) {
    $_POST['aemail'] = '';
}
if (!isset($_POST['paypal_email'])) {
    $_POST['paypal_email'] = '';
}
if (!isset($_POST['tax_id'])) {
    $_POST['tax_id'] = '';
}
if (!isset($_POST['astreet'])) {
    $_POST['astreet'] = '';
}
if (!isset($_POST['atown'])) {
    $_POST['atown'] = '';
}
if (!isset($_POST['astate'])) {
    $_POST['astate'] = '';
}
if (!isset($_POST['apostcode'])) {
    $_POST['apostcode'] = '';
}
if (!isset($_POST['aphone'])) {
    $_POST['aphone'] = '';
}
if (!isset($_POST['user_name'])) {
    $_POST['user_name'] = '';
}
if (!isset($_POST['apayable'])) {
    $_POST['apayable'] = '';
}

if (isset($_POST['doRegister'])) {
    global $wpdb;
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
    $_POST = array_map('strip_tags', $_POST); //$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

    if (get_option('wp_aff_use_recaptcha')) {
        //Recaptcha was shown to the user to lets check the response
        include_once(WP_AFF_PLATFORM_PATH . 'lib/recaptchalib.php');
        $secret = get_option('wp_aff_captcha_private_key'); //Secret

        $reCaptcha = new WPAP_ReCaptcha($secret);
        $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_REQUEST["g-recaptcha-response"]);
        if ($resp != null && $resp->success) {
            //Valid reCAPTCHA response. Go ahead with the registration
        } else {
            //Invalid response. Stop going forward. Set the error msg so the form shows it to the user.
            $recaptcha_error = AFF_IMAGE_VERIFICATION_FAILED;
            $_GET['msg'] = AFF_IMAGE_VERIFICATION_FAILED;
            wp_aff_standalone_signup_rego_form($recaptcha_error);
            exit;
        }
    }

    //=================
    include_once(ABSPATH . WPINC . '/class-phpass.php');
    $wp_hasher = new PasswordHash(8, TRUE);
    $password = $wp_hasher->HashPassword($_POST['pwd']);

    $user_ip = wp_aff_get_user_ip();
    $host = $_SERVER['HTTP_HOST'];
    $host_upper = strtoupper($host);
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $activ_code = rand(1000, 9999);
    $aemail = esc_sql($_POST['aemail']);
    $user_name = esc_sql($_POST['user_name']);
    $userid = esc_sql($_POST['user_name']);

    $result = $wpdb->get_results("SELECT refid FROM $affiliates_table_name where refid='$userid'", OBJECT);
    if ($result) {
        $err = urlencode("ERROR: The username already exists. Please try again with different username and email.");
        header("Location: register.php?msg=$err");
        exit();
    }
    // save and send notification email
    // check if referred by another affiliate
    $referrer = "";
    if (!empty($_SESSION['ap_id'])) {
        $referrer = $_SESSION['ap_id'];
    } else if (isset($_COOKIE['ap_id'])) {
        $referrer = $_COOKIE['ap_id'];
    }

    $commission_level = get_option('wp_aff_commission_level');
    $date = (date("Y-m-d"));
    $account_details = "";
    $sec_tier_commissionlevel = "";
    $account_status = 'approved';
    if ($wp_aff_platform_config->getValue('wp_aff_enable_manual_signup_approval') == '1') {
        wp_affiliate_log_debug("Manual affiliate registration option is enabled. So the account status will be set to pending.", true);
        $account_status = 'pending';
    }

    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TABLE;
    $updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,date,paypalemail,commissionlevel,referrer,tax_id,account_details,sec_tier_commissionlevel,account_status) VALUES ('" . $_POST['user_name'] . "', '" . $password . "', '" . $_POST['acompany'] . "', '" . $_POST['afirstname'] . "', '" . $_POST['alastname'] . "', '" . $_POST['awebsite'] . "', '" . $_POST['aemail'] . "', '" . $_POST['apayable'] . "', '" . $_POST['astreet'] . "', '" . $_POST['atown'] . "', '" . $_POST['astate'] . "', '" . $_POST['apostcode'] . "', '" . $_POST['acountry'] . "', '" . $_POST['aphone'] . "', '$date','" . $_POST['paypal_email'] . "','" . $commission_level . "','" . $referrer . "', '" . $_POST['tax_id'] . "','$account_details','$sec_tier_commissionlevel','$account_status')";
    $results = $wpdb->query($updatedb);

    $affiliate_login_url = get_option('wp_aff_login_url');

    $email_subj = get_option('wp_aff_signup_email_subject');
    $body_sign_up = get_option('wp_aff_signup_email_body');
    $from_email_address = get_option('wp_aff_senders_email_address');
    $headers = 'From: ' . $from_email_address . "\r\n";

    //TODO - use wp_aff_dynamically_replace_affiliate_details_in_message() function instead
    $tags1 = array("{user_name}", "{email}", "{password}", "{login_url}");
    $vals1 = array($user_name, $aemail, $_POST['pwd'], $affiliate_login_url);
    $vals2 = array($user_name, $aemail, "********", $affiliate_login_url);
    $aemailbody = str_replace($tags1, $vals1, $body_sign_up);
    $admin_email_body = str_replace($tags1, $vals2, $body_sign_up);
    $admin_email_body = "The following email was sent to the affiliate: \n" .
            "-----------------------------------------\n" . $admin_email_body;

    if (get_option('wp_aff_admin_notification')) {
        $admin_email_subj = "New affiliate sign up notification";
        $admin_contact_email = get_option('wp_aff_contact_email');
        if (empty($admin_contact_email)) {
            $admin_contact_email = $from_email_address;
        }
        wp_mail($admin_contact_email, $admin_email_subj, $admin_email_body);
        wp_affiliate_log_debug("Affiliate signup notification email successfully sent to the admin: " . $admin_contact_email, true);
    }
    wp_mail($_POST['aemail'], $email_subj, $aemailbody, $headers);
    wp_affiliate_log_debug("Welcome email successfully sent to the affiliate: " . $_POST['aemail'], true);

    //Check and give registration bonus
    wp_aff_check_and_give_registration_bonus($userid);

    //Check and do autoresponder signup
    wp_aff_global_autoresponder_signup($_POST['afirstname'], $_POST['alastname'], $_POST['aemail']);

    do_action('wp_aff_registration_complete');//registration complete hook
    
    header('Location: ' . plugins_url('thankyou.php', __FILE__));
}

wp_aff_standalone_signup_rego_form();

function wp_aff_standalone_signup_rego_form($recaptcha_error = "") {
    global $wpdb;
    global $wp_aff_platform_config;
    $page_meta_title = get_option('wp_aff_site_title') . " - " . AFF_SIGNUP_PAGE_TITLE;
    define('AFF_META_TITLE', $page_meta_title);
    include "header.php";
    ?>

    <script language="JavaScript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>
    <script>
        $(document).ready(function() {
            $.validator.addMethod("username", function(value, element) {
                return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
            }, "Username must contain only letters, numbers, or underscore.");

            $("#regForm").validate();
        });
    </script>

    <p>
        <?php
        if (isset($_GET['done'])) {
            echo "<h2 class='title'>Thank you</h2> <p class='message'>Your registration is now complete and you can <a style='color:#CC0000;' href=\"login.php\">login here</a></p>";
            $additional_success_msg = apply_filters('wpap_below_registration_success_message', '');
            if (!empty($additional_success_msg)) {
                echo $additional_success_msg;
            }
            exit();
        }
        ?>
    </p>

    <?php
    echo '<h3 class="title">' . AFF_SIGNUP_PAGE_TITLE . '</h3>';
    if (get_option('wp_aff_disable_visitor_signup')) {//Affiliate self signup is disabled
        echo '<p style="color:red;" align="center"><strong>' . AFF_ACCOUNT_SIGNUP_DISABLED . '</strong></p>';
        include "footer.php";
        exit;
    }
    echo '<p>' . AFF_SIGNUP_PAGE_MESSAGE . '</p>';
    if (isset($_GET['msg'])) {
        $msg = sanitize_text_field($_GET['msg']);
        echo "<div class=\"error\">$msg</div>";
    }
    if (isset($_GET['done'])) {
        echo "<h2 class='title'>" . AFF_THANK_YOU . "</h2> <p class='message'>" . AFF_REGO_COMPLETE . " " . AFF_REGO_COMPLETE_MESSAGE . "</p>";
        exit();
    }
    ?>

    <!-- Start Registration Form -->
    <form action="register.php" method="post" name="regForm" id="regForm" >
        <table width="95%" border="0" cellpadding="3" cellspacing="3" class="forms">

            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_FIRST_NAME; ?>: *</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="afirstname" size=20 value="<?php echo $_POST['afirstname']; ?>" class="required">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_LAST_NAME; ?>: *</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="alastname" size=20 value="<?php echo $_POST['alastname']; ?>" class="required">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_COMPANY; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="acompany" size=20 value="<?php echo $_POST['acompany']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_WEBSITE; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="awebsite" size=20 value="<?php echo $_POST['awebsite']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_EMAIL; ?>: *</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="aemail" size=20 value="<?php echo $_POST['aemail']; ?>" class="required email">
                        </font></b></td>
            </tr>
            <tr>
                <?php
                if ($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required') == '1') {
                    echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">' . AFF_PAYPAL_EMAIL . ': *</font></b></td>';
                    echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '" class="required email"></font></b></td>';
                } else {
                    echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">' . AFF_PAYPAL_EMAIL . ': </font></b></td>';
                    echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '"></font></b></td>';
                }
                ?>
            </tr>
            <?php
            if ($wp_aff_platform_config->getValue('wp_aff_hide_tax_id_field') == '1') {
                //Do not show the tax ID field           		
            } else {
                echo '<tr>';
                echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000">' . AFF_TAX_ID . ': </font></b></td>';
                echo '<td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><input type="text" name="tax_id" size="20" value="' . $_POST['tax_id'] . '"></font></b></td>';
                echo '</tr>';
            }
            ?>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_ADDRESS; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="astreet" size=20 value="<?php echo $_POST['astreet']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_TOWN; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="atown" size=20 value="<?php echo $_POST['atown']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_STATE; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="astate" size=20 value="<?php echo $_POST['astate']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_ZIP; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="apostcode" size=20 value="<?php echo $_POST['apostcode']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_COUNTRY; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <select name=acountry class=dropdown value="UK" >
                            <?php
                            foreach ($GLOBALS['countries'] as $key => $country)
                                print '<option value="' . $key . '" ' . ($key == "VN" ? 'selected' : '') . '>' . $country . '</option>' . "\n";
                            ?>
                        </select>
                        </font></b></td>
            </tr>

            <tr> 
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"><?php echo AFF_PHONE; ?>:</font></b></td>
                <td><b><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> 
                        <input type="text" name="aphone" size=20 value="<?php echo $_POST['aphone']; ?>">
                        </font></b></td>
            </tr>
            <tr> 
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>


            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr> 
                <td colspan="2"><h4><strong><?php echo AFF_LOGIN_DETAILS; ?></strong></h4></td>
            </tr>
            <tr> 
                <td><?php echo AFF_USERNAME; ?><span class="required"><font color="#CC0000">*</font></span></td>
                <td><input name="user_name" type="text" id="user_name" class="required username" minlength="5" value="<?php echo $_POST['user_name']; ?>" > 
                    <input name="btnAvailable" type="button" class="button" id="btnAvailable" 
                           onclick='$("#checkid").html("<?php echo AFF_SI_PLEASE_WAIT; ?>");
                $.get("checkuser.php", {cmd: "check", user: $("#user_name").val()}, function(data) {
                    $("#checkid").html(data);
                });'
                           value="<?php echo AFF_AVAILABILITY_BUTTON_LABEL; ?>"> 

                    <br /><span style="color:red; font: bold 12px verdana; " id="checkid" ></span> 
                </td>
            </tr>
            <tr>
                <td><?php echo AFF_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
                </td>
                <td><input name="pwd" type="password" class="required password" minlength="5" id="pwd" > 
                    <span class="example">** <?php echo AFF_MIN_PASS_LENGTH; ?>..</span></td>
            </tr>
            <tr> 
                <td><?php echo AFF_RETYPE_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
                </td>
                <td><input name="pwd2"  id="pwd2" class="required password" type="password" minlength="5" equalto="#pwd"></td>
            </tr>
            <tr> 
                <td colspan="2">&nbsp;</td>
            </tr>
            <?php
            if (get_option('wp_aff_use_recaptcha')) {
                echo '<tr><td width="22%"></td><td width="78%" class="wpap_g_captcha_td">';
                $publickey = get_option('wp_aff_captcha_public_key'); //SiteKey
                $captcha_code = '';
                $captcha_code .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
                $captcha_code .= '<div class="wpam_g_captcha">';
                $captcha_code .= '<div class="g-recaptcha" data-sitekey="' . $publickey . '"></div>';
                $captcha_code .= '</div>';
                echo $captcha_code;
                echo '</td></tr>';
            }
            ?>

        </table>
        <p align="center">
            <?php
            if (get_option('wp_aff_disable_visitor_signup')) {
                echo '<p style="color:red;" align="center"><strong>' . AFF_ACCOUNT_SIGNUP_DISABLED . '</strong></p>';
            } else {
                $terms_url = get_option('wp_aff_terms_url');
                if (!empty($terms_url)) {
                    $terms = "<a href=\"$terms_url\" target=\"_blank\"><u>" . AFF_TERMS_AND_COND . "</u></a>";
                    echo '<label for="affiliate-t-and-c">' . AFF_TERMS_AGREE . $terms . '</label><input type="checkbox" name="affiliate-t-and-c" class="affiliate-t-and-c required" value="" /><br />';
                    //echo AFF_YOU_AGREE_TO.' <strong><a href="'.$terms_url.'" target="_blank">'.AFF_TERMS_AND_COND.'</a></strong><br /><br />';
                }
                echo '<input name="doRegister" type="submit" id="doRegister" class="button" value="' . AFF_SIGN_UP_BUTTON_LABEL . '">';
            }
            ?>
        </p>
    </form>

    <p>&nbsp;</p>
    <p><?php echo AFF_ALREADY_MEMBER; ?>? <img src="images/login.png" /> <a style="color:#CC0000" href=login.php><?php echo AFF_LOGIN_HERE; ?></a></p>

    <?php
    include "footer.php";
}
