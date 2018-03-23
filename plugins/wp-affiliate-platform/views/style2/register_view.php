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
        echo '<div class="alert alert-danger">' . AFF_ACCOUNT_SIGNUP_DISABLED . '</div>';
    } else {
        if (isset($_SESSION['wp_aff_signup_success_msg']) && !empty($_SESSION['wp_aff_signup_success_msg'])) {
            echo '<div class="alert alert-success">' . $_SESSION['wp_aff_signup_success_msg'] . '</div>';
        } else {
            wp_aff_show_signup_form();
        }
    }
    echo '</div>';
    echo wp_aff_view_2_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_signup_form($recaptcha_error = '') {
    global $wpdb;
    global $wp_aff_platform_config;
    include_once(WP_AFF_PLATFORM_PATH . 'views/countries.php');
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
    <div class="alert alert-info"><?php echo AFF_SIGNUP_PAGE_MESSAGE; ?></div>

    <?php
    if (isset($_GET['aff_signup_error_msg'])) {
        $msg = strip_tags($_GET['aff_signup_error_msg']);
        echo '<div class="alert alert-danger">' . $msg . '</div>';
    }
    ?>

    <!-- Start Registration Form -->
    <form action="" method="post" name="regForm" id="regForm" >

        <table class="table">

            <tr> 
                <td><?php echo AFF_FIRST_NAME; ?>: *</td>
                <td> 
                    <input type="text" name="afirstname" size="20" value="<?php echo $_POST['afirstname']; ?>" class="required">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_LAST_NAME; ?>: *</td>
                <td> 
                    <input type="text" name="alastname" size="20" value="<?php echo $_POST['alastname']; ?>" class="required">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_COMPANY; ?>:</td>
                <td> 
                    <input type="text" name="acompany" size="20" value="<?php echo $_POST['acompany']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_WEBSITE; ?>:</td>
                <td> 
                    <input type="text" name="awebsite" size="20" value="<?php echo $_POST['awebsite']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_EMAIL; ?>: *</td>
                <td> 
                    <input type="text" name="aemail" size="20" value="<?php echo $_POST['aemail']; ?>" class="required email">
                </td>
            </tr>
            <tr>
                <?php
                if ($wp_aff_platform_config->getValue('wp_aff_make_paypal_email_required') == '1') {
                    echo '<td>' . AFF_PAYPAL_EMAIL . ': *</td>';
                    echo '<td><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '" class="required email"></td>';
                } else {
                    echo '<td>' . AFF_PAYPAL_EMAIL . ': </td>';
                    echo '<td><input type="text" name="paypal_email" size="20" value="' . $_POST['paypal_email'] . '"></td>';
                }
                ?>
            </tr>
            <?php
            if ($wp_aff_platform_config->getValue('wp_aff_hide_tax_id_field') == '1') {
                //Do not show the tax ID field           		
            } else {
                echo '<tr>';
                echo '<td>' . AFF_TAX_ID . ': </td>';
                echo '<td><input type="text" name="tax_id" size="20" value="' . $_POST['tax_id'] . '"></td>';
                echo '</tr>';
            }
            ?>
            <tr> 
                <td><?php echo AFF_ADDRESS; ?>:</td>
                <td> 
                    <input type="text" name="astreet" size="20" value="<?php echo $_POST['astreet']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_TOWN; ?>:</td>
                <td> 
                    <input type="text" name="atown" size="20" value="<?php echo $_POST['atown']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_STATE; ?>:</td>
                <td> 
                    <input type="text" name="astate" size="20" value="<?php echo $_POST['astate']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_ZIP; ?>:</td>
                <td> 
                    <input type="text" name="apostcode" size="20" value="<?php echo $_POST['apostcode']; ?>">
                </td>
            </tr>
            <tr> 
                <td><?php echo AFF_COUNTRY; ?>:</td>
                <td> 
                    <select name="acountry" class="user-select">
                        <?php
                        foreach ($GLOBALS['countries'] as $key => $country)
                            print '<option value="' . $key . '" ' . ($key == "VN" ? 'selected' : '') . '>' . $country . '</option>' . "\n";
                        ?>
                    </select>
                </td>
            </tr>

            <tr> 
                <td><?php echo AFF_PHONE; ?>:</td>
                <td> 
                    <input type="text" name="aphone" size="20" value="<?php echo $_POST['aphone']; ?>">
                </td>
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
                    <input name="btnAvailable" type="button" class="btn btn-default btn-sm" id="btnAvailable" onclick='jQuery(document).ready(function($) {
                    $("#checkid").html("<?php echo AFF_SI_PLEASE_WAIT; ?>");
                    $.get("<?php echo WP_AFF_PLATFORM_URL . '/affiliates/checkuser.php'; ?>", {cmd: "check", user: $("#user_name").val()}, function(data) {
                        $("#checkid").html(data);
                    });
                });' value="<?php echo AFF_AVAILABILITY_BUTTON_LABEL; ?>" >
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

        <div class="row text-center">
            <?php
            if (get_option('wp_aff_disable_visitor_signup')) {
                echo '<div class="alert alert-danger">' . AFF_ACCOUNT_SIGNUP_DISABLED . '</div>';
            } else {
                $terms_url = get_option('wp_aff_terms_url');
                if (!empty($terms_url)) {
                    echo '<div class="row text-center wpap-buffer-10">';
                    $terms = "<a href=\"$terms_url\" target=\"_blank\">" . AFF_TERMS_AND_COND . "</a>";
                    echo AFF_TERMS_AGREE . $terms . ' ';
                    echo '<input type="checkbox" name="affiliate-t-and-c" class="affiliate-t-and-c required" value="" />';
                    echo '</div>';
                }

                echo '<button name="wpAffDoRegister" class="btn btn-default btn-lg wpap-buffer-5" type="submit" id="wpAffDoRegister" value="">';
                echo '<span class="glyphicon glyphicon-pencil"></span> ' . AFF_SIGN_UP_BUTTON_LABEL;
                echo '</button>';
            }
            ?>
        </div>
    </form>

    <div class="row text-center wpap-vertical-buffer-10">
        <?php echo AFF_ALREADY_MEMBER; ?>? 
        <span class="glyphicon glyphicon-lock"></span>
        <a href="<?php echo $login_url; ?>"><?php echo AFF_LOGIN_HERE; ?></a>
    </div>

    <?php
}
