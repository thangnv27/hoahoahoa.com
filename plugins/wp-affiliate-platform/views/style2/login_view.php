<?php

function wp_aff_login_view() {    
    define('DONOTCACHEPAGE', TRUE); //Cache plugin compatibility. Do not cache the login form.
    
    $output = "";
    $output .= wp_aff_view_get_navbar();
    $output .= '<div id="wp_aff_inside">';
    $output .= wp_aff_show_login_page();
    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_show_login_page() {
    global $wpdb;
    $output = "";
    ob_start();
    ?>
    <!-- Load jQuery Validation -->
    <script language="JavaScript" type="text/javascript" src="<?php echo WP_AFF_PLATFORM_URL . '/affiliates/js/jquery.validate.min.js'; ?>"></script>
    <script type="text/javascript">
        /* <![CDATA[ */
        jQuery(document).ready(function($) {
            $("#logForm").validate();
        });
        /*]]>*/
    </script>

    <h3 class="wp_aff_title"><?php echo AFF_LOGIN_PAGE_TITLE; ?></h3>

    <?php
    // This code is to show error messages
    if (isset($_GET['msg'])) {
        $msg = sanitize_text_field($_GET['msg']);
        echo "<div class='alert alert-danger'>$msg</div>";
    }
    ?>

    <!-- Start Login Form -->
    <form action="" method="post" name="logForm" id="logForm" >
        
        <div class="row wpap-buffer-5">
            <div class="input-group wpap-col-centered">
                <input type="text" name="userid" class="form-control required" placeholder="<?php echo AFF_AFFILIATE_ID; ?>">
            </div>
        </div>        

        <div class="row wpap-buffer-5">
            <div class="input-group wpap-col-centered">
                <input type="password" name="password" class="form-control required" placeholder="<?php echo AFF_PASSWORD; ?>">
            </div>
        </div>   

        <div class="row text-center wpap-buffer-5">
            <input name="remember" type="checkbox" id="remember" value="1">
            <?php echo AFF_REMEMBER_ME; ?>
        </div>

        <div class="row text-center">
            <button name="wpAffDoLogin" class="btn btn-default btn-lg wpap-buffer-5" type="submit" id="wpAffDoLogin" value="">
                <span class="glyphicon glyphicon-lock"></span> <?php echo AFF_LOGIN_BUTTON_LABEL; ?>
            </button>
        </div>

        <div class="row text-center wpap-vertical-buffer-10">
            <span class="glyphicon glyphicon-pencil"></span>
            <a href="<?php echo wp_aff_view_get_url_with_separator("signup"); ?>"><?php echo AFF_AFFILIATE_SIGN_UP_LABEL; ?></a>
            | 
            <span class="glyphicon glyphicon-refresh"></span>
            <a href="<?php echo wp_aff_view_get_url_with_separator("forgot_pass"); ?>"><?php echo AFF_FORGOT_PASSWORD_LABEL; ?></a>
        </div>


    </form>
    <!-- End Login Form -->
    <?php
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}
