<?php

function affiliate_platform_affiliate_view_2_main() {

    $output = '';
    $output .= '<div id="wpap_style2_view_body">';
    if (wp_aff_is_logged_in()) {
        $action = isset($_GET['wp_affiliate_view']) ? $_GET['wp_affiliate_view'] : '';
        switch ($action) {
            case 'members_only':
                include_once('members_only_view.php');
                $output .= wp_aff_members_only_view();
                break;
            case 'details':
                include_once('details_view.php');
                $output .= wp_aff_details_view();
                break;
            case 'clicks':
                include_once('referrals_view.php');
                $output .= wp_aff_referrals_view();
                break;
            case 'sub-affiliates':
                include_once('sub_affiliates_view.php');
                $output .= wp_aff_sub_affiliates_view();
                break;
            case 'leads':
                include_once('leads_view.php');
                $output .= wp_aff_leads_view();
                break;
            case 'sales':
                include_once('sales_view.php');
                $output .= wp_aff_sales_view();
                break;
            case 'payments':
                include_once('payments_view.php');
                $output .= wp_aff_payment_history_view();
                break;
            case 'ads':
                include_once('ads_view.php');
                $output .= wp_aff_ads_view();
                break;
            case 'creatives':
                include_once('creatives_view.php');
                $output .= wp_aff_creatives_view();
                break;
            case 'link_generation':
                include_once('link_generation_view.php');
                $output .= wp_aff_link_generation_view();
                break;             
            case 'contact':
                include_once('contact_view.php');
                $output .= wp_aff_contact_view();
                break;
            case 'logout':
                //see the code in "wp_affiliate_platform1.php" file
                break;
            default:
                include_once('members_only_view.php');
                $output .= wp_aff_members_only_view();
                break;
        }
    } else {
        $action = isset($_GET['wp_affiliate_view']) ? $_GET['wp_affiliate_view'] : '';
        switch ($action) {
            case 'login':
                include_once('login_view.php');
                $output .= wp_aff_login_view();
                break;
            case 'signup':
                include_once('register_view.php');
                $output .= wp_aff_register_view();
                break;
            case 'forgot_pass':
                include_once('forgot_pass_view.php');
                $output .= wp_aff_forgot_pass_view();
                break;
            default:
                $output .= wp_aff_view_2_main_index();
                break;
        }
    }
    $output .= '</div>';
    return $output;
}

function wp_aff_view_2_main_index() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $login_url = wp_aff_view_get_url_with_separator("login");
    $signup_url = wp_aff_view_get_url_with_separator("signup");

    $output = "";
    $output .= wp_aff_view_get_navbar();
    $output .= '<div id="wp_aff_inside">';

    $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title');

    $output .= '<h3 class="wp_aff_title">' . $wp_aff_index_title . '</h3>';

    $output .= '<div class="row text-center">';

    $output .= '<a class="btn btn-default btn-lg wpap-buffer-5" href="' . $signup_url . '">';
    $output .= '<span class="glyphicon glyphicon-pencil"></span> ' . AFF_SIGN_UP;
    $output .= '</a>';

    $output .= '<a class="btn btn-default btn-lg wpap-buffer-5" href="' . $login_url . '">';
    $output .= '<span class="glyphicon glyphicon-lock"></span> ' . AFF_LOGIN;
    $output .= '</a>';

    $output .= '</div>';
    $output .= '<div class="row wpap-vertical-buffer-10"></div>';

    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    $wp_aff_index_body = apply_filters('the_content', $wp_aff_index_body);
    $output .= '<div id="wp_aff_index_body">' . $wp_aff_index_body . '</div>';
    $output .= '<div class="wp_aff_clear"></div>';

    $output .= '</div>';
    $output .= wp_aff_view_2_get_footer();
    return $output;
}

function wp_aff_view_2_get_footer() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $output = "";
    if ($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section') != '1') {
        $output .= '<div class="row wpap-buffer-10"></div>';
        $output .= '<div class="panel panel-default">';
        $aff_id = get_option('wp_aff_user_affilate_id');
        if (!empty($aff_id)) {
            $output .= '<div class="wpap-buffer-10">Powered by&nbsp;&nbsp;<a target="_blank" href="https://www.tipsandtricks-hq.com/?p=1474&ap_id=' . $aff_id . '">WP Affiliate Platform</a></div>';
        } else {
            $output .= '<div class="wpap-buffer-10">Powered by&nbsp;&nbsp;<a target="_blank" href="https://ppo.vn/affiliate">WP Affiliate Platform</a></div>';
        }
        $output .= '</div>';
    }
    return $output;
}

function wp_aff_view_2_get_navbar() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $output = "";
    if (wp_aff_view_is_logged_in()) {
        $separator = '?';
        $url = get_permalink();
        if (strpos($url, '?wp_affiliate_view=')) {
            $separator = '?';
        } else if (strpos($url, '?') !== false) {
            $separator = '&';
        }

        $output .= '
        <nav class="navbar navbar-default" role="navigation">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>';

        //<!-- Collect the nav links, forms, and other content for toggling -->
        $output .= '<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">';
        $output .= '<ul class="nav navbar-nav">';

        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=members_only">' . AFF_NAV_HOME . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=details">' . AFF_NAV_EDIT_PROFILE . '</a></li>';
        
        $output .= '<li class="dropdown">';
            $output .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.AFF_NAV_REFERRALS.'<b class="caret"></b></a>';
            $output .= '<ul class="dropdown-menu">';
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=clicks">' . AFF_NAV_CLICKS . '</a></li>';
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=sales">' . AFF_NAV_SALES . '</a></li>';
            if($wp_aff_platform_config->getValue('wp_aff_show_leads_to_affiliates')!=''){//Show leads nav menu
		$output .= '<li><a href="'. $url . $separator . 'wp_affiliate_view=leads">' .AFF_NAV_LEADS.'</a></li>';
            }
            if (get_option('wp_aff_use_2tier')){
		$output .= '<li><a href="'.$url . $separator . 'wp_affiliate_view=sub-affiliates">'.AFF_NAV_SUB_AFFILIATES.'</a></li>';
            }            
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=payments">' . AFF_NAV_PAYMENT_HISTORY . '</a></li>';
            $output .= '</ul>';
        $output .= '</li>';
        
        $output .= '<li class="dropdown">';
            $output .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.AFF_NAV_ADS.'<b class="caret"></b></a>';
            $output .= '<ul class="dropdown-menu">';        
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=ads">' . AFF_NAV_BANNERS . '</a></li>';
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=creatives">' . AFF_NAV_CREATIVES . '</a></li>';
            $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=link_generation">' . AFF_NAV_LINK_GENERATION . '</a></li>';
            $output .= '</ul>';
        $output .= '</li>';
        
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=contact">' . AFF_NAV_CONTACT . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=logout">' . AFF_NAV_LOGOUT . '</a></li>';
        
        $output .= '</ul>';

        $output .= '</div>'; //<!-- /.navbar-collapse -->
        $output .= '</div>'; //<!-- /.container-fluid -->
        $output .= '</nav>'; //<!-- /.navbar -->';

        $output .= wp_aff_area_user_notice();
        return $output;
    }
    return $output;
}