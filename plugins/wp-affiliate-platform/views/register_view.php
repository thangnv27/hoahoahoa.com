<?php

function wp_aff_register_view() {
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

    $output = "";
    ob_start();
    echo wp_aff_view_get_navbar();
    echo '<div id="wp_aff_inside">';
    $retval = "";
    if (get_option('wp_aff_disable_visitor_signup')) {//Affiliate self signup is disabled
        echo '<p style="color:red;" align="center"><strong>' . AFF_ACCOUNT_SIGNUP_DISABLED . '</strong></p>';
    } else {
        if (isset($_SESSION['wp_aff_signup_success_msg']) && !empty($_SESSION['wp_aff_signup_success_msg'])) {
            echo $_SESSION['wp_aff_signup_success_msg'];
        } else {
            wp_aff_show_signup_form();
        }
    }
    echo '</div>';
    echo wp_aff_view_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_signup_form($recaptcha_error = '') {
    global $wpdb;
    global $wp_aff_platform_config;
    include_once('countries.php');
    $login_url = wp_aff_view_get_url_with_separator("login");
    ?>

    <script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/js/jquery.validate.min.js'; ?>"></script>
    <script type="text/javascript">
        /* <![CDATA[ */
        jQuery(document).ready(function($) {
            $.validator.addMethod("username", function(value, element) {
                return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
            }, "Username must contain only letters, numbers, or underscore.");

            $("#regForm").validate();
        });
        /*]]>*/
    </script>

    <h3 class="wp_aff_title"><?php echo AFF_SIGNUP_PAGE_TITLE; ?></h3>
    <p><?php echo AFF_SIGNUP_PAGE_MESSAGE; ?></p>

    <?php
    if (isset($_GET['aff_signup_error_msg'])) {
        $msg = strip_tags($_GET['aff_signup_error_msg']);
        echo "<div class=\"wp_aff_error_msg\">$msg</div>";
    }
    ?>

    <!-- Start Registration Form -->
    <form action="" method="post" name="regForm" id="regForm" >
        <table width="95%" border="0" cellpadding="3" cellspacing="3" class="forms">

            <tr> 
                <td><b><?php echo AFF_FIRST_NAME; ?>: *</b></td>
                <td><b> 
                        <input type="text" name="afirstname" size="20" value="<?php echo $_POST['afirstname']; ?>" class="required">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_LAST_NAME; ?>: *</b></td>
                <td><b> 
                        <input type="text" name="alastname" size="20" value="<?php echo $_POST['alastname']; ?>" class="required">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_COMPANY; ?>:</b></td>
                <td><b> 
                        <input type="text" name="acompany" size="20" value="<?php echo $_POST['acompany']; ?>">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_WEBSITE; ?>:</b></td>
                <td><b> 
                        <input type="text" name="awebsite" size="20" value="<?php echo $_POST['awebsite']; ?>">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_EMAIL; ?>: *</b></td>
                <td><b> 
                        <input type="text" name="aemail" size="20" value="<?php echo $_POST['aemail']; ?>" class="required email">
                    </b></td>
            </tr>
            <tr>
                <?php
                if ($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required') == '1') {
                    echo '<td><b>' . AFF_PAYPAL_EMAIL . ': *</b></td>';
                    echo '<td><b><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '" class="required email"></b></td>';
                } else {
                    echo '<td><b>' . AFF_PAYPAL_EMAIL . ': </b></td>';
                    echo '<td><b><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '"></b></td>';
                }
                ?>
            </tr>
            <?php
            if ($wp_aff_platform_config->getValue('wp_aff_hide_tax_id_field') == '1') {
                //Do not show the tax ID field           		
            } else {
                echo '<tr>';
                echo '<td><b>' . AFF_TAX_ID . ': </b></td>';
                echo '<td><b><input type="text" name="tax_id" size="20" value="' . $_POST['tax_id'] . '"></b></td>';
                echo '</tr>';
            }
            ?>
            <tr> 
                <td><b><?php echo AFF_ADDRESS; ?>:</b></td>
                <td><b> 
                        <input type="text" name="astreet" size="20" value="<?php echo $_POST['astreet']; ?>">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_TOWN; ?>:</b></td>
                <td><b> 
                        <input type="text" name="atown" size="20" value="<?php echo $_POST['atown']; ?>">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_STATE; ?>:</b></td>
                <td><b> 
                        <input type="text" name="astate" size="20" value="<?php echo $_POST['astate']; ?>">
                    </b></td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_ZIP; ?>:</b></td>
                <td><b> 
                        <input type="text" name="apostcode" size="20" value="<?php echo $_POST['apostcode']; ?>">
                    </b>
                    <small>Bạn có thể lấy mã Zip Postal Code <a href="<?php echo get_option('wp_aff_zipcode_url'); ?>" target="_blank">tại đây</a>.</small>
                </td>
            </tr>
            <tr> 
                <td><b><?php echo AFF_COUNTRY; ?>:</b></td>
                <td><b> 
                        <select name="acountry" class="user-select">
                            <?php
                            foreach ($GLOBALS['countries'] as $key => $country)
                                print '<option value="' . $key . '" ' . ($key == "VN" ? 'selected' : '') . '>' . $country . '</option>' . "\n";
                            ?>
                        </select>
                    </b></td>
            </tr>

            <tr> 
                <td><b><?php echo AFF_PHONE; ?>:</b></td>
                <td><b> 
                        <input type="text" name="aphone" size="20" value="<?php echo $_POST['aphone']; ?>">
                    </b></td>
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
                    <br /><span style="color:red; font: bold 12px verdana; " id="checkid" ></span>
                </td>

            <tr>
                <td></td>
                <td>
                    <input name="btnAvailable" type="button" class="button" id="btnAvailable" onclick='jQuery(document).ready(function($) {
                    $("#checkid").html("<?php echo AFF_SI_PLEASE_WAIT; ?>");
                    $.get("<?php echo WP_AFF_PLATFORM_URL . '/affiliates/checkuser.php'; ?>", {cmd: "check", user: $("#user_name").val()}, function(data) {
                        $("#checkid").html(data);
                    });
                });' value="<?php echo AFF_AVAILABILITY_BUTTON_LABEL; ?>">                        	
                </td>
            </tr>  			  	     

            </tr>
            <tr>
                <td><?php echo AFF_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
                </td>
                <td><input name="wp_aff_pwd" type="password" class="required password user-edit" minlength="5" id="wp_aff_pwd"></td>
            </tr>
            <tr> 
                <td><?php echo AFF_RETYPE_PASSWORD; ?><span class="required"><font color="#CC0000">*</font></span> 
                </td>
                <td><input name="wp_aff_pwd2"  id="wp_aff_pwd2" class="required password user-edit" type="password" minlength="5" equalto="#wp_aff_pwd"></td>
            </tr>

            <?php
            if (get_option('wp_aff_use_recaptcha')) {
                echo '<tr><td width="22%"></td><td width="78%" class="wpap_g_captcha_td" style="padding-top: 10px;">';
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
                echo '<input name="wpAffDoRegister" type="submit" id="wpAffDoRegister" class="button" value="' . AFF_SIGN_UP_BUTTON_LABEL . '">';
            }
            ?>
        </p>
    </form>

    <p>&nbsp;</p>
    <p><?php echo AFF_ALREADY_MEMBER; ?>? <img src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/images/login.png'; ?>" /> <a style="color:#CC0000" href="<?php echo $login_url; ?>"><?php echo AFF_LOGIN_HERE; ?></a></p>
    <?php
}
